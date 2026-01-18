<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    // Test: User can add product to cart
    public function test_user_can_add_product_to_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'status' => 1,
            'total_price' => 50.00
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('cart.add'), [
            'product_uuid' => $product->uuid,
            'quantity' => 2
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);
    }

    // Test: Cannot add inactive product to cart
    public function test_cannot_add_inactive_product_to_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'status' => 0 // Inactive
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response->assertStatus(422); // Validation error
    }

    // Test: User can update cart item quantity
    public function test_user_can_update_cart_item_quantity()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $cartItem = CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 50.00,
            'subtotal' => 50.00
        ]);

        $this->actingAs($user);

        $response = $this->putJson(route('cart.update'), [
            'cart_item_id' => $cartItem->id,
            'quantity' => 3
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseHas('cart_items', [
            'id' => $cartItem->id,
            'quantity' => 3
        ]);
    }

    // Test: User can remove item from cart
    public function test_user_can_remove_item_from_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        $cartItem = CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 50.00,
            'subtotal' => 50.00
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('cart.remove'), [
            'cart_item_id' => $cartItem->id
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);

        $this->assertDatabaseMissing('cart_items', [
            'id' => $cartItem->id
        ]);
    }

    // Test: User can view cart
    public function test_user_can_view_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 50.00,
            'subtotal' => 100.00
        ]);

        $this->actingAs($user);

        $response = $this->getJson(route('cart.api.list'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'items',
                'total',
                'count'
            ]);
    }

    // Test: Cart count is correct
    public function test_cart_count_is_correct()
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(3)->create();
        
        foreach ($products as $product) {
            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'quantity' => 1,
                'price' => 50.00,
                'subtotal' => 50.00
            ]);
        }

        $this->actingAs($user);

        $response = $this->getJson(route('cart.count'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'count' => 3
                ]
            ]);
    }

    // Test: Cannot add product to cart without authentication
    public function test_cannot_add_to_cart_without_authentication()
    {
        $product = Product::factory()->create([
            'status' => 1
        ]);

        // Cart now requires authentication
        $response = $this->postJson(route('cart.add'), [
            'product_uuid' => $product->uuid,
            'quantity' => 1
        ]);

        // Should redirect to login (401 Unauthorized for API)
        $response->assertStatus(401);
    }
}

