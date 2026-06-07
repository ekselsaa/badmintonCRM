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
        Schema::table('points_history', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'idx_ph_user_created');
        });

        Schema::table('redemptions', function (Blueprint $table) {
            $table->index(['status', 'kode_expired_at'], 'idx_red_status_expired');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->index(['user_id', 'status'], 'idx_vch_user_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('points_history', function (Blueprint $table) {
            $table->dropIndex('idx_ph_user_created');
        });

        Schema::table('redemptions', function (Blueprint $table) {
            $table->dropIndex('idx_red_status_expired');
        });

        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropIndex('idx_vch_user_status');
        });
    }
};
