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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
    $table->foreignId('guest_id')->constrained();
    $table->foreignId('room_id')->constrained();
    $table->foreignId('user_id')->constrained(); // Who made the booking
    $table->dateTime('check_in_at');
    $table->dateTime('check_out_at');
    $table->dateTime('actual_check_out_at')->nullable();
    $table->decimal('total_price', 10, 2);
    $table->enum('status', ['checked_in', 'checked_out', 'cancelled'])->default('checked_in');
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
