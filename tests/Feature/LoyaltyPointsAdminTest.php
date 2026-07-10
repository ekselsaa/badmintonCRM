<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PointHistory;
use App\Models\Redemption;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Carbon\Carbon;

class LoyaltyPointsAdminTest extends TestCase
{
    use RefreshDatabase;

    private $admin;
    private $pelanggan;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin
        $this->admin = User::create([
            'name' => 'Admin User',
            'password' => bcrypt('admin'),
            'role' => 'admin',
        ]);

        // Create Customer
        $this->pelanggan = User::create([
            'name' => 'Customer User',
            'password' => bcrypt('password'),
            'role' => 'pelanggan',
            'poin_saldo' => 100, // Saldo awal
        ]);
    }

    /**
     * Test admin can credit points manually.
     */
    public function test_admin_can_credit_points_manually()
    {
        $response = $this->actingAs($this->admin)->post("/admin/crm/pelanggan/{$this->pelanggan->id}/adjust-points", [
            'tipe' => 'kredit',
            'jumlah_poin' => 50,
            'keterangan' => 'Hadiah loyalitas spesial',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->pelanggan->refresh();
        $this->assertEquals(150, $this->pelanggan->poin_saldo);

        $this->assertDatabaseHas('points_history', [
            'user_id' => $this->pelanggan->id,
            'tipe' => 'kredit',
            'jumlah_poin' => 50,
            'sumber' => 'penyesuaian_manual',
            'keterangan' => 'Hadiah loyalitas spesial',
        ]);
    }

    /**
     * Test admin can debit points manually.
     */
    public function test_admin_can_debit_points_manually()
    {
        $response = $this->actingAs($this->admin)->post("/admin/crm/pelanggan/{$this->pelanggan->id}/adjust-points", [
            'tipe' => 'debit',
            'jumlah_poin' => 40,
            'keterangan' => 'Koreksi kesalahan booking',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->pelanggan->refresh();
        $this->assertEquals(60, $this->pelanggan->poin_saldo);

        $this->assertDatabaseHas('points_history', [
            'user_id' => $this->pelanggan->id,
            'tipe' => 'debit',
            'jumlah_poin' => 40,
            'sumber' => 'penyesuaian_manual',
            'keterangan' => 'Koreksi kesalahan booking',
        ]);
    }

    /**
     * Test admin cannot debit points below zero.
     */
    public function test_admin_cannot_debit_points_below_zero()
    {
        $response = $this->actingAs($this->admin)->post("/admin/crm/pelanggan/{$this->pelanggan->id}/adjust-points", [
            'tipe' => 'debit',
            'jumlah_poin' => 150, // Lebih dari saldo (100)
            'keterangan' => 'Debit berlebih',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error'); // Exception thrown and captured in try-catch

        $this->pelanggan->refresh();
        $this->assertEquals(100, $this->pelanggan->poin_saldo);
    }

    /**
     * Test customer cannot adjust points.
     */
    public function test_customer_cannot_adjust_points()
    {
        $response = $this->actingAs($this->pelanggan)->post("/admin/crm/pelanggan/{$this->pelanggan->id}/adjust-points", [
            'tipe' => 'kredit',
            'jumlah_poin' => 50,
            'keterangan' => 'Hacking points',
        ]);

        $response->assertStatus(403);
        $this->pelanggan->refresh();
        $this->assertEquals(100, $this->pelanggan->poin_saldo);
    }

    /**
     * Test admin can mark voucher as claimed offline.
     */
    public function test_admin_can_claim_voucher_offline()
    {
        $voucher = Redemption::create([
            'user_id' => $this->pelanggan->id,
            'jenis_hadiah' => 'kok_satuan',
            'poin_digunakan' => 20,
            'kode_voucher' => 'TEST-VOUCHER-UUID',
            'status' => 'aktif',
            'kode_expired_at' => Carbon::now()->addDays(10),
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/loyalty/klaim-voucher", [
            'kode_voucher' => 'TEST-VOUCHER-UUID',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $voucher->refresh();
        $this->assertEquals('digunakan', $voucher->status);
        $this->assertNotNull($voucher->digunakan_pada);
    }

    /**
     * Test admin can claim voucher offline using only the 8-character short code.
     */
    public function test_admin_can_claim_voucher_offline_using_short_code()
    {
        $voucher = Redemption::create([
            'user_id' => $this->pelanggan->id,
            'jenis_hadiah' => 'kok_satuan',
            'poin_digunakan' => 20,
            'kode_voucher' => '5e3418c0-8483-4fbb-a4ce-91f3eb86b770',
            'status' => 'aktif',
            'kode_expired_at' => Carbon::now()->addDays(10),
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/loyalty/klaim-voucher", [
            'kode_voucher' => '5e3418c0', // 8-character short code
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $voucher->refresh();
        $this->assertEquals('digunakan', $voucher->status);
        $this->assertNotNull($voucher->digunakan_pada);
    }

    /**
     * Test admin can claim membership voucher offline.
     */
    public function test_admin_can_claim_membership_voucher_offline()
    {
        $voucher = \App\Models\Voucher::create([
            'user_id' => $this->pelanggan->id,
            'voucher_code' => 'PARTNER-ABCD1234',
            'tipe_voucher' => 'partner',
            'status' => 'aktif',
            'expired_date' => Carbon::now()->addDays(30),
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/loyalty/klaim-voucher", [
            'kode_voucher' => 'PARTNER-ABCD1234',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $voucher->refresh();
        $this->assertEquals('digunakan', $voucher->status);
        $this->assertNotNull($voucher->digunakan_pada);
    }

    /**
     * Test admin cannot claim VIP membership voucher without renewal verification.
     */
    public function test_admin_cannot_claim_vip_membership_voucher_without_renewal_check()
    {
        $voucher = \App\Models\Voucher::create([
            'user_id' => $this->pelanggan->id,
            'voucher_code' => 'VIP-ABCD1234',
            'tipe_voucher' => 'vip',
            'status' => 'aktif',
            'expired_date' => Carbon::now()->addDays(14),
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/loyalty/klaim-voucher", [
            'kode_voucher' => 'VIP-ABCD1234',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error'); // renewal verification required

        $voucher->refresh();
        $this->assertEquals('aktif', $voucher->status);
        $this->assertNull($voucher->digunakan_pada);
    }

    /**
     * Test admin can claim VIP membership voucher with renewal verification.
     */
    public function test_admin_can_claim_vip_membership_voucher_with_renewal_check()
    {
        $voucher = \App\Models\Voucher::create([
            'user_id' => $this->pelanggan->id,
            'voucher_code' => 'VIP-XYZ98765',
            'tipe_voucher' => 'vip',
            'status' => 'aktif',
            'expired_date' => Carbon::now()->addDays(14),
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/loyalty/klaim-voucher", [
            'kode_voucher' => 'VIP-XYZ98765',
            'is_member_renewal' => true,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $voucher->refresh();
        $this->assertEquals('digunakan', $voucher->status);
        $this->assertNotNull($voucher->digunakan_pada);
    }
}

