<?php

namespace App\Services;

use App\Models\User;
use App\Models\Booking;
use App\Models\PointHistory;
use App\Models\Redemption;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * LoyaltyPointService
 *
 * Pusat seluruh logika bisnis sistem poin loyalitas GOR Anbiyaa.
 * Semua operasi poin (kredit, debit, penukaran) HARUS melalui service ini
 * untuk menjaga konsistensi saldo di tabel users.poin_saldo.
 *
 * Aturan Poin:
 *  - Rasio dasar   : Rp 5.000 = 1 Poin (floor)
 *  - Weekday Off-Peak (07:00–15:xx) = DOUBLE POINTS (×2)
 *  - Fasilitas     : poin tetap per jenis
 *  - Expiry        : 6 bulan sejak kredit
 */
class LoyaltyPointService
{
    // ════════════════════════════════════════════════════════════
    //  KONSTANTA — Sumber Kebenaran Tunggal untuk Aturan Bisnis
    // ════════════════════════════════════════════════════════════

    /** Nilai rupiah per 1 poin dasar */
    const RASIO_POIN = 5000;

    /** Jam mulai Off-Peak (inklusif) */
    const JAM_OFFPEAK_MULAI = 7;

    /** Jam selesai Off-Peak (eksklusif — jam mulai booking HARUS < nilai ini) */
    const JAM_OFFPEAK_SELESAI = 16;

    /** Multiplier jam off-peak Weekdays */
    const MULTIPLIER_OFFPEAK = 2;

    /** Multiplier normal (Peak / Weekend) */
    const MULTIPLIER_NORMAL = 1;

    /** Poin tetap per fasilitas (per unit) */
    const POIN_SEWA_RAKET     = 5;
    const POIN_KOK_SATUAN     = 3;
    const POIN_KOK_SLOP       = 27;

    /** Poin paket member */
    const POIN_MEMBER_PAGI    = 70;   // Weekdays Pagi/Siang Rp 350k
    const POIN_MEMBER_MALAM   = 100;  // Weekdays Malam Rp 500k
    const POIN_MEMBER_WEEKEND = 110;  // Weekend Rp 550k

    /** Masa kadaluwarsa poin kredit (bulan) */
    const EXPIRY_MONTHS = 6;

    /**
     * Menu penukaran poin — array of ['poin' => int, 'label' => string, 'deskripsi' => string]
     * Key = nilai untuk kolom redemptions.jenis_hadiah
     */
    const REDEEM = [
        'anbiyaa_water' => [
            'poin'      => 10,
            'label'     => 'Gratis Anbiyaa Water',
            'deskripsi' => 'Klaim 1 botol Anbiyaa Water dingin di meja kasir.',
            'icon'      => '💧',
        ],
        'kok_satuan' => [
            'poin'      => 20,
            'label'     => 'Gratis 1 Shuttlecock Satuan',
            'deskripsi' => 'Klaim 1 buah shuttlecock gratis di meja kasir.',
            'icon'      => '🏸',
        ],
        'raket' => [
            'poin'      => 35,
            'label'     => 'Gratis Sewa Raket 1 Sesi',
            'deskripsi' => 'Sewa raket gratis untuk 1 sesi booking lapangan.',
            'icon'      => '🎾',
        ],
        'lapangan_offpeak' => [
            'poin'      => 50,
            'label'     => 'Gratis 1 Jam Lapangan Off-Peak',
            'deskripsi' => 'Gratis 1 jam sewa lapangan pada Weekdays Pagi/Siang (07:00–16:00).',
            'icon'      => '☀️',
        ],
        'voucher_50k' => [
            'poin'      => 75,
            'label'     => 'Voucher Potongan Rp 50.000',
            'deskripsi' => 'Potongan harga Rp 50.000 untuk sewa lapangan eceran.',
            'icon'      => '🎫',
        ],
        'lapangan_peak' => [
            'poin'      => 100,
            'label'     => 'Gratis 1 Jam Lapangan Peak-Time',
            'deskripsi' => 'Gratis 1 jam sewa lapangan Weekdays Malam atau Weekend.',
            'icon'      => '🌟',
        ],
        'voucher_member' => [
            'poin'        => 180,
            'label'       => 'Voucher Rp 100.000 Perpanjangan Member',
            'deskripsi'   => 'Diskon Rp 100.000 untuk perpanjangan paket member bulan berikutnya.',
            'icon'        => '👑',
            'member_only' => true, // Hanya bisa ditukar oleh pelanggan berstatus member aktif
        ],
    ];

    // ════════════════════════════════════════════════════════════
    //  METODE UTAMA: Kredit Poin dari Booking yang Diverifikasi
    // ════════════════════════════════════════════════════════════

    /**
     * Titik masuk utama — dipanggil oleh AdminController saat pembayaran DIVERIFIKASI.
     *
     * @param  Booking $booking  Harus sudah di-load dengan relasi: jadwal, lapangan, bookingFasilitas.fasilitas
     * @return int  Total poin yang dikreditkan (0 jika pelanggan offline tanpa akun)
     */
    public function kreditPoinDariBooking(Booking $booking): int
    {
        // Hanya proses untuk pelanggan yang punya akun (bukan offline tanpa user_id)
        if (!$booking->user_id) {
            return 0;
        }

        $user = User::find($booking->user_id);
        if (!$user) {
            return 0;
        }

        // Cegah double-crediting: cek apakah booking ini sudah mendapatkan kredit poin sebelumnya
        $sudahAda = PointHistory::where('booking_id', $booking->id)
            ->where('tipe', 'kredit')
            ->exists();
        if ($sudahAda) {
            Log::warning("[Loyalty] Percobaan double-crediting untuk Booking #{$booking->id} dibatalkan.");
            return 0;
        }

        $poinSewa      = 0;
        $poinFasilitas = 0;

        // 1. Hitung poin dari sewa lapangan
        if ($booking->jadwal) {
            $poinSewa = $this->hitungPoinSewa($booking);
        }

        // 2. Hitung poin dari fasilitas tambahan
        if ($booking->bookingFasilitas && $booking->bookingFasilitas->isNotEmpty()) {
            $poinFasilitas = $this->hitungPoinFasilitas($booking);
        }

        $totalPoin = $poinSewa + $poinFasilitas;

        // 3. Simpan ke database jika ada poin
        if ($totalPoin > 0) {
            $this->simpanPoinKredit($user, $booking, $poinSewa, $poinFasilitas);
        }

        Log::info("[Loyalty] Booking #{$booking->id}: +{$totalPoin} poin (sewa: {$poinSewa}, fasilitas: {$poinFasilitas}) → User #{$user->id} ({$user->name})");

        return $totalPoin;
    }

    // ════════════════════════════════════════════════════════════
    //  LOGIKA CEK OFF-PEAK
    // ════════════════════════════════════════════════════════════

    /**
     * Menentukan apakah slot booking masuk kategori Off-Peak Weekdays.
     *
     * Syarat KEDUANYA harus terpenuhi:
     * 1. Hari booking adalah Senin–Jumat (bukan Sabtu/Minggu)
     * 2. Jam MULAI booking >= 07:00 DAN jam MULAI < 16:00
     *
     * CONTOH:
     *   Senin,  07:00–09:00 → OFF-PEAK ✓ (Double ×2)
     *   Senin,  15:00–17:00 → OFF-PEAK ✓ (jam mulai 15 < 16, meski selesai 17)
     *   Senin,  16:00–17:00 → PEAK    ✗ (jam mulai 16 tidak < 16)
     *   Sabtu,  08:00–09:00 → PEAK    ✗ (Weekend)
     *
     * @param  Booking $booking  Harus punya relasi jadwal
     * @return bool
     */
    public function isOffPeak(Booking $booking): bool
    {
        if (!$booking->jadwal) {
            return false;
        }

        // Syarat 1: Hari booking HARUS Weekday (Senin–Jumat)
        $isWeekday = !Carbon::parse($booking->tanggal_booking)->isWeekend();

        // Syarat 2: Jam MULAI booking >= 07:00 DAN < 16:00
        $jamMulai = (int) Carbon::parse($booking->jadwal->jam_mulai)->format('H');
        $isOffPeakHour = ($jamMulai >= self::JAM_OFFPEAK_MULAI && $jamMulai < self::JAM_OFFPEAK_SELESAI);

        // KEDUANYA harus terpenuhi
        return $isWeekday && $isOffPeakHour;
    }

    // ════════════════════════════════════════════════════════════
    //  KALKULASI POIN SEWA LAPANGAN
    // ════════════════════════════════════════════════════════════

    /**
     * Hitung poin dari komponen harga sewa lapangan.
     *
     * Rumus:
     *   poin = floor(harga_lapangan / RASIO_POIN) × multiplier
     *
     * Harga lapangan = total_harga - subtotal_fasilitas
     * Jika booking menggunakan reward (total = 0), hitung berdasarkan harga standar lapangan.
     *
     * @return int
     */
    private function hitungPoinSewa(Booking $booking): int
    {
        // Pisahkan harga lapangan dari harga fasilitas
        $hargaFasilitas = $booking->bookingFasilitas->sum('subtotal') ?? 0;
        $hargaLapangan  = max(0, $booking->total_harga - $hargaFasilitas);

        // Jika total harga lapangan 0 (mis. booking reward gratis),
        // gunakan harga standar untuk tetap memberikan poin
        if ($hargaLapangan <= 0 && $booking->jadwal && $booking->lapangan) {
            $isWeekend   = Carbon::parse($booking->tanggal_booking)->isWeekend();
            $hargaPerJam = $isWeekend
                ? $booking->lapangan->harga_weekend
                : $booking->lapangan->harga_weekday;

            $durasi = ceil(
                Carbon::parse($booking->jadwal->jam_mulai)
                      ->diffInMinutes(Carbon::parse($booking->jadwal->jam_selesai)) / 60
            );

            $hargaLapangan = $durasi * $hargaPerJam;
        }

        if ($hargaLapangan <= 0) {
            return 0;
        }

        $multiplier = $this->isOffPeak($booking) ? self::MULTIPLIER_OFFPEAK : self::MULTIPLIER_NORMAL;
        $poinDasar  = (int) floor($hargaLapangan / self::RASIO_POIN);

        return $poinDasar * $multiplier;
    }

    // ════════════════════════════════════════════════════════════
    //  KALKULASI POIN FASILITAS TAMBAHAN
    // ════════════════════════════════════════════════════════════

    /**
     * Hitung poin dari fasilitas yang disewa bersama booking (raket, kok satuan, kok slop).
     * Poin bersifat tetap per unit, tidak bergantung pada harga.
     *
     * Pencocokan nama fasilitas menggunakan str_contains() (case-insensitive).
     * Pastikan nama fasilitas di DB mengandung kata: "raket", "slop", "dos", "kok", atau "shuttlecock".
     *
     * @return int
     */
    private function hitungPoinFasilitas(Booking $booking): int
    {
        $total = 0;

        foreach ($booking->bookingFasilitas as $pivot) {
            $nama = strtolower($pivot->fasilitas->nama ?? '');
            $qty  = (int) $pivot->jumlah;

            if (str_contains($nama, 'raket')) {
                $total += self::POIN_SEWA_RAKET * $qty;

            } elseif (str_contains($nama, 'slop') || str_contains($nama, 'dos')) {
                // Kok slop / dos = 1 slop berisi 12 biji
                $total += self::POIN_KOK_SLOP * $qty;

            } elseif (str_contains($nama, 'kok') || str_contains($nama, 'shuttlecock')) {
                // Kok satuan
                $total += self::POIN_KOK_SATUAN * $qty;
            }
        }

        return $total;
    }

    // ════════════════════════════════════════════════════════════
    //  SIMPAN KREDIT POIN KE DATABASE
    // ════════════════════════════════════════════════════════════

    /**
     * Mencatat satu atau dua entri kredit ke points_history dan update saldo user.
     * Jika ada komponen sewa + fasilitas, keduanya digabung dalam 1 entri demi simplisitas.
     */
    private function simpanPoinKredit(User $user, Booking $booking, int $poinSewa, int $poinFasilitas): void
    {
        $totalPoin = $poinSewa + $poinFasilitas;
        $offPeak   = $this->isOffPeak($booking);
        $isWeekend = Carbon::parse($booking->tanggal_booking)->isWeekend();

        // Tentukan sumber dominan
        if ($isWeekend) {
            $sumber = 'sewa_lapangan_peak';
        } elseif ($offPeak) {
            $sumber = 'sewa_lapangan_offpeak';
        } else {
            $sumber = 'sewa_lapangan_peak';
        }

        // Bangun keterangan yang informatif
        $keteranganParts = [];
        if ($poinSewa > 0) {
            $label = $offPeak ? "Off-Peak ×2" : "Peak";
            $keteranganParts[] = "Sewa lapangan ({$label}): +{$poinSewa} poin";
        }
        if ($poinFasilitas > 0) {
            $keteranganParts[] = "Fasilitas tambahan: +{$poinFasilitas} poin";
        }

        $keterangan = sprintf(
            'Booking #%d — %s, %s. %s',
            $booking->id,
            $booking->lapangan->nama_lapangan ?? 'Lapangan',
            Carbon::parse($booking->tanggal_booking)->translatedFormat('d M Y'),
            implode('. ', $keteranganParts)
        );

        DB::transaction(function () use ($user, $booking, $totalPoin, $sumber, $keterangan) {
            // Cek ulang inside transaction dengan DB lock untuk mencegah race condition
            // double-crediting jika dua proses (misalnya admin verifikasi manual & Midtrans webhook)
            // berjalan bersamaan.
            $alreadyExists = PointHistory::where('booking_id', $booking->id)
                ->where('tipe', 'kredit')
                ->lockForUpdate()
                ->exists();

            if ($alreadyExists) {
                Log::warning("[Loyalty] Double-credit dicegah oleh DB lock untuk Booking #{$booking->id}.");
                return;
            }

            // Update saldo poin (atomic increment) dan dapatkan saldo terbaru
            $user->increment('poin_saldo', $totalPoin);
            $user->increment('poin_bulanan', $totalPoin);
            $user->refresh();

            PointHistory::create([
                'user_id'          => $user->id,
                'booking_id'       => $booking->id,
                'tipe'             => 'kredit',
                'jumlah_poin'      => $totalPoin,
                'poin_saldo_after' => $user->poin_saldo,
                'sumber'           => $sumber,
                'keterangan'       => $keterangan,
                'expired_at'       => now()->addMonths(self::EXPIRY_MONTHS),
                'is_expired'       => false,
            ]);
        });
    }

    // ════════════════════════════════════════════════════════════
    //  KREDIT POIN PAKET MEMBER (Manual oleh Admin)
    // ════════════════════════════════════════════════════════════

    /**
     * Dikreditkan Admin saat mendaftarkan atau memperpanjang paket member pelanggan.
     *
     * @param  User   $user
     * @param  string $jenisPaket  'pagi_siang' | 'malam' | 'weekend'
     * @return int  Jumlah poin yang dikreditkan
     * @throws \InvalidArgumentException
     */
    public function kreditPoinPaketMember(User $user, string $jenisPaket): int
    {
        $mapping = [
            'pagi_siang' => [
                'poin'   => self::POIN_MEMBER_PAGI,
                'sumber' => 'paket_member_pagi_siang',
                'label'  => 'Member Weekdays Pagi/Siang',
            ],
            'malam' => [
                'poin'   => self::POIN_MEMBER_MALAM,
                'sumber' => 'paket_member_malam',
                'label'  => 'Member Weekdays Malam',
            ],
            'weekend' => [
                'poin'   => self::POIN_MEMBER_WEEKEND,
                'sumber' => 'paket_member_weekend',
                'label'  => 'Member Weekend',
            ],
        ];

        if (!isset($mapping[$jenisPaket])) {
            throw new \InvalidArgumentException("Jenis paket member tidak valid: [{$jenisPaket}]. Pilihan: pagi_siang, malam, weekend.");
        }

        $config = $mapping[$jenisPaket];

        DB::transaction(function () use ($user, $config) {
            $user->increment('poin_saldo', $config['poin']);
            $user->increment('poin_bulanan', $config['poin']);
            $user->refresh();

            PointHistory::create([
                'user_id'     => $user->id,
                'booking_id'  => null,
                'tipe'        => 'kredit',
                'jumlah_poin' => $config['poin'],
                'poin_saldo_after' => $user->poin_saldo,
                'sumber'      => $config['sumber'],
                'keterangan'  => "Pendaftaran/Perpanjangan Paket {$config['label']}",
                'expired_at'  => now()->addMonths(self::EXPIRY_MONTHS),
                'is_expired'  => false,
            ]);
        });

        Log::info("[Loyalty] Paket member '{$jenisPaket}': +{$config['poin']} poin → User #{$user->id}");

        return $config['poin'];
    }

    /**
     * Kredit/Debit poin manual oleh admin.
     *
     * @param  User   $user
     * @param  string $tipe         'kredit' | 'debit'
     * @param  int    $jumlahPoin
     * @param  string $keterangan
     * @return void
     * @throws \Exception
     */
    public function sesuaikanPoinManual(User $user, string $tipe, int $jumlahPoin, string $keterangan): void
    {
        if ($jumlahPoin <= 0) {
            throw new \Exception("Jumlah poin harus lebih besar dari 0.");
        }

        if (!in_array($tipe, ['kredit', 'debit'])) {
            throw new \Exception("Tipe penyesuaian tidak valid.");
        }

        $user->refresh();

        if ($tipe === 'debit' && $user->poin_saldo < $jumlahPoin) {
            throw new \Exception("Poin tidak mencukupi untuk melakukan pengurangan. Saldo saat ini: {$user->poin_saldo} poin.");
        }

        DB::transaction(function () use ($user, $tipe, $jumlahPoin, $keterangan) {
            if ($tipe === 'kredit') {
                $user->increment('poin_saldo', $jumlahPoin);
                $user->increment('poin_bulanan', $jumlahPoin);
            } else {
                $user->decrement('poin_saldo', $jumlahPoin);
                // Kurangi poin_bulanan juga, jaga agar tidak kurang dari 0
                $user->update([
                    'poin_bulanan' => max(0, $user->poin_bulanan - $jumlahPoin)
                ]);
            }
            $user->refresh();

            PointHistory::create([
                'user_id'     => $user->id,
                'booking_id'  => null,
                'tipe'        => $tipe,
                'jumlah_poin' => $jumlahPoin,
                'poin_saldo_after' => $user->poin_saldo,
                'sumber'      => 'penyesuaian_manual',
                'keterangan'  => $keterangan ?: ($tipe === 'kredit' ? 'Kredit Poin Manual oleh Admin' : 'Debit Poin Manual oleh Admin'),
                'expired_at'  => $tipe === 'kredit' ? now()->addMonths(self::EXPIRY_MONTHS) : null,
                'is_expired'  => false,
            ]);
        });

        Log::info("[Loyalty] Penyesuaian Manual ({$tipe}): {$jumlahPoin} poin → User #{$user->id}, Keterangan: {$keterangan}");
    }

    // ════════════════════════════════════════════════════════════
    //  PENUKARAN POIN (REDEMPTION)
    // ════════════════════════════════════════════════════════════

    /**
     * Proses penukaran poin pelanggan menjadi voucher hadiah.
     *
     * Flow:
     * 1. Validasi jenis hadiah & cek saldo poin mencukupi
     * 2. Debit poin di points_history
     * 3. Kurangi users.poin_saldo
     * 4. Buat record di redemptions dengan kode UUID unik
     *
     * @param  User   $user
     * @param  string $jenisHadiah  Key dari const REDEEM
     * @return Redemption           Objek voucher yang baru dibuat
     * @throws \Exception           Jika poin tidak cukup atau jenis tidak valid
     */
    public function tukarPoin(User $user, string $jenisHadiah): Redemption
    {
        if (!isset(self::REDEEM[$jenisHadiah])) {
            throw new \Exception("Jenis hadiah tidak valid: [{$jenisHadiah}].");
        }

        $config         = self::REDEEM[$jenisHadiah];
        $poinDibutuhkan = $config['poin'];
        $label          = $config['label'];

        // Validasi: hadiah khusus member hanya bisa ditukar oleh pelanggan berstatus member aktif
        if (!empty($config['member_only']) && !$user->isMember()) {
            throw new \Exception(
                "Hadiah ini hanya tersedia untuk pelanggan yang telah menjadi Member aktif. Silakan daftar member terlebih dahulu."
            );
        }

        // Re-read saldo dari DB (bukan dari cache model) untuk keamanan
        $user->refresh();

        if ($user->poin_saldo < $poinDibutuhkan) {
            throw new \Exception(
                "Poin tidak mencukupi. Dibutuhkan {$poinDibutuhkan} poin, saldo Anda saat ini: {$user->poin_saldo} poin."
            );
        }

        return DB::transaction(function () use ($user, $jenisHadiah, $poinDibutuhkan, $label) {
            // 1. Kurangi saldo poin (atomic decrement)
            $user->decrement('poin_saldo', $poinDibutuhkan);

            // Safety net: pastikan tidak pernah negatif
            if ($user->fresh()->poin_saldo < 0) {
                throw new \Exception("Terjadi konflik data. Silakan coba kembali.");
            }
            $user->refresh();

            // 2. Catat debit di points_history
            PointHistory::create([
                'user_id'     => $user->id,
                'booking_id'  => null,
                'tipe'        => 'debit',
                'jumlah_poin' => $poinDibutuhkan,
                'poin_saldo_after' => $user->poin_saldo,
                'sumber'      => 'penukaran_' . $jenisHadiah,
                'keterangan'  => "Penukaran Poin: {$label}",
                'expired_at'  => null,
                'is_expired'  => false,
            ]);

            // 3. Buat voucher penukaran
            $redemption = Redemption::create([
                'user_id'         => $user->id,
                'jenis_hadiah'    => $jenisHadiah,
                'poin_digunakan'  => $poinDibutuhkan,
                'kode_voucher'    => Str::uuid()->toString(),
                'status'          => 'aktif',
                'kode_expired_at' => now()->addDays(30),
            ]);

            Log::info("[Loyalty] Penukaran: {$jenisHadiah} (-{$poinDibutuhkan} poin) → User #{$user->id}, Kode: {$redemption->kode_voucher}");

            return $redemption;
        });
    }

    // ════════════════════════════════════════════════════════════
    //  HELPER: Ambil Data untuk View
    // ════════════════════════════════════════════════════════════

    /**
     * Ambil riwayat poin terbaru untuk halaman profil pelanggan.
     */
    public function getRiwayatPoin(User $user, int $perPage = 15)
    {
        return PointHistory::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Ambil daftar voucher aktif milik user.
     */
    public function getVoucherAktif(User $user)
    {
        return Redemption::where('user_id', $user->id)
            ->where('status', 'aktif')
            ->where('kode_expired_at', '>', now())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Hitung total poin yang diperoleh user pada bulan & tahun tertentu.
     * Digunakan oleh cron job segmentasi.
     *
     * @param  User $user
     * @param  int  $bulan  1-12
     * @param  int  $tahun  e.g. 2026
     * @return int
     */
    public function getPoinBulanTertentu(User $user, int $bulan, int $tahun): int
    {
        return (int) PointHistory::where('user_id', $user->id)
            ->where('tipe', 'kredit')
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->sum('jumlah_poin');
    }

    /**
     * Tentukan label segmen berdasarkan total poin bulanan.
     */
    public function tentukanSegmen(int $poin): string
    {
        return match(true) {
            $poin > 250  => 'vip',
            $poin >= 150 => 'loyalist',
            $poin >= 80  => 'partner',
            $poin >= 30  => 'ally',
            default      => 'visitor',
        };
    }

    /**
     * Cek peningkatan status pelanggan di akhir bulan.
     * Menggunakan poin_bulanan pelanggan saat ini.
     *
     * @param  User $user
     * @return void
     */
    public function checkStatusUpgrade(User $user): void
    {
        $poinBulanan = $user->poin_bulanan;
        $segmenBaru = $this->tentukanSegmen($poinBulanan);
        $segmenLama = $user->segmen_pelanggan;

        // Ambil urutan segmen untuk menentukan apakah ini sebuah "kenaikan" (upgrade)
        $order = ['visitor' => 1, 'ally' => 2, 'partner' => 3, 'loyalist' => 4, 'vip' => 5];
        $isUpgrade = isset($order[$segmenBaru]) && isset($order[$segmenLama]) && ($order[$segmenBaru] > $order[$segmenLama]);

        // Selalu update segmen_pelanggan di database
        $user->update([
            'segmen_pelanggan'  => $segmenBaru,
            'segmen_updated_at' => now(),
        ]);

        // Berikan reward jika baru naik status
        if ($isUpgrade) {
            $this->grantAccumulativeRewards($user, $segmenBaru);
        }
    }

    /**
     * Berikan voucher reward secara akumulatif.
     * Ketika pelanggan naik status, mereka mendapatkan reward status tersebut ditambah
     * reward level di bawahnya yang belum pernah mereka klaim di periode bulan berjalan ini.
     *
     * @param  User   $user
     * @param  string $newStatus
     * @return void
     */
    public function grantAccumulativeRewards(User $user, string $newStatus): void
    {
        $statusTiers = ['ally', 'partner', 'loyalist', 'vip'];
        $order = ['ally' => 1, 'partner' => 2, 'loyalist' => 3, 'vip' => 4];

        // Cari tahu batas maksimal reward yang didapatkan
        if (!isset($order[$newStatus])) {
            return; // Visitor tidak mendapat reward
        }
        $maxIndex = $order[$newStatus];

        // Loop dari ally sampai status baru
        for ($i = 0; $i < $maxIndex; $i++) {
            $tier = $statusTiers[$i];

            // Cek apakah user sudah memiliki voucher dengan tipe_voucher ini yang diterbitkan di bulan berjalan ini
            $sudahDapat = \App\Models\Voucher::where('user_id', $user->id)
                ->where('tipe_voucher', $tier)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->exists();

            if (!$sudahDapat) {
                // Terbitkan voucher baru
                $code = strtoupper($tier) . '-' . strtoupper(Str::random(8));
                
                // VIP berlaku 14 hari, tier lain 30 hari
                $expiredDays = ($tier === 'vip') ? 14 : 30;

                \App\Models\Voucher::create([
                    'user_id'      => $user->id,
                    'voucher_code' => $code,
                    'tipe_voucher' => $tier,
                    'status'       => 'aktif',
                    'expired_date' => now()->addDays($expiredDays),
                ]);

                Log::info("[Loyalty Reward] Terbitkan voucher {$code} ({$tier}) untuk User #{$user->id} ({$user->name})");
            }
        }
    }

    /**
     * Membatalkan/mendebit kembali poin yang pernah dikreditkan dari booking yang dibatalkan/dihapus.
     *
     * @param  Booking $booking
     * @return int  Total poin yang didebit/dikurangi (0 jika tidak ada)
     */
    public function debitPoinDariBatalBooking(Booking $booking): int
    {
        if (!$booking->user_id) {
            return 0;
        }

        $user = User::find($booking->user_id);
        if (!$user) {
            return 0;
        }

        // Cek total poin yang pernah dikreditkan untuk booking ini
        $kreditList = PointHistory::where('booking_id', $booking->id)
            ->where('tipe', 'kredit')
            ->get();

        if ($kreditList->isEmpty()) {
            return 0;
        }

        // Hitung total poin yang pernah didebit sebelumnya untuk pembatalan booking ini (jika ada, cegah double-debit)
        $sudahDidebit = PointHistory::where('booking_id', $booking->id)
            ->where('tipe', 'debit')
            ->where('sumber', 'pembatalan_booking')
            ->exists();

        if ($sudahDidebit) {
            return 0;
        }

        $totalPoin = $kreditList->sum('jumlah_poin');

        if ($totalPoin > 0) {
            DB::transaction(function () use ($user, $booking, $totalPoin) {
                // Kurangi saldo poin (tidak boleh kurang dari 0)
                $user->decrement('poin_saldo', $totalPoin);
                // Sesuaikan poin bulanan (tidak boleh kurang dari 0)
                $user->update([
                    'poin_bulanan' => max(0, $user->poin_bulanan - $totalPoin),
                    'poin_saldo'   => max(0, $user->poin_saldo) // Safeguard decrement
                ]);
                $user->refresh();

                PointHistory::create([
                    'user_id'          => $user->id,
                    'booking_id'       => $booking->id,
                    'tipe'             => 'debit',
                    'jumlah_poin'      => $totalPoin,
                    'poin_saldo_after' => $user->poin_saldo,
                    'sumber'           => 'pembatalan_booking',
                    'keterangan'       => "Pembatalan/Refund Booking #{$booking->id} — Poin ditarik kembali",
                    'expired_at'       => null,
                    'is_expired'       => false,
                ]);
            });

            Log::info("[Loyalty] Booking #{$booking->id} Dibatalkan/Dihapus: -{$totalPoin} poin ditarik kembali dari User #{$user->id}");
        }

        return $totalPoin;
    }
}
