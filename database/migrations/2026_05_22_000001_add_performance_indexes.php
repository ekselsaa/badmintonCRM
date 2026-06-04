<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Menambahkan database index pada kolom yang sering di-WHERE/ORDER BY
 * untuk meningkatkan performa query secara signifikan.
 *
 * Kolom tanpa index memaksa MySQL full table scan setiap query.
 * Dengan index, pencarian menjadi O(log n) instead of O(n).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── Index tabel bookings ──────────────────────────────────
        Schema::table('bookings', function (Blueprint $table) {
            // status: difilter di hampir semua query
            if (!$this->indexExists('bookings', 'bookings_status_index')) {
                $table->index('status', 'bookings_status_index');
            }
            // tanggal_booking: difilter di dashboard, laporan
            if (!$this->indexExists('bookings', 'bookings_tanggal_booking_index')) {
                $table->index('tanggal_booking', 'bookings_tanggal_booking_index');
            }
            // user_id: difilter di riwayat & CRM pelanggan
            if (!$this->indexExists('bookings', 'bookings_user_id_index')) {
                $table->index('user_id', 'bookings_user_id_index');
            }
            // Composite: user_id + status (untuk query riwayat pelanggan dengan filter status)
            if (!$this->indexExists('bookings', 'bookings_user_status_index')) {
                $table->index(['user_id', 'status'], 'bookings_user_status_index');
            }
            // Composite: status + tanggal_booking (untuk dashboard & laporan yang filter keduanya)
            if (!$this->indexExists('bookings', 'bookings_status_tanggal_index')) {
                $table->index(['status', 'tanggal_booking'], 'bookings_status_tanggal_index');
            }
        });

        // ── Index tabel pembayaran ────────────────────────────────
        Schema::table('pembayaran', function (Blueprint $table) {
            // status_verifikasi: sering difilter di dashboard & verifikasi
            if (!$this->indexExists('pembayaran', 'pembayaran_status_verifikasi_index')) {
                $table->index('status_verifikasi', 'pembayaran_status_verifikasi_index');
            }
            // created_at: untuk sorting ORDER BY created_at DESC
            if (!$this->indexExists('pembayaran', 'pembayaran_created_at_index')) {
                $table->index('created_at', 'pembayaran_created_at_index');
            }
        });

        // ── Index tabel jadwal ────────────────────────────────────
        Schema::table('jadwal', function (Blueprint $table) {
            // tanggal: difilter di semua halaman booking
            if (!$this->indexExists('jadwal', 'jadwal_tanggal_index')) {
                $table->index('tanggal', 'jadwal_tanggal_index');
            }
            // status: difilter di query cek ketersediaan
            if (!$this->indexExists('jadwal', 'jadwal_status_index')) {
                $table->index('status', 'jadwal_status_index');
            }
            // Composite: lapangan_id + tanggal + status (query anti-double-booking)
            if (!$this->indexExists('jadwal', 'jadwal_lapangan_tanggal_status_index')) {
                $table->index(['lapangan_id', 'tanggal', 'status'], 'jadwal_lapangan_tanggal_status_index');
            }
        });

        // ── Index tabel users ─────────────────────────────────────
        Schema::table('users', function (Blueprint $table) {
            // role: sering difilter where('role', 'pelanggan')
            if (!$this->indexExists('users', 'users_role_index')) {
                $table->index('role', 'users_role_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndexIfExists('bookings_status_index');
            $table->dropIndexIfExists('bookings_tanggal_booking_index');
            $table->dropIndexIfExists('bookings_user_id_index');
            $table->dropIndexIfExists('bookings_user_status_index');
            $table->dropIndexIfExists('bookings_status_tanggal_index');
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropIndexIfExists('pembayaran_status_verifikasi_index');
            $table->dropIndexIfExists('pembayaran_created_at_index');
        });

        Schema::table('jadwal', function (Blueprint $table) {
            $table->dropIndexIfExists('jadwal_tanggal_index');
            $table->dropIndexIfExists('jadwal_status_index');
            $table->dropIndexIfExists('jadwal_lapangan_tanggal_status_index');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndexIfExists('users_role_index');
        });
    }

    /**
     * Cek apakah index sudah ada agar migration tidak error jika dijalankan ulang.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            return false;
        }

        $indexes = \Illuminate\Support\Facades\DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        );
        return !empty($indexes);
    }
};
