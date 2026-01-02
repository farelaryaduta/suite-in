<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\BookingRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1',
        ]);

        $hotel = Hotel::findOrFail($request->hotel_id);
        $room = Room::with('roomType')->findOrFail($request->room_id);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);

        // Check availability
        $isAvailable = $this->checkRoomAvailability($room->id, $request->check_in, $request->check_out);
        if (!$isAvailable) {
            return back()->withErrors(['room' => 'Room is not available for selected dates.']);
        }

        $subtotal = $room->price_per_night * $nights;
        $tax = $subtotal * config('booking.tax_rate', 0.10); // Tax goes to platform (admin)
        $serviceCharge = $subtotal * config('booking.service_charge_rate', 0.05); // Service charge goes to partner
        $totalAmount = $subtotal + $tax + $serviceCharge;
        $guests = $request->guests;

        return view('bookings.create', compact('hotel', 'room', 'checkIn', 'checkOut', 'nights', 'guests', 'subtotal', 'tax', 'serviceCharge', 'totalAmount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|exists:hotels,id',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|after_or_equal:today',
            'check_out' => 'required|date|after:check_in',
            'guests' => 'required|integer|min:1|max:20', // Added max limit
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'special_requests' => 'nullable|string|max:1000', // Added max limit
        ]);

        $room = Room::findOrFail($request->room_id);

        // Double check availability
        $isAvailable = $this->checkRoomAvailability($room->id, $request->check_in, $request->check_out);
        if (!$isAvailable) {
            return back()->withErrors(['room' => 'Room is not available for selected dates.'])->withInput();
        }

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);

        // Validate booking duration
        $maxDays = config('booking.max_booking_days', 30);
        if ($nights > $maxDays) {
            return back()->withErrors(['check_out' => "Maximum booking duration is {$maxDays} days."])->withInput();
        }

        $subtotal = $room->price_per_night * $nights;
        $tax = $subtotal * config('booking.tax_rate', 0.10); // Tax goes to platform (admin)
        $serviceCharge = $subtotal * config('booking.service_charge_rate', 0.05); // Service charge goes to partner
        $totalAmount = $subtotal + $tax + $serviceCharge;

        $booking = Booking::create([
            'user_id' => Auth::id(),
            'hotel_id' => $request->hotel_id,
            'booking_number' => 'BK' . strtoupper(Str::random(10)),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'nights' => $nights,
            'guests' => $request->guests,
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
            'guest_phone' => $request->guest_phone,
            'special_requests' => $request->special_requests,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'service_charge' => $serviceCharge,
            'total_amount' => $totalAmount,
            'status' => 'pending',
        ]);

        BookingRoom::create([
            'booking_id' => $booking->id,
            'room_id' => $room->id,
            'quantity' => 1,
            'price_per_night' => $room->price_per_night,
            'total_price' => $subtotal,
        ]);

        return redirect()->route('bookings.payment', $booking->id);
    }

    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['hotel', 'rooms.room', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function show($id)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->with(['hotel', 'rooms.room.roomType', 'payment', 'review'])
            ->findOrFail($id);

        return view('bookings.show', compact('booking'));
    }

    private function checkRoomAvailability($roomId, $checkIn, $checkOut)
    {
        $bookings = Booking::where('status', '!=', 'cancelled')
            ->whereHas('rooms', function ($query) use ($roomId) {
                $query->where('room_id', $roomId);
            })
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                    ->orWhereBetween('check_out', [$checkIn, $checkOut])
                    ->orWhere(function ($q) use ($checkIn, $checkOut) {
                        $q->where('check_in', '<=', $checkIn)
                            ->where('check_out', '>=', $checkOut);
                    });
            })
            ->count();

        return $bookings === 0;
    }
}
