<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WishlistTest extends TestCase
{
    use RefreshDatabase;

    // Test: User can add product to wishlist
    public function test_user_can_add_product_to_wishlist()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'status' => 1
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('wishlist.add'), [
            'product_uuid' => $product->uuid
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }

    // Test: User can remove product from wishlist
    public function test_user_can_remove_product_from_wishlist()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('wishlist.remove'), [
            'product_uuid' => $product->uuid
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseMissing('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }

    // Test: Cannot add same product twice to wishlist
    public function test_cannot_add_duplicate_product_to_wishlist()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'status' => 1
        ]);

        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('wishlist.add'), [
            'product_uuid' => $product->uuid
        ]);

        // Should return error for duplicate
        $response->assertStatus(400)
            ->assertJson([
                'success' => false
            ]);
    }

    // Test: User can view wishlist
    public function test_user_can_view_wishlist()
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(3)->create();

        foreach ($products as $product) {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id
            ]);
        }

        $this->actingAs($user);

        $response = $this->getJson(route('wishlist.list'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'items',
                    'count'
                ]
            ]);
    }

    // Test: Wishlist count is correct
    public function test_wishlist_count_is_correct()
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(5)->create();

        foreach ($products as $product) {
            Wishlist::create([
                'user_id' => $user->id,
                'product_id' => $product->id
            ]);
        }

        $this->actingAs($user);

        $response = $this->getJson(route('wishlist.count'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'count' => 5
                ]
            ]);
    }

    // Test: Cannot add to wishlist without authentication
    public function test_cannot_add_to_wishlist_without_authentication()
    {
        $product = Product::factory()->create();

        $response = $this->postJson(route('wishlist.add'), [
            'product_uuid' => $product->uuid
        ]);

        $response->assertStatus(401); // Unauthorized or redirect
    }

    // Test: Can check wishlist status for multiple products
    public function test_can_check_wishlist_status_for_products()
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(3)->create();

        // Add first product to wishlist
        Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $products[0]->id
        ]);

        $this->actingAs($user);

        $productUuids = $products->pluck('uuid')->toArray();

        $response = $this->postJson(route('wishlist.check'), [
            'product_uuids' => $productUuids
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'status'
                ]
            ]);
    }
}

