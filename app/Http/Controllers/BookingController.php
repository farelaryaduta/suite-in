<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\BookingRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
        
        // Fix #13: Check hotel status before allowing booking
        if ($hotel->status !== 'active') {
            return back()->withErrors(['hotel' => 'This hotel is currently not accepting bookings.'])->withInput();
        }
        
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
            'guests' => 'required|integer|min:1|max:' . config('booking.max_guests', 20),
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'required|string|max:20',
            'special_requests' => 'nullable|string|max:' . config('booking.max_special_requests_length', 1000),
        ]);

        // Fix #3: Use database transaction with locking to prevent race conditions
        try {
            return DB::transaction(function () use ($request) {
                // Lock the room for checking (pessimistic locking)
                $room = Room::where('id', $request->room_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                // Get hotel and check status
                $hotel = Hotel::findOrFail($request->hotel_id);
                
                // Fix #13: Check hotel status before allowing booking
                if ($hotel->status !== 'active') {
                    throw new \Exception('This hotel is currently not accepting bookings.');
                }

                // Double check availability with lock held
                $isAvailable = $this->checkRoomAvailability($room->id, $request->check_in, $request->check_out);
                if (!$isAvailable) {
                    throw new \Exception('Room is not available for selected dates.');
                }

                $checkIn = Carbon::parse($request->check_in);
                $checkOut = Carbon::parse($request->check_out);
                $nights = $checkIn->diffInDays($checkOut);

                // Validate booking duration
                $maxDays = config('booking.max_booking_days', 30);
                if ($nights > $maxDays) {
                    throw new \Exception("Maximum booking duration is {$maxDays} days.");
                }

                $subtotal = $room->price_per_night * $nights;
                $tax = $subtotal * config('booking.tax_rate', 0.10); // Tax goes to platform (admin)
                $serviceCharge = $subtotal * config('booking.service_charge_rate', 0.05); // Service charge goes to partner
                $totalAmount = $subtotal + $tax + $serviceCharge;

                // Fix #9: Generate unique booking number with retry logic
                do {
                    $bookingNumber = 'BK' . strtoupper(Str::random(10));
                } while (Booking::where('booking_number', $bookingNumber)->exists());

                $booking = Booking::create([
                    'user_id' => Auth::id(),
                    'hotel_id' => $request->hotel_id,
                    'booking_number' => $bookingNumber,
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
            });
        } catch (\Exception $e) {
            Log::error('Booking creation failed: ' . $e->getMessage(), [
                'hotel_id' => $request->hotel_id,
                'room_id' => $request->room_id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
            ]);
            
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function index()
    {
        // Fix #6: Add eager loading for roomType to prevent N+1 query
        $bookings = Booking::where('user_id', Auth::id())
            ->with(['hotel', 'rooms.room.roomType', 'payment'])
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
        // Fix #4: Improved date overlap logic
        // Two bookings overlap if: new_check_in < existing_check_out AND new_check_out > existing_check_in
        // This correctly handles all edge cases including same-day check-in/check-out
        $bookings = Booking::where('status', '!=', 'cancelled')
            ->whereHas('rooms', function ($query) use ($roomId) {
                $query->where('room_id', $roomId);
            })
            ->where('check_in', '<', $checkOut)
            ->where('check_out', '>', $checkIn)
            ->count();

        return $bookings === 0;
    }
}
