@extends('layouts.partner_app')

@section('content')
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold leading-tight text-gray-900">Booking Details</h1>
                <p class="mt-2 text-sm text-gray-600">{{ $booking->booking_number }}</p>
            </div>
            <a href="{{ route('partner.bookings.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                Back to Bookings
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                <p class="ml-3 text-sm text-green-700">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div class="ml-3">
                    @foreach($errors->all() as $error)
                        <p class="text-sm text-red-700">{{ $error }}</p>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Booking Status -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Booking Status</h2>
                <div class="flex items-center justify-between">
                    <div>
                        <span class="px-4 py-2 text-sm font-medium rounded-full
                            @if($booking->status == 'confirmed') bg-green-100 text-green-800
                            @elseif($booking->status == 'pending') bg-yellow-100 text-yellow-800
                            @elseif($booking->status == 'cancelled') bg-red-100 text-red-800
                            @elseif($booking->status == 'completed') bg-purple-100 text-purple-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>

                    @if($booking->status == 'pending')
                        <div class="flex space-x-2">
                            <form action="{{ route('partner.bookings.updateStatus', $booking) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="confirmed">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    Confirm Booking
                                </button>
                            </form>
                            <button onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </button>
                        </div>
                    @elseif($booking->status == 'confirmed')
                        <div class="flex space-x-2">
                            <form action="{{ route('partner.bookings.updateStatus', $booking) }}" method="POST">
                                @csrf
                                <input type="hidden" name="status" value="completed">
                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                    Mark as Completed
                                </button>
                            </form>
                            <button onclick="document.getElementById('cancelModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </button>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Guest Information -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Guest Information</h2>
                <dl class="grid grid-cols-1 gap-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Full Name</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $booking->guest_name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Email</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $booking->guest_email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Phone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $booking->guest_phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Number of Guests</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $booking->number_of_guests }} guest(s)</dd>
                    </div>
                    @if($booking->special_requests)
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Special Requests</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $booking->special_requests }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <!-- Room Details -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Room Details</h2>
                @foreach($booking->rooms as $bookingRoom)
                    <div class="flex items-center justify-between py-3 {{ !$loop->last ? 'border-b border-gray-200' : '' }}">
                        <div class="flex items-center">
                            @if($bookingRoom->room->image)
                                <img class="h-16 w-16 rounded object-cover" src="{{ asset('storage/' . $bookingRoom->room->image) }}" alt="">
                            @else
                                <div class="h-16 w-16 rounded bg-gray-200 flex items-center justify-center">
                                    <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                            @endif
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $bookingRoom->room->roomType->name }}</div>
                                <div class="text-sm text-gray-500">Room {{ $bookingRoom->room->room_number }}</div>
                                <div class="text-xs text-gray-400">{{ $bookingRoom->quantity }} room(s) × {{ $bookingRoom->nights }} night(s)</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-medium text-gray-900">Rp {{ number_format($bookingRoom->subtotal, 0, ',', '.') }}</div>
                            <div class="text-xs text-gray-500">Rp {{ number_format($bookingRoom->price_per_night, 0, ',', '.') }}/night</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Payment Information -->
            @if($booking->payment)
                <div class="bg-white shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Payment Information</h2>
                    <dl class="grid grid-cols-1 gap-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Payment Status</dt>
                            <dd class="mt-1">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $booking->payment->status == 'settlement' ? 'bg-green-100 text-green-800' : 
                                       ($booking->payment->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                       'bg-red-100 text-red-800') }}">
                                    {{ ucfirst($booking->payment->status) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Payment Method</dt>
                            <dd class="mt-1 text-sm text-gray-900">{{ $booking->payment->payment_method }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Transaction ID</dt>
                            <dd class="mt-1 text-sm text-gray-900 font-mono">{{ $booking->payment->transaction_id ?? 'N/A' }}</dd>
                        </div>
                    </dl>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Booking Summary -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Booking Summary</h2>
                <dl class="space-y-3">
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Booking Number</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $booking->booking_number }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Hotel</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $booking->hotel->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Check In</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $booking->check_in->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Check Out</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $booking->check_out->format('M d, Y') }}</dd>
                    </div>
                    <div class="flex justify-between pt-3 border-t border-gray-200">
                        <dt class="text-sm text-gray-600">Subtotal</dt>
                        <dd class="text-sm font-medium text-gray-900">Rp {{ number_format($booking->subtotal, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Tax ({{ config('booking.tax_rate') * 100 }}%)</dt>
                        <dd class="text-sm font-medium text-gray-900">Rp {{ number_format($booking->tax, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-sm text-gray-600">Service Charge ({{ config('booking.service_charge_rate') * 100 }}%)</dt>
                        <dd class="text-sm font-medium text-gray-900">Rp {{ number_format($booking->service_charge, 0, ',', '.') }}</dd>
                    </div>
                    <div class="flex justify-between pt-3 border-t-2 border-gray-900">
                        <dt class="text-base font-semibold text-gray-900">Total Amount</dt>
                        <dd class="text-base font-bold text-gray-900">Rp {{ number_format($booking->total, 0, ',', '.') }}</dd>
                    </div>
                    <div class="pt-3 border-t border-gray-200">
                        <dt class="text-sm font-medium text-green-600 mb-1">Your Revenue</dt>
                        <dd class="text-lg font-bold text-green-600">Rp {{ number_format($booking->subtotal + $booking->service_charge, 0, ',', '.') }}</dd>
                        <p class="text-xs text-gray-500 mt-1">Subtotal + Service Charge<br>(Tax goes to platform)</p>
                    </div>
                </dl>
            </div>

            <!-- Booking Timeline -->
            <div class="bg-white shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 mb-4">Timeline</h2>
                <div class="flow-root">
                    <ul class="-mb-8">
                        <li>
                            <div class="relative pb-8">
                                <div class="relative flex space-x-3">
                                    <div>
                                        <span class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                            <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div>
                                            <p class="text-sm text-gray-900">Booking Created</p>
                                            <p class="text-xs text-gray-500">{{ $booking->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="hidden fixed z-10 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('cancelModal').classList.add('hidden')"></div>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form action="{{ route('partner.bookings.cancel', $booking) }}" method="POST">
                    @csrf
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Cancel Booking</h3>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cancellation Reason *</label>
                            <textarea name="cancellation_reason" rows="4" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                placeholder="Please provide a reason for cancellation..."></textarea>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel Booking
                        </button>
                        <button type="button" onclick="document.getElementById('cancelModal').classList.add('hidden')" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Go Back
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
