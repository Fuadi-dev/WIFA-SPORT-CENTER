<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sport_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sport_id')->constrained('sports')->onDelete('cascade');
            $table->string('time_slot'); // morning, afternoon, evening
            $table->time('start_time'); // 08:00, 12:00, 18:00
            $table->time('end_time'); // 12:00, 18:00, 00:00
            $table->decimal('weekday_price', 12, 2)->default(0); // Senin-Kamis
            $table->decimal('weekend_price', 12, 2)->default(0); // Jumat-Minggu
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Unique constraint: satu sport hanya punya satu harga per time slot
            $table->unique(['sport_id', 'time_slot']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sport_prices');
    }
};
