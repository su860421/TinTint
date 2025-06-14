<?php

namespace Database\Factories;

use App\Enums\OrderStatusEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $user = User::firstOrCreate(
            $attributes = ['email' => 'test@example.com'],
            User::factory()->raw($attributes)
        );

        return [
            'user_id' => $user->id,
            'order_number' => fake()->unique()->numerify('ORD-########'),
            'status' => OrderStatusEnum::defaultStatus()->value,
            'total_amount' => fake()->numberBetween(100, 10000),
        ];
    }

    public function status(OrderStatusEnum $status): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status->value
        ]);
    }
}
