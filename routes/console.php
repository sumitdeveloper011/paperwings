<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('users:cleanup-unverified --days=30')
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->timezone('UTC')
    ->description('Clean up unverified users older than 30 days');

Schedule::call(function () {
    $jobId = time() . '_' . uniqid();
    \App\Jobs\ImportEposNowCategoriesJob::dispatch($jobId);
})->dailyAt('00:05')
    ->timezone(config('app.timezone'))
    ->description('Daily category import from EposNow at midnight');

// Import Products from EposNow - Daily at 12:30 AM - 25 minutes after categories
Schedule::call(function () {
    $jobId = time() . '_' . uniqid();
    \App\Jobs\ImportEposNowProductsJob::dispatch($jobId);
})->dailyAt('00:30')
    ->timezone(config('app.timezone'))
    ->description('Daily product import from EposNow at 00:30 AM');

// Import Product Stock from EposNow - Daily at 01:00 AM - 30 minutes after products
Schedule::call(function () {
    $jobId = time() . '_' . uniqid();
    \App\Jobs\ImportEposNowStockJob::dispatch($jobId);
})->dailyAt('01:00')
    ->timezone(config('app.timezone'))
    ->description('Daily stock import from EposNow at 01:00 AM');

// ========== QUEUE PROCESSING ==========
// Single queue worker processes ALL queues in priority order
// Priority: default (emails/notifications) > newsletters > imports
// This approach is safe for shared hosting - no background processes
// Optimized settings: lower max-jobs and max-time to prevent memory issues and OOM kills
Schedule::command('queue:work database --queue=default,newsletters,imports --stop-when-empty --tries=3 --max-time=120 --max-jobs=15 --memory=128 --sleep=5')
    ->everyMinute()
    ->withoutOverlapping(3)
    ->sendOutputTo(storage_path('logs/queue-worker.log'))
    ->description('Process queued jobs - optimized for stability and rate limit handling');

// Restart queue worker every 15 minutes to prevent memory leaks
// This ensures workers pick up code changes and don't accumulate memory
Schedule::command('queue:restart')
    ->everyFifteenMinutes()
    ->description('Restart queue workers to prevent memory leaks');
