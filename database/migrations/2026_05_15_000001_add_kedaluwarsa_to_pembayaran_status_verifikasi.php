<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambahkan nilai 'kedaluwarsa' ke ENUM status_verifikasi di tabel pembayaran.
     * Diperlukan karena AdminController::pembayaranIndex() otomatis menandai
     * pembayaran yang jadwalnya sudah lewat sebagai 'kedaluwarsa'.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE pembayaran MODIFY COLUMN status_verifikasi ENUM('menunggu', 'diverifikasi', 'ditolak', 'kedaluwarsa') DEFAULT 'menunggu'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // Sebelum rollback, ubah semua 'kedaluwarsa' ke 'ditolak' agar tidak error
            DB::statement("UPDATE pembayaran SET status_verifikasi = 'ditolak' WHERE status_verifikasi = 'kedaluwarsa'");
            DB::statement("ALTER TABLE pembayaran MODIFY COLUMN status_verifikasi ENUM('menunggu', 'diverifikasi', 'ditolak') DEFAULT 'menunggu'");
        }
    }
};
