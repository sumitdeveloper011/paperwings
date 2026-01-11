<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates New Zealand regions.
     * Safe to run in production as it uses updateOrCreate.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding regions...');

        $regions = [
            'Northland',
            'Auckland',
            'Waikato',
            'Bay of Plenty',
            'Gisborne',
            "Hawke's Bay",
            'Taranaki',
            'Manawatū-Whanganui',
            'Wellington',
            'Tasman',
            'Nelson',
            'Marlborough',
            'West Coast',
            'Canterbury',
            'Otago',
            'Southland',
        ];

        $created = 0;
        $updated = 0;

        foreach ($regions as $regionName) {
            $region = Region::updateOrCreate(
                ['name' => $regionName],
                [
                    'slug' => \Illuminate\Support\Str::slug($regionName),
                    'status' => 1, // Active
                ]
            );

            if ($region->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("✅ Regions seeded successfully!");
        $this->command->info("  • Created: {$created}");
        $this->command->info("  • Updated: {$updated}");
        $this->command->info("  • Total: " . count($regions));
    }
}
