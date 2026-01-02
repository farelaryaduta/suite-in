<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RoomManagementController extends Controller
{
    public function index($hotel)
    {
        $user = Auth::guard('admin')->user();
        
        if ($user->isAdmin()) {
            $hotel = Hotel::findOrFail($hotel);
        } else {
            $hotel = Hotel::where('owner_id', $user->id)->findOrFail($hotel);
        }

        $rooms = Room::where('hotel_id', $hotel->id)
            ->with('roomType')
            ->latest()
            ->paginate(15);

        return view('admin.rooms.index', compact('hotel', 'rooms'));
    }

    public function create($hotel)
    {
        $user = Auth::guard('admin')->user();
        
        if ($user->isAdmin()) {
            $hotel = Hotel::findOrFail($hotel);
        } else {
            $hotel = Hotel::where('owner_id', $user->id)->findOrFail($hotel);
        }

        $roomTypes = RoomType::all();
        $amenities = Amenity::whereIn('type', ['room', 'both'])->get();

        return view('admin.rooms.create', compact('hotel', 'roomTypes', 'amenities'));
    }

    public function store(Request $request, $hotel)
    {
        $user = Auth::guard('admin')->user();
        
        if ($user->isAdmin()) {
            $hotel = Hotel::findOrFail($hotel);
        } else {
            $hotel = Hotel::where('owner_id', $user->id)->findOrFail($hotel);
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

        return redirect()->route('admin.rooms.index', $hotel->id)->with('success', 'Room created successfully!');
    }

    public function edit($hotel, $room)
    {
        $user = Auth::guard('admin')->user();
        
        if ($user->isAdmin()) {
            $hotel = Hotel::findOrFail($hotel);
        } else {
            $hotel = Hotel::where('owner_id', $user->id)->findOrFail($hotel);
        }

        $room = Room::where('hotel_id', $hotel->id)->with('amenities')->findOrFail($room);
        $roomTypes = RoomType::all();
        $amenities = Amenity::whereIn('type', ['room', 'both'])->get();

        return view('admin.rooms.edit', compact('hotel', 'room', 'roomTypes', 'amenities'));
    }

    public function update(Request $request, $hotel, $room)
    {
        $user = Auth::guard('admin')->user();
        
        if ($user->isAdmin()) {
            $hotel = Hotel::findOrFail($hotel);
        } else {
            $hotel = Hotel::where('owner_id', $user->id)->findOrFail($hotel);
        }

        $room = Room::where('hotel_id', $hotel->id)->findOrFail($room);

        $validated = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'room_number' => 'required|string|max:50|unique:rooms,room_number,' . $room->id . ',id,hotel_id,' . $hotel->id,
            'price_per_night' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,id',
        ]);

        if ($request->hasFile('image')) {
            if ($room->image) {
                Storage::disk('public')->delete($room->image);
            }
            $validated['image'] = $request->file('image')->store('rooms', 'public');
        } else {
            unset($validated['image']);
        }

        $room->update($validated);

        if ($request->has('amenities')) {
            $room->amenities()->sync($request->amenities);
        }

        return redirect()->route('admin.rooms.index', $hotel->id)->with('success', 'Room updated successfully!');
    }

    public function destroy($hotel, $room)
    {
        $user = Auth::guard('admin')->user();
        
        if ($user->isAdmin()) {
            $hotel = Hotel::findOrFail($hotel);
        } else {
            $hotel = Hotel::where('owner_id', $user->id)->findOrFail($hotel);
        }

        $room = Room::where('hotel_id', $hotel->id)->findOrFail($room);

        if ($room->image) {
            Storage::disk('public')->delete($room->image);
        }

        $room->delete();

        return redirect()->route('admin.rooms.index', $hotel->id)->with('success', 'Room deleted successfully!');
    }
}
