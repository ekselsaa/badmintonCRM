<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Jadwal;
use App\Models\Booking;
use Carbon\Carbon;

class CleanupPendingJadwal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jadwal:cleanup-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus semua jadwal dan booking pending yang harinya sudah terlewat.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->toDateString();

        // Ambil booking pending di hari yang sudah lewat
        $expiredBookings = Booking::where('status', 'pending')
            ->where('tanggal_booking', '<', $today)
            ->get();

        $canceledBookings = 0;
        $resetJadwals     = 0;

        foreach ($expiredBookings as $booking) {
            // Update status booking menjadi 'dibatalkan' (soft cancel — data tetap ada untuk CRM)
            $booking->update(['status' => 'dibatalkan']);
            $canceledBookings++;

            // Kembalikan jadwal ke 'tersedia' jika tidak ada booking lain yang aktif
            if ($booking->jadwal) {
                $stillActive = Booking::where('jadwal_id', $booking->jadwal_id)
                    ->where('id', '!=', $booking->id)
                    ->whereIn('status', ['pending', 'dikonfirmasi', 'dipesan'])
                    ->exists();

                if (!$stillActive && $booking->jadwal->status === 'pending') {
                    $booking->jadwal->update(['status' => 'tersedia']);
                    $resetJadwals++;
                }
            }
        }

        // ─── CLEANUP-2: Bersihkan jadwal lama tanpa booking ──────────────────
        $oldTersediaDeleted = Jadwal::where('status', 'tersedia')
            ->where('tanggal', '<', Carbon::today()->subDays(30)->toDateString())
            ->whereDoesntHave('bookings')
            ->delete();

        $oldDitutupDeleted = Jadwal::where('status', 'ditutup')
            ->where('tanggal', '<', $today)
            ->whereDoesntHave('bookings')
            ->delete();

        $this->info("Berhasil membatalkan {$canceledBookings} booking expired dan mereset {$resetJadwals} jadwal ke tersedia.");
        $this->info("Membersihkan {$oldTersediaDeleted} jadwal 'tersedia' lama (>30 hari) dan {$oldDitutupDeleted} jadwal 'ditutup' lama.");
    }
}
