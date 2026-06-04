<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\LoyaltyPointService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ResetMonthlyPoints
 *
 * Command untuk mengecek upgrade status pelanggan di akhir bulan berdasarkan
 * poin bulanan yang diperoleh, mencetak voucher hadiah Sapu Bersih,
 * lalu mereset poin bulanan ke 0 per tanggal 1.
 *
 * Dijadwalkan: Setiap tanggal 1 pukul 00:00 WIB
 * Command    : php artisan loyalty:reset-monthly-points
 */
class ResetMonthlyPoints extends Command
{
    protected $signature   = 'loyalty:reset-monthly-points';
    protected $description = 'Evaluasi segmentasi akhir bulan pelanggan dan reset akumulasi poin bulanan.';

    public function __construct(private LoyaltyPointService $loyaltyService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('══════════════════════════════════════════════════');
        $this->info('   Loyalty Points — Reset Monthly Points & Segment  ');
        $this->info('══════════════════════════════════════════════════');
        $this->info('Waktu: ' . now()->translatedFormat('d F Y, H:i:s'));

        $totalPelanggan = User::where('role', 'pelanggan')->count();
        $this->info("Total pelanggan dievaluasi: {$totalPelanggan}");

        $totalUpgraded = 0;
        $totalError    = 0;

        // Gunakan chunk(100) agar tidak OOM untuk ratusan/ribuan pelanggan.
        // Setiap pelanggan diproses dalam transaksi TERPISAH sehingga
        // kegagalan satu user tidak membatalkan seluruh batch.
        User::where('role', 'pelanggan')
            ->chunk(100, function ($pelanggan) use (&$totalUpgraded, &$totalError) {
                foreach ($pelanggan as $user) {
                    try {
                        DB::transaction(function () use ($user, &$totalUpgraded) {
                            $segmenLama = $user->segmen_pelanggan;

                            // 1. Jalankan upgrade check & reward grant
                            $this->loyaltyService->checkStatusUpgrade($user);

                            $user->refresh();
                            if ($user->segmen_pelanggan !== $segmenLama) {
                                $totalUpgraded++;
                                $this->line("  👤 {$user->name}: [{$segmenLama}] → [{$user->segmen_pelanggan}] (Poin: {$user->poin_bulanan})");
                            }

                            // 2. Reset poin bulanan
                            $user->update(['poin_bulanan' => 0]);
                        });
                    } catch (\Exception $e) {
                        $totalError++;
                        Log::error("[loyalty:reset-monthly-points] Gagal untuk User #{$user->id} ({$user->name}): " . $e->getMessage());
                        $this->warn("  ⚠️  Gagal memproses {$user->name}: " . $e->getMessage());
                    }
                }
            });

        $this->info('');
        $this->info('✅ Proses selesai!');
        $this->info("   Total pelanggan berubah status: {$totalUpgraded}");
        $this->info("   Error/dilewati: {$totalError}");
        $this->info('   Semua akumulasi poin bulanan disetel kembali ke 0.');

        Log::info("[loyalty:reset-monthly-points] Sukses dijalankan. Upgraded: {$totalUpgraded}, Error: {$totalError}.");

        return self::SUCCESS;
    }
}
