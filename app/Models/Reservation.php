<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'guest_id', 'user_id', 'room_id', 
        'booked_date', 'check_in_date', 'check_out_date', 
        'actual_check_out_date', 'total_price', 'status'
    ];

    public function guest() {
        return $this->belongsTo(Guest::class);
    }

    public function staff() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function room() {
        return $this->belongsTo(Room::class);
    }

    public function payment() {
        return $this->hasOne(Payment::class);
    }
}