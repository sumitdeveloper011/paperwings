<?php

use App\Jobs\ImportEposNowCategoriesJob;
use App\Jobs\ImportEposNowProductsJob;
use App\Jobs\ImportEposNowStockJob;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled tasks (Laravel 12.x scheduling)
|--------------------------------------------------------------------------
| See: https://laravel.com/docs/12.x/scheduling
|--------------------------------------------------------------------------
*/

Schedule::command('users:cleanup-unverified', ['--days' => 30])
    ->weekly()
    ->sundays()
    ->at('02:00')
    ->timezone('Pacific/Auckland')
    ->description('Clean up unverified users older than 30 days');

/*
|--------------------------------------------------------------------------
| EposNow imports (Pacific/Auckland)
|--------------------------------------------------------------------------
*/
Schedule::job(new ImportEposNowCategoriesJob(time() . '_' . uniqid()), 'imports')
    ->dailyAt('00:05')
    ->timezone('Pacific/Auckland')
    ->withoutOverlapping(55)
    ->description('Daily category import from EposNow');

Schedule::job(new ImportEposNowProductsJob(time() . '_' . uniqid()), 'imports')
    ->dailyAt('00:45')
    ->timezone('Pacific/Auckland')
    ->withoutOverlapping(55)
    ->description('Daily product import from EposNow');

Schedule::job(new ImportEposNowStockJob(time() . '_' . uniqid()), 'imports')
    ->dailyAt('01:30')
    ->timezone('Pacific/Auckland')
    ->withoutOverlapping(55)
    ->description('Daily stock import from EposNow');

/*
|--------------------------------------------------------------------------
| Queue worker (run via scheduler on shared hosting)
|--------------------------------------------------------------------------
| Uses queue:work with --stop-when-empty so the process exits after
| processing available jobs; next schedule run starts a fresh worker.
| See: https://laravel.com/docs/12.x/queues#running-the-queue-worker
|--------------------------------------------------------------------------
*/
Schedule::command('queue:work', [
    'database',
    '--queue=default,newsletters,imports',
    '--stop-when-empty',
    '--tries=3',
    '--max-time=120',
    '--timeout=3600',
    '--memory=256',
    '--sleep=5',
])
    ->everyTwoMinutes()
    ->withoutOverlapping(3)
    ->sendOutputTo(storage_path('logs/queue-worker.log'))
    ->description('Process queued jobs (shared hosting)');
