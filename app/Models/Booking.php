<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use \App\Traits\NormalizePhoneNumber;
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

                if (!$wasConfirmed && $isConfirmed) {
                    $booking->adjustFasilitasStock('decrement');
                } elseif ($wasConfirmed && !$isConfirmed) {
                    $booking->adjustFasilitasStock('increment');
                }

                // Auto-refund voucher jika status diubah menjadi 'dibatalkan'
                if ($newStatus === 'dibatalkan') {
                    Redemption::where('booking_id', $booking->id)->update([
                        'status'          => 'aktif',
                        'digunakan_pada'  => null,
                        'booking_id'      => null,
                    ]);
                    Voucher::where('booking_id', $booking->id)->update([
                        'status'          => 'aktif',
                        'digunakan_pada'  => null,
                        'booking_id'      => null,
                    ]);

                    if ($wasConfirmed) {
                        $loyaltyService = new \App\Services\LoyaltyPointService();
                        $loyaltyService->debitPoinDariBatalBooking($booking);
                    }
                }
            }
        });

        // 3. Saat booking dihapus
        static::deleting(function ($booking) {
            Redemption::where('booking_id', $booking->id)->update([
                'status'          => 'aktif',
                'digunakan_pada'  => null,
                'booking_id'      => null,
            ]);
            Voucher::where('booking_id', $booking->id)->update([
                'status'          => 'aktif',
                'digunakan_pada'  => null,
                'booking_id'      => null,
            ]);

            // Jika status booking aktif/selesai dihapus, kembalikan/refund stok consumable
            if (in_array($booking->status, ['dipesan', 'selesai'])) {
                $booking->adjustFasilitasStock('increment');

                $loyaltyService = new \App\Services\LoyaltyPointService();
                $loyaltyService->debitPoinDariBatalBooking($booking);
            }

            // Reset status jadwal ke 'tersedia' jika tidak ada booking aktif lain untuk jadwal ini
            if ($booking->jadwal_id) {
                $otherBookings = static::where('jadwal_id', $booking->jadwal_id)
                    ->where('id', '!=', $booking->id)
                    ->whereIn('status', ['pending', 'dikonfirmasi', 'dipesan'])
                    ->exists();

                if (!$otherBookings) {
                    Jadwal::where('id', $booking->jadwal_id)->update(['status' => 'tersedia']);
                }
            }
        });
    }

    /** Menyesuaikan stok fisik fasilitas habis pakai (consumable) */
    public function adjustFasilitasStock(string $action = 'decrement'): void
    {
        // Pastikan relasi bookingFasilitas dimuat
        $this->load('bookingFasilitas.fasilitas');

        foreach ($this->bookingFasilitas as $bf) {
            $fasilitas = $bf->fasilitas;
            if ($fasilitas) {
                $namaLower = strtolower($fasilitas->nama);
                // Hanya kurangi stok untuk consumable (bukan raket / sewa)
                $isConsumable = !str_contains($namaLower, 'raket') && !str_contains($namaLower, 'sewa');

                if ($isConsumable) {
                    if ($action === 'decrement') {
                        $fasilitas->decrement('stok', $bf->jumlah);
                    } else {
                        $fasilitas->increment('stok', $bf->jumlah);
                    }
                }
            }
        }
    }

    /** Nama tampilan pemesan (offline atau akun pelanggan) */
    public function getNamaPemesanAttribute(): string
    {
        if ($this->is_offline) {
            return ($this->nama_pemesan_offline ?? 'Pemesan Offline') . ' (Offline)';
        }
        return $this->user?->name ?? '-';
    }

    public function user()      { return $this->belongsTo(User::class); }
    public function jadwal()    { return $this->belongsTo(Jadwal::class); }
    public function lapangan()  { return $this->belongsTo(Lapangan::class); }
    public function pembayaran(){ return $this->hasOne(Pembayaran::class); }
    public function bookingFasilitas(){ return $this->hasMany(BookingFasilitas::class); }
    public function redemption(){ return $this->hasOne(Redemption::class); }
    public function voucher()   { return $this->belongsTo(Voucher::class); }
    public function redemptions(){ return $this->hasMany(Redemption::class); }
    public function vouchers()   { return $this->hasMany(Voucher::class); }

    /**
     * Cancel booking pending yang sudah expired langsung via SQL — tanpa overhead Artisan bootstrap.
     * Menggunakan cache lock 60 detik agar hanya berjalan max 1x per menit per server.
     */
    public static function cancelExpiredGracefully(): void
    {
        // Lock 60 detik: jika sudah ada proses lain yang berjalan, skip
        if (!\Illuminate\Support\Facades\Cache::add('booking_cancel_expired_running', true, 60)) {
            return;
        }

        try {
            $now = Carbon::now();

            // 1. Expire booking QRIS/belum pilih metode: lebih dari 15 menit setelah dibuat
            $qrisExpiredIds = static::where('status', 'pending')
                ->whereDoesntHave('pembayaran', fn($q) => $q->whereNotNull('bukti_pembayaran'))
                ->where(function ($q) {
                    $q->whereDoesntHave('pembayaran')
                      ->orWhereHas('pembayaran', fn($q2) => $q2->where('metode_pembayaran', '!=', 'tunai'));
                })
                ->where('created_at', '<=', $now->copy()->subMinutes(15))
                ->pluck('id')->toArray();

            // 2. Expire booking tunai: 45 menit sebelum jadwal main
            $tunaiExpiredIds = static::where('status', 'pending')
                ->whereDoesntHave('pembayaran', fn($q) => $q->whereNotNull('bukti_pembayaran'))
                ->whereHas('pembayaran', fn($q) => $q->where('metode_pembayaran', 'tunai'))
                ->whereHas('jadwal', function ($q) use ($now) {
                    $q->whereRaw("TIMESTAMP(tanggal, jam_mulai) <= ?", [$now->copy()->addMinutes(45)->toDateTimeString()]);
                })
                ->pluck('id')->toArray();

            $expiredIds = array_unique(array_merge($qrisExpiredIds, $tunaiExpiredIds));

            if (!empty($expiredIds)) {
                // Ambil jadwal_id sebelum dihapus
                $expiredJadwalIds = static::whereIn('id', $expiredIds)->pluck('jadwal_id')->filter()->unique()->toArray();

                // Release vouchers/redemptions first
                Redemption::whereIn('booking_id', $expiredIds)->update([
                    'status'          => 'aktif',
                    'digunakan_pada'  => null,
                    'booking_id'      => null,
                ]);
                Voucher::whereIn('booking_id', $expiredIds)->update([
                    'status'          => 'aktif',
                    'digunakan_pada'  => null,
                    'booking_id'      => null,
                ]);

                // Hapus child records + booking
                \App\Models\BookingFasilitas::whereIn('booking_id', $expiredIds)->delete();
                \App\Models\Pembayaran::whereIn('booking_id', $expiredIds)->delete();
                static::whereIn('id', $expiredIds)->delete();

                // Kembalikan jadwal yang sudah kosong menjadi 'tersedia'
                if (!empty($expiredJadwalIds)) {
                    $masihAdaBooking = static::whereIn('jadwal_id', $expiredJadwalIds)
                        ->whereIn('status', ['pending', 'dikonfirmasi', 'dipesan'])
                        ->pluck('jadwal_id')->toArray();

                    $bebasIds = array_diff($expiredJadwalIds, $masihAdaBooking);
                    if (!empty($bebasIds)) {
                        Jadwal::whereIn('id', $bebasIds)->update(['status' => 'tersedia']);
                    }
                }
            }

            // 3. Auto-selesaikan booking 'dipesan' yang jadwalnya sudah lewat
            $todayStr = $now->toDateString();
            $timeStr  = $now->toTimeString();

            static::where('status', 'dipesan')
                ->whereHas('jadwal', function ($q) use ($todayStr, $timeStr) {
                    $q->where('tanggal', '<', $todayStr)
                      ->orWhere(function ($q2) use ($todayStr, $timeStr) {
                          $q2->where('tanggal', '=', $todayStr)
                             ->where(function ($q3) use ($timeStr) {
                                 $q3->where('jam_selesai', '<=', $timeStr)
                                    ->where('jam_selesai', '!=', '24:00:00');
                             });
                      })
                      ->orWhere(function ($q2) use ($todayStr, $timeStr) {
                          $q2->where('tanggal', '=', $todayStr)
                             ->where('jam_selesai', '=', '24:00:00')
                             ->whereRaw("? >= '23:59:59'", [$timeStr]);
                      });
                })
                ->update(['status' => 'selesai']);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('cancelExpiredGracefully error: ' . $e->getMessage());
        }
    }

    /**
     * Mutator untuk menormalisasi nomor HP pemesan offline.
     */
    public function setNoHpOfflineAttribute($value)
    {
        $this->attributes['no_hp_offline'] = $this->normalizePhoneNumber($value);
    }
}
