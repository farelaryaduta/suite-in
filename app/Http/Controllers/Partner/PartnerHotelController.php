<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PartnerHotelController extends Controller
{
    public function index()
    {
        // Redirect to dashboard as dashboard lists hotels
        return redirect()->route('partner.dashboard');
    }

    public function create()
    {
        $amenities = Amenity::whereIn('type', ['hotel', 'both'])->get();
        return view('partner.hotels.create', compact('amenities'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'star_rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|max:2048',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('hotels', 'public');
        }

        $validated['owner_id'] = Auth::id();
        $validated['status'] = 'draft'; // Default to draft
        $validated['rating'] = 0;
        $validated['total_reviews'] = 0;

        $hotel = Hotel::create($validated);

        if ($request->has('amenities')) {
            $hotel->amenities()->attach($request->amenities);
        }

        return redirect()->route('partner.dashboard')->with('success', 'Hotel created successfully! Please submit for verification when ready.');
    }

    public function edit(Hotel $hotel)
    {
        if ($hotel->owner_id !== Auth::id()) {
            abort(403);
        }
        
        $amenities = Amenity::whereIn('type', ['hotel', 'both'])->get();
        return view('partner.hotels.edit', compact('hotel', 'amenities'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        if ($hotel->owner_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|string|max:10',
            'phone' => 'required|string|max:20',
            'email' => 'required|email',
            'star_rating' => 'required|integer|min:1|max:5',
            'image' => 'nullable|image|max:2048',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
            'submit_for_review' => 'nullable|boolean',
        ]);

        if ($request->hasFile('image')) {
            if ($hotel->image) {
                Storage::disk('public')->delete($hotel->image);
            }
            $validated['image'] = $request->file('image')->store('hotels', 'public');
        } else {
            unset($validated['image']);
        }

        if ($request->has('submit_for_review') && $request->submit_for_review) {
            $validated['status'] = 'pending';
        }

        $hotel->update($validated);

        if ($request->has('amenities')) {
            $hotel->amenities()->sync($request->amenities);
        }

        return redirect()->route('partner.dashboard')->with('success', 'Hotel updated successfully!');
    }

    public function destroy(Hotel $hotel)
    {
        if ($hotel->owner_id !== Auth::id()) {
            abort(403);
        }

        if ($hotel->image) {
            Storage::disk('public')->delete($hotel->image);
        }

        $hotel->delete();

        return redirect()->route('partner.dashboard')->with('success', 'Hotel deleted successfully!');
    }
}
