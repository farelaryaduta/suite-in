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
use Illuminate\Support\Facades\Log;

class PartnerRoomController extends Controller
{
    /**
     * Display listing of rooms for a specific hotel
     */
    public function index(Hotel $hotel)
    {
        // Verify ownership
        if ($hotel->owner_id !== Auth::guard('partner')->id()) {
            abort(403, 'Unauthorized access to this hotel.');
        }

        $rooms = $hotel->rooms()
            ->with(['roomType', 'amenities'])
            ->orderBy('room_number')
            ->paginate(20);

        return view('partner.rooms.index', compact('hotel', 'rooms'));
    }

    /**
     * Show form for creating a new room
     */
    public function create(Hotel $hotel)
    {
        // Verify ownership
        if ($hotel->owner_id !== Auth::guard('partner')->id()) {
            abort(403, 'Unauthorized access to this hotel.');
        }

        $roomTypes = RoomType::all();
        $amenities = Amenity::whereIn('type', ['room', 'both'])->get();

        return view('partner.rooms.create', compact('hotel', 'roomTypes', 'amenities'));
    }

    /**
     * Store a newly created room
     */
    public function store(Request $request, Hotel $hotel)
    {
        if ($hotel->owner_id !== Auth::guard('partner')->id()) {
            abort(403, 'Unauthorized access to this hotel.');
        }

        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:50|unique:rooms,room_number,NULL,id,hotel_id,' . $hotel->id,
            'price_per_night' => 'required|numeric|min:0|max:999999999',
            'quantity' => 'required|integer|min:1|max:1000',
            'description' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
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

        if ($request->has('add_another')) {
            return redirect()->route('partner.hotels.rooms.create', $hotel)
                ->with('success', 'Room added successfully! Add another one.');
        }

        return redirect()->route('partner.hotels.rooms.index', $hotel)
            ->with('success', 'Room added successfully!');
    }

    /**
     * Show form for editing a room
     */
    public function edit(Hotel $hotel, Room $room)
    {
        // Verify ownership
        if ($hotel->owner_id !== Auth::guard('partner')->id()) {
            abort(403, 'Unauthorized access to this hotel.');
        }

        // Verify room belongs to hotel
        if ($room->hotel_id !== $hotel->id) {
            abort(404, 'Room not found in this hotel.');
        }

        $roomTypes = RoomType::all();
        $amenities = Amenity::whereIn('type', ['room', 'both'])->get();

        return view('partner.rooms.edit', compact('hotel', 'room', 'roomTypes', 'amenities'));
    }

    /**
     * Update a room
     */
    public function update(Request $request, Hotel $hotel, Room $room)
    {
        // Verify ownership
        if ($hotel->owner_id !== Auth::guard('partner')->id()) {
            abort(403, 'Unauthorized access to this hotel.');
        }

        // Verify room belongs to hotel
        if ($room->hotel_id !== $hotel->id) {
            abort(404, 'Room not found in this hotel.');
        }

        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:50|unique:rooms,room_number,' . $room->id . ',id,hotel_id,' . $hotel->id,
            'price_per_night' => 'required|numeric|min:0|max:999999999',
            'quantity' => 'required|integer|min:1|max:1000',
            'description' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        } else {
            // Keep existing image
            unset($validated['image']);
        }

        $room->update($validated);

        // Sync amenities
        if ($request->has('amenities')) {
            $room->amenities()->sync($request->amenities);
        } else {
            $room->amenities()->detach();
        }

        return redirect()->route('partner.hotels.rooms.index', $hotel)
            ->with('success', 'Room updated successfully!');
    }

    /**
     * Delete a room
     */
    public function destroy(Hotel $hotel, Room $room)
    {
        // Verify ownership
        if ($hotel->owner_id !== Auth::guard('partner')->id()) {
            abort(403, 'Unauthorized access to this hotel.');
        }

        // Verify room belongs to hotel
        if ($room->hotel_id !== $hotel->id) {
            abort(404, 'Room not found in this hotel.');
        }

        // Check if room has active bookings
        $activeBookings = $room->bookingRooms()
            ->whereHas('booking', function ($query) {
                $query->whereIn('status', ['pending', 'confirmed'])
                    ->where('check_out', '>', now());
            })
            ->count();

        if ($activeBookings > 0) {
            return back()->withErrors([
                'error' => 'Cannot delete room with active or upcoming bookings. Please wait until all bookings are completed or cancelled.'
            ]);
        }

        // Delete image from storage
        if ($room->image) {
            Storage::disk('public')->delete($room->image);
        }

        // Detach amenities
        $room->amenities()->detach();

        // Delete room
        $room->delete();

        return redirect()->route('partner.hotels.rooms.index', $hotel)
            ->with('success', 'Room deleted successfully!');
    }

    /**
     * Toggle room active status
     */
    public function toggleActive(Hotel $hotel, Room $room)
    {
        // Verify ownership
        if ($hotel->owner_id !== Auth::guard('partner')->id()) {
            abort(403, 'Unauthorized access to this hotel.');
        }

        // Verify room belongs to hotel
        if ($room->hotel_id !== $hotel->id) {
            abort(404, 'Room not found in this hotel.');
        }

        $room->is_active = !$room->is_active;
        $room->save();

        $status = $room->is_active ? 'activated' : 'deactivated';
        
        return back()->with('success', "Room {$status} successfully!");
    }
}
