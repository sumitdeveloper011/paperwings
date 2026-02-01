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
    public $timeout = 300;
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
            $connection = DB::connection();
            $pdo = $connection->getPdo();
            
            // Check if PDO is null before using it
            if ($pdo === null) {
                Log::warning('Database connection PDO is null, reconnecting...');
                DB::reconnect();
                $pdo = DB::connection()->getPdo();
            }
            
            // Verify connection is active
            if ($pdo !== null) {
                $pdo->query('SELECT 1');
            } else {
                Log::error('Database connection failed: PDO is still null after reconnect');
                throw new \Exception('Database connection failed: Unable to establish PDO connection');
            }
        } catch (\Exception $e) {
            Log::warning('Database connection lost, reconnecting...', ['error' => $e->getMessage()]);
            DB::reconnect();
            
            // Verify reconnection worked
            $pdo = DB::connection()->getPdo();
            if ($pdo === null) {
                Log::error('Database reconnection failed: PDO is still null');
                throw new \Exception('Database reconnection failed: Unable to establish PDO connection');
            }
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
        // With bulk API, batching is less critical since we fetch all stock in 1-10 API calls
        // However, batching can still help with memory management for extremely large datasets
        // Only batch if we have a very large number of products (e.g., > 2000)
        // Note: Each batch job will still fetch ALL stock from API, but processes fewer products in memory
        return $this->productIds === null && $products->count() > 2000;
    }

    protected function dispatchBatches($products): void
    {
        // With bulk API, we can use larger batch sizes since API calls are efficient
        // Each batch job fetches all stock but processes only its assigned products
        $batchSize = 500; // Increased from 10 since API is now efficient
        $batches = $products->chunk($batchSize);
        $totalBatches = $batches->count();
        
        Log::info('Splitting stock import into batches (using bulk API)', [
            'total_products' => $products->count(),
            'total_batches' => $totalBatches,
            'batch_size' => $batchSize,
            'note' => 'Each batch uses bulk API but processes subset of products for memory management'
        ]);

        $batchIndex = 0;
        foreach ($batches as $batch) {
            $batchProductIds = $batch->pluck('eposnow_product_id')->toArray();
            $batchJobId = $this->jobId . '_batch_' . ($batchIndex + 1);
            
            // Reduced delay since bulk API is faster
            self::dispatch($batchJobId, $batchProductIds)->delay(now()->addSeconds($batchIndex * 5));
            
            $batchIndex++;
        }

        $this->updateProgress(100, $this->processedCount, $products->count(), "Dispatched {$totalBatches} batch jobs for {$products->count()} products (using bulk API).", [
            'status' => 'batched',
            'total_batches' => $totalBatches,
            'batch_size' => $batchSize
        ]);
    }

    protected function processProducts($products, EposNowService $eposNowService, RateLimitTrackerService $rateLimitTracker): void
    {
        $total = $products->count() + $this->processedCount;
        $processed = $this->processedCount;
        $updated = 0;
        $skipped = 0;
        $failed = [];
        $allStock = [];

        // Edge case: Check rate limit before starting bulk fetch
        $cooldown = $rateLimitTracker->checkCooldown();
        if ($cooldown['in_cooldown']) {
            $this->releaseJobWithDelay($cooldown['minutes_remaining'] * 60, $processed, $total, $updated, $skipped);
            return;
        }

        $this->updateProgress(10, $processed, $total, 'Fetching all stock data from EposNow (bulk)...', ['status' => 'fetching']);

        try {
            // Fetch all product stock in one bulk API call (with pagination handled internally)
            $allStock = $eposNowService->getAllProductStock();
            
            // Edge case: Handle empty stock data
            if (empty($allStock) || !is_array($allStock)) {
                $this->updateProgress(100, $processed, $total, 'No stock data found from EposNow API', [
                    'status' => 'completed',
                    'updated' => 0,
                    'skipped' => $total,
                    'failed' => 0,
                    'message' => 'EposNow API returned empty stock data'
                ]);
                Log::warning('Stock import: No stock data returned from EposNow API', [
                    'total_products' => $total
                ]);
                return;
            }

            $this->updateProgress(30, $processed, $total, 'Processing stock updates...', [
                'status' => 'processing',
                'stock_data_fetched' => count($allStock)
            ]);

            // DEBUG: Log stock lookup data
            $sampleLookup = array_slice($allStock, 0, 10, true);
            Log::info('DEBUG: Stock lookup data received', [
                'total_stock_entries' => count($allStock),
                'sample_productIds' => array_keys($sampleLookup),
                'sample_stock_values' => array_values($sampleLookup),
                'products_to_process' => $products->count()
            ]);

            // Edge case: Create lookup map for faster access
            $stockLookup = $allStock; // Already indexed by productId

            // Update products based on fetched stock data
            foreach ($products as $index => $product) {
                try {
                    $this->ensureDatabaseConnection();
                    
                    // Edge case: Validate product has eposnow_product_id
                    $eposNowProductId = $product->eposnow_product_id;
                    
                    if (!$eposNowProductId || !is_numeric($eposNowProductId) || $eposNowProductId <= 0) {
                        $skipped++;
                        $processed++;
                        continue;
                    }

                    $eposNowProductId = (int) $eposNowProductId;

                    // Edge case: Get stock for this product from bulk data
                    $newStock = $stockLookup[$eposNowProductId] ?? null;

                    // DEBUG: Log first few product lookups
                    if ($index < 5) {
                        Log::info('DEBUG: Product stock lookup', [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'eposnow_product_id' => $eposNowProductId,
                            'current_stock_in_db' => $product->stock,
                            'found_in_lookup' => isset($stockLookup[$eposNowProductId]),
                            'new_stock_value' => $newStock,
                            'lookup_keys_sample' => array_slice(array_keys($stockLookup), 0, 5)
                        ]);
                    }

                    // Edge case: Handle products not found in stock data
                    if ($newStock === null) {
                        // Product not found in stock data - skip it (might be new product or deleted from EposNow)
                        // DEBUG: Log why product was skipped
                        if ($index < 10) {
                            Log::info('DEBUG: Product skipped - not found in stock data', [
                                'product_id' => $product->id,
                                'eposnow_product_id' => $eposNowProductId,
                                'product_name' => $product->name,
                                'stock_lookup_has_key' => isset($stockLookup[$eposNowProductId]),
                                'sample_lookup_keys' => array_slice(array_keys($stockLookup), 0, 10)
                            ]);
                        }
                        $skipped++;
                        $processed++;
                        continue;
                    }

                    // Edge case: Validate stock is numeric (should be, but double-check)
                    if (!is_numeric($newStock)) {
                        Log::warning('Stock import: Invalid stock value for product', [
                            'product_id' => $product->id,
                            'eposnow_product_id' => $eposNowProductId,
                            'stock_value' => $newStock,
                            'type' => gettype($newStock)
                        ]);
                        $skipped++;
                        $processed++;
                        continue;
                    }

                    $newStock = (int) $newStock;

                    // Edge case: Handle stock value of 0 (valid - means out of stock)
                    // We update even if stock is 0, as it's a valid value

                    // Update if stock changed
                    $wasUpdated = false;
                    $updateError = null;
                    
                    try {
                        DB::transaction(function () use ($product, $newStock, &$wasUpdated, $index) {
                            // Edge case: Only update if stock actually changed (avoid unnecessary DB writes)
                            if ($product->stock != $newStock) {
                                // DEBUG: Log database update
                                if ($index < 5) {
                                    Log::info('DEBUG: Updating product stock in database', [
                                        'product_id' => $product->id,
                                        'eposnow_product_id' => $product->eposnow_product_id,
                                        'old_stock' => $product->stock,
                                        'new_stock' => $newStock,
                                        'update_query' => "UPDATE products SET stock = {$newStock} WHERE id = {$product->id}"
                                    ]);
                                }
                                
                                $product->update(['stock' => $newStock]);
                                $wasUpdated = true;
                                
                                // DEBUG: Verify update
                                if ($index < 5) {
                                    $product->refresh();
                                    Log::info('DEBUG: Product stock updated - verification', [
                                        'product_id' => $product->id,
                                        'stock_after_update' => $product->stock,
                                        'update_successful' => $product->stock == $newStock
                                    ]);
                                }
                            } else {
                                // DEBUG: Log when stock didn't change
                                if ($index < 5) {
                                    Log::info('DEBUG: Product stock unchanged - skipping update', [
                                        'product_id' => $product->id,
                                        'eposnow_product_id' => $product->eposnow_product_id,
                                        'current_stock' => $product->stock,
                                        'new_stock' => $newStock
                                    ]);
                                }
                            }
                        }, 3); // Edge case: Retry transaction up to 3 times
                    } catch (\Exception $dbException) {
                        // Edge case: Handle database transaction failures
                        $updateError = $dbException->getMessage();
                        Log::error('Stock update: Database transaction failed', [
                            'product_id' => $product->id,
                            'eposnow_product_id' => $eposNowProductId,
                            'error' => $updateError
                        ]);
                        throw $dbException; // Re-throw to be caught by outer catch
                    }

                    if ($wasUpdated) {
                        $updated++;
                    } else {
                        $skipped++; // Stock didn't change, so skipped
                    }
                
                    $processed++;
                    $this->processedProductIds[] = $eposNowProductId;

                    // Edge case: Update progress every 10 products (less frequent than before since we're faster)
                    if (($index + 1) % 10 === 0 || ($index + 1) === $products->count()) {
                        $percentage = min(99, 30 + (int)(($processed / $total) * 70));
                        $this->updateProgress(
                            $percentage,
                            $processed,
                            $total,
                            "Processing... {$processed}/{$total} products (Updated: {$updated}, Skipped: {$skipped})",
                            [
                                'updated' => $updated,
                                'skipped' => $skipped,
                                'failed' => count($failed)
                            ]
                        );
                    
                        // Edge case: Cleanup memory periodically for large datasets
                        $this->cleanupMemory();
                    }

                } catch (\Exception $e) {
                    // Edge case: Handle individual product processing errors
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
                
                    Log::error('Stock update failed for product', [
                        'product_id' => $product->id ?? null,
                        'eposnow_product_id' => $product->eposnow_product_id ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            $finalStatus = "Stock import completed! Updated: {$updated}, Skipped: {$skipped}, Failed: " . count($failed);
            $this->updateProgress(100, $processed, $total, $finalStatus, [
                'updated' => $updated,
                'skipped' => $skipped,
                'failed' => count($failed),
                'failed_items' => $failed,
                'status' => 'completed',
                'stock_data_fetched' => count($allStock)
            ]);

            Log::info('Stock import completed', [
                'total' => $total,
                'updated' => $updated,
                'skipped' => $skipped,
                'failed' => count($failed),
                'stock_data_fetched' => count($allStock),
                'products_with_stock' => count(array_filter($allStock, fn($stock) => $stock > 0))
            ]);

        } catch (\Exception $e) {
            // Edge case: Handle bulk fetch errors
            if ($this->isRateLimitError($e)) {
                $this->handleRateLimitError($e);
                return;
            }
            
            // Edge case: Handle other bulk fetch errors - log and fail gracefully
            Log::error('Stock import: Bulk fetch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'processed_so_far' => $processed,
                'total' => $total,
                'job_id' => $this->jobId
            ]);
            
            // If we processed some products before the error, log partial success
            if ($processed > 0) {
                Log::warning('Stock import: Partial completion before error', [
                    'processed' => $processed,
                    'total' => $total,
                    'updated' => $updated,
                    'skipped' => $skipped
                ]);
            }
            
            $this->handleException($e);
        }
    }


    protected function isRateLimitError(\Throwable $e): bool
    {
        $message = $e->getMessage();
        return stripos($message, 'rate limit') !== false ||
               stripos($message, 'maximum API limit') !== false ||
               stripos($message, 'cooldown period') !== false ||
               stripos($message, '403') !== false;
    }

    protected function handleRateLimitError(\Throwable $e): void
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

    protected function handleException(\Throwable $e): void
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
