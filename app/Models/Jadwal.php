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
    public function booking()  { return $this->hasOne(Booking::class); }

    public function scopeTersedia($query) { return $query->where('status', 'tersedia'); }

    public function getHargaAttribute()
    {
        if (!$this->lapangan) return 0;
        $isWeekend = \Carbon\Carbon::parse($this->tanggal)->isWeekend();
        return $isWeekend ? $this->lapangan->harga_weekend : $this->lapangan->harga_weekday;
    }

    /**
     * @todo STUB - Belum diimplementasikan. Selalu return false.
     * Fitur ini untuk memeriksa apakah slot di-cover oleh paket member aktif.
     * Pastikan panggilan ke method ini tidak digunakan dalam logika bisnis kritis.
     */
    public static function isSlotCoveredByActiveMember($tanggal, $jamMulai, $jamSelesai)
    {
        return false; // @stub
    }

    /**
     * @todo STUB - Belum diimplementasikan. Selalu return collect() kosong.
     * Fitur ini untuk generate slot virtual untuk member aktif pada tanggal tertentu.
     */
    public static function getMemberSlotsForDate($tanggal)
    {
        return collect(); // @stub
    }

    /**
     * @todo STUB - Belum diimplementasikan. Hanya return $jadwals apa adanya.
     * Fitur ini untuk menggabungkan jadwal riil dengan slot virtual member aktif.
     */
    public static function mergeWithVirtualMemberSlots($jadwals, $tanggal)
    {
        return $jadwals; // @stub
    }
}
