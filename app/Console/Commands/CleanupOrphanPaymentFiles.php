<?php

namespace App\Console\Commands;

use App\Models\Pembayaran;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * CleanupOrphanPaymentFiles
 *
 * Membersihkan file bukti pembayaran yang tidak lagi memiliki relasi booking aktif
 * (booking sudah dibatalkan, atau file di-upload ulang sehingga file lama tertinggal).
 *
 * Dijadwalkan: Setiap hari pukul 03:00 WIB
 * Command    : php artisan storage:cleanup-payment-files
 */
class CleanupOrphanPaymentFiles extends Command
{
    protected $signature   = 'storage:cleanup-payment-files {--dry-run : Tampilkan file yang akan dihapus tanpa benar-benar menghapus}';
    protected $description = 'Hapus file bukti pembayaran orphan (booking reguler & membership dibatalkan / file sudah diganti).';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $this->info('Menjalankan pembersihan file bukti pembayaran...');
        $this->info('Mode: ' . ($isDryRun ? 'DRY RUN (tidak menghapus)' : 'LIVE'));
        $this->info('Waktu: ' . now()->translatedFormat('d F Y, H:i:s'));

        // 1. Kumpulkan semua path file yang VALID dari kedua tabel
        $validPembayaranPaths = Pembayaran::whereNotNull('bukti_pembayaran')
            ->where('bukti_pembayaran', '!=', 'midtrans_auto')
            ->pluck('bukti_pembayaran')
            ->filter()
            ->values()
            ->toArray();

        $validMembershipPaths = \App\Models\MembershipPayment::whereNotNull('bukti_pembayaran')
            ->pluck('bukti_pembayaran')
            ->filter()
            ->values()
            ->toArray();

        $validPaths = array_merge($validPembayaranPaths, $validMembershipPaths);

        $this->line('File terdaftar di DB (valid): ' . count($validPaths));

        // 2. Scan file di folder pembayaran & membership_payments di storage
        $allFiles = [];
        if (Storage::disk('public')->exists('pembayaran')) {
            $allFiles = array_merge($allFiles, Storage::disk('public')->files('pembayaran'));
        }
        if (Storage::disk('public')->exists('membership_payments')) {
            $allFiles = array_merge($allFiles, Storage::disk('public')->files('membership_payments'));
        }

        $this->line('Total file di storage (pembayaran & membership_payments): ' . count($allFiles));

        // 3. Identifikasi file orphan (ada di disk tapi tidak ada di DB)
        $orphanFiles = array_filter($allFiles, fn($file) => !in_array($file, $validPaths));

        $orphanCount = count($orphanFiles);
        $this->line('File orphan ditemukan: ' . $orphanCount);
        $this->line('');

        if ($orphanCount === 0) {
            $this->info('Tidak ada file orphan. Storage sudah bersih.');
            return self::SUCCESS;
        }

        // 4. Hapus (atau tampilkan saja jika dry-run)
        $deleted   = 0;
        $failed    = 0;
        $totalSize = 0;

        foreach ($orphanFiles as $file) {
            try {
                $size = Storage::disk('public')->size($file);
                $totalSize += $size;

                if ($isDryRun) {
                    $this->line("  [DRY-RUN] Akan dihapus: {$file} (" . number_format($size / 1024, 1) . ' KB)');
                } else {
                    Storage::disk('public')->delete($file);
                    $deleted++;
                    $this->line("  Dihapus: {$file} (" . number_format($size / 1024, 1) . ' KB)');
                }
            } catch (\Exception $e) {
                $failed++;
                Log::warning("[storage:cleanup-payment-files] Gagal menghapus {$file}: " . $e->getMessage());
                $this->warn("  Gagal: {$file} — " . $e->getMessage());
            }
        }

        $sizeMb = number_format($totalSize / (1024 * 1024), 2);

        $this->line('');
        if ($isDryRun) {
            $this->info("DRY RUN selesai. {$orphanCount} file akan dihapus ({$sizeMb} MB).");
            $this->info('Jalankan tanpa --dry-run untuk menghapus sebenarnya.');
        } else {
            $this->info("Selesai! {$deleted} file dihapus, {$failed} gagal. Total ruang dibebaskan: {$sizeMb} MB.");
            Log::info("[storage:cleanup-payment-files] Selesai. Dihapus: {$deleted}, Gagal: {$failed}, Ukuran: {$sizeMb} MB.");
        }

        return self::SUCCESS;
    }
}
