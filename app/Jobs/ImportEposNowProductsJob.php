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

    // Create a new job instance
    public function __construct(string $jobId)
    {
        $this->jobId = $jobId;
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
                $this->updateProgress(10, 0, 0, 'Fetching products from EPOSNOW API (this may take a few minutes)...');
                $products = $eposNowService->getAllProducts();
                $this->updateProgress(20, 0, 0, 'Products fetched successfully. Processing data...');
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

            $chunks = array_chunk($products, 50);

            foreach ($chunks as $chunkIndex => $chunk) {
                DB::transaction(function () use ($chunk, $categoryRepository, $eposNowService, &$processed, &$inserted, &$updated, &$failed, &$imagesImported, $total) {
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

                            $productData = [
                                'category_id' => $categoryId,
                                'brand_id' => null,
                                'eposnow_product_id' => $eposnowProductId,
                                'eposnow_category_id' => $product['CategoryId'] ?? null,
                                'eposnow_brand_id' => $product['BrandId'] ?? null,
                                'barcode' => is_array($product['Barcode'] ?? null) 
                                    ? (string) ($product['Barcode'][0] ?? null) 
                                    : (string) ($product['Barcode'] ?? null) ?? null,
                                'stock' => null,
                                'product_type' => null,
                                'name' => $productName,
                                'slug' => $slug,
                                'total_price' => $product['SalePrice'] ?? 0.00,
                                'discount_price' => null,
                                'description' => $product['Description'] ?? null,
                                'short_description' => null,
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
                });

                $percentage = (int)(($processed / $total) * 100);
                $status = "Processing... {$processed}/{$total} products";
                $this->updateProgress($percentage, $processed, $total, $status);
            }

            $finalStatus = "Import completed! Inserted: {$inserted}, Updated: {$updated}, Images: {$imagesImported}";
            if (!empty($failed)) {
                $finalStatus .= ", Failed: " . count($failed);
            }

            $this->updateProgress(100, $processed, $total, $finalStatus, [
                'inserted' => $inserted,
                'updated' => $updated,
                'failed' => count($failed),
                'images_imported' => $imagesImported,
                'failed_items' => $failed
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
