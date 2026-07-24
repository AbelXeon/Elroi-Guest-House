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

        $table->foreignId('guest_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->foreignId('user_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->foreignId('room_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->dateTime('booked_date');
        $table->dateTime('check_in_date');
        $table->dateTime('check_out_date');
        $table->dateTime('actual_check_out_date')->nullable();

        $table->decimal('total_price',10,2);

        $table->enum('status',[
            'checked_in',
            'checked_out',
            'cancelled'
        ])->default('checked_in');

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
