<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel histori poin masuk (kredit) dan keluar (debit).
     * Setiap transaksi poin harus tercatat di sini sebagai audit trail.
     *
     * Sumber poin kredit:
     *   sewa_lapangan_offpeak | sewa_lapangan_peak
     *   sewa_raket | beli_kok_satuan | beli_kok_slop
     *   paket_member_pagi_siang | paket_member_malam | paket_member_weekend
     *
     * Sumber poin debit:
     *   penukaran_kok_satuan | penukaran_raket | penukaran_lapangan_offpeak
     *   penukaran_voucher_50k | penukaran_lapangan_peak | penukaran_voucher_member
     *   kadaluwarsa
     */
    public function up(): void
    {
        Schema::create('points_history', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('booking_id')
                  ->nullable()
                  ->constrained('bookings')
                  ->onDelete('set null')
                  ->comment('Null jika bukan dari booking (penukaran, kadaluwarsa, paket member)');

            // Arah poin: kredit = masuk, debit = keluar
            $table->enum('tipe', ['kredit', 'debit']);

            // Nilai selalu positif — tipe yg menentukan arah saldo
            $table->unsignedInteger('jumlah_poin');

            // Identifikasi sumber untuk laporan & analitik
            $table->string('sumber', 80)->comment('Lihat komentar tabel untuk daftar valid sumber');

            // Deskripsi human-readable untuk tampilan ke pelanggan
            $table->string('keterangan', 255)->nullable();

            // Masa kadaluwarsa — hanya diisi untuk tipe 'kredit'
            $table->timestamp('expired_at')->nullable();
            $table->boolean('is_expired')->default(false)->comment('Di-flag true oleh cron loyalty:expire-points');

            $table->timestamps();

            // ─── Index untuk optimasi query ───────────────────────────
            $table->index(['user_id', 'tipe'], 'ph_user_tipe');
            $table->index(['user_id', 'is_expired', 'expired_at'], 'ph_user_expiry');
            $table->index('expired_at', 'ph_expired_at'); // Untuk cron batch
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_history');
    }
};
