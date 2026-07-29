<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\AdminAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function dashboard(Request $request)
    {
        // ---- Overview stat/chart data ----
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

        $today = today();
        $incomeStats = [
            'today' => (float) Payment::whereDate('created_at', $today)->sum('amount_paid'),
            'week'  => (float) Payment::where('created_at', '>=', now()->subDays(6)->startOfDay())->sum('amount_paid'),
            'month' => (float) Payment::whereMonth('created_at', $today->month)->whereYear('created_at', $today->year)->sum('amount_paid'),
            'year'  => (float) Payment::whereYear('created_at', $today->year)->sum('amount_paid'),
        ];

        $incomeLabels = [];
        $incomeData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = today()->subDays($i);
            $incomeLabels[] = $date->format('D');
            $incomeData[] = (float) Payment::whereDate('created_at', $date)->sum('amount_paid');
        }

        $roomsByType = $roomTypes->map(function ($type) use ($allRooms) {
            return [
                'name'  => $type->name,
                'count' => $allRooms->where('room_type_id', $type->id)->count(),
            ];
        });

        // ---- Recent Activity widget (last 6 reservations) ----
        $recentReservations = Reservation::with(['guest', 'room', 'user', 'payment'])
            ->latest('check_in_date')
            ->take(6)
            ->get();

        return view('admin.dashboard', compact(
            'roomTypes', 'staffCount', 'roomStats',
            'incomeStats', 'incomeLabels', 'incomeData', 'roomsByType',
            'recentReservations'
        ));
    }

    public function staffData()
    {
        $staff = User::where('role', 'staff')->orderBy('fullname')->get(['id', 'fullname', 'username']);
        return response()->json(['data' => $staff]);
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

        return redirect()->route('admin.dashboard', ['panel' => 'staff'])->with('success', 'Staff member created.');
    }

    public function staffUpdate(Request $request, User $staff)
    {
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $staff->id,
            'password' => 'nullable|string|min:6',
        ]);

        $staff->fullname = $validated['fullname'];
        $staff->username = $validated['username'];
        if (!empty($validated['password'])) {
            $staff->password = Hash::make($validated['password']);
        }
        $staff->save();

        AdminAction::create([
            'user_id'     => auth()->id(),
            'action_type' => 'edited_staff',
            'status'      => "Staff '{$staff->fullname}' updated",
        ]);

        return redirect()->route('admin.dashboard', ['panel' => 'staff'])->with('success', 'Staff member updated.');
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

        return redirect()->route('admin.dashboard', ['panel' => 'staff'])->with('success', 'Staff member removed.');
    }

   public function reportsData(Request $request)
{
    $range = $request->query('range', 'today');
    $today = today();

    switch ($range) {
        case 'week':
            $from = now()->subDays(6)->startOfDay();
            $to   = now()->endOfDay();
            break;
        case 'month':
            $from = $today->copy()->startOfMonth();
            $to   = $today->copy()->endOfMonth()->endOfDay();
            break;
        case 'custom':
            $from = $request->query('from') ? Carbon::parse($request->query('from'))->startOfDay() : $today->copy()->startOfDay();
            $to   = $request->query('to') ? Carbon::parse($request->query('to'))->endOfDay() : $today->copy()->endOfDay();
            break;
        default:
            $from = $today->copy()->startOfDay();
            $to   = $today->copy()->endOfDay();
    }

    $reservations = Reservation::with(['guest', 'room', 'user', 'payment'])
        ->whereBetween('check_in_date', [$from, $to])
        ->orderByDesc('check_in_date')
        ->get();

    $rows = $reservations->map(function ($r) {
        $remaining = $r->payment ? (float) $r->payment->remaining_amount : (float) $r->total_price;

        return [
            'guest_name'       => $r->guest->fullname ?? '—',
            'phone'            => $r->guest->phone_no ?? '—',
            'staff'            => $r->user->fullname ?? '—',
            'room'             => $r->room->room_number ?? '—',
            'room_price'       => number_format($r->room->price_per_night ?? 0, 2),
            'check_in'         => optional($r->check_in_date)->format('Y-m-d'),
            'check_out'        => optional($r->check_out_date)->format('Y-m-d'),
            'actual_check_out' => $r->actual_check_out_date ? $r->actual_check_out_date->format('Y-m-d') : '—',
            'total_price'      => number_format($r->total_price, 2),
            'paid'             => $r->payment ? number_format($r->payment->amount_paid, 2) : '0.00',
            'remaining'        => number_format($remaining, 2),
            'payment_status'   => $remaining <= 0 ? 'paid' : 'remaining',
            'status'           => $r->status,
        ];
    });

    return response()->json([
        'data' => $rows,
        'summary' => [
            'count'           => $reservations->count(),
            'total_revenue'   => number_format($reservations->sum('total_price'), 2),
            'total_collected' => number_format($reservations->sum(fn($r) => $r->payment->amount_paid ?? 0), 2),
        ],
    ]);
}
}
