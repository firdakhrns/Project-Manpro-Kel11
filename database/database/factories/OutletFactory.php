<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class OutletFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'address' => $this->faker->address(),
            'owner_name' => $this->faker->name(),
            'status' => $this->faker->randomElement(['Aktif','Non-Aktif']),
            'latitude' => $this->faker->latitude(-3.35, -3.30), // sekitar Banjarmasin
            'longitude' => $this->faker->longitude(114.55, 114.60),
            'created_by' => User::inRandomOrder()->first()->id,
            'updated_at' => now(),
        ];
    }
}
