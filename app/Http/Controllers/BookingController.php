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
            ->whereDate('tanggal', $tanggal)
            ->whereIn('status', ['pending', 'dipesan', 'ditutup'])
            ->orderBy('jam_mulai')
            ->get();
        $jadwals = Jadwal::mergeWithVirtualMemberSlots($jadwals, $tanggal);

        $liburs = HariLibur::where('tanggal', $tanggal)->get();
        $fasilitas_list = \App\Models\Fasilitas::where('is_active', true)->get();

        $vouchers = \App\Models\Redemption::where('user_id', auth()->id())
            ->where('status', 'aktif')
            ->where('kode_expired_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();

        // Ambil juga voucher status keanggotaan (Sapu Bersih)
        $membershipVouchers = \App\Models\Voucher::where('user_id', auth()->id())
            ->where('status', 'aktif')
            ->where('expired_date', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'tanggal' => $tanggal,
                'formatted_tanggal' => \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y'),
                'jadwals' => $jadwals,
                'liburs' => $liburs
            ]);
        }

        return view('pelanggan.booking.index', compact(
            'lapangans', 'jadwals', 'tanggal', 'liburs', 'fasilitas_list', 'vouchers', 'membershipVouchers'
        ));
    }

    // ─── Form Booking ─────────────────────────────────────────────
    /**
     * Menampilkan form pemilihan jadwal untuk lapangan tertentu.
     */
    public function create(Request $request)
    {
        $lapangans     = Lapangan::where('status', 'aktif')->get();
        $tanggal       = $request->get('tanggal', date('Y-m-d'));
        $lapangan_id   = $request->get('lapangan_id');
        $status_filter = $request->get('status');

        // Ambil jadwal terisi (pending/dipesan)
        $jadwalsQuery = Jadwal::with('lapangan')
            ->whereDate('tanggal', $tanggal)
            ->whereIn('status', ['pending', 'dipesan', 'ditutup']);

        if ($lapangan_id) {
            $jadwalsQuery->where('lapangan_id', $lapangan_id);
        }

        if ($status_filter) {
            if ($status_filter === 'pending') {
                $jadwalsQuery->where('status', 'pending');
            } elseif ($status_filter === 'dipesan') {
                $jadwalsQuery->where('status', 'dipesan');
            } elseif ($status_filter === 'tersedia') {
                $jadwalsQuery->where('status', 'tersedia');
            }
        }

        // Ambil jadwal dasar
        $booked_jadwals = $jadwalsQuery->orderBy('jam_mulai')->get();
        $booked_jadwals = Jadwal::mergeWithVirtualMemberSlots($booked_jadwals, $tanggal);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'tanggal' => $tanggal,
                'formatted_tanggal' => \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y'),
                'jadwals' => $booked_jadwals
            ]);
        }

        return redirect()->route('booking.index', $request->query());
    }

    // ─── Cek Ketersediaan Fasilitas (AJAX) ────────────────────────
    public function cekFasilitas(Request $request)
    {
        $tanggal = $request->get('tanggal');
        $jamMulai = $request->get('jam_mulai');
        $jamSelesai = $request->get('jam_selesai');
        $excludeBookingId = $request->get('booking_id');

        if (!$tanggal || !$jamMulai || !$jamSelesai) {
            return response()->json([
                'success' => false,
                'message' => 'Tanggal, jam mulai, dan jam selesai harus ditentukan.'
            ]);
        }

        $fasilitas = \App\Models\Fasilitas::where('is_active', true)->get();

        // MEDIUM-5: Preload active bookings & pending bookings to prevent loop-query N+1 problem
        $preloadedBookings = Booking::whereIn('status', ['pending', 'dikonfirmasi', 'dipesan'])
            ->where('tanggal_booking', $tanggal)
            ->when($excludeBookingId, fn($q) => $q->where('id', '!=', $excludeBookingId))
            ->with(['jadwal', 'bookingFasilitas'])
            ->get();

        $pendingBookings = Booking::where('status', 'pending')
            ->when($excludeBookingId, fn($q) => $q->where('id', '!=', $excludeBookingId))
            ->with('bookingFasilitas')
            ->get();

        $results = [];

        foreach ($fasilitas as $f) {
            $preloadedPendingQty = $pendingBookings->flatMap->bookingFasilitas
                ->where('fasilitas_id', $f->id)
                ->sum('jumlah');

            // Pertama, hitung sisa stok aktual untuk slot tersebut
            // Caranya: panggil checkAvailability dengan qty = 1
            $check1 = $f->checkAvailability($tanggal, $jamMulai, $jamSelesai, 1, $excludeBookingId, $preloadedBookings, $preloadedPendingQty);
            $sisaStok = $check1['sisa_stok'];
            
            $tersediaPada = null;
            // Jika sisa stok kurang dari total stok, berarti ada yang disewa.
            // Kita cari tahu kapan stok tambahan/berikutnya akan tersedia.
            if ($sisaStok < $f->stok) {
                // Minta 1 unit lebih banyak dari sisa stok saat ini untuk tahu kapan stok bertambah
                $qtyTarget = $sisaStok + 1;
                $checkNext = $f->checkAvailability($tanggal, $jamMulai, $jamSelesai, $qtyTarget, $excludeBookingId, $preloadedBookings, $preloadedPendingQty);
                $tersediaPada = $checkNext['tersedia_pada'];
            }

            $results[$f->id] = [
                'id' => $f->id,
                'nama' => $f->nama,
                'total_stok' => $f->stok,
                'sisa_stok' => $sisaStok,
                'tersedia_pada' => $tersediaPada
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }

    // ─── Proses Simpan Booking ────────────────────────────────────
    /**
     * FITUR KUNCI: Mencegah double booking menggunakan DB transaction + lock.
     */
    public function store(StoreBookingRequest $request)
    {
        // Cek lapangan masih aktif
        $lapanganCek = Lapangan::find($request->lapangan_id);
        if (!$lapanganCek || $lapanganCek->status !== 'aktif') {
            return back()->withInput()->with('error', 'Pemesanan gagal. Lapangan yang dipilih sedang tidak aktif.');
        }

        // Cek Libur
        $libur = HariLibur::where('tanggal', $request->tanggal)
            ->where(function($q) use ($request) {
                $q->whereNull('lapangan_id')->orWhere('lapangan_id', $request->lapangan_id);
            })->first();

        if ($libur) {
            return back()->with('error', 'Pemesanan gagal. Lapangan ditutup pada tanggal tersebut karena: ' . ($libur->keterangan ?? 'Libur/Maintenance'));
        }

        try {
            $booking = DB::transaction(function () use ($request) {
                // Cek apakah jadwal benar-benar sudah DIPESAN (dikonfirmasi admin)
                // Jika masih PENDING, pelanggan lain masih boleh mencoba memesan (siapa cepat dia bayar)
                $overlap = Jadwal::where('lapangan_id', $request->lapangan_id)
                    ->where('tanggal', \Carbon\Carbon::parse($request->tanggal))
                    ->whereIn('status', ['pending', 'dipesan', 'ditutup'])
                    ->where('jam_mulai', '<', $request->jam_selesai)
                    ->where('jam_selesai', '>', $request->jam_mulai)
                    ->lockForUpdate()->exists();

                if ($overlap || Jadwal::isSlotCoveredByActiveMember($request->tanggal, $request->jam_mulai, $request->jam_selesai)) {
                    throw new \Exception('Jadwal bentrok! Lapangan sudah terpesan oleh Member atau sedang ditutup.');
                }

                // Simpan atau update jadwal dengan status 'pending'
                $jadwal = Jadwal::updateOrCreate(
                    [
                        'lapangan_id' => $request->lapangan_id,
                        'tanggal'     => \Carbon\Carbon::parse($request->tanggal),
                        'jam_mulai'   => $request->jam_mulai,
                    ],
                    [
                        'jam_selesai' => $request->jam_selesai,
                        'status'      => 'pending' // Set ke pending dulu
                    ]
                );

                // Hitung total harga
                $lapangan = Lapangan::find($request->lapangan_id);
                // Kalkulasi harga dasar lapangan
                $isWeekend = \Carbon\Carbon::parse($request->tanggal)->isWeekend();
                $hargaPerJam = $isWeekend ? $lapangan->harga_weekend : $lapangan->harga_weekday;

                $mulaiMins  = \Carbon\Carbon::parse($request->jam_mulai);
                $selesaiMins = \Carbon\Carbon::parse($request->jam_selesai);
                $durasi = ceil($mulaiMins->diffInMinutes($selesaiMins) / 60);

                $totalHarga = $durasi * $hargaPerJam;

                // Tambahan Fasilitas Dynamic
                $fasilitasArr = [];
                if ($request->has('fasilitas')) {
                    foreach ($request->fasilitas as $fasilitas_id => $qty) {
                        if ($qty > 0) {
                            $f = \App\Models\Fasilitas::find($fasilitas_id);
                            if ($f) {
                                $availability = $f->checkAvailability($request->tanggal, $request->jam_mulai, $request->jam_selesai, $qty);
                                if ($availability['status'] === 'tersedia') {
                                    $totalHarga += ($qty * $f->harga);
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

                // ── PROSES LOYALTY VOUCHER / PENUKARAN POIN LANGSUNG ──
                $totalDiscount = 0;
                $appliedRedemptions = collect();
                $appliedMembershipVouchers = collect();

                // 1. Ambil membership vouchers
                $mIds = $request->input('membership_voucher_ids', []);
                if ($request->filled('membership_voucher_id')) {
                    $mIds[] = $request->membership_voucher_id;
                }
                $mIds = array_unique(array_filter($mIds));

                if (!empty($mIds)) {
                    $appliedMembershipVouchers = \App\Models\Voucher::where('user_id', auth()->id())
                        ->whereIn('id', $mIds)
                        ->where('status', 'aktif')
                        ->where(function ($q) {
                            $q->whereNull('expired_date')
                              ->orWhere('expired_date', '>', now());
                        })
                        ->get();

                    if ($appliedMembershipVouchers->count() !== count($mIds)) {
                        throw new \Exception('Salah satu voucher keanggotaan tidak ditemukan, sudah digunakan, atau tidak berlaku.');
                    }
                }

                // 2. Ambil redemption vouchers
                $rIds = $request->input('voucher_ids', []);
                if ($request->filled('voucher_id')) {
                    $rIds[] = $request->voucher_id;
                }
                $rIds = array_unique(array_filter($rIds));

                if (!empty($rIds)) {
                    $appliedRedemptions = \App\Models\Redemption::where('user_id', auth()->id())
                        ->whereIn('id', $rIds)
                        ->where('status', 'aktif')
                        ->where('kode_expired_at', '>', now())
                        ->get();

                    if ($appliedRedemptions->count() !== count($rIds)) {
                        throw new \Exception('Salah satu voucher penukaran poin tidak ditemukan, sudah digunakan, atau tidak berlaku.');
                    }
                }

                // 3. Direct redemption jika ada
                if ($request->filled('direct_redeem_jenis')) {
                    $jenisHadiah = $request->direct_redeem_jenis;
                    $user = auth()->user();
                    $loyaltyService = new \App\Services\LoyaltyPointService();
                    $directRedemption = $loyaltyService->tukarPoin($user, $jenisHadiah);
                    $appliedRedemptions->push($directRedemption);
                }

                // 4. Hitung kuota fasilitas terpilih untuk validasi voucher raket, kok, & water
                $raketFasilitas = \App\Models\Fasilitas::where('nama', 'like', '%raket%')->first();
                $qtyRaket = 0;
                if ($raketFasilitas && isset($request->fasilitas[$raketFasilitas->id])) {
                    $qtyRaket = (int) $request->fasilitas[$raketFasilitas->id];
                }
                
                $kokFasilitas = \App\Models\Fasilitas::where('nama', 'like', '%kok%')
                    ->where('nama', 'not like', '%slop%')
                    ->first();
                $qtyKok = 0;
                if ($kokFasilitas && isset($request->fasilitas[$kokFasilitas->id])) {
                    $qtyKok = (int) $request->fasilitas[$kokFasilitas->id];
                }

                $waterFasilitas = \App\Models\Fasilitas::where(function ($q) {
                    $q->where('nama', 'like', '%mineral%')
                      ->orWhere('nama', 'like', '%water%');
                })->first();
                $qtyWater = 0;
                if ($waterFasilitas && isset($request->fasilitas[$waterFasilitas->id])) {
                    $qtyWater = (int) $request->fasilitas[$waterFasilitas->id];
                }
 
                // 5. Kalkulasi durasi booking untuk batas voucher lapangan
                $mulai = (int) \Carbon\Carbon::parse($request->jam_mulai)->format('H');
                $selesai = (int) \Carbon\Carbon::parse($request->jam_selesai)->format('H');
                if (str_ends_with($request->jam_selesai, '59')) {
                    $selesai += 1;
                }
                $durasi = max(1, $selesai - $mulai);
                $isWeekend = in_array(\Carbon\Carbon::parse($request->tanggal)->dayOfWeek, [0, 6]);
 
                // Tracking pemakaian voucher
                $usedRaketVouchers = 0;
                $usedKokVouchers = 0;
                $usedOffPeakVouchers = 0;
                $usedPeakVouchers = 0;
                $usedWaterVouchers = 0;
                $appliedVoucherDetails = [];
 
                // Hitung Diskon dari Membership Vouchers
                foreach ($appliedMembershipVouchers as $mv) {
                    $tier = $mv->tipe_voucher;
                    if ($tier === 'ally') {
                        // Gratis Anbiyaa Water
                        if (!$waterFasilitas) {
                            throw new \Exception('Voucher Gratis Anbiyaa Water tidak bisa digunakan: fasilitas air mineral tidak tersedia saat ini. Silakan hubungi admin.');
                        }
                        $usedWaterVouchers++;
                        if ($qtyWater < $usedWaterVouchers) {
                            throw new \Exception('Voucher Gratis Anbiyaa Water (Keanggotaan) memerlukan Anda menambahkan minimal ' . $usedWaterVouchers . ' Air Mineral.');
                        }
                        $discount = $waterFasilitas->harga;
                        $totalDiscount += $discount;
                        $appliedVoucherDetails[] = "Gratis Anbiyaa Water: -Rp " . number_format($discount, 0, ',', '.');
                    } elseif ($tier === 'partner') {
                        // Gratis Sewa Raket 1 Sesi
                        if (!$raketFasilitas) {
                            throw new \Exception('Voucher Gratis Sewa Raket tidak bisa digunakan: fasilitas raket tidak tersedia saat ini. Silakan hubungi admin.');
                        }
                        $usedRaketVouchers++;
                        if ($qtyRaket < $usedRaketVouchers) {
                            throw new \Exception('Voucher Gratis Sewa Raket (Keanggotaan) memerlukan Anda menyewa minimal ' . $usedRaketVouchers . ' raket.');
                        }
                        $discount = $raketFasilitas->harga;
                        $totalDiscount += $discount;
                        $appliedVoucherDetails[] = "Gratis Sewa Raket 1 Sesi: -Rp " . number_format($discount, 0, ',', '.');
                    } elseif ($tier === 'loyalist') {
                        // Gratis 1 Jam Sewa Lapangan Off-Peak (07:00-16:00)
                        $isOffPeak = (!$isWeekend && $mulai >= 7 && $mulai < 16);
                        if (!$isOffPeak) {
                            throw new \Exception('Voucher Gratis 1 Jam Lapangan Off-Peak (Keanggotaan) hanya berlaku jam 07:00-16:00 pada Weekdays.');
                        }
                        $usedOffPeakVouchers++;
                        if ($usedOffPeakVouchers + $usedPeakVouchers > $durasi) {
                            throw new \Exception('Jumlah voucher gratis lapangan melebihi durasi jam sewa.');
                        }
                        $totalDiscount += $hargaPerJam;
                        $appliedVoucherDetails[] = "Gratis 1 Jam Lapangan Off-Peak (Keanggotaan): -Rp " . number_format($hargaPerJam, 0, ',', '.');
                    } elseif ($tier === 'vip') {
                        // Potongan Rp 100.000
                        $totalDiscount += 100000;
                        $appliedVoucherDetails[] = "Voucher VIP Potongan Rp 100.000: -Rp 100.000";
                    }
                }
 
                // Hitung Diskon dari Redemption Vouchers
                foreach ($appliedRedemptions as $red) {
                    $jenis = $red->jenis_hadiah;
                    if ($jenis === 'voucher_50k') {
                        $totalDiscount += 50000;
                        $appliedVoucherDetails[] = "Voucher Potongan Rp 50.000: -Rp 50.000";
                    } elseif ($jenis === 'voucher_member') {
                        $totalDiscount += 100000;
                        $appliedVoucherDetails[] = "Voucher Potongan Rp 100.000: -Rp 100.000";
                    } elseif ($jenis === 'lapangan_offpeak') {
                        $isOffPeak = (!$isWeekend && $mulai >= 7 && $mulai < 16);
                        if (!$isOffPeak) {
                            throw new \Exception('Voucher Lapangan Off-Peak hanya berlaku jam 07:00-16:00 pada Weekdays.');
                        }
                        $usedOffPeakVouchers++;
                        if ($usedOffPeakVouchers + $usedPeakVouchers > $durasi) {
                            throw new \Exception('Jumlah voucher gratis lapangan melebihi durasi jam sewa.');
                        }
                        $totalDiscount += $hargaPerJam;
                        $appliedVoucherDetails[] = "Gratis 1 Jam Lapangan Off-Peak: -Rp " . number_format($hargaPerJam, 0, ',', '.');
                    } elseif ($jenis === 'lapangan_peak') {
                        $usedPeakVouchers++;
                        if ($usedOffPeakVouchers + $usedPeakVouchers > $durasi) {
                            throw new \Exception('Jumlah voucher gratis lapangan melebihi durasi jam sewa.');
                        }
                        $totalDiscount += $hargaPerJam;
                        $appliedVoucherDetails[] = "Gratis 1 Jam Lapangan Peak-Time: -Rp " . number_format($hargaPerJam, 0, ',', '.');
                    } elseif ($jenis === 'raket') {
                        if (!$raketFasilitas) {
                            throw new \Exception('Voucher Gratis Sewa Raket tidak bisa digunakan: fasilitas raket tidak tersedia saat ini.');
                        }
                        $usedRaketVouchers++;
                        if ($qtyRaket < $usedRaketVouchers) {
                            throw new \Exception('Voucher Gratis Sewa Raket memerlukan Anda menyewa minimal ' . $usedRaketVouchers . ' raket.');
                        }
                        $discount = $raketFasilitas->harga;
                        $totalDiscount += $discount;
                        $appliedVoucherDetails[] = "Gratis Sewa Raket 1 Sesi: -Rp " . number_format($discount, 0, ',', '.');
                    } elseif ($jenis === 'kok_satuan') {
                        if (!$kokFasilitas) {
                            throw new \Exception('Voucher Gratis Kok Satuan tidak bisa digunakan: fasilitas kok tidak tersedia saat ini.');
                        }
                        $usedKokVouchers++;
                        if ($qtyKok < $usedKokVouchers) {
                            throw new \Exception('Voucher shuttlecock gratis memerlukan Anda menambahkan minimal ' . $usedKokVouchers . ' kok satuan.');
                        }
                        $discount = $kokFasilitas->harga;
                        $totalDiscount += $discount;
                        $appliedVoucherDetails[] = "Gratis 1 Kok Satuan: -Rp " . number_format($discount, 0, ',', '.');
                    } elseif ($jenis === 'anbiyaa_water') {
                        if (!$waterFasilitas) {
                            throw new \Exception('Voucher Gratis Anbiyaa Water tidak bisa digunakan: fasilitas air mineral tidak tersedia saat ini.');
                        }
                        $usedWaterVouchers++;
                        if ($qtyWater < $usedWaterVouchers) {
                            throw new \Exception('Voucher Gratis Anbiyaa Water memerlukan Anda menambahkan minimal ' . $usedWaterVouchers . ' Air Mineral.');
                        }
                        $discount = $waterFasilitas->harga;
                        $totalDiscount += $discount;
                        $appliedVoucherDetails[] = "Gratis Anbiyaa Water: -Rp " . number_format($discount, 0, ',', '.');
                    }
                }

                $totalHarga = max(0, $totalHarga - $totalDiscount);

                // Tentukan status awal
                $bookingStatus  = 'pending';
                $paymentStatus  = 'menunggu';
                $jadwalStatus   = 'pending';

                // Auto-verifikasi jika total = 0 (full discount/free)
                if ($totalHarga == 0) {
                    $bookingStatus = 'dipesan';
                    $paymentStatus = 'diverifikasi';
                    $jadwalStatus  = 'dipesan';
                    // Kunci jadwal langsung
                    $jadwal->update(['status' => 'dipesan']);
                }

                $booking = Booking::create([
                    'user_id'         => auth()->id(),
                    'jadwal_id'       => $jadwal->id,
                    'lapangan_id'     => $request->lapangan_id,
                    'tanggal_booking' => $request->tanggal,
                    'total_harga'     => $totalHarga,
                    'status'          => $bookingStatus,
                    'catatan'         => $request->catatan,
                    'fasilitas'       => implode(', ', $fasilitasArr),
                    'reward_applied'  => ($appliedRedemptions->isNotEmpty() || $appliedMembershipVouchers->isNotEmpty()),
                    'voucher_id'      => $appliedMembershipVouchers->first()?->id,
                ]);

                // Simpan ke Pivot Tabel BookingFasilitas
                if ($request->has('fasilitas')) {
                    foreach ($request->fasilitas as $fasilitas_id => $qty) {
                        if ($qty > 0) {
                            $f = \App\Models\Fasilitas::find($fasilitas_id);
                            if ($f) {
                                \App\Models\BookingFasilitas::create([
                                    'booking_id'   => $booking->id,
                                    'fasilitas_id' => $f->id,
                                    'jumlah'       => $qty,
                                    'harga_satuan' => $f->harga,
                                    'subtotal'     => $qty * $f->harga,
                                ]);
                            }
                        }
                    }
                }

                // Jika voucher/redemption digunakan, link ke booking ini
                foreach ($appliedRedemptions as $red) {
                    $red->update([
                        'status'          => 'digunakan',
                        'digunakan_pada'  => now(),
                        'booking_id'      => $booking->id,
                    ]);
                }

                foreach ($appliedMembershipVouchers as $mv) {
                    $mv->update([
                        'status'          => 'digunakan',
                        'digunakan_pada'  => now(),
                        'booking_id'      => $booking->id,
                    ]);
                }

                // Simpan data pembayaran awal
                $catatanVoucher = null;
                if (!empty($appliedVoucherDetails)) {
                    $catatanVoucher = "Voucher digunakan:\n" . implode("\n", $appliedVoucherDetails) . "\nTotal Potongan: Rp " . number_format($totalDiscount, 0, ',', '.');
                }

                $booking->pembayaran()->create([
                    'jumlah_bayar'      => $totalHarga,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'status_verifikasi' => $paymentStatus,
                    'catatan_admin'     => $catatanVoucher,
                    'verified_at'       => $totalHarga == 0 ? now() : null,
                ]);

                return $booking;
            });

            return redirect()->route('booking.show', $booking->id)
                ->with('success', 'Booking berhasil! Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    // ─── Edit Booking ─────────────────────────────────────────────
    public function edit(Request $request, $id)
    {
        $booking = Booking::with(['jadwal', 'pembayaran'])->where('user_id', Auth::id())->findOrFail($id);

        if ($booking->status !== 'pending' || ($booking->pembayaran && $booking->pembayaran->bukti_pembayaran)) {
            return back()->with('error', 'Booking tidak dapat diedit karena sudah dibayar atau diproses.');
        }

        $fasilitas = $booking->fasilitas ? explode(', ', $booking->fasilitas) : [];
        $qty_raket = 0; $qty_kok_satuan = 0; $qty_kok_slop = 0;
        foreach($fasilitas as $f) {
            if(str_contains($f, 'Raket')) $qty_raket = (int) filter_var($f, FILTER_SANITIZE_NUMBER_INT);
            if(str_contains($f, 'Kok Satuan')) $qty_kok_satuan = (int) filter_var($f, FILTER_SANITIZE_NUMBER_INT);
            if(str_contains($f, 'Kok Slop')) $qty_kok_slop = (int) filter_var($f, FILTER_SANITIZE_NUMBER_INT);
        }

        $lapangans = Lapangan::where('status', 'aktif')->get();

        // Get conflicting jadwals for the selected date
        $tanggal = $request->get('tanggal', old('tanggal', $booking->tanggal_booking->format('Y-m-d')));
        $lapangan_id = $request->get('lapangan_id', old('lapangan_id', $booking->lapangan_id));
        
        $jadwalsQuery = Jadwal::with('lapangan')
            ->where('tanggal', $tanggal)
            ->where('id', '!=', $booking->jadwal_id)
            ->whereIn('status', ['pending', 'dipesan', 'ditutup']);

        if ($lapangan_id) {
            $jadwalsQuery->where('lapangan_id', $lapangan_id);
        }

        $jadwals = $jadwalsQuery->orderBy('jam_mulai')->get();
        $jadwals = Jadwal::mergeWithVirtualMemberSlots($jadwals, $tanggal);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'tanggal' => $tanggal,
                'formatted_tanggal' => \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y'),
                'jadwals' => $jadwals
            ]);
        }

        return view('pelanggan.booking.edit', compact(
            'booking', 'lapangans', 'jadwals', 'tanggal', 'lapangan_id',
            'qty_raket', 'qty_kok_satuan', 'qty_kok_slop'
        ))->with('fasilitas_list', \App\Models\Fasilitas::where('is_active', true)->get());
    }

    // ─── Update Booking ───────────────────────────────────────────
    public function update(Request $request, $id)
    {
        $booking = Booking::with(['jadwal', 'pembayaran'])->where('user_id', Auth::id())->findOrFail($id);

        if ($booking->status !== 'pending' || ($booking->pembayaran && $booking->pembayaran->bukti_pembayaran)) {
            return back()->with('error', 'Booking tidak dapat diupdate karena sudah dibayar atau diproses.');
        }

        $request->validate([
            'lapangan_id' => 'required|exists:lapangan,id',
            'tanggal'     => 'required|date|after_or_equal:today',
            'jam_mulai'   => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'catatan'     => 'nullable|string|max:500',
            'metode_pembayaran' => 'required|in:qris,tunai',
        ], [
            'jam_selesai.after' => 'Jam selesai harus lebih besar (setelah) dari jam mulai.',
            'tanggal.after_or_equal' => 'Tanggal booking tidak boleh di masa lalu.',
        ]);

        // Validasi waktu terlewat untuk hari ini
        $now = now();
        $bookingDate = \Carbon\Carbon::parse($request->tanggal);
        if ($bookingDate->isToday()) {
            $jamMulai = \Carbon\Carbon::parse($request->jam_mulai);
            // Jika tanggal booking atau jam mulai berubah ke hari ini, pastikan jam mulai tidak terlewat
            $isDateTimeChanged = ($request->tanggal != $booking->tanggal_booking->format('Y-m-d') || 
                                  $request->jam_mulai != \Carbon\Carbon::parse($booking->jadwal->jam_mulai)->format('H:i'));
            if ($isDateTimeChanged && $jamMulai->format('H:i') <= $now->format('H:i')) {
                return back()->withInput()->withErrors(['jam_mulai' => 'Tidak bisa memesan jam yang sudah terlewat pada hari ini.']);
            }
        }

        // Cek Libur
        $libur = HariLibur::where('tanggal', $request->tanggal)
            ->where(function($q) use ($request) {
                $q->whereNull('lapangan_id')->orWhere('lapangan_id', $request->lapangan_id);
            })->first();

        if ($libur) {
            return back()->with('error', 'Update gagal. Lapangan ditutup pada tanggal tersebut karena: ' . ($libur->keterangan ?? 'Libur/Maintenance'));
        }

        try {
            DB::transaction(function () use ($request, $booking) {
                // Check overlap excluding current booking's jadwal
                $overlap = Jadwal::where('lapangan_id', $request->lapangan_id)
                    ->where('tanggal', \Carbon\Carbon::parse($request->tanggal))
                    ->whereIn('status', ['pending', 'dipesan', 'ditutup'])
                    ->where('jam_mulai', '<', $request->jam_selesai)
                    ->where('jam_selesai', '>', $request->jam_mulai)
                    ->where('id', '!=', $booking->jadwal_id)
                    ->lockForUpdate()->exists();

                if ($overlap || Jadwal::isSlotCoveredByActiveMember($request->tanggal, $request->jam_mulai, $request->jam_selesai)) {
                    throw new \Exception('Jadwal bentrok! Lapangan sudah terpesan oleh Member atau sedang ditutup.');
                }

                $targetJadwal = Jadwal::updateOrCreate(
                    [
                        'lapangan_id' => $request->lapangan_id,
                        'tanggal'     => \Carbon\Carbon::parse($request->tanggal),
                        'jam_mulai'   => $request->jam_mulai,
                    ],
                    [
                        'jam_selesai' => $request->jam_selesai,
                        'status'      => 'pending'
                    ]
                );

                $oldJadwalId = $booking->jadwal_id;

                // Update pricing and extra amenities
                $lapangan = Lapangan::find($request->lapangan_id);
                $isWeekend = \Carbon\Carbon::parse($request->tanggal)->isWeekend();
                $hargaPerJam = $isWeekend ? $lapangan->harga_weekend : $lapangan->harga_weekday;

                $mulaiMins  = \Carbon\Carbon::parse($request->jam_mulai);
                $selesaiMins = \Carbon\Carbon::parse($request->jam_selesai);
                $durasi = ceil($mulaiMins->diffInMinutes($selesaiMins) / 60);

                $totalHarga = $durasi * $hargaPerJam;

                // ── Jika booking ini memakai reward, terapkan ulang diskon berdasarkan rate baru ──
                $rewardDiskon = 0;
                if ($booking->reward_applied) {
                    $rewardDiskon = $hargaPerJam; // 1 jam dengan rate terbaru
                    $totalHarga  = max(0, $totalHarga - $rewardDiskon);
                }

                $fasilitasArr = [];

                // Hapus data fasilitas lama dari pivot terlebih dahulu
                \App\Models\BookingFasilitas::where('booking_id', $booking->id)->delete();

                // Validasi dan hitung stok baru
                if ($request->has('fasilitas')) {
                    foreach ($request->fasilitas as $fasilitas_id => $qty) {
                        if ($qty > 0) {
                            $f = \App\Models\Fasilitas::find($fasilitas_id);
                            if ($f) {
                                $availability = $f->checkAvailability($request->tanggal, $request->jam_mulai, $request->jam_selesai, $qty, $booking->id);
                                if ($availability['status'] === 'tersedia') {
                                    $totalHarga += ($qty * $f->harga);
                                    $fasilitasArr[] = $f->nama . " x" . $qty;

                                    // Simpan ke Pivot
                                    \App\Models\BookingFasilitas::create([
                                        'booking_id'   => $booking->id,
                                        'fasilitas_id' => $f->id,
                                        'jumlah'       => $qty,
                                        'harga_satuan' => $f->harga,
                                        'subtotal'     => $qty * $f->harga,
                                    ]);
                                } else {
                                    $pesanError = "Stok fasilitas {$f->nama} tidak mencukupi saat diubah.";
                                    if ($availability['tersedia_pada']) {
                                        $pesanError .= " Akan tersedia pada jam " . $availability['tersedia_pada'];
                                    }
                                    throw new \Exception($pesanError);
                                }
                            }
                        }
                    }
                }

                // Tentukan status berdasarkan apakah total jadi 0
                $newBookingStatus = $booking->status; // default tidak berubah
                $newPaymentStatus = null;
                if ($totalHarga == 0 && $booking->reward_applied) {
                    $newBookingStatus = 'dipesan';
                    $newPaymentStatus = 'diverifikasi';
                    $targetJadwal->update(['status' => 'dipesan']);
                }

                $updateData = [
                    'jadwal_id'       => $targetJadwal->id,
                    'lapangan_id'     => $request->lapangan_id,
                    'tanggal_booking' => $request->tanggal,
                    'total_harga'     => $totalHarga,
                    'catatan'         => $request->catatan,
                    'fasilitas'       => implode(', ', $fasilitasArr),
                    'status'          => $newBookingStatus,
                ];
                $booking->update($updateData);

                if ($booking->pembayaran) {
                    $paymentUpdateData = [
                        'jumlah_bayar'      => $totalHarga,
                        'metode_pembayaran' => $request->metode_pembayaran,
                    ];
                    if ($newPaymentStatus) {
                        $paymentUpdateData['status_verifikasi'] = $newPaymentStatus;
                        $paymentUpdateData['verified_at']       = now();
                        $catatanReward = 'Gratis 1 jam lapangan (Loyalty Reward). Diskon: Rp ' . number_format($rewardDiskon, 0, ',', '.');
                        $paymentUpdateData['catatan_admin']     = $catatanReward;
                    }
                    $booking->pembayaran->update($paymentUpdateData);
                } else {
                    $booking->pembayaran()->create([
                        'jumlah_bayar'      => $totalHarga,
                        'metode_pembayaran' => $request->metode_pembayaran,
                        'status_verifikasi' => $newPaymentStatus ?? 'menunggu',
                        'verified_at'       => $newPaymentStatus === 'diverifikasi' ? now() : null,
                    ]);
                }

                if ($oldJadwalId != $targetJadwal->id) {
                    $oldActive = Booking::where('jadwal_id', $oldJadwalId)
                        ->where('id', '!=', $booking->id)
                        ->whereIn('status', ['pending', 'dikonfirmasi', 'dipesan'])
                        ->exists();
                    if (!$oldActive) {
                        Jadwal::where('id', $oldJadwalId)->update(['status' => 'tersedia']);
                    }
                }

            });

            return redirect()->route('booking.show', $booking->id)
                ->with('success', 'Pesanan berhasil diupdate! Silakan lanjutkan pembayaran.');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // ─── Detail Booking ─────────────────────────────────────────────
    public function show($id)
    {
        // Pastikan pelanggan hanya bisa lihat booking miliknya sendiri
        $booking = Booking::with(['jadwal.lapangan', 'lapangan', 'pembayaran'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return view('pelanggan.booking.show', compact('booking'));
    }

    // ─── Riwayat Booking ─────────────────────────────────────────
    public function riwayat()
    {
        Booking::cancelExpiredGracefully();

        $bookings = Booking::with(['jadwal', 'lapangan', 'pembayaran'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pelanggan.booking.riwayat', compact('bookings'));
    }

    // ─── Upload Bukti Pembayaran ──────────────────────────────────
    public function uploadPembayaran(Request $request, $bookingId)
    {
        $request->validate([
            // Validasi ganda: extension + actual MIME type
            'bukti_pembayaran'  => [
                'required',
                'file',
                'mimes:jpg,jpeg,png',
                'mimetypes:image/jpeg,image/png',
                'max:2048',
            ],
            'metode_pembayaran' => 'required|in:qris,tunai',
        ], [
            'bukti_pembayaran.required'   => 'Bukti pembayaran wajib diupload.',
            'bukti_pembayaran.mimes'      => 'File harus berupa gambar (JPG atau PNG).',
            'bukti_pembayaran.mimetypes'  => 'Tipe file tidak valid. Hanya JPG dan PNG yang diperbolehkan.',
            'bukti_pembayaran.max'        => 'Ukuran file maksimal 2MB.',
        ]);

        $booking = Booking::where('user_id', Auth::id())->findOrFail($bookingId);

        // ─── HIGH-2: Hapus file lama sebelum upload file baru ─────────────────
        // Mencegah penumpukan file orphan di storage/app/public/pembayaran/
        if ($booking->pembayaran && $booking->pembayaran->bukti_pembayaran) {
            $oldPath = $booking->pembayaran->bukti_pembayaran;
            // Jangan hapus jika path adalah dummy Midtrans atau sudah tidak ada
            if ($oldPath !== 'midtrans_auto' && \Storage::disk('public')->exists($oldPath)) {
                \Storage::disk('public')->delete($oldPath);
            }
        }
        // ────────────────────────────────────────────────────────────────────

        // Upload file baru ke storage/app/public/pembayaran
        $path = $request->file('bukti_pembayaran')->store('pembayaran', 'public');

        // Update data pembayaran (upload bukti)
        $booking->pembayaran()->updateOrCreate(
            ['booking_id' => $bookingId],
            [
                'bukti_pembayaran'  => $path,
                'metode_pembayaran' => $request->metode_pembayaran,
                'status_verifikasi' => 'menunggu',
            ]
        );

        return redirect()->route('booking.show', $bookingId)
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi admin.');
    }

    // ─── Simpan Ulasan Pelanggan ──────────────────────────────────
    public function storeUlasan(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'ulasan' => 'nullable|string|max:500',
        ]);

        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        if ($booking->status !== 'selesai') {
            return back()->with('error', 'Hanya booking yang telah selesai yang dapat diberi ulasan.');
        }

        if ($booking->rating) {
            return back()->with('error', 'Anda sudah memberikan ulasan untuk booking ini.');
        }

        $booking->update([
            'rating' => $request->rating,
            // MEDIUM-4: strip_tags mencegah XSS jika ada template yang menggunakan {!! !!}
            'ulasan' => $request->ulasan ? strip_tags(trim($request->ulasan)) : null,
        ]);

        return back()->with('success', 'Terima kasih! Ulasan Anda telah disimpan.');
    }
}
