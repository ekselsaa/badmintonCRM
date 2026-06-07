<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Jadwal;
use App\Models\Lapangan;
use App\Models\Pembayaran;
use App\Services\LoyaltyPointService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * AdminController - Menangani dashboard admin, verifikasi pembayaran, dan CRM pelanggan.
 */
class AdminController extends Controller
{
    // ─── Dashboard Admin ──────────────────────────────────────────
    /**
     * Menampilkan statistik ringkasan untuk admin.
     */
    public function dashboard()
    {
        // cancelExpiredGracefully dihandle oleh scheduler (booking:cancel-expired setiap menit)
        // Tidak perlu dipanggil manual di sini.

        // Statistik umum
        $totalPelangganOnline  = User::where('role', 'pelanggan')->count();
        $totalPelangganOffline = Booking::where('is_offline', true)->distinct('nama_pemesan_offline')->count('nama_pemesan_offline');
        $totalPelanggan        = $totalPelangganOnline + $totalPelangganOffline;
        $totalLapangan   = Lapangan::count();
        $totalBooking    = Booking::count();
        $pendingVerif    = Pembayaran::where('status_verifikasi', 'menunggu')->count();

        // Kontekstual: hari ini & bulan ini
        $bookingHariIni  = Booking::whereDate('tanggal_booking', today())->count();
        $bookingBulanIni = Booking::whereMonth('tanggal_booking', now()->month)
                                  ->whereYear('tanggal_booking', now()->year)->count();
        $pendapatanBulan = Booking::whereMonth('tanggal_booking', now()->month)
                                  ->whereYear('tanggal_booking', now()->year)
                                  ->whereIn('status', ['dipesan', 'selesai'])
                                  ->sum('total_harga');
        $pelangganBaru   = User::where('role', 'pelanggan')
                               ->whereMonth('created_at', now()->month)
                               ->whereYear('created_at', now()->year)->count();

        // Fasilitas Tambahan
        $totalFasilitas  = \App\Models\Fasilitas::where('is_active', 1)->count();
        $fasilitasHabis  = \App\Models\Fasilitas::where('is_active', 1)->where('stok', 0)->count();
        $fasilitasList   = \App\Models\Fasilitas::where('is_active', 1)->orderBy('stok')->get();

        // Booking terbaru (5 data)
        $bookingTerbaru  = Booking::with(['user', 'lapangan', 'jadwal'])
            ->orderBy('created_at', 'desc')
            ->take(5)->get();

        // Pembayaran menunggu verifikasi
        $pembayaranPending = Pembayaran::with(['booking.user', 'booking.lapangan'])
            ->where('status_verifikasi', 'menunggu')
            ->orderBy('created_at', 'desc')
            ->take(5)->get();

        // ─── DATA GRAFIK ANALYTICS (Chart.js) — Di-cache 10 menit ───
        $chartData = Cache::remember('dashboard_chart_data', 600, function () {
            // 1. Tren Pendapatan & Booking 6 Bulan Terakhir
            $rawRevenue = Booking::whereIn('status', ['dipesan', 'selesai'])
                ->select(
                    DB::raw("DATE_FORMAT(tanggal_booking, '%Y-%m') as bulan"),
                    DB::raw("SUM(total_harga) as total_pendapatan"),
                    DB::raw("COUNT(*) as total_booking")
                )
                ->where('tanggal_booking', '>=', now()->subMonths(5)->startOfMonth()->toDateString())
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->get();

            $chartRevenue = [];
            for ($i = 5; $i >= 0; $i--) {
                $monthKey  = now()->subMonths($i)->format('Y-m');
                $monthName = now()->subMonths($i)->translatedFormat('F Y');
                $match     = $rawRevenue->firstWhere('bulan', $monthKey);
                $chartRevenue[] = [
                    'bulan'            => $monthName,
                    'total_pendapatan' => $match ? (float) $match->total_pendapatan : 0,
                    'total_booking'    => $match ? (int) $match->total_booking : 0,
                ];
            }

            // 2. Okupansi Lapangan
            $courtOccupancy = Booking::whereIn('status', ['dipesan', 'selesai'])
                ->select('lapangan_id', DB::raw('COUNT(*) as total_booking'))
                ->with('lapangan:id,nama_lapangan')
                ->groupBy('lapangan_id')
                ->get()
                ->map(fn($item) => [
                    'nama_lapangan' => $item->lapangan->nama_lapangan ?? 'Lapangan ' . $item->lapangan_id,
                    'total_booking' => (int) $item->total_booking,
                ]);

            // 3. Jam Sibuk Booking (Peak Hours)
            $rawPeakHours = Booking::join('jadwal', 'bookings.jadwal_id', '=', 'jadwal.id')
                ->whereIn('bookings.status', ['dipesan', 'selesai'])
                ->select(DB::raw("HOUR(jadwal.jam_mulai) as jam"), DB::raw('COUNT(*) as total_booking'))
                ->groupBy('jam')
                ->orderBy('jam')
                ->get();

            $chartPeakHours = [];
            for ($h = 7; $h <= 23; $h++) {
                $match = $rawPeakHours->firstWhere('jam', $h);
                $chartPeakHours[] = [
                    'jam'           => sprintf('%02d:00', $h),
                    'total_booking' => $match ? (int) $match->total_booking : 0,
                ];
            }

            // 4. Perbandingan Metode Pembayaran
            $paymentMethods = Pembayaran::where('status_verifikasi', 'diverifikasi')
                ->select('metode_pembayaran', DB::raw('COUNT(*) as total'))
                ->groupBy('metode_pembayaran')
                ->get()
                ->map(function ($item) {
                    $label = 'Transfer';
                    if ($item->metode_pembayaran === 'qris')  $label = 'QRIS';
                    if ($item->metode_pembayaran === 'tunai') $label = 'Tunai';
                    return ['metode' => $label, 'total' => (int) $item->total];
                });

            return compact('chartRevenue', 'courtOccupancy', 'chartPeakHours', 'paymentMethods');
        });

        $chartRevenue   = $chartData['chartRevenue'];
        $courtOccupancy = $chartData['courtOccupancy'];
        $chartPeakHours = $chartData['chartPeakHours'];
        $paymentMethods = $chartData['paymentMethods'];

        return view('admin.dashboard', compact(
            'totalPelanggan', 'totalPelangganOnline', 'totalPelangganOffline', 'totalLapangan', 'totalBooking', 'pendingVerif',
            'bookingHariIni', 'bookingBulanIni', 'pendapatanBulan', 'pelangganBaru',
            'totalFasilitas', 'fasilitasHabis', 'fasilitasList',
            'bookingTerbaru', 'pembayaranPending',
            'chartRevenue', 'courtOccupancy', 'chartPeakHours', 'paymentMethods'
        ));
    }

    /**
     * Endpoint API untuk mengecek jumlah pembayaran yang butuh verifikasi.
     * Digunakan oleh fitur Notifikasi Desktop Admin.
     * Cleanup expired booking dihandle oleh scheduler, bukan di sini.
     */
    public function getPendingVerifCount()
    {
        $bookingCount = Pembayaran::where('status_verifikasi', 'menunggu')->count();
        $membershipCount = \App\Models\MembershipPayment::where('status_verifikasi', 'menunggu')->count();
        return response()->json([
            'pending_count' => $bookingCount + $membershipCount,
            'booking_count' => $bookingCount,
            'membership_count' => $membershipCount,
        ]);
    }

    // ─── Kelola Semua Booking ─────────────────────────────────────
    public function bookingIndex(Request $request)
    {
        // cancelExpiredGracefully dihandle oleh scheduler, tidak perlu dipanggil di sini

        $query = Booking::with(['user', 'lapangan', 'jadwal', 'pembayaran']);

        // Filter berdasarkan status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan tanggal
        if ($request->tanggal) {
            $query->whereHas('jadwal', fn($q) => $q->where('tanggal', $request->tanggal));
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15);

        // Build base query for stats — respects active date filter
        $baseStats = Booking::query();
        if ($request->tanggal) {
            $baseStats->whereHas('jadwal', fn($q) => $q->where('tanggal', $request->tanggal));
        }

        // Total: respects both status & date filter
        $totalQuery = clone $baseStats;
        if ($request->status) {
            $totalQuery->where('status', $request->status);
        }

        // Pending: always status=pending, respects date filter
        $pendingQuery = (clone $baseStats)->where('status', 'pending');

        // Aktif: always status=dikonfirmasi/dipesan, respects date filter
        $aktifQuery = (clone $baseStats)->whereIn('status', ['dikonfirmasi', 'dipesan']);

        // Pendapatan: respects date filter; if status filter active, sum only that status; else sum productive statuses
        $pendapatanQuery = clone $baseStats;
        if ($request->status) {
            $pendapatanQuery->where('status', $request->status);
        } else {
            $pendapatanQuery->whereIn('status', ['dikonfirmasi', 'dipesan', 'selesai']);
        }

        // Label kartu pendapatan: jika filter tanggal aktif tampilkan per-tanggal, jika tidak tampilkan bulan ini
        $pendapatanLabel = $request->tanggal
            ? 'Pendapatan ' . \Carbon\Carbon::parse($request->tanggal)->translatedFormat('d M Y')
            : 'Pendapatan Bulan Ini';

        // Jika tidak ada filter sama sekali, batasi pendapatan ke bulan ini
        if (!$request->tanggal && !$request->status) {
            $pendapatanQuery->whereHas('jadwal', fn($q) => $q->whereMonth('tanggal', now()->month)->whereYear('tanggal', now()->year));
        }

        $stats = [
            'total'               => $totalQuery->count(),
            'pending'             => $pendingQuery->count(),
            'aktif'               => $aktifQuery->count(),
            'pendapatan_bulan_ini'=> $pendapatanQuery->sum('total_harga'),
            'pendapatan_label'    => $pendapatanLabel,
        ];

        return view('admin.booking.index', compact('bookings', 'stats'));
    }

    public function bookingShow($id)
    {
        // cancelExpiredGracefully dihandle oleh scheduler, tidak perlu dipanggil di sini
        $booking = Booking::with([
            'user',
            'lapangan',
            'jadwal.lapangan',
            'pembayaran',
            'bookingFasilitas.fasilitas'
        ])->findOrFail($id);

        return view('admin.booking.show', compact('booking'));
    }

    /** Ubah status booking */
    public function bookingUpdateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,dikonfirmasi,dipesan,selesai,dibatalkan',
        ]);

        // HIGH-6: Semua operasi (update status, sinkronisasi pembayaran, loyalty points, jadwal)
        // dibungkus dalam satu DB transaction agar konsistensi data terjaga.
        // Jika salah satu langkah gagal, seluruh perubahan di-rollback otomatis.
        DB::transaction(function () use ($request, $id) {
            $booking   = Booking::findOrFail($id);
            $oldStatus = $booking->status;
            $booking->update(['status' => $request->status]);

            // ── Sinkronisasi status pembayaran ──
            if ($booking->pembayaran) {
                if ($request->status === 'dibatalkan') {
                    $booking->pembayaran->update([
                        'status_verifikasi' => 'ditolak',
                        'catatan_admin'     => 'Otomatis ditolak karena status booking diubah menjadi Dibatalkan oleh Admin.'
                    ]);
                } elseif (in_array($request->status, ['dipesan', 'selesai'])) {
                    $booking->pembayaran->update([
                        'status_verifikasi' => 'diverifikasi',
                        'verified_at'       => $booking->pembayaran->verified_at ?: now(),
                        'catatan_admin'     => 'Otomatis diverifikasi karena status booking diubah menjadi ' . ucfirst($request->status) . '.'
                    ]);

                    // ─── LOYALTY POINTS ─────────────────────────────────────────────
                    $booking->load(['jadwal', 'lapangan', 'bookingFasilitas.fasilitas']);
                    if ($booking->user_id) {
                        $loyaltyService = new LoyaltyPointService();
                        $poinDidapat = $loyaltyService->kreditPoinDariBooking($booking);

                        if ($poinDidapat > 0) {
                            $catatanLama = $booking->pembayaran->catatan_admin;
                            $catatanBaru = "[Loyalty +{$poinDidapat} poin → {$booking->user->name}]";
                            $booking->pembayaran->update([
                                'catatan_admin' => $catatanLama
                                    ? $catatanLama . "\n" . $catatanBaru
                                    : $catatanBaru,
                            ]);
                        }
                    }
                    // ────────────────────────────────────────────────────────────────
                } elseif (in_array($request->status, ['pending', 'dikonfirmasi'])) {
                    $booking->pembayaran->update([
                        'status_verifikasi' => 'menunggu',
                        'catatan_admin'     => 'Otomatis diubah menjadi Menunggu karena status booking diubah menjadi ' . ucfirst($request->status) . '.'
                    ]);
                }
            }

            // ── Sinkronisasi status jadwal berdasarkan perubahan status booking ──

            // Jika booking dikonfirmasi menjadi 'dipesan', kunci juga jadwalnya
            if ($request->status === 'dipesan' && $oldStatus !== 'dipesan') {
                if ($booking->jadwal) {
                    $booking->jadwal->update(['status' => 'dipesan']);
                }
                // Batalkan booking lain yang masih pending untuk jadwal yang sama
                Booking::where('jadwal_id', $booking->jadwal_id)
                    ->where('id', '!=', $booking->id)
                    ->where('status', 'pending')
                    ->update(['status' => 'dibatalkan']);
            }

            // Jika dibatalkan, cek apakah masih ada booking lain yang aktif untuk jadwal ini
            if ($request->status === 'dibatalkan' && $oldStatus !== 'dibatalkan') {
                $otherBookings = Booking::where('jadwal_id', $booking->jadwal_id)
                    ->where('id', '!=', $booking->id)
                    ->whereIn('status', ['pending', 'dikonfirmasi', 'dipesan'])
                    ->exists();

                if (!$otherBookings && $booking->jadwal) {
                    $booking->jadwal->update(['status' => 'tersedia']);
                }
            }
        });

        return back()->with('success', 'Status booking berhasil diperbarui!');
    }

    // ─── Verifikasi Pembayaran ────────────────────────────────────
    public function pembayaranIndex()
    {
        // cancelExpiredGracefully dihandle oleh scheduler, tidak perlu dipanggil di sini.

        // Otomatis tandai 'kedaluwarsa' untuk pembayaran menunggu yang jadwalnya sudah lewat
        $kedaluwarsaIds = Pembayaran::where('status_verifikasi', 'menunggu')
            ->whereHas('booking.jadwal', function ($q) {
                $q->where('tanggal', '<', today()->toDateString());
            })
            ->pluck('id');

        if ($kedaluwarsaIds->isNotEmpty()) {
            Pembayaran::whereIn('id', $kedaluwarsaIds)->update([
                'status_verifikasi' => 'kedaluwarsa',
                'catatan_admin'     => 'Otomatis ditolak: jadwal sudah lewat.',
                'verified_at'       => now(),
            ]);

            // Ambil booking yang terkait yang akan dibatalkan
            $bookingsToCancel = Booking::whereHas('pembayaran', function ($q) use ($kedaluwarsaIds) {
                $q->whereIn('id', $kedaluwarsaIds);
            })->whereIn('status', ['pending', 'dikonfirmasi'])->get();

            foreach ($bookingsToCancel as $booking) {
                $booking->update(['status' => 'dibatalkan']);

                // Kembalikan status jadwal menjadi tersedia jika tidak ada booking aktif lainnya
                if ($booking->jadwal) {
                    $otherBookings = Booking::where('jadwal_id', $booking->jadwal_id)
                        ->where('id', '!=', $booking->id)
                        ->whereIn('status', ['pending', 'dikonfirmasi', 'dipesan'])
                        ->exists();

                    if (!$otherBookings) {
                        $booking->jadwal->update(['status' => 'tersedia']);
                    }
                }

            }
        }

        $pembayarans = Pembayaran::with(['booking.user', 'booking.lapangan', 'booking.jadwal'])
            ->orderByRaw("FIELD(status_verifikasi, 'menunggu', 'diverifikasi', 'kedaluwarsa', 'ditolak')")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    /** Admin memverifikasi / menolak pembayaran */
    public function verifikasiPembayaran(Request $request, $id)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:diverifikasi,ditolak',
            'catatan_admin'     => 'nullable|string|max:500',
        ]);

        $pembayaran = Pembayaran::with('booking.jadwal')->findOrFail($id);

        // Guard: jangan izinkan verifikasi jika jadwal sudah lewat
        if ($pembayaran->booking?->jadwal) {
            $tglJadwal = \Carbon\Carbon::parse($pembayaran->booking->jadwal->tanggal);
            if ($tglJadwal->isPast() && !$tglJadwal->isToday()) {
                return back()->with('error', 'Tidak dapat memverifikasi: jadwal lapangan sudah lewat.');
            }
        }

        $pembayaran->update([
            'status_verifikasi' => $request->status_verifikasi,
            'catatan_admin'     => $request->catatan_admin,
            'verified_at'       => now(),
        ]);

        if ($request->status_verifikasi === 'diverifikasi') {
            DB::transaction(function() use ($pembayaran) {
                $pembayaran->booking->update(['status' => 'dipesan']);
                $pembayaran->booking->jadwal->update(['status' => 'dipesan']);

                $otherPending = Booking::where('jadwal_id', $pembayaran->booking->jadwal_id)
                    ->where('id', '!=', $pembayaran->booking_id)
                    ->where('status', 'pending')
                    ->get();
                
                foreach ($otherPending as $ob) {
                    $ob->update(['status' => 'dibatalkan']);
                }

                // ─── LOYALTY POINTS: Kredit poin setelah pembayaran terverifikasi ────────
                $booking = $pembayaran->booking->load([
                    'jadwal',
                    'lapangan',
                    'bookingFasilitas.fasilitas',
                ]);

                // Hanya proses jika ada akun pelanggan (bukan booking offline tanpa user)
                if ($booking->user_id) {
                    $loyaltyService = new LoyaltyPointService();
                    $poinDidapat = $loyaltyService->kreditPoinDariBooking($booking);

                    if ($poinDidapat > 0) {
                        // Append catatan admin agar terlacak
                        $catatanLama = $pembayaran->catatan_admin;
                        $catatanBaru = "[Loyalty +{$poinDidapat} poin → {$booking->user->name}]";
                        $pembayaran->update([
                            'catatan_admin' => $catatanLama
                                ? $catatanLama . "\n" . $catatanBaru
                                : $catatanBaru,
                        ]);
                    }
                }
                // ─────────────────────────────────────────────────────────────────
            });
        } elseif ($request->status_verifikasi === 'ditolak') {
            DB::transaction(function() use ($pembayaran) {
                $pembayaran->booking->update(['status' => 'dibatalkan']);
                
                $otherBookings = Booking::where('jadwal_id', $pembayaran->booking->jadwal_id)
                    ->where('id', '!=', $pembayaran->booking_id)
                    ->whereIn('status', ['pending', 'dikonfirmasi', 'dipesan'])
                    ->exists();

                if (!$otherBookings) {
                    $pembayaran->booking->jadwal->update(['status' => 'tersedia']);
                }
            });
        }

        return back()->with('success', 'Verifikasi pembayaran berhasil!');
    }

    // ─── CRM: Kelola Pelanggan ────────────────────────────────────
    /**
     * Menampilkan daftar semua pelanggan dengan statistik booking.
     */
    public function pelangganIndex(Request $request)
    {
        // Fetch all online customers
        $onlineQuery = User::where('role', 'pelanggan')
            ->withCount('bookings')
            ->withSum(['bookings' => function ($q) {
                $q->whereIn('status', ['dipesan', 'selesai']);
            }], 'total_harga');

        if ($request->search) {
            $search = $request->search;
            $onlineQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('nomor_hp', 'like', '%' . $search . '%');
            });
        }
        $onlineCustomers = $onlineQuery->get()->map(function($user) {
            $user->is_offline = false;
            $user->bookings_sum_total_harga = $user->bookings_sum_total_harga ?? 0;
            return $user;
        });

        // Fetch all offline bookings — aggregate di SQL, bukan di PHP memory (HIGH-1)
        $offlineQuery = Booking::where('is_offline', true)
            ->select(
                'nama_pemesan_offline as name',
                'no_hp_offline as nomor_hp',
                DB::raw('COUNT(*) as bookings_count'),
                DB::raw('SUM(CASE WHEN status IN ("dipesan","selesai") THEN total_harga ELSE 0 END) as bookings_sum_total_harga'),
                DB::raw('MIN(created_at) as created_at')
            )
            ->groupBy('nama_pemesan_offline', 'no_hp_offline');

        if ($request->search) {
            $search = $request->search;
            $offlineQuery->where(function ($q) use ($search) {
                $q->where('nama_pemesan_offline', 'like', "%{$search}%")
                  ->orWhere('no_hp_offline', 'like', "%{$search}%");
            });
        }

        $offlineCustomers = $offlineQuery->get()->map(function ($row) {
            $row->id        = null;
            $row->email     = '-';
            $row->alamat    = '-';
            $row->poin_saldo = 0;
            $row->is_offline = true;
            return $row;
        });

        // Merge collections
        $combined = $onlineCustomers->concat($offlineCustomers);

        // Sort by bookings_count descending
        $combined = $combined->sortByDesc('bookings_count')->values();

        // Top Pelanggan (Top 5) from the combined list
        $topPelanggan = $combined->take(5);

        // Calculate counts for header
        $totalOnline = $onlineCustomers->count();
        $totalOffline = $offlineCustomers->count();

        // Paginate manually
        $page = $request->get('page', 1);
        $perPage = 15;
        $offset = ($page * $perPage) - $perPage;
        
        $itemsForCurrentPage = $combined->slice($offset, $perPage)->all();
        
        $pelanggan = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            $combined->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.crm.pelanggan', compact('pelanggan', 'topPelanggan', 'totalOnline', 'totalOffline'));
    }

    /**
     * Detail CRM: riwayat booking satu pelanggan.
     */
    public function pelangganDetail($id)
    {
        $pelanggan = User::where('role', 'pelanggan')->findOrFail($id);

        $bookings = Booking::with(['lapangan', 'jadwal', 'pembayaran'])
            ->where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Statistik pelanggan ini
        $stats = [
            'total_booking'   => $bookings->total(),
            'total_bayar'     => Booking::where('user_id', $id)->whereIn('status', ['dipesan', 'selesai'])->sum('total_harga'),
            'booking_selesai' => Booking::where('user_id', $id)
                ->where(function($q) {
                    // Status selesai manual, ATAU sudah dipesan + tanggalnya sudah lewat
                    $q->where('status', 'selesai')
                      ->orWhere(function($q2) {
                          $q2->where('status', 'dipesan')
                             ->whereHas('jadwal', fn($j) => $j->where('tanggal', '<', now()->toDateString()));
                      });
                })
                ->count(),
        ];

        $pointHistories = \App\Models\PointHistory::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        $redemptions = \App\Models\Redemption::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get();

        return view('admin.crm.detail', compact('pelanggan', 'bookings', 'stats', 'pointHistories', 'redemptions'));
    }

    /**
     * Mengubah status kategori_member pelanggan.
     */
    public function pelangganToggleMember($id)
    {
        $pelanggan = User::where('role', 'pelanggan')->findOrFail($id);
        
        $pelanggan->kategori_member = $pelanggan->isMember() ? 'non-member' : 'member';
        $pelanggan->save();

        $status = $pelanggan->isMember() ? 'diupgrade menjadi Member' : 'diturunkan menjadi Non-Member';

        return back()->with('success', "Status pelanggan {$pelanggan->name} berhasil {$status}.");
    }

    /**
     * Menyesuaikan poin pelanggan secara manual.
     */
    public function pelangganAdjustPoints(Request $request, $id)
    {
        $request->validate([
            'tipe'        => 'required|in:kredit,debit',
            'jumlah_poin' => 'required|integer|min:1',
            'keterangan'  => 'nullable|string|max:255',
        ]);

        $pelanggan = User::where('role', 'pelanggan')->findOrFail($id);
        $loyaltyService = new LoyaltyPointService();

        try {
            $loyaltyService->sesuaikanPoinManual(
                $pelanggan,
                $request->tipe,
                (int) $request->jumlah_poin,
                $request->keterangan ?: ''
            );

            $label = $request->tipe === 'kredit' ? 'ditambahkan' : 'dikurangi';
            return back()->with('success', "Poin pelanggan {$pelanggan->name} berhasil {$label} sebanyak {$request->jumlah_poin} poin!");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Menghapus data pelanggan offline / walk-in.
     * Menghapus semua booking offline yang terasosiasi dengan nama & no hp tersebut.
     */
    public function pelangganDestroyOffline(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'nomor_hp' => 'nullable|string',
        ]);

        $name = $request->name;
        $nomor_hp = $request->nomor_hp;

        DB::transaction(function () use ($name, $nomor_hp) {
            $query = Booking::where('is_offline', true)
                ->where('nama_pemesan_offline', $name);

            if ($nomor_hp && $nomor_hp !== '-') {
                $query->where('no_hp_offline', $nomor_hp);
            } else {
                $query->where(function($q) {
                    $q->whereNull('no_hp_offline')
                      ->orWhere('no_hp_offline', '-')
                      ->orWhere('no_hp_offline', '');
                });
            }

            $bookings = $query->get();

            foreach ($bookings as $booking) {
                $booking->delete(); // Memicu deleting event di model Booking
            }
        });

        return back()->with('success', "Seluruh data booking untuk pelanggan offline '{$name}' berhasil dihapus dari sistem.");
    }

    // ─── Laporan ──────────────────────────────────────────────────
    /**
     * Menampilkan laporan harian dan bulanan.
     */
    public function laporanIndex(Request $request)
    {
        $bulan  = $request->get('bulan', now()->format('Y-m'));
        $filter = Carbon::parse($bulan . '-01');

        // Booking per hari dalam bulan terpilih
        $bookingPerHari = Booking::with(['lapangan', 'user'])
            ->whereYear('tanggal_booking', $filter->year)
            ->whereMonth('tanggal_booking', $filter->month)
            ->orderBy('tanggal_booking')
            ->get()
            ->groupBy('tanggal_booking');

        // Statistik bulan ini
        $totalBookingBulan = Booking::whereYear('tanggal_booking', $filter->year)
            ->whereMonth('tanggal_booking', $filter->month)
            ->count();

        // Hanya hitung booking yang sudah diverifikasi/dibayar (dipesan atau selesai)
        $totalPendapatanBulan = Booking::whereYear('tanggal_booking', $filter->year)
            ->whereMonth('tanggal_booking', $filter->month)
            ->whereIn('status', ['dipesan', 'selesai'])
            ->sum('total_harga');

        // Jumlah booking yang sudah dikonfirmasi pembayarannya (dipesan + selesai)
        $bookingDikonfirmasi = Booking::whereYear('tanggal_booking', $filter->year)
            ->whereMonth('tanggal_booking', $filter->month)
            ->whereIn('status', ['dipesan', 'selesai'])
            ->count();

        $bookingDibatalkan = Booking::whereYear('tanggal_booking', $filter->year)
            ->whereMonth('tanggal_booking', $filter->month)
            ->where('status', 'dibatalkan')
            ->count();

        // Lapangan terpopuler bulan ini (hanya booking yang berhasil)
        $lapanganPopuler = Booking::with('lapangan')
            ->whereYear('tanggal_booking', $filter->year)
            ->whereMonth('tanggal_booking', $filter->month)
            ->whereIn('status', ['dipesan', 'selesai'])
            ->select('lapangan_id', DB::raw('count(*) as total'))
            ->groupBy('lapangan_id')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // Pendapatan Fasilitas Tambahan
        $totalPendapatanFasilitas = \App\Models\BookingFasilitas::whereHas('booking', function ($q) use ($filter) {
            $q->whereYear('tanggal_booking', $filter->year)
              ->whereMonth('tanggal_booking', $filter->month)
              ->whereIn('status', ['dipesan', 'selesai']);
        })->sum('subtotal');

        $totalPendapatanLapangan = $totalPendapatanBulan - $totalPendapatanFasilitas;

        // Fasilitas terjual bulan ini
        $fasilitasPopuler = \App\Models\BookingFasilitas::with('fasilitas')
            ->whereHas('booking', function ($q) use ($filter) {
                $q->whereYear('tanggal_booking', $filter->year)
                  ->whereMonth('tanggal_booking', $filter->month)
                  ->whereIn('status', ['dipesan', 'selesai']);
            })
            ->select('fasilitas_id', DB::raw('sum(jumlah) as total_terjual'), DB::raw('sum(subtotal) as total_pendapatan'))
            ->groupBy('fasilitas_id')
            ->orderByDesc('total_terjual')
            ->get();

        // Chart: pendapatan per hari — hanya booking sudah diverifikasi/dibayar
        $pendapatanPerHari = Booking::whereYear('tanggal_booking', $filter->year)
            ->whereMonth('tanggal_booking', $filter->month)
            ->whereIn('status', ['dipesan', 'selesai'])
            ->select('tanggal_booking', DB::raw('sum(total_harga) as total'))
            ->groupBy('tanggal_booking')
            ->orderBy('tanggal_booking')
            ->get();

        return view('admin.laporan.index', compact(
            'bulan', 'filter', 'bookingPerHari',
            'totalBookingBulan', 'totalPendapatanBulan', 'totalPendapatanLapangan', 'totalPendapatanFasilitas',
            'bookingDikonfirmasi', 'bookingDibatalkan',
            'lapanganPopuler', 'fasilitasPopuler', 'pendapatanPerHari'
        ));
    }

    /**
     * Export laporan ke PDF menggunakan DomPDF.
     * Memerlukan: composer require barryvdh/laravel-dompdf
     */
    public function exportPdf(Request $request)
    {
        $bulan  = $request->get('bulan', now()->format('Y-m'));
        $filter = Carbon::parse($bulan . '-01');

        $bookings = Booking::with(['user', 'lapangan', 'pembayaran', 'jadwal', 'bookingFasilitas.fasilitas'])
            ->whereYear('tanggal_booking', $filter->year)
            ->whereMonth('tanggal_booking', $filter->month)
            ->whereIn('status', ['dipesan', 'selesai'])
            ->orderBy('tanggal_booking')
            ->get();

        $totalPendapatan = $bookings->sum('total_harga');

        // Jika DomPDF terinstall
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.laporan.pdf', compact('bookings', 'filter', 'totalPendapatan'));
            return $pdf->download("laporan-keuangan-{$bulan}.pdf");
        }

        // Fallback: tampilkan view biasa jika PDF tidak tersedia
        return view('admin.laporan.pdf', compact('bookings', 'filter', 'totalPendapatan'));
    }

    /**
     * Export laporan ke Excel/CSV.
     * Fallback ke CSV jika maatwebsite/excel tidak terinstall.
     */
    public function exportExcel(Request $request)
    {
        $bulan  = $request->get('bulan', now()->format('Y-m'));
        $filter = Carbon::parse($bulan . '-01');

        $bookings = Booking::with(['user', 'lapangan', 'pembayaran', 'jadwal'])
            ->whereYear('tanggal_booking', $filter->year)
            ->whereMonth('tanggal_booking', $filter->month)
            ->whereIn('status', ['dipesan', 'selesai'])
            ->orderBy('tanggal_booking')
            ->get();

        // Export ke CSV (tanpa library tambahan)
        $filename = "laporan-keuangan-{$bulan}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            // BOM untuk Excel agar baca UTF-8 dengan benar
            fputs($handle, "\xEF\xBB\xBF");

            // Header kolom
            fputcsv($handle, [
                'No', 'Tanggal Booking', 'Pelanggan', 'Email', 'No HP',
                'Lapangan', 'Jam', 'Fasilitas', 'Total Harga', 'Status'
            ]);

            foreach ($bookings as $i => $b) {
                fputcsv($handle, [
                    $i + 1,
                    $b->tanggal_booking->format('Y-m-d'),
                    $b->nama_pemesan,
                    $b->user?->email ?? '-',
                    $b->user?->nomor_hp ?? $b->no_hp_offline ?? '-',
                    $b->lapangan->nama_lapangan ?? '-',
                    $b->jadwal ? $b->jadwal->jam_mulai . ' - ' . $b->jadwal->jam_selesai : '-',
                    $b->fasilitas ?: '-',
                    $b->total_harga,
                    ucfirst($b->status),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ─── Manajemen Ulasan (Testimoni) ─────────────────────────────
    public function ulasanIndex()
    {
        $ulasans = Booking::with(['user', 'lapangan'])
            ->whereNotNull('rating')
            ->orderBy('updated_at', 'desc')
            ->paginate(15);
            
        return view('admin.ulasan.index', compact('ulasans'));
    }

    public function ulasanToggleBeranda($id)
    {
        $booking = Booking::findOrFail($id);
        
        $booking->update([
            'is_tampil_beranda' => !$booking->is_tampil_beranda
        ]);

        $status = $booking->is_tampil_beranda ? 'ditampilkan di' : 'disembunyikan dari';
        return back()->with('success', "Ulasan berhasil {$status} beranda.");
    }

    /**
     * Mengambil daftar fasilitas dengan stok dinamis untuk booking tertentu.
     */
    public function bookingGetFasilitas($id)
    {
        $booking = Booking::with(['jadwal', 'bookingFasilitas'])->findOrFail($id);
        
        $tanggal = $booking->tanggal_booking->format('Y-m-d');
        $jamMulai = $booking->jadwal ? Carbon::parse($booking->jadwal->jam_mulai)->format('H:i') : '07:00';
        $jamSelesai = $booking->jadwal ? Carbon::parse($booking->jadwal->jam_selesai)->format('H:i') : '08:00';
        
        $fasilitas = \App\Models\Fasilitas::where('is_active', true)->get();

        // MEDIUM-5: Preload active bookings & pending bookings to prevent loop-query N+1 problem
        $preloadedBookings = Booking::whereIn('status', ['pending', 'dikonfirmasi', 'dipesan'])
            ->where('tanggal_booking', $tanggal)
            ->where('id', '!=', $booking->id)
            ->with(['jadwal', 'bookingFasilitas'])
            ->get();

        $pendingBookings = Booking::where('status', 'pending')
            ->where('id', '!=', $booking->id)
            ->with('bookingFasilitas')
            ->get();

        $dataFasilitas = [];
        
        foreach ($fasilitas as $f) {
            $preloadedPendingQty = $pendingBookings->flatMap->bookingFasilitas
                ->where('fasilitas_id', $f->id)
                ->sum('jumlah');

            // Panggil checkAvailability dengan mengecualikan booking ini sendiri
            $check = $f->checkAvailability($tanggal, $jamMulai, $jamSelesai, 1, $booking->id, $preloadedBookings, $preloadedPendingQty);
            
            // Hitung sisa stok jika qty target +1
            $sisaStok = $check['sisa_stok'];
            $tersediaPada = null;
            if ($sisaStok < $f->stok) {
                $qtyTarget = $sisaStok + 1;
                $checkNext = $f->checkAvailability($tanggal, $jamMulai, $jamSelesai, $qtyTarget, $booking->id, $preloadedBookings, $preloadedPendingQty);
                $tersediaPada = $checkNext['tersedia_pada'];
            }
            
            // Dapatkan jumlah yang sedang disewa oleh booking ini
            $pivot = $booking->bookingFasilitas->firstWhere('fasilitas_id', $f->id);
            $jumlahDipesan = $pivot ? $pivot->jumlah : 0;
            
            $dataFasilitas[] = [
                'id' => $f->id,
                'nama' => $f->nama,
                'harga' => $f->harga,
                'stok' => $f->stok,
                'icon' => $f->icon,
                'jumlah_dipesan' => $jumlahDipesan,
                'sisa_stok' => $sisaStok,
                'tersedia_pada' => $tersediaPada,
            ];
        }
        
        return response()->json([
            'success' => true,
            'booking' => [
                'id' => $booking->id,
                'nama_pemesan' => $booking->nama_pemesan,
                'tanggal' => $tanggal,
                'jam_mulai' => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'total_harga' => $booking->total_harga,
                'fasilitas_text' => $booking->fasilitas
            ],
            'fasilitas' => $dataFasilitas
        ]);
    }

    /**
     * Memperbarui sewa fasilitas pada booking yang sedang aktif/dipesan.
     */
    public function bookingUpdateFasilitas(Request $request, $id)
    {
        $request->validate([
            'fasilitas' => 'nullable|array',
        ]);
        
        $booking = Booking::with(['jadwal', 'pembayaran', 'bookingFasilitas'])->findOrFail($id);
        
        // Hanya izinkan jika status booking adalah dipesan, dikonfirmasi, atau selesai
        if (!in_array($booking->status, ['dipesan', 'dikonfirmasi', 'selesai'])) {
            return back()->with('error', 'Fasilitas hanya bisa diubah untuk booking yang berstatus Dipesan, Dikonfirmasi, atau Selesai.');
        }
        
        $tanggal = $booking->tanggal_booking->format('Y-m-d');
        $jamMulai = $booking->jadwal ? Carbon::parse($booking->jadwal->jam_mulai)->format('H:i') : '07:00';
        $jamSelesai = $booking->jadwal ? Carbon::parse($booking->jadwal->jam_selesai)->format('H:i') : '08:00';
        
        try {
            DB::transaction(function () use ($request, $booking, $tanggal, $jamMulai, $jamSelesai) {
                $totalHargaFasilitasLama = 0;
                foreach ($booking->bookingFasilitas as $pf) {
                    $totalHargaFasilitasLama += $pf->subtotal;
                }
                
                $totalHargaFasilitasBaru = 0;
                $fasilitasArr = [];
                $newPivots = [];
                
                if ($request->has('fasilitas')) {
                    foreach ($request->fasilitas as $fasilitas_id => $qty) {
                        $qty = (int) $qty;
                        if ($qty > 0) {
                            $f = \App\Models\Fasilitas::findOrFail($fasilitas_id);
                            
                            // Validasi ketersediaan stok baru (exclude booking ini sendiri)
                            $availability = $f->checkAvailability($tanggal, $jamMulai, $jamSelesai, $qty, $booking->id);
                            if ($availability['status'] !== 'tersedia') {
                                $pesanError = "Stok fasilitas {$f->nama} tidak mencukupi.";
                                if ($availability['tersedia_pada']) {
                                    $pesanError .= " Akan tersedia pada jam " . $availability['tersedia_pada'];
                                }
                                throw new \Exception($pesanError);
                            }
                            
                            $subtotal = $qty * $f->harga;
                            $totalHargaFasilitasBaru += $subtotal;
                            
                            $fasilitasArr[] = $f->nama . " x" . $qty;
                            $newPivots[] = [
                                'booking_id' => $booking->id,
                                'fasilitas_id' => $f->id,
                                'jumlah' => $qty,
                                'harga_satuan' => $f->harga,
                                'subtotal' => $subtotal,
                            ];
                        }
                    }
                }
                
                // Hitung selisih harga
                $selisihHarga = $totalHargaFasilitasBaru - $totalHargaFasilitasLama;
                
                // 1. Hapus pivot lama, masukkan yang baru
                \App\Models\BookingFasilitas::where('booking_id', $booking->id)->delete();
                foreach ($newPivots as $pivotData) {
                    \App\Models\BookingFasilitas::create($pivotData);
                }
                
                // 2. Update booking
                $booking->total_harga += $selisihHarga;
                $booking->fasilitas = implode(', ', $fasilitasArr);
                $booking->save();
                
                // 3. Update pembayaran jika ada
                if ($booking->pembayaran) {
                    $booking->pembayaran->jumlah_bayar += $selisihHarga;
                    
                    // Catat perubahan di catatan_admin
                    $infoPerubahan = "Penyesuaian fasilitas sewa oleh Admin. Selisih: Rp " . number_format($selisihHarga, 0, ',', '.');
                    if ($booking->pembayaran->catatan_admin) {
                        $booking->pembayaran->catatan_admin .= "\n[" . now()->format('d/m/Y H:i') . "] " . $infoPerubahan;
                    } else {
                        $booking->pembayaran->catatan_admin = $infoPerubahan;
                    }
                    $booking->pembayaran->save();
                }
            });
            
            return back()->with('success', 'Fasilitas booking berhasil diperbarui!');
            
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui fasilitas: ' . $e->getMessage());
        }
    }

    /**
     * Tampilkan daftar verifikasi pembayaran membership
     */
    public function membershipPembayaranIndex()
    {
        $pembayarans = \App\Models\MembershipPayment::with('user')
            ->orderByRaw("FIELD(status_verifikasi, 'menunggu', 'diverifikasi', 'ditolak')")
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.pembayaran.membership', compact('pembayarans'));
    }

    /**
     * Proses verifikasi pembayaran membership (setuju/tolak)
     */
    public function verifikasiPembayaranMembership(Request $request, $id)
    {
        $request->validate([
            'status_verifikasi' => 'required|in:diverifikasi,ditolak',
            'catatan_admin'     => 'nullable|string|max:500',
        ]);

        $payment = \App\Models\MembershipPayment::findOrFail($id);

        \Illuminate\Support\Facades\DB::transaction(function () use ($payment, $request) {
            $payment->update([
                'status_verifikasi' => $request->status_verifikasi,
                'catatan_admin'     => $request->catatan_admin,
                'verified_at'       => now(),
            ]);

            if ($request->status_verifikasi === 'diverifikasi') {
                // Otomatis ubah kategori_member user menjadi kategori paket yang dipilih
                $payment->user->update([
                    'kategori_member' => $payment->paket
                ]);

                // Otomatis buat 4 booking (sesi rutin member)
                if ($payment->hari && $payment->lapangan_id) {
                    $dayNameEnglish = match(strtolower($payment->hari)) {
                        'senin'  => 'Monday',
                        'selasa' => 'Tuesday',
                        'rabu'   => 'Wednesday',
                        'kamis'  => 'Thursday',
                        'jumat'  => 'Friday',
                        'sabtu'  => 'Saturday',
                        'minggu' => 'Sunday',
                    };

                    $dates = [];
                    $currentDate = \Carbon\Carbon::now();
                    for ($i = 0; $i < 4; $i++) {
                        $currentDate = $currentDate->copy()->next($dayNameEnglish);
                        $dates[] = $currentDate->format('Y-m-d');
                    }

                    foreach ($dates as $index => $date) {
                        $jadwal = \App\Models\Jadwal::updateOrCreate(
                            [
                                'lapangan_id' => $payment->lapangan_id,
                                'tanggal'     => $date,
                                'jam_mulai'   => $payment->jam_mulai,
                            ],
                            [
                                'jam_selesai' => $payment->jam_selesai,
                                'status'      => 'dipesan',
                                'keterangan'  => 'Slot Member: ' . $payment->user->name
                            ]
                        );

                        // Masukkan nominal bayar membership pada booking sesi pertama agar tercatat di laporan transaksi
                        $hargaBooking = ($index === 0) ? $payment->jumlah_bayar : 0;

                        $booking = \App\Models\Booking::create([
                            'user_id'         => $payment->user_id,
                            'jadwal_id'       => $jadwal->id,
                            'lapangan_id'     => $payment->lapangan_id,
                            'tanggal_booking' => $date,
                            'total_harga'     => $hargaBooking,
                            'status'          => 'dipesan',
                            'catatan'         => 'Sesi Rutin Member (Paket ' . ucfirst($payment->paket) . ')',
                        ]);

                        $booking->pembayaran()->create([
                            'jumlah_bayar'      => $hargaBooking,
                            'metode_pembayaran' => $payment->metode_pembayaran,
                            'status_verifikasi' => 'diverifikasi',
                            'catatan_admin'     => 'Auto-generated dari verifikasi membership #' . $payment->id,
                            'verified_at'       => now(),
                        ]);
                    }
                }
            }
        });

        return back()->with('success', 'Verifikasi pembayaran membership berhasil diperbarui!');
    }
}
