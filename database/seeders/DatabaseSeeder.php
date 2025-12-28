<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\RegionSeeder;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\PageSeeder;
use Database\Seeders\TagSeeder;
use Database\Seeders\ProductFaqSeeder;
use Database\Seeders\ProductBundleSeeder;
use Database\Seeders\ProductReviewSeeder;
use Database\Seeders\ProductQuestionSeeder;
use Database\Seeders\SpecialOffersBannerSeeder;
use Database\Seeders\AboutSectionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            AdminUserSeeder::class,
            UserSeeder::class,
            PermissionSeeder::class,
            RegionSeeder::class,
            PageSeeder::class,
            // New feature seeders
            TagSeeder::class,
            ProductFaqSeeder::class,
            ProductBundleSeeder::class,
            ProductReviewSeeder::class,
            ProductQuestionSeeder::class,
                   SpecialOffersBannerSeeder::class,
                   AboutSectionSeeder::class,
                   RolePermissionSeeder::class,
               ]);
    }
}
