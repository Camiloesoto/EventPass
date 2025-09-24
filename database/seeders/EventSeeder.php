<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Venue;
use App\Enums\EventStatus;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $venues = Venue::all();
        
        if ($venues->isEmpty()) {
            $this->command->warn('No venues found. Please run VenueSeeder first.');
            return;
        }

        $events = [
            [
                'name' => 'Tech Conference 2024',
                'description' => 'Annual technology conference featuring the latest innovations in software development, AI, and cloud computing.',
                'start_time' => Carbon::now()->addDays(30)->setTime(9, 0),
                'end_time' => Carbon::now()->addDays(30)->setTime(18, 0),
                'capacity' => 500,
                'status' => EventStatus::published,
                'venue_id' => $venues->random()->getId(),
            ],
            [
                'name' => 'Music Festival Summer',
                'description' => 'Three-day music festival featuring local and international artists across multiple genres.',
                'start_time' => Carbon::now()->addDays(45)->setTime(14, 0),
                'end_time' => Carbon::now()->addDays(47)->setTime(23, 0),
                'capacity' => 2000,
                'status' => EventStatus::published,
                'venue_id' => $venues->random()->getId(),
            ],
            [
                'name' => 'Business Networking Event',
                'description' => 'Professional networking event for entrepreneurs and business leaders to connect and share ideas.',
                'start_time' => Carbon::now()->addDays(15)->setTime(18, 30),
                'end_time' => Carbon::now()->addDays(15)->setTime(21, 30),
                'capacity' => 150,
                'status' => EventStatus::published,
                'venue_id' => $venues->random()->getId(),
            ],
            [
                'name' => 'Art Exhibition Opening',
                'description' => 'Contemporary art exhibition featuring works from emerging and established artists.',
                'start_time' => Carbon::now()->addDays(20)->setTime(19, 0),
                'end_time' => Carbon::now()->addDays(20)->setTime(22, 0),
                'capacity' => 100,
                'status' => EventStatus::draft,
                'venue_id' => $venues->random()->getId(),
            ],
            [
                'name' => 'Sports Championship Final',
                'description' => 'Championship final match with live entertainment and food vendors.',
                'start_time' => Carbon::now()->addDays(60)->setTime(15, 0),
                'end_time' => Carbon::now()->addDays(60)->setTime(18, 0),
                'capacity' => 5000,
                'status' => EventStatus::published,
                'venue_id' => $venues->random()->getId(),
            ],
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }

        $this->command->info('Events created successfully!');
    }
}