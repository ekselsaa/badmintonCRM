<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use App\Models\Jadwal;

/**
 * HomeController - Menangani halaman publik yang bisa diakses tanpa login.
 * Route ini TIDAK menggunakan middleware auth.
 */
class HomeController extends Controller
{
    /**
     * Halaman utama (/) - bisa diakses siapapun.
     * Menampilkan info lapangan dan CTA (call to action) untuk booking/login.
     */
    public function index()
    {
        // Ambil semua lapangan untuk ditampilkan di halaman publik (yang aktif tampil pertama)
        $lapangans = Lapangan::orderBy('status', 'asc')->orderBy('nama_lapangan', 'asc')->get();

        // Ambil jadwal tersedia untuk hari ini sebagai pratinjau
        // Status 'tersedia' sudah cukup — tidak perlu cek booking karena
        // jadwal dengan booking aktif statusnya sudah berubah ke 'pending'/'dipesan'
        $jadwalHariIni = Jadwal::with('lapangan')
            ->where('tanggal', today())
            ->where('status', 'tersedia')
            ->take(6)
            ->get();

        // Ambil testimoni pilihan untuk ditampilkan di beranda
        $testimonis = \App\Models\Booking::with('user')
            ->whereNotNull('rating')
            ->where('is_tampil_beranda', true)
            ->where('status', 'selesai')
            ->orderBy('updated_at', 'desc')
            ->take(6)
            ->get();

        return view('home', compact('lapangans', 'jadwalHariIni', 'testimonis'));
    }
}
