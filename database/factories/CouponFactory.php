<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CouponFactory extends Factory
{
    protected $model = \App\Models\Coupon::class;

    public function definition(): array
    {
        return [
            'uuid' => Str::uuid(),
            'code' => strtoupper(Str::random(8)),
            'name' => fake()->words(3, true),
            'description' => fake()->sentence(),
            'type' => 'percentage',
            'value' => fake()->numberBetween(5, 50),
            'minimum_amount' => fake()->numberBetween(10, 100),
            'maximum_discount' => fake()->numberBetween(500, 1000),
            'usage_limit' => fake()->numberBetween(10, 100),
            'usage_count' => 0,
            'start_date' => Carbon::now()->subDays(5),
            'end_date' => Carbon::now()->addDays(30),
            'status' => 1,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
            'start_date' => Carbon::now()->subDays(5),
            'end_date' => Carbon::now()->addDays(30),
        ]);
    }
}

