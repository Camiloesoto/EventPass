<?php

namespace Database\Factories;

use App\Models\{Order, OrderItem, TicketType};
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'ticket_type_id' => TicketType::factory(),
            'quantity' => $this->faker->numberBetween(1, 4),
            'unit_price' => $this->faker->randomFloat(2, 50, 200),
        ];
    }
}
