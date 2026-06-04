<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->unique()->constrained('bookings')->onDelete('cascade');
            $table->string('bukti_pembayaran');
            $table->decimal('jumlah_bayar', 10, 2);
            $table->enum('metode_pembayaran', ['transfer', 'tunai', 'qris'])->default('transfer');
            $table->enum('status_verifikasi', ['menunggu', 'diverifikasi', 'ditolak'])->default('menunggu');
            $table->text('catatan_admin')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
