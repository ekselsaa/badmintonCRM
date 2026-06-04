<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model PointHistory
 *
 * Merepresentasikan setiap transaksi poin (kredit atau debit).
 * Ini adalah audit trail lengkap — jangan pernah hapus record di sini,
 * tandai dengan is_expired = true jika perlu di-invalidate.
 *
 * @property int         $id
 * @property int         $user_id
 * @property int|null    $booking_id
 * @property string      $tipe         'kredit' | 'debit'
 * @property int         $jumlah_poin
 * @property string      $sumber
 * @property string|null $keterangan
 * @property \Carbon\Carbon|null $expired_at
 * @property bool        $is_expired
 */
class PointHistory extends Model
{
    protected $table = 'points_history';

    protected $fillable = [
        'user_id',
        'booking_id',
        'tipe',
        'jumlah_poin',
        'poin_saldo_after',
        'sumber',
        'keterangan',
        'expired_at',
        'is_expired',
    ];

    protected $casts = [
        'expired_at' => 'datetime',
        'is_expired' => 'boolean',
        'poin_saldo_after' => 'integer',
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
     * Hanya entri kredit yang masih aktif (belum expired).
     */
    public function scopeAktif($query)
    {
        return $query->where('tipe', 'kredit')
                     ->where('is_expired', false)
                     ->where(function ($q) {
                         $q->whereNull('expired_at')
                           ->orWhere('expired_at', '>', now());
                     });
    }

    /**
     * Hanya entri kredit.
     */
    public function scopeKredit($query)
    {
        return $query->where('tipe', 'kredit');
    }

    /**
     * Hanya entri debit.
     */
    public function scopeDebit($query)
    {
        return $query->where('tipe', 'debit');
    }

    // ─── Accessor ───────────────────────────────────────────────

    /**
     * Label tipe yang ramah untuk tampilan.
     */
    public function getLabelTipeAttribute(): string
    {
        return $this->tipe === 'kredit' ? 'Poin Masuk' : 'Poin Keluar';
    }

    /**
     * CSS class warna badge untuk tampilan UI.
     */
    public function getBadgeTipeAttribute(): string
    {
        return $this->tipe === 'kredit' ? 'badge-success' : 'badge-danger';
    }

    /**
     * Prefix simbol poin (+/-) untuk tampilan.
     */
    public function getPoinFormattedAttribute(): string
    {
        $prefix = $this->tipe === 'kredit' ? '+' : '-';
        return $prefix . number_format($this->jumlah_poin);
    }

    /**
     * Label sumber yang human-readable.
     */
    public function getLabelSumberAttribute(): string
    {
        return match($this->sumber) {
            'sewa_lapangan_offpeak'    => '🌅 Sewa Lapangan (Off-Peak ×2)',
            'sewa_lapangan_peak'       => '🏸 Sewa Lapangan',
            'sewa_raket'               => '🎾 Sewa Raket',
            'beli_kok_satuan'          => '🏸 Beli Kok Satuan',
            'beli_kok_slop'            => '📦 Beli Kok Slop',
            'paket_member_pagi_siang'  => '🌄 Paket Member Pagi/Siang',
            'paket_member_malam'       => '🌙 Paket Member Malam',
            'paket_member_weekend'     => '🎉 Paket Member Weekend',
            'penukaran_kok_satuan'     => '🎁 Tukar: Kok Satuan',
            'penukaran_raket'          => '🎁 Tukar: Sewa Raket',
            'penukaran_lapangan_offpeak' => '🎁 Tukar: Lap. Off-Peak',
            'penukaran_voucher_50k'    => '🎫 Tukar: Voucher Rp 50k',
            'penukaran_lapangan_peak'  => '🎁 Tukar: Lap. Peak',
            'penukaran_voucher_member' => '👑 Tukar: Voucher Member',
            'kadaluwarsa'              => '⌛ Poin Kadaluwarsa',
            default                    => ucfirst(str_replace('_', ' ', $this->sumber)),
        };
    }
}
