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

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 1;

    /**
     * The maximum number of seconds the job can run before timing out.
     */
    public $timeout = 3600;

    public function __construct(string $jobId, ?array $productIds = null)
    {
        $this->jobId = $jobId;
        $this->productIds = $productIds;
        $this->onQueue('imports');
    }

    public function handle(): void
    {
        try {
            $eposNowService = app(EposNowService::class);
            $rateLimitTracker = app(RateLimitTrackerService::class);
            
            $cooldown = $rateLimitTracker->checkCooldown();
            if ($cooldown['in_cooldown']) {
                $this->updateProgress(0, 0, 0, 'API is in cooldown period. Please wait ' . $cooldown['minutes_remaining'] . ' more minutes before retrying.', [
                    'status' => 'rate_limited',
                    'cooldown_until' => $cooldown['cooldown_until'],
                    'minutes_remaining' => $cooldown['minutes_remaining']
                ]);
                return;
            }
            
            $this->updateProgress(0, 0, 0, 'Starting stock import...', ['status' => 'starting']);

            $this->updateProgress(5, 0, 0, 'Fetching products from database...', ['status' => 'fetching']);

            $query = Product::whereNotNull('eposnow_product_id')
                ->where('eposnow_product_id', '!=', 0);

            if ($this->productIds !== null && !empty($this->productIds)) {
                $query->whereIn('eposnow_product_id', $this->productIds);
            }

            $products = $query->get(['id', 'eposnow_product_id', 'name', 'stock']);

            if ($products->isEmpty()) {
                $this->updateProgress(100, 0, 0, 'No products found with EposNow product ID');
                return;
            }

            $total = $products->count();
            $processed = 0;
            $updated = 0;
            $skipped = 0;
            $failed = [];

            $this->updateProgress(10, 0, $total, "Found {$total} products. Preparing to import stock...", ['status' => 'processing']);

            $chunkSize = 15;
            $chunks = $products->chunk($chunkSize);
            $totalChunks = $chunks->count();

            Log::info('Starting stock import processing', [
                'total_products' => $total,
                'total_chunks' => $totalChunks,
                'chunk_size' => $chunkSize
            ]);

            foreach ($chunks as $chunkIndex => $chunk) {
                try {
                    $cooldown = $rateLimitTracker->checkCooldown();
                    if ($cooldown['in_cooldown']) {
                        $this->updateProgress(
                            min(99, (int)(($processed / $total) * 100)),
                            $processed,
                            $total,
                            'Paused: API cooldown period. Please retry in ' . $cooldown['minutes_remaining'] . ' minutes.',
                            [
                                'updated' => $updated,
                                'skipped' => $skipped,
                                'failed' => count($failed),
                                'status' => 'rate_limited',
                                'cooldown_until' => $cooldown['cooldown_until'],
                                'remaining_products' => $total - $processed
                            ]
                        );
                        return;
                    }

                    if ($rateLimitTracker->shouldPause()) {
                        $status = $rateLimitTracker->getStatus();
                        $this->updateProgress(
                            min(99, (int)(($processed / $total) * 100)),
                            $processed,
                            $total,
                            'Pausing: Approaching rate limit (' . round($status['percentage']) . '%). Waiting 60 seconds...',
                            [
                                'updated' => $updated,
                                'skipped' => $skipped,
                                'failed' => count($failed),
                                'status' => 'pausing'
                            ]
                        );
                        sleep(60);
                    }

                    DB::transaction(function () use ($chunk, $eposNowService, $rateLimitTracker, &$processed, &$updated, &$skipped, &$failed, $total) {
                        DB::statement('SET SESSION innodb_lock_wait_timeout = 300');
                        
                        foreach ($chunk as $product) {
                            try {
                                if (!$product->eposnow_product_id) {
                                    $skipped++;
                                    $processed++;
                                    continue;
                                }

                                $rateLimitStatus = $rateLimitTracker->getStatus();
                                $delay = $rateLimitTracker->getRecommendedDelay();
                                
                                if ($delay > 0) {
                                    usleep((int)($delay * 1000000));
                                }

                                $stock = $eposNowService->getProductStock($product->eposnow_product_id);

                                if ($stock === null) {
                                    $skipped++;
                                    $processed++;
                                    continue;
                                }

                                if ($product->stock != $stock) {
                                    $product->update(['stock' => $stock]);
                                    $updated++;
                                }

                                $processed++;
                            } catch (\Exception $e) {
                                if (stripos($e->getMessage(), 'rate limit') !== false || 
                                    stripos($e->getMessage(), 'cooldown period') !== false) {
                                    $rateLimitTracker->setCooldown();
                                    throw $e;
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
                    }, 5);
                } catch (\Exception $e) {
                    if (stripos($e->getMessage(), 'rate limit') !== false || 
                        stripos($e->getMessage(), 'cooldown period') !== false) {
                        $rateLimitTracker->setCooldown();
                        $cooldown = $rateLimitTracker->checkCooldown();
                        
                        $this->updateProgress(
                            min(99, (int)(($processed / $total) * 100)),
                            $processed,
                            $total,
                            'Rate limit reached. Cooldown period: ' . $cooldown['minutes_remaining'] . ' minutes. Job will resume automatically.',
                            [
                                'updated' => $updated,
                                'skipped' => $skipped,
                                'failed' => count($failed),
                                'status' => 'rate_limited',
                                'cooldown_until' => $cooldown['cooldown_until'],
                                'remaining_products' => $total - $processed
                            ]
                        );
                        
                        Log::warning('Stock import paused due to rate limit', [
                            'processed' => $processed,
                            'total' => $total,
                            'cooldown_minutes' => $cooldown['minutes_remaining']
                        ]);
                        
                        return;
                    }
                    
                    Log::error('Stock import chunk failed', [
                        'chunk_index' => $chunkIndex,
                        'error' => $e->getMessage(),
                    ]);
                    
                    foreach ($chunk as $product) {
                        $failed[] = [
                            'id' => $product->eposnow_product_id ?? 'unknown',
                            'name' => $product->name ?? 'unknown',
                            'error' => 'Chunk processing failed: ' . $e->getMessage()
                        ];
                        $processed++;
                    }
                }

                $percentage = min(99, (int)(($processed / $total) * 100));
                $status = "Processing... {$processed}/{$total} products (Chunk " . ($chunkIndex + 1) . "/{$totalChunks})";
                $this->updateProgress($percentage, $processed, $total, $status, [
                    'updated' => $updated,
                    'skipped' => $skipped,
                    'failed' => count($failed)
                ]);
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
        } catch (\Exception $e) {
            if (stripos($e->getMessage(), 'rate limit') !== false || 
                stripos($e->getMessage(), 'maximum API limit') !== false) {
                $errorMsg = $e->getMessage();
                $errorMsg = preg_replace('/^(API Rate Limit|EposNow API Rate Limit):\s*/i', '', $errorMsg);
                $errorMsg = preg_replace('/Please try again later\.?\s*$/i', '', $errorMsg);
                
                $this->updateProgress(0, 0, 0, 'API Rate Limit Reached: You have reached your maximum API limit. Please wait a few minutes and try again later.', [
                    'error' => $errorMsg,
                    'status' => 'rate_limited'
                ]);
                return;
            }

            $this->updateProgress(0, 0, 0, 'Stock import failed: ' . $e->getMessage(), [
                'status' => 'failed',
                'error' => $e->getMessage()
            ]);

            Log::error('Stock import job failed', [
                'job_id' => $this->jobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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

        Cache::put("stock_import_{$this->jobId}", $progressData, 3600);
    }
}
