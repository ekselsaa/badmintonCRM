<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Flag booking offline (tanpa akun pelanggan terdaftar)
            $table->boolean('is_offline')->default(false)->after('catatan');
            // Nama pemesan untuk booking offline
            $table->string('nama_pemesan_offline')->nullable()->after('is_offline');
            // Nomor HP pemesan offline (opsional)
            $table->string('no_hp_offline')->nullable()->after('nama_pemesan_offline');
            // user_id boleh null untuk booking offline
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['is_offline', 'nama_pemesan_offline', 'no_hp_offline']);
            $table->unsignedBigInteger('user_id')->nullable(false)->change();
        });
    }
};
