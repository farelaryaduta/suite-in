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
        $userId = Auth::id();
        
        // Get partner's hotels
        $hotels = Hotel::where('owner_id', $userId)->with('rooms')->latest()->get();
        $hotelIds = $hotels->pluck('id');
        
        // Get total bookings for partner's hotels
        $totalBookings = Booking::whereIn('hotel_id', $hotelIds)->count();
        
        // Get confirmed bookings count
        $confirmedBookings = Booking::whereIn('hotel_id', $hotelIds)
            ->where('status', 'confirmed')
            ->count();
        
        // Partner revenue = subtotal + service_charge (tax goes to admin/platform)
        // So partner gets: subtotal + service_charge (excluding 10% tax)
        $partnerRevenue = Booking::whereIn('hotel_id', $hotelIds)
            ->where('status', 'confirmed')
            ->selectRaw('SUM(subtotal + service_charge) as total')
            ->value('total') ?? 0;
        
        // Get recent bookings for partner's hotels
        $recentBookings = Booking::whereIn('hotel_id', $hotelIds)
            ->with(['hotel', 'user', 'rooms.room.roomType'])
            ->latest()
            ->take(10)
            ->get();
        
        return view('partner.dashboard', compact(
            'hotels', 
            'totalBookings', 
            'confirmedBookings',
            'partnerRevenue', 
            'recentBookings'
        ));
    }
}
