<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPayment extends Model
{
    protected $table = 'membership_payments';

    protected $fillable = [
        'user_id',
        'paket',
        'jumlah_bayar',
        'metode_pembayaran',
        'bukti_pembayaran',
        'status_verifikasi',
        'catatan_admin',
        'verified_at',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'lapangan_id',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('sidebar_pending_membership');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('sidebar_pending_membership');
        });
    }

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Lapangan
     */
    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class);
    }
}
