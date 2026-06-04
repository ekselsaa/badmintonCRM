<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Jadwal;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CancelExpiredBookings extends Command
{
    protected $signature   = 'booking:cancel-expired';
    protected $description = 'Batalkan otomatis booking pending yang sudah melewati batas waktu pembayaran (24 jam)';

    public function handle(): void
    {
        $now = Carbon::now();

        $pendingBookings = Booking::with(['pembayaran', 'jadwal'])
            ->where('status', 'pending')
            ->whereDoesntHave('pembayaran', fn($q) => $q->whereNotNull('bukti_pembayaran'))
            ->get();

        $expired = $pendingBookings->filter(function ($booking) use ($now) {
            $isTunai = $booking->pembayaran && $booking->pembayaran->metode_pembayaran === 'tunai';
            
            if ($isTunai && $booking->jadwal) {
                // Tunai: kedaluwarsa 45 menit sebelum jadwal main
                $jadwalDateTime = Carbon::parse($booking->jadwal->tanggal->format('Y-m-d') . ' ' . $booking->jadwal->jam_mulai);
                $deadline = $jadwalDateTime->subMinutes(45);
                return $now->greaterThanOrEqualTo($deadline);
            } else {
                // QRIS / belum milih: kedaluwarsa 15 menit setelah booking dibuat
                $deadline = $booking->created_at->addMinutes(15);
                return $now->greaterThanOrEqualTo($deadline);
            }
        });

        if ($expired->isEmpty()) {
            $this->info("Tidak ada booking expired yang ditemukan.");
        } else {
            $expiredBookingIds = $expired->pluck('id')->toArray();
            $expiredJadwalIds = $expired->pluck('jadwal_id')->unique()->filter()->toArray();

            // Hapus child data
            \App\Models\BookingFasilitas::whereIn('booking_id', $expiredBookingIds)->delete();
            \App\Models\Pembayaran::whereIn('booking_id', $expiredBookingIds)->delete();
            Booking::whereIn('id', $expiredBookingIds)->delete();

            // Kembalikan slot jadwal menjadi tersedia jika kosong
            if (!empty($expiredJadwalIds)) {
                $jadwalAktifIds = Booking::whereIn('jadwal_id', $expiredJadwalIds)
                    ->whereIn('status', ['pending', 'dikonfirmasi', 'dipesan'])
                    ->pluck('jadwal_id')
                    ->toArray();

                $jadwalAmanIds = array_diff($expiredJadwalIds, $jadwalAktifIds);

                if (!empty($jadwalAmanIds)) {
                    Jadwal::whereIn('id', $jadwalAmanIds)->update(['status' => 'tersedia']);
                }
            }

            $count = count($expiredBookingIds);
            $this->info("✅ {$count} booking expired berhasil dihapus permanen.");
        }

        // ─── OTOMATISASI STATUS SELESAI ───
        // Ubah langsung melalui query untuk performa yang lebih baik (jangan load semua data)
        $todayStr = $now->toDateString();
        $timeStr  = $now->toTimeString();

        // Karena '24:00:00' secara teknis sama dengan besok, kita akan update yang aman saja dari database
        // dan jika ada sisa kita looping ulang seperti sebelumnya tapi dengan filter awal.
        $finishedBookings = Booking::where('status', 'dipesan')
            ->whereHas('jadwal', function ($q) use ($todayStr, $timeStr) {
                $q->where('tanggal', '<', $todayStr)
                  ->orWhere(function ($q2) use ($todayStr, $timeStr) {
                      $q2->where('tanggal', '=', $todayStr)
                         ->where('jam_selesai', '<=', $timeStr)
                         ->where('jam_selesai', '!=', '24:00:00'); 
                  });
            })->get();

        $completedCount = 0;
        
        // Selesaikan secara massal untuk yang tertangkap filter
        if ($finishedBookings->isNotEmpty()) {
            Booking::whereIn('id', $finishedBookings->pluck('id'))->update(['status' => 'selesai']);
            $completedCount += $finishedBookings->count();
        }

        // Pengecekan ekstra untuk jam_selesai = '24:00:00' hari ini
        $midnightBookings = Booking::with('jadwal')
            ->where('status', 'dipesan')
            ->whereHas('jadwal', function($q) use ($todayStr) {
                $q->where('tanggal', '=', $todayStr)
                  ->where('jam_selesai', '24:00:00');
            })->get();

        foreach ($midnightBookings as $b) {
            $jadwalEnd = Carbon::parse($b->jadwal->tanggal->format('Y-m-d') . ' 23:59:59');
            if ($now->greaterThanOrEqualTo($jadwalEnd)) {
                $b->update(['status' => 'selesai']);
                $completedCount++;
            }
        }
        
        if ($completedCount > 0) {
            $this->info("✅ {$completedCount} booking otomatis diselesaikan.");
        }
    }
}
