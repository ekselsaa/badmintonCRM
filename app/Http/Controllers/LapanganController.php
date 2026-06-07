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
            'harga_weekday.required' => 'Harga Senin-Jumat wajib diisi.',
            'harga_weekend.required' => 'Harga Sabtu & Minggu wajib diisi.',
            'harga_weekday.numeric'  => 'Harga harus berupa angka.',
            'harga_weekend.numeric'  => 'Harga harus berupa angka.',
            'harga_weekday.min'      => 'Harga minimal Rp 1.000.',
            'harga_weekend.min'      => 'Harga minimal Rp 1.000.',
        ]);

        Lapangan::create($validated);

        return redirect()->route('admin.lapangan.index')
            ->with('success', 'Lapangan berhasil ditambahkan!');
    }

    /** Form edit lapangan */
    public function edit($id)
    {
        $lapangan = Lapangan::findOrFail($id);
        return view('admin.lapangan.edit', compact('lapangan'));
    }

    /** Update data lapangan */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_lapangan' => 'required|string|max:100',
            'harga_weekday' => 'required|numeric|min:1000',
            'harga_weekend' => 'required|numeric|min:1000',
            'status'        => 'required|in:aktif,nonaktif',
        ]);

        $lapangan = Lapangan::findOrFail($id);
        $lapangan->update($validated);

        return redirect()->route('admin.lapangan.index')
            ->with('success', 'Lapangan berhasil diperbarui!');
    }

    /** Hapus lapangan */
    public function destroy($id)
    {
        $lapangan = Lapangan::findOrFail($id);

        // Cek apakah ada booking aktif
        $adaBookingAktif = Booking::where('lapangan_id', $lapangan->id)
            ->whereIn('status', ['pending', 'dikonfirmasi', 'dipesan'])
            ->exists();

        if ($adaBookingAktif) {
            return redirect()->route('admin.lapangan.index')
                ->with('error', 'Tidak dapat menghapus lapangan karena masih ada booking aktif. Selesaikan atau batalkan booking terkait terlebih dahulu.');
        }

        $lapangan->delete();

        return redirect()->route('admin.lapangan.index')
            ->with('success', 'Lapangan berhasil dihapus!');
    }

    // ─── Kelola Jadwal ────────────────────────────────────────────

    /** Daftar jadwal untuk semua lapangan */
    public function jadwalIndex()
    {
        $jadwals   = Jadwal::with('lapangan')
            ->where('status', 'ditutup')
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'asc')
            ->paginate(15);
        $lapangans = Lapangan::where('status', 'aktif')->get();
        $liburs    = HariLibur::with('lapangan')->orderBy('tanggal', 'desc')->get();
        $fasilitas_list = \App\Models\Fasilitas::where('is_active', true)->get();
        return view('admin.jadwal.index', compact('jadwals', 'lapangans', 'liburs', 'fasilitas_list'));
    }

    /** Simpan jadwal baru */
    public function jadwalStore(Request $request)
    {
        $request->validate([
            'lapangan_id' => 'required|exists:lapangan,id',
            'tanggal'     => 'required|date|after_or_equal:today',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
            'keterangan'  => 'nullable|string|max:255',
        ], [
            'tanggal.after_or_equal' => 'Tanggal tidak boleh di masa lalu.',
        ]);

        $mulai = Carbon::parse($request->jam_mulai);
        $selesai = Carbon::parse($request->jam_selesai);

        $mulaiMins = $mulai->hour * 60 + $mulai->minute;
        $selesaiMins = $selesai->hour * 60 + $selesai->minute;

        if ($selesaiMins === 0) {
            $selesaiMins = 24 * 60; // Midnight 24:00
        }

        // Jam operasional: 07:00 (420 menit) - 24:00 (1440 menit)
        if ($mulaiMins < 420 || $selesaiMins > 1440 || $mulaiMins >= $selesaiMins) {
            return back()->withInput()->with('error', 'Gagal memblokir! Waktu harus berada di dalam jam operasional GOR (07:00 - 24:00) dan jam selesai harus setelah jam mulai.');
        }

        // Cek apakah jadwal bentrok (ada booking aktif/pending)
        $exists = Jadwal::where('lapangan_id', $request->lapangan_id)
            ->where('tanggal', $request->tanggal)
            ->where('jam_mulai', '<', $request->jam_selesai)
            ->where('jam_selesai', '>', $request->jam_mulai)
            ->whereIn('status', ['pending', 'dipesan'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Gagal memblokir! Sudah ada booking aktif/pending di waktu tersebut.');
        }

        // Cek jika waktu yang diinput sudah terlewat (di masa lalu)
        $startDateTime = Carbon::parse($request->tanggal . ' ' . $request->jam_mulai);
        if ($startDateTime->lt(Carbon::now()->subMinutes(2))) {
            return back()->withInput()->with('error', 'Gagal memblokir! Waktu tidak boleh di masa lalu.');
        }

        // Hapus slot tersedia yang overlap agar tidak terjadi duplikasi/constraint violation
        Jadwal::where('lapangan_id', $request->lapangan_id)
            ->where('tanggal', $request->tanggal)
            ->where('jam_mulai', '<', $request->jam_selesai)
            ->where('jam_selesai', '>', $request->jam_mulai)
            ->where('status', 'tersedia')
            ->delete();

        Jadwal::updateOrCreate(
            [
                'lapangan_id' => $request->lapangan_id,
                'tanggal'     => $request->tanggal,
                'jam_mulai'   => $request->jam_mulai,
            ],
            [
                'jam_selesai' => $request->jam_selesai,
                'status'      => 'ditutup',
                'keterangan'  => $request->keterangan
            ]
        );

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jam lapangan berhasil diblokir/ditutup!');
    }

    /** Hapus jadwal */
    public function jadwalDestroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);

        // Cek apakah ada booking aktif yang masih menggunakan jadwal ini
        $adaBookingAktif = Booking::where('jadwal_id', $jadwal->id)
            ->whereIn('status', ['pending', 'dikonfirmasi', 'dipesan'])
            ->exists();

        if ($adaBookingAktif) {
            return redirect()->route('admin.jadwal.index')
                ->with('error', 'Tidak dapat menghapus jadwal karena masih ada booking aktif yang menggunakan slot ini. Batalkan booking terkait terlebih dahulu.');
        }

        $jadwal->delete();

        return redirect()->route('admin.jadwal.index')
            ->with('success', 'Jadwal berhasil dihapus!');
    }

    // ─── Booking Offline ──────────────────────────────────────────

    /**
     * Admin mencatat pemesanan lapangan secara offline.
     * Membuat Jadwal (dipesan) + Booking offline sekaligus.
     */
    public function bookingOfflineStore(Request $request)
    {
        $request->validate([
            'lapangan_id'          => 'required|exists:lapangan,id',
            'tanggal'              => 'required|date|after_or_equal:today',
            'jam_mulai'            => 'required',
            'jam_selesai'          => 'required|after:jam_mulai',
            'nama_pemesan_offline' => 'required|string|max:100',
            'no_hp_offline'        => 'nullable|string|max:20',
            'total_harga'          => 'required|numeric|min:0',
            'catatan'              => 'nullable|string|max:255',
            'fasilitas'            => 'nullable|array',
            'fasilitas.*'          => 'integer|min:0|max:100',
        ], [
            'lapangan_id.required'          => 'Lapangan wajib dipilih.',
            'tanggal.after_or_equal'        => 'Tanggal tidak boleh di masa lalu.',
            'jam_selesai.after'             => 'Jam selesai harus setelah jam mulai.',
            'nama_pemesan_offline.required' => 'Nama pemesan wajib diisi.',
            'total_harga.required'          => 'Total harga wajib diisi.',
        ]);

        $mulai = Carbon::parse($request->jam_mulai);
        $selesai = Carbon::parse($request->jam_selesai);

        $mulaiMins = $mulai->hour * 60 + $mulai->minute;
        $selesaiMins = $selesai->hour * 60 + $selesai->minute;

        if ($selesaiMins === 0) {
            $selesaiMins = 24 * 60; // Midnight 24:00
        }

        // Jam operasional: 07:00 (420 menit) - 24:00 (1440 menit)
        if ($mulaiMins < 420 || $selesaiMins > 1440 || $mulaiMins >= $selesaiMins) {
            return back()->withInput()->with('error', 'Pemesanan gagal. Jam operasional GOR adalah 07:00 - 24:00.');
        }

        // Cek jika waktu yang diinput sudah terlewat (di masa lalu)
        $startDateTime = Carbon::parse($request->tanggal . ' ' . $request->jam_mulai);
        if ($startDateTime->lt(Carbon::now()->subMinutes(2))) {
            return back()->withInput()->with('error', 'Gagal mencatat booking offline: Waktu tidak boleh di masa lalu.');
        }

        // Cek apakah jadwal sudah terpakai (booking/pending/tutup)
        $exists = Jadwal::where('lapangan_id', $request->lapangan_id)
            ->where('tanggal', $request->tanggal)
            ->where('jam_mulai', '<', $request->jam_selesai)
            ->where('jam_selesai', '>', $request->jam_mulai)
            ->whereIn('status', ['pending', 'dipesan', 'ditutup'])
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('error', 'Gagal! Slot waktu tersebut sudah terisi atau diblokir.');
        }

        // Hapus slot tersedia yang overlap agar tidak terjadi duplikasi/constraint violation
        Jadwal::where('lapangan_id', $request->lapangan_id)
            ->where('tanggal', $request->tanggal)
            ->where('jam_mulai', '<', $request->jam_selesai)
            ->where('jam_selesai', '>', $request->jam_mulai)
            ->where('status', 'tersedia')
            ->delete();

        try {
            DB::transaction(function () use ($request) {
                // 1. Buat/kunci jadwal sebagai 'dipesan'
                $jadwal = Jadwal::create([
                    'lapangan_id' => $request->lapangan_id,
                    'tanggal'     => $request->tanggal,
                    'jam_mulai'   => $request->jam_mulai,
                    'jam_selesai' => $request->jam_selesai,
                    'status'      => 'dipesan',
                    'keterangan'  => 'Booking Offline: ' . $request->nama_pemesan_offline,
                ]);

                // Hitung tambahan fasilitas
                $fasilitasArr = [];
                $hargaFasilitas = 0;
                if ($request->has('fasilitas')) {
                    foreach ($request->fasilitas as $fasilitas_id => $qty) {
                        if ($qty > 0) {
                            $f = \App\Models\Fasilitas::find($fasilitas_id);
                            if ($f) {
                                $availability = $f->checkAvailability($request->tanggal, $request->jam_mulai, $request->jam_selesai, $qty);
                                if ($availability['status'] === 'tersedia') {
                                    $hargaFasilitas += ($qty * $f->harga);
                                    $fasilitasArr[] = $f->nama . " x" . $qty;
                                } else {
                                    $pesanError = "Stok fasilitas {$f->nama} tidak mencukupi.";
                                    if ($availability['tersedia_pada']) {
                                        $pesanError .= " Akan tersedia pada jam " . $availability['tersedia_pada'];
                                    }
                                    throw new \Exception($pesanError);
                                }
                            }
                        }
                    }
                }

                $lapangan = \App\Models\Lapangan::find($request->lapangan_id);
                $isWeekend = Carbon::parse($request->tanggal)->isWeekend();
                $harga = $request->total_harga > 0
                    ? $request->total_harga
                    : (($isWeekend ? $lapangan->harga_weekend : $lapangan->harga_weekday) + $hargaFasilitas);

                // 2. Buat record booking offline (tanpa user)
                $booking = Booking::create([
                    'user_id'              => null,
                    'jadwal_id'            => $jadwal->id,
                    'lapangan_id'          => $request->lapangan_id,
                    'tanggal_booking'      => $request->tanggal,
                    'total_harga'          => $harga,
                    'status'               => 'dipesan',
                    'catatan'              => $request->catatan,
                    'is_offline'           => true,
                    'nama_pemesan_offline' => $request->nama_pemesan_offline,
                    'no_hp_offline'        => $request->no_hp_offline,
                    'fasilitas'            => implode(', ', $fasilitasArr)
                ]);

                // Simpan ke Pivot Tabel BookingFasilitas
                if ($request->has('fasilitas')) {
                    foreach ($request->fasilitas as $fasilitas_id => $qty) {
                        if ($qty > 0) {
                            $f = \App\Models\Fasilitas::find($fasilitas_id);
                            if ($f) {
                                \App\Models\BookingFasilitas::create([
                                    'booking_id' => $booking->id,
                                    'fasilitas_id' => $f->id,
                                    'jumlah' => $qty,
                                    'harga_satuan' => $f->harga,
                                    'subtotal' => $qty * $f->harga,
                                ]);
                            }
                        }
                    }
                }

                // 3. Buat record pembayaran otomatis (offline = tunai, langsung diverifikasi)
                $booking->pembayaran()->create([
                    'jumlah_bayar'      => $harga,
                    'metode_pembayaran' => 'tunai',
                    'status_verifikasi' => 'diverifikasi',
                    'catatan_admin'     => 'Booking offline oleh admin' . ($request->catatan ? ': ' . $request->catatan : '.'),
                    'verified_at'       => now(),
                ]);
            });

            return redirect()->route('admin.jadwal.index')
                ->with('success', 'Booking offline berhasil dicatat! Jadwal telah dikunci sebagai Dipesan.');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal mencatat booking offline: ' . $e->getMessage());
        }
    }

    // ─── Kelola Hari Libur ────────────────────────────────────────────

    public function liburStore(Request $request)
    {
        $request->validate([
            'tanggal'     => 'required|date|after_or_equal:today',
            'lapangan_id' => 'nullable|exists:lapangan,id',
            'keterangan'  => 'nullable|string|max:255',
        ], [
            'tanggal.after_or_equal' => 'Tanggal tidak boleh di masa lalu.'
        ]);

        // MEDIUM-1: Gunakan only() bukan all() untuk mencegah mass assignment attack
        HariLibur::create($request->only(['tanggal', 'lapangan_id', 'keterangan']));

        return redirect()->back()->with('success', 'Hari libur berhasil ditetapkan!');
    }

    public function liburDestroy($id)
    {
        HariLibur::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Hari libur berhasil dihapus!');
    }
}
