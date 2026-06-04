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
        $menuTukar = LoyaltyPointService::RED