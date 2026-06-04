<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Services\LoyaltyPointService;

/**
 * ProfilController - Menangani halaman profil pelanggan.
 * Fitur: lihat profil, update data diri, ganti foto, ganti password.
 */
class ProfilController extends Controller
{
    /** Tampilkan halaman profil pelanggan */
    public function show(Request $request, LoyaltyPointService $loyaltyService)
    {
        $user = Auth::user()->load('bookings.lapangan');

        // Statistik booking pelanggan untuk CRM
        $stats = [
            'total_booking'   => $user->bookings->count(),
            'booking_selesai' => $user->bookings->where('status', 'selesai')->count(),
            'booking_pending' => $user->bookings->where('status', 'pending')->count(),
            'total_bayar'     => $user->bookings->whereIn('status', ['dipesan', 'selesai'])->sum('total_harga'),
        ];

        // Booking terbaru (5 data)
        $bookingTerbaru = $user->bookings()
            ->with(['lapangan', 'jadwal'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ── DATA LOYALTY ──
        $riwayat  = $loyaltyService->getRiwayatPoin($user, 15);
        $vouchers = $loyaltyService->getVoucherAktif($user);
        $menuTukar = LoyaltyPointService::REDEEM;

        // Hitung berapa poin yang akan kadaluwarsa dalam 30 hari ke depan
        $poinSegera = \App\Models\PointHistory::where('user_id', $user->id)
            ->where('tipe', 'kredit')
            ->where('is_expired', false)
            ->whereBetween('expired_at', [now(), now()->addDays(30)])
            ->sum('jumlah_poin');

        // Hitung tren perolehan poin bulanan untuk 6 bulan terakhir
        $monthlyPoints = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $bulan = $date->month;
            $tahun = $date->year;
            $label = $date->translatedFormat('M Y');
            
            $points = \App\Models\PointHistory::where('user_id', $user->id)
                ->where('tipe', 'kredit')
                ->whereMonth('created_at', $bulan)
                ->whereYear('created_at', $tahun)
                ->sum('jumlah_poin');
                
            $monthlyPoints[] = [
                'label'  => $label,
                'points' => (int) $points
            ];
        }

        // Tentukan tab aktif
        $activeTab = 'profil';
        if ($request->routeIs('loyalty.index') || $request->query('tab') === 'loyalty') {
            $activeTab = 'loyalty';
        }

        return view('pelanggan.profil.show', compact(
            'user', 'stats', 'bookingTerbaru',
            'riwayat', 'vouchers', 'menuTukar', 'poinSegera', 'monthlyPoints', 'activeTab'
        ));
    }

    /** Update data profil pelanggan */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'      => 'required|string|max:100',
            'nomor_hp'  => 'nullable|string|max:20',
            'alamat'    => 'nullable|string|max:255',
            'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password'  => 'nullable|min:6|confirmed',
        ], [
            'name.required'    => 'Nama wajib diisi.',
            'foto_profil.image' => 'File foto harus berupa gambar.',
            'foto_profil.max'  => 'Ukuran foto maksimal 2MB.',
            'password.min'     => 'Password minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Update foto profil jika ada file baru
        if ($request->hasFile('foto_profil')) {
            // Hapus foto lama jika ada
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $path = $request->file('foto_profil')->store('profil', 'public');
            $user->foto_profil = $path;
        }

        // Update data dasar
        $user->name     = $request->name;
        $user->nomor_hp = $request->nomor_hp;
        $user->alamat   = $request->alamat;

        // Ganti password hanya jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        return back()->with('success', 'Profil berhasil diperbarui!');
    }

    /** Tampilkan halaman informasi membership */
    public function membership()
    {
        $user = Auth::user();
        $latestPayment = \App\Models\MembershipPayment::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->first();

        $lapangans = \App\Models\Lapangan::where('status', 'aktif')->get();

        return view('pelanggan.membership.index', compact('user', 'latestPayment', 'lapangans'));
    }

    /** Proses pembayaran membership */
    public function bayarMembership(Request $request)
    {
        $user = Auth::user();

        // Cek apakah user sudah jadi member
        if ($user->isMember()) {
            return back()->with('error', 'Anda sudah terdaftar sebagai member aktif.');
        }

        // Cek apakah ada pembayaran tertunda (menunggu verifikasi)
        $pendingPayment = \App\Models\MembershipPayment::where('user_id', $user->id)
            ->where('status_verifikasi', 'menunggu')
            ->exists();

        if ($pendingPayment) {
            return back()->with('error', 'Anda masih memiliki pembayaran membership yang menunggu verifikasi.');
        }

        // Validasi input
        $request->validate([
            'paket'             => 'required|in:weekday_pagi,weekday_malam,weekend',
            'metode_pembayaran' => 'required|in:qris,tunai',
            'bukti_pembayaran'  => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'lapangan_id'       => 'required|exists:lapangan,id',
            'hari'              => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu,minggu',
            'sesi'              => 'required|string',
        ], [
            'paket.required'             => 'Paket membership wajib dipilih.',
            'paket.in'                   => 'Pilihan paket tidak valid.',
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'metode_pembayaran.in'       => 'Pilihan metode pembayaran tidak valid.',
            'bukti_pembayaran.required'  => 'Bukti pembayaran wajib diunggah.',
            'bukti_pembayaran.image'     => 'File harus berupa gambar.',
            'bukti_pembayaran.mimes'     => 'Format gambar harus jpg, jpeg, atau png.',
            'bukti_pembayaran.max'       => 'Ukuran gambar maksimal 2MB.',
            'lapangan_id.required'       => 'Pilihan lapangan wajib diisi.',
            'lapangan_id.exists'         => 'Lapangan tidak valid.',
            'hari.required'              => 'Hari pilihan wajib diisi.',
            'hari.in'                    => 'Pilihan hari tidak valid.',
            'sesi.required'              => 'Sesi waktu wajib diisi.',
        ]);

        $hari = strtolower($request->hari);
        $isWeekendHari = in_array($hari, ['sabtu', 'minggu']);
        
        if ($request->paket === 'weekend' && !$isWeekendHari) {
            return back()->withInput()->with('error', 'Paket weekend hanya boleh memilih hari Sabtu atau Minggu.');
        }
        
        if (($request->paket === 'weekday_pagi' || $request->paket === 'weekday_malam') && $isWeekendHari) {
            return back()->withInput()->with('error', 'Paket weekday hanya boleh memilih hari Senin sampai Jumat.');
        }

        // Parse sesi (e.g. "07:00-10:00")
        $sesiParts = explode('-', $request->sesi);
        if (count($sesiParts) !== 2) {
            return back()->withInput()->with('error', 'Format sesi tidak valid.');
        }
        $jamMulai = trim($sesiParts[0]);
        $jamSelesai = trim($sesiParts[1]);
        if ($jamSelesai === '24:00') {
            $jamSelesai = '23:59';
        }

        // Hitung jumlah bayar sesuai paket
        $hargaPaket = match ($request->paket) {
            'weekday_pagi'  => 350000,
            'weekday_malam' => 500000,
            'weekend'       => 550000,
        };

        // Unggah bukti pembayaran ke storage/app/public/membership_payments
        $path = $request->file('bukti_pembayaran')->store('membership_payments', 'public');

        // Simpan data pembayaran
        \App\Models\MembershipPayment::create([
            'user_id'           => $user->id,
            'paket'             => $request->paket,
            'jumlah_bayar'      => $hargaPaket,
            'metode_pembayaran' => $request->metode_pembayaran,
            'bukti_pembayaran'  => $path,
            'status_verifikasi' => 'menunggu',
            'hari'              => $hari,
            'jam_mulai'         => $jamMulai,
            'jam_selesai'       => $jamSelesai,
            'lapangan_id'       => $request->lapangan_id,
        ]);

        return back()->with('success', 'Bukti pembayaran berhasil dikirim! Menunggu verifikasi admin.');
    }
}
