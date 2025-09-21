<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Sport;
use App\Models\Court;

class SportsAndCourtsSeeder extends Seeder
{
    public function run(): void
    {
        // Create Sports with prices based on the price list image
        $futsal = Sport::create([
            'name' => 'Futsal',
            'slug' => 'sports-futsal',
            'icon' => 'fas fa-futbol',
            'description' => 'Olahraga sepak bola dalam ruangan',
            'price_per_hour' => 80000, // Base price (weekday 12:00-18:00)
            'is_active' => true
        ]);

        $basket = Sport::create([
            'name' => 'Basket',
            'slug' => 'sports-basket',
            'icon' => 'fas fa-basketball-ball',
            'description' => 'Olahraga bola basket',
            'price_per_hour' => 80000, // Same as futsal (shared court)
            'is_active' => true
        ]);

        $voli = Sport::create([
            'name' => 'Voli',
            'slug' => 'sports-volleyball',
            'icon' => 'fas fa-volleyball-ball',
            'description' => 'Olahraga bola voli',
            'price_per_hour' => 60000, // Base price (weekday 12:00-18:00)
            'is_active' => true
        ]);

        $badminton = Sport::create([
            'name' => 'Badminton',
            'slug' => 'sports-badminton',
            'icon' => 'fas fa-table-tennis',
            'description' => 'Olahraga bulutangkis',
            'price_per_hour' => 35000, // Base price per line (weekday 12:00-18:00)
            'is_active' => true
        ]);

        // Create Courts
        // Futsal Court (Physical Location 1)
        Court::create([
            'name' => 'Lapangan Futsal',
            'slug' => 'futsal-court',
            'sport_id' => $futsal->id,
            'type' => null,
            'physical_location' => 'location_1', // Lapangan fisik #1
            'description' => 'Lapangan futsal standar internasional',
            'is_active' => true
        ]);

        // Basket Court (uses same physical location as futsal)
        Court::create([
            'name' => 'Lapangan Futsal (Basket)',
            'slug' => 'futsal-basket-court',
            'sport_id' => $basket->id,
            'type' => null,
            'physical_location' => 'location_1', // Same as futsal - shared court
            'description' => 'Lapangan futsal yang juga bisa digunakan untuk basket',
            'is_active' => true
        ]);

        // Voli Court (Physical Location 2)
        Court::create([
            'name' => 'Lapangan Voli',
            'slug' => 'volleyball-court',
            'sport_id' => $voli->id,
            'type' => null,
            'physical_location' => 'location_2', // Lapangan fisik #2
            'description' => 'Lapangan voli standar',
            'is_active' => true
        ]);

        // Badminton Courts (both use same physical location as voli)
        Court::create([
            'name' => 'Badminton A',
            'slug' => 'badminton-court-a',
            'sport_id' => $badminton->id,
            'type' => 'A',
            'physical_location' => 'location_2', // Same as voli - shared court
            'description' => 'Court badminton A (setengah lapangan voli)',
            'is_active' => true
        ]);

        Court::create([
            'name' => 'Badminton B',
            'slug' => 'badminton-court-b',
            'sport_id' => $badminton->id,
            'type' => 'B',
            'physical_location' => 'location_2', // Same as voli - shared court
            'description' => 'Court badminton B (setengah lapangan voli)',
            'is_active' => true
        ]);
    }
}
