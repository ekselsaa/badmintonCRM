<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAccessTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->pelanggan = User::create([
            'name' => 'Pelanggan Test',
            'email' => 'pelanggan@test.com',
            'password' => bcrypt('password'),
            'role' => 'pelanggan',
        ]);
    }

    public function test_guest_cannot_access_dashboard()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');

        $response = $this->get('/booking');
        $response->assertRedirect('/login');
    }

    public function test_pelanggan_cannot_access_admin_pages()
    {
        $response = $this->actingAs($this->pelanggan)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_pelanggan_pages()
    {
        $response = $this->actingAs($this->admin)->get('/booking');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_dashboard()
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_pelanggan_can_access_booking_page()
    {
        $response = $this->actingAs($this->pelanggan)->get('/booking');
        $response->assertStatus(200);
    }

    public function test_admin_can_check_pending_verifications_count()
    {
        $lapangan = \App\Models\Lapangan::create([
            'nama_lapangan' => 'Lapangan Test Admin',
            'harga_weekday' => 50000,
            'harga_weekend' => 60000,
            'status' => 'aktif',
        ]);

        $jadwal = \App\Models\Jadwal::create([
            'lapangan_id' => $lapangan->id,
            'tanggal' => \Carbon\Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
            'status' => 'tersedia',
        ]);

        $booking = \App\Models\Booking::create([
            'user_id' => $this->pelanggan->id,
            'jadwal_id' => $jadwal->id,
            'lapangan_id' => $lapangan->id,
            'tanggal_booking' => \Carbon\Carbon::tomorrow()->format('Y-m-d'),
            'total_harga' => 50000,
            'status' => 'pending',
        ]);

        \App\Models\Pembayaran::create([
            'booking_id' => $booking->id,
            'bukti_pembayaran' => 'bukti_test.jpg',
            'jumlah_bayar' => 50000,
            'metode_pembayaran' => 'transfer',
            'status_verifikasi' => 'menunggu',
        ]);

        \App\Models\MembershipPayment::create([
            'user_id' => $this->pelanggan->id,
            'paket' => 'Bulanan',
            'jumlah_bayar' => 150000,
            'metode_pembayaran' => 'qris',
            'bukti_pembayaran' => 'bukti_member.jpg',
            'status_verifikasi' => 'menunggu',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/api/pending-verif');

        $response->assertStatus(200);
        $response->assertJson([
            'pending_count' => 2,
            'booking_count' => 1,
            'membership_count' => 1,
        ]);
    }
}
