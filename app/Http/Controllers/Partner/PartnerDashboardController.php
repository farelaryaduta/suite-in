<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerDashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::guard('partner')->id();
        
        // Get partner's hotels
        $hotels = Hotel::where('owner_id', $userId)->with('rooms')->latest()->get();
        $hotelIds = $hotels->pluck('id');
        
        // Get booking statistics by status
        $bookingStats = [
            'total' => Booking::whereIn('hotel_id', $hotelIds)->count(),
            'pending' => Booking::whereIn('hotel_id', $hotelIds)->where('status', 'pending')->count(),
            'confirmed' => Booking::whereIn('hotel_id', $hotelIds)->where('status', 'confirmed')->count(),
            'completed' => Booking::whereIn('hotel_id', $hotelIds)->where('status', 'completed')->count(),
            'cancelled' => Booking::whereIn('hotel_id', $hotelIds)->where('status', 'cancelled')->count(),
        ];
        
        // ACTION ITEMS: Pending bookings that need attention
        $pendingBookings = Booking::whereIn('hotel_id', $hotelIds)
            ->where('status', 'pending')
            ->count();
        
        // ACTION ITEMS: Hotels pending approval
        $pendingHotels = Hotel::where('owner_id', $userId)
            ->where('status', 'pending')
            ->count();
        
        // Partner revenue = subtotal + service_charge (tax goes to platform)
        // Only count confirmed and completed bookings
        $partnerRevenue = Booking::whereIn('hotel_id', $hotelIds)
            ->whereIn('status', ['confirmed', 'completed'])
            ->selectRaw('SUM(subtotal + service_charge) as total')
            ->value('total') ?? 0;
        
        // Get recent bookings for partner's hotels with proper eager loading
        $recentBookings = Booking::whereIn('hotel_id', $hotelIds)
            ->with(['hotel', 'user', 'rooms.room.roomType', 'payment'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('partner.dashboard', compact(
            'hotels', 
            'bookingStats',
            'pendingBookings',
            'pendingHotels',
            'partnerRevenue', 
            'recentBookings'
        ));
    }
}
