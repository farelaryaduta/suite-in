<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function __construct()
    {
        // Set konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    public function show($bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->with(['hotel', 'rooms.room.roomType', 'payment'])
            ->findOrFail($bookingId);

        if ($booking->status !== 'pending') {
            return redirect()->route('bookings.show', $booking->id);
        }

        // Cek apakah sudah ada payment record, jika belum buat baru
        $payment = $booking->payment;
        if (!$payment) {
            $payment = Payment::create([
                'booking_id' => $booking->id,
                'payment_number' => 'PAY' . rand(100000, 999999), // Simple random number
                'amount' => $booking->total_amount,
                'method' => 'midtrans',
                'status' => 'pending',
                'transaction_id' => null,
            ]);
        }

        // Generate Snap Token jika belum ada atau jika status masih pending
        if (empty($payment->snap_token) && $payment->status === 'pending') {
            $params = [
                'transaction_details' => [
                    'order_id' => $payment->payment_number,
                    'gross_amount' => (int) $payment->amount,
                ],
                'customer_details' => [
                    'first_name' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ],
                'item_details' => [
                    [
                        'id' => $booking->id,
                        'price' => (int) $payment->amount,
                        'quantity' => 1,
                        'name' => 'Booking Hotel ' . $booking->hotel->name,
                    ]
                ]
            ];

            $payment->snap_token = Snap::getSnapToken($params);
            $payment->save();
        }

        return view('payments.show', compact('booking', 'payment'));
    }

    // Method untuk menangani Webhook/Notification dari Midtrans
    public function notification(Request $request)
    {
        $payload = $request->getContent();
        $notification = json_decode($payload);

        Log::info('Midtrans Notification received:', (array)$notification);

        $validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . config('midtrans.server_key'));

        if ($notification->signature_key != $validSignatureKey) {
            Log::error('Midtrans Invalid Signature');
            return response(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $notification->transaction_status;
        $paymentNumber = $notification->order_id;

        $payment = Payment::where('payment_number', $paymentNumber)->first();

        if (!$payment) {
            return response(['message' => 'Payment not found'], 404);
        }

        if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
            $payment->update(['status' => 'completed', 'paid_at' => now()]);
            $payment->booking->update(['status' => 'confirmed']);
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $payment->update(['status' => 'failed']);
            $payment->booking->update(['status' => 'cancelled']);
        } else if ($transactionStatus == 'pending') {
            $payment->update(['status' => 'processing']);
        }

        return response(['message' => 'Payment status updated']);
    }
}
