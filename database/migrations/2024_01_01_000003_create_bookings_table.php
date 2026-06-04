<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('jadwal_id')->unique()->constrained('jadwal')->onDelete('cascade');
            $table->foreignId('lapangan_id')->constrained('lapangan')->onDelete('cascade');
            $table->date('tanggal_booking');
            $table->decimal('total_harga', 10, 2);
            $table->enum('status', ['pending', 'dikonfirmasi', 'dibatalkan', 'selesai'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
