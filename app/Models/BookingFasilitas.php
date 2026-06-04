<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingFasilitas extends Model
{
    protected $table = 'booking_fasilitas';
    protected $fillable = ['booking_id', 'fasilitas_id', 'jumlah', 'harga_satuan', 'subtotal'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function fasilitas()
    {
        return $this->belongsTo(Fasilitas::class);
    }
}
