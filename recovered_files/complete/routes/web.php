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
// ══════════════════════════════════════