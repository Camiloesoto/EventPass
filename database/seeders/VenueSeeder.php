<?php

namespace Database\Seeders;

use App\Models\Venue;
use Illuminate\Database\Seeder;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        $venues = [
            [
                'name' => 'Main Hall',
                'address' => '123 Main Street, Downtown',
                'timezone' => 'America/Bogota',
            ],
            [
                'name' => 'Convention Center',
                'address' => '456 Convention Ave, Business District',
                'timezone' => 'America/Bogota',
            ],
            [
                'name' => 'Theater Royal',
                'address' => '789 Theater Lane, Arts Quarter',
                'timezone' => 'America/Bogota',
            ],
            [
                'name' => 'Sports Arena',
                'address' => '321 Sports Blvd, Stadium District',
                'timezone' => 'America/Bogota',
            ],
        ];

        foreach ($venues as $venueData) {
            Venue::firstOrCreate(
                ['name' => $venueData['name']],
                $venueData
            );
        }
    }
}
