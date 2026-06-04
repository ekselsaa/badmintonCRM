<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom loyalty points & segmentasi ke tabel users.
     * - poin_saldo        : Saldo poin aktif (cache, selalu sync via LoyaltyPointService)
     * - segmen_pelanggan  : Segmen CRM otomatis dari cron job bulanan
     * - segmen_updated_at : Timestamp terakhir segmen diperbarui
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('poin_saldo')
                  ->default(0)
                  ->after('kategori_member')
                  ->comment('Cache saldo poin aktif pelanggan');

            $table->enum('segmen_pelanggan', ['elite', 'regular', 'passive', 'inactive'])
                  ->default('inactive')
                  ->after('poin_saldo')
                  ->comment('Segmen CRM: elite>200, regular 80-200, passive 30-79, inactive<30 poin/bln');

            $table->timestamp('segmen_updated_at')
                  ->nullable()
                  ->after('segmen_pelanggan')
                  ->comment('Kapan segmen terakhir diperbarui oleh cron job');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['poin_saldo', 'segmen_pelanggan', 'segmen_updated_at']);
        });
    }
};
