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
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
    $table->string('full_name');
    $table->string('id_type'); 
    $table->string('id_number');
    $table->enum('status', ['active', 'blacklisted'])->default('active');
    $table->string('phone_no');
    $table->longText('id_photo')->nullable(); 
    $table->text('address')->nullable();
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
