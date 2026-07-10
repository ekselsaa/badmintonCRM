<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckExpiredMemberships extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'membership:check-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cek dan turunkan status member pelanggan yang masa aktifnya telah habis kembali menjadi non-member.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan masa aktif member...');

        // Cari pengguna yang statusnya member, memiliki tanggal kedaluwarsa, dan tanggal tersebut sudah terlewati
        $expiredUsers = \App\Models\User::where('kategori_member', '!=', 'non-member')
            ->whereNotNull('membership_expires_at')
            ->where('membership_expires_at', '<=', now())
            ->get(['id', 'name']);

        if ($expiredUsers->isEmpty()) {
            $this->info("Pengecekan selesai. Sebanyak 0 member telah diturunkan menjadi non-member.");
            return;
        }

        $userIds = $expiredUsers->pluck('id')->toArray();

        $keteranganList = [];
        foreach ($expiredUsers as $user) {
            $keteranganList[] = 'Slot Member: ' . $user->name;
            $keteranganList[] = 'Slot Member: ' . $user->name . ' (#' . $user->id . ')';
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($userIds, $keteranganList) {
            // 1. Turunkan status member pelanggan ke non-member
            \App\Models\User::whereIn('id', $userIds)->update([
                'kategori_member'       => 'non-member',
                'membership_expires_at' => null,
            ]);

            // 2. Bebaskan slot jadwal rutin masa depan milik member-member ini
            \App\Models\Jadwal::where('tanggal', '>=', now()->toDateString())
                ->whereIn('keterangan', $keteranganList)
                ->update([
                    'status' => 'tersedia',
                    'keterangan' => null
                ]);

            // 3. Batalkan booking member masa depan (jika ada)
            \App\Models\Booking::whereIn('user_id', $userIds)
                ->where('tanggal_booking', '>=', now()->toDateString())
                ->where('catatan', 'like', 'Sesi Rutin Member%')
                ->update(['status' => 'dibatalkan']);
        });

        foreach ($expiredUsers as $user) {
            $this->line("User ID: {$user->id} | Nama: {$user->name} status member telah kedaluwarsa.");
        }

        $this->info("Pengecekan selesai. Sebanyak {$expiredUsers->count()} member telah diturunkan menjadi non-member.");
    }
}
