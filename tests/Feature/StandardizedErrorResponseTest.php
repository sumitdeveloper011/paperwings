<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StandardizedErrorResponseTest extends TestCase
{
    use RefreshDatabase;

    // Test: JSON error response structure
    public function test_json_error_response_has_standardized_structure(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $response = $this->postJson(route('cart.add'), []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => []
            ])
            ->assertJson([
                'success' => false
            ]);
    }

    // Test: Validation error response structure
    public function test_validation_error_response_has_standardized_structure(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $response = $this->postJson(route('checkout.store-details'), []);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => []
            ])
            ->assertJson([
                'success' => false
            ]);
    }

    // Test: Unauthenticated error response
    public function test_unauthenticated_error_response(): void
    {
        $product = Product::factory()->create(['status' => 1]);
        
        $response = $this->postJson(route('cart.add'), [
            'product_uuid' => $product->uuid,
            'quantity' => 1
        ]);

        $response->assertStatus(401)
            ->assertJsonStructure([
                'success',
                'message',
                'error_code'
            ])
            ->assertJson([
                'success' => false,
                'error_code' => 'UNAUTHENTICATED'
            ]);
    }

    // Test: Not found error response
    public function test_not_found_error_response(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('checkout.apply-coupon'), [
            'code' => 'INVALID_CODE'
        ]);

        $response->assertStatus(404)
            ->assertJsonStructure([
                'success',
                'message',
                'error_code'
            ])
            ->assertJson([
                'success' => false
            ]);
    }

    // Test: Success response structure
    public function test_success_response_has_standardized_structure(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'status' => 1
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('cart.add'), [
            'product_uuid' => $product->uuid,
            'quantity' => 1
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message'
            ])
            ->assertJson([
                'success' => true
            ]);
    }

    // Test: Error response includes error code
    public function test_error_response_includes_code(): void
    {
        $response = $this->getJson(route('checkout.details'));

        $response->assertStatus(401)
            ->assertJsonStructure([
                'success',
                'message',
                'error_code'
            ])
            ->assertJson([
                'success' => false
            ]);
    }

    // Test: Validation errors are properly formatted
    public function test_validation_errors_are_properly_formatted(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $response = $this->postJson(route('checkout.store-details'), [
            'shipping_first_name' => '',
            'shipping_email' => 'invalid-email'
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'success',
                'message',
                'errors' => [
                    'shipping_first_name',
                    'shipping_email'
                ]
            ])
            ->assertJson([
                'success' => false
            ]);
    }
}
