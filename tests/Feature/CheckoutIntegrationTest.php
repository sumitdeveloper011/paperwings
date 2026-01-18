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

class CheckoutIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Region::factory()->create([
            'id' => 1,
            'name' => 'Auckland',
            'status' => 1
        ]);
    }

    // Test: Complete checkout flow - add to cart, checkout details, review, payment
    public function test_complete_checkout_flow(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'status' => 1,
            'total_price' => 100.00,
            'stock' => 10
        ]);

        $this->actingAs($user);

        // Step 1: Add product to cart
        $addResponse = $this->postJson(route('cart.add'), [
            'product_uuid' => $product->uuid,
            'quantity' => 1
        ]);

        $addResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        // Step 2: Access checkout (index redirects to details)
        $checkoutResponse = $this->get(route('checkout.details'));
        $checkoutResponse->assertStatus(200);

        // Step 3: Store checkout details
        $detailsResponse = $this->postJson(route('checkout.store-details'), [
            'shipping_first_name' => 'John',
            'shipping_last_name' => 'Doe',
            'shipping_email' => 'john@example.com',
            'shipping_phone' => '0211234567',
            'shipping_street_address' => '123 Test St',
            'shipping_city' => 'Auckland',
            'shipping_suburb' => 'CBD',
            'shipping_region_id' => 1,
            'shipping_zip_code' => '1010',
            'shipping_country' => 'New Zealand',
            'billing_different' => false
        ]);

        // storeDetails redirects to checkout.review for regular requests
        $detailsResponse->assertRedirect(route('checkout.review'));

        // Step 4: Access review page
        $reviewResponse = $this->get(route('checkout.review'));
        $reviewResponse->assertStatus(200);

        // Step 5: Confirm review (redirects to payment)
        $confirmResponse = $this->post(route('checkout.confirm-review'));
        $confirmResponse->assertRedirect(route('checkout.payment'));
    }

    // Test: Checkout flow with coupon
    public function test_checkout_flow_with_coupon(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'status' => 1,
            'total_price' => 100.00,
            'stock' => 10
        ]);
        $coupon = Coupon::factory()->create([
            'code' => 'TEST10',
            'type' => 'percentage',
            'value' => 10,
            'status' => 1,
            'start_date' => now()->subDay(),
            'end_date' => now()->addDay()
        ]);

        $this->actingAs($user);

        // Add to cart
        $this->postJson(route('cart.add'), [
            'product_uuid' => $product->uuid,
            'quantity' => 1
        ]);

        // Store checkout details (redirects to review)
        $this->post(route('checkout.store-details'), [
            'shipping_first_name' => 'John',
            'shipping_last_name' => 'Doe',
            'shipping_email' => 'john@example.com',
            'shipping_phone' => '0211234567',
            'shipping_street_address' => '123 Test St',
            'shipping_city' => 'Auckland',
            'shipping_suburb' => 'CBD',
            'shipping_region_id' => 1,
            'shipping_zip_code' => '1010',
            'shipping_country' => 'New Zealand',
            'billing_different' => false
        ])->assertRedirect(route('checkout.review'));

        // Apply coupon
        $couponResponse = $this->postJson(route('checkout.apply-coupon'), [
            'code' => 'TEST10'
        ]);

        $couponResponse->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'discount',
                    'total'
                ]
            ]);
    }

    // Test: Checkout flow with shipping calculation
    public function test_checkout_flow_with_shipping_calculation(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'status' => 1,
            'total_price' => 100.00,
            'stock' => 10
        ]);

        $this->actingAs($user);

        // Add to cart
        $this->postJson(route('cart.add'), [
            'product_uuid' => $product->uuid,
            'quantity' => 1
        ]);

        // Calculate shipping
        $shippingResponse = $this->postJson(route('checkout.calculate-shipping'), [
            'region_id' => 1,
            'subtotal' => 100.00,
            'discount' => 0
        ]);

        $shippingResponse->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'shipping',
                    'shipping_price'
                ]
            ]);
    }

    // Test: Checkout flow validation errors
    public function test_checkout_flow_validation_errors(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Try to store checkout details without required fields
        $response = $this->postJson(route('checkout.store-details'), []);

        $response->assertStatus(422)
            ->assertJson(['success' => false])
            ->assertJsonStructure([
                'success',
                'message',
                'errors'
            ]);
    }

    // Test: Checkout flow with empty cart
    public function test_checkout_flow_with_empty_cart(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('checkout.index'));

        // checkout.index redirects to checkout.details, which redirects to cart.index if empty
        $response->assertRedirect(route('checkout.details'));
    }
}
