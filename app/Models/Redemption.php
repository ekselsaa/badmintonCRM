<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Redemption
 *
 * Merepresentasikan setiap penukaran poin menjadi voucher hadiah.
 * Setiap redemption punya kode UUID unik yang digunakan pelanggan
 * untuk klaim hadiah di meja kasir.
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $jenis_hadiah
 * @property int         $poin_digunakan
 * @property string      $kode_voucher
 * @property string      $status         'aktif' | 'digunakan' | 'kadaluwarsa'
 * @property \Carbon\Carbon|null $digunakan_pada
 * @property \Carbon\Carbon|null $kode_expired_at
 */
class Redemption extends Model
{
    protected $fillable = [
        'user_id',
        'booking_id',
        'jenis_hadiah',
        'poin_digunakan',
        'kode_voucher',
        'status',
        'digunakan_pada',
        'kode_expired_at',
    ];

    protected $casts = [
        'digunakan_pada'  => 'datetime',
        'kode_expired_at' => 'datetime',
    ];

    // ─── Relasi ─────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // ─── Scopes ─────────────────────────────────────────────────

    /**
     * Voucher yang masih aktif dan belum kadaluwarsa.
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')
                     ->where('kode_expired_at', '>', now());
    }

    // ─── Accessor ───────────────────────────────────────────────

    /**
     * Label nama hadiah yang human-readable dari konstanta service.
     */
    public function getLabelHadiahAttribute(): string
    {
        return \App\Services\LoyaltyPointService::REDEEM[$this->jenis_hadiah]['label']
            ?? ucfirst(str_replace('_', ' ', $this->jenis_hadiah));
    }

    /**
     * Icon emoji hadiah.
     */
    public function getIconHadiahAttribute(): string
    {
        return \App\Services\LoyaltyPointService::REDEEM[$this->jenis_hadiah]['icon'] ?? '🎁';
    }

    /**
     * Badge CSS class berdasarkan status voucher.
     */
    public function getBadgeStatusAttribute(): string
    {
        return match($this->status) {
            'aktif'      => 'badge-success',
            'digunakan'  => 'badge-secondary',
            'kadaluwarsa'=> 'badge-danger',
            default      => 'badge-secondary',
        };
    }

    /**
     * Apakah voucher masih bisa digunakan.
     */
    public function getIsValidAttribute(): bool
    {
        return $this->status === 'aktif'
            && $this->kode_expired_at
            && $this->kode_expired_at->isFuture();
    }

    /**
     * Tampilkan kode voucher yang diformat (uppercase, kelompok 4 karakter).
     * UUID biasanya sudah dalam format yang bagus, jadi langsung uppercase.
     */
    public function getKodeDisplayAttribute(): string
    {
        return strtoupper(substr($this->kode_voucher, 0, 8));
    }
}
