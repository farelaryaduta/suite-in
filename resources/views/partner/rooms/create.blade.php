@extends('layouts.partner')

@section('title', 'Add Room - suite.in')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Add Room</h1>
        <p class="text-gray-600 mt-2">Add a room to {{ $hotel->name }}</p>
    </div>

    <div class="bg-white rounded-xl shadow-md p-8">
        <form action="{{ route('partner.hotels.rooms.store', $hotel->id) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Room Type *</label>
                    <select name="room_type_id" required
                        class="w-full px-4 py-2 border {{ $errors->has('room_type_id') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Select Room Type</option>
                        @foreach($roomTypes as $roomType)
                            <option value="{{ $roomType->id }}" {{ old('room_type_id') == $roomType->id ? 'selected' : '' }}>
                                {{ $roomType->name }} ({{ $roomType->max_occupancy }} guests)
                            </option>
                        @endforeach
                    </select>
                    @error('room_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Room Number *</label>
                    <input type="text" name="room_number" value="{{ old('room_number') }}" required
                        class="w-full px-4 py-2 border {{ $errors->has('room_number') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="e.g., 101, 201">
                    @error('room_number')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price per Night (Rp) *</label>
                    <input type="number" name="price_per_night" value="{{ old('price_per_night') }}" required min="0"
                        step="1000"
                        class="w-full px-4 py-2 border {{ $errors->has('price_per_night') ? 'border-red-500' : 'border-gray-300' }} rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('price_per_night')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                    <input type="number" name="quantity" value="{{ old('quantity', 1) }}" required min="1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Room Image</label>
                    <input type="file" name="image" accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Room Amenities</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        @foreach($amenities as $amenity)
                            <label class="flex items-center">
                                <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" {{ in_array($amenity->id, old('amenities', [])) ? 'checked' : '' }}
                                    class="mr-2 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="text-sm text-gray-700">{{ $amenity->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="mt-8 flex items-center justify-end space-x-4">
                <a href="{{ route('partner.dashboard') }}"
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    I'll add rooms later
                </a>
                <button type="submit" name="add_another" value="1"
                    class="bg-gray-100 text-gray-700 px-6 py-2 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                    Save & Add Another
                </button>
                <button type="submit"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    Save & Finish
                </button>
            </div>
        </form>
    </div>
@endsection