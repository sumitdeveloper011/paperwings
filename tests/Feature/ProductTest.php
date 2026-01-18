<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    // Test: Can view product detail page
    public function test_can_view_product_detail_page()
    {
        $product = Product::factory()->create([
            'status' => 1,
            'slug' => 'test-product'
        ]);

        $response = $this->get(route('product.detail', ['slug' => 'test-product']));

        $response->assertStatus(200)
            ->assertViewIs('frontend.product.product-detail')
            ->assertViewHas('product', $product);
    }

    // Test: Cannot view inactive product
    public function test_cannot_view_inactive_product()
    {
        $product = Product::factory()->create([
            'status' => 0,
            'slug' => 'inactive-product'
        ]);

        $response = $this->get(route('product.detail', ['slug' => 'inactive-product']));

        $response->assertStatus(404);
    }

    // Test: Can view products by category
    public function test_can_view_products_by_category()
    {
        $category = Category::factory()->create([
            'slug' => 'test-category',
            'status' => 1
        ]);

        $products = Product::factory()->count(5)->create([
            'category_id' => $category->id,
            'status' => 1
        ]);

        $response = $this->get(route('category.show', ['slug' => 'test-category']));

        $response->assertStatus(200)
            ->assertViewIs('frontend.category.category');
    }

    // Test: Can view shop page
    public function test_can_view_shop_page()
    {
        Product::factory()->count(10)->create(['status' => 1]);

        $response = $this->get(route('shop'));

        $response->assertStatus(200)
            ->assertViewIs('frontend.shop.shop');
    }

    // Test: Shop page filters work
    public function test_shop_page_filters_work()
    {
        Product::factory()->count(5)->create([
            'status' => 1,
            'total_price' => 50.00
        ]);

        $response = $this->get(route('shop', [
            'min_price' => 40,
            'max_price' => 60
        ]));

        $response->assertStatus(200);
    }
}

