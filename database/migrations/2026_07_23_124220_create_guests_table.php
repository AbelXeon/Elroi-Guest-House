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

        $table->string('fullname');
        $table->string('phone_no');

        $table->enum('id_type',[
            'driving_license',
            'national_id',
            'passport',
            'kebele_id'
        ]);
        $table->longText('id_photo')->nullable();

        $table->enum('status',[
            'active',
            'blacklisted'
        ])->default('active');

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
