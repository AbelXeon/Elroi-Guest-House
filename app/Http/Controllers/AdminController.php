<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Payment;
use App\Models\AdminAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // ---- Unfiltered data used for stat cards + charts ----
        $allRooms = Room::with('roomType')->get();
        $roomTypes = RoomType::all();
        $allStaff = User::where('role', 'staff')->get();

        $roomStats = [
            'total'       => $allRooms->count(),
            'available'   => $allRooms->where('status', 'available')->count(),
            'booked'      => $allRooms->where('status', 'booked')->count(),
            'reserved'    => $allRooms->where('status', 'reserved')->count(),
            'maintenance' => $allRooms->where('status', 'maintenance')->count(),
            'cleaning'    => $allRooms->where('status', 'cleaning')->count(),
        ];
        $staffCount = $allStaff->count();

        // ---- Income stats ----
        $today = today();
        $incomeStats = [
            'today' => (float) Payment::whereDate('created_at', $today)->sum('amount_paid'),
            'week'  => (float) Payment::where('created_at', '>=', now()->subDays(6)->startOfDay())->sum('amount_paid'),
            'month' => (float) Payment::whereMonth('created_at', $today->month)->whereYear('created_at', $today->year)->sum('amount_paid'),
            'year'  => (float) Payment::whereYear('created_at', $today->year)->sum('amount_paid'),
        ];

        // ---- 7-day income chart ----
        $incomeLabels = [];
        $incomeData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $incomeLabels[] = $date->format('D');
            $incomeData[] = (float) Payment::whereDate('created_at', $date)->sum('amount_paid');
        }

        // ---- Rooms by type chart ----
        $roomsByType = $roomTypes->map(function ($type) use ($allRooms) {
            return [
                'name'  => $type->name,
                'count' => $allRooms->where('room_type_id', $type->id)->count(),
            ];
        });

        // ---- Filtered + paginated ROOMS list ----
        $roomQuery = Room::with('roomType');
        if ($roomQ = $request->query('room_q')) {
            $roomQuery->where('room_number', 'like', "%{$roomQ}%");
        }
        if ($roomStatusFilter = $request->query('room_status')) {
            $roomQuery->where('status', $roomStatusFilter);
        }
        $rooms = $roomQuery->orderBy('room_number')->paginate(12, ['*'], 'rooms_page')->withQueryString();

        // ---- Filtered + paginated STAFF list ----
        $staffQuery = User::where('role', 'staff');
        if ($staffQ = $request->query('staff_q')) {
            $staffQuery->where(function ($q) use ($staffQ) {
                $q->where('fullname', 'like', "%{$staffQ}%")
                  ->orWhere('username', 'like', "%{$staffQ}%");
            });
        }
        $staff = $staffQuery->orderBy('fullname')->paginate(10, ['*'], 'staff_page')->withQueryString();

        return view('admin.dashboard', compact(
            'rooms', 'roomTypes', 'staff', 'staffCount', 'roomStats',
            'incomeStats', 'incomeLabels', 'incomeData', 'roomsByType'
        ));
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

        return redirect()->route('admin.dashboard', [
            'panel'      => 'staff',
            'staff_page' => $request->input('return_page', 1),
            'staff_q'    => $request->input('return_q'),
        ])->with('success', 'Staff member created.');
    }

    public function staffDestroy(Request $request, User $staff)
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

        return redirect()->route('admin.dashboard', [
            'panel'      => 'staff',
            'staff_page' => $request->input('return_page', 1),
            'staff_q'    => $request->input('return_q'),
        ])->with('success', 'Staff member removed.');
    }
}