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
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create Customer
        $this->pelanggan = User::create([
            'name' => 'Customer User',
            'email' => 'customer@test.com',
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
