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
        // 1. Add username column as nullable first to support existing database records
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable()->after('name');
            $table->string('email')->nullable()->change();
        });

        // 2. Populate usernames for any existing users
        $users = DB::table('users')->get();
        foreach ($users as $user) {
            if (empty($user->username)) {
                $emailVal = $user->email ?? 'user' . $user->id;
                $emailPrefix = explode('@', $emailVal)[0];
                // Clean the username (only alphanumeric and underscore)
                $username = strtolower(preg_replace('/[^a-zA-Z0-9_]/', '', $emailPrefix));
                if (empty($username)) {
                    $username = 'user_' . $user->id;
                }
                
                // Handle duplicate collision
                $originalUsername = $username;
                $counter = 1;
                while (DB::table('users')->where('username', $username)->exists()) {
                    $username = $originalUsername . $counter;
                    $counter++;
                }

                DB::table('users')->where('id', $user->id)->update([
                    'username' => $username,
                ]);
            }
        }

        // 3. Update the username column to be unique and NOT nullable now that all rows have it
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop unique index first if needed, but dropping the column will automatically drop its unique index in MySQL
            $table->dropColumn('username');
            $table->string('email')->nullable(false)->change();
        });
    }
};
