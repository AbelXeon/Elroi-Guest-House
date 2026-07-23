<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationsController extends Controller
{
     public function create()
    {
        $guests = Guest::where('status', 'active')->get();
        $rooms = Room::where('status', 'available')->get();

        return view('reservations.create', compact('guests', 'rooms'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guest_id'     => 'required|exists:guests,id',
            'room_id'      => 'required|exists:rooms,id',
            'check_in_at'  => 'required|date',
            'check_out_at' => 'required|date|after:check_in_at',
            'total_price'  => 'required|numeric',
        ]);

        // Create Reservation
        Reservation::create([
            'guest_id'     => $request->guest_id,
            'room_id'      => $request->room_id,
            'user_id'      => Auth::id(), // Staff who made booking
            'check_in_at'  => $request->check_in_at,
            'check_out_at' => $request->check_out_at,
            'total_price'  => $request->total_price,
            'status'       => 'checked_in',
        ]);

        // Mark room as occupied
        $room = Room::findOrFail($request->room_id);
        $room->update(['status' => 'occupied']);

        return redirect()->route('staff.dashboard')->with('success', 'Guest checked in successfully!');
    }

    public function checkout($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->update([
            'status' => 'checked_out',
            'actual_check_out_at' => now(),
        ]);

        // Mark room as available again
        $reservation->room->update(['status' => 'available']);

        return back()->with('success', 'Guest checked out successfully!');
    }
}
