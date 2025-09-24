<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\{Order, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'order_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'status' => OrderStatus::paid,
            'subtotal_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => 0,
        ];
    }
}
