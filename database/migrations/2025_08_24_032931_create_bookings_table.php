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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code')->unique(); // WIFA-001, WIFA-002, etc
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('sport_id')->constrained('sports')->onDelete('cascade');
            $table->foreignId('court_id')->constrained('courts')->onDelete('cascade');
            $table->string('slug')->unique()->nullable();
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('team_name'); // nama tim/instansi/individu
            $table->text('notes')->nullable();
            $table->enum('payment_method', ['cash', 'midtrans']);
            $table->decimal('total_price', 10, 2);
            $table->enum('status', ['pending_payment', 'confirmed', 'paid', 'cancelled', 'completed'])->default('pending_payment');
            $table->string('midtrans_snap_token')->nullable(); // For Midtrans integration
            $table->string('midtrans_order_id')->nullable(); // Midtrans order ID
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
