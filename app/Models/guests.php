<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class guests extends Model
{
    /** @use HasFactory<\Database\Factories\GuestsFactory> */
    use HasFactory;
    protected $fillable = [
    'full_name', 'id_type', 'id_number', 'status', 'phone_no', 'id_photo', 'address'
];
}
