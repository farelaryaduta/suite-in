<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\Room;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HotelController extends Controller
{
    public function show($id, Request $request)
    {
        $hotel = Hotel::with(['rooms.roomType', 'amenities', 'reviews.user'])
            ->where('status', 'active')
            ->findOrFail($id);

        $checkIn = $request->get('check_in', now()->format('Y-m-d'));
        $checkOut = $request->get('check_out', now()->addDay()->format('Y-m-d'));
        $guests = $request->get('guests', 2);

        // Validate and parse dates
        try {
            $checkIn = Carbon::parse($checkIn)->format('Y-m-d');
            $checkOut = Carbon::parse($checkOut)->format('Y-m-d');
        } catch (\Exception $e) {
            $checkIn = now()->format('Y-m-d');
            $checkOut = now()->addDay()->format('Y-m-d');
        }

        // Ensure check_out is after check_in
        if ($checkOut <= $checkIn) {
            $checkOut = Carbon::parse($checkIn)->addDay()->format('Y-m-d');
        }

        // Get available rooms - check for overlapping bookings
        $bookedRoomIds = Booking::where('hotel_id', $id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->where(function ($q) use ($checkIn, $checkOut) {
                    // Check if booking check_in is between search dates
                    $q->whereBetween('check_in', [$checkIn, $checkOut]);
                })
                ->orWhere(function ($q) use ($checkIn, $checkOut) {
                    // Check if booking check_out is between search dates
                    $q->whereBetween('check_out', [$checkIn, $checkOut]);
                })
                ->orWhere(function ($q) use ($checkIn, $checkOut) {
                    // Check if booking completely covers search dates
                    $q->where('check_in', '<=', $checkIn)
                        ->where('check_out', '>=', $checkOut);
                });
            })
            ->with('rooms')
            ->get()
            ->pluck('rooms')
            ->flatten()
            ->pluck('room_id')
            ->unique();

        $rooms = Room::where('hotel_id', $id)
            ->where('is_active', true)
            ->whereNotIn('id', $bookedRoomIds)
            ->with(['roomType', 'amenities'])
            ->get()
            ->groupBy('room_type_id');

        return view('hotels.show', compact('hotel', 'rooms', 'checkIn', 'checkOut', 'guests'));
    }
}
