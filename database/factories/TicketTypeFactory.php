<?php

namespace Database\Factories;

use App\Models\{Event, TicketType};
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketTypeFactory extends Factory
{
    protected $model = TicketType::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'name' => $this->faker->randomElement(['General', 'VIP', 'Premium']),
            'price' => $this->faker->numberBetween(50000, 250000) / 100,
            'quantity' => $this->faker->numberBetween(50, 200),
        ];
    }
}
