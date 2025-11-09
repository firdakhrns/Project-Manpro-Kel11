<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\ReturnLog;
use App\Models\Product;

class ReturnLogItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'return_log_id' => ReturnLog::inRandomOrder()->first()->id,
            'product_id' => Product::inRandomOrder()->first()->id,
            'quantity' => $this->faker->numberBetween(1,10),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
