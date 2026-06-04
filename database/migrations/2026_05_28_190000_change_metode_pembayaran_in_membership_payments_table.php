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
            $table->string('metode_pembayaran')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('membership_payments', function (Blueprint $table) {
            $table->enum('metode_pembayaran', ['transfer', 'qris'])->change();
        });
    }
};
