<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerDashboardController extends Controller
{
    public function index()
    {
        $hotels = Hotel::where('owner_id', Auth::id())->latest()->get();
        return view('partner.dashboard', compact('hotels'));
    }
}
