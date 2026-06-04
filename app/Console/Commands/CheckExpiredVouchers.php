<?php

namespace App\Console\Commands;

use App\Models\Voucher;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

/**
 * CheckExpiredVouchers
 *
 * Command untuk mengecek masa aktif voucher di tabel vouchers.
 * Jika expired_date <= NOW() dan status masih 'aktif', ubah menjadi 'kadaluwarsa'.
 *
 * Dijadwalkan: Setiap hari pukul 00:10 WIB
 * Command    : php artisan loyalty:check-expired-vouchers
 */
class CheckExpiredVouchers extends Command
{
    protected $signature   = 'loyalty:check-expired-vouchers';
    protected $description = 'Tandai voucher status keanggotaan yang sudah melewati masa berlaku.';

    public function handle(): int
    {
        $this->info('═══════════════════════════════════════════');
        $this->info('  Loyalty — Check Expired Vouchers        ');
        $this->info('═══════════════════════════════════════════');
        $this->info('Waktu: ' . now()->translatedFormat('d F Y, H:i:s'));

        $expired = Voucher::where('status', 'aktif')
            ->whereNotNull('expired_date')
            ->where('expired_date', '<=', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('✅ Tidak ada voucher yang perlu dikadaluwarsakan.');
            return self::SUCCESS;
        }

        $this->warn("⚠️  Ditemukan {$expired->count()} voucher yang sudah melewati masa berlaku.");

        $count = 0;
        foreach ($expired as $voucher) {
            $voucher->update(['status' => 'kadaluwarsa']);
            $count++;
            $this->line("  → Voucher {$voucher->voucher_code} ({$voucher->tipe_voucher}) — User #{$voucher->user_id}");
        }

        $this->info('');
        $this->info("✅ {$count} voucher ditandai sebagai kadaluwarsa.");

        Log::info("[loyalty:check-expired-vouchers] {$count} voucher dikadaluwarsakan.");

        return self::SUCCESS;
    }
}
