<?php

namespace App\Http\Controllers;

use App\Models\Redemption;
use App\Services\LoyaltyPointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * LoyaltyController
 *
 * Menangani semua interaksi pelanggan dengan sistem loyalty points:
 *  - Proses penukaran poin menjadi voucher
 */
class LoyaltyController extends Controller
{
    public function __construct(private LoyaltyPointService $loyaltyService) {}

    // ─── Proses Penukaran Poin ─────────────────────────────────────────

    /**
     * Proses POST permintaan penukaran poin dari pelanggan.
     */
    public function tukarPoin(Request $request)
    {
        $request->validate([
            'jenis_hadiah' => 'required|in:kok_satuan,raket,lapangan_offpeak,voucher_50k,lapangan_peak,voucher_member,anbiyaa_water',
        ], [
            'jenis_hadiah.required' => 'Pilih hadiah yang ingin ditukar.',
            'jenis_hadiah.in'       => 'Pilihan hadiah tidak valid.',
        ]);

        $user = Auth::user();

        try {
            $redemption = $this->loyaltyService->tukarPoin($user, $request->jenis_hadiah);

            $label = LoyaltyPointService::REDEEM[$request->jenis_hadiah]['label'];

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Penukaran poin berhasil!',
                    'data' => [
                        'label'        => $label,
                        'kode_voucher' => $redemption->kode_voucher,
                        'kode_display' => $redemption->kode_display,
                        'icon'         => $redemption->icon_hadiah,
                        'expired_at'   => $redemption->kode_expired_at->translatedFormat('d F Y'),
                    ]
                ]);
            }

            return redirect()->route('loyalty.index')
                ->with('success_redemption', [
                    'id'           => $redemption->id,
                    'label'        => $label,
                    'kode_voucher' => $redemption->kode_voucher,
                    'expired_at'   => $redemption->kode_expired_at->translatedFormat('d F Y'),
                ]);

        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 400);
            }
            return back()->with('error', $e->getMessage());
        }
    }
}
