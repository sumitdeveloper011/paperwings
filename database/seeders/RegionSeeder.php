<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Region;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

        foreach ($regions as $regionName) {
            Region::updateOrCreate(
                ['name' => $regionName],
                [
                    'slug' => \Illuminate\Support\Str::slug($regionName),
                    'status' => 1, // Active
                ]
            );
        }
    }
}
