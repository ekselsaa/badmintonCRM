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
            $table->string('hari')->nullable();
            $table->string('jam_mulai')->nullable();
            $table->string('jam_selesai')->nullable();
            $table->foreignId('lapangan_id')->nullable()->constrained('lapangan')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_payments', function (Blueprint $table) {
            $table->dropForeign(['lapangan_id']);
            $table->dropColumn(['hari', 'jam_mulai', 'jam_selesai', 'lapangan_id']);
        });
    }
};
