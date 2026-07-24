<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminAction extends Model
{
    /** @use HasFactory<\Database\Factories\AdminActionsFactory> */
    use HasFactory;
     protected $fillable = ['user_id', 'action_type', 'status'];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
