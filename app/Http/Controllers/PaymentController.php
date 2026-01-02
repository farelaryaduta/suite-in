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
                'payment_number' => 'PAY' . strtoupper(Str::random(12)), // Cryptographically secure
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

    /**
     * Check and update payment status manually (called after Midtrans popup success)
     * This is a fallback in case webhook doesn't fire (common in localhost/development)
     */
    public function checkStatus($bookingId)
    {
        $booking = Booking::where('user_id', Auth::id())
            ->with('payment')
            ->findOrFail($bookingId);

        $payment = $booking->payment;

        if (!$payment) {
            return response()->json(['status' => 'error', 'message' => 'Payment not found'], 404);
        }

        // If already completed, just return success
        if ($payment->status === 'completed') {
            return response()->json([
                'status' => 'success',
                'payment_status' => 'completed',
                'booking_status' => $booking->status
            ]);
        }

        try {
            // Check status from Midtrans API
            $serverKey = config('midtrans.server_key');
            $orderId = $payment->payment_number;
            
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => (config('midtrans.is_production') ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com') . '/v2/' . $orderId . '/status',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Basic ' . base64_encode($serverKey . ':')
                ],
            ]);
            
            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                Log::error('Midtrans Status Check Error: ' . $err);
                return response()->json(['status' => 'error', 'message' => 'Failed to check status'], 500);
            }

            $result = json_decode($response);
            Log::info('Midtrans Status Check Result:', (array)$result);

            if (isset($result->transaction_status)) {
                $transactionStatus = $result->transaction_status;

                if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                    $payment->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                        'transaction_id' => $result->transaction_id ?? null
                    ]);
                    $booking->update(['status' => 'confirmed']);
                    
                    return response()->json([
                        'status' => 'success',
                        'payment_status' => 'completed',
                        'booking_status' => 'confirmed'
                    ]);
                } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                    $payment->update(['status' => 'failed']);
                    $booking->update(['status' => 'cancelled']);
                    
                    return response()->json([
                        'status' => 'success',
                        'payment_status' => 'failed',
                        'booking_status' => 'cancelled'
                    ]);
                } else if ($transactionStatus == 'pending') {
                    $payment->update(['status' => 'processing']);
                    
                    return response()->json([
                        'status' => 'success',
                        'payment_status' => 'processing',
                        'booking_status' => 'pending'
                    ]);
                }
            }

            return response()->json([
                'status' => 'success',
                'payment_status' => $payment->status,
                'booking_status' => $booking->status
            ]);

        } catch (\Exception $e) {
            Log::error('Payment Status Check Exception: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
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
