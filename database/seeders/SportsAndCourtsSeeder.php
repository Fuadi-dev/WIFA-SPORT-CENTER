<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sport;
use App\Models\Court;

class SportsAndCourtsSeeder extends Seeder
{
    public function run(): void
    {
        // Create Sports
        $futsal = Sport::create([
            'name' => 'Futsal',
            'icon' => 'fas fa-futbol',
            'description' => 'Olahraga sepak bola dalam ruangan',
            'price_per_hour' => 150000,
            'is_active' => true
        ]);

        $basket = Sport::create([
            'name' => 'Basket',
            'icon' => 'fas fa-basketball-ball',
            'description' => 'Olahraga bola basket',
            'price_per_hour' => 120000,
            'is_active' => true
        ]);

        $voli = Sport::create([
            'name' => 'Voli',
            'icon' => 'fas fa-volleyball-ball',
            'description' => 'Olahraga bola voli',
            'price_per_hour' => 100000,
            'is_active' => true
        ]);

        $badminton = Sport::create([
            'name' => 'Badminton',
            'icon' => 'fas fa-table-tennis',
            'description' => 'Olahraga bulutangkis',
            'price_per_hour' => 80000,
            'is_active' => true
        ]);

        // Create Courts
        // Futsal Court (Physical Location 1)
        Court::create([
            'name' => 'Lapangan Futsal',
            'sport_id' => $futsal->id,
            'type' => null,
            'physical_location' => 'location_1', // Lapangan fisik #1
            'description' => 'Lapangan futsal standar internasional',
            'is_active' => true
        ]);

        // Basket Court (uses same physical location as futsal)
        Court::create([
            'name' => 'Lapangan Futsal (Basket)',
            'sport_id' => $basket->id,
            'type' => null,
            'physical_location' => 'location_1', // Same as futsal - shared court
            'description' => 'Lapangan futsal yang juga bisa digunakan untuk basket',
            'is_active' => true
        ]);

        // Voli Court (Physical Location 2)
        Court::create([
            'name' => 'Lapangan Voli',
            'sport_id' => $voli->id,
            'type' => null,
            'physical_location' => 'location_2', // Lapangan fisik #2
            'description' => 'Lapangan voli standar',
            'is_active' => true
        ]);

        // Badminton Courts (both use same physical location as voli)
        Court::create([
            'name' => 'Badminton A',
            'sport_id' => $badminton->id,
            'type' => 'A',
            'physical_location' => 'location_2', // Same as voli - shared court
            'description' => 'Court badminton A (setengah lapangan voli)',
            'is_active' => true
        ]);

        Court::create([
            'name' => 'Badminton B',
            'sport_id' => $badminton->id,
            'type' => 'B',
            'physical_location' => 'location_2', // Same as voli - shared court
            'description' => 'Court badminton B (setengah lapangan voli)',
            'is_active' => true
        ]);
    }
}
