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
        Schema::table('bookings', function (Blueprint $table) {
            $table->tinyInteger('rating')->nullable()->after('is_offline')->comment('1-5 stars');
            $table->text('ulasan')->nullable()->after('rating');
            $table->boolean('is_tampil_beranda')->default(false)->after('ulasan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['rating', 'ulasan', 'is_tampil_beranda']);
        });
    }
};
