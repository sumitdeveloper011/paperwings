<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates product tags.
     * Safe to run in production as it uses firstOrCreate.
     */
    public function run(): void
    {
        $this->command->info('🌱 Seeding tags...');

        $tags = [
            'Stationery',
            'Office Supplies',
            'School Supplies',
            'Art Supplies',
            'Writing Tools',
            'Notebooks',
            'Pens',
            'Pencils',
            'Markers',
            'Highlighters',
            'Erasers',
            'Rulers',
            'Folders',
            'Binders',
            'Paper',
            'Sticky Notes',
            'Desk Organizers',
            'Backpacks',
            'Bags',
            'Gift Sets',
        ];

        $created = 0;
        $skipped = 0;

        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate(
                ['name' => $tagName],
                ['slug' => Str::slug($tagName)]
            );

            if ($tag->wasRecentlyCreated) {
                $created++;
            } else {
                $skipped++;
            }
        }

        $this->command->info("✅ Tags seeded successfully!");
        $this->command->info("  • Created: {$created}");
        $this->command->info("  • Skipped (already exist): {$skipped}");
        $this->command->info("  • Total: " . count($tags));
    }
}
