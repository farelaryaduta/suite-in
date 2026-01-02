<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartnerBookingController extends Controller
{
    /**
     * Display list of bookings for partner's hotels
     */
    public function index(Request $request)
    {
        $userId = Auth::guard('partner')->id();
        
        // Get partner's hotel IDs
        $hotelIds = Hotel::where('owner_id', $userId)->pluck('id');

        if ($hotelIds->isEmpty()) {
            return view('partner.bookings.index', [
                'bookings' => collect([]),
                'hotels' => collect([]),
                'stats' => [
                    'total' => 0,
                    'pending' => 0,
                    'confirmed' => 0,
                    'completed' => 0,
                    'cancelled' => 0,
                ]
            ]);
        }

        // Build query
        $query = Booking::whereIn('hotel_id', $hotelIds)
            ->with(['hotel', 'user', 'rooms.room.roomType', 'payment']);

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Filter by hotel
        if ($request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('check_in', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('check_out', '<=', $request->date_to);
        }

        // Search by booking number or guest name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                  ->orWhere('guest_name', 'like', "%{$search}%")
                  ->orWhere('guest_email', 'like', "%{$search}%");
            });
        }

        // Order by latest
        $bookings = $query->latest()->paginate(20);

        // Get statistics
        $stats = [
            'total' => Booking::whereIn('hotel_id', $hotelIds)->count(),
            'pending' => Booking::whereIn('hotel_id', $hotelIds)->where('status', 'pending')->count(),
            'confirmed' => Booking::whereIn('hotel_id', $hotelIds)->where('status', 'confirmed')->count(),
            'completed' => Booking::whereIn('hotel_id', $hotelIds)->where('status', 'completed')->count(),
            'cancelled' => Booking::whereIn('hotel_id', $hotelIds)->where('status', 'cancelled')->count(),
        ];

        // Get hotels for filter dropdown
        $hotels = Hotel::where('owner_id', $userId)->get();

        return view('partner.bookings.index', compact('bookings', 'hotels', 'stats'));
    }

    /**
     * Display booking details
     */
    public function show($id)
    {
        $userId = Auth::guard('partner')->id();
        
        // Get partner's hotel IDs
        $hotelIds = Hotel::where('owner_id', $userId)->pluck('id');

        $booking = Booking::with(['hotel', 'user', 'rooms.room.roomType', 'payment'])
            ->whereIn('hotel_id', $hotelIds)
            ->findOrFail($id);

        return view('partner.bookings.show', compact('booking'));
    }

    /**
     * Update booking status
     */
    public function updateStatus(Request $request, $id)
    {
        $userId = Auth::guard('partner')->id();
        
        // Get partner's hotel IDs
        $hotelIds = Hotel::where('owner_id', $userId)->pluck('id');

        $booking = Booking::whereIn('hotel_id', $hotelIds)->findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:confirmed,completed,cancelled',
            'notes' => 'nullable|string|max:1000',
        ]);

        // Validate status transitions
        $this->validateStatusTransition($booking->status, $validated['status']);

        // Use transaction for status update
        DB::transaction(function () use ($booking, $validated, $request) {
            $oldStatus = $booking->status;
            $booking->status = $validated['status'];
            $booking->save();

            // Log status change
            Log::info('Booking status updated by partner', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
                'partner_id' => Auth::guard('partner')->id(),
                'notes' => $request->notes ?? null,
            ]);

            // If cancelled, update payment status
            if ($validated['status'] === 'cancelled' && $booking->payment) {
                $booking->payment->update([
                    'status' => 'refunded',
                    'notes' => 'Cancelled by partner: ' . ($request->notes ?? 'No reason provided'),
                ]);
            }
        });

        return back()->with('success', 'Booking status updated successfully to ' . $validated['status'] . '!');
    }

    /**
     * Cancel a booking
     */
    public function cancel(Request $request, $id)
    {
        $userId = Auth::guard('partner')->id();
        
        // Get partner's hotel IDs
        $hotelIds = Hotel::where('owner_id', $userId)->pluck('id');

        $booking = Booking::whereIn('hotel_id', $hotelIds)->findOrFail($id);

        $validated = $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        // Check if booking can be cancelled
        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return back()->withErrors([
                'error' => 'Cannot cancel a booking that is already ' . $booking->status . '.'
            ]);
        }

        DB::transaction(function () use ($booking, $validated) {
            $oldStatus = $booking->status;
            $booking->status = 'cancelled';
            $booking->save();

            // Log cancellation
            Log::info('Booking cancelled by partner', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'old_status' => $oldStatus,
                'partner_id' => Auth::guard('partner')->id(),
                'reason' => $validated['cancellation_reason'],
            ]);

            // Update payment if exists
            if ($booking->payment) {
                $booking->payment->update([
                    'status' => 'refunded',
                    'notes' => 'Cancelled by partner. Reason: ' . $validated['cancellation_reason'],
                ]);
            }
        });

        return redirect()->route('partner.bookings.index')
            ->with('success', 'Booking cancelled successfully. Customer will be notified.');
    }

    /**
     * Validate status transition
     */
    private function validateStatusTransition($currentStatus, $newStatus)
    {
        $allowedTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['completed', 'cancelled'],
            'completed' => [], // Cannot change from completed
            'cancelled' => [], // Cannot change from cancelled
        ];

        if (!isset($allowedTransitions[$currentStatus])) {
            abort(400, 'Invalid current status.');
        }

        if (!in_array($newStatus, $allowedTransitions[$currentStatus])) {
            abort(400, "Cannot transition from {$currentStatus} to {$newStatus}.");
        }
    }
}
