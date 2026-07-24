<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\AdminAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        $rooms = Room::with('roomType')->orderBy('room_number')->get();
        $roomTypes = RoomType::all();
        $staff = User::where('role', 'staff')->orderBy('fullname')->get();
        $staffCount = $staff->count();
        $roomStats = [
            'total'       => $rooms->count(),
            'available'   => $rooms->where('status', 'available')->count(),
            'booked'      => $rooms->where('status', 'booked')->count(),
            'maintenance' => $rooms->where('status', 'maintenance')->count(),
        ];

        return view('admin.dashboard', compact('rooms', 'roomTypes', 'staff', 'staffCount', 'roomStats'));
    }

    public function staffStore(Request $request)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|string|min:6',
        ]);

        User::create([
            'fullname' => $validated['fullname'],
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'role'     => 'staff',
        ]);

        AdminAction::create([
            'user_id'     => auth()->id(),
            'action_type' => 'create_staff',
            'status'      => "Staff '{$validated['fullname']}' created",
        ]);

        return back()->with('success', 'Staff member created.');
    }

    public function staffDestroy(User $staff)
    {
        if ($staff->role !== 'staff') {
            return back()->withErrors(['staff' => 'Cannot delete a non-staff user.']);
        }

        $name = $staff->fullname;
        $staff->delete();

        AdminAction::create([
            'user_id'     => auth()->id(),
            'action_type' => 'remove_staff',
            'status'      => "Staff '$name' removed",
        ]);

        return back()->with('success', 'Staff member removed.');
    }
}