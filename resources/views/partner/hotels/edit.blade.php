@extends('layouts.partner_app')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Edit Property</h1>
        <p class="text-gray-600 mt-2">Update your property details</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form action="{{ route('partner.hotels.update', $hotel->id) }}" method="POST" enctype="multipart/form-data" class="divide-y divide-gray-200">
            @csrf
            @method('PUT')
            
            <!-- Basic Information -->
            <div class="p-8">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Basic Information</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            General details about your property. This information will be displayed to guests.
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2 space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Property Name *</label>
                            <div class="mt-1">
                                <input type="text" name="name" id="name" value="{{ old('name', $hotel->name) }}" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">Description *</label>
                            <div class="mt-1">
                                <textarea id="description" name="description" rows="4" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">{{ old('description', $hotel->description) }}</textarea>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="star_rating" class="block text-sm font-medium text-gray-700">Star Rating *</label>
                                <div class="mt-1">
                                    <select id="star_rating" name="star_rating" required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                        @for($i = 1; $i <= 5; $i++)
                                            <option value="{{ $i }}" {{ old('star_rating', $hotel->star_rating) == $i ? 'selected' : '' }}>{{ $i }} Star{{ $i > 1 ? 's' : '' }}</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="image" class="block text-sm font-medium text-gray-700">Property Image</label>
                                @if($hotel->image)
                                    <div class="mt-2 mb-2">
                                        <img src="{{ asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}" class="h-24 w-24 rounded-lg object-cover border border-gray-200">
                                    </div>
                                @endif
                                <div class="mt-1">
                                    <input type="file" name="image" id="image" accept="image/*"
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location -->
            <div class="p-8">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Location</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Where is your property located?
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2 space-y-6">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700">Address *</label>
                            <div class="mt-1">
                                <input type="text" name="address" id="address" value="{{ old('address', $hotel->address) }}" required
                                    class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-3">
                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700">City *</label>
                                <div class="mt-1">
                                    <input type="text" name="city" id="city" value="{{ old('city', $hotel->city) }}" required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>

                            <div>
                                <label for="province" class="block text-sm font-medium text-gray-700">Province *</label>
                                <div class="mt-1">
                                    <input type="text" name="province" id="province" value="{{ old('province', $hotel->province) }}" required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>

                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code *</label>
                                <div class="mt-1">
                                    <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code', $hotel->postal_code) }}" required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="p-8">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Contact Information</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            How can guests contact the property?
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2 space-y-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-2">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Phone *</label>
                                <div class="mt-1">
                                    <input type="text" name="phone" id="phone" value="{{ old('phone', $hotel->phone) }}" required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email *</label>
                                <div class="mt-1">
                                    <input type="email" name="email" id="email" value="{{ old('email', $hotel->email) }}" required
                                        class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Amenities -->
            <div class="p-8">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Amenities</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Select the amenities available at your property.
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            @foreach($amenities as $amenity)
                                <div class="relative flex items-start">
                                    <div class="flex items-center h-5">
                                        <input id="amenity-{{ $amenity->id }}" name="amenities[]" value="{{ $amenity->id }}" type="checkbox"
                                            class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded"
                                            {{ $hotel->amenities->contains($amenity->id) ? 'checked' : '' }}>
                                    </div>
                                    <div class="ml-3 text-sm">
                                        <label for="amenity-{{ $amenity->id }}" class="font-medium text-gray-700">{{ $amenity->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification Status -->
            @if($hotel->status === 'draft' || $hotel->status === 'rejected')
            <div class="p-8 bg-blue-50">
                <div class="md:grid md:grid-cols-3 md:gap-6">
                    <div class="md:col-span-1">
                        <h3 class="text-lg font-medium leading-6 text-blue-900">Verification</h3>
                        <p class="mt-1 text-sm text-blue-700">
                            Submit your property for review.
                        </p>
                    </div>
                    <div class="mt-5 md:mt-0 md:col-span-2">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="submit_for_review" name="submit_for_review" value="1" type="checkbox"
                                    class="focus:ring-blue-500 h-4 w-4 text-blue-600 border-gray-300 rounded">
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="submit_for_review" class="font-medium text-blue-900">Submit for Verification</label>
                                <p class="text-blue-700">Check this box if you are ready to submit your property for admin review. Once submitted, you cannot edit until approved or rejected.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Form Actions -->
            <div class="px-8 py-5 bg-gray-50 flex justify-end">
                <a href="{{ route('partner.dashboard') }}" class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mr-3">
                    Cancel
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
@endsection
