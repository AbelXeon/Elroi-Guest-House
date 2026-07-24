<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Room;
use App\Models\Reservation;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ReservationsController extends Controller
{
    public function getAvailableRooms() {
        return response()->json(Room::where('status', 'available')->with('roomType')->get());
    }

    public function checkIn(Request $request) {
        // 1. Create or Update Guest
        $guest = Guest::updateOrCreate(
            ['phone_no' => $request->phone_no],
            [
                'fullname' => $request->fullname,
                'id_type' => $request->id_type,
                'id_number' => $request->id_number,
                'id_photo' => $request->id_photo, // Base64 from Webcam
            ]
        );

        // 2. Create Reservation
        $room = Room::find($request->room_id);
        $checkout = Carbon::now()->addDays($request->nights);
        
        $reservation = Reservation::create([
            'guest_id' => $guest->id,
            'user_id' => Auth::id(),
            'room_id' => $room->id,
            'booked_date' => now(),
            'check_in_date' => now(),
            'check_out_date' => $checkout,
            'total_price' => $room->price_per_night * $request->nights,
            'status' => 'checked_in'
        ]);

        // 3. Update Room Status
        $room->update(['status' => 'booked']);

        return response()->json(['message' => 'Checked in successfully!']);
    }

    public function list() {
        return response()->json(Reservation::with(['guest', 'room'])->orderBy('created_at', 'desc')->get());
    }
}