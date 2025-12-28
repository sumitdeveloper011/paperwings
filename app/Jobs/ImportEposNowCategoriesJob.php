<?php

namespace App\Jobs;

use App\Models\Category;
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

class ImportEposNowCategoriesJob implements ShouldQueue
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
                $this->updateProgress(10, 0, 0, 'Fetching categories from EPOSNOW API (this may take a few minutes)...');
                $categories = $eposNowService->getAllCategories();
                $this->updateProgress(20, 0, 0, 'Categories fetched successfully. Processing data...');
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

            if (empty($categories)) {
                $this->updateProgress(100, 0, 0, 'No categories found in EposNow API');
                return;
            }

            $total = count($categories);
            $processed = 0;
            $inserted = 0;
            $updated = 0;
            $failed = [];

            $chunks = array_chunk($categories, 50);

            foreach ($chunks as $chunkIndex => $chunk) {
                DB::transaction(function () use ($chunk, $categoryRepository, &$processed, &$inserted, &$updated, &$failed, $total) {
                    foreach ($chunk as $cat) {
                        try {
                            if (!isset($cat['Id']) || !isset($cat['Name'])) {
                                $failed[] = [
                                    'id' => $cat['Id'] ?? 'unknown',
                                    'name' => $cat['Name'] ?? 'unknown',
                                    'error' => 'Missing required fields (Id or Name)'
                                ];
                                $processed++;
                                continue;
                            }

                            $eposnowCategoryId = $cat['Id'];
                            $categoryName = $cat['Name'];
                            $categorySlug = Str::slug($categoryName);

                            $existing = Category::where('eposnow_category_id', $eposnowCategoryId)->first();

                            $categoryData = [
                                'eposnow_category_id' => $eposnowCategoryId,
                                'name' => $categoryName,
                                'slug' => $categorySlug,
                                'description' => $cat['Description'] ?? null,
                                'status' => 1,
                                'image' => null,
                            ];

                            if ($existing) {
                                $hasChanges = false;
                                if ($existing->name !== $categoryName) {
                                    $hasChanges = true;
                                }
                                if ($existing->description !== ($categoryData['description'])) {
                                    $hasChanges = true;
                                }
                                if ($existing->slug !== $categorySlug) {
                                    $hasChanges = true;
                                }

                                if ($hasChanges) {
                                    if (empty($existing->uuid)) {
                                        $categoryData['uuid'] = Str::uuid();
                                    }
                                    $categoryRepository->update($existing, $categoryData);
                                    $updated++;
                                } else {
                                    $processed++;
                                    continue;
                                }
                            } else {
                                $categoryData['uuid'] = Str::uuid();
                                $categoryRepository->create($categoryData);
                                $inserted++;
                            }

                            $processed++;
                        } catch (\Exception $e) {
                            $failed[] = [
                                'id' => $cat['Id'] ?? 'unknown',
                                'name' => $cat['Name'] ?? 'unknown',
                                'error' => $e->getMessage()
                            ];
                            $processed++;
                            Log::error('Category import failed', [
                                'category_id' => $cat['Id'] ?? null,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                });

                $percentage = (int)(($processed / $total) * 100);
                $status = "Processing... {$processed}/{$total} categories";
                $this->updateProgress($percentage, $processed, $total, $status);
            }

            $finalStatus = "Import completed! Inserted: {$inserted}, Updated: {$updated}";
            if (!empty($failed)) {
                $finalStatus .= ", Failed: " . count($failed);
            }

            $this->updateProgress(100, $processed, $total, $finalStatus, [
                'inserted' => $inserted,
                'updated' => $updated,
                'failed' => count($failed),
                'failed_items' => $failed
            ]);

        } catch (\Exception $e) {
            Log::error('ImportEposNowCategoriesJob failed', [
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

        Cache::put("category_import_{$this->jobId}", $progressData, 3600);
    }

    // Handle a job failure
    public function failed(\Throwable $exception): void
    {
        Log::error('ImportEposNowCategoriesJob failed', [
            'job_id' => $this->jobId,
            'error' => $exception->getMessage()
        ]);

        $this->updateProgress(0, 0, 0, 'Import failed: ' . $exception->getMessage(), [
            'error' => $exception->getMessage(),
            'status' => 'failed'
        ]);
    }
}
