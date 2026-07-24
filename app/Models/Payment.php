<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
   protected $fillable = [
        'reservation_id', 'payment_type', 'payment_way', 
        'total_amount', 'amount_paid', 'remaining_amount', 'status'
    ];

    public function reservation() {
        return $this->belongsTo(Reservation::class);
    }
}