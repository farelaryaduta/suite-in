<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admin')->user();
        
        // Admin sees platform-wide stats
        // Admin revenue = only 10% tax from all confirmed bookings
        $hotels = Hotel::count();
        $bookings = Booking::count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        
        // Platform revenue (tax only - 10% goes to admin/platform)
        $platformRevenue = Booking::where('status', 'confirmed')->sum('tax');
        
        // Total transaction volume (for reference)
        $totalTransactionVolume = Booking::where('status', 'confirmed')->sum('total_amount');
        
        $recentBookings = Booking::with(['hotel', 'user', 'payment'])->latest()->take(10)->get();
        
        // Pending hotels awaiting approval
        $pendingHotels = Hotel::where('status', 'pending')->count();

        return view('admin.dashboard', compact(
            'hotels', 
            'bookings', 
            'confirmedBookings',
            'platformRevenue',
            'totalTransactionVolume',
            'recentBookings',
            'pendingHotels'
        ));
    }
}
