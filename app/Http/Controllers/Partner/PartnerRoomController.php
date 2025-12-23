<?php

namespace App\Http\Controllers\Partner;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PartnerRoomController extends Controller
{
    public function create(Hotel $hotel)
    {
        // specific check: partner must own the hotel
        if ($hotel->owner_id !== Auth::id()) {
            abort(403);
        }

        $roomTypes = RoomType::all();
        $amenities = Amenity::whereIn('type', ['room', 'both'])->get();

        return view('partner.rooms.create', compact('hotel', 'roomTypes', 'amenities'));
    }

    public function store(Request $request, Hotel $hotel)
    {
        if ($hotel->owner_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:50|unique:rooms,room_number,NULL,id,hotel_id,' . $hotel->id,
            'price_per_night' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        }

        $validated['hotel_id'] = $hotel->id;
        $validated['is_active'] = true;

        $room = Room::create($validated);

        if ($request->has('amenities')) {
            $room->amenities()->attach($request->amenities);
        }

        // Redirect back to dashboard or allow adding another room?
        // User request: "harusnya mereka juga bisa langsung menambahkan Room Room" (plural)
        // So maybe redirect back to creating another room or a room list?
        // For now, let's redirect to dashboard with success, or offer "Add another"

        if ($request->has('add_another')) {
            return redirect()->route('partner.hotels.rooms.create', $hotel)->with('success', 'Room added! Add another one.');
        }

        return redirect()->route('partner.dashboard')->with('success', 'Room added successfully!');
    }
}
