<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class CheckStockImportStatus extends Command
{
    protected $signature = 'stock:check-status {--job-id= : Specific job ID to check}';
    protected $description = 'Check stock import job status and recent activity';

    public function handle()
    {
        $this->info('=== Stock Import Status Check ===');
        $this->newLine();

        if ($jobId = $this->option('job-id')) {
            $this->checkSpecificJob($jobId);
        } else {
            $this->checkQueueStatus();
            $this->checkRecentActivity();
            $this->checkScheduledTask();
        }

        return 0;
    }

    protected function checkSpecificJob(string $jobId)
    {
        $this->info("Checking job: {$jobId}");
        $cacheKey = "stock_import_{$jobId}";
        $progress = Cache::get($cacheKey);

        if ($progress) {
            $this->table(
                ['Field', 'Value'],
                [
                    ['Status', $progress['status'] ?? 'unknown'],
                    ['Percentage', ($progress['percentage'] ?? 0) . '%'],
                    ['Processed', $progress['processed'] ?? 0],
                    ['Total', $progress['total'] ?? 0],
                    ['Updated', $progress['updated'] ?? 0],
                    ['Skipped', $progress['skipped'] ?? 0],
                    ['Failed', $progress['failed'] ?? 0],
                    ['Message', $progress['message'] ?? 'N/A'],
                    ['Last Updated', $progress['updated_at'] ?? 'N/A'],
                ]
            );
        } else {
            $this->warn("Job not found in cache. It may have expired or not started yet.");
        }
    }

    protected function checkQueueStatus()
    {
        $this->info('1. Queue Status:');
        
        $pendingJobs = DB::table('jobs')
            ->where('queue', 'imports')
            ->count();

        $failedJobs = DB::table('failed_jobs')
            ->where('queue', 'imports')
            ->count();

        $this->line("   Pending jobs in 'imports' queue: {$pendingJobs}");
        $this->line("   Failed jobs in 'imports' queue: {$failedJobs}");
        
        if ($pendingJobs > 0) {
            $this->warn("   ⚠️  There are {$pendingJobs} pending job(s) waiting to be processed.");
        } else {
            $this->info("   ✓ No pending jobs");
        }
        $this->newLine();
    }

    protected function checkRecentActivity()
    {
        $this->info('2. Recent Stock Updates:');
        
        $recentUpdates = Product::whereNotNull('eposnow_product_id')
            ->where('eposnow_product_id', '!=', 0)
            ->whereNotNull('stock')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get(['id', 'name', 'stock', 'eposnow_product_id', 'updated_at']);

        if ($recentUpdates->count() > 0) {
            $this->table(
                ['ID', 'Name', 'Stock', 'EposNow ID', 'Last Updated'],
                $recentUpdates->map(function ($product) {
                    return [
                        $product->id,
                        substr($product->name, 0, 30),
                        $product->stock,
                        $product->eposnow_product_id,
                        $product->updated_at->format('Y-m-d H:i:s'),
                    ];
                })->toArray()
            );
        } else {
            $this->warn("   No products with stock data found.");
        }
        $this->newLine();
    }

    protected function checkScheduledTask()
    {
        $this->info('3. Scheduled Task:');
        $this->line("   Scheduled time: 01:00 AM daily");
        $this->line("   Timezone: Pacific/Auckland");
        $this->line("   Current time: " . now()->format('Y-m-d H:i:s'));
        $this->newLine();
        
        $this->info('4. Quick Test:');
        $this->line("   To test manually, run:");
        $this->line("   php artisan tinker");
        $this->line("   Then: \\App\\Jobs\\ImportEposNowStockJob::dispatch(time() . '_test');");
        $this->newLine();
    }
}
