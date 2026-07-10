<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-cancel booking yang melewati batas waktu pembayaran (setiap menit)
// Lebih sering agar tidak perlu Artisan::call() di HTTP request
Schedule::command('booking:cancel-expired')->everyMinute();

// Hapus otomatis semua data jadwal dan booking pending yang harinya telah terlewat (berjalan tiap tengah malam)
Schedule::command('jadwal:cleanup-pending')->daily();

// CLEANUP-1: Bersihkan file bukti pembayaran orphan setiap hari jam 03:00
Schedule::command('storage:cleanup-payment-files')->dailyAt('03:00');

// CLEANUP-3: Bersihkan file session Laravel yang sudah kedaluwarsa setiap hari jam 04:00
Schedule::command('session:gc')->dailyAt('04:00');

// ─── Loyalty Points Scheduler ─────────────────────────────────────────────

// Hanguskan poin kredit yang sudah melewati masa kadaluwarsa 6 bulan (tiap hari 01:00)
Schedule::command('loyalty:expire-points')->dailyAt('01:00');

// ─── Loyalty Membership & Voucher Scheduler ───────────────────────────────

// Reset poin bulanan semua pelanggan & evaluasi status keanggotaan (tiap tgl 1 pukul 00:00)
Schedule::command('loyalty:reset-monthly-points')->monthlyOn(1, '00:00');

// Tandai voucher yang sudah melewati masa berlaku (tiap hari 00:10)
Schedule::command('loyalty:check-expired-vouchers')->dailyAt('00:10');

// Cek masa aktif member yang kedaluwarsa (tiap hari jam 00:05)
Schedule::command('membership:check-expiry')->dailyAt('00:05');
