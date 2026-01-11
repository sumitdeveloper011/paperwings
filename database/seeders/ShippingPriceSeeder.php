<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ShippingPrice;
use App\Models\Region;
use Illuminate\Support\Str;

class ShippingPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates shipping prices for all regions.
     * Will NOT run in production environment for safety.
     */
    public function run(): void
    {
        // Prevent running in production
        if (app()->environment('production')) {
            $this->command->warn('⚠️  ShippingPriceSeeder skipped: Cannot run in production environment!');
            return;
        }

        $this->command->info('🌱 Seeding shipping prices...');

        // Get all active regions
        $regions = Region::active()->get();

        if ($regions->isEmpty()) {
            $this->command->warn('⚠️  No active regions found. Please run RegionSeeder first.');
            return;
        }

        $created = 0;
        $updated = 0;
        $shippingPrice = 10.00; // Set shipping price to 10 for all regions

        foreach ($regions as $region) {
            $shippingPriceRecord = ShippingPrice::updateOrCreate(
                ['region_id' => $region->id],
                [
                    'uuid' => Str::uuid(),
                    'shipping_price' => $shippingPrice,
                    'free_shipping_minimum' => null,
                    'status' => 1, // Active
                ]
            );

            if ($shippingPriceRecord->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("✅ Shipping prices seeded successfully!");
        $this->command->info("  • Created: {$created}");
        $this->command->info("  • Updated: {$updated}");
        $this->command->info("  • Total: " . $regions->count());
        $this->command->info("  • Shipping Price: $" . number_format($shippingPrice, 2) . " for all regions");
    }
}
