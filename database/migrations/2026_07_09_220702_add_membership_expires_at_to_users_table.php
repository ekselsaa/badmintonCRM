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
        if (!Schema::hasColumn('users', 'membership_expires_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('membership_expires_at')->nullable()->after('kategori_member');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'membership_expires_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('membership_expires_at');
            });
        }
    }
};
