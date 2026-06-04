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
        Schema::table('membership_payments', function (Blueprint $table) {
            $table->index('status_verifikasi', 'mp_status_verifikasi_index');
            $table->index(['user_id', 'status_verifikasi'], 'mp_user_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_payments', function (Blueprint $table) {
            $table->dropIndex('mp_status_verifikasi_index');
            $table->dropIndex('mp_user_status_index');
        });
    }
};
