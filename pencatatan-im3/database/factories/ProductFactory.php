<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_name' => $this->faker->word() . ' ' . $this->faker->randomNumber(2),
            'product_code' => strtoupper($this->faker->unique()->bothify('IM3###')),
            'price' => $this->faker->numberBetween(30000, 100000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
