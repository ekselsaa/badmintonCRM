<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

/**
 * Model Voucher
 *
 * Merepresentasikan voucher reward status keanggotaan "Sapu Bersih".
 *
 * @property int         $id
 * @property int         $user_id
 * @property string      $voucher_code
 * @property string      $tipe_voucher    'ally' | 'partner' | 'loyalist' | 'vip'
 * @property string      $status          'aktif' | 'digunakan' | 'kadaluwarsa'
 * @property Carbon|null $expired_date
 * @property int|null    $booking_id
 * @property Carbon|null $digunakan_pada
 */
class Voucher extends Model
{
    protected $fillable = [
        'user_id',
        'voucher_code',
        'tipe_voucher',
        'status',
        'expired_date',
        'booking_id',
        'digunakan_pada',
    ];

    protected $casts = [
        'expired_date'   => 'datetime',
        'digunakan_pada' => 'datetime',
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

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif')
                     ->where(function ($q) {
                         $q->whereNull('expired_date')
                           ->orWhere('expired_date', '>', now());
                     });
    }

    // ─── Accessor ───────────────────────────────────────────────

    /**
     * Label nama hadiah yang human-readable.
     */
    public function getLabelHadiahAttribute(): string
    {
        return match($this->tipe_voucher) {
            'ally'     => 'Gratis Anbiyaa Water',
            'partner'  => 'Gratis Sewa Raket 1 Sesi',
            'loyalist' => 'Gratis 1 Jam Lapangan Off-Peak',
            'vip'      => 'Voucher VIP Potongan Rp 100.000',
            default    => 'Voucher GOR Anbiyaa',
        };
    }

    /**
     * Deskripsi voucher.
     */
    public function getDeskripsiAttribute(): string
    {
        return match($this->tipe_voucher) {
            'ally'     => 'Klaim 1 botol Anbiyaa Water dingin di meja kasir.',
            'partner'  => 'Gratis biaya sewa 1 buah raket badminton selama 1 sesi.',
            'loyalist' => 'Gratis sewa lapangan badminton selama 1 jam pada jam off-peak (07:00-16:00).',
            'vip'      => 'Potongan harga Rp 100.000 untuk perpanjangan member minimal 1 bulan.',
            default    => 'Voucher loyalitas GOR Anbiyaa.',
        };
    }

    /**
     * Icon emoji hadiah.
     */
    public function getIconHadiahAttribute(): string
    {
        return match($this->tipe_voucher) {
            'ally'     => '💧',
            'partner'  => '🎾',
            'loyalist' => '☀️',
            'vip'      => '💎',
            default    => '🎁',
        };
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
            && (!$this->expired_date || $this->expired_date->isFuture());
    }

    /**
     * Tampilkan kode voucher.
     */
    public function getKodeDisplayAttribute(): string
    {
        return strtoupper($this->voucher_code);
    }
}
