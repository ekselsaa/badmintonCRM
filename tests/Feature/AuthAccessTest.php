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
            'username' => 'admin',
            'password' => bcrypt('admin'),
            'role' => 'admin',
            'nomor_hp' => '081234567890',
        ]);

        $this->pelanggan = User::create([
            'name' => 'Pelanggan Test',
            'username' => 'pelanggan',
            'password' => bcrypt('password'),
            'role' => 'pelanggan',
            'nomor_hp' => '089876543210',
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

    public function test_user_can_login_with_username()
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_user_can_login_with_nomor_hp()
    {
        // Test with unnormalized phone number input
        $response = $this->post('/login', [
            'username' => '089876543210',
            'password' => 'password',
        ]);

        $response->assertRedirect('/booking');
        $this->assertAuthenticatedAs($this->pelanggan);
    }

    public function test_membership_verifikasi_sets_expiry_date()
    {
        $lapangan = \App\Models\Lapangan::create([
            'nama_lapangan' => 'Lapangan Test Member',
            'harga_weekday' => 50000,
            'harga_weekend' => 60000,
            'status' => 'aktif',
        ]);

        $payment = \App\Models\MembershipPayment::create([
            'user_id' => $this->pelanggan->id,
            'paket' => 'weekday_pagi',
            'jumlah_bayar' => 150000,
            'metode_pembayaran' => 'transfer',
            'bukti_pembayaran' => 'bukti.jpg',
            'status_verifikasi' => 'menunggu',
            'hari' => 'senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '09:00:00',
            'lapangan_id' => $lapangan->id,
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/pembayaran-membership/{$payment->id}/verif", [
            'status_verifikasi' => 'diverifikasi',
            'catatan_admin' => 'Approved',
        ]);

        $response->assertRedirect();
        
        $this->pelanggan->refresh();
        $this->assertEquals('weekday_pagi', $this->pelanggan->kategori_member);
        $this->assertNotNull($this->pelanggan->membership_expires_at);
        $this->assertTrue($this->pelanggan->membership_expires_at->isFuture());
        // Harusnya bersisa sekitar 30 hari
        $this->assertEquals(30, $this->pelanggan->sisaHariAktifMember());

        // Uji coba perpanjangan dini: masa aktif harus diperpanjang 30 hari lagi (total 60 hari)
        $payment2 = \App\Models\MembershipPayment::create([
            'user_id' => $this->pelanggan->id,
            'paket' => 'weekday_pagi',
            'jumlah_bayar' => 150000,
            'metode_pembayaran' => 'transfer',
            'bukti_pembayaran' => 'bukti2.jpg',
            'status_verifikasi' => 'menunggu',
            'hari' => 'senin',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '09:00:00',
            'lapangan_id' => $lapangan->id,
        ]);

        $response2 = $this->actingAs($this->admin)->put("/admin/pembayaran-membership/{$payment2->id}/verif", [
            'status_verifikasi' => 'diverifikasi',
            'catatan_admin' => 'Approved 2',
        ]);

        $this->pelanggan->refresh();
        $this->assertEquals(60, $this->pelanggan->sisaHariAktifMember());
    }

    public function test_membership_check_expiry_command()
    {
        $lapangan = \App\Models\Lapangan::create([
            'nama_lapangan' => 'Lapangan C',
            'harga_weekday' => 50000,
            'harga_weekend' => 60000,
            'status' => 'aktif',
        ]);

        // 1. User dengan member aktif di masa depan
        $memberActive = User::create([
            'name' => 'Active Member',
            'username' => 'active_member',
            'password' => bcrypt('password'),
            'role' => 'pelanggan',
            'nomor_hp' => '081111111111',
            'kategori_member' => 'weekday_pagi',
            'membership_expires_at' => \Carbon\Carbon::now()->addDays(5),
        ]);

        // 2. User dengan member yang sudah lewat masa aktif (expired)
        $memberExpired = User::create([
            'name' => 'Expired Member',
            'username' => 'expired_member',
            'password' => bcrypt('password'),
            'role' => 'pelanggan',
            'nomor_hp' => '082222222222',
            'kategori_member' => 'weekday_pagi',
            'membership_expires_at' => \Carbon\Carbon::now()->subDays(1),
        ]);

        // Buat jadwal dan booking masa depan untuk member expired
        $jadwalMasaDepan = \App\Models\Jadwal::create([
            'lapangan_id' => $lapangan->id,
            'tanggal' => \Carbon\Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '09:00:00',
            'status' => 'dipesan',
            'keterangan' => 'Slot Member: Expired Member',
        ]);

        $bookingMasaDepan = \App\Models\Booking::create([
            'user_id' => $memberExpired->id,
            'jadwal_id' => $jadwalMasaDepan->id,
            'lapangan_id' => $lapangan->id,
            'tanggal_booking' => \Carbon\Carbon::tomorrow()->format('Y-m-d'),
            'total_harga' => 150000,
            'status' => 'dipesan',
            'catatan' => 'Sesi Rutin Member (Paket weekday_pagi)',
        ]);

        // Jalankan perintah artisan
        $this->artisan('membership:check-expiry')
             ->expectsOutput('Memulai pengecekan masa aktif member...')
             ->expectsOutput("User ID: {$memberExpired->id} | Nama: {$memberExpired->name} status member telah kedaluwarsa.")
             ->assertExitCode(0);

        $memberActive->refresh();
        $memberExpired->refresh();
        $jadwalMasaDepan->refresh();
        $bookingMasaDepan->refresh();

        $this->assertEquals('weekday_pagi', $memberActive->kategori_member);
        $this->assertNotNull($memberActive->membership_expires_at);

        $this->assertEquals('non-member', $memberExpired->kategori_member);
        $this->assertNull($memberExpired->membership_expires_at);

        // Jadwal masa depan harus terbebas
        $this->assertEquals('tersedia', $jadwalMasaDepan->status);
        $this->assertNull($jadwalMasaDepan->keterangan);

        // Booking masa depan harus dibatalkan
        $this->assertEquals('dibatalkan', $bookingMasaDepan->status);
    }
}
