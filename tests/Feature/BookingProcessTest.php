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

    public function test_pelanggan_can_book_available_schedule()
    {
        $response = $this->actingAs($this->pelanggan)->post('/booking/simpan', [
            'lapangan_id' => $this->lapangan->id,
            'jadwal_id' => $this->jadwal->id,
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
            'metode_pembayaran' => 'qris',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('bookings', [
            'user_id' => $this->pelanggan->id,
            'jadwal_id' => $this->jadwal->id,
            'status' => 'pending'
        ]);

        // Note: The controller marks the schedule status as 'pending' initially, not 'dipesan' (which is done on payment).
        // Let's verify the updated status matches the implementation: updateOrCreate sets 'pending'
        $this->assertDatabaseHas('jadwal', [
            'id' => $this->jadwal->id,
            'status' => 'pending'
        ]);
    }

    public function test_system_prevents_double_booking()
    {
        // Pelanggan 1 booking duluan
        Booking::create([
            'user_id' => $this->pelanggan->id,
            'jadwal_id' => $this->jadwal->id,
            'lapangan_id' => $this->lapangan->id,
            'tanggal_booking' => Carbon::tomorrow()->format('Y-m-d'),
            'total_harga' => $this->jadwal->harga,
            'status' => 'pending',
        ]);

        // Status jadwal sudah diupdate oleh sistem/observer atau action (di sini kita update manual karena simulasi DB)
        $this->jadwal->update(['status' => 'dipesan']);


        // Pelanggan 2 mencoba booking jadwal yang sama
        $response = $this->actingAs($this->pelanggan2)->post('/booking/simpan', [
            'lapangan_id' => $this->lapangan->id,
            'jadwal_id' => $this->jadwal->id,
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
            'metode_pembayaran' => 'qris',
        ]);

        $response->assertSessionHas('error');

        // Pastikan booking kedua tidak tercipta
        $this->assertDatabaseMissing('bookings', [
            'user_id' => $this->pelanggan2->id,
            'jadwal_id' => $this->jadwal->id,
        ]);
    }

    public function test_guest_can_view_public_schedule()
    {
        $response = $this->get('/jadwal');
        $response->assertStatus(200);
        $response->assertSee('Lapangan Test');
    }

    public function test_pelanggan_can_book_with_multiple_vouchers()
    {
        // 1. Create a membership voucher (e.g. vip)
        $mv = \App\Models\Voucher::create([
            'user_id' => $this->pelanggan->id,
            'voucher_code' => 'VIP-TEST-CODE',
            'tipe_voucher' => 'vip',
            'status' => 'aktif',
            'expired_date' => Carbon::tomorrow(),
        ]);

        // 2. Create a redemption voucher (e.g. voucher_50k)
        $red = \App\Models\Redemption::create([
            'user_id' => $this->pelanggan->id,
            'jenis_hadiah' => 'voucher_50k',
            'poin_digunakan' => 75,
            'kode_voucher' => 'RED-TEST-CODE-UUID',
            'status' => 'aktif',
            'kode_expired_at' => Carbon::tomorrow(),
        ]);

        $response = $this->actingAs($this->pelanggan)->post('/booking/simpan', [
            'lapangan_id' => $this->lapangan->id,
            'jadwal_id' => $this->jadwal->id,
            'tanggal' => Carbon::tomorrow()->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
            'metode_pembayaran' => 'qris',
            'membership_voucher_ids' => [$mv->id],
            'voucher_ids' => [$red->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Verify booking was created and total price is 0
        $booking = Booking::where('user_id', $this->pelanggan->id)->where('jadwal_id', $this->jadwal->id)->first();
        $this->assertNotNull($booking);
        $this->assertEquals(0, $booking->total_harga);

        // Verify both vouchers are marked as used and linked to the booking
        $mv->refresh();
        $red->refresh();
        $this->assertEquals('digunakan', $mv->status);
        $this->assertEquals($booking->id, $mv->booking_id);
        $this->assertEquals('digunakan', $red->status);
        $this->assertEquals($booking->id, $red->booking_id);

        // Verify payment record has correct total
        $pembayaran = $booking->pembayaran;
        $this->assertNotNull($pembayaran);
        $this->assertEquals(0, $pembayaran->jumlah_bayar);
        $this->assertStringContainsString('VIP', $pembayaran->catatan_admin);
        $this->assertStringContainsString('Rp 50.000', $pembayaran->catatan_admin);
    }

    public function test_active_member_slots_block_bookings()
    {
        // 1. Create active member
        $member = User::create([
            'name' => 'Active Member',
            'email' => 'member@test.com',
            'password' => bcrypt('password'),
            'role' => 'pelanggan',
            'kategori_member' => 'member',
        ]);

        // Define a weekday date (e.g., next Monday)
        $monday = Carbon::parse('next monday');
        
        // Create a schedule on Monday from 10:00 to 11:00 (Weekday Pagi)
        $jadwalPagi = Jadwal::create([
            'lapangan_id' => $this->lapangan->id,
            'tanggal' => $monday->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
            'status' => 'dipesan',
            'keterangan' => 'Slot Member: ' . $member->name,
        ]);

        Booking::create([
            'user_id' => $member->id,
            'jadwal_id' => $jadwalPagi->id,
            'lapangan_id' => $this->lapangan->id,
            'tanggal_booking' => $monday->format('Y-m-d'),
            'total_harga' => 0,
            'status' => 'dipesan',
        ]);

        // Attempting to book this slot should fail because it's already booked
        $response = $this->actingAs($this->pelanggan)->post('/booking/simpan', [
            'lapangan_id' => $this->lapangan->id,
            'jadwal_id' => $jadwalPagi->id,
            'tanggal' => $monday->format('Y-m-d'),
            'jam_mulai' => '10:00',
            'jam_selesai' => '11:00',
            'metode_pembayaran' => 'qris',
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('Jadwal bentrok', session('error'));
    }

    public function test_public_schedule_displays_member_slots_as_dipesan()
    {
        // Create active member with weekday_malam package
        $member = User::create([
            'name' => 'Member Malam',
            'email' => 'malam@test.com',
            'password' => bcrypt('password'),
            'role' => 'pelanggan',
            'kategori_member' => 'member',
        ]);

        $monday = Carbon::parse('next monday');

        // Create a member booking for Monday
        $jadwal = Jadwal::create([
            'lapangan_id' => $this->lapangan->id,
            'tanggal' => $monday->format('Y-m-d'),
            'jam_mulai' => '18:00',
            'jam_selesai' => '21:00',
            'status' => 'dipesan',
            'keterangan' => 'Slot Member: ' . $member->name,
        ]);

        Booking::create([
            'user_id' => $member->id,
            'jadwal_id' => $jadwal->id,
            'lapangan_id' => $this->lapangan->id,
            'tanggal_booking' => $monday->format('Y-m-d'),
            'total_harga' => 0,
            'status' => 'dipesan',
        ]);

        // Access public schedule page
        $response = $this->get('/jadwal?tanggal=' . $monday->format('Y-m-d'));
        
        $response->assertStatus(200);
        $response->assertSee('Member Malam');
    }

    public function test_admin_verification_creates_four_weekly_bookings()
    {
        // 1. Create a pelanggan
        $member = User::create([
            'name' => 'Member Test',
            'email' => 'member_test@test.com',
            'password' => bcrypt('password'),
            'role' => 'pelanggan',
            'kategori_member' => 'non-member',
        ]);

        // 2. Create an admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_test@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // 3. Create a membership payment request with selected schedule (Monday 18:00-21:00 on Lapangan Test)
        $payment = \App\Models\MembershipPayment::create([
            'user_id' => $member->id,
            'paket' => 'weekday_malam',
            'jumlah_bayar' => 500000,
            'metode_pembayaran' => 'qris',
            'bukti_pembayaran' => 'dummy_bukti.png',
            'status_verifikasi' => 'menunggu',
            'hari' => 'senin',
            'jam_mulai' => '18:00',
            'jam_selesai' => '21:00',
            'lapangan_id' => $this->lapangan->id,
        ]);

        // 4. Verify as admin
        $response = $this->actingAs($admin)->put("/admin/pembayaran-membership/{$payment->id}/verif", [
            'status_verifikasi' => 'diverifikasi',
            'catatan_admin' => 'Approved',
        ]);

        $response->assertRedirect();
        
        // 5. Assert user is now a member and categorized correctly
        $member->refresh();
        $this->assertTrue($member->isMember());
        $this->assertEquals('weekday_malam', $member->kategori_member);

        // 6. Assert that 4 bookings were automatically created in the database
        $bookings = Booking::where('user_id', $member->id)->get();
        $this->assertCount(4, $bookings);

        // 7. Get the dates of next 4 Mondays
        $monday = Carbon::parse('next monday');
        for ($i = 0; $i < 4; $i++) {
            $formattedDate = $monday->format('Y-m-d');
            
            // Find booking for this date
            $booking = $bookings->first(function($b) use ($formattedDate) {
                return $b->tanggal_booking->format('Y-m-d') === $formattedDate;
            });
            $this->assertNotNull($booking, "Booking not found for date {$formattedDate}");
            $this->assertEquals($this->lapangan->id, $booking->lapangan_id);
            $this->assertEquals('dipesan', $booking->status);
            $this->assertEquals(0, $booking->total_harga);

            // Assert corresponding Jadwal exists
            $jadwal = Jadwal::where('lapangan_id', $this->lapangan->id)
                ->whereDate('tanggal', $formattedDate)
                ->where('jam_mulai', '18:00')
                ->first();
            $this->assertNotNull($jadwal, "Jadwal not found for date {$formattedDate}");
            $this->assertEquals('18:00', Carbon::parse($jadwal->jam_mulai)->format('H:i'));
            $this->assertEquals('21:00', Carbon::parse($jadwal->jam_selesai)->format('H:i'));
            $this->assertEquals('dipesan', $jadwal->status);
            $this->assertEquals('Slot Member: ' . $member->name, $jadwal->keterangan);

            $monday = $monday->copy()->next('Monday');
        }
    }

    public function test_offline_customer_deletion()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_crm@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create offline booking
        $booking = Booking::create([
            'jadwal_id' => $this->jadwal->id,
            'lapangan_id' => $this->lapangan->id,
            'tanggal_booking' => Carbon::tomorrow()->format('Y-m-d'),
            'total_harga' => $this->jadwal->harga,
            'status' => 'dipesan',
            'is_offline' => true,
            'nama_pemesan_offline' => 'Walk-in Guest',
            'no_hp_offline' => '0812345678',
        ]);

        $this->assertDatabaseHas('bookings', [
            'nama_pemesan_offline' => 'Walk-in Guest',
            'is_offline' => true,
        ]);

        // Delete offline customer
        $response = $this->actingAs($admin)->delete('/admin/crm/pelanggan/offline', [
            'name' => 'Walk-in Guest',
            'nomor_hp' => '0812345678',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Assert booking is deleted
        $this->assertDatabaseMissing('bookings', [
            'nama_pemesan_offline' => 'Walk-in Guest',
        ]);
    }

    public function test_booking_deletion_resets_schedule_status()
    {
        // Set jadwal status to pending
        $this->jadwal->update(['status' => 'pending']);

        // Create booking
        $booking = Booking::create([
            'user_id' => $this->pelanggan->id,
            'jadwal_id' => $this->jadwal->id,
            'lapangan_id' => $this->lapangan->id,
            'tanggal_booking' => Carbon::tomorrow()->format('Y-m-d'),
            'total_harga' => $this->jadwal->harga,
            'status' => 'pending',
        ]);

        $this->assertEquals('pending', $this->jadwal->fresh()->status);

        // Delete booking
        $booking->delete();

        // Assert schedule status is back to tersedia
        $this->assertEquals('tersedia', $this->jadwal->fresh()->status);
    }

    public function test_admin_schedule_page_shows_manage_button()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin_schedule@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/jadwal?tanggal=' . Carbon::tomorrow()->format('Y-m-d'));
        $response->assertStatus(200);
        $response->assertSee('Kelola');
        $response->assertSee('admin/jadwal');
    }
}

