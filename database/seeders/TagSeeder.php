<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tag;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    public function run(): void
    {
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

        foreach ($tags as $tagName) {
            Tag::firstOrCreate(
                ['name' => $tagName],
                ['slug' => Str::slug($tagName)]
            );
        }
    }
}
