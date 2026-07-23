<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Room;
use App\Models\Reservation;

class StaffController extends Controller
{
    public function dashboard()
    {
        $totalGuests = Guest::count();
        $availableRooms = Room::where('status', 'available')->count();
        $activeReservations = Reservation::where('status', 'checked_in')->get();

        return view('staff.dashboard', compact('totalGuests', 'availableRooms', 'activeReservations'));
    }
}