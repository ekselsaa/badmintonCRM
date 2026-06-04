<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use App\Models\Jadwal;
use Illuminate\Http\Request;

/**
 * JadwalPublicController - Menampilkan jadwal lapangan SECARA PUBLIK.
 *
 * Perbedaan dengan BookingController:
 * - Tidak memerlukan login untuk MELIHAT jadwal
 * - Hanya booking yang memerlukan login (dihandle di BookingController)
 */
class JadwalPublicController extends Controller
{
    /**
     * Halaman jadwal publik (/jadwal).
     * Siapapun bisa melihat jadwal yang tersedia tanpa perlu login.
     */
    public function index(Request $request)
    {
        \App\Models\Booking::cancelExpiredGracefully();

        $tanggal       = $request->get('tanggal', today()->toDateString());
        $lapangan_id   = $request->get('lapangan_id');
        $status_filter = $request->get('status');

        // Semua lapangan untuk filter dropdown
        $lapangans = Lapangan::orderBy('status', 'asc')->orderBy('nama_lapangan', 'asc')->get();

        // Ambil semua jadwal (dipesan, pending, dan ditutup/diblokir admin)
        $booked_jadwals = Jadwal::with('lapangan')
            ->where('tanggal', $tanggal)
            ->whereIn('status', ['dipesan', 'pending', 'ditutup'])
            ->get();

        $lapangansQuery = Lapangan::orderBy('status', 'asc')->orderBy('nama_lapangan', 'asc');
        if ($lapangan_id) {
            $lapangansQuery->where('id', $lapangan_id);
        }
 