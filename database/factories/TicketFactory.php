<?php

namespace Database\Factories;

use App\Enums\TicketStatus;
use App\Models\{OrderItem, Ticket, User};
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'order_item_id' => OrderItem::factory(),
            'user_id' => User::factory(),
            'pdf_url' => '#',
            'status' => TicketStatus::issued,
            'meta' => ['seat' => 'A1'],
        ];
    }
}
