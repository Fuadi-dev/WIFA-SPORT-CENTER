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
        Schema::table('bookings', function (Blueprint $table) {
            // Flag untuk menandai booking yang dibuat kurang dari 1 jam sebelum waktu booking
            // Booking last minute tidak akan di-auto cancel
            $table->boolean('is_last_minute_booking')->default(false)->after('status');
            
            // Timestamp kapan booking dibatalkan otomatis oleh sistem
            $table->timestamp('auto_cancelled_at')->nullable()->after('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['is_last_minute_booking', 'auto_cancelled_at']);
        });
    }
};
