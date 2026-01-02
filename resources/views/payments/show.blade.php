@extends('layouts.app')

@section('title', 'Payment - suite.in')

@section('content')
<!-- Midtrans Snap Script -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-3xl font-bold text-gray-900 mb-8">Payment</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Booking Summary</h2>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Hotel</span>
                        <span class="font-medium">{{ $booking->hotel->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Check In</span>
                        <span class="font-medium">{{ $booking->check_in->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Check Out</span>
                        <span class="font-medium">{{ $booking->check_out->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Nights</span>
                        <span class="font-medium">{{ $booking->nights }} night(s)</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">Payment Details</h2>
                
                <div class="mb-6">
                    <p class="text-gray-600">Total Amount:</p>
                    <p class="text-3xl font-bold text-blue-600">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</p>
                </div>

                <button id="pay-button" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-300">
                    Pay Now
                </button>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-md p-6 sticky top-20">
                <h2 class="text-xl font-semibold mb-4">Price Summary</h2>
                <div class="space-y-2 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-medium">Rp {{ number_format($booking->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax (10%)</span>
                        <span class="font-medium">Rp {{ number_format($booking->tax, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Service Charge (5%)</span>
                        <span class="font-medium">Rp {{ number_format($booking->service_charge, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-lg font-bold border-t pt-2">
                        <span>Total</span>
                        <span class="text-blue-600">Rp {{ number_format($booking->total_amount, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
        window.snap.pay('{{ $payment->snap_token }}', {
            onSuccess: function (result) {
                console.log('Payment Success:', result);
                
                // Show loading message
                payButton.textContent = 'Processing...';
                payButton.disabled = true;
                
                // Call backend to update payment status (fallback for webhook)
                fetch("{{ route('payments.check-status', $booking->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Status check result:', data);
                    // Redirect to booking detail page
                    window.location.href = "{{ route('bookings.show', $booking->id) }}";
                })
                .catch(error => {
                    console.error('Error checking status:', error);
                    // Still redirect even if status check fails (webhook might handle it)
                    window.location.href = "{{ route('bookings.show', $booking->id) }}";
                });
            },
            onPending: function (result) {
                console.log('Payment Pending:', result);
                alert("Waiting for your payment!");
                location.reload();
            },
            onError: function (result) {
                console.error('Payment Error:', result);
                alert("Payment failed!");
                location.reload();
            },
            onClose: function () {
                alert('You closed the popup without finishing the payment');
            }
        });
    });
</script>
@endsection

