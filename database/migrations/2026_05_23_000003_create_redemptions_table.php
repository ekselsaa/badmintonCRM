<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel histori penukaran poin menjadi hadiah/voucher.
     * Setiap penukaran menghasilkan kode_voucher UUID yang unik.
     * Voucher berlaku 30 hari sejak ditukar.
     */
    public function up(): void
    {
        Schema::create('redemptions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->enum('jenis_hadiah', [
                'kok_satuan',          // 20 poin → 1 Shuttlecock Satuan
                'raket',               // 35 poin → Gratis Sewa Raket 1 Sesi
                'lapangan_offpeak',    // 50 poin → Gratis 1 Jam Lapangan Off-Peak
                'voucher_50k',         // 75 poin → Voucher Potongan Rp 50.000
                'lapangan_peak',       // 100 poin → Gratis 1 Jam Lapangan Peak
                'voucher_member',      // 180 poin → Voucher Rp 100.000 Perpanjangan Member
            ]);

            $table->unsignedInteger('poin_digunakan')
                  ->comment('Snapshot jumlah poin saat ditukar');

            // Kode unik UUID untuk klaim hadiah di meja kasir
            $table->string('kode_voucher', 36)->unique();

            $table->enum('status', ['aktif', 'digunakan', 'kadaluwarsa'])->default('aktif');

            // Kapan voucher dipakai oleh pelanggan (diisi admin saat klaim)
            $table->timestamp('digunakan_pada')->nullable();

            // Voucher kadaluwarsa 30 hari setelah ditukar
            $table->timestamp('kode_expired_at')->nullable();

            $table->timestamps();

            // ─── Index ─────────────────────────────────────────────────
            $table->index(['user_id', 'status'], 'rd_user_status');
            $table->index('kode_voucher', 'rd_kode_voucher');
            $table->index('kode_expired_at', 'rd_kode_expired'); // Untuk cron cleanup voucher
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redemptions');
    }
};
