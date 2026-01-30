<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\EposNowService;
use App\Services\RateLimitTrackerService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ImportEposNowStockJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels;

    protected string $jobId;
    protected ?array $productIds;
    protected int $processedCount = 0;
    protected array $processedProductIds = [];

    public $tries = 3;
    public $backoff = [300, 900, 1800];
    public $timeout = 180;
    public $maxExceptions = 2;

    public function __construct(string $jobId, ?array $productIds = null, int $processedCount = 0, array $processedProductIds = [])
    {
        $this->jobId = $jobId;
        $this->productIds = $productIds;
        $this->processedCount = $processedCount;
        $this->processedProductIds = $processedProductIds;
        $this->onQueue('imports');
    }

    public function handle(): void
    {
        try {
            $this->ensureDatabaseConnection();
            
            $eposNowService = app(EposNowService::class);
            $rateLimitTracker = app(RateLimitTrackerService::class);
            
            $cooldown = $rateLimitTracker->checkCooldown();
            if ($cooldown['in_cooldown']) {
                $this->releaseJobWithDelay($cooldown['minutes_remaining'] * 60);
                return;
            }
            
            $this->updateProgress(0, $this->processedCount, 0, 'Starting stock import...', ['status' => 'starting']);

            $products = $this->fetchProducts();
            
            if ($products->isEmpty()) {
                $this->updateProgress(100, $this->processedCount, 0, 'No products found with EposNow product ID');
                return;
            }

            if ($this->shouldBatch($products)) {
                $this->dispatchBatches($products);
                return;
            }

            $this->processProducts($products, $eposNowService, $rateLimitTracker);
            
        } catch (\Exception $e) {
            if ($this->isRateLimitError($e)) {
                $this->handleRateLimitError($e);
                return;
            }
            $this->handleException($e);
        } finally {
            $this->cleanup();
        }
    }

    protected function ensureDatabaseConnection(): void
    {
        try {
            $pdo = DB::connection()->getPdo();
            $pdo->query('SELECT 1');
        } catch (\Exception $e) {
            Log::warning('Database connection lost, reconnecting...', ['error' => $e->getMessage()]);
            DB::reconnect();
        }
    }

    protected function fetchProducts()
    {
        $this->updateProgress(5, $this->processedCount, 0, 'Fetching products from database...', ['status' => 'fetching']);

        $query = Product::whereNotNull('eposnow_product_id')
            ->where('eposnow_product_id', '!=', 0);

        if ($this->productIds !== null && !empty($this->productIds)) {
            $query->whereIn('eposnow_product_id', $this->productIds);
        }

        if (!empty($this->processedProductIds)) {
            $query->whereNotIn('eposnow_product_id', $this->processedProductIds);
        }

        return $query->get(['id', 'eposnow_product_id', 'name', 'stock']);
    }

    protected function shouldBatch($products): bool
    {
        return $this->productIds === null && $products->count() > 10;
    }

    protected function dispatchBatches($products): void
    {
        $batchSize = 10;
        $batches = $products->chunk($batchSize);
        $totalBatches = $batches->count();
        
        Log::info('Splitting stock import into batches', [
            'total_products' => $products->count(),
            'total_batches' => $totalBatches,
            'batch_size' => $batchSize
        ]);

        $batchIndex = 0;
        foreach ($batches as $batch) {
            $batchProductIds = $batch->pluck('eposnow_product_id')->toArray();
            $batchJobId = $this->jobId . '_batch_' . ($batchIndex + 1);
            
            self::dispatch($batchJobId, $batchProductIds)->delay(now()->addSeconds($batchIndex * 10));
            
            $batchIndex++;
        }

        $this->updateProgress(100, $this->processedCount, $products->count(), "Dispatched {$totalBatches} batch jobs for {$products->count()} products.", [
            'status' => 'batched',
            'total_batches' => $totalBatches
        ]);
    }

    protected function processProducts($products, EposNowService $eposNowService, RateLimitTrackerService $rateLimitTracker): void
    {
        $total = $products->count() + $this->processedCount;
        $processed = $this->processedCount;
        $updated = 0;
        $skipped = 0;
        $failed = [];

        $this->updateProgress(10, $processed, $total, "Processing {$products->count()} products...", ['status' => 'processing']);

        foreach ($products as $index => $product) {
            try {
                $this->ensureDatabaseConnection();
                
                $cooldown = $rateLimitTracker->checkCooldown();
                if ($cooldown['in_cooldown']) {
                    $this->releaseJobWithDelay($cooldown['minutes_remaining'] * 60, $processed, $total, $updated, $skipped);
                    return;
                }

                if ($rateLimitTracker->shouldPause()) {
                    $status = $rateLimitTracker->getStatus();
                    $this->updateProgress(
                        min(99, (int)(($processed / $total) * 100)),
                        $processed,
                        $total,
                        'Pausing: Approaching rate limit. Waiting 60 seconds...',
                        ['status' => 'pausing']
                    );
                    sleep(60);
                }

                $result = $this->processProduct($product, $eposNowService, $rateLimitTracker);
                
                if ($result['updated']) $updated++;
                if ($result['skipped']) $skipped++;
                
                $processed++;
                $this->processedProductIds[] = $product->eposnow_product_id;

                if (($index + 1) % 3 === 0) {
                    $this->updateProgress(
                        min(99, (int)(($processed / $total) * 100)),
                        $processed,
                        $total,
                        "Processing... {$processed}/{$total} products",
                        ['updated' => $updated, 'skipped' => $skipped, 'failed' => count($failed)]
                    );
                    
                    $this->cleanupMemory();
                }

            } catch (\Exception $e) {
                if ($this->isRateLimitError($e)) {
                    $rateLimitTracker->setCooldown();
                    $this->releaseJobWithDelay(1800, $processed, $total, $updated, $skipped);
                    return;
                }
                
                $failed[] = [
                    'id' => $product->eposnow_product_id ?? 'unknown',
                    'name' => $product->name ?? 'unknown',
                    'error' => $e->getMessage()
                ];
                $processed++;
                
                Log::error('Stock import failed for product', [
                    'product_id' => $product->id,
                    'eposnow_product_id' => $product->eposnow_product_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $finalStatus = "Stock import completed! Updated: {$updated}, Skipped: {$skipped}, Failed: " . count($failed);
        $this->updateProgress(100, $processed, $total, $finalStatus, [
            'updated' => $updated,
            'skipped' => $skipped,
            'failed' => count($failed),
            'failed_items' => $failed,
            'status' => 'completed'
        ]);

        Log::info('Stock import completed', [
            'total' => $total,
            'updated' => $updated,
            'skipped' => $skipped,
            'failed' => count($failed)
        ]);
    }

    protected function processProduct($product, EposNowService $eposNowService, RateLimitTrackerService $rateLimitTracker): array
    {
        if (!$product->eposnow_product_id) {
            return ['updated' => false, 'skipped' => true];
        }

        $delay = $rateLimitTracker->getRecommendedDelay();
        if ($delay > 0) {
            usleep((int)($delay * 1000000));
        }

        $stock = $eposNowService->getProductStock($product->eposnow_product_id);

        if ($stock === null) {
            return ['updated' => false, 'skipped' => true];
        }

        $updated = false;
        DB::transaction(function () use ($product, $stock, &$updated) {
            if ($product->stock != $stock) {
                $product->update(['stock' => $stock]);
                $updated = true;
            }
        }, 3);

        return ['updated' => $updated, 'skipped' => false];
    }

    protected function isRateLimitError(\Exception $e): bool
    {
        $message = $e->getMessage();
        return stripos($message, 'rate limit') !== false ||
               stripos($message, 'maximum API limit') !== false ||
               stripos($message, 'cooldown period') !== false ||
               stripos($message, '403') !== false;
    }

    protected function handleRateLimitError(\Exception $e): void
    {
        $rateLimitTracker = app(RateLimitTrackerService::class);
        $rateLimitTracker->setCooldown(30);
        $cooldown = $rateLimitTracker->checkCooldown();
        
        $this->releaseJobWithDelay($cooldown['minutes_remaining'] * 60);
    }

    protected function releaseJobWithDelay(int $seconds, int $processed = 0, int $total = 0, int $updated = 0, int $skipped = 0): void
    {
        $minutes = round($seconds / 60);
        $this->updateProgress(
            $total > 0 ? min(99, (int)(($processed / $total) * 100)) : 0,
            $processed,
            $total,
            "Rate limit reached. Job will retry in {$minutes} minutes.",
            [
                'status' => 'rate_limited',
                'retry_at' => now()->addSeconds($seconds)->toDateTimeString(),
                'updated' => $updated,
                'skipped' => $skipped
            ]
        );
        
        $this->release($seconds);
    }

    protected function handleException(\Exception $e): void
    {
        $this->updateProgress(0, $this->processedCount, 0, 'Stock import failed: ' . $e->getMessage(), [
            'status' => 'failed',
            'error' => $e->getMessage()
        ]);

        Log::error('Stock import job failed', [
            'job_id' => $this->jobId,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        throw $e;
    }

    protected function cleanup(): void
    {
        try {
            if (DB::connection()->getPdo() !== null) {
                DB::disconnect();
            }
        } catch (\Exception $e) {
            Log::debug('Error disconnecting database: ' . $e->getMessage());
        }
        
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    protected function cleanupMemory(): void
    {
        if (function_exists('gc_collect_cycles')) {
            gc_collect_cycles();
        }
    }

    protected function updateProgress(int $percentage, int $processed, int $total, string $message, array $additional = []): void
    {
        $progressData = array_merge([
            'percentage' => $percentage,
            'processed' => $processed,
            'total' => $total,
            'message' => $message,
            'status' => $percentage === 100 ? 'completed' : 'processing',
            'updated_at' => now()->toDateTimeString()
        ], $additional);

        Cache::put("stock_import_{$this->jobId}", $progressData, 7200);
    }

    public function failed(\Throwable $exception): void
    {
        if ($this->isRateLimitError($exception)) {
            Log::warning('Stock import job failed due to rate limit - will retry', [
                'job_id' => $this->jobId,
                'error' => $exception->getMessage()
            ]);
            return;
        }

        $this->updateProgress(0, $this->processedCount, 0, 'Stock import job failed permanently: ' . $exception->getMessage(), [
            'status' => 'failed',
            'error' => $exception->getMessage()
        ]);

        Log::error('Stock import job failed permanently', [
            'job_id' => $this->jobId,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
