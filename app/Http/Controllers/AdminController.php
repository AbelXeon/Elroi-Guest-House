<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\AdminAction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
   public function dashboard()
    {
        $rooms = Room::with('roomType')->orderBy('room_number')->get();
        $roomTypes = RoomType::all();
        $staffCount = User::where('role', 'staff')->count();
        $roomStats = [
            'total'       => $rooms->count(),
            'available'   => $rooms->where('status', 'available')->count(),
            'booked'      => $rooms->where('status', 'booked')->count(),
            'maintenance' => $rooms->where('status', 'maintenance')->count(),
        ];

        return view('admin.dashboard', compact('rooms', 'roomTypes', 'staffCount', 'roomStats'));
    }
}