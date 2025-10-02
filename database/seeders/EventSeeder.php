<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Sport;
use App\Models\Court;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sports = Sport::all();
        $courts = Court::all();

        $events = [
            [
                'title' => 'Turnamen Futsal Ramadan Cup 2024',
                'slug' => Str::slug('Turnamen Futsal Ramadan Cup 2024'),
                'description' => 'Turnamen futsal terbuka untuk umum dalam rangka menyambut bulan suci Ramadan. Hadiah total jutaan rupiah!',
                'event_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'sport_id' => $sports->where('name', 'Futsal')->first()->id,
                'court_id' => $courts->where('sport_id', $sports->where('name', 'Futsal')->first()->id)->first()->id,
                'max_teams' => 16,
                'registration_fee' => 500000,
                'registration_deadline' => Carbon::now()->addDays(20)->format('Y-m-d'),
                'status' => 'open_registration',
                'poster' => '/asset/events/futsal-ramadan-cup.jpg',
                'requirements' => 'Tim terdiri dari maksimal 8 pemain (5 main + 3 cadangan). Usia maksimal 35 tahun. Registrasi dibuka hingga 20 hari sebelum acara. Biaya registrasi sudah termasuk jersey dan konsumsi.',
                'prize_info' => 'Hadiah juara 1: Rp 5.000.000. Hadiah juara 2: Rp 3.000.000. Hadiah juara 3: Rp 2.000.000. Total hadiah Rp 10.000.000',
            ],
            [
                'title' => 'Badminton Championship 2024',
                'slug' => Str::slug('Badminton Championship 2024'),
                'description' => 'Kejuaraan bulutangkis tingkat regional. Kategori tunggal putra, tunggal putri, ganda putra, ganda putri, dan ganda campuran.',
                'event_date' => Carbon::now()->addDays(25)->format('Y-m-d'),
                'start_time' => '07:00:00',
                'end_time' => '18:00:00',
                'sport_id' => $sports->where('name', 'Badminton')->first()->id,
                'court_id' => $courts->where('sport_id', $sports->where('name', 'Badminton')->first()->id)->first()->id,
                'max_teams' => 64,
                'registration_fee' => 150000,
                'registration_deadline' => Carbon::now()->addDays(15)->format('Y-m-d'),
                'status' => 'open_registration',
                'poster' => '/asset/events/badminton-championship.jpg',
                'requirements' => 'Terbuka untuk semua usia. 5 kategori: Tunggal Putra, Tunggal Putri, Ganda Putra, Ganda Putri, Ganda Campuran. Sistem gugur langsung. Menggunakan shuttlecock plastik.',
                'prize_info' => 'Hadiah trofi dan uang pembinaan untuk juara 1-3 setiap kategori. Total hadiah Rp 5.000.000',
            ],
            [
                'title' => 'Basketball 3x3 Street Tournament',
                'slug' => Str::slug('Basketball 3x3 Street Tournament'),
                'description' => 'Turnamen bola basket 3x3 gaya streetball. Format pertandingan cepat dan seru untuk semua level pemain.',
                'event_date' => Carbon::now()->addDays(21)->format('Y-m-d'),
                'start_time' => '09:00:00',
                'end_time' => '16:00:00',
                'sport_id' => $sports->where('name', 'Basket')->first()->id,
                'court_id' => $courts->where('sport_id', $sports->where('name', 'Basket')->first()->id)->first()->id,
                'max_teams' => 24,
                'registration_fee' => 300000,
                'registration_deadline' => Carbon::now()->addDays(14)->format('Y-m-d'),
                'status' => 'open_registration',
                'poster' => '/asset/events/basketball-3x3.jpg',
                'requirements' => 'Tim terdiri dari 4 pemain (3 main + 1 cadangan). Durasi pertandingan 10 menit atau first to 21 points. Usia minimum 16 tahun. Kategori open (campur).',
                'prize_info' => 'Hadiah uang tunai untuk juara 1-3. Total hadiah Rp 3.000.000',
            ],
            [
                'title' => 'Badminton Community Cup',
                'slug' => Str::slug('Badminton Community Cup'),
                'description' => 'Turnamen badminton untuk mempererat tali silaturahmi antar komunitas. Kategori pemula hingga mahir.',
                'event_date' => Carbon::now()->addDays(35)->format('Y-m-d'),
                'start_time' => '08:00:00',
                'end_time' => '15:00:00',
                'sport_id' => $sports->where('name', 'Badminton')->first()->id,
                'court_id' => $courts->where('sport_id', $sports->where('name', 'Badminton')->first()->id)->first()->id,
                'max_teams' => 32,
                'registration_fee' => 75000,
                'registration_deadline' => Carbon::now()->addDays(25)->format('Y-m-d'),
                'status' => 'open_registration',
                'poster' => '/asset/events/badminton-community-cup.jpg',
                'requirements' => 'Kategori Pemula dan Mahir. Sistem round robin untuk penyisihan. Knockout untuk fase final. Best of 3 games untuk final. Semua peralatan disediakan panitia.',
                'prize_info' => 'Hadiah trofi dan uang pembinaan untuk juara 1-3. Total hadiah Rp 2.000.000',
            ],
            [
                'title' => 'Voli Pantai Festival',
                'slug' => Str::slug('Esports Mobile Legends Tournament'),
                'description' => 'Festival voli pantai dengan konsep fun tournament. Dilengkapi dengan musik DJ dan doorprize menarik.',
                'event_date' => Carbon::now()->addDays(40)->format('Y-m-d'),
                'start_time' => '10:00:00',
                'end_time' => '17:00:00',
                'sport_id' => $sports->where('name', 'Voli')->first()->id,
                'court_id' => $courts->where('sport_id', $sports->where('name', 'Voli')->first()->id)->first()->id,
                'max_teams' => 20,
                'registration_fee' => 200000,
                'registration_deadline' => Carbon::now()->addDays(28)->format('Y-m-d'),
                'status' => 'open_registration',
                'poster' => '/asset/events/beach-volleyball.jpg',
                'requirements' => 'Tim terdiri dari 2 pemain (inti) + 1 cadangan. Format best of 3 set. Setiap set sampai 21 poin. Dress code: kaos pantai dan celana pendek.',
                'prize_info' => 'Hadiah voucher belanja dan piala. Total hadiah Rp 1.500.000',
            ],
            [
                'title' => 'Fun Run & Healthy Lifestyle',
                'slug' => Str::slug('Esports Mobile Legends Tournament'),
                'description' => 'Event lari santai 5K yang mengutamakan kebersamaan dan gaya hidup sehat. Cocok untuk keluarga.',
                'event_date' => Carbon::now()->addDays(15)->format('Y-m-d'),
                'start_time' => '06:00:00',
                'end_time' => '09:00:00',
                'sport_id' => $sports->first()->id, // Default sport
                'court_id' => $courts->first()->id, // Default court
                'max_teams' => 200,
                'registration_fee' => 50000,
                'registration_deadline' => Carbon::now()->addDays(10)->format('Y-m-d'),
                'status' => 'open_registration',
                'poster' => '/asset/events/fun-run.jpg',
                'requirements' => 'Jarak tempuh 5 kilometer. Start pukul 06:30 WIB. Kategori: Pria, Wanita, Anak-anak (dibawah 12 tahun). Tersedia pos kesehatan di sepanjang rute.',
                'prize_info' => 'Finisher mendapat medal dan sertifikat. Total hadiah Rp 500.000',
            ],
            [
                'title' => 'Esports Mobile Legends Tournament',
                'slug' => Str::slug('Esports Mobile Legends Tournament'),
                'description' => 'Turnamen Mobile Legends dengan total hadiah belasan juta rupiah. Daftar tim sekarang!',
                'event_date' => Carbon::now()->addDays(18)->format('Y-m-d'),
                'start_time' => '09:00:00',
                'end_time' => '21:00:00',
                'sport_id' => $sports->first()->id, // Default sport for esports
                'court_id' => $courts->first()->id, // Default court for esports
                'max_teams' => 32,
                'registration_fee' => 250000,
                'registration_deadline' => Carbon::now()->addDays(12)->format('Y-m-d'),
                'status' => 'open_registration',
                'poster' => '/asset/events/mobile-legends.jpg',
                'requirements' => 'Tim terdiri dari 5 pemain inti + 1 substitute. Format: Best of 3 (BO3) untuk babak awal, Best of 5 (BO5) untuk final. Draft pick mode. Rank minimum: Epic.',
                'prize_info' => 'Hadiah juara 1: Rp 8.000.000. Total hadiah Rp 15.000.000',
            ],
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }
    }
}
