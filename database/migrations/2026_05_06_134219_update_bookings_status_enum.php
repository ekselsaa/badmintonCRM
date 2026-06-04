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
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('bookings', function (Blueprint $table) {
                DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'dikonfirmasi', 'dibatalkan', 'selesai', 'dipesan') DEFAULT 'pending'");
            });
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('bookings', function (Blueprint $table) {
                DB::statement("ALTER TABLE bookings MODIFY COLUMN status ENUM('pending', 'dikonfirmasi', 'dibatalkan', 'selesai') DEFAULT 'pending'");
            });
        }
    }
};
