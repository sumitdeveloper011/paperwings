<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates 5 diverse testimonials covering different scenarios:
     * - Different ratings (3-5 stars)
     * - With/Without images
     * - With/Without designation
     * - Active/Inactive status
     * - Different sort orders
     */
    public function run(): void
    {
        $this->command->info('⭐ Seeding testimonials...');

        // Pre-generate UUIDs for each testimonial to use as unique identifier
        $uuids = [];
        for ($i = 0; $i < 5; $i++) {
            $uuids[] = Str::uuid()->toString();
        }

        $testimonials = [
            // 1. 5 Star - Active - With Designation
            [
                'uuid' => $uuids[0],
                'name' => 'John Smith',
                'email' => 'john.smith@example.com',
                'review' => '<p>Excellent service and high-quality products! I\'ve been a customer for over 2 years and I\'m always impressed with the fast shipping and great customer support. Highly recommended!</p>',
                'rating' => 5,
                'image' => null, // Can be set to actual image path if needed
                'designation' => 'CEO, Tech Solutions Inc.',
                'status' => 1,
                'sort_order' => 1,
            ],

            // 2. 4 Star - Active - Without Designation
            [
                'uuid' => $uuids[1],
                'name' => 'Sarah Johnson',
                'email' => 'sarah.j@example.com',
                'review' => '<p>Great experience shopping here! The products are exactly as described and the delivery was prompt. The only reason I\'m giving 4 stars is because I wish there were more color options available.</p>',
                'rating' => 4,
                'image' => null,
                'designation' => null,
                'status' => 1,
                'sort_order' => 2,
            ],

            // 3. 5 Star - Active - With Designation
            [
                'uuid' => $uuids[2],
                'name' => 'Michael Chen',
                'email' => 'michael.chen@example.com',
                'review' => '<p>Outstanding quality and exceptional customer service! I\'ve ordered multiple times and each experience has been perfect. The team really cares about their customers.</p>',
                'rating' => 5,
                'image' => null,
                'designation' => 'Marketing Director',
                'status' => 1,
                'sort_order' => 3,
            ],

            // 4. 3 Star - Active - Honest Review
            [
                'uuid' => $uuids[3],
                'name' => 'Emily Davis',
                'email' => 'emily.davis@example.com',
                'review' => '<p>Good products overall, but there\'s room for improvement. The quality is decent and prices are fair. Customer service responded quickly to my inquiry, which was appreciated.</p>',
                'rating' => 3,
                'image' => null,
                'designation' => 'Freelance Designer',
                'status' => 1,
                'sort_order' => 4,
            ],

            // 5. 5 Star - Inactive - For Testing
            [
                'uuid' => $uuids[4],
                'name' => 'David Wilson',
                'email' => 'david.wilson@example.com',
                'review' => '<p>Absolutely fantastic! Best purchase I\'ve made this year. The product exceeded my expectations and the support team was incredibly helpful throughout the process.</p>',
                'rating' => 5,
                'image' => null,
                'designation' => 'Business Owner',
                'status' => 0,
                'sort_order' => 5,
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($testimonials as $testimonialData) {
            // Ensure UUID exists, generate if missing
            if (!isset($testimonialData['uuid']) || empty($testimonialData['uuid'])) {
                $testimonialData['uuid'] = Str::uuid()->toString();
            }

            // Use updateOrCreate with uuid as unique identifier
            $testimonial = Testimonial::updateOrCreate(
                ['uuid' => $testimonialData['uuid']],
                $testimonialData
            );

            if ($testimonial->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("✅ Created: {$created} testimonials");
        $this->command->info("🔄 Updated: {$updated} testimonials");
        $this->command->info("📊 Total: " . count($testimonials) . " testimonials seeded");
        $this->command->newLine();
        $this->command->info('📌 Testimonial Summary:');
        $this->command->line('   • 5 Star Ratings: ' . count(array_filter($testimonials, fn($t) => $t['rating'] === 5)));
        $this->command->line('   • 4 Star Ratings: ' . count(array_filter($testimonials, fn($t) => $t['rating'] === 4)));
        $this->command->line('   • 3 Star Ratings: ' . count(array_filter($testimonials, fn($t) => $t['rating'] === 3)));
        $this->command->line('   • Active Testimonials: ' . count(array_filter($testimonials, fn($t) => $t['status'] === 1)));
        $this->command->line('   • Inactive Testimonials: ' . count(array_filter($testimonials, fn($t) => $t['status'] === 0)));
        $this->command->line('   • With Designation: ' . count(array_filter($testimonials, fn($t) => !empty($t['designation']))));
    }
}
