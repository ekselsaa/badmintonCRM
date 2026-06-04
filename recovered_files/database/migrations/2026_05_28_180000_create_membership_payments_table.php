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
        Schema::create('membership_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('paket'); // e.g. weekday_pagi, weekday_malam, weekend
            $table->decimal('jumlah_bayar', 10, 2);
            $table->enum('metode_pembayaran', ['transfer', 'qris']);
            $table->string('bukti_pembayaran'); // Path file
            $table->enum('status_verifikasi', ['menunggu', 'diverifikasi', 'ditolak'])->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_payments');
    }
};
