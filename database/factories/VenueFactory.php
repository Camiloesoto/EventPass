<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

class VenueFactory extends Factory
{
    protected $model = Venue::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company . ' Hall',
            'address' => $this->faker->address,
            'timezone' => 'America/Bogota',
        ];
    }
}
