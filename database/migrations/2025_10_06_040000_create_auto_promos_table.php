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
        Schema::create('auto_promos', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('discount_type', ['percentage', 'fixed']);
            $table->decimal('discount_value', 8, 2);
            
            // Schedule Type: specific_date, day_of_week, daily
            $table->enum('schedule_type', ['specific_date', 'day_of_week', 'daily']);
            
            // For specific dates
            $table->date('specific_date')->nullable();
            
            // For day of week (0=Sunday, 1=Monday, ... 6=Saturday)
            $table->json('days_of_week')->nullable(); // [0,1,2] for Sun, Mon, Tue
            
            // Time range
            $table->time('start_time');
            $table->time('end_time');
            
            // Date range for promo validity (nullable for specific_date type)
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            
            // Minimum transaction
            $table->decimal('min_transaction', 10, 2)->nullable();
            
            // Maximum discount (for percentage type)
            $table->decimal('max_discount', 10, 2)->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auto_promos');
    }
};
