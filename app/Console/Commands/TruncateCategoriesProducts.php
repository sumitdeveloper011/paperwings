<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateCategoriesProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:truncate-categories-products 
                            {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate categories and products tables (handles foreign key constraints)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to truncate categories and products tables? This will delete ALL data!')) {
                $this->info('Operation cancelled.');
                return 0;
            }
        }

        try {
            $this->info('Starting truncation...');

            // Disable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS = 0');
            $this->info('Foreign key checks disabled.');

            // Check if tables exist
            if (!Schema::hasTable('products')) {
                $this->error('Products table does not exist!');
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
                return 1;
            }

            if (!Schema::hasTable('categories')) {
                $this->error('Categories table does not exist!');
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
                return 1;
            }

            // Truncate products first (since it references categories)
            $this->info('Truncating products table...');
            DB::table('products')->truncate();
            $this->info('✓ Products table truncated.');

            // Truncate categories
            $this->info('Truncating categories table...');
            DB::table('categories')->truncate();
            $this->info('✓ Categories table truncated.');

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            $this->info('Foreign key checks re-enabled.');

            $this->info('');
            $this->info('✓ Successfully truncated both tables!');
            $this->warn('Note: Related records in other tables may have been affected by cascade deletes.');

            return 0;
        } catch (\Exception $e) {
            // Re-enable foreign key checks even if error occurs
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            
            $this->error('Error occurred: ' . $e->getMessage());
            return 1;
        }
    }
}
