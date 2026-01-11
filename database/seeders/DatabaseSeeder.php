<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * 
     * IMPORTANT: This seeder will NOT run in production environment for safety.
     * Some seeders are safe to run in production (like RegionSeeder), but most
     * are development-only to prevent accidental data overwrites.
     */
    public function run(): void
    {
        // Check if running in production
        if (app()->environment('production')) {
            $this->command->error('❌ DatabaseSeeder cannot run in production environment!');
            $this->command->warn('⚠️  For production, run individual seeders manually if needed.');
            $this->command->info('   Safe seeders for production: RegionSeeder, TagSeeder');
            return;
        }

        $this->command->info('🚀 Starting database seeding...');
        $this->command->info('Environment: ' . app()->environment());
        $this->command->newLine();

        // Core seeders (run first)
        $this->command->info('📋 Running core seeders...');
        $this->call([
            RolePermissionSeeder::class,  // Roles and Permissions (must run first)
            UserSeeder::class,              // Users (admin + regular) - merged from AdminUserSeeder
        ]);

        // Data seeders (safe to run multiple times)
        $this->command->newLine();
        $this->command->info('📦 Running data seeders...');
        $this->call([
            RegionSeeder::class,            // New Zealand regions (safe for production)
            ShippingPriceSeeder::class,     // Shipping prices for all regions
            TagSeeder::class,               // Product tags (safe for production)
        ]);

        // Content seeders (development only)
        $this->command->newLine();
        $this->command->info('📝 Running content seeders...');
        $this->call([
            PageSeeder::class,              // Default pages
            AboutSectionSeeder::class,      // About sections
            SpecialOffersBannerSeeder::class, // Special offers
        ]);

        // Product-related seeders (development only)
        $this->command->newLine();
        $this->command->info('🛍️  Running product seeders...');
        $this->call([
            ProductFaqSeeder::class,        // Product FAQs
            ProductBundleSeeder::class,     // Product bundles
            ProductReviewSeeder::class,      // Product reviews
            ProductQuestionSeeder::class,    // Product questions
        ]);

        $this->command->newLine();
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->newLine();
        $this->command->info('📌 Login credentials:');
        $this->command->line('   SuperAdmin: admin@paperwings.co.nz');
        $this->command->line('   Admin: admin@example.com (password: Password123!)');
        $this->command->line('   Manager: manager@example.com (password: Password123!)');
        $this->command->line('   Editor: editor@example.com (password: Password123!)');
        $this->command->line('   User: user@example.com (password: Password123!)');
    }
}
