<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Venue;
use App\Enums\EventStatus;
use Illuminate\Support\Facades\Log;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Log the seeder execution
        Log::info('Starting EventSeeder execution');

        // Create venues first
        $venues = [
            [
                'name' => 'Centro de Convenciones Bogotá',
                'address' => 'Calle 26 #68-35, Bogotá',
                'timezone' => 'America/Bogota'
            ],
            [
                'name' => 'Teatro Colón',
                'address' => 'Calle 10 #5-32, Bogotá',
                'timezone' => 'America/Bogota'
            ],
            [
                'name' => 'Auditorio Mayor Universidad Nacional',
                'address' => 'Carrera 30 #45-03, Bogotá',
                'timezone' => 'America/Bogota'
            ]
        ];

        foreach ($venues as $venueData) {
            $venue = Venue::create($venueData);
            Log::info('Venue created', ['venue_id' => $venue->getId(), 'venue_name' => $venue->getName()]);
        }

        // Create events
        $events = [
            [
                'venue_id' => 1,
                'name' => 'Conferencia de Tecnología 2025',
                'description' => 'La conferencia más importante de tecnología del año. Únete a nosotros para conocer las últimas tendencias en desarrollo web, inteligencia artificial y tecnologías emergentes.',
                'start_time' => now()->addDays(15)->setTime(9, 0),
                'end_time' => now()->addDays(15)->setTime(18, 0),
                'capacity' => 500,
                'status' => EventStatus::published
            ],
            [
                'venue_id' => 2,
                'name' => 'Concierto de Música Clásica',
                'description' => 'Una noche mágica con la Orquesta Sinfónica Nacional interpretando las mejores obras de Mozart, Beethoven y Bach.',
                'start_time' => now()->addDays(20)->setTime(19, 30),
                'end_time' => now()->addDays(20)->setTime(22, 0),
                'capacity' => 300,
                'status' => EventStatus::published
            ],
            [
                'venue_id' => 3,
                'name' => 'Workshop de Laravel Avanzado',
                'description' => 'Aprende las mejores prácticas de Laravel con ejemplos prácticos. Perfecto para desarrolladores que quieren llevar sus habilidades al siguiente nivel.',
                'start_time' => now()->addDays(25)->setTime(8, 0),
                'end_time' => now()->addDays(25)->setTime(17, 0),
                'capacity' => 50,
                'status' => EventStatus::published
            ],
            [
                'venue_id' => 1,
                'name' => 'Feria de Empleo Tech',
                'description' => 'Conecta con las mejores empresas de tecnología. Más de 50 empresas buscando talento en desarrollo, diseño y marketing digital.',
                'start_time' => now()->addDays(30)->setTime(10, 0),
                'end_time' => now()->addDays(30)->setTime(16, 0),
                'capacity' => 1000,
                'status' => EventStatus::published
            ],
            [
                'venue_id' => 2,
                'name' => 'Festival de Cine Independiente',
                'description' => 'Proyección de cortometrajes y largometrajes independientes. Incluye charlas con directores y productores.',
                'start_time' => now()->addDays(35)->setTime(14, 0),
                'end_time' => now()->addDays(35)->setTime(23, 0),
                'capacity' => 200,
                'status' => EventStatus::published
            ]
        ];

        foreach ($events as $eventData) {
            $event = Event::create($eventData);
            Log::info('Event created', [
                'event_id' => $event->getId(),
                'event_name' => $event->getName(),
                'venue_id' => $event->getVenueId(),
                'capacity' => $event->getCapacity()
            ]);
        }

        $this->command->info('Events and venues created successfully!');
        $this->command->info('Created ' . count($venues) . ' venues and ' . count($events) . ' events');
    }
}