<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('redemptions', function (Blueprint $table) {
            // Drop redundant index since `kode_voucher` already has a unique index
            $table->dropIndex('rd_kode_voucher');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            // Drop redundant index since `vouchers_user_id_status_index` already exists
            $table->dropIndex('idx_vch_user_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('redemptions', function (Blueprint $table) {
            $table->index('kode_voucher', 'rd_kode_voucher');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_vch_user_status');
        });
    }
};
