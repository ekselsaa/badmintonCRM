<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['jadwal_id']);
            // Drop unique index (Laravel uses table_col_unique as naming convention)
            $table->dropUnique('bookings_jadwal_id_unique');
            // Re-add foreign key as a normal index
            $table->foreign('jadwal_id')->references('id')->on('jadwal')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropForeign(['jadwal_id']);
            $table->unique('jadwal_id');
            $table->foreign('jadwal_id')->references('id')->on('jadwal')->onDelete('cascade');
        });
    }
};
