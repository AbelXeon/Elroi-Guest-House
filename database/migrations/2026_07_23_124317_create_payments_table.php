<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

        $table->foreignId('reservation_id')
              ->constrained()
              ->cascadeOnDelete();

        $table->enum('payment_type',[
            'cash',
            'bank_transfer',
            'pos'
        ]);

        $table->enum('payment_way',[
            'full',
            'partial'
        ]);

        $table->decimal('total_amount',10,2);
        $table->decimal('amount_paid',10,2);
        $table->decimal('remaining_amount',10,2);

        $table->enum('status',[
            'fully_paid',
            'remaining'
        ]);

        $table->timestamps();
    });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
