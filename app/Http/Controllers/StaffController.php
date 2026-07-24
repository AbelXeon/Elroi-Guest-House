<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 


class StaffController extends Controller
{
 public function index() {
        // Security check: ensure only staff (or admin) can enter
        if (Auth::user()->role !== 'staff' && Auth::user()->role !== 'admin') {
            return redirect('/login');
        }
        return view('staff.dashboard');
    }
}