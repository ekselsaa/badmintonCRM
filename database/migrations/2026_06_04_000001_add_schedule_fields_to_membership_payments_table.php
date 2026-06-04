<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom jadwal ke tabel membership_payments.
     * Diperlukan untuk membuat booking mingguan otomatis saat verifikasi.
     */
    public function up(): void
    {
        Schema::table('membership_payments', function (Blueprint $table) {
            // Hari booking member: senin, selasa, ..., minggu (nullable untuk backward compat)
            $table->string('hari', 10)->nullable()->after('paket');
            // Jam mulai dan selesai sesi member
            $table->time('jam_mulai')->nullable()->after('hari');
            $table->time('jam_selesai')->nullable()->after('jam_mulai');
            // Lapangan yang dipilih untuk paket member
            $table->foreignId('lapangan_id')->nullable()->constrained('lapangan')->nullOnDelete()->after('jam_selesai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_payments', function (Blueprint $table) {
            $table->dropForeign(['lapangan_id']);
            $table->dropColumn(['hari', 'jam_mulai', 'jam_selesai', 'lapangan_id']);
        });
    }
};
