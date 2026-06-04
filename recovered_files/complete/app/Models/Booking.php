<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id', 'jadwal_id', 'lapangan_id',
        'tanggal_booking', 'total_harga', 'snap_token', 'status', 'catatan', 'fasilitas',
        'is_offline', 'nama_pemesan_offline', 'no_hp_offline',
        'rating', 'ulasan', 'is_tampil_beranda', 'reward_applied', 'voucher_id',
    ];

    protected $casts = [
        'tanggal_booking' => 'date',
        'is_offline'      => 'boolean',
        'reward_applied'  => 'boolean',
        'voucher_id'      => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        // 1. Saat booking baru terbuat dan langsung berstatus dipesan/selesai (e.g. booking offline)
        static::created(function ($booking) {
            if (in_array($booking->status, ['dipesan', 'selesai'])) {
                $booking->adjustFasilitasStock('decrement');
            }
        });

        // 2. Saat status booking berubah
        static::updated(function ($booking) {
            if ($booking->isDirty('status')) {
                $oldStatus = $booking->getOriginal('status');
                $newStatus = $booking->status;

                $wasConfirmed = in_array($oldStatus, ['dipesan', 'selesai']);
                $isConfirmed = in_array($newStatus, ['dipesan', 'selesai']);

                if (!$wasConfirmed && 