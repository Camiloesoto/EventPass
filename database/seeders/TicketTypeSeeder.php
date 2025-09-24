<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\TicketType;
use Illuminate\Database\Seeder;

class TicketTypeSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::all();

        foreach ($events as $event) {
            // Crear diferentes tipos de tickets para cada evento
            TicketType::create([
                'event_id' => $event->getId(),
                'name' => 'General Admission',
                'quantity' => $event->getCapacity(),
                'price' => 25.00,
            ]);

            TicketType::create([
                'event_id' => $event->getId(),
                'name' => 'VIP',
                'quantity' => max(1, intval($event->getCapacity() * 0.2)),
                'price' => 50.00,
            ]);

            TicketType::create([
                'event_id' => $event->getId(),
                'name' => 'Student',
                'quantity' => max(1, intval($event->getCapacity() * 0.3)),
                'price' => 15.00,
            ]);
        }
    }
}
