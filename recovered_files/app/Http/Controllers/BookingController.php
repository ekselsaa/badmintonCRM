<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use App\Models\Jadwal;
use App\Models\Booking;
use App\Models\HariLibur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreBookingRequest;

/**
 * BookingController - Menangani semua proses booking lapangan oleh pelanggan.
 * Fitur utama: cek double booking, simpan booking, riwayat booking.
 */
class BookingController extends Controller
{
    // ─── Halaman Utama Booking ────────────────────────────────────
    /**
     * Menampilkan daftar lapangan dan jadwal yang tersedia.
     * Pelanggan bisa memilih lapangan, tanggal, dan jam.
     */
    public function index(Request $request)
    {
        Booking::cancelExpiredGracefully();

        $tanggal   = $request->get('tanggal', date('Y-m-d'));
        $now = now();
        $requestedDate = \Carbon\Carbon::parse($tanggal);
        if ($requestedDate->lt($now->copy()->startOfDay())) {
            $tanggal = $now->toDateString();
        }
        $lapangans = Lapangan::where('status', 'aktif')->get();

        // Ambil jadwal yang sudah DIPESAN pada tanggal yang dipilih agar user tahu jam berapa yang kosong
        $jadwals = Jadwal::with('lapangan')
            ->where('tanggal', $tanggal)
            ->whereIn('status', ['pending', 'dipesan', 'd