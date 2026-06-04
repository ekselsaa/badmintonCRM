<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Lapangan;
use App\Models\Jadwal;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin
        User::create([
            'name'     => 'Administrator',
            'email'    => 'admin@anbiyaasport.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        // 2. Pelanggan
        $pelanggan = [
            ['name' => 'Budi Santoso', 'email' => 'budi@mail.com', 'nomor_hp' => '081234567890', 'alamat' => 'Jl. Mawar', 'kategori_member' => 'member'],
            ['name' => 'Siti Rahayu',  'email' => 'siti@mail.com', 'nomor_hp' => '082345678901', 'alamat' => 'Jl. Melati', 'kategori_member' => 'non-member'],
        ];

        foreach ($pelanggan as $p) {
            User::create(array_merge($p, [
                'password' => Hash::make('password'),
                'role'     => 'pelanggan',
            ]));
        }

        // 3. Lapangan
        $lapangans = [
            ['nama_lapangan' => 'Lapangan 1', 'deskripsi' => 'Karpet premium badminton, pencahayaan LED, Kipas angin', 'harga_weekday' => 55000, 'harga_weekend' => 60000, 'status' => 'aktif'],
            ['nama_lapangan' => 'Lapangan 2', 'deskripsi' => 'Karpet premium badminton, pencahayaan LED, Kipas angin', 'harga_weekday' => 55000, 'harga_weekend' => 60000, 'status' => 'aktif'],
            ['nama_lapangan' => 'Lapangan 3', 'deskripsi' => 'Karpet premium badminton, pencahayaan LED, Kipas angin', 'harga_weekday' => 55000, 'harga_weekend' => 60000, 'status' => 'aktif'],
        ];

        foreach ($lapangans as $l) {
            Lapangan::create($l);
        }

        // 4. Jadwal (7 hari ke depan)
        $jamSlot = [
            ['08:00', '09:00'], ['09:00', '10:00'], ['10:00', '11:00'],
            ['15:00', '16:00'], ['16:00', '17:00'], ['19:00', '20:00'],
            ['20:00', '21:00'], ['21:00', '22:00']
        ];

        for ($hari = 0; $hari <= 7; $hari++) {
            $tanggal = Carbon::today()->addDays($hari)->format('Y-m-d');
            foreach (Lapangan::all() as $lapangan) {
                foreach ($jamSlot as $jam) {
                    Jadwal::create([
                        'lapangan_id' => $lapangan->id,
                        'tanggal'     => $tanggal,
                        'jam_mulai'   => $jam[0],
                        'jam_selesai' => $jam[1],
                        'status'      => 'tersedia',
                    ]);
                }
            }
        }
    }
}
