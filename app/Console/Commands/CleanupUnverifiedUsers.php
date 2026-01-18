<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CleanupUnverifiedUsers extends Command
{
    protected $signature = 'users:cleanup-unverified 
                            {--days=30 : Number of days after which unverified users should be deleted}
                            {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Delete unverified users older than specified days (default: 30 days)';

    public function handle()
    {
        $days = (int) $this->option('days');
        $dryRun = $this->option('dry-run');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Looking for unverified users created before: {$cutoffDate->format('Y-m-d H:i:s')}");

        $unverifiedUsers = User::whereNull('email_verified_at')
            ->where('created_at', '<', $cutoffDate)
            ->get();

        if ($unverifiedUsers->isEmpty()) {
            $this->info('No unverified users found to clean up.');
            return 0;
        }

        $count = $unverifiedUsers->count();
        $this->warn("Found {$count} unverified user(s) to delete:");

        foreach ($unverifiedUsers as $user) {
            $this->line("  - ID: {$user->id}, Email: {$user->email}, Created: {$user->created_at->format('Y-m-d H:i:s')}");
        }

        if ($dryRun) {
            $this->info("\n[DRY RUN] No users were deleted. Remove --dry-run to perform actual deletion.");
            return 0;
        }

        if (!$this->confirm("\nAre you sure you want to delete these {$count} unverified user(s)?")) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $deletedCount = 0;
        foreach ($unverifiedUsers as $user) {
            try {
                $email = $user->email;
                $user->delete();
                $deletedCount++;

                Log::info('Unverified user deleted by cleanup command', [
                    'user_id' => $user->id,
                    'email' => $email,
                    'created_at' => $user->created_at,
                    'days_old' => $user->created_at->diffInDays(Carbon::now())
                ]);
            } catch (\Exception $e) {
                $this->error("Failed to delete user ID {$user->id}: {$e->getMessage()}");
                Log::error('Failed to delete unverified user during cleanup', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("\n✓ Successfully deleted {$deletedCount} unverified user(s).");
        
        return 0;
    }
}
