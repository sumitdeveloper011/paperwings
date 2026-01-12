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
                'image' => null,
            ]
        );

        // Define multiple products to create
        $productsData = [
            [
                'slug' => 'premium-notebook-set-3-pack',
                'name' => 'Premium Notebook Set - 3 Pack',
                'product_type' => 1, // Featured
                'total_price' => 24.99,
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'discount_price' => 19.99,
                'stock' => 100,
                'description' => '<p>High-quality premium notebook set featuring 3 beautifully designed notebooks. Each notebook contains 120 pages of premium quality paper, perfect for writing, sketching, or note-taking.</p><p><strong>Features:</strong></p><ul><li>120 pages per notebook</li><li>Premium quality paper</li><li>Hardcover design</li><li>Lined pages</li><li>Perfect for office or personal use</li></ul>',
                'short_description' => 'Premium notebook set with 3 beautifully designed notebooks, each containing 120 pages of high-quality paper.',
            ],
            [
                'slug' => 'professional-pen-set-12-pack',
                'name' => 'Professional Pen Set - 12 Pack',
                'product_type' => 1, // Featured
                'total_price' => 15.99,
                'discount_type' => 'percentage',
                'discount_value' => 10,
                'discount_price' => 14.39,
                'stock' => 150,
                'description' => '<p>Premium quality professional pens in a convenient 12-pack. Perfect for office use, meetings, and daily writing tasks.</p><ul><li>12 premium pens</li><li>Smooth writing experience</li><li>Long-lasting ink</li><li>Professional design</li></ul>',
                'short_description' => 'Premium quality professional pens in a convenient 12-pack for office use.',
            ],
            [
                'slug' => 'executive-desk-organizer',
                'name' => 'Executive Desk Organizer',
                'product_type' => 2, // On Sale
                'total_price' => 45.99,
                'discount_type' => 'direct',
                'discount_value' => null,
                'discount_price' => 39.99,
                'stock' => 75,
                'description' => '<p>Elegant desk organizer to keep your workspace tidy and professional. Made from high-quality materials with multiple compartments.</p><ul><li>Multiple compartments</li><li>Durable construction</li><li>Modern design</li><li>Perfect for any office</li></ul>',
                'short_description' => 'Elegant desk organizer to keep your workspace tidy and professional.',
            ],
            [
                'slug' => 'premium-stapler-set',
                'name' => 'Premium Stapler Set',
                'product_type' => 2, // On Sale
                'total_price' => 18.99,
                'discount_type' => 'percentage',
                'discount_value' => 15,
                'discount_price' => 16.14,
                'stock' => 120,
                'description' => '<p>Heavy-duty stapler set with staple remover. Perfect for office and home use. Includes 1000 staples.</p><ul><li>Heavy-duty construction</li><li>Includes staple remover</li><li>1000 staples included</li><li>Ergonomic design</li></ul>',
                'short_description' => 'Heavy-duty stapler set with staple remover and 1000 staples included.',
            ],
            [
                'slug' => 'highlighter-set-6-colors',
                'name' => 'Highlighter Set - 6 Colors',
                'product_type' => 3, // Top Rated
                'total_price' => 12.99,
                'discount_type' => 'none',
                'discount_value' => null,
                'discount_price' => null,
                'stock' => 200,
                'description' => '<p>Bright and vibrant highlighters in 6 different colors. Perfect for studying, note-taking, and organizing information.</p><ul><li>6 vibrant colors</li><li>Non-toxic ink</li><li>Chisel tip design</li><li>Long-lasting</li></ul>',
                'short_description' => 'Bright and vibrant highlighters in 6 different colors for studying and note-taking.',
            ],
            [
                'slug' => 'document-folder-set-10-pack',
                'name' => 'Document Folder Set - 10 Pack',
                'product_type' => 3, // Top Rated
                'total_price' => 22.99,
                'discount_type' => 'percentage',
                'discount_value' => 12,
                'discount_price' => 20.23,
                'stock' => 90,
                'description' => '<p>Durable document folders in a convenient 10-pack. Perfect for organizing documents, reports, and important papers.</p><ul><li>10 folders included</li><li>Durable construction</li><li>Tabbed design</li><li>Multiple colors</li></ul>',
                'short_description' => 'Durable document folders in a convenient 10-pack for organizing documents.',
            ],
            [
                'slug' => 'magnetic-whiteboard-24x36',
                'name' => 'Magnetic Whiteboard 24x36',
                'product_type' => 1, // Featured
                'total_price' => 89.99,
                'discount_type' => 'percentage',
                'discount_value' => 20,
                'discount_price' => 71.99,
                'stock' => 50,
                'description' => '<p>Large magnetic whiteboard perfect for offices, classrooms, and home offices. Includes mounting hardware and markers.</p><ul><li>24x36 inches</li><li>Magnetic surface</li><li>Includes markers</li><li>Easy to clean</li></ul>',
                'short_description' => 'Large magnetic whiteboard perfect for offices, classrooms, and home offices.',
            ],
            [
                'slug' => 'paper-clips-assorted-500-pack',
                'name' => 'Paper Clips Assorted - 500 Pack',
                'product_type' => 2, // On Sale
                'total_price' => 8.99,
                'discount_type' => 'direct',
                'discount_value' => null,
                'discount_price' => 7.99,
                'stock' => 300,
                'description' => '<p>Assorted paper clips in various sizes. Essential office supply for organizing documents and papers.</p><ul><li>500 clips</li><li>Multiple sizes</li><li>Rust-resistant</li><li>Great value</li></ul>',
                'short_description' => 'Assorted paper clips in various sizes - 500 pack for organizing documents.',
            ],
        ];

        $createdProducts = [];

        foreach ($productsData as $productData) {
            $product = Product::updateOrCreate(
                ['slug' => $productData['slug']],
                [
                    'uuid' => Str::uuid(),
                    'category_id' => $category->id,
                    'brand_id' => null,
                    'eposnow_product_id' => null,
                    'eposnow_category_id' => null,
                    'eposnow_brand_id' => null,
                    'barcode' => 'PW-' . strtoupper(Str::random(8)),
                    'stock' => $productData['stock'],
                    'product_type' => $productData['product_type'],
                    'name' => $productData['name'],
                    'total_price' => $productData['total_price'],
                    'discount_type' => $productData['discount_type'],
                    'discount_value' => $productData['discount_value'],
                    'discount_price' => $productData['discount_price'],
                    'description' => $productData['description'],
                    'short_description' => $productData['short_description'],
                    'meta_title' => $productData['name'] . ' | Paper Wings',
                    'meta_description' => $productData['short_description'],
                    'meta_keywords' => strtolower(str_replace(['-', ' - '], ', ', $productData['slug'])),
                    'status' => 1,
                ]
            );

            $createdProducts[] = $product;

            // Delete existing images and accordions to avoid duplicates
            ProductImage::where('product_id', $product->id)->delete();
            ProductAccordion::where('product_id', $product->id)->delete();

            // Create Product Images (using proper UUID folder structure)
            $productUuid = $product->uuid;
            $imagePaths = [
                'products/' . $productUuid . '/original/image-1.jpg',
                'products/' . $productUuid . '/original/image-2.jpg',
                'products/' . $productUuid . '/original/image-3.jpg',
            ];

            foreach ($imagePaths as $index => $imagePath) {
                ProductImage::updateOrCreate(
                    [
                        'product_id' => $product->id,
                        'image' => $imagePath,
                    ],
                    [
                        'eposnow_product_id' => null,
                    ]
                );
            }

            // Create Product Accordions
            $accordions = [
                [
                    'heading' => 'Product Details',
                    'content' => '<p>' . $productData['short_description'] . '</p>',
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
        }

        $this->command->info('✅ Category and Products created/updated successfully!');
        $this->command->info('Category: ' . $category->name . ' (ID: ' . $category->id . ')');
        $this->command->info('Products created: ' . count($createdProducts));
        foreach ($createdProducts as $product) {
            $this->command->info('  • ' . $product->name . ' (ID: ' . $product->id . ', Type: ' . $product->product_type . ')');
        }
    }
}
