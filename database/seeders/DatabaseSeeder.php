<?php

namespace Database\Seeders;

use App\Models\Jadwal;
use App\Models\Lapangan;
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
            'username' => 'admin',
            'password' => Hash::make('admin'),
            'role'     => 'admin',
        ]);

        // ===================================================
        // 2. LAPANGAN
        // ===================================================
        Lapangan::create(['nama_lapangan' => 'Lapangan 1', 'deskripsi' => 'Karpet premium badminton, pencahayaan LED, Kipas angin', 'harga_weekday' => 55000, 'harga_weekend' => 60000, 'status' => 'aktif']);
        Lapangan::create(['nama_lapangan' => 'Lapangan 2', 'deskripsi' => 'Karpet premium badminton, pencahayaan LED, Kipas angin', 'harga_weekday' => 55000, 'harga_weekend' => 60000, 'status' => 'aktif']);
        Lapangan::create(['nama_lapangan' => 'Lapangan 3', 'deskripsi' => 'Karpet premium badminton, pencahayaan LED, Kipas angin', 'harga_weekday' => 55000, 'harga_weekend' => 60000, 'status' => 'aktif']);

        // ===================================================
        // 3. JADWAL TERSEDIA (14 hari ke depan)
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
                foreach ($jamSlots as [$mulai, $selesai]) {
                    Jadwal::firstOrCreate(
                        ['lapangan_id' => $lapangan->id, 'tanggal' => $tanggal, 'jam_mulai' => $mulai],
                        ['jam_selesai' => $selesai, 'status' => 'tersedia']
                    );
                }
            }
        }

        $this->call(FasilitasSeeder::class);
    }
}
