<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    /** @use HasFactory<\Database\Factories\GuestsFactory> */
    use HasFactory;
    
     protected $fillable = [
        'fullname', 
        'phone_no', 
        'id_type', 
        'id_photo', 
        'status'
    ];

    public function reservations() {
        return $this->hasMany(Reservation::class);
    }
}
