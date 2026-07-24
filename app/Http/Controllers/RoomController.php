<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\AdminAction;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with('roomType')->orderBy('room_number')->get();
        $roomTypes = RoomType::all();

        return view('admin.rooms', compact('rooms', 'roomTypes'));
    }

    public function batchStore(Request $request)
    {
        $validated = $request->validate([
            'floor_number'     => 'nullable|integer',
            'start_room_no'    => 'required|integer',
            'end_room_no'      => 'required|integer|gte:start_room_no',
            'room_type_id'     => 'required|exists:room_types,id',
            'price_per_night'  => 'required|numeric|min:0',
        ]);

        $created = 0;
        $skipped = [];

        for ($num = $validated['start_room_no']; $num <= $validated['end_room_no']; $num++) {
            $roomNumber = (string) $num;

            if (Room::where('room_number', $roomNumber)->exists()) {
                $skipped[] = $roomNumber;
                continue;
            }

            Room::create([
                'room_number'     => $roomNumber,
                'floor_number'    => $validated['floor_number'] ?? null,
                'room_type_id'    => $validated['room_type_id'],
                'price_per_night' => $validated['price_per_night'],
                'status'          => 'available',
            ]);

            $created++;
        }

        AdminAction::create([
            'user_id'     => auth()->id(),
            'action_type' => 'created_rooms',
            'status'      => "$created rooms created" . (count($skipped) ? ', skipped existing: ' . implode(',', $skipped) : ''),
        ]);

        return redirect()->route('admin.dashboard', [
            'panel'       => 'rooms',
            'rooms_page'  => $request->input('return_page', 1),
            'room_q'      => $request->input('return_q'),
            'room_status' => $request->input('return_status'),
        ])->with('success', "$created rooms created." . (count($skipped) ? ' Already existed: ' . implode(', ', $skipped) : ''));
    }

    // Bulk price update: every room, or every room of one type
    public function bulkPriceUpdate(Request $request)
    {
        $validated = $request->validate([
            'scope'           => 'required|in:all,type',
            'room_type_id'    => 'nullable|required_if:scope,type|exists:room_types,id',
            'price_per_night' => 'required|numeric|min:0',
        ]);

        $query = Room::query();
        if ($validated['scope'] === 'type') {
            $query->where('room_type_id', $validated['room_type_id']);
        }
        $count = $query->update(['price_per_night' => $validated['price_per_night']]);

        AdminAction::create([
            'user_id'     => auth()->id(),
            'action_type' => 'edited_rooms',
            'status'      => "Bulk price update: {$count} room(s) set to {$validated['price_per_night']}",
        ]);

        return redirect()->route('admin.dashboard', [
            'panel'       => 'rooms',
            'rooms_page'  => $request->input('return_page', 1),
            'room_q'      => $request->input('return_q'),
            'room_status' => $request->input('return_status'),
        ])->with('success', "{$count} room(s) updated.");
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_type_id'    => 'required|exists:room_types,id',
            'price_per_night' => 'required|numeric|min:0',
            'status'          => 'required|in:available,booked,reserved,maintenance,cleaning',
        ]);

        $room->update($validated);

        AdminAction::create([
            'user_id'     => auth()->id(),
            'action_type' => 'edited_rooms',
            'status'      => "Room {$room->room_number} updated",
        ]);

        return redirect()->route('admin.dashboard', [
            'panel'       => 'rooms',
            'rooms_page'  => $request->input('return_page', 1),
            'room_q'      => $request->input('return_q'),
            'room_status' => $request->input('return_status'),
        ])->with('success', 'Room updated.');
    }

    public function destroy(Request $request, Room $room)
    {
        $room->delete();

        return redirect()->route('admin.dashboard', [
            'panel'       => 'rooms',
            'rooms_page'  => $request->input('return_page', 1),
            'room_q'      => $request->input('return_q'),
            'room_status' => $request->input('return_status'),
        ])->with('success', 'Room deleted.');
    }
}