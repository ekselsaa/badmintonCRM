<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use App\Models\Jadwal;
use App\Models\Booking;
use App\Models\HariLibur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * LapanganController - Admin mengelola data lapangan (CRUD) dan jadwal.
 */
class LapanganController extends Controller
{
    // ─── CRUD Lapangan ────────────────────────────────────────────

    /** Daftar semua lapangan */
    public function index()
    {
        $lapangans = Lapangan::withCount('bookings')->paginate(10);
        return view('admin.lapangan.index', compact('lapangans'));
    }

    /** Form tambah lapangan */
    public function create()
    {
        return view('admin.lapangan.create');
    }

    /** Simpan lapangan baru */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lapangan' => 'required|string|max:100',
            'deskripsi'     => 'nullable|string|max:500',
            'harga_weekday' => 'required|numeric|min:1000',
            'harga_weekend' => 'required|numeric|min:1000',
            'status'        => 'required|in:aktif,nonaktif',
        ], [
            'nama_lapangan.required' => 'Nama lapangan wajib diisi.',
            'harga_weekday.required' => 'Harga Senin-Jumat wajib diisi.