<?php

namespace App\Console\Commands;

use App\Models\PointHistory;
use App\Models\User;
use App\Services\LoyaltyPointService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * UpdateCustomerSegmentation
 *
 * Command untuk memperbarui segmen pelanggan setiap awal bulan berdasarkan
 * akumulasi poin kredit yang diperoleh pada bulan SEBELUMNYA.
 *
 * Threshold segmen (poin diperoleh dalam 1 bulan):
 *  - elite    : > 200 poin
 *  - regular  : 80 – 200 poin
 *  - passive  : 30 – 79 poin
 *  - inactive : < 30 poin
 *
 * Dijadwalkan: Setiap tanggal 1 pukul 00:05 WIB
 * Command    : php artisan loyalty:update-segmentation
 * Flag       : --bulan=YYYY-MM  (opsional, untuk backfill data historis)
 */
class UpdateCustomerSegmentation extends Command
{
    protected $signature   = 'loyalty:update-segmentation
                              {--bulan= : Bulan target format YYYY-MM (default: bulan lalu)}';
    protected $description = 'Perbarui segmen pelanggan berdasarkan akumulasi poin bulan sebelumnya.';

    public function __construct(private LoyaltyPointService $loyaltyService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('══════════════════════════════════════════════════');
        $this->info('  Loyalty Points — Update Customer Segmentation  ');
        $this->info('══════════════════════════════════════════════════');

        // Tentukan periode yang dievaluasi
        if ($this->option('bulan')) {
            $periodeEval = Carbon::parse($this->option('bulan') . '-01');
        } else {
            $periodeEval = Carbon::now()->subMonth()->startOfMonth();
        }

        $bulan = (int) $periodeEval->format('m');
        $tahun = (int) $periodeEval->format('Y');

        $this->info('Waktu           : ' . now()->translatedFormat('d F Y, H:i:s'));
        $this->info('Periode dievaluasi: ' . $periodeEval->translatedFormat('F Y'));

        // Ambil semua pelanggan (bukan admin)
        $pelanggan = User::where('role', 'pelanggan')->get();
        $this->info("Total pelanggan : {$pelanggan->count()}");
        $this->line('');

        $stats = [
            'vip'      => 0,
            'loyalist' => 0,
            'partner'  => 0,
            'ally'     => 0,
            'visitor'  => 0,
        ];

        $totalBerubah = 0;

        foreach ($pelanggan as $user) {
            // Hitung total poin KREDIT yang diperoleh pada bulan evaluasi
            $poinBulan = (int) PointHistory::where('user_id', $user->id)
                ->where('tipe', 'kredit')
                ->whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->sum('jumlah_poin');

            $segmenBaru = $this->loyaltyService->tentukanSegmen($poinBulan);
            $stats[$segmenBaru]++;

            // Update hanya jika segmen berubah
            if ($user->segmen_pelanggan !== $segmenBaru) {
                $segmenLama = $user->segmen_pelanggan;

                // Cek kenaikan status
                $order = ['visitor' => 1, 'ally' => 2, 'partner' => 3, 'loyalist' => 4, 'vip' => 5];
                $isUpgrade = isset($order[$segmenBaru]) && isset($order[$segmenLama]) && ($order[$segmenBaru] > $order[$segmenLama]);

                $user->update([
                    'segmen_pelanggan'  => $segmenBaru,
                    'segmen_updated_at' => now(),
                ]);

                if ($isUpgrade) {
                    $this->loyaltyService->grantAccumulativeRewards($user, $segmenBaru);
                }

                $totalBerubah++;
                $this->line("  ✏  {$user->name}: [{$segmenLama}] → [{$segmenBaru}] ({$poinBulan} poin)");
            }
        }

        $this->line('');
        $this->info("✅ Segmentasi selesai! ({$totalBerubah} pelanggan berubah segmen)");
        $this->table(
            ['Segmen', 'Threshold', 'Jumlah Pelanggan'],
            [
                ['💎 VIP',      '> 250 poin/bln',   $stats['vip']],
                ['👑 Loyalist', '150-250 poin/bln', $stats['loyalist']],
                ['🏸 Partner',  '80-149 poin/bln',  $stats['partner']],
                ['🤝 Ally',     '30-79 poin/bln',   $stats['ally']],
                ['👤 Visitor',  '0-29 poin/bln',    $stats['visitor']],
            ]
        );

        Log::info("[loyalty:update-segmentation] Periode: {$periodeEval->format('Y-m')}, {$totalBerubah} perubahan.", $stats);

        return self::SUCCESS;
    }
}
