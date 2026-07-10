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
            // Drop redundant indexes
            if ($this->indexExists('bookings', 'bookings_user_id_index')) {
                $table->dropIndex('bookings_user_id_index');
            }
            if ($this->indexExists('bookings', 'bookings_status_index')) {
                $table->dropIndex('bookings_status_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            // Re-create indexes if rolled back
            $table->index('user_id', 'bookings_user_id_index');
            $table->index('status', 'bookings_status_index');
        });
    }

    /**
     * Check if index exists on a table.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'sqlite') {
            // SQLite drops index if it exists, Laravel handles gracefully
            return true;
        }

        $indexes = \Illuminate\Support\Facades\DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        );
        return !empty($indexes);
    }
};
