<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Perluas nilai enum kategori_member agar mendukung nama paket spesifik.
     * Sebelumnya: ['member', 'non-member']
     * Sesudah   : ['member', 'non-member', 'weekday_pagi', 'weekday_malam', 'weekend']
     *
     * Menggunakan change() agar kompatibel dengan MySQL dan SQLite (test environment).
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('kategori_member', [
                'member', 'non-member', 'weekday_pagi', 'weekday_malam', 'weekend',
            ])->default('non-member')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan nilai paket spesifik ke 'member' agar tidak melanggar constraint lama
        \Illuminate\Support\Facades\DB::table('users')
            ->whereIn('kategori_member', ['weekday_pagi', 'weekday_malam', 'weekend'])
            ->update(['kategori_member' => 'member']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('kategori_member', ['member', 'non-member'])
                ->default('non-member')->change();
        });
    }
};
