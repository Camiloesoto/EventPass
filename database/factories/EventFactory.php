<?php

namespace Database\Factories;

use App\Enums\EventStatus;
use App\Models\{Event, Venue};
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('+1 day', '+1 month');

        return [
            'venue_id' => Venue::factory(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph,
            'start_time' => $start,
            'end_time' => (clone $start)->modify('+2 hours'),
            'capacity' => $this->faker->numberBetween(50, 500),
            'status' => EventStatus::published,
        ];
    }
}
