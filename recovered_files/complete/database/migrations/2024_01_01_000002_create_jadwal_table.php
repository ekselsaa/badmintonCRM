<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lapangan_id')->constrained('lapangan')->onDelete('cascade');
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->enum('status', ['tersedia', 'dipesan'])->default('tersedia');
            $table->timestamps();

            // CONSTRAINT: Anti double booking di level database
            // Kombinasi lapangan_id + tanggal + jam_mulai harus unik
            $table->unique(['lapangan_id', 'tanggal', 'jam_mulai'], 'unique_jadwal_slot');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
