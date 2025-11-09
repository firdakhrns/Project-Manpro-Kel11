<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\StockLog;
use App\Models\Product;

class StockLogItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'stock_log_id' => StockLog::inRandomOrder()->first()->id,
            'product_id' => Product::inRandomOrder()->first()->id,
            'quantity' => $this->faker->numberBetween(1,50),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
