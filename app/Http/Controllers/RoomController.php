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

    public function roomsData()
    {
        $rooms = Room::with('roomType')->orderBy('room_number')->get();

        $data = $rooms->map(function ($room) {
            return [
                'id'              => $room->id,
                'room_number'     => $room->room_number,
                'floor_number'    => $room->floor_number ?? '—',
                'room_type_id'    => $room->room_type_id,
                'room_type'       => $room->roomType->name ?? '—',
                'price_per_night' => number_format($room->price_per_night, 2),
                'price_raw'       => $room->price_per_night,
                'status'          => $room->status,
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function batchStore(Request $request)
    {
        $validated = $request->validate([
            'room_type_name'    => 'required|string|max:255',
            'start_room_number' => 'required|string|max:50',
            'count'             => 'required|integer|min:1|max:500',
            'price_per_night'   => 'required|numeric|min:0',
        ]);

        if (!preg_match('/^([A-Za-z\-]*)(\d+)$/', $validated['start_room_number'], $m)) {
            return back()->withErrors(['start_room_number' => 'Room number must end with digits, e.g. G001 or F101.'])->withInput();
        }

        $prefix     = $m[1];
        $numberPart = $m[2];
        $width      = strlen($numberPart);
        $startNum   = (int) $numberPart;

        $roomType = RoomType::firstOrCreate(['name' => trim($validated['room_type_name'])]);

        $created = 0;
        $skipped = [];

        for ($i = 0; $i < $validated['count']; $i++) {
            $num        = $startNum + $i;
            $padded     = str_pad($num, $width, '0', STR_PAD_LEFT);
            $roomNumber = $prefix . $padded;

            if (Room::where('room_number', $roomNumber)->exists()) {
                $skipped[] = $roomNumber;
                continue;
            }

            Room::create([
                'room_number'     => $roomNumber,
                'room_type_id'    => $roomType->id,
                'price_per_night' => $validated['price_per_night'],
                'status'          => 'available',
            ]);

            $created++;
        }

        AdminAction::create([
            'user_id'     => auth()->id(),
            'action_type' => 'created_rooms',
            'status'      => "$created rooms created" . (count($skipped) ? ', skipped existing: ' . implode(', ', $skipped) : ''),
        ]);

        return redirect()->route('admin.dashboard', ['panel' => 'rooms'])
            ->with('success', "$created rooms created." . (count($skipped) ? ' Already existed: ' . implode(', ', $skipped) : ''));
    }

    // Bulk price update — by room type only
    public function bulkPriceUpdate(Request $request)
    {
        $validated = $request->validate([
            'room_type_id'    => 'required|exists:room_types,id',
            'price_per_night' => 'required|numeric|min:0',
        ]);

        $count = Room::where('room_type_id', $validated['room_type_id'])
            ->update(['price_per_night' => $validated['price_per_night']]);

        AdminAction::create([
            'user_id'     => auth()->id(),
            'action_type' => 'edited_rooms',
            'status'      => "Bulk price update: {$count} room(s) set to {$validated['price_per_night']}",
        ]);

        return redirect()->route('admin.dashboard', ['panel' => 'rooms'])->with('success', "{$count} room(s) updated.");
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'room_number'     => 'required|string|max:50|unique:rooms,room_number,' . $room->id,
            'room_type_name'  => 'required|string|max:255',
            'price_per_night' => 'required|numeric|min:0',
            'status'          => 'required|in:available,booked,reserved,maintenance,cleaning',
        ]);

        $roomType = RoomType::firstOrCreate(['name' => trim($validated['room_type_name'])]);

        $room->update([
            'room_number'     => $validated['room_number'],
            'room_type_id'    => $roomType->id,
            'price_per_night' => $validated['price_per_night'],
            'status'          => $validated['status'],
        ]);

        AdminAction::create([
            'user_id'     => auth()->id(),
            'action_type' => 'edited_rooms',
            'status'      => "Room {$room->room_number} updated",
        ]);

        return redirect()->route('admin.dashboard', ['panel' => 'rooms'])->with('success', 'Room updated.');
    }

    public function destroy(Room $room)
    {
        $room->delete();
        return redirect()->route('admin.dashboard', ['panel' => 'rooms'])->with('success', 'Room deleted.');
    }
}