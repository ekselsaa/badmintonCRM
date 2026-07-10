<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table    = 'jadwal';
    protected $fillable = ['lapangan_id', 'tanggal', 'jam_mulai', 'jam_selesai', 'status', 'keterangan'];
    protected $casts    = ['tanggal' => 'date'];

    public function lapangan() { return $this->belongsTo(Lapangan::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    // Alias hasOne untuk backward compat (ambil booking pertama aktif)
    public function booking()  { return $this->hasOne(Booking::class)->whereIn('status', ['pending', 'dikonfirmasi', 'dipesan', 'selesai']); }

    public function scopeTersedia($query) { return $query->where('status', 'tersedia'); }

    public function getHargaAttribute()
    {
        if (!$this->lapangan) return 0;
        $isWeekend = \Carbon\Carbon::parse($this->tanggal)->isWeekend();
        return $isWeekend ? $this->lapangan->harga_weekend : $this->lapangan->harga_weekday;
    }
}
