<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TruncateEmailTemplates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:truncate-email-templates 
                            {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate email_templates and email_template_variables tables (handles foreign key constraints)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('Are you sure you want to truncate email templates tables? This will delete ALL email template data!')) {
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
            if (!Schema::hasTable('email_templates')) {
                $this->error('Email templates table does not exist!');
                DB::statement('SET FOREIGN_KEY_CHECKS = 1');
                return 1;
            }

            if (!Schema::hasTable('email_template_variables')) {
                $this->warn('Email template variables table does not exist. Skipping...');
            } else {
                // Truncate email_template_variables first (since it references email_templates)
                $this->info('Truncating email_template_variables table...');
                DB::table('email_template_variables')->truncate();
                $this->info('✓ Email template variables table truncated.');
            }

            // Truncate email_templates
            $this->info('Truncating email_templates table...');
            DB::table('email_templates')->truncate();
            $this->info('✓ Email templates table truncated.');

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            $this->info('Foreign key checks re-enabled.');

            $this->info('');
            $this->info('✓ Successfully truncated email templates tables!');
            $this->warn('Note: All email templates and their variables have been deleted.');
            $this->info('You can restore them by running: php artisan db:seed --class=EmailTemplateSeeder');

            return 0;
        } catch (\Exception $e) {
            // Re-enable foreign key checks even if error occurs
            DB::statement('SET FOREIGN_KEY_CHECKS = 1');
            
            $this->error('Error occurred: ' . $e->getMessage());
            return 1;
        }
    }
}
