<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class HotelManagementController extends Controller
{
    public function index()
    {
        $user = Auth::guard('admin')->user();

        if ($user->isAdmin()) {
            $hotels = Hotel::with('owner')->latest()->paginate(15);
        } else {
            $hotels = Hotel::where('owner_id', $user->id)->latest()->paginate(15);
        }

        return view('admin.hotels.index', compact('hotels'));
    }

    public function create()
    {
        $amenities = Amenity::whereIn('type', ['hotel', 'both'])->get();
        return view('admin.hotels.create', compact('amenities'));
    }

    public function store(Request $request)
    {
        $user = Auth::guard('admin')->user();

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

        $validated['owner_id'] = $user->isAdmin() && $request->owner_id ? $request->owner_id : $user->id;
        $validated['status'] = $user->isAdmin() ? ($request->status ?? 'active') : 'draft'; // Hotel owner needs admin approval
        $validated['rating'] = 0;
        $validated['total_reviews'] = 0;

        $hotel = Hotel::create($validated);

        if ($request->has('amenities')) {
            $hotel->amenities()->attach($request->amenities);
        }

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel created successfully!');
    }

    public function edit($hotel)
    {
        $user = Auth::guard('admin')->user();

        if ($user->isAdmin()) {
            $hotel = Hotel::with('amenities')->findOrFail($hotel);
        } else {
            $hotel = Hotel::where('owner_id', $user->id)->with('amenities')->findOrFail($hotel);
        }

        $amenities = Amenity::whereIn('type', ['hotel', 'both'])->get();
        return view('admin.hotels.edit', compact('hotel', 'amenities'));
    }

    public function update(Request $request, $hotel)
    {
        $user = Auth::guard('admin')->user();

        if ($user->isAdmin()) {
            $hotel = Hotel::findOrFail($hotel);
        } else {
            $hotel = Hotel::where('owner_id', $user->id)->findOrFail($hotel);
        }

        $rules = [
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
        ];

        if ($user->isAdmin()) {
            $rules['status'] = 'required|in:active,pending,draft,rejected';
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('image')) {
            if ($hotel->image) {
                Storage::disk('public')->delete($hotel->image);
            }
            $validated['image'] = $request->file('image')->store('hotels', 'public');
        } else {
            unset($validated['image']);
        }

        // Status is already in $validated if user is admin
        // if ($user->isAdmin() && $request->has('status')) {
        //    $validated['status'] = $request->status;
        // }

        $hotel->update($validated);

        if ($request->has('amenities')) {
            $hotel->amenities()->sync($request->amenities);
        }

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel updated successfully!');
    }

    public function destroy($hotel)
    {
        $user = Auth::guard('admin')->user();

        if ($user->isAdmin()) {
            $hotel = Hotel::findOrFail($hotel);
        } else {
            $hotel = Hotel::where('owner_id', $user->id)->findOrFail($hotel);
        }

        if ($hotel->image) {
            Storage::disk('public')->delete($hotel->image);
        }

        $hotel->delete();

        return redirect()->route('admin.hotels.index')->with('success', 'Hotel deleted successfully!');
    }
}
