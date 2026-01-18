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
