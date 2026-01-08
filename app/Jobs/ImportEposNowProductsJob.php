<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\ProductImage;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Services\EposNowService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ImportEposNowProductsJob implements ShouldQueue
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
    public $timeout = 3600; // 1 hour

    // Create a new job instance
    public function __construct(string $jobId, ?array $productIds = null)
    {
        $this->jobId = $jobId;
        $this->productIds = $productIds; // Optional: specific product IDs to import
    }

    // Execute the job
    public function handle(): void
    {
        try {
            $eposNowService = app(EposNowService::class);
            $categoryRepository = app(CategoryRepositoryInterface::class);
            
            $this->updateProgress(0, 0, 0, 'Starting import...');

            // Update progress before API call
            $this->updateProgress(5, 0, 0, 'Connecting to EPOSNOW API...');

            try {
                if ($this->productIds !== null && !empty($this->productIds)) {
                    // Retry mode: fetch all products and filter by IDs
                    $this->updateProgress(10, 0, 0, 'Fetching products from EPOSNOW API for retry...');
                    $allProducts = $eposNowService->getAllProducts();
                    
                    // Filter products by IDs
                    $products = array_filter($allProducts, function($product) {
                        return isset($product['Id']) && in_array($product['Id'], $this->productIds);
                    });
                    $products = array_values($products); // Re-index array
                    
                    $this->updateProgress(20, 0, 0, 'Found ' . count($products) . ' products to retry. Processing...');
                } else {
                    // Normal mode: fetch all products
                    $this->updateProgress(10, 0, 0, 'Fetching products from EPOSNOW API (this may take a few minutes)...');
                    $products = $eposNowService->getAllProducts();
                    $this->updateProgress(20, 0, 0, 'Products fetched successfully. Processing data...');
                }
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
                throw $e;
            }

            if (empty($products)) {
                $this->updateProgress(100, 0, 0, 'No products found in EposNow API');
                return;
            }

            $total = count($products);
            $processed = 0;
            $inserted = 0;
            $updated = 0;
            $failed = [];
            $imagesImported = 0;

            $chunks = array_chunk($products, 25); // Reduced chunk size for better performance
            $totalChunks = count($chunks);

            Log::info('Starting product import processing', [
                'total_products' => $total,
                'total_chunks' => $totalChunks,
                'chunk_size' => 25
            ]);

            foreach ($chunks as $chunkIndex => $chunk) {
                try {
                    DB::transaction(function () use ($chunk, $categoryRepository, &$processed, &$inserted, &$updated, &$failed, $total) {
                        // Set transaction timeout
                        DB::statement('SET SESSION innodb_lock_wait_timeout = 300');
                    foreach ($chunk as $product) {
                        try {
                            if (!isset($product['Id']) || !isset($product['Name'])) {
                                $failed[] = [
                                    'id' => $product['Id'] ?? 'unknown',
                                    'name' => $product['Name'] ?? 'unknown',
                                    'error' => 'Missing required fields (Id or Name)'
                                ];
                                $processed++;
                                continue;
                            }

                            $eposnowProductId = $product['Id'];
                            $productName = $product['Name'];
                            $slug = Str::slug($productName) . '-' . $eposnowProductId;

                            $categoryId = 17;
                            if (!empty($product['CategoryId'])) {
                                $category = $categoryRepository->getByEposnowCategoryId($product['CategoryId']);
                                $categoryId = $category ? $category->id : 17;
                            }

                            $existing = Product::where('eposnow_product_id', $eposnowProductId)->first();

                            $description = $product['Description'] ?? null;

                            $productData = [
                                'category_id' => $categoryId,
                                'brand_id' => null,
                                'eposnow_product_id' => $eposnowProductId,
                                'eposnow_category_id' => $product['CategoryId'] ?? null,
                                'eposnow_brand_id' => $product['BrandId'] ?? null,
                                'barcode' => $this->normalizeBarcode($product['Barcode'] ?? null),
                                'stock' => null,
                                'product_type' => rand(1, 3), // Random 1, 2, or 3
                                'name' => $productName,
                                'slug' => $slug,
                                'total_price' => $product['SalePrice'] ?? 0.00,
                                'discount_price' => null,
                                'description' => $description,
                                'short_description' => $description, // Same as description
                                'meta_title' => $productName, // Product name as meta title
                                'meta_description' => $this->truncateDescription($description, 160), // Truncated description for meta
                                'meta_keywords' => $this->generateMetaKeywords($productName, $categoryId), // Generate keywords
                                'status' => 1,
                            ];

                            if ($existing) {
                                $hasChanges = false;
                                if ($existing->name !== $productName) {
                                    $hasChanges = true;
                                }
                                if ($existing->total_price != ($productData['total_price'])) {
                                    $hasChanges = true;
                                }
                                if ($existing->description !== ($productData['description'])) {
                                    $hasChanges = true;
                                }
                                if ($existing->category_id != $categoryId) {
                                    $hasChanges = true;
                                }

                                if ($hasChanges) {
                                    if (empty($existing->uuid)) {
                                        $productData['uuid'] = Str::uuid();
                                    }
                                    $existing->update($productData);
                                    $updated++;
                                } else {
                                    $processed++;
                                    continue;
                                }
                                $savedProduct = $existing;
                            } else {
                                $productData['uuid'] = Str::uuid();
                                $savedProduct = Product::create($productData);
                                $inserted++;
                            }

                            // Image import disabled during bulk import to improve performance
                            // Images can be imported separately using: php artisan products:import-images
                            /*
                            if ($savedProduct && !$savedProduct->images()->exists()) {
                                try {
                                    $images = $eposNowService->getProductImages((string) $eposnowProductId);
                                    
                                    if (!empty($images)) {
                                        foreach ($images as $imageData) {
                                            if (empty($imageData['ImageUrl']) || !is_string($imageData['ImageUrl'])) {
                                                continue;
                                            }

                                            $imageUrl = $imageData['ImageUrl'];

                                            if ($imageUrl === 'string' || !filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                                                continue;
                                            }

                                            $isMainImage = $imageData['MainImage'] ?? false;
                                            $savedImagePath = $eposNowService->downloadAndSaveImage($imageUrl, (string) $eposnowProductId, $isMainImage);

                                            if ($savedImagePath) {
                                                ProductImage::create([
                                                    'uuid' => Str::uuid(),
                                                    'product_id' => $savedProduct->id,
                                                    'eposnow_product_id' => (string) $eposnowProductId,
                                                    'image' => $savedImagePath,
                                                ]);
                                                $imagesImported++;
                                            }

                                            usleep(100000);
                                        }
                                    }
                                } catch (\Exception $e) {
                                    Log::warning('Product image import failed', [
                                        'product_id' => $eposnowProductId,
                                        'error' => $e->getMessage()
                                    ]);
                                }
                            }
                            */

                            $processed++;
                        } catch (\Exception $e) {
                            $failed[] = [
                                'id' => $product['Id'] ?? 'unknown',
                                'name' => $product['Name'] ?? 'unknown',
                                'error' => $e->getMessage()
                            ];
                            $processed++;
                            Log::error('Product import failed', [
                                'product_id' => $product['Id'] ?? null,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                    }, 5); // 5 attempts for transaction
                } catch (\Exception $e) {
                    Log::error('Product import chunk failed', [
                        'chunk_index' => $chunkIndex,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    
                    // Mark all items in this chunk as failed
                    foreach ($chunk as $product) {
                        $failed[] = [
                            'id' => $product['Id'] ?? 'unknown',
                            'name' => $product['Name'] ?? 'unknown',
                            'error' => 'Chunk processing failed: ' . $e->getMessage()
                        ];
                        $processed++;
                    }
                }

                $percentage = min(99, (int)(($processed / $total) * 100));
                $status = "Processing... {$processed}/{$total} products (Chunk " . ($chunkIndex + 1) . "/{$totalChunks})";
                $this->updateProgress($percentage, $processed, $total, $status, [
                    'inserted' => $inserted,
                    'updated' => $updated,
                    'failed' => count($failed)
                ]);

                // Log progress every 10 chunks
                if (($chunkIndex + 1) % 10 == 0) {
                    Log::info('Product import chunk progress', [
                        'chunk' => $chunkIndex + 1,
                        'total_chunks' => $totalChunks,
                        'processed' => $processed,
                        'total' => $total,
                        'inserted' => $inserted,
                        'updated' => $updated,
                        'failed' => count($failed)
                    ]);
                }
            }

            $finalStatus = "Import completed! Inserted: {$inserted}, Updated: {$updated}";
            if (!empty($failed)) {
                $finalStatus .= ", Failed: " . count($failed);
            }
            $finalStatus .= " (Images import disabled - import separately)";

            Log::info('Product import completed successfully', [
                'job_id' => $this->jobId,
                'total' => $total,
                'processed' => $processed,
                'inserted' => $inserted,
                'updated' => $updated,
                'failed' => count($failed)
            ]);

            $this->updateProgress(100, $processed, $total, $finalStatus, [
                'inserted' => $inserted,
                'updated' => $updated,
                'failed' => count($failed),
                'images_imported' => 0,
                'failed_items' => $failed,
                'status' => 'completed'
            ]);

        } catch (\Exception $e) {
            Log::error('ImportEposNowProductsJob failed', [
                'job_id' => $this->jobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->updateProgress(0, 0, 0, 'Import failed: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'status' => 'failed'
            ]);
        }
    }

    // Update progress in cache
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

        Cache::put("product_import_{$this->jobId}", $progressData, 3600);
    }

    /**
     * Normalize barcode value - convert empty strings to null and handle array
     */
    private function normalizeBarcode($barcode): ?string
    {
        if (is_array($barcode)) {
            $barcode = $barcode[0] ?? null;
        }
        
        if ($barcode === null || $barcode === '' || $barcode === '0') {
            return null;
        }
        
        // Convert to string and trim
        $barcode = trim((string) $barcode);
        if ($barcode === '') {
            return null;
        }
        
        return $barcode;
    }

    /**
     * Truncate description for meta description (max 160 characters)
     */
    private function truncateDescription(?string $description, int $maxLength = 160): ?string
    {
        if (empty($description)) {
            return null;
        }
        
        // Remove HTML tags
        $description = strip_tags($description);
        
        // Trim whitespace
        $description = trim($description);
        
        if (strlen($description) <= $maxLength) {
            return $description;
        }
        
        // Truncate and add ellipsis
        return substr($description, 0, $maxLength - 3) . '...';
    }

    /**
     * Generate meta keywords from product name and category
     */
    private function generateMetaKeywords(string $productName, int $categoryId): ?string
    {
        $keywords = [];
        
        // Add product name words
        $nameWords = explode(' ', strtolower($productName));
        $keywords = array_merge($keywords, array_filter($nameWords, function($word) {
            return strlen($word) > 3; // Only words longer than 3 characters
        }));
        
        // Add category name if available
        if ($categoryId) {
            try {
                $category = \App\Models\Category::find($categoryId);
                if ($category && $category->name) {
                    $categoryWords = explode(' ', strtolower($category->name));
                    $keywords = array_merge($keywords, array_filter($categoryWords, function($word) {
                        return strlen($word) > 3;
                    }));
                }
            } catch (\Exception $e) {
                // Category not found, skip
            }
        }
        
        // Remove duplicates and limit to 10 keywords
        $keywords = array_unique($keywords);
        $keywords = array_slice($keywords, 0, 10);
        
        return !empty($keywords) ? implode(', ', $keywords) : null;
    }

    // Handle a job failure
    public function failed(\Throwable $exception): void
    {
        Log::error('ImportEposNowProductsJob failed', [
            'job_id' => $this->jobId,
            'error' => $exception->getMessage()
        ]);

        $this->updateProgress(0, 0, 0, 'Import failed: ' . $exception->getMessage(), [
            'error' => $exception->getMessage(),
            'status' => 'failed'
        ]);
    }
}
