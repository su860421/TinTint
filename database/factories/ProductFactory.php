<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        $price = fake()->numberBetween(50, 1000);
        
        return [
            'name' => fake()->name,
            'price' => $price,
            'stock' => fake()->numberBetween(0, 100),
        ];
    }
}
