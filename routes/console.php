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
    ->timezone('Pacific/Auckland')
    ->description('Clean up unverified users older than 30 days');

Schedule::call(function () {
    $jobId = time() . '_' . uniqid();
    \App\Jobs\ImportEposNowCategoriesJob::dispatch($jobId);
})->dailyAt('00:05')
    ->timezone('Pacific/Auckland')
    ->description('Daily category import from EposNow at midnight');

Schedule::call(function () {
    $jobId = time() . '_' . uniqid();
    \App\Jobs\ImportEposNowProductsJob::dispatch($jobId);
})->dailyAt('00:45')
    ->timezone('Pacific/Auckland')
    ->description('Daily product import from EposNow at 00:45 AM (rescheduled for better rate limit coordination)');

Schedule::call(function () {
    $jobId = time() . '_' . uniqid();
    \App\Jobs\ImportEposNowStockJob::dispatch($jobId);
})->dailyAt('01:30')
    ->timezone('Pacific/Auckland')
    ->description('Daily stock import from EposNow at 01:30 AM (rescheduled for better rate limit coordination, bulk API - optimized)');

Schedule::command('queue:work database --queue=default,newsletters,imports --stop-when-empty --tries=3 --max-time=120 --max-jobs=15 --memory=256 --sleep=5')
    ->everyTwoMinutes()
    ->withoutOverlapping(3)
    ->sendOutputTo(storage_path('logs/queue-worker.log'))
    ->description('Process queued jobs - optimized for stability and rate limit handling');
