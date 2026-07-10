<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Clean and normalize existing phone numbers in users table
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            if (!empty($user->nomor_hp)) {
                $cleaned = preg_replace('/[^0-9]/', '', $user->nomor_hp);
                if (str_starts_with($cleaned, '0')) {
                    $cleaned = '62' . substr($cleaned, 1);
                } elseif (str_starts_with($cleaned, '8')) {
                    $cleaned = '62' . $cleaned;
                }
                DB::table('users')->where('id', $user->id)->update(['nomor_hp' => $cleaned]);
            } else {
                DB::table('users')->where('id', $user->id)->update(['nomor_hp' => null]);
            }
        }

        // 2. Specific phone updates for existing users (Fahri, Misbah, Ical)
        DB::table('users')->where('id', 2)->update(['nomor_hp' => '62895357304477']);
        DB::table('users')->where('id', 3)->update(['nomor_hp' => '6282194775707']);
        DB::table('users')->where('id', 4)->update(['nomor_hp' => '62895341602482']);

        // 3. Clean and normalize offline bookings
        $bookings = DB::table('bookings')->where('is_offline', true)->get();
        foreach ($bookings as $b) {
            if (!empty($b->no_hp_offline)) {
                $cleaned = preg_replace('/[^0-9]/', '', $b->no_hp_offline);
                if (str_starts_with($cleaned, '0')) {
                    $cleaned = '62' . substr($cleaned, 1);
                } elseif (str_starts_with($cleaned, '8')) {
                    $cleaned = '62' . $cleaned;
                }
                DB::table('bookings')->where('id', $b->id)->update(['no_hp_offline' => $cleaned]);
            }
        }

        // 4. Specific phone updates for offline bookings (Awal, Dila)
        DB::table('bookings')->where('id', 3)->update(['no_hp_offline' => '6287864965073']);
        DB::table('bookings')->where('id', 7)->update(['no_hp_offline' => '6281313378355']);

        // 5. Apply the UNIQUE constraint to users.nomor_hp
        Schema::table('users', function (Blueprint $table) {
            $table->string('nomor_hp', 20)->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nomor_hp']);
            $table->string('nomor_hp', 20)->nullable()->change();
        });
    }
};
