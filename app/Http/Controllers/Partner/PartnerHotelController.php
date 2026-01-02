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
        $roomTypes = \App\Models\RoomType::all();
        $roomAmenities = Amenity::whereIn('type', ['room', 'both'])->get();
        return view('partner.hotels.create', compact('amenities', 'roomTypes', 'roomAmenities'));
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
            'rooms' => 'nullable|array',
            'rooms.*.room_type_id' => 'required|exists:room_types,id',
            'rooms.*.room_number' => 'required|string|max:50',
            'rooms.*.price_per_night' => 'required|numeric|min:0',
            'rooms.*.quantity' => 'required|integer|min:1',
            'rooms.*.amenities' => 'nullable|array',
            'rooms.*.amenities.*' => 'exists:amenities,id',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('hotels', 'public');
        }

        $hotel = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $request) {
            // Create Hotel
            $hotelData = collect($validated)->except(['amenities', 'rooms'])->toArray();
            $hotelData['owner_id'] = Auth::guard('partner')->id();
            $hotelData['status'] = 'draft';
            $hotelData['rating'] = 0;
            $hotelData['total_reviews'] = 0;

            $hotel = Hotel::create($hotelData);

            if ($request->has('amenities')) {
                $hotel->amenities()->attach($request->amenities);
            }

            // Create Rooms
            if ($request->has('rooms')) {
                foreach ($request->rooms as $roomData) {
                    $room = new \App\Models\Room($roomData);
                    $room->hotel_id = $hotel->id;
                    $room->is_active = true;
                    $room->save();

                    if (isset($roomData['amenities'])) {
                        $room->amenities()->attach($roomData['amenities']);
                    }
                }
            }

            return $hotel;
        });

        return redirect()->route('partner.dashboard')->with('success', 'Hotel and Rooms created successfully!');
    }

    public function edit(Hotel $hotel)
    {
        if ($hotel->owner_id !== Auth::guard('partner')->id()) {
            abort(403);
        }

        $amenities = Amenity::whereIn('type', ['hotel', 'both'])->get();
        $roomTypes = \App\Models\RoomType::all();
        $roomAmenities = Amenity::whereIn('type', ['room', 'both'])->get();
        return view('partner.hotels.edit', compact('hotel', 'amenities', 'roomTypes', 'roomAmenities'));
    }

    public function update(Request $request, Hotel $hotel)
    {
        if ($hotel->owner_id !== Auth::guard('partner')->id()) {
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
            'rooms' => 'nullable|array',
            'rooms.*.room_type_id' => 'required|exists:room_types,id',
            'rooms.*.room_number' => 'required|string|max:50',
            'rooms.*.price_per_night' => 'required|numeric|min:0',
            'rooms.*.quantity' => 'required|integer|min:1',
            'rooms.*.amenities' => 'nullable|array',
            'rooms.*.amenities.*' => 'exists:amenities,id',
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

        // Create New Rooms
        if ($request->has('rooms')) {
            foreach ($request->rooms as $roomData) {
                $room = new \App\Models\Room($roomData);
                $room->hotel_id = $hotel->id;
                $room->is_active = true;
                $room->save();

                if (isset($roomData['amenities'])) {
                    $room->amenities()->attach($roomData['amenities']);
                }
            }
        }

        return redirect()->route('partner.dashboard')->with('success', 'Hotel updated successfully!');
    }

    public function destroy(Hotel $hotel)
    {
        if ($hotel->owner_id !== Auth::guard('partner')->id()) {
            abort(403);
        }

        if ($hotel->image) {
            Storage::disk('public')->delete($hotel->image);
        }

        $hotel->delete();

        return redirect()->route('partner.dashboard')->with('success', 'Hotel deleted successfully!');
    }
}
