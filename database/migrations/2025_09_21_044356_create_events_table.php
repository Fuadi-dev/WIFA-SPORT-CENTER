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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_code')->unique(); // EVT-001, EVT-002, etc
            $table->string('title');
            $table->string('slug')->unique()->nullable();
            $table->text('description');
            $table->string('poster')->nullable(); // path to poster image
            $table->foreignId('sport_id')->constrained('sports')->onDelete('cascade');
            $table->foreignId('court_id')->constrained('courts')->onDelete('cascade');
            $table->date('event_date'); // must be >32 days from creation
            $table->time('start_time')->default('08:00:00');
            $table->time('end_time')->default('17:00:00');
            $table->decimal('registration_fee', 10, 2)->default(0); // biaya pendaftaran
            $table->integer('max_teams')->default(16); // maksimal tim yang bisa daftar
            $table->date('registration_deadline'); // batas waktu pendaftaran
            $table->enum('status', ['draft', 'open_registration', 'registration_closed', 'ongoing', 'completed', 'cancelled'])->default('draft');
            $table->text('requirements')->nullable(); // persyaratan peserta
            $table->text('prize_info')->nullable(); // informasi hadiah
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
