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
        Schema::table('auto_promos', function (Blueprint $table) {
            $table->renameColumn('type', 'discount_type');
            $table->renameColumn('value', 'discount_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auto_promos', function (Blueprint $table) {
            $table->renameColumn('discount_type', 'type');
            $table->renameColumn('discount_value', 'value');
        });
    }
};
