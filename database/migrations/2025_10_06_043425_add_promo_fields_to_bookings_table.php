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
            $table->foreignId('promo_code_id')->nullable()->after('payment_method')->constrained('promo_codes')->nullOnDelete();
            $table->foreignId('auto_promo_id')->nullable()->after('promo_code_id')->constrained('auto_promos')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->default(0)->after('auto_promo_id');
            $table->decimal('original_price', 10, 2)->nullable()->after('discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['promo_code_id']);
            $table->dropForeign(['auto_promo_id']);
            $table->dropColumn(['promo_code_id', 'auto_promo_id', 'discount_amount', 'original_price']);
        });
    }
};
