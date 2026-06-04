<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lapangan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lapangan', 100);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga_weekday', 10, 2)->default(55000);
            $table->decimal('harga_weekend', 10, 2)->default(60000);
            $table->string('foto')->nullable();
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lapangan');
    }
};
