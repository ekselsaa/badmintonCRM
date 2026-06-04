<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tambah kolom poin_bulanan ke tabel users (jika belum ada)
        if (!Schema::hasColumn('users', 'poin_bulanan')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('poin_bulanan')
                      ->default(0)
                      ->after('poin_saldo')
                      ->comment('Akumulasi poin diperoleh pelanggan pada bulan berjalan');
            });
        }

        if (DB::getDriverName() !== 'sqlite') {
            // Ubah enum segmen_pelanggan di MySQL dengan trik VARCHAR agar tidak truncated
            DB::statement("ALTER TABLE users MODIFY COLUMN segmen_pelanggan VARCHAR(50)");
            
            // Update data lama agar bernilai 'visitor' (yang valid di enum baru)
            DB::table('users')->update(['segmen_pelanggan' => 'visitor']);

            // Ubah menjadi enum baru
            DB::statement("ALTER TABLE users MODIFY COLUMN segmen_pelanggan ENUM('visitor', 'ally', 'partner', 'loyalist', 'vip') DEFAULT 'visitor' COMMENT 'Segmen CRM berdasarkan akumulasi poin bulanan'");
        } else {
            DB::table('users')->update(['segmen_pelanggan' => 'visitor']);
        }

        // 2. Buat tabel vouchers baru untuk Sapu Bersih
        if (!Schema::hasTable('vouchers')) {
            Schema::create('vouchers', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('voucher_code', 50)->unique();
                $table->enum('tipe_voucher', ['ally', 'partner', 'loyalist', 'vip']);
                $table->enum('status', ['aktif', 'digunakan', 'kadaluwarsa'])->default('aktif');
                $table->timestamp('expired_date')->nullable();
                $table->unsignedBigInteger('booking_id')->nullable(); // Ditautkan ke booking
                $table->timestamp('digunakan_pada')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'status']);
            });
        }

        // 3. Tambah kolom voucher_id ke tabel bookings (transactions)
        if (!Schema::hasColumn('bookings', 'voucher_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->unsignedBigInteger('voucher_id')->nullable()->after('reward_applied');
                $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('set null');
            });
        }

        // 4. Tambah foreign key booking_id ke vouchers
        // Cek dulu apakah tabel vouchers ada dan kolom booking_id belum memiliki foreign key
        try {
            Schema::table('vouchers', function (Blueprint $table) {
                $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('set null');
            });
        } catch (\Exception $e) {
            // Abaikan jika foreign key sudah ada
        }

        // 5. Tambah audit trail saldo ke points_history
        if (!Schema::hasColumn('points_history', 'poin_saldo_after')) {
            Schema::table('points_history', function (Blueprint $table) {
                $table->unsignedInteger('poin_saldo_after')
                      ->nullable()
                      ->after('jumlah_poin')
                      ->comment('Snapshot saldo poin pasca transaksi');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('points_history', 'poin_saldo_after')) {
            Schema::table('points_history', function (Blueprint $table) {
                $table->dropColumn('poin_saldo_after');
            });
        }

        if (Schema::hasTable('vouchers')) {
            try {
                Schema::table('vouchers', function (Blueprint $table) {
                    $table->dropForeign(['booking_id']);
                });
            } catch (\Exception $e) {}
        }

        if (Schema::hasColumn('bookings', 'voucher_id')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropForeign(['voucher_id']);
                $table->dropColumn('voucher_id');
            });
        }

        Schema::dropIfExists('vouchers');

        if (Schema::hasColumn('users', 'poin_bulanan')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('poin_bulanan');
            });
        }

        if (DB::getDriverName() !== 'sqlite') {
            // Kembalikan enum segmen_pelanggan
            DB::statement("ALTER TABLE users MODIFY COLUMN segmen_pelanggan VARCHAR(50)");
            DB::table('users')->update(['segmen_pelanggan' => 'inactive']);
            DB::statement("ALTER TABLE users MODIFY COLUMN segmen_pelanggan ENUM('elite', 'regular', 'passive', 'inactive') DEFAULT 'inactive'");
        } else {
            DB::table('users')->update(['segmen_pelanggan' => 'inactive']);
        }
    }
};
