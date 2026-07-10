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
            $table->index(['hari', 'status_verifikasi'], 'idx_mp_hari_status');
            $table->index('created_at', 'idx_mp_created_at');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->index('created_at', 'idx_bookings_created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_payments', function (Blueprint $table) {
            $table->dropIndex('idx_mp_hari_status');
            $table->dropIndex('idx_mp_created_at');
        });

        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex('idx_bookings_created_at');
        });
    }
};
