<?php

namespace Database\Seeders;

use App\Models\Coupon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates comprehensive coupon data covering all possible cases:
     * - Percentage and Fixed discount types
     * - Active/Inactive status
     * - Current/Future/Expired dates
     * - With/Without usage limits
     * - With/Without minimum amount
     * - With/Without maximum discount
     * - With/Without usage_limit_per_user
     * - Various usage counts
     */
    public function run(): void
    {
        $this->command->info('🎫 Seeding coupons...');

        $now = now();
        $yesterday = $now->copy()->subDay();
        $tomorrow = $now->copy()->addDay();
        $nextWeek = $now->copy()->addWeek();
        $nextMonth = $now->copy()->addMonth();
        $lastMonth = $now->copy()->subMonth();
        $lastWeek = $now->copy()->subWeek();

        // Pre-generate UUIDs for each coupon to use as unique identifier
        $uuids = [];
        for ($i = 0; $i < 25; $i++) {
            $uuids[] = Str::uuid()->toString();
        }

        $coupons = [
            // ========== PERCENTAGE DISCOUNT COUPONS ==========

            // 1. Active Percentage - Current (No limits)
            [
                'uuid' => $uuids[0],
                'code' => 'SAVE20',
                'name' => '20% Off Everything',
                'description' => '<p>Get 20% off on all products. Valid for a limited time only!</p>',
                'type' => 'percentage',
                'value' => 20.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => null,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],

            // 2. Active Percentage - Current (With minimum amount)
            [
                'uuid' => $uuids[1],
                'code' => 'SAVE15MIN50',
                'name' => '15% Off Orders Over $50',
                'description' => '<p>Save 15% on orders over $50. Perfect for larger purchases!</p>',
                'type' => 'percentage',
                'value' => 15.00,
                'minimum_amount' => 50.00,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => null,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],

            // 3. Active Percentage - Current (With maximum discount)
            [
                'uuid' => $uuids[2],
                'code' => 'SAVE25MAX100',
                'name' => '25% Off (Max $100)',
                'description' => '<p>Get 25% off with maximum discount of $100. Great for high-value items!</p>',
                'type' => 'percentage',
                'value' => 25.00,
                'minimum_amount' => null,
                'maximum_discount' => 100.00,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => null,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],

            // 4. Active Percentage - Current (With usage limit)
            [
                'uuid' => $uuids[3],
                'code' => 'FIRST100',
                'name' => '30% Off First 100 Customers',
                'description' => '<p>Limited to first 100 customers only. Use it before it\'s gone!</p>',
                'type' => 'percentage',
                'value' => 30.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => 100,
                'usage_count' => 15,
                'usage_limit_per_user' => null,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],

            // 5. Active Percentage - Current (With usage_limit_per_user)
            [
                'uuid' => $uuids[4],
                'code' => 'WELCOME10',
                'name' => 'Welcome 10% Off',
                'description' => '<p>New customers get 10% off. One use per customer.</p>',
                'type' => 'percentage',
                'value' => 10.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => 1,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],

            // 6. Active Percentage - Current (All limits combined)
            [
                'uuid' => $uuids[5],
                'code' => 'MEGA50',
                'name' => 'Mega Sale 50% Off',
                'description' => '<p>Mega sale! 50% off with all conditions. Minimum $100 order, max $200 discount, limited to 50 uses, 1 per customer.</p>',
                'type' => 'percentage',
                'value' => 50.00,
                'minimum_amount' => 100.00,
                'maximum_discount' => 200.00,
                'usage_limit' => 50,
                'usage_count' => 8,
                'usage_limit_per_user' => 1,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextWeek->format('Y-m-d'),
                'status' => 1,
            ],

            // 7. Active Percentage - Future (Not started yet)
            [
                'uuid' => $uuids[6],
                'code' => 'BLACKFRIDAY',
                'name' => 'Black Friday 40% Off',
                'description' => '<p>Black Friday special! 40% off starting tomorrow. Don\'t miss out!</p>',
                'type' => 'percentage',
                'value' => 40.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => null,
                'start_date' => $tomorrow->format('Y-m-d'),
                'end_date' => $nextWeek->format('Y-m-d'),
                'status' => 1,
            ],

            // 8. Inactive Percentage
            [
                'uuid' => $uuids[7],
                'code' => 'INACTIVE20',
                'name' => 'Inactive 20% Off',
                'description' => '<p>This coupon is currently inactive.</p>',
                'type' => 'percentage',
                'value' => 20.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => null,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 0,
            ],

            // 9. Expired Percentage
            [
                'uuid' => $uuids[8],
                'code' => 'EXPIRED15',
                'name' => 'Expired 15% Off',
                'description' => '<p>This coupon has expired.</p>',
                'type' => 'percentage',
                'value' => 15.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => null,
                'start_date' => $lastMonth->format('Y-m-d'),
                'end_date' => $lastWeek->format('Y-m-d'),
                'status' => 1,
            ],

            // 10. Percentage - Usage limit reached
            [
                'uuid' => $uuids[9],
                'code' => 'SOLD50',
                'name' => '50 Uses Only - Sold Out',
                'description' => '<p>This coupon has reached its usage limit.</p>',
                'type' => 'percentage',
                'value' => 25.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => 50,
                'usage_count' => 50,
                'usage_limit_per_user' => null,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],

            // ========== FIXED AMOUNT DISCOUNT COUPONS ==========

            // 11. Active Fixed - Current (No limits)
            [
                'uuid' => $uuids[10],
                'code' => 'SAVE10',
                'name' => '$10 Off',
                'description' => '<p>Get $10 off on your order. Simple and straightforward!</p>',
                'type' => 'fixed',
                'value' => 10.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => null,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],

            // 12. Active Fixed - Current (With minimum amount)
            [
                'uuid' => $uuids[11],
                'code' => 'SAVE25MIN100',
                'name' => '$25 Off Orders Over $100',
                'description' => '<p>Save $25 on orders over $100. Perfect for larger purchases!</p>',
                'type' => 'fixed',
                'value' => 25.00,
                'minimum_amount' => 100.00,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => null,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],

            // 13. Active Fixed - Current (With usage limit)
            [
                'uuid' => $uuids[12],
                'code' => 'FIRST50',
                'name' => '$50 Off First 50 Orders',
                'description' => '<p>Limited to first 50 orders only. Act fast!</p>',
                'type' => 'fixed',
                'value' => 50.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => 50,
                'usage_count' => 12,
                'usage_limit_per_user' => null,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],

            // 14. Active Fixed - Current (With usage_limit_per_user)
            [
                'uuid' => $uuids[13],
                'code' => 'NEWUSER15',
                'name' => '$15 Off New Users',
                'description' => '<p>New users get $15 off. One use per customer.</p>',
                'type' => 'fixed',
                'value' => 15.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => 1,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],

            // 15. Active Fixed - Current (All limits combined)
            [
                'uuid' => $uuids[14],
                'code' => 'MEGA100',
                'name' => 'Mega $100 Off',
                'description' => '<p>Mega deal! $100 off with all conditions. Minimum $200 order, limited to 25 uses, 1 per customer.</p>',
                'type' => 'fixed',
                'value' => 100.00,
                'minimum_amount' => 200.00,
                'maximum_discount' => null,
                'usage_limit' => 25,
                'usage_count' => 5,
                'usage_limit_per_user' => 1,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextWeek->format('Y-m-d'),
                'status' => 1,
            ],

            // 16. Active Fixed - Future (Not started yet)
            [
                'uuid' => $uuids[15],
                'code' => 'CYBERMONDAY',
                'name' => 'Cyber Monday $30 Off',
                'description' => '<p>Cyber Monday special! $30 off starting next week.</p>',
                'type' => 'fixed',
                'value' => 30.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => null,
                'start_date' => $nextWeek->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],

            // 17. Inactive Fixed
            [
                'uuid' => $uuids[16],
                'code' => 'INACTIVE50',
                'name' => 'Inactive $50 Off',
                'description' => '<p>This coupon is currently inactive.</p>',
                'type' => 'fixed',
                'value' => 50.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => null,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 0,
            ],

            // 18. Expired Fixed
            [
                'uuid' => $uuids[17],
                'code' => 'EXPIRED20',
                'name' => 'Expired $20 Off',
                'description' => '<p>This coupon has expired.</p>',
                'type' => 'fixed',
                'value' => 20.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => null,
                'start_date' => $lastMonth->format('Y-m-d'),
                'end_date' => $lastWeek->format('Y-m-d'),
                'status' => 1,
            ],

            // 19. Fixed - Usage limit reached
            [
                'uuid' => $uuids[18],
                'code' => 'SOLD100',
                'name' => '100 Uses Only - Sold Out',
                'description' => '<p>This coupon has reached its usage limit.</p>',
                'type' => 'fixed',
                'value' => 20.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => 100,
                'usage_count' => 100,
                'usage_limit_per_user' => null,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],

            // ========== EDGE CASES ==========

            // 20. Small discount amount
            [
                'uuid' => $uuids[19],
                'code' => 'TINY5',
                'name' => 'Tiny $5 Off',
                'description' => '<p>Small discount for small orders.</p>',
                'type' => 'fixed',
                'value' => 5.00,
                'minimum_amount' => 10.00,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => null,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],

            // 21. Large discount amount
            [
                'uuid' => $uuids[20],
                'code' => 'BIG500',
                'name' => 'Big $500 Off',
                'description' => '<p>Large discount for premium customers.</p>',
                'type' => 'fixed',
                'value' => 500.00,
                'minimum_amount' => 1000.00,
                'maximum_discount' => null,
                'usage_limit' => 10,
                'usage_count' => 2,
                'usage_limit_per_user' => 1,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],

            // 22. High percentage
            [
                'uuid' => $uuids[21],
                'code' => 'MEGA75',
                'name' => 'Mega 75% Off',
                'description' => '<p>Huge discount! 75% off with maximum discount cap.</p>',
                'type' => 'percentage',
                'value' => 75.00,
                'minimum_amount' => 50.00,
                'maximum_discount' => 500.00,
                'usage_limit' => 20,
                'usage_count' => 3,
                'usage_limit_per_user' => 1,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextWeek->format('Y-m-d'),
                'status' => 1,
            ],

            // 23. Single day coupon
            [
                'uuid' => $uuids[22],
                'code' => 'TODAYONLY',
                'name' => 'Today Only 10%',
                'description' => '<p>Valid only today! Don\'t miss out.</p>',
                'type' => 'percentage',
                'value' => 10.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => null,
                'start_date' => $now->format('Y-m-d'),
                'end_date' => $now->format('Y-m-d'),
                'status' => 1,
            ],

            // 24. Long duration coupon
            [
                'uuid' => $uuids[23],
                'code' => 'YEARLONG',
                'name' => 'Year Long 5% Off',
                'description' => '<p>Valid for the entire year. Use anytime!</p>',
                'type' => 'percentage',
                'value' => 5.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => null,
                'start_date' => $now->format('Y-m-d'),
                'end_date' => $now->copy()->addYear()->format('Y-m-d'),
                'status' => 1,
            ],

            // 25. Multiple uses per user
            [
                'uuid' => $uuids[24],
                'code' => 'MULTIUSE',
                'name' => 'Multi-Use 15% Off',
                'description' => '<p>Use up to 5 times per customer.</p>',
                'type' => 'percentage',
                'value' => 15.00,
                'minimum_amount' => null,
                'maximum_discount' => null,
                'usage_limit' => null,
                'usage_count' => 0,
                'usage_limit_per_user' => 5,
                'start_date' => $yesterday->format('Y-m-d'),
                'end_date' => $nextMonth->format('Y-m-d'),
                'status' => 1,
            ],
        ];

        $created = 0;
        $updated = 0;

        foreach ($coupons as $couponData) {
            // Ensure UUID exists, generate if missing
            if (!isset($couponData['uuid']) || empty($couponData['uuid'])) {
                $couponData['uuid'] = Str::uuid()->toString();
            }

            // Use updateOrCreate with uuid as unique identifier
            $coupon = Coupon::updateOrCreate(
                ['uuid' => $couponData['uuid']],
                $couponData
            );

            if ($coupon->wasRecentlyCreated) {
                $created++;
            } else {
                $updated++;
            }
        }

        $this->command->info("✅ Created: {$created} coupons");
        $this->command->info("🔄 Updated: {$updated} coupons");
        $this->command->info("📊 Total: " . count($coupons) . " coupons seeded");
        $this->command->newLine();
        $this->command->info('📌 Coupon Types Summary:');
        $this->command->line('   • Percentage Discounts: ' . count(array_filter($coupons, fn($c) => $c['type'] === 'percentage')));
        $this->command->line('   • Fixed Amount Discounts: ' . count(array_filter($coupons, fn($c) => $c['type'] === 'fixed')));
        $this->command->line('   • Active Coupons: ' . count(array_filter($coupons, fn($c) => $c['status'] === 1)));
        $this->command->line('   • Inactive Coupons: ' . count(array_filter($coupons, fn($c) => $c['status'] === 0)));
    }
}
