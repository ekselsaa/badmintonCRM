<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JadwalPublicController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\LapanganController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminProfilController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\LoyaltyController;

/*
|--------------------------------------------------------------------------
| Web Routes - Badminton CRM System
|--------------------------------------------------------------------------
|
| Struktur route dibagi menjadi 3 kelompok:
|
|  1. PUBLIC  → Bisa diakses tanpa login (/, /jadwal)
|  2. AUTH    → Wajib login tapi bisa admin/pelanggan (/booking, /riwayat)
|  3. ADMIN   → Wajib login + role admin (/admin/*)
|
| Middleware yang digunakan:
|  - 'guest'     → hanya untuk yang BELUM login
|  - 'auth'      → hanya untuk yang SUDAH login
|  - 'admin'     → sudah login + role admin
|  - 'pelanggan' → sudah login + role pelanggan
|
*/

// ════════════════════════════════════════════════════════════════
//  BAGIAN 1: ROUTE PUBLIK — Tidak memerlukan login sama sekali
// ════════════════════════════════════════════════════════════════

/**
 * Halaman utama (/) — Landing page publik.
 * Menampilkan info lapangan, CTA login/booking.
 * Siapapun bisa mengakses, termasuk tamu (belum login).
 */
Route::get('/', [HomeController::class, 'index'])->name('home');

/**
 * Halaman jadwal publik (/jadwal) — Tampilkan slot waktu tersedia.
 * ⚠️ PENTING: Route ini SENGAJA tidak menggunakan middleware 'auth'
 * agar pengunjung tamu bisa melihat jadwal sebelum memutuskan daftar.
 */
Route::prefix('jadwal')->name('jadwal.')->group(function () {
    Route::get('/',        [JadwalPublicController::class, 'index'])->name('index'); // Daftar semua jadwal
    Route::get('/{id}',   [JadwalPublicController::class, 'show'])->name('show');   // Detail per lapangan
});

// Webhook untuk Midtrans Callback
Route::post('/webhook/midtrans', [\App\Http\Controllers\MidtransWebhookController::class, 'handle'])->name('webhook.midtrans');

// ════════════════════════════════════════════════════════════════
//  BAGIAN 2: AUTENTIKASI — Hanya untuk tamu (belum login)
// ════════════════════════════════════════════════════════════════

/**
 * Middleware 'guest' mencegah user yang sudah login mengakses /login & /register.
 * Jika sudah login lalu akses /login → redirect ke home.
 */
Route::middleware(['guest', 'throttle:10,1'])->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login'])->name('login.post');
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
});

// Logout — harus sudah login
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');


// ════════════════════════════════════════════════════════════════
//  BAGIAN 3: ROUTE PELANGGAN — Wajib login + role pelanggan
// ════════════════════════════════════════════════════════════════

/**
 * Semua route booking DI BAWAH INI memerlukan:
 * 1. User sudah login ('auth')
 * 2. User memiliki role 'pelanggan' ('pelanggan')
 *
 * Jika tamu mencoba booking → diarahkan ke halaman login.
 * Jika admin mencoba booking → ditolak (403 Forbidden).
 */
Route::middleware(['auth', 'pelanggan'])->prefix('booking')->name('booking.')->group(function () {
    Route::get('/',            [BookingController::class, 'index'])->name('index');               // Pilih jadwal
    Route::get('/buat',        [BookingController::class, 'create'])->name('create');             // Form booking
    Route::get('/cek-fasilitas', [BookingController::class, 'cekFasilitas'])->name('cek-fasilitas'); // Cek ketersediaan fasilitas via AJAX
    Route::post('/simpan',     [BookingController::class, 'store'])->name('store');               // Proses booking
    Route::get('/riwayat',     [BookingController::class, 'riwayat'])->name('riwayat');           // Riwayat
    Route::get('/{id}',        [BookingController::class, 'show'])->name('show');                 // Detail booking
    Route::get('/{id}/edit',   [BookingController::class, 'edit'])->name('edit');                 // Edit booking
    Route::put('/{id}',        [BookingController::class, 'update'])->name('update');             // Update booking
    Route::post('/{id}/bayar', [BookingController::class, 'uploadPembayaran'])->name('upload.pembayaran'); // Upload bukti
    Route::post('/{id}/ulasan',[BookingController::class, 'storeUlasan'])->name('ulasan');        // Kirim ulasan
});

// ── Profil Pelanggan ─────────────────────────────────────────────
Route::middleware(['auth', 'pelanggan'])->prefix('profil')->name('profil.')->group(function () {
    Route::get('/',  [ProfilController::class, 'show'])->name('show');    // Halaman profil
    Route::put('/',  [ProfilController::class, 'update'])->name('update'); // Update profil
});

// ── Membership ───────────────────────────────────────────────────
Route::middleware(['auth', 'pelanggan'])->get('/membership', [ProfilController::class, 'membership'])->name('membership.index');
Route::middleware(['auth', 'pelanggan'])->post('/membership/bayar', [ProfilController::class, 'bayarMembership'])->name('membership.bayar');

// ── Loyalty Points Pelanggan ──────────────────────────────────
Route::middleware(['auth', 'pelanggan'])->prefix('loyalty')->name('loyalty.')->group(function () {
    Route::get('/',       [ProfilController::class, 'show'])->name('index');     // Dashboard poin (sekarang menyatu ke profil)
    Route::post('/tukar', [LoyaltyController::class, 'tukarPoin'])->name('tukar'); // Tukar poin
});


// ════════════════════════════════════════════════════════════════
//  BAGIAN 4: ROUTE ADMIN — Wajib login + role admin
// ════════════════════════════════════════════════════════════════

/**
 * Semua route admin DI BAWAH INI memerlukan:
 * 1. User sudah login ('auth')
 * 2. User memiliki role 'admin' ('admin')
 *
 * Jika tamu mengakses → diarahkan ke login.
 * Jika pelanggan mengakses → ditolak (403 Forbidden).
 */
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // Dashboard ringkasan statistik
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Endpoint API untuk Notifikasi Desktop Admin
    Route::get('/api/pending-verif', [AdminController::class, 'getPendingVerifCount'])->name('api.pending-verif');

    // ── Kelola Lapangan (CRUD) ──────────────────────────────────
    Route::prefix('lapangan')->name('lapangan.')->group(function () {
        Route::get('/',          [LapanganController::class, 'index'])->name('index');
        Route::get('/tambah',    [LapanganController::class, 'create'])->name('create');
        Route::post('/simpan',   [LapanganController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [LapanganController::class, 'edit'])->name('edit');
        Route::put('/{id}',      [LapanganController::class, 'update'])->name('update');
        Route::delete('/{id}',   [LapanganController::class, 'destroy'])->name('destroy');
    });

    // ── Kelola Jadwal ───────────────────────────────────────
    Route::prefix('jadwal')->name('jadwal.')->group(function () {
        Route::get('/',                  [LapanganController::class, 'jadwalIndex'])->name('index');
        Route::post('/simpan',           [LapanganController::class, 'jadwalStore'])->name('store');
        Route::post('/booking-offline',  [LapanganController::class, 'bookingOfflineStore'])->name('booking-offline.store');
        Route::delete('/{id}',           [LapanganController::class, 'jadwalDestroy'])->name('destroy');
    });

    // ── Kelola Fasilitas (Stok & Harga) ──────────────────────────
    Route::prefix('fasilitas')->name('fasilitas.')->group(function () {
        Route::get('/',          [\App\Http\Controllers\Admin\FasilitasController::class, 'index'])->name('index');
        Route::post('/simpan',   [\App\Http\Controllers\Admin\FasilitasController::class, 'store'])->name('store');
        Route::put('/{id}',      [\App\Http\Controllers\Admin\FasilitasController::class, 'update'])->name('update');
        Route::delete('/{id}',   [\App\Http\Controllers\Admin\FasilitasController::class, 'destroy'])->name('destroy');
    });

    // ── Kelola Booking ──────────────────────────────────────────
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/',            [AdminController::class, 'bookingIndex'])->name('index');
        Route::get('/{id}',        [AdminController::class, 'bookingShow'])->name('show');
        Route::put('/{id}/status', [AdminController::class, 'bookingUpdateStatus'])->name('status');
        Route::get('/{id}/fasilitas', [AdminController::class, 'bookingGetFasilitas'])->name('fasilitas.get');
        Route::put('/{id}/fasilitas', [AdminController::class, 'bookingUpdateFasilitas'])->name('fasilitas.update');
    });

    // ── Kelola Hari Libur ───────────────────────────────────────────
    Route::post('/libur/store', [LapanganController::class, 'liburStore'])->name('libur.store');
    Route::delete('/libur/{id}', [LapanganController::class, 'liburDestroy'])->name('libur.destroy');

    // ── Verifikasi Pembayaran ─────────────────────────────────────
    Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
        Route::get('/',           [AdminController::class, 'pembayaranIndex'])->name('index');
        Route::put('/{id}/verif', [AdminController::class, 'verifikasiPembayaran'])->name('verifikasi');
    });

    // ── Verifikasi Pembayaran Membership ──────────────────────────
    Route::prefix('pembayaran-membership')->name('pembayaran-membership.')->group(function () {
        Route::get('/',           [AdminController::class, 'membershipPembayaranIndex'])->name('index');
        Route::put('/{id}/verif', [AdminController::class, 'verifikasiPembayaranMembership'])->name('verifikasi');
    });

    // ── CRM Pelanggan ───────────────────────────────────────────
    Route::prefix('crm')->name('crm.')->group(function () {
        Route::get('/pelanggan',      [AdminController::class, 'pelangganIndex'])->name('pelanggan');
        Route::get('/pelanggan/{id}', [AdminController::class, 'pelangganDetail'])->name('pelanggan.detail');
        Route::put('/pelanggan/{id}/toggle-member', [AdminController::class, 'pelangganToggleMember'])->name('pelanggan.toggle-member');
        Route::post('/pelanggan/{id}/adjust-points', [AdminController::class, 'pelangganAdjustPoints'])->name('pelanggan.adjust-points');
        Route::delete('/pelanggan/offline', [AdminController::class, 'pelangganDestroyOffline'])->name('pelanggan.destroy-offline');
    });

    // ── Laporan ─────────────────────────────────────────────────
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/',              [AdminController::class, 'laporanIndex'])->name('index');        // Halaman laporan
        Route::get('/export-pdf',    [AdminController::class, 'exportPdf'])->name('export.pdf');     // Export PDF
        Route::get('/export-excel',  [AdminController::class, 'exportExcel'])->name('export.excel'); // Export CSV/Excel
    });

    // ── Ulasan Pelanggan ─────────────────────────────────────────
    Route::prefix('ulasan')->name('ulasan.')->group(function () {
        Route::get('/', [AdminController::class, 'ulasanIndex'])->name('index');
        Route::put('/{id}/toggle-beranda', [AdminController::class, 'ulasanToggleBeranda'])->name('toggle-beranda');
    });

    // ── Loyalty Admin: Lihat Poin & Kredit Manual Member ─────────────
    Route::prefix('loyalty')->name('loyalty.')->group(function () {
        Route::get('/',                  [\App\Http\Controllers\Admin\LoyaltyAdminController::class, 'index'])->name('index');          // Dashboard loyalty admin
        Route::post('/kredit-member',    [\App\Http\Controllers\Admin\LoyaltyAdminController::class, 'kreditMember'])->name('kredit-member'); // Kredit poin paket member
        Route::post('/klaim-voucher',    [\App\Http\Controllers\Admin\LoyaltyAdminController::class, 'klaimVoucher'])->name('klaim-voucher'); // Admin klaim voucher pelanggan
    });

    // ── Profil Admin ─────────────────────────────────────────────
    Route::prefix('profil')->name('profil.')->group(function () {
        Route::get('/',  [AdminProfilController::class, 'show'])->name('show');
        Route::put('/',  [AdminProfilController::class, 'update'])->name('update');
    });
});
