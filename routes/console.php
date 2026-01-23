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

// ========== AUTOMATED IMPORTS ==========
// Import Categories from EposNow - Daily at 12:00 AM (midnight)
Schedule::call(function () {
    $jobId = time() . '_' . uniqid();
    \App\Jobs\ImportEposNowCategoriesJob::dispatch($jobId);
})->dailyAt('00:05')
    ->timezone(config('app.timezone'))
    ->description('Daily category import from EposNow at midnight');

// Import Products from EposNow - Daily at 12:00 AM (midnight) - 5 minutes after categories
Schedule::call(function () {
    $jobId = time() . '_' . uniqid();
    \App\Jobs\ImportEposNowProductsJob::dispatch($jobId);
})->dailyAt('00:30')
    ->timezone(config('app.timezone'))
    ->description('Daily product import from EposNow at 12:05 AM');

// ========== QUEUE PROCESSING ==========
// Process regular queue (emails, notifications) - every 30 seconds
// Note: Cron minimum is 1 minute, so we'll run every minute but process multiple jobs
Schedule::command('queue:work database --queue=default --stop-when-empty --tries=3 --max-time=60 --max-jobs=10')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Process queued jobs (emails, notifications) - every minute');

// Process newsletter queue - every 2 minutes (newsletters are less urgent)
Schedule::command('queue:work database --queue=newsletters --stop-when-empty --tries=3 --max-time=120 --max-jobs=5')
    ->everyTwoMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Process newsletter emails - every 2 minutes');

// Process import jobs queue - every 5 minutes (only when imports are manually triggered)
Schedule::command('queue:work database --queue=imports --stop-when-empty --tries=1 --max-time=3600')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Process import jobs (products, categories) - every 5 minutes');
