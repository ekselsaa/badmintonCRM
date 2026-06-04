<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table    = 'pembayaran';
    protected $fillable = [
        'booking_id', 'bukti_pembayaran', 'jumlah_bayar',
        'metode_pembayaran', 'status_verifikasi', 'catatan_admin', 'verified_at'
    ];
    protected $casts = ['verified_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();

        static::saved(function () {
            \Illuminate\Support\Facades\Cache::forget('sidebar_pending_booking');
        });

        static::deleted(function () {
            \Illuminate\Support\Facades\Cache::forget('sidebar_pending_booking');
        });
    }

    public function booking() { return $this->belongsTo(Booking::class); }
}
