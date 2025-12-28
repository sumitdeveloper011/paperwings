<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = \App\Models\Order::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'user_id' => User::factory(),
            'billing_first_name' => fake()->firstName(),
            'billing_last_name' => fake()->lastName(),
            'billing_email' => fake()->email(),
            'billing_phone' => fake()->phoneNumber(),
            'billing_street_address' => fake()->streetAddress(),
            'billing_city' => fake()->city(),
            'billing_zip_code' => fake()->postcode(),
            'billing_country' => 'New Zealand',
            'shipping_first_name' => fake()->firstName(),
            'shipping_last_name' => fake()->lastName(),
            'shipping_email' => fake()->email(),
            'shipping_phone' => fake()->phoneNumber(),
            'shipping_street_address' => fake()->streetAddress(),
            'shipping_city' => fake()->city(),
            'shipping_zip_code' => fake()->postcode(),
            'shipping_country' => 'New Zealand',
            'subtotal' => fake()->randomFloat(2, 50, 500),
            'shipping' => fake()->randomFloat(2, 5, 20),
            'discount' => fake()->randomFloat(2, 0, 50),
            'total' => fake()->randomFloat(2, 55, 520),
            'payment_method' => 'stripe',
            'payment_status' => 'pending',
            'status' => 'pending',
        ];
    }
}

