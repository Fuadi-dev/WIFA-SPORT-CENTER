<?php

namespace Database\Seeders;

use App\Models\Sport;
use App\Models\SportPrice;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SportPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Price list based on WIFA Sport Center:
     * 
     * Futsal:
     * - Jam 08.00-12.00: Weekday 60k, Weekend 65k
     * - Jam 12.00-18.00: Weekday 80k, Weekend 85k
     * - Jam 18.00-00.00: Weekday 100k, Weekend 105k
     * 
     * Badminton (Per Line):
     * - Jam 08.00-12.00: Weekday 30k, Weekend 35k
     * - Jam 12.00-18.00: Weekday 35k, Weekend 40k
     * - Jam 18.00-00.00: Weekday 40k, Weekend 45k
     * 
     * Voli:
     * - Jam 08.00-12.00: Weekday 50k, Weekend 55k
     * - Jam 12.00-18.00: Weekday 60k, Weekend 65k
     * - Jam 18.00-00.00: Weekday 70k, Weekend 75k
     * 
     * Note: Weekday = Senin-Kamis, Weekend = Jumat-Minggu
     */
    public function run(): void
    {
        $priceList = [
            'Futsal' => [
                ['slot' => 'morning', 'start' => '08:00', 'end' => '12:00', 'weekday' => 60000, 'weekend' => 65000],
                ['slot' => 'afternoon', 'start' => '12:00', 'end' => '18:00', 'weekday' => 80000, 'weekend' => 85000],
                ['slot' => 'evening', 'start' => '18:00', 'end' => '00:00', 'weekday' => 100000, 'weekend' => 105000],
            ],
            'Badminton' => [
                ['slot' => 'morning', 'start' => '08:00', 'end' => '12:00', 'weekday' => 30000, 'weekend' => 35000],
                ['slot' => 'afternoon', 'start' => '12:00', 'end' => '18:00', 'weekday' => 35000, 'weekend' => 40000],
                ['slot' => 'evening', 'start' => '18:00', 'end' => '00:00', 'weekday' => 40000, 'weekend' => 45000],
            ],
            'Voli' => [
                ['slot' => 'morning', 'start' => '08:00', 'end' => '12:00', 'weekday' => 50000, 'weekend' => 55000],
                ['slot' => 'afternoon', 'start' => '12:00', 'end' => '18:00', 'weekday' => 60000, 'weekend' => 65000],
                ['slot' => 'evening', 'start' => '18:00', 'end' => '00:00', 'weekday' => 70000, 'weekend' => 75000],
            ],
        ];

        foreach ($priceList as $sportName => $prices) {
            $sport = Sport::where('name', $sportName)->first();
            
            if (!$sport) {
                $this->command->warn("Sport '{$sportName}' not found. Skipping...");
                continue;
            }

            foreach ($prices as $price) {
                SportPrice::updateOrCreate(
                    [
                        'sport_id' => $sport->id,
                        'time_slot' => $price['slot'],
                    ],
                    [
                        'start_time' => $price['start'],
                        'end_time' => $price['end'],
                        'weekday_price' => $price['weekday'],
                        'weekend_price' => $price['weekend'],
                        'is_active' => true,
                    ]
                );
            }

            $this->command->info("Prices for {$sportName} seeded successfully.");
        }
    }
}
