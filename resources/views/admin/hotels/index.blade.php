@extends('layouts.admin')

@section('title', 'Manage Hotels - suite.in')

@section('content')
<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Manage Hotels</h1>
        <p class="text-gray-600 mt-2">Add, edit, or remove hotels</p>
    </div>
    <a href="{{ route('admin.hotels.create') }}" class="bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
        + Add New Hotel
    </a>
</div>

@if($hotels->count() > 0)
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hotel</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Owner</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($hotels as $hotel)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                @if($hotel->image)
                                    <img src="{{ asset('storage/' . $hotel->image) }}" alt="{{ $hotel->name }}" class="h-12 w-12 rounded-lg object-cover mr-4">
                                @else
                                    <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                        </svg>
                                    </div>
                                @endif
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $hotel->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $hotel->star_rating }} ⭐</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $hotel->city }}</div>
                            <div class="text-sm text-gray-500">{{ $hotel->province }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $hotel->owner->name ?? 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full 
                                {{ $hotel->status === 'active' ? 'bg-green-100 text-green-800' : 
                                   ($hotel->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($hotel->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($hotel->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-3">
                                <a href="{{ route('admin.rooms.index', $hotel->id) }}" class="text-blue-600 hover:text-blue-900">Rooms</a>
                                <a href="{{ route('admin.hotels.edit', $hotel->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                <form action="{{ route('admin.hotels.destroy', $hotel->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $hotels->links() }}
    </div>
@else
    <div class="bg-white rounded-xl shadow-md p-12 text-center">
        <p class="text-gray-600 text-lg mb-4">No hotels found.</p>
        <a href="{{ route('admin.hotels.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-medium hover:bg-blue-700 transition-colors">
            Add Your First Hotel
        </a>
    </div>
@endif
@endsection

