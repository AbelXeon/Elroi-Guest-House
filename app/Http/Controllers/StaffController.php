<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Guest;
use App\Models\Payment;
use Carbon\Carbon;


class StaffController extends Controller
{
    public function dashboard()
    {
        $roomTypes = RoomType::withCount([
            'rooms',
            'rooms as available_rooms_count' => fn($q) => $q->where('status', 'available'),
        ])->get();

        $activeStays = Reservation::with(['guest', 'room'])
            ->where('status', 'checked_in')
            ->whereNull('actual_check_out_date')
            ->whereHas('room', fn($q) => $q->where('status', 'booked'))
            ->orderByDesc('check_in_date')
            ->get();

        $pendingArrivals = Reservation::with(['guest', 'room'])
            ->whereHas('room', fn($q) => $q->where('status', 'reserved'))
            ->orderByDesc('booked_date')
            ->get();

        $bannedGuests = Guest::where('status', 'blacklisted')
            ->orderBy('fullname')
            ->get();

        $dashStats = [
            'active_guests'    => $activeStays->count(),
            'available_rooms'  => Room::where('status', 'available')->count(),
            'pending_arrivals' => $pendingArrivals->count(),
            'checked_in_today' => Reservation::whereDate('check_in_date', today())->count(),
        ];

        return view('staff.dashboard', compact(
            'roomTypes', 'activeStays', 'pendingArrivals', 'bannedGuests', 'dashStats'
        ));
    }

    // Reusable date-overlap check: is this room already booked for any part of this date range?
    private function roomHasOverlap($roomId, $checkIn, $checkOut, $excludeReservationId = null)
    {
        return Reservation::where('room_id', $roomId)
            ->where('status', 'checked_in')
            ->when($excludeReservationId, fn($q) => $q->where('id', '!=', $excludeReservationId))
            ->where('check_in_date', '<', $checkOut)
            ->where('check_out_date', '>', $checkIn)
            ->exists();
    }

    // Saves a captured base64 photo as a real file and returns its public URL.
    // If the string isn't base64 image data (e.g. an existing stored URL), it's returned unchanged.
    private function storeIdPhoto(?string $base64): ?string
    {
        if (!$base64) return null;
        if (!str_starts_with($base64, 'data:image')) return $base64;

        [$meta, $data] = explode(',', $base64, 2);
        preg_match('/data:image\/(\w+);base64/', $meta, $matches);
        $ext = $matches[1] ?? 'jpg';

        $binary   = base64_decode($data);
        $filename = 'guest-ids/' . uniqid() . '.' . $ext;
        Storage::disk('public')->put($filename, $binary);

        return Storage::disk('public')->url($filename);
    }

    public function availableRooms(Request $request)
    {
        $validated = $request->validate([
            'room_type_id'   => 'required|exists:room_types,id',
            'check_in_date'  => 'nullable|date',
            'check_out_date' => 'nullable|date|after:check_in_date',
        ]);

        $query = Room::where('room_type_id', $validated['room_type_id'])
            ->whereNotIn('status', ['maintenance', 'cleaning']);

        if (!empty($validated['check_in_date']) && !empty($validated['check_out_date'])) {
            $checkIn  = $validated['check_in_date'];
            $checkOut = $validated['check_out_date'];

            $query->whereDoesntHave('reservations', function ($q) use ($checkIn, $checkOut) {
                $q->where('status', 'checked_in')
                  ->where('check_in_date', '<', $checkOut)
                  ->where('check_out_date', '>', $checkIn);
            });
        } else {
            $query->where('status', 'available');
        }

        $rooms = $query->orderBy('room_number')->get(['id', 'room_number', 'price_per_night']);

        return response()->json($rooms);
    }

    public function checkGuestStatus(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string',
            'phone_no' => 'required|string',
        ]);

        $guest = Guest::where('fullname', $request->fullname)
            ->where('phone_no', $request->phone_no)
            ->first();

        return response()->json([
            'found'  => (bool) $guest,
            'status' => $guest->status ?? 'active',
        ]);
    }

    public function checkoutShow(Reservation $reservation)
    {
        $reservation->load(['guest', 'room', 'payment']);
        return response()->json($reservation);
    }

    public function reservationShow(Reservation $reservation)
    {
        $reservation->load(['guest', 'room', 'payment']);
        return response()->json($reservation);
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

        $existingGuest = Guest::where('fullname', $validated['fullname'])
            ->where('phone_no', $validated['phone_no'])
            ->first();

        if ($existingGuest && $existingGuest->status === 'blacklisted') {
            return back()->withErrors(['fullname' => 'This guest is banned and cannot be checked in.'])->withInput();
        }

        $room = Room::findOrFail($validated['room_id']);
        $checkIn  = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);

        if (in_array($room->status, ['maintenance', 'cleaning'])) {
            return back()->withErrors(['room_id' => 'That room is currently unavailable (maintenance/cleaning). Pick another.'])->withInput();
        }

        if ($this->roomHasOverlap($room->id, $checkIn, $checkOut)) {
            return back()->withErrors(['room_id' => 'That room is already booked for an overlapping date range. Pick another room or adjust the dates.'])->withInput();
        }

        $nights     = max(1, $checkIn->diffInDays($checkOut));
        $totalPrice = $nights * $room->price_per_night;

        $photoPath = $this->storeIdPhoto($validated['id_photo'] ?? null);

        $guest = Guest::firstOrCreate(
            ['fullname' => $validated['fullname'], 'phone_no' => $validated['phone_no']],
            [
                'id_type'  => $validated['id_type'],
                'id_photo' => $photoPath,
                'status'   => 'active',
            ]
        );

        $guest->update([
            'id_type'  => $validated['id_type'],
            'id_photo' => $photoPath ?? $guest->id_photo,
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

        $res->update([
            'status' => 'checked_out',
            'actual_check_out_date' => now()
        ]);

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

        $existingGuest = Guest::where('fullname', $validated['fullname'])
            ->where('phone_no', $validated['phone_no'])
            ->first();

        if ($existingGuest && $existingGuest->status === 'blacklisted') {
            return back()->withErrors(['fullname' => 'This guest is banned and cannot be reserved for.'])->withInput();
        }

        $room = Room::findOrFail($validated['room_id']);
        $checkIn  = Carbon::parse($validated['check_in_date']);
        $checkOut = Carbon::parse($validated['check_out_date']);

        if (in_array($room->status, ['maintenance', 'cleaning'])) {
            return back()->withErrors(['room_id' => 'That room is currently unavailable (maintenance/cleaning). Pick another.'])->withInput();
        }

        if ($this->roomHasOverlap($room->id, $checkIn, $checkOut)) {
            return back()->withErrors(['room_id' => 'That room is already booked for an overlapping date range. Pick another room or adjust the dates.'])->withInput();
        }

        $guest = Guest::firstOrCreate(
            ['fullname' => $validated['fullname'], 'phone_no' => $validated['phone_no']],
            ['id_type' => 'national_id', 'status' => 'active']
        );

        $nights = max(1, $checkIn->diffInDays($checkOut));
        $total  = $nights * $room->price_per_night;

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
        $query = $request->query('query');
        $res = Reservation::with(['guest', 'room', 'payment'])
            ->whereHas('room', function($q){
                $q->where('status', 'reserved');
            })
            ->whereHas('guest', function($q) use ($query){
                $q->where('fullname', 'LIKE', "%{$query}%")
                  ->orWhere('phone_no', 'LIKE', "%{$query}%");
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

        $photoPath = $this->storeIdPhoto($request->id_photo);

        $res->guest->update([
            'id_type' => $request->id_type,
            'id_photo' => $photoPath,
        ]);

        $res->room->update(['status' => 'booked']);

        return back()->with('success', 'Reservation converted to full Check-in!');
    }

    public function banGuest(Request $request)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $guest = Guest::findOrFail($request->guest_id);
        $guest->update(['status' => 'blacklisted']);

        return back()->with('success', "{$guest->fullname} has been banned.");
    }

    public function unbanGuest(Request $request)
    {
        $request->validate([
            'guest_id' => 'required|exists:guests,id',
            'password' => 'required|string',
        ]);

        if (!Hash::check($request->password, auth()->user()->password)) {
            return back()->withErrors(['password' => 'Incorrect password.']);
        }

        $guest = Guest::findOrFail($request->guest_id);
        $guest->update(['status' => 'active']);

        return back()->with('success', "{$guest->fullname} has been unbanned.");
    }
}