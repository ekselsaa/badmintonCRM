<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';
    protected $fillable = ['nama', 'harga', 'stok', 'icon', 'is_active'];

    public function bookingFasilitas()
    {
        return $this->hasMany(BookingFasilitas::class);
    }

    /**
     * Menghitung ketersediaan fasilitas pada slot waktu tertentu
     * dan mencari jam ketersediaan berikutnya jika stok tidak cukup.
     */
    public function checkAvailability($tanggal, $jamMulai, $jamSelesai, $requestedQty, $excludeBookingId = null, $preloadedBookings = null, $preloadedPendingQty = null)
    {
        $namaLower = strtolower($this->nama);
        $isRental = str_contains($namaLower, 'raket') || str_contains($namaLower, 'sewa');

        if (!$isRental) {
            // Untuk consumable: sisa stok = stok di DB - qty di booking pending lainnya
            if ($preloadedPendingQty !== null) {
                $pendingQty = $preloadedPendingQty;
            } else {
                $pendingQuery = BookingFasilitas::where('fasilitas_id', $this->id)
                    ->whereHas('booking', function ($query) use ($excludeBookingId) {
                        $query->where('status', 'pending');
                        if ($excludeBookingId) {
                            $query->where('id', '!=', $excludeBookingId);
                        }
                    });

                $pendingQty = $pendingQuery->sum('jumlah');
            }
            $availableCurrent = $this->stok - $pendingQty;

            if ($availableCurrent >= $requestedQty) {
                return [
                    'status' => 'tersedia',
                    'sisa_stok' => $availableCurrent < 0 ? 0 : $availableCurrent,
                    'tersedia_pada' => null
                ];
            } else {
                return [
                    'status' => 'penuh',
                    'sisa_stok' => $availableCurrent < 0 ? 0 : $availableCurrent,
                    'tersedia_pada' => null
                ];
            }
        }

        $totalStok = $this->stok; // Stok fisik maksimal

        // 1. Ambil semua booking aktif pada tanggal tersebut yang menyewa fasilitas ini
        if ($preloadedBookings !== null) {
            $activeBookings = $preloadedBookings->filter(function ($booking) {
                return $booking->bookingFasilitas->contains('fasilitas_id', $this->id);
            });
        } else {
            $activeBookingsQuery = Booking::whereIn('status', ['pending', 'dikonfirmasi', 'dipesan'])
                ->where('tanggal_booking', $tanggal)
                ->whereHas('bookingFasilitas', function ($query) {
                    $query->where('fasilitas_id', $this->id);
                });

            if ($excludeBookingId) {
                $activeBookingsQuery->where('id', '!=', $excludeBookingId);
            }

            $activeBookings = $activeBookingsQuery->with(['jadwal', 'bookingFasilitas' => function ($query) {
                $query->where('fasilitas_id', $this->id);
            }])->get();
        }

        // 2. Hitung jumlah raket/fasilitas yang sedang disewa pada slot waktu yang diuji
        $testStart = Carbon::parse($jamMulai)->format('H:i');
        $testEnd = Carbon::parse($jamSelesai)->format('H:i');

        $testStartMins = (int)explode(':', $testStart)[0] * 60 + (int)explode(':', $testStart)[1];
        $testEndMins   = (int)explode(':', $testEnd)[0]   * 60 + (int)explode(':', $testEnd)[1];

        $rentedCurrent = 0;
        foreach ($activeBookings as $booking) {
            if ($booking->jadwal) {
                $start = Carbon::parse($booking->jadwal->jam_mulai)->format('H:i');
                $end = Carbon::parse($booking->jadwal->jam_selesai)->format('H:i');

                $startMins = (int)explode(':', $start)[0] * 60 + (int)explode(':', $start)[1];
                $endMins   = (int)explode(':', $end)[0]   * 60 + (int)explode(':', $end)[1];

                // Cek overlap dengan perbandingan menit integer
                if ($startMins < $testEndMins && $endMins > $testStartMins) {
                    $rentedCurrent += $booking->bookingFasilitas->where('fasilitas_id', $this->id)->sum('jumlah');
                }
            }
        }

        $availableCurrent = $totalStok - $rentedCurrent;

        // Jika stok mencukupi, kembalikan status tersedia
        if ($availableCurrent >= $requestedQty) {
            return [
                'status' => 'tersedia',
                'sisa_stok' => $availableCurrent,
                'tersedia_pada' => null
            ];
        }

        // 3. JIKA TIDAK CUKUP: Cari kapan stok akan tersedia kembali
        // Hanya hitung tersedia_pada jika ini adalah fasilitas rental sewa raket
        $nextAvailableTime = null;
        if (str_contains(strtolower($this->nama), 'raket')) {
            // Buat daftar event pengembalian fasilitas (jam selesai booking yang overlap)
            $events = [];
            foreach ($activeBookings as $booking) {
                if ($booking->jadwal) {
                    $start = Carbon::parse($booking->jadwal->jam_mulai)->format('H:i');
                    $end = Carbon::parse($booking->jadwal->jam_selesai)->format('H:i');

                    $startMins = (int)explode(':', $start)[0] * 60 + (int)explode(':', $start)[1];
                    $endMins   = (int)explode(':', $end)[0]   * 60 + (int)explode(':', $end)[1];

                    // Kita hanya peduli booking yang overlap dengan waktu pencarian
                    if ($startMins < $testEndMins && $endMins > $testStartMins) {
                        $events[] = [
                            'time' => $end,
                            'time_mins' => $endMins,
                            'qty' => $booking->bookingFasilitas->where('fasilitas_id', $this->id)->sum('jumlah')
                        ];
                    }
                }
            }

            // Urutkan event berdasarkan jam selesai paling awal
            usort($events, function ($a, $b) {
                return $a['time_mins'] <=> $b['time_mins'];
            });

            $simulatedRented = $rentedCurrent;

            foreach ($events as $event) {
                $simulatedRented -= $event['qty'];
                $simulatedAvailable = $totalStok - $simulatedRented;

                if ($simulatedAvailable >= $requestedQty) {
                    $nextAvailableTime = Carbon::parse($event['time'])->format('H:i');
                    break;
                }
            }
        }

        return [
            'status' => 'penuh',
            'sisa_stok' => $availableCurrent < 0 ? 0 : $availableCurrent,
            'tersedia_pada' => $nextAvailableTime
        ];
    }
}
