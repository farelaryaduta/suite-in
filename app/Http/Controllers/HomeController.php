<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Hotel::where('status', 'active');

        if ($request->has('city') && $request->city) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->has('check_in') && $request->check_in) {
            // Will be used for availability check later
        }

        if ($request->has('check_out') && $request->check_out) {
            // Will be used for availability check later
        }

        $hotels = $query->with('rooms.roomType')
            ->orderBy('rating', 'desc')
            ->paginate(12);

        return view('home', compact('hotels'));
    }
}
