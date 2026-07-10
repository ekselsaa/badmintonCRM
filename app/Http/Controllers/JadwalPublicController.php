<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use App\Models\Jadwal;
use Illuminate\Http\Request;

/**
 * JadwalPublicController - Menampilkan jadwal lapangan SECARA PUBLIK.
 *
 * Perbedaan dengan BookingController:
 * - Tidak memerlukan login untuk MELIHAT jadwal
 * - Hanya booking yang memerlukan login (dihandle di BookingController)
 */
class JadwalPublicController extends Controller
{
    /**
     * Halaman jadwal publik (/jadwal).
     * Siapapun bisa melihat jadwal yang tersedia tanpa perlu login.
     */
    public function index(Request $request)
    {
        $tanggal       = $request->get('tanggal', today()->toDateString());
        $lapangan_id   = $request->get('lapangan_id');
        $status_filter = $request->get('status');

        // Semua lapangan untuk filter dropdown
        $lapangans = Lapangan::orderBy('status', 'asc')->orderBy('nama_lapangan', 'asc')->get();

        // Ambil semua jadwal (dipesan, pending, dan ditutup/diblokir admin)
        $booked_jadwals = Jadwal::with(['lapangan', 'booking.user'])
            ->where('tanggal', \Carbon\Carbon::parse($tanggal))
            ->whereIn('status', ['dipesan', 'pending', 'ditutup'])
            ->get();

        $lapangansQuery = Lapangan::orderBy('status', 'asc')->orderBy('nama_lapangan', 'asc');
        if ($lapangan_id) {
            $lapangansQuery->where('id', $lapangan_id);
        }
        $lapangansTampil = $lapangansQuery->get();

        $jadwalPerLapangan = collect();
        $totalSlotTersedia = 0;

        $isWeekend = \Carbon\Carbon::parse($tanggal)->isWeekend();

        $hariLiburs = \App\Models\HariLibur::where('tanggal', $tanggal)->get();

        // Preload membership payments for this day of week to prevent N+1 queries in the loop
        $dayNames = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $dayOfWeek = $dayNames[\Carbon\Carbon::parse($tanggal)->dayOfWeek];
        $memberPayments = \App\Models\MembershipPayment::where('hari', $dayOfWeek)
            ->whereIn('status_verifikasi', ['menunggu', 'diverifikasi'])
            ->where(function ($q) use ($tanggal) {
                $q->where('status_verifikasi', 'menunggu')
                  ->orWhereHas('user', function ($qu) use ($tanggal) {
                      $qu->whereIn('kategori_member', ['member', 'weekday_pagi', 'weekday_malam', 'weekend'])
                        ->where(function ($query) use ($tanggal) {
                            $query->whereNull('membership_expires_at')
                                  ->orWhere('membership_expires_at', '>=', \Carbon\Carbon::parse($tanggal)->startOfDay());
                        });
                  });
            })
            ->with('user')
            ->get();

        foreach ($lapangansTampil as $lap) {
            $slots = collect();
            $lap_bookings = $booked_jadwals->where('lapangan_id', $lap->id);

            $libur = $hariLiburs->first(function ($hl) use ($lap) {
                return $hl->lapangan_id === null || $hl->lapangan_id === $lap->id;
            });

            // Generate slot 1 jam-an dari 07:00 sampai 24:00
            for ($i = 7; $i < 24; $i++) {
                $start = sprintf('%02d:00:00', $i);
                $end   = $i == 23 ? '23:59:00' : sprintf('%02d:00:00', $i + 1);

                if ($lap->status !== 'aktif') {
                    $status = 'ditutup';
                    $keterangan = 'Tidak Aktif';
                } elseif ($libur) {
                    $status = 'ditutup';
                    $keterangan = $libur->keterangan ?? 'Libur/Maintenance';
                } else {
                    // Cek status dari database (tersedia / dipesan / pending)
                    $db_slot = $lap_bookings->first(function ($booking) use ($start, $end) {
                        return $booking->jam_mulai < $end && $booking->jam_selesai > $start;
                    });
                    $status = $db_slot ? $db_slot->status : 'tersedia';
                    $keterangan = null;
                    if ($db_slot) {
                        if ($status === 'dipesan' || $status === 'pending') {
                            if ($db_slot->booking) {
                                $keterangan = $db_slot->booking->is_offline 
                                    ? $db_slot->booking->nama_pemesan_offline 
                                    : ($db_slot->booking->user->name ?? null);
                            }
                            if (empty($keterangan) && !empty($db_slot->keterangan)) {
                                $keterangan = str_replace(['Booking Offline: ', 'Slot Member: '], '', $db_slot->keterangan);
                            }
                            if (!empty($keterangan)) {
                                $keterangan = preg_replace('/\s*\(#\d+\)$/', '', $keterangan);
                            }
                        } else {
                            $keterangan = $db_slot->keterangan;
                        }
                    } else {
                        // Cek apakah bentrok dengan slot rutin member (aktif atau pending)
                        $memberSlot = $memberPayments->first(function ($mp) use ($lap, $start, $end) {
                            return $mp->lapangan_id === $lap->id &&
                                   $mp->jam_mulai < $end &&
                                   $mp->jam_selesai > $start;
                        });

                        if ($memberSlot) {
                            $status = 'dipesan';
                            $keterangan = $memberSlot->user->name ?? 'Calon Member';
                        }
                    }
                }
                
                $slotStart = \Carbon\Carbon::parse($tanggal . ' ' . $start);
                $isPast = $slotStart->isPast();

                // Jika statusnya tersedia atau pending, dan belum terlewat, hitung sebagai tersedia
                if (($status === 'tersedia' || $status === 'pending') && !$isPast) {
                    $totalSlotTersedia++;
                }

                $slots->push((object)[
                    'jam_mulai'   => sprintf('%02d:00', $i),
                    'jam_selesai' => $end === '23:59:00' ? '23:59' : sprintf('%02d:00', $i + 1),
                    'status'      => $status,
                    'keterangan'  => $keterangan,
                    'lapangan_id' => $lap->id,
                    'lapangan'    => $lap,
                    'harga'       => $isWeekend ? $lap->harga_weekend : $lap->harga_weekday
                ]);
            }

            // FILTER STATUS di level collection
            if ($status_filter) {
                $slots = $slots->filter(function($slot) use ($status_filter) {
                    return $slot->status === $status_filter;
                });
            }

            $jadwalPerLapangan->put($lap->id, $slots);
        }

        // Untuk menjaga kompatibilitas variabel dengan view
        $jadwals = collect();
        foreach ($jadwalPerLapangan as $slots) {
            $jadwals = $jadwals->merge($slots);
        }

        return view('jadwal.index', compact(
            'lapangans', 'jadwals', 'jadwalPerLapangan', 'tanggal', 'lapangan_id', 'totalSlotTersedia', 'status_filter'
        ));
    }

    /**
     * Detail jadwal satu lapangan (/jadwal/{lapangan}).
     */
    public function show(Request $request, $lapanganId)
    {
        $lapangan = Lapangan::findOrFail($lapanganId);
        $tanggal  = $request->get('tanggal', today()->toDateString());

        $isWeekend = \Carbon\Carbon::parse($tanggal)->isWeekend();

        $booked_jadwals = Jadwal::with('booking.user')
            ->where('lapangan_id', $lapanganId)
            ->where('tanggal', \Carbon\Carbon::parse($tanggal))
            ->whereIn('status', ['dipesan', 'pending', 'ditutup'])
            ->get();

        $hariLiburs = \App\Models\HariLibur::where('tanggal', $tanggal)->get();
        $libur = $hariLiburs->first(function ($hl) use ($lapangan) {
            return $hl->lapangan_id === null || $hl->lapangan_id === $lapangan->id;
        });

        // Preload membership payments for this court and day of week to prevent N+1 queries in the loop
        $dayNames = ['minggu', 'senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu'];
        $dayOfWeek = $dayNames[\Carbon\Carbon::parse($tanggal)->dayOfWeek];
        $memberPayments = \App\Models\MembershipPayment::where('lapangan_id', $lapanganId)
            ->where('hari', $dayOfWeek)
            ->whereIn('status_verifikasi', ['menunggu', 'diverifikasi'])
            ->where(function ($q) use ($tanggal) {
                $q->where('status_verifikasi', 'menunggu')
                  ->orWhereHas('user', function ($qu) use ($tanggal) {
                      $qu->whereIn('kategori_member', ['member', 'weekday_pagi', 'weekday_malam', 'weekend'])
                        ->where(function ($query) use ($tanggal) {
                            $query->whereNull('membership_expires_at')
                                  ->orWhere('membership_expires_at', '>=', \Carbon\Carbon::parse($tanggal)->startOfDay());
                        });
                  });
            })
            ->with('user')
            ->get();

        // Generate slot jam-an dari 07:00 sampai 24:00
        $jadwals = collect();
        for ($i = 7; $i < 24; $i++) {
            $start = sprintf('%02d:00:00', $i);
            $end   = $i == 23 ? '23:59:00' : sprintf('%02d:00:00', $i + 1);

            if ($lapangan->status !== 'aktif') {
                $status = 'ditutup';
                $keterangan = 'Tidak Aktif';
            } elseif ($libur) {
                $status = 'ditutup';
                $keterangan = $libur->keterangan ?? 'Libur/Maintenance';
            } else {
                $db_slot = $booked_jadwals->first(function ($booking) use ($start, $end) {
                    return $booking->jam_mulai < $end && $booking->jam_selesai > $start;
                });
                $status = $db_slot ? $db_slot->status : 'tersedia';
                $keterangan = null;
                if ($db_slot) {
                    if ($status === 'dipesan' || $status === 'pending') {
                        if ($db_slot->booking) {
                            $keterangan = $db_slot->booking->is_offline 
                                ? $db_slot->booking->nama_pemesan_offline 
                                : ($db_slot->booking->user->name ?? null);
                        }
                        if (empty($keterangan) && !empty($db_slot->keterangan)) {
                            $keterangan = str_replace(['Booking Offline: ', 'Slot Member: '], '', $db_slot->keterangan);
                        }
                        if (!empty($keterangan)) {
                            $keterangan = preg_replace('/\s*\(#\d+\)$/', '', $keterangan);
                        }
                    } else {
                        $keterangan = $db_slot->keterangan;
                    }
                } else {
                    // Cek apakah bentrok dengan slot rutin member (aktif atau pending)
                    $memberSlot = $memberPayments->first(function ($mp) use ($start, $end) {
                        return $mp->jam_mulai < $end && $mp->jam_selesai > $start;
                    });

                    if ($memberSlot) {
                        $status = 'dipesan';
                        $keterangan = $memberSlot->user->name ?? 'Calon Member';
                    }
                }
            }

            $jadwals->push((object)[
                'jam_mulai'   => sprintf('%02d:00', $i),
                'jam_selesai' => $end === '23:59:00' ? '23:59' : sprintf('%02d:00', $i + 1),
                'status'      => $status,
                'keterangan'  => $keterangan,
                'lapangan_id' => $lapangan->id,
                'lapangan'    => $lapangan,
                'harga'       => $isWeekend ? $lapangan->harga_weekend : $lapangan->harga_weekday,
            ]);
        }

        return view('jadwal.show', compact('lapangan', 'jadwals', 'tanggal'));
    }
}
