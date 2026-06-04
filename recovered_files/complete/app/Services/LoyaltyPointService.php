<?php

namespace App\Services;

use App\Models\User;
use App\Models\Booking;
use App\Models\PointHistory;
use App\Models\Redemption;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * LoyaltyPointService
 *
 * Pusat seluruh logika bisnis sistem poin loyalitas GOR Anbiyaa.
 * Semua operasi poin (kredit, debit, penukaran) HARUS melalui service ini
 * untuk menjaga konsistensi saldo di tabel users.poin_saldo.
 *
 * Aturan Poin:
 *  - Rasio dasar   : Rp 5.000 = 1 Poin (floor)
 *  - Weekday Off-Peak (07:00–15:xx) = DOUBLE POINTS (×2)
 *  - Fasilitas     : poin tetap per jenis
 *  - Expiry        : 6 bulan sejak kredit
 */
class LoyaltyPointService
{
    // ════════════════════════════════════════════════════════════
    //  KONSTANTA — Sumber Kebenaran Tunggal untuk Aturan Bisnis
    // ════════════════════════════════════════════════════════════

    /** Nilai rupiah per 1 poin dasar */
    const RASIO_POIN = 5000;

    /** Jam mulai Off-Peak (inklusif) */
    const JAM_OFFPEAK_MULAI = 7;

    /** Jam selesai Off-Peak (eksklusif — jam mulai booking HARUS < nilai ini) */
    const JAM_OFFPEAK_SELESAI = 16;