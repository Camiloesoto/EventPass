<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder; use App\Models\{Venue,Event,TicketType}; use App\Enums\EventStatus;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $venue = Venue::query()->firstOrCreate(['name'=>'Main Hall'], ['address'=>'123 Example St','timezone'=>'America/Bogota']);
        $event = Event::query()->create([
            'venue_id'=>$venue->id,
            'name'=>'Sample Concert',
            'description'=>'A demo seeded event',
            'start_time'=>now()->addDays(7),
            'end_time'=>now()->addDays(7)->addHours(2),
            'capacity'=>500,
            'status'=>EventStatus::published,
        ]);
        TicketType::query()->create(['event_id'=>$event->id,'name'=>'General','price'=>100000,'quantity'=>400]);
        TicketType::query()->create(['event_id'=>$event->id,'name'=>'VIP','price'=>250000,'quantity'=>100]);
    }
}
