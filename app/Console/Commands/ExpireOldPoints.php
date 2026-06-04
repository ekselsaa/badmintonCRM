<?php

namespace App\Console\Commands;

use App\Models\PointHistory;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ExpireOldPoints
 *
 * Command untuk menghanguskan poin kredit yang sudah melewati
 * masa kadaluwarsa (6 bulan sejak diperoleh).
 *
 * Cara kerja:
 * 1. Cari semua entri kredit dengan expired_at <= NOW() yang belum ditandai
 * 2. Tandai is_expired = true pada entri tersebut
 * 3. Catat debit poin dengan sumber 'kadaluwarsa' per user
 * 4. Kurangi saldo poin user (min 0, tidak pernah negatif)
 *
 * Dijadwalkan: Setiap hari pukul 01:00 WIB
 * Command    : php artisan loyalty:expire-points
 */
class ExpireOldPoints extends Command
{
    protected $signature   = 'loyalty:expire-points';
    protected $description = 'Hanguskan poin kredit yang sudah melewati masa kadaluwarsa 6 bulan.';

    public function handle(): int
    {
        $this->info('═══════════════════════════════════════');
        $this->info('  Loyalty Points — Expire Old Points  ');
        $this->info('═══════════════════════════════════════');
        $this->info('Waktu: ' . now()->translatedFormat('d F Y, H:i:s'));

        // 1. Temukan semua entri kredit yang sudah expired, belum ditandai
        $expiredEntries = PointHistory::where('tipe', 'kredit')
            ->where('is_expired', false)
            ->whereNotNull('expired_at')
            ->where('expired_at', '<=', now())
            ->get();

        if ($expiredEntries->isEmpty()) {
            $this->info('✅ Tidak ada poin yang kadaluwarsa hari ini.');
            return self::SUCCESS;
        }

        $this->warn("⚠️  Ditemukan {$expiredEntries->count()} entri poin kadaluwarsa.");

        $perUser = $expiredEntries->groupBy('user_id');

        $totalUserAffected = 0;
        $totalPoinHangus   = 0;

        DB::transaction(function () use ($perUser, &$totalUserAffected, &$totalPoinHangus) {
            foreach ($perUser as $userId => $entries) {
                $user = User::find($userId);
                if (!$user) {
                    continue;
                }

                // Ambil seluruh kredit dari awal untuk simulasi FIFO
                $allCredits = PointHistory::where('user_id', $userId)
                    ->where('tipe', 'kredit')
                    ->orderBy('id', 'asc')
                    ->get();

                // Ambil total debit user dari awal
                $totalDebit = PointHistory::where('user_id', $userId)
                    ->where('tipe', 'debit')
                    ->sum('jumlah_poin');

                $debitRemaining = $totalDebit;
                $totalExpiredToday = 0;
                $creditsToMarkExpired = [];

                foreach ($allCredits as $credit) {
                    if ($debitRemaining >= $credit->jumlah_poin) {
                        $unconsumed = 0;
                        $debitRemaining -= $credit->jumlah_poin;
                    } else {
                        $unconsumed = $credit->jumlah_poin - $debitRemaining;
                        $debitRemaining = 0;
                    }

                    // Jika kredit ini sudah melewati batas waktu dan belum ditandai expired
                    if ($credit->expired_at && $credit->expired_at->isPast() && !$credit->is_expired) {
                        if ($unconsumed > 0) {
                            $totalExpiredToday += $unconsumed;
                        }
                        $creditsToMarkExpired[] = $credit->id;
                    }
                }

                // Tandai kredit sebagai expired di database agar tidak diproses lagi
                if (!empty($creditsToMarkExpired)) {
                    PointHistory::whereIn('id', $creditsToMarkExpired)->update(['is_expired' => true]);
                }

                if ($totalExpiredToday > 0) {
                    // Catat debit poin di histori
                    PointHistory::create([
                        'user_id'     => $userId,
                        'booking_id'  => null,
                        'tipe'        => 'debit',
                        'jumlah_poin' => $totalExpiredToday,
                        'sumber'      => 'kadaluwarsa',
                        'keterangan'  => "Poin kadaluwarsa hangus otomatis (" . count($creditsToMarkExpired) . " batch)",
                        'expired_at'  => null,
                        'is_expired'  => false,
                    ]);

                    // Kurangi saldo user — pastikan tidak negatif
                    $saldobaru = max(0, $user->poin_saldo - $totalExpiredToday);
                    $user->update(['poin_saldo' => $saldobaru]);

                    $totalPoinHangus += $totalExpiredToday;
                    $totalUserAffected++;
                    $this->line("  → {$user->name} (ID #{$userId}): -{$totalExpiredToday} poin (saldo baru: {$saldobaru})");
                } else {
                    $this->line("  → {$user->name} (ID #{$userId}): 0 poin hangus (kredit kadaluwarsa sudah terpakai)");
                }
            }
        });

        $this->info('');
        $this->info("✅ Proses selesai!");
        $this->info("   Total pelanggan terdampak : {$totalUserAffected}");
        $this->info("   Total poin dihanguskan    : {$totalPoinHangus}");

        Log::info("[loyalty:expire-points] Selesai: {$totalUserAffected} user, {$totalPoinHangus} poin dihanguskan.");

        return self::SUCCESS;
    }
}
