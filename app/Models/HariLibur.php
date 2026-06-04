<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    protected $table = 'hari_liburs';
    protected $fillable = ['tanggal', 'lapangan_id', 'keterangan'];

    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class);
    }
}
