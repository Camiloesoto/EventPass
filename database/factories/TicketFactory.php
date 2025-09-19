<?php

namespace Database\Factories;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status'  => Ticket::STATUS_ISSUED,
            'meta'    => ['seat'=>'A1'],
            // omit code and qr_hash => model boot fills them
        ];
    }
}
