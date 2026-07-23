<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
      public function index()
    {
        $guests = Guest::latest()->get();
        return view('guests.index', compact('guests'));
    }

    public function create()
    {
        return view('guests.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'id_type'   => 'required|string',
            'id_number' => 'required|string',
            'phone_no'  => 'required|string',
            'id_photo'  => 'nullable|string', // Base64 string from Webcam
            'address'   => 'nullable|string',
        ]);

        Guest::create([
            'full_name' => $request->full_name,
            'id_type'   => $request->id_type,
            'id_number' => $request->id_number,
            'phone_no'  => $request->phone_no,
            'id_photo'  => $request->id_photo,
            'address'   => $request->address,
            'status'    => 'active',
        ]);

        return redirect()->route('staff.dashboard')->with('success', 'Guest registered successfully!');
    }
}
