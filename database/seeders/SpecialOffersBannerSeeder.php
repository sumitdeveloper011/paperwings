<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SpecialOffersBanner;
use Illuminate\Support\Str;

class SpecialOffersBannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates special offers banners.
     * Will NOT run in production environment for safety.
     */
    public function run(): void
    {
        // Prevent running in production
        if (app()->environment('production')) {
            $this->command->warn('⚠️  SpecialOffersBannerSeeder skipped: Cannot run in production environment!');
            return;
        }

        $this->command->info('🌱 Seeding special offers banners...');

        $banners = [
            [
                'title' => 'Summer Sale - Up to 50% OFF',
                'description' => 'Get amazing discounts on all stationery items. Limited time offer!',
                'image' => null, // You can add image path if you have images
                'button_text' => 'Shop Now',
                'button_link' => '/shop',
                'start_date' => now()->subDays(5),
                'end_date' => now()->addDays(30),
                'show_countdown' => true,
                'status' => 1,
                'sort_order' => 1,
            ],
            [
                'title' => 'Back to School Special',
                'description' => 'Everything you need for the new school year. Save big on school supplies!',
                'image' => null,
                'button_text' => 'Explore Collection',
                'button_link' => '/category/school-supplies',
                'start_date' => now()->subDays(2),
                'end_date' => now()->addDays(45),
                'show_countdown' => true,
                'status' => 1,
                'sort_order' => 2,
            ],
            [
                'title' => 'Free Shipping on Orders Over $50',
                'description' => 'Order now and get free shipping on all orders above $50. No code needed!',
                'image' => null,
                'button_text' => 'Start Shopping',
                'button_link' => '/shop',
                'start_date' => now(),
                'end_date' => now()->addDays(60),
                'show_countdown' => false,
                'status' => 1,
                'sort_order' => 3,
            ],
            [
                'title' => 'New Arrivals - Fresh Collection',
                'description' => 'Check out our latest stationery collection. New products added weekly!',
                'image' => null,
                'button_text' => 'View New Products',
                'button_link' => '/shop?sort=newest',
                'start_date' => now()->subDays(10),
                'end_date' => now()->addDays(20),
                'show_countdown' => false,
                'status' => 1,
                'sort_order' => 4,
            ],
            [
                'title' => 'Bundle Deals - Save More',
                'description' => 'Buy product bundles and save up to 30% on your favorite items.',
                'image' => null,
                'button_text' => 'View Bundles',
                'button_link' => '/bundles',
                'start_date' => now()->subDays(3),
                'end_date' => now()->addDays(25),
                'show_countdown' => true,
                'status' => 1,
                'sort_order' => 5,
            ],
        ];

        foreach ($banners as $bannerData) {
            SpecialOffersBanner::updateOrCreate(
                [
                    'title' => $bannerData['title']
                ],
                [
                    'uuid' => Str::uuid(),
                    'title' => $bannerData['title'],
                    'description' => $bannerData['description'],
                    'image' => $bannerData['image'],
                    'button_text' => $bannerData['button_text'],
                    'button_link' => $bannerData['button_link'],
                    'start_date' => $bannerData['start_date'],
                    'end_date' => $bannerData['end_date'],
                    'show_countdown' => $bannerData['show_countdown'],
                    'status' => $bannerData['status'],
                    'sort_order' => $bannerData['sort_order'],
                ]
            );
        }

        $created = 0;
        $updated = 0;

        foreach ($banners as $bannerData) {
            $banner = SpecialOffersBanner::updateOrCreate(
                ['title' => $bannerData['title']],
                array_merge($bannerData, ['uuid' => Str::uuid()])
            );

            if ($banner->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("✅ Special Offers Banners seeded successfully!");
        $this->command->info("  • Created: {$created}");
        $this->command->info("  • Updated: {$updated}");
        $this->command->info("  • Total: " . count($banners));
    }
}

