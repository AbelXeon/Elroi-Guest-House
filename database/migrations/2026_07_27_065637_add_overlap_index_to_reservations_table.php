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
          Schema::table('reservations', function (Blueprint $table) {
            $table->index(['room_id', 'status', 'check_in_date', 'check_out_date'], 'idx_reservations_overlap');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
$table->dropIndex('idx_reservations_overlap');        });
    }
};
