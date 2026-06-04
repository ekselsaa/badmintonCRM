<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Jadwal;
use App\Models\Lapangan;
use App\Models\MembershipPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===================================================
        // 1. ADMIN
        // ===================================================
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@anbiyaasport.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // ===================================================
        // 2. LAPANGAN
        // ===================================================
        $lap1 = Lapangan::create(['nama_lapangan' => 'Lapangan 1', 'deskripsi' => 'Karpet premium badminton, pencahayaan LED, Kipas angin', 'harga_weekday' => 55000, 'harga_weekend' => 60000, 'status' => 'aktif']);
        $lap2 = Lapangan::create(['nama_lapangan' => 'Lapangan 2', 'deskripsi' => 'Karpet premium badminton, pencahayaan LED, Kipas angin', 'harga_weekday' => 55000, 'harga_weekend' => 60000, 'status' => 'aktif']);
        $lap3 = Lapangan::create(['nama_lapangan' => 'Lapangan 3', 'deskripsi' => 'Karpet premium badminton, pencahayaan LED, Kipas angin', 'harga_weekday' => 55000, 'harga_weekend' => 60000, 'status' => 'aktif']);

        // ===================================================
        // 3. PELANGGAN MEMBER
        // ===================================================

        // -- Member A: Budi Santoso (Weekday Malam – Senin 18:00-21:00, Lapangan 1)
        $budi = User::create([
            'name'             => 'Budi Santoso',
            'email'            => 'budi@mail.com',
            'password'         => Hash::make('password'),
            'role'             => 'pelanggan',
            'nomor_hp'         => '081234567890',
            'alamat'           => 'Jl. Mawar No. 5, Bekasi',
            'kategori_member'  => 'weekday_malam',
            'poin_saldo'       => 0,
            'poin_bulanan'     => 0,
            'segmen_pelanggan' => 'loyalist',
        ]);
        $budiPayment = MembershipPayment::create([
            'user_id'           => $budi->id,
            'paket'             => 'weekday_malam',
            'jumlah_bayar'      => 500000,
            'metode_pembayaran' => 'qris',
            'bukti_pembayaran'  => 'membership_payments/dummy_bukti.png',
            'status_verifikasi' => 'diverifikasi',
            'verified_at'       => now()->subMonth(),
            'hari'              => 'senin',
            'jam_mulai'         => '18:00',
            'jam_selesai'       => '21:00',
            'lapangan_id'       => $lap1->id,
        ]);

        // -- Member B: Ahmad Fauzi (Weekday Pagi – Rabu 08:00-11:00, Lapangan 2)
        $ahmad = User::create([
            'name'             => 'Ahmad Fauzi',
            'email'            => 'ahmad@mail.com',
            'password'         => Hash::make('password'),
            'role'             => 'pelanggan',
            'nomor_hp'         => '083456789012',
            'alamat'           => 'Jl. Kebon Jeruk No. 12, Jakarta Barat',
            'kategori_member'  => 'weekday_pagi',
            'poin_saldo'       => 0,
            'poin_bulanan'     => 0,
            'segmen_pelanggan' => 'loyalist',
        ]);
        $ahmadPayment = MembershipPayment::create([
            'user_id'           => $ahmad->id,
            'paket'             => 'weekday_pagi',
            'jumlah_bayar'      => 500000,
            'metode_pembayaran' => 'transfer',
            'bukti_pembayaran'  => 'membership_payments/dummy_bukti.png',
            'status_verifikasi' => 'diverifikasi',
            'verified_at'       => now()->subWeeks(2),
            'hari'              => 'rabu',
            'jam_mulai'         => '08:00',
            'jam_selesai'       => '11:00',
            'lapangan_id'       => $lap2->id,
        ]);

        // -- Member C: Dewi Lestari (Weekend – Sabtu 08:00-11:00, Lapangan 1)
        $dewi = User::create([
            'name'             => 'Dewi Lestari',
            'email'            => 'dewi@mail.com',
            'password'         => Hash::make('password'),
            'role'             => 'pelanggan',
            'nomor_hp'         => '084567890123',
            'alamat'           => 'Jl. Anggrek No. 3, Tangerang Selatan',
            'kategori_member'  => 'weekend',
            'poin_saldo'       => 0,
            'poin_bulanan'     => 0,
            'segmen_pelanggan' => 'loyalist',
        ]);
        $dewiPayment = MembershipPayment::create([
            'user_id'           => $dewi->id,
            'paket'             => 'weekend',
            'jumlah_bayar'      => 600000,
            'metode_pembayaran' => 'qris',
            'bukti_pembayaran'  => 'membership_payments/dummy_bukti.png',
            'status_verifikasi' => 'diverifikasi',
            'verified_at'       => now()->subWeeks(3),
            'hari'              => 'sabtu',
            'jam_mulai'         => '08:00',
            'jam_selesai'       => '11:00',
            'lapangan_id'       => $lap1->id,
        ]);

        // ===================================================
        // 4. BUAT SLOT MEMBER (Jadwal Terblokir + Booking Rutin)
        //    Dibuat untuk 4 minggu ke depan
        // ===================================================
        $memberConfig = [
            ['user' => $budi,  'payment' => $budiPayment,  'lapangan' => $lap1, 'day' => 'Monday',    'mulai' => '18:00', 'selesai' => '21:00', 'paket' => 'Weekday Malam'],
            ['user' => $ahmad, 'payment' => $ahmadPayment, 'lapangan' => $lap2, 'day' => 'Wednesday', 'mulai' => '08:00', 'selesai' => '11:00', 'paket' => 'Weekday Pagi'],
            ['user' => $dewi,  'payment' => $dewiPayment,  'lapangan' => $lap1, 'day' => 'Saturday',  'mulai' => '08:00', 'selesai' => '11:00', 'paket' => 'Weekend'],
        ];

        foreach ($memberConfig as $cfg) {
            $nextDate = Carbon::today()->next($cfg['day']);
            for ($w = 0; $w < 4; $w++) {
                $dateStr = $nextDate->format('Y-m-d');

                $jadwal = Jadwal::updateOrCreate(
                    ['lapangan_id' => $cfg['lapangan']->id, 'tanggal' => $dateStr, 'jam_mulai' => $cfg['mulai']],
                    ['jam_selesai' => $cfg['selesai'], 'status' => 'dipesan', 'keterangan' => 'Slot Member: ' . $cfg['user']->name]
                );

                $booking = Booking::create([
                    'user_id'         => $cfg['user']->id,
                    'jadwal_id'       => $jadwal->id,
                    'lapangan_id'     => $cfg['lapangan']->id,
                    'tanggal_booking' => $dateStr,
                    'total_harga'     => 0,
                    'status'          => 'dipesan',
                    'catatan'         => 'Sesi Rutin Member (Paket ' . $cfg['paket'] . ')',
                ]);

                $booking->pembayaran()->create([
                    'jumlah_bayar'      => 0,
                    'metode_pembayaran' => 'qris',
                    'status_verifikasi' => 'diverifikasi',
                    'catatan_admin'     => 'Auto-generated dari verifikasi membership #' . $cfg['payment']->id,
                    'verified_at'       => now(),
                ]);

                $nextDate->addWeek();
            }
        }

        // ===================================================
        // 5. PELANGGAN NON-MEMBER
        // ===================================================
        $siti = User::create([
            'name'             => 'Siti Rahayu',
            'email'            => 'siti@mail.com',
            'password'         => Hash::make('password'),
            'role'             => 'pelanggan',
            'nomor_hp'         => '082345678901',
            'alamat'           => 'Jl. Melati No. 8, Depok',
            'kategori_member'  => 'non-member',
            'poin_saldo'       => 150,
            'poin_bulanan'     => 60,
            'segmen_pelanggan' => 'partner',
        ]);

        $rudi = User::create([
            'name'             => 'Rudi Hartono',
            'email'            => 'rudi@mail.com',
            'password'         => Hash::make('password'),
            'role'             => 'pelanggan',
            'nomor_hp'         => '085678901234',
            'alamat'           => 'Jl. Cempaka No. 2, Bogor',
            'kategori_member'  => 'non-member',
            'poin_saldo'       => 80,
            'poin_bulanan'     => 30,
            'segmen_pelanggan' => 'ally',
        ]);

        $maya = User::create([
            'name'             => 'Maya Sari',
            'email'            => 'maya@mail.com',
            'password'         => Hash::make('password'),
            'role'             => 'pelanggan',
            'nomor_hp'         => '086789012345',
            'alamat'           => 'Jl. Dahlia No. 15, Jakarta Selatan',
            'kategori_member'  => 'non-member',
            'poin_saldo'       => 220,
            'poin_bulanan'     => 80,
            'segmen_pelanggan' => 'partner',
        ]);

        $hendro = User::create([
            'name'             => 'Hendro Wijaya',
            'email'            => 'hendro@mail.com',
            'password'         => Hash::make('password'),
            'role'             => 'pelanggan',
            'nomor_hp'         => '087890123456',
            'alamat'           => 'Jl. Gambir No. 7, Jakarta Pusat',
            'kategori_member'  => 'non-member',
            'poin_saldo'       => 40,
            'poin_bulanan'     => 0,
            'segmen_pelanggan' => 'visitor',
        ]);

        $eka = User::create([
            'name'             => 'Eka Putri',
            'email'            => 'eka@mail.com',
            'password'         => Hash::make('password'),
            'role'             => 'pelanggan',
            'nomor_hp'         => '088901234567',
            'alamat'           => 'Jl. Flamboyan No. 1, Bekasi Utara',
            'kategori_member'  => 'non-member',
            'poin_saldo'       => 20,
            'poin_bulanan'     => 20,
            'segmen_pelanggan' => 'visitor',
        ]);

        // ===================================================
        // 6. RIWAYAT BOOKING MASA LALU (status: selesai)
        // ===================================================
        $past = function (User $user, Lapangan $lap, int $daysAgo, string $jamMulai, string $jamSelesai, int $harga, ?int $rating = null, ?string $ulasan = null) {
            $date = Carbon::today()->subDays($daysAgo)->format('Y-m-d');

            $jadwal = Jadwal::create([
                'lapangan_id' => $lap->id,
                'tanggal'     => $date,
                'jam_mulai'   => $jamMulai,
                'jam_selesai' => $jamSelesai,
                'status'      => 'dipesan',
            ]);

            $booking = Booking::create([
                'user_id'           => $user->id,
                'jadwal_id'         => $jadwal->id,
                'lapangan_id'       => $lap->id,
                'tanggal_booking'   => $date,
                'total_harga'       => $harga,
                'status'            => 'selesai',
                'rating'            => $rating,
                'ulasan'            => $ulasan,
                'is_tampil_beranda' => ($rating !== null && $rating >= 4) ? 1 : 0,
            ]);

            $booking->pembayaran()->create([
                'jumlah_bayar'      => $harga,
                'metode_pembayaran' => 'qris',
                'status_verifikasi' => 'diverifikasi',
                'verified_at'       => Carbon::today()->subDays($daysAgo - 1),
            ]);
        };

        // Siti Rahayu – pelanggan setia, 6 booking dengan beberapa ulasan bagus
        $past($siti, $lap1,  7, '08:00', '09:00', 55000, 5, 'Lapangan bersih dan nyaman, pelayanan sangat ramah!');
        $past($siti, $lap2, 14, '10:00', '11:00', 55000, 4, 'Oke banget tempatnya, pasti balik lagi.');
        $past($siti, $lap1, 21, '16:00', '17:00', 55000, 5, 'Fasilitas lengkap, harga terjangkau. Recommended!');
        $past($siti, $lap3, 28, '09:00', '10:00', 55000, 4, 'Lapangan bagus, tidak licin, enak buat rally.');
        $past($siti, $lap2, 35, '15:00', '16:00', 55000);
        $past($siti, $lap1, 42, '08:00', '09:00', 55000);

        // Rudi Hartono – pelanggan biasa, 3 booking
        $past($rudi, $lap2, 10, '19:00', '20:00', 55000, 4, 'Lapangan enak, lampu sangat terang. Cocok untuk malam hari.');
        $past($rudi, $lap1, 25, '20:00', '21:00', 55000);
        $past($rudi, $lap3, 40, '09:00', '10:00', 55000, 3, 'Cukup baik, namun area parkir agak sempit.');

        // Maya Sari – pelanggan setia, 5 booking dengan banyak ulasan positif
        $past($maya, $lap1,  5, '10:00', '11:00', 55000, 5, 'Sangat puas! Lapangan dalam kondisi prima, sejuk 😍');
        $past($maya, $lap2, 12, '08:00', '09:00', 55000, 5, 'Top banget! Selalu jadi pilihan utama saya untuk olahraga.');
        $past($maya, $lap1, 19, '09:00', '10:00', 55000, 4, 'Suka tempatnya, proses booking online juga mudah.');
        $past($maya, $lap3, 26, '16:00', '17:00', 55000, 5, 'Lapangan 3 kondisinya bagus dan bersih, lanjut terus!');
        $past($maya, $lap1, 33, '10:00', '11:00', 55000);

        // Hendro Wijaya – pelanggan jarang, 1 booking sudah lama (at risk)
        $past($hendro, $lap1, 90, '15:00', '16:00', 55000, 3, 'Lumayan, tapi bisa ditingkatkan lagi kualitasnya.');

        // Eka Putri – pelanggan baru, 1 booking perdana
        $past($eka, $lap2, 3, '08:00', '09:00', 55000, 5, 'Pertama kali main di sini dan langsung suka! Pasti balik lagi.');

        // ===================================================
        // 7. JADWAL TERSEDIA (14 hari ke depan)
        //    HANYA dibuat jika TIDAK overlap dengan slot member
        // ===================================================
        $jamSlots = [
            ['07:00', '08:00'], ['08:00', '09:00'], ['09:00', '10:00'],
            ['10:00', '11:00'], ['11:00', '12:00'], ['12:00', '13:00'],
            ['13:00', '14:00'], ['14:00', '15:00'], ['15:00', '16:00'],
            ['16:00', '17:00'], ['17:00', '18:00'], ['18:00', '19:00'],
            ['19:00', '20:00'], ['20:00', '21:00'], ['21:00', '22:00'],
        ];

        for ($day = 0; $day <= 14; $day++) {
            $tanggal = Carbon::today()->addDays($day)->format('Y-m-d');

            foreach (Lapangan::all() as $lapangan) {
                // Ambil semua slot member (dipesan) untuk lapangan & tanggal ini
                $memberBlocks = Jadwal::where('lapangan_id', $lapangan->id)
                    ->whereDate('tanggal', $tanggal)
                    ->whereIn('status', ['dipesan', 'ditutup'])
                    ->get(['jam_mulai', 'jam_selesai']);

                foreach ($jamSlots as [$mulai, $selesai]) {
                    // Cek apakah slot ini overlap dengan slot member
                    $isBlocked = $memberBlocks->first(function ($block) use ($mulai, $selesai) {
                        return $block->jam_mulai < $selesai && $block->jam_selesai > $mulai;
                    });

                    if (!$isBlocked) {
                        Jadwal::firstOrCreate(
                            ['lapangan_id' => $lapangan->id, 'tanggal' => $tanggal, 'jam_mulai' => $mulai],
                            ['jam_selesai' => $selesai, 'status' => 'tersedia']
                        );
                    }
                }
            }
        }
    }
}
