<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Region;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a region for shipping
        Region::factory()->create([
            'id' => 1,
            'name' => 'Auckland',
            'status' => 1
        ]);
    }

    // Test: User can access checkout page
    public function test_user_can_access_checkout_page()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 50.00,
            'subtotal' => 50.00
        ]);

        $this->actingAs($user);

        $response = $this->get(route('checkout.details'));

        $response->assertStatus(200)
            ->assertViewIs('frontend.checkout.details');
    }

    // Test: Cannot access checkout with empty cart
    public function test_cannot_access_checkout_with_empty_cart()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // checkout.index redirects to checkout.details, which redirects to cart.index if empty
        $response = $this->get(route('checkout.details'));

        $response->assertRedirect(route('cart.index'))
            ->assertSessionHas('error');
    }

    // Test: User can apply coupon code
    public function test_user_can_apply_coupon_code()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $coupon = Coupon::factory()->create([
            'code' => 'TEST10',
            'type' => 'percentage',
            'value' => 10,
            'status' => 1,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay()
        ]);
        
        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100.00,
            'subtotal' => 100.00
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('checkout.apply-coupon'), [
            'code' => 'TEST10'
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'discount',
                    'total'
                ]
            ]);
    }

    // Test: Cannot apply invalid coupon code
    public function test_cannot_apply_invalid_coupon_code()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        
        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100.00,
            'subtotal' => 100.00
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('checkout.apply-coupon'), [
            'code' => 'INVALID'
        ]);

        $response->assertStatus(404)
            ->assertJson([
                'success' => false
            ]);
    }

    // Test: User can remove coupon
    public function test_user_can_remove_coupon()
    {
        $user = User::factory()->create();
        
        Session::put('applied_coupon', [
            'id' => 1,
            'code' => 'TEST10',
            'discount' => 10.00
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('checkout.remove-coupon'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true
            ]);
    }

    // Test: User can calculate shipping
    public function test_user_can_calculate_shipping()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('checkout.calculate-shipping'), [
            'region_id' => 1,
            'subtotal' => 100.00,
            'discount' => 0
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'shipping',
                    'shipping_price'
                ]
            ]);
    }

    // Test: Cannot access checkout without authentication
    public function test_cannot_access_checkout_without_authentication()
    {
        $response = $this->get(route('checkout.index'));

        $response->assertRedirect(route('login'));
    }

    // Test: Order success page shows correct order
    public function test_order_success_page_shows_correct_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-123456',
            'payment_status' => 'paid'
        ]);

        $this->actingAs($user);

        $response = $this->get(route('checkout.success', ['order' => 'ORD-123456']));

        $response->assertStatus(200)
            ->assertViewIs('frontend.checkout.success')
            ->assertViewHas('order', $order);
    }

    // Test: Cannot view order success if payment not completed
    public function test_cannot_view_order_success_if_payment_not_completed()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'order_number' => 'ORD-123456',
            'payment_status' => 'pending'
        ]);

        $this->actingAs($user);

        $response = $this->get(route('checkout.success', ['order' => 'ORD-123456']));

        $response->assertRedirect(route('checkout.index'))
            ->assertSessionHas('error');
    }
}

