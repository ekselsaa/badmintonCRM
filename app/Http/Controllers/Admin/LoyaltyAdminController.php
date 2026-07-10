<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PointHistory;
use App\Models\Redemption;
use App\Services\LoyaltyPointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * LoyaltyAdminController
 *
 * Menangani halaman admin untuk mengelola program loyalty:
 *  - Dashboard: ringkasan poin pelanggan & segmentasi
 *  - Kredit poin manual untuk paket member
 *  - Klaim voucher pelanggan di meja kasir
 */
class LoyaltyAdminController extends Controller
{
    public function __construct(private LoyaltyPointService $loyaltyService) {}

    // Dashboard Loyalty Admin

    /**
     * Tampilkan ringkasan program loyalty dan daftar pelanggan beserta poin.
     */
    public function index(Request $request)
    {
        // Ringkasan statistik
        $stats = [
            'total_poin_beredar' => User::where('role', 'pelanggan')->sum('poin_saldo'),
            'total_redemption'   => Redemption::count() + \App\Models\Voucher::count(),
            'voucher_aktif'      => Redemption::where('status', 'aktif')->count() + \App\Models\Voucher::where('status', 'aktif')->count(),
            'segmen' => [
                'visitor'  => User::where('role', 'pelanggan')->where('segmen_pelanggan', 'visitor')->count(),
                'ally'     => User::where('role', 'pelanggan')->where('segmen_pelanggan', 'ally')->count(),
                'partner'  => User::where('role', 'pelanggan')->where('segmen_pelanggan', 'partner')->count(),
                'loyalist' => User::where('role', 'pelanggan')->where('segmen_pelanggan', 'loyalist')->count(),
                'vip'      => User::where('role', 'pelanggan')->where('segmen_pelanggan', 'vip')->count(),
            ],
        ];

        // Daftar pelanggan dengan poin (bisa filter by segmen)
        $query = User::where('role', 'pelanggan')
            ->orderBy('poin_saldo', 'desc');

        if ($request->segmen) {
            $query->where('segmen_pelanggan', $request->segmen);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }

        $pelanggan   = $query->paginate(20);
        $menuRedeem  = LoyaltyPointService::REDEEM;
        $jenisPaket  = ['pagi_siang', 'malam', 'weekend'];

        return view('admin.loyalty.index', compact('stats', 'pelanggan', 'menuRedeem', 'jenisPaket'));
    }

    // Kredit Poin Paket Member (Manual Admin)

    /**
     * Admin mengkreditkan poin saat mendaftarkan/memperpanjang paket member.
     */
    public function kreditMember(Request $request)
    {
        $request->validate([
            'user_id'     => 'required|exists:users,id',
            'jenis_paket' => 'required|in:pagi_siang,malam,weekend',
        ]);

        $user = User::findOrFail($request->user_id);

        try {
            $poin = $this->loyaltyService->kreditPoinPaketMember($user, $request->jenis_paket);

            $labelPaket = [
                'pagi_siang' => 'Weekdays Pagi/Siang',
                'malam'      => 'Weekdays Malam',
                'weekend'    => 'Weekend',
            ][$request->jenis_paket];

            return back()->with('success',
                "✅ Berhasil! +{$poin} poin dikreditkan ke <strong>{$user->name}</strong> untuk Paket Member {$labelPaket}."
            );
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Klaim Voucher di Kasir

    /**
     * Admin memvalidasi dan menandai voucher sebagai "digunakan"
     * saat pelanggan menyerahkan kode voucher di meja kasir.
     */
    public function klaimVoucher(Request $request)
    {
        $request->validate([
            'kode_voucher' => 'required|string',
        ], [
            'kode_voucher.required' => 'Kode voucher wajib diisi.',
        ]);

        $kode = trim($request->kode_voucher);
        $kodeLower = strtolower($kode);
        $kodeUpper = strtoupper($kode);

        // 1. Cari di Redemption (Voucher penukaran poin)
        $voucher = Redemption::with('user')
            ->where(function ($q) use ($kode, $kodeLower, $kodeUpper) {
                if (strlen($kode) === 8) {
                    $q->where('kode_voucher', 'like', $kode . '%');
                } else {
                    $q->whereIn('kode_voucher', [$kode, $kodeLower, $kodeUpper]);
                }
            })
            ->first();

        $isRedemption = true;

        // 2. Jika tidak ditemukan, cari di Voucher (Voucher status keanggotaan)
        if (!$voucher) {
            $voucher = \App\Models\Voucher::with('user')
                ->where(function ($q) use ($kode, $kodeLower, $kodeUpper) {
                    if (strlen($kode) === 8) {
                        $q->where('voucher_code', 'like', $kode . '%');
                    } else {
                        $q->whereIn('voucher_code', [$kode, $kodeLower, $kodeUpper]);
                    }
                })
                ->first();
            $isRedemption = false;
        }

        if (!$voucher) {
            return back()->with('error', '❌ Kode voucher tidak ditemukan.');
        }

        if ($voucher->status !== 'aktif') {
            $statusLabel = $voucher->status === 'digunakan' ? 'sudah digunakan' : 'sudah kadaluwarsa';
            return back()->with('error', "❌ Voucher ini {$statusLabel} pada " . ($voucher->digunakan_pada?->translatedFormat('d F Y H:i') ?? '-') . ".");
        }

        // Cek kadaluwarsa
        $expiredDate = $isRedemption ? $voucher->kode_expired_at : $voucher->expired_date;
        if ($expiredDate && $expiredDate->isPast()) {
            $voucher->update(['status' => 'kadaluwarsa']);
            return back()->with('error', '❌ Voucher sudah kadaluwarsa (expired ' . $expiredDate->translatedFormat('d F Y') . ').');
        }

        // Validasi tambahan untuk VIP Voucher (hanya untuk tipe Voucher)
        if (!$isRedemption && $voucher->tipe_voucher === 'vip') {
            if (!$request->has('is_member_renewal') || !$request->is_member_renewal) {
                return back()->withInput()->with('error', '❌ Voucher VIP (Potongan Rp 100.000) hanya dapat diklaim jika disertai dengan perpanjangan member minimal 1 bulan. Silakan verifikasi.');
            }
        }

        // Tandai sebagai digunakan
        $voucher->update([
            'status'          => 'digunakan',
            'digunakan_pada'  => now(),
        ]);

        $labelHadiah = $voucher->label_hadiah;

        return back()->with('success',
            "✅ Voucher berhasil diklaim! Hadiah: <strong>{$labelHadiah}</strong> untuk pelanggan <strong>{$voucher->user->name}</strong>."
        );
    }
}
