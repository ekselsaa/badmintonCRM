<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lapangan extends Model
{
    protected $table    = 'lapangan';
    protected $fillable = ['nama_lapangan', 'deskripsi', 'harga_weekday', 'harga_weekend', 'status', 'foto'];

    public function jadwals()  { return $this->hasMany(Jadwal::class); }
    public function bookings() { return $this->hasMany(Booking::class); }

    public function getFotoUrlAttribute(): string
    {
        return $this->foto
            ? asset('storage/' . $this->foto)
            : 'https://via.placeholder.com/400x200/0f172a/ffffff?text=' . urlencode($this->nama_lapangan);
    }
}
