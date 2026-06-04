<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\PointHistory;
use App\Models\User;
use App\Services\LoyaltyPointService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * BackfillLoyaltyPoints
 *
 * Command untuk melakukan backfill (penyesuaian retroaktif) poin loyalty
 * pelanggan berdasarkan data booking terverifikasi yang sudah ada sebelum
 * sistem loyalty points ini dipasang.
 *
 * Command : php artisan loyalty:backfill-points
 * Flag    : --dry-run (simulasi tanpa menyimpan)
 */
class BackfillLoyaltyPoints extends Command
{
    protected $signature   = 'loyalty:backfill-points {--dry-run : Hanya simulasi perhitungan tanpa menyimpan ke database}';
    protected $description = 'Kreditkan poin untuk seluruh booking yang sudah terverifikasi dari data yang ada di database.';

    public function __construct(private LoyaltyPointService $loyaltyService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('══════════════════════════════════════════════════');
        $this->info('  Loyalty Points — Backfill Points from Bookings  ');
        $this->info('══════════════════════════════════════════════════');

        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn('⚠️ Mode SIMULASI (Dry Run) aktif. Database tidak akan diubah.');
        }

        // Ambil booking yang berstatus dipesan/selesai, terhubung ke pelanggan,
        // dan status pembayaran sudah diverifikasi.
        $bookings = Booking::whereIn('status', ['dipesan', 'selesai'])
            ->whereNotNull('user_id')
            ->whereHas('pembayaran', function ($q) {
                $q->where('status_verifikasi', 'diverifikasi');
            })
            ->with(['jadwal', 'lapangan', 'bookingFasilitas.fasilitas', 'user'])
            ->orderBy('id', 'asc')
            ->get();

        if ($bookings->isEmpty()) {
            $this->info('✅ Tidak ada data booking terverifikasi yang ditemukan.');
            return self::SUCCESS;
        }

        $this->info('Ditemukan ' . $bookings->count() . ' booking terverifikasi.');
        $this->line('');

        $totalPoinDikreditkan = 0;
        $totalBookingDiproses = 0;
        $usersAffected = collect();

        foreach ($bookings as $booking) {
            // Cek apakah sudah ada kredit poin untuk booking ini
            $sudahAda = PointHistory::where('booking_id', $booking->id)
                ->where('tipe', 'kredit')
                ->exists();

            if ($sudahAda) {
                $this->line("  → Booking #{$booking->id} (User: {$booking->user->name}): [LEWAT] Poin sudah pernah dikreditkan.");
                continue;
            }

            $poinDidapat = 0;

            // Memanfaatkan transaction rollback untuk mode dry-run agar kalkulasinya presisi
            DB::beginTransaction();
            try {
                $poinDidapat = $this->loyaltyService->kreditPoinDariBooking($booking);
                
                if ($dryRun) {
                    DB::rollBack();
                } else {
                    DB::commit();
                }
            } catch (\Exception $e) {
                DB::rollBack();
                $this->error("  → Gagal memproses Booking #{$booking->id}: " . $e->getMessage());
                continue;
            }

            if ($poinDidapat > 0) {
                $totalPoinOriginal = $poinDidapat;
                $totalPoinDikreditkan += $totalPoinOriginal;
                $totalBookingDiproses++;
                $usersAffected->put($booking->user_id, $booking->user);

                $statusText = $dryRun ? '[SIMULASI]' : '[SUKSES]';
                $this->line("  → {$statusText} Booking #{$booking->id} (User: {$booking->user->name}): +{$totalPoinOriginal} poin.");
            } else {
                $this->line("  → Booking #{$booking->id} (User: {$booking->user->name}): +0 poin (di bawah batas minimum Rp 5.000).");
            }
        }

        $this->line('');
        $this->info('✅ Proses selesai!');
        $this->info("   Total booking sukses diproses : {$totalBookingDiproses}");
        $this->info("   Total poin dikreditkan        : {$totalPoinDikreditkan}");
        $this->info("   Total pelanggan terdampak     : " . $usersAffected->count());

        // Jika bukan dry-run, perbarui segmentasi pelanggan agar up-to-date
        if (!$dryRun && $usersAffected->isNotEmpty()) {
            $this->line('');
            $this->info('Memperbarui segmentasi pelanggan terdampak...');
            
            foreach ($usersAffected as $user) {
                // Hitung akumulasi poin bulan lalu
                $bulanLalu = now()->subMonth();
                $poinBulanLalu = (int) PointHistory::where('user_id', $user->id)
                    ->where('tipe', 'kredit')
                    ->whereMonth('created_at', $bulanLalu->month)
                    ->whereYear('created_at', $bulanLalu->year)
                    ->sum('jumlah_poin');

                // Hitung akumulasi poin bulan ini
                $poinBulanIni = (int) PointHistory::where('user_id', $user->id)
                    ->where('tipe', 'kredit')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('jumlah_poin');

                // Gunakan poin terbaik agar backfill segmentasi akurat
                $poinEvaluasi = max($poinBulanLalu, $poinBulanIni);
                $segmenBaru = $this->loyaltyService->tentukanSegmen($poinEvaluasi);

                if ($user->segmen_pelanggan !== $segmenBaru) {
                    $segmenLama = $user->segmen_pelanggan;
                    $user->update([
                        'segmen_pelanggan'  => $segmenBaru,
                        'segmen_updated_at' => now(),
                    ]);
                    $this->line("   ➔ {$user->name}: [{$segmenLama}] ➔ [{$segmenBaru}] (Poin: {$poinEvaluasi})");
                }
            }
            $this->info('Kategori segmentasi seluruh pelanggan terdampak berhasil diperbarui!');
        }

        return self::SUCCESS;
    }
}
