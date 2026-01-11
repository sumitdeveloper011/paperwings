<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AboutSection;
use Illuminate\Support\Str;

class AboutSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder creates a single About Section entry.
     * Will NOT run in production environment for safety.
     */
    public function run(): void
    {
        // Prevent running in production
        if (app()->environment('production')) {
            $this->command->warn('⚠️  AboutSectionSeeder skipped: Cannot run in production environment!');
            return;
        }

        $this->command->info('🌱 Seeding about section...');

        // Delete all existing entries first (since we only want one)
        $existingCount = AboutSection::count();
        if ($existingCount > 0) {
            AboutSection::truncate();
            $this->command->info("  • Removed {$existingCount} existing entries");
        }

        // Create single About Section entry
        $aboutSection = AboutSection::create([
            'badge' => 'THE STATIONERO',
            'title' => 'The Stationery Company',
            'description' => 'Our Office Supplies Will Help You Organize Your Workspace From All Kinds Of Desk Essentials To Top Quality Staplers, Calculators And Organizers.',
            'image' => null, // You can add image path if you have images
            'button_text' => 'Find Out More',
            'button_link' => '/about-us',
            'status' => 1, // Active
            'sort_order' => 0,
        ]);

        $this->command->info("✅ About Section seeded successfully!");
        $this->command->info("  • Created: 1 entry");
        $this->command->info("  • Title: {$aboutSection->title}");
        $this->command->info("  • Status: " . ($aboutSection->status ? 'Active' : 'Inactive'));
    }
}
