<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
     protected $fillable = [
        'guest_id', 'user_id', 'room_id', 'booked_date',
        'check_in_date', 'check_out_date', 'actual_check_out_date',
        'total_price', 'status',
    ];

    protected $casts = [
        'booked_date'           => 'datetime',
        'check_in_date'         => 'datetime',
        'check_out_date'        => 'datetime',
        'actual_check_out_date' => 'datetime',
    ];

    public function guest() { return $this->belongsTo(Guest::class); }
    public function room()  { return $this->belongsTo(Room::class); }
    public function user()  { return $this->belongsTo(User::class); }
    public function payment() { return $this->hasOne(Payment::class); }
}