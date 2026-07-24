<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use App\Models\Guest;
use App\Models\Payment;
use Carbon\Carbon;


class StaffController extends Controller
{
 public function dashboard()
    {
        $roomTypes = RoomType::all();
        return view('staff.dashboard', compact('roomTypes'));
    }

    // AJAX: returns available rooms for a given room type
    public function availableRooms(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
        ]);

        $rooms = Room::where('room_type_id', $request->room_type_id)
            ->where('status', 'available')
            ->orderBy('room_number')
            ->get(['id', 'room_number', 'price_per_night']);

        return response()->json($rooms);
    }

    public function checkinStore(Request $request)
    {
        $validated = $request->validate([
            'fullname'        => 'required|string|max:255',
            'phone_no'        => 'required|string|max:50',
            'id_type'         => 'required|in:driving_license,national_id,passport,kebele_id',
            'id_photo'        => 'nullable|string',
            'room_id'         => 'required|exists:rooms,id',
            'check_in_date'   => 'required|date',
            'check_out_date'  => 'required|date|after:check_in_date',
            'payment_type'    => 'required|in:cash,bank_transfer,pos',
            'payment_way'     => 'required|in:full,partial',
            'amount_paid'     => 'required|numeric|min:0',
        ]);

        $room = Room::findOrFail($validated['room_id']);

        if ($room->status !== 'available') {
            return back()->withErrors(['room_id' => 'That room is no longer available. Pick another.'])->withInput();
        }

        $checkIn  = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);
        $nights   = max(1, $checkIn->diffInDays($checkOut));
        $totalPrice = $nights * $room->price_per_night;

        $guest = Guest::create([
            'fullname' => $validated['fullname'],
            'phone_no' => $validated['phone_no'],
            'id_type'  => $validated['id_type'],
            'id_photo' => $validated['id_photo'] ?? null,
            'status'   => 'active',
        ]);

        $reservation = Reservation::create([
            'guest_id'      => $guest->id,
            'user_id'       => auth()->id(),
            'room_id'       => $room->id,
            'booked_date'   => now(),
            'check_in_date' => $checkIn,
            'check_out_date'=> $checkOut,
            'total_price'   => $totalPrice,
            'status'        => 'checked_in',
        ]);

        $amountPaid = $validated['amount_paid'];
        $remaining  = max(0, $totalPrice - $amountPaid);

        Payment::create([
            'reservation_id'   => $reservation->id,
            'payment_type'     => $validated['payment_type'],
            'payment_way'      => $validated['payment_way'],
            'total_amount'     => $totalPrice,
            'amount_paid'      => $amountPaid,
            'remaining_amount' => $remaining,
            'status'           => $remaining <= 0 ? 'fully_paid' : 'remaining',
        ]);

        $room->update(['status' => 'booked']);

        return redirect()->route('staff.dashboard')
            ->with('success', "Checked in {$guest->fullname} — Room {$room->room_number}.");
    }

    public function checkoutSearch(Request $request)
{
    $query = $request->get('query');
    
    // Find active reservations where guest name matches query
    $reservations = Reservation::with(['guest', 'room', 'payment'])
        ->where('status', 'checked_in')
        ->whereHas('guest', function($q) use ($query) {
            $q->where('fullname', 'LIKE', "%{$query}%")
              ->orWhere('phone_no', 'LIKE', "%{$query}%");
        })
        ->get();

    return response()->json($reservations);
}

public function checkoutProcess(Request $request)
{
    $request->validate(['reservation_id' => 'required|exists:reservations,id']);
    
    $res = Reservation::findOrFail($request->reservation_id);
    
    // 1. Mark Reservation as checked out
    $res->update([
        'status' => 'checked_out',
        'actual_check_out_date' => now()
    ]);

    // 2. Make the room available again
    Room::where('id', $res->room_id)->update(['status' => 'available']);

    return back()->with('success', 'Guest checked out successfully and room is now available.');
}

public function reservationStore(Request $request)
{
    $validated = $request->validate([
        'fullname' => 'required|string',
        'phone_no' => 'required|string',
        'room_id'  => 'required|exists:rooms,id',
        'check_in_date' => 'required|date',
        'check_out_date'=> 'required|date|after:check_in_date',
        'amount_paid'   => 'required|numeric',
    ]);

    $room = Room::findOrFail($validated['room_id']);

    // Create Guest
    $guest = Guest::create([
        'fullname' => $validated['fullname'],
        'phone_no' => $validated['phone_no'],
        'id_type'  => 'national_id', // Default, will update on arrival
        'status'   => 'active'
    ]);

    $checkIn  = \Carbon\Carbon::parse($validated['check_in_date']);
    $checkOut = \Carbon\Carbon::parse($validated['check_out_date']);
    $nights   = max(1, $checkIn->diffInDays($checkOut));
    $total    = $nights * $room->price_per_night;

    // Create Reservation as 'checked_in' but we will track it by room status
    // Or you can add a 'reserved' status to your reservation enum if you wish. 
    // Using 'checked_in' for now to keep your DB schema safe.
    $res = Reservation::create([
        'guest_id' => $guest->id,
        'user_id'  => auth()->id(),
        'room_id'  => $room->id,
        'booked_date' => now(),
        'check_in_date' => $checkIn,
        'check_out_date' => $checkOut,
        'total_price' => $total,
        'status' => 'checked_in' 
    ]);

    Payment::create([
        'reservation_id' => $res->id,
        'payment_type' => 'cash',
        'payment_way' => 'partial',
        'total_amount' => $total,
        'amount_paid' => $validated['amount_paid'],
        'remaining_amount' => $total - $validated['amount_paid'],
        'status' => 'remaining'
    ]);

    $room->update(['status' => 'reserved']);

    return back()->with('success', 'Room reserved successfully.');
}

public function reservationSearch(Request $request)
{
    // Search for guests in rooms that are currently 'reserved'
    $res = Reservation::with(['guest', 'room', 'payment'])
        ->whereHas('room', function($q){ $q->where('status', 'reserved'); })
        ->whereHas('guest', function($q) use ($request){
            $q->where('fullname', 'LIKE', "%{$request->query('query')}%");
        })->get();
    return response()->json($res);
}

public function reservationComplete(Request $request)
{
    $request->validate([
        'reservation_id' => 'required',
        'id_type' => 'required',
        'id_photo' => 'required'
    ]);

    $res = Reservation::findOrFail($request->reservation_id);
    $res->guest->update([
        'id_type' => $request->id_type,
        'id_photo' => $request->id_photo
    ]);

    $res->room->update(['status' => 'booked']);

    return back()->with('success', 'Reservation converted to full Check-in!');
}

}