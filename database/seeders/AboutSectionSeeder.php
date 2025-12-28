<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AboutSection;
use Illuminate\Support\Str;

class AboutSectionSeeder extends Seeder
{
    public function run(): void
    {
        $aboutSections = [
            [
                'badge' => 'THE STATIONERO',
                'title' => 'The Stationery Company',
                'description' => 'Our Office Supplies Will Help You Organize Your Workspace From All Kinds Of Desk Essentials To Top Quality Staplers, Calculators And Organizers.',
                'image' => null, // You can add image path if you have images
                'button_text' => 'Find Out More',
                'button_link' => '/about-us',
                'status' => 1,
                'sort_order' => 1,
            ],
            [
                'badge' => 'QUALITY FIRST',
                'title' => 'Premium Stationery Solutions',
                'description' => 'We provide high-quality stationery products that help you stay organized and productive. From elegant notebooks to professional office supplies, we have everything you need.',
                'image' => null,
                'button_text' => 'Shop Now',
                'button_link' => '/shop',
                'status' => 1,
                'sort_order' => 2,
            ],
            [
                'badge' => 'YOUR TRUSTED PARTNER',
                'title' => 'Paper Wings - Your Stationery Destination',
                'description' => 'With years of experience in the stationery industry, we are committed to providing the best products and services to our customers. Quality, reliability, and customer satisfaction are our top priorities.',
                'image' => null,
                'button_text' => 'Learn More',
                'button_link' => '/contact-us',
                'status' => 1,
                'sort_order' => 3,
            ],
        ];

        foreach ($aboutSections as $aboutSectionData) {
            AboutSection::updateOrCreate(
                [
                    'title' => $aboutSectionData['title']
                ],
                [
                    'uuid' => Str::uuid(),
                    'badge' => $aboutSectionData['badge'],
                    'title' => $aboutSectionData['title'],
                    'description' => $aboutSectionData['description'],
                    'image' => $aboutSectionData['image'],
                    'button_text' => $aboutSectionData['button_text'],
                    'button_link' => $aboutSectionData['button_link'],
                    'status' => $aboutSectionData['status'],
                    'sort_order' => $aboutSectionData['sort_order'],
                ]
            );
        }

        $this->command->info('About Sections seeded successfully!');
    }
}
