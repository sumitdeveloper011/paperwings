<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(3, true);
        
        return [
            'uuid' => Str::uuid(),
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'name' => $name,
            'slug' => Str::slug($name),
            'total_price' => fake()->randomFloat(2, 10, 500),
            'discount_price' => fake()->randomFloat(2, 5, 400),
            'description' => fake()->paragraph(),
            'short_description' => fake()->sentence(),
            'status' => 1,
            'stock' => fake()->numberBetween(0, 100),
            'product_type' => fake()->numberBetween(1, 3),
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 0,
        ]);
    }
}

