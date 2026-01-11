<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductAccordion;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create or Update Category
        $category = Category::updateOrCreate(
            ['slug' => 'office-supplies'],
            [
                'uuid' => Str::uuid(),
                'name' => 'Office Supplies',
                'description' => 'Essential office supplies for your workspace. From stationery to organizational tools, find everything you need to keep your office running smoothly.',
                'meta_title' => 'Office Supplies - Paper Wings',
                'meta_description' => 'Browse our wide range of office supplies including pens, notebooks, folders, and more. Quality products at competitive prices.',
                'meta_keywords' => 'office supplies, stationery, office equipment, workplace essentials',
                'status' => 1,
                'image' => null, // You can add image path if needed
            ]
        );

        // Create or Update Product
        $product = Product::updateOrCreate(
            ['slug' => 'premium-notebook-set-3-pack'],
            [
                'uuid' => Str::uuid(),
                'category_id' => $category->id,
                'brand_id' => null,
                'eposnow_product_id' => null,
                'eposnow_category_id' => null,
                'eposnow_brand_id' => null,
                'barcode' => 'PW-' . strtoupper(Str::random(8)),
                'stock' => 100,
                'product_type' => 1, // 1: Featured, 2: On Sale, 3: Top Rated
                'name' => 'Premium Notebook Set - 3 Pack',
                'total_price' => 24.99,
                'discount_price' => 19.99,
                'description' => '<p>High-quality premium notebook set featuring 3 beautifully designed notebooks. Each notebook contains 120 pages of premium quality paper, perfect for writing, sketching, or note-taking.</p><p><strong>Features:</strong></p><ul><li>120 pages per notebook</li><li>Premium quality paper</li><li>Hardcover design</li><li>Lined pages</li><li>Perfect for office or personal use</li></ul>',
                'short_description' => 'Premium notebook set with 3 beautifully designed notebooks, each containing 120 pages of high-quality paper.',
                'meta_title' => 'Premium Notebook Set - 3 Pack | Paper Wings',
                'meta_description' => 'Get our premium notebook set featuring 3 high-quality notebooks with 120 pages each. Perfect for office or personal use.',
                'meta_keywords' => 'notebook, notebook set, office supplies, stationery, writing supplies',
                'status' => 1,
            ]
        );

        // Delete existing images and accordions to avoid duplicates
        ProductImage::where('product_id', $product->id)->delete();
        ProductAccordion::where('product_id', $product->id)->delete();

        // Create Product Images (using placeholder paths - you can update with actual images)
        $imagePaths = [
            'products/' . Str::uuid() . '/image-1.jpg',
            'products/' . Str::uuid() . '/image-2.jpg',
            'products/' . Str::uuid() . '/image-3.jpg',
            'products/' . Str::uuid() . '/image-4.jpg',
            'products/' . Str::uuid() . '/image-5.jpg',
        ];

        foreach ($imagePaths as $index => $imagePath) {
            ProductImage::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'image' => $imagePath,
                ],
                [
                    'uuid' => Str::uuid(),
                    'eposnow_product_id' => null,
                ]
            );
        }

        // Create Product Accordions
        $accordions = [
            [
                'heading' => 'Product Details',
                'content' => '<p>This premium notebook set includes 3 high-quality notebooks, each with 120 pages of premium paper. The hardcover design ensures durability and protection for your notes.</p>',
            ],
            [
                'heading' => 'Specifications',
                'content' => '<ul><li>Pages per notebook: 120</li><li>Paper quality: Premium</li><li>Cover: Hardcover</li><li>Page type: Lined</li><li>Dimensions: A5 size</li></ul>',
            ],
            [
                'heading' => 'Shipping & Returns',
                'content' => '<p>Free shipping on orders over $50. Returns accepted within 30 days of purchase. Items must be in original condition.</p>',
            ],
        ];

        foreach ($accordions as $accordionData) {
            ProductAccordion::updateOrCreate(
                [
                    'product_id' => $product->id,
                    'heading' => $accordionData['heading'],
                ],
                [
                    'uuid' => Str::uuid(),
                    'eposnow_product_id' => null,
                    'content' => $accordionData['content'],
                ]
            );
        }

        $this->command->info('Category and Product created/updated successfully!');
        $this->command->info('Category: ' . $category->name . ' (ID: ' . $category->id . ')');
        $this->command->info('Product: ' . $product->name . ' (ID: ' . $product->id . ')');
        $this->command->info('Product Images: ' . count($imagePaths) . ' created/updated');
        $this->command->info('Product Accordions: ' . count($accordions) . ' created/updated');
    }
}
