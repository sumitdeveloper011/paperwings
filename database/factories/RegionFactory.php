<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RegionFactory extends Factory
{
    protected $model = \App\Models\Region::class;

    public function definition(): array
    {
        $name = fake()->city();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'status' => 1,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1,
        ]);
    }
}

