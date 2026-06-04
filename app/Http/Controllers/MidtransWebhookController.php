<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = config('midtrans.server_key');
        \Midtrans\Config::$isProduction = config('midtrans.is_production');

        // ─── CRITICAL: Verifikasi Signature Hash Midtrans ──────────────────
        // Mencegah notifikasi pembayaran palsu dari pihak yang tidak berwenang.
        // Formula: SHA-512(order_id + status_code + gross_amount + server_key)
        $input = $request->all();
        if (empty($input['order_id']) || empty($input['status_code']) || empty($input['gross_amount']) || empty($input['signature_key'])) {
            Log::warning('[Midtrans] Request webhook tidak lengkap | IP: ' . $request->ip());
            return response()->json(['message' => 'Incomplete webhook payload'], 400);
        }

        $expectedSignature = hash(
            'sha512',
            $input['order_id'] . $input['status_code'] . $input['gross_amount'] . config('midtrans.server_key')
        );

        if (!hash_equals($expectedSignature, $input['signature_key'])) {
            Log::warning('[Midtrans] Signature tidak valid! Order ID: ' . ($input['order_id'] ?? '-') . ' | IP: ' . $request->ip());
            return response()->json(['message' => 'Invalid signature'], 403);
        }
        // ────────────────────────────────────────────────────────────────────

        try {
            $notification = new \Midtrans\Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid notification: ' . $e->getMessage()], 400);
        }

        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id; // format: BOOK-{id}-{timestamp}

        // Ekstrak ID Booking dari order_id
        $parts = explode('-', $orderId);
        if (count($parts) < 2) {
            return response()->json(['message' => 'Invalid Order ID format'], 400);
        }
        $bookingId = $parts[1];

        // Terapkan atomic lock selama 10 detik untuk booking ini
        $lockKey = "midtrans_webhook_lock_booking_{$bookingId}";
        $lock = Cache::lock($lockKey, 10);

        if (!$lock->get()) {
            Log::warning("Double webhook terdeteksi untuk Booking ID: {$bookingId}. Request diabaikan.");
            return response()->json(['message' => 'Request sedang diproses, silakan tunggu.'], 409);
        }

        try {
            $booking = Booking::find($bookingId);
            if (!$booking) {
                return response()->json(['message' => 'Booking not found'], 404);
            }

            // Cek jika status booking atau pembayaran sudah sesuai untuk menghindari pemrosesan ganda (Idempotent)
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                if ($booking->status === 'dipesan') {
                    return response()->json(['message' => 'Transaksi sudah diselesaikan sebelumnya (Idempotent)'], 200);
                }
            } else if (in_array($transactionStatus, ['cancel', 'deny', 'expire'])) {
                if ($booking->status === 'dibatalkan') {
                    return response()->json(['message' => 'Transaksi sudah dibatalkan sebelumnya (Idempotent)'], 200);
                }
            }

            // Cari atau buat record pembayaran
            $pembayaran = Pembayaran::firstOrNew(['booking_id' => $booking->id]);
            
            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                $pembayaran->jumlah_bayar = $notification->gross_amount;
                $pembayaran->metode_pembayaran = $notification->payment_type == 'qris' ? 'qris' : 'transfer';
                $pembayaran->status_verifikasi = 'diverifikasi';
                $pembayaran->bukti_pembayaran = 'midtrans_auto'; // Dummy path untuk bypass required
                $pembayaran->verified_at = now();
                $pembayaran->catatan_admin = 'Diverifikasi otomatis oleh Midtrans';
                $pembayaran->save();

                // Update status booking menjadi 'dipesan' agar sinkron dengan jadwal
                $booking->status = 'dipesan';
                $booking->save();

                // SINKRONISASI: Update juga status di tabel jadwal terkait
                if ($booking->jadwal) {
                    $booking->jadwal->status = 'dipesan';
                    $booking->jadwal->save();
                }

                // ─── LOYALTY POINTS ──────────────────────────────────────
                $booking->load(['jadwal', 'lapangan', 'bookingFasilitas.fasilitas']);
                if ($booking->user_id) {
                    $loyaltyService = new \App\Services\LoyaltyPointService();
                    $poinDidapat = $loyaltyService->kreditPoinDariBooking($booking);

                    if ($poinDidapat > 0) {
                        $catatanLama = $pembayaran->catatan_admin;
                        $catatanBaru = "[Loyalty +{$poinDidapat} poin → {$booking->user->name}]";
                        $pembayaran->update([
                            'catatan_admin' => $catatanLama
                                ? $catatanLama . "\n" . $catatanBaru
                                : $catatanBaru,
                        ]);
                    }
                }
                // ─────────────────────────────────────────────────────────
            } 
            else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
                $pembayaran->status_verifikasi = 'ditolak';
                $pembayaran->catatan_admin = 'Pembayaran gagal/expired (Midtrans)';
                $pembayaran->save();

                $booking->status = 'dibatalkan'; 
                $booking->save();

                // Free up the schedule if no other bookings are holding it
                if ($booking->jadwal) {
                    $adaBookingAktif = Booking::where('jadwal_id', $booking->jadwal_id)
                        ->where('id', '!=', $booking->id)
                        ->whereIn('status', ['pending', 'dikonfirmasi', 'dipesan'])
                        ->exists();

                    if (!$adaBookingAktif) {
                        $booking->jadwal->status = 'tersedia';
                        $booking->jadwal->save();
                    }
                }
            }

            return response()->json(['message' => 'OK']);
        } finally {
            $lock->release();
        }
    }
}

