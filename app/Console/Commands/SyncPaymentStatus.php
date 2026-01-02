<?php

namespace App\Console\Commands;

use App\Models\Payment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncPaymentStatus extends Command
{
    protected $signature = 'payments:sync-status';
    protected $description = 'Sync pending payment statuses with Midtrans API';

    public function handle()
    {
        $pendingPayments = Payment::where('status', 'pending')
            ->whereNotNull('snap_token')
            ->with('booking')
            ->get();

        $this->info("Found {$pendingPayments->count()} pending payments to check...");

        $serverKey = config('midtrans.server_key');
        $baseUrl = config('midtrans.is_production') 
            ? 'https://api.midtrans.com' 
            : 'https://api.sandbox.midtrans.com';

        $updated = 0;
        $failed = 0;

        foreach ($pendingPayments as $payment) {
            $this->line("Checking payment: {$payment->payment_number}");

            try {
                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL => $baseUrl . '/v2/' . $payment->payment_number . '/status',
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
                    $this->error("  Error: $err");
                    $failed++;
                    continue;
                }

                $result = json_decode($response);

                if (!isset($result->transaction_status)) {
                    $this->warn("  No transaction found or not yet paid");
                    continue;
                }

                $transactionStatus = $result->transaction_status;
                $this->line("  Midtrans status: $transactionStatus");

                if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                    $payment->update([
                        'status' => 'completed',
                        'paid_at' => now(),
                        'transaction_id' => $result->transaction_id ?? null
                    ]);
                    $payment->booking->update(['status' => 'confirmed']);
                    $this->info("  ✓ Updated to COMPLETED");
                    $updated++;
                } elseif ($transactionStatus == 'pending') {
                    $payment->update(['status' => 'processing']);
                    $this->line("  → Still processing");
                } elseif (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                    $payment->update(['status' => 'failed']);
                    $payment->booking->update(['status' => 'cancelled']);
                    $this->warn("  ✗ Marked as FAILED");
                    $updated++;
                }

            } catch (\Exception $e) {
                $this->error("  Exception: " . $e->getMessage());
                $failed++;
            }
        }

        $this->newLine();
        $this->info("Sync complete: $updated updated, $failed failed");

        return Command::SUCCESS;
    }
}
