<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE jadwal MODIFY COLUMN status ENUM('tersedia', 'dipesan', 'ditutup', 'pending') DEFAULT 'tersedia'");
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE jadwal MODIFY COLUMN status ENUM('tersedia', 'dipesan') DEFAULT 'tersedia'");
        }
    }
};
