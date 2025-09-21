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
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->string('registration_code')->unique(); // REG-EVT-001, REG-EVT-002, etc
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // team leader
            $table->string('team_name');
            $table->text('team_members'); // JSON array of team members with names and positions
            $table->string('contact_person'); // nama penanggung jawab
            $table->string('contact_phone'); // nomor telepon penanggung jawab
            $table->string('contact_email'); // email penanggung jawab
            $table->text('additional_info')->nullable(); // informasi tambahan
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            $table->decimal('registration_fee_paid', 10, 2)->default(0);
            $table->timestamp('registered_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
