<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Outlet;

class SalesLogFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'outlet_id' => Outlet::inRandomOrder()->first()->id,
            'date' => $this->faker->dateTimeBetween('-30 days','now'),
            'total_sales' => $this->faker->numberBetween(500000,2000000),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
