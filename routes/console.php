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

// ========== QUEUE PROCESSING ==========
// Process regular queue (emails, notifications) - every minute
Schedule::command('queue:work database --stop-when-empty --tries=3 --max-time=60')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Process queued jobs (emails, notifications)');

// Process newsletter queue - every minute, faster processing
Schedule::command('queue:work database --queue=newsletters --stop-when-empty --tries=3 --max-time=60')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Process newsletter emails');

// Process import jobs separately (long-running) - every 5 minutes
Schedule::command('queue:work database --queue=imports --stop-when-empty --tries=1 --max-time=3600')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->description('Process import jobs (products, categories)');
