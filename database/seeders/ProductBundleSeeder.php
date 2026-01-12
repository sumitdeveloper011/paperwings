<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;

class ProductBundleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * This seeder creates sample product bundles.
     * Will NOT run in production environment for safety.
     */
    public function run(): void
    {
        // Prevent running in production
        if (app()->environment('production')) {
            $this->command->warn('⚠️  ProductBundleSeeder skipped: Cannot run in production environment!');
            return;
        }

        $this->command->info('🌱 Seeding product bundles...');

        $products = Product::active()->get();

        if ($products->count() < 6) {
            $this->command->warn('Need at least 6 products to create bundles. Please create more products first.');
            return;
        }

        // Get or create Bundles category
        $bundlesCategory = Category::firstOrCreate(
            ['slug' => 'bundles'],
            [
                'uuid' => Str::uuid(),
                'name' => 'Bundles',
                'description' => 'Product bundles and special offers',
                'meta_title' => 'Bundles - Paper Wings',
                'meta_description' => 'Browse our special product bundles and get great deals on combined items.',
                'meta_keywords' => 'bundles, product bundles, special offers, deals',
                'status' => 1,
            ]
        );

        $bundles = [
            [
                'name' => 'Complete Writing Set',
                'description' => '<p>Everything you need for writing - pens, pencils, erasers, and more! This comprehensive bundle includes all essential writing tools for students and professionals.</p><ul><li>Premium quality pens</li><li>Mechanical pencils</li><li>Erasers and sharpeners</li><li>Rulers and highlighters</li></ul>',
                'short_description' => 'Everything you need for writing - pens, pencils, erasers, and more!',
                'total_price' => 29.99,
                'discount_type' => 'percentage',
                'discount_value' => 15,
            ],
            [
                'name' => 'School Starter Pack',
                'description' => '<p>Perfect bundle for students starting a new school year. Includes all essential supplies to get them ready for academic success.</p><ul><li>Notebooks and binders</li><li>Pens and pencils</li><li>Folders and dividers</li><li>Calculator and ruler</li></ul>',
                'short_description' => 'Perfect bundle for students starting a new school year.',
                'total_price' => 49.99,
                'discount_type' => 'percentage',
                'discount_value' => 20,
            ],
            [
                'name' => 'Office Essentials Bundle',
                'description' => '<p>All the essential office supplies in one convenient bundle. Perfect for setting up a new office or restocking your workspace.</p><ul><li>Desk organizers</li><li>Writing supplies</li><li>File folders</li><li>Stapler and paper clips</li></ul>',
                'short_description' => 'All the essential office supplies in one convenient bundle.',
                'total_price' => 79.99,
                'discount_type' => 'percentage',
                'discount_value' => 10,
            ],
            [
                'name' => 'Creative Artist Bundle',
                'description' => '<p>Perfect for artists and creative professionals. Includes high-quality art supplies for drawing, sketching, and coloring.</p><ul><li>Sketchbooks</li><li>Drawing pencils set</li><li>Colored pencils</li><li>Erasers and sharpeners</li></ul>',
                'short_description' => 'Perfect for artists and creative professionals with high-quality art supplies.',
                'total_price' => 59.99,
                'discount_type' => 'direct',
                'discount_value' => null,
                'discount_price' => 49.99,
            ],
        ];

        foreach ($bundles as $bundleData) {
            // Check if bundle with same name already exists
            $existingBundle = Product::bundles()->where('name', $bundleData['name'])->first();
            if ($existingBundle) {
                $this->command->info("Bundle '{$bundleData['name']}' already exists. Skipping...");
                continue;
            }

            // Calculate discount_price from percentage or use direct price
            $discountPrice = null;
            if ($bundleData['discount_type'] === 'percentage' && isset($bundleData['discount_value']) && $bundleData['discount_value']) {
                $discountAmount = $bundleData['total_price'] * ($bundleData['discount_value'] / 100);
                $discountPrice = round($bundleData['total_price'] - $discountAmount, 2);
            } elseif ($bundleData['discount_type'] === 'direct' && isset($bundleData['discount_price'])) {
                $discountPrice = $bundleData['discount_price'];
            }

            // Create bundle as Product with product_type = 4
            $bundle = Product::create([
                'category_id' => $bundlesCategory->id,
                'name' => $bundleData['name'],
                'description' => $bundleData['description'],
                'short_description' => $bundleData['short_description'] ?? substr(strip_tags($bundleData['description']), 0, 200),
                'total_price' => $bundleData['total_price'],
                'discount_type' => $bundleData['discount_type'],
                'discount_value' => $bundleData['discount_value'] ?? null,
                'discount_price' => $discountPrice,
                'product_type' => 4, // Bundle type
                'status' => 1,
                'sort_order' => 0,
                'barcode' => 'PW-BUNDLE-' . strtoupper(Str::random(6)),
                'stock' => null, // Bundles don't have stock
            ]);

            // Attach 3-4 random products to each bundle using sync to prevent duplicates
            $selectedProducts = $products->random(rand(3, 4));
            $productsData = [];
            foreach ($selectedProducts as $product) {
                $productsData[$product->id] = ['quantity' => rand(1, 3)];
            }
            $bundle->bundleProducts()->sync($productsData);
        }

        $this->command->info("✅ Product Bundles seeded successfully!");
        $this->command->info("  • Processed: " . count($bundles) . " bundles");
    }
}
