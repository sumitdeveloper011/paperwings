<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductBundle;

class ProductBundleSeeder extends Seeder
{
    public function run(): void
    {
        $products = Product::active()->get();

        if ($products->count() < 6) {
            $this->command->warn('Need at least 6 products to create bundles. Please create more products first.');
            return;
        }

        $bundles = [
            [
                'name' => 'Complete Writing Set',
                'description' => 'Everything you need for writing - pens, pencils, erasers, and more!',
                'bundle_price' => 29.99,
                'discount_percentage' => 15,
            ],
            [
                'name' => 'School Starter Pack',
                'description' => 'Perfect bundle for students starting a new school year.',
                'bundle_price' => 49.99,
                'discount_percentage' => 20,
            ],
            [
                'name' => 'Office Essentials Bundle',
                'description' => 'All the essential office supplies in one convenient bundle.',
                'bundle_price' => 79.99,
                'discount_percentage' => 10,
            ],
        ];

        foreach ($bundles as $bundleData) {
            $bundle = ProductBundle::create([
                'name' => $bundleData['name'],
                'slug' => \Illuminate\Support\Str::slug($bundleData['name']),
                'description' => $bundleData['description'],
                'bundle_price' => $bundleData['bundle_price'],
                'discount_percentage' => $bundleData['discount_percentage'],
                'status' => true,
                'sort_order' => 0,
            ]);

            // Attach 3-4 random products to each bundle
            $selectedProducts = $products->random(rand(3, 4));
            foreach ($selectedProducts as $product) {
                $bundle->products()->attach($product->id, [
                    'quantity' => rand(1, 3)
                ]);
            }
        }

        $this->command->info('Product Bundles seeded successfully!');
    }
}
