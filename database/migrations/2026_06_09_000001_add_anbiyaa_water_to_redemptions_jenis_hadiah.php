<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE redemptions MODIFY COLUMN jenis_hadiah ENUM('kok_satuan', 'raket', 'lapangan_offpeak', 'voucher_50k', 'lapangan_peak', 'voucher_member', 'anbiyaa_water') NOT NULL");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE redemptions MODIFY COLUMN jenis_hadiah ENUM('kok_satuan', 'raket', 'lapangan_offpeak', 'voucher_50k', 'lapangan_peak', 'voucher_member') NOT NULL");
        }
    }
};
