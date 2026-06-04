<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Lapangan;
use App\Models\Jadwal;
use App\Models\Booking;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class BookingProcessTest extends TestCase
{
    use RefreshDatabase;

    private $pelanggan;
    private $pelanggan2;
    private $lapangan;
    private $jadwal;

    protected function setUp(): void
    {
        parent::setUp();

        $this->pelanggan = User::create([
            'name' => 'Pelanggan 1',
            'email' => 'p1@test.com',
            'password' => bcrypt('password'),
            'role' => 'pelanggan',
        ]);

        $this->pelanggan2 = User::create([
            'name' => 'Pelanggan 2',
            'email' => 'p2@test.com',
            'password' => bcrypt('password'),
            'role' => 'pelanggan',
        ]);

        $this->lapangan = Lapangan::create([
            'nama_lapangan' => 'Lapangan Test',
            'harga_weekday' => 55000,
            'harga_weekend' => 60000,
            'status' => 'aktif',
        ]);

        $this->jadwal = Jadwal::create([
            'lapangan_id' => $this->lapangan->id,
            'tanggal' => Carbon::tomorrow(),
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
            'status' => 'tersedia',
        ]);
    }

    public function test_