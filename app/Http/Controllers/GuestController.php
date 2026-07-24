<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use Illuminate\Http\Request;

class GuestController extends Controller
{
  public function search(Request $request) {
        $query = $request->get('query');
        $guests = Guest::where('fullname', 'LIKE', "%{$query}%")
                       ->orWhere('phone_no', 'LIKE', "%{$query}%")
                       ->get();
        return response()->json($guests);
    }
}
