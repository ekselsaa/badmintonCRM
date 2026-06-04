@extends('layouts.app')
@section('title', 'Detail Booking #' . $booking->id)
@section('page_title', 'Detail Booking #' . $booking->id)
@section('page_subtitle', 'Status: ' . ucfirst($booking->status))
@section('topbar_actions')
    <a href="{{ route('admin.booking.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
@endsection

@section('content')
<div class="p-0">
    <div class="row g-3">
        {{-- Kolom Kiri: Informasi Booking --}}
        <div class="col-lg-6">
            <div class="table-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0"><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Informasi Booking</h6>
                </div>

                <div class="mb-3 d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid #f1f5f9">
                    <span class="text-muted small">Status Booking</span>
                    <span class="badge badge-{{ $booking->status }} px-3 py-2 rounded-pill">{{ ucfirst($booking->status) }}</span>
                </div>
                <div class="mb-3 d-flex justify-content-between py-2" style="border-bottom:1px solid #f1f5f9">
                    <span class="text-muted small">Lapangan</span>
                    <span class="fw-600">{{ $booking->lapangan->nama_lapangan ?? '-' }}</span>
                </div>
                <div class="mb-3 d-flex justify-content-between py-2" style="border-bottom:1px solid #f1f5f9">
                    <span class="text-muted small">Tanggal</span>
                    <span class="fw-600">{{ $booking->jadwal ? $booking->jadwal->tanggal->format('d M Y') : '-' }}</span>
                </div>
                <div class="mb-3 d-flex justify-content-between py-2" style="border-bottom:1px solid #f1f5f9">
                    <span class="text-muted small">Jam</span>
                    <span class="fw-600">{{ $booking->jadwal ? \Carbon\Carbon::parse($booking->jadwal->jam_mulai)->format('H:i') . ' - ' . ($booking->jadwal->jam_selesai == '24:00:00' ? '24:00' : \Carbon\Carbon::parse($booking->jadwal->jam_selesai)->format('H:i')) : '-' }}</span>
                </div>
                <div class="mb-3 d-flex justify-content-between py-2" style="border-bottom:1px solid #f1f5f9">
                    <span class="text-muted small">Total Harga</span>
                    <span class="fw-bold text-success fs-6">
                        @if($booking->reward_applied && $booking->total_harga == 0)
                            <span class="text-decoration-line-through text-muted me-1 small">Rp {{ number_format($booking->total_harga + ($booking->jadwal ? ($booking->jadwal->lapangan->harga_weekday) : 0), 0, ',', '.') }}</span> Gratis
                        @else
                            Rp {{ number_format($booking->total_harga, 0, ',', '.') }}
                        @endif
                    </span>
                </div>

                {{-- Estimasi/Poin Diterima --}}
                @php
                    $pointsEarned = \App\Models\PointHistory::where('booking_id', $booking->id)
                        ->where('tipe', 'kredit')
                        ->sum('jumlah_poin');
                    
                    $isEstimated = false;
                    if ($pointsEarned == 0 && ($booking->status === 'pending' || $booking->status === 'menunggu')) {
                        $isEstimated = true;
                        if ($booking->jadwal && $booking->lapangan) {
                            $poinSewa = 0;
                            $hargaFasilitas = $booking->bookingFasilitas->sum('subtotal') ?? 0;
                            $hargaLapangan  = max(0, $booking->total_harga - $hargaFasilitas);
                            if ($hargaLapangan <= 0) {
                                $isWeekend   = \Carbon\Carbon::parse($booking->tanggal_booking)->isWeekend();
                                $hargaPerJam = $isWeekend ? $booking->lapangan->harga_weekend : $booking->lapangan->harga_weekday;
                                $durasi = ceil(\Carbon\Carbon::parse($booking->jadwal->jam_mulai)->diffInMinutes(\Carbon\Carbon::parse($booking->jadwal->jam_selesai)) / 60);
                                $hargaLapangan = $durasi * $hargaPerJam;
                            }
                            if ($hargaLapangan > 0) {
                                $tanggalObj = \Carbon\Carbon::parse($booking->tanggal_booking);
                                $jamMulaiHour = (int) \Carbon\Carbon::parse($booking->jadwal->jam_mulai)->format('H');
                                $isWeekday = !$tanggalObj->isWeekend();
                                $isOffPeakHour = ($jamMulaiHour >= 7 && $jamMulaiHour < 16);
                                $offPeak = $isWeekday && $isOffPeakHour;
                                $multiplier = $offPeak ? 2 : 1;
                                $poinSewa = ((int) floor($hargaLapangan / 5000)) * $multiplier;
                            }
                            
                            $poinFasilitas = 0;
                            foreach ($booking->bookingFasilitas as $pivot) {
                                $nama = strtolower($pivot->fasilitas->nama ?? '');
                                $qty  = (int) $pivot->jumlah;
                                if (str_contains($nama, 'raket')) {
                                    $poinFasilitas += 5 * $qty;
                                } elseif (str_contains($nama, 'slop') || str_contains($nama, 'dos')) {
                                    $poinFasilitas += 27 * $qty;
                                } elseif (str_contains($nama, 'kok') || str_contains($nama, 'shuttlecock')) {
                                    $poinFasilitas += 3 * $qty;
                                }
                            }
                            $pointsEarned = $poinSewa + $poinFasilitas;
                        }
                    }
                @endphp
                @if($pointsEarned > 0)
                <div class="mb-3 d-flex justify-content-between py-2" style="border-bottom:1px solid #f1f5f9">
                    <span class="text-muted small">{{ $isEstimated ? 'Estimasi Poin Didapat' : 'Poin Diterima' }}</span>
                    <span class="fw-bold text-success">
                        <i class="bi bi-gem me-1"></i>+{{ number_format($pointsEarned) }} Poin
                        @if($isEstimated)
                            <span class="text-muted font-normal" style="font-size: 0.7rem; font-weight: normal;">(Setelah verifikasi)</span>
                        @endif
                    </span>
                </div>
                @endif

                @if($booking->catatan)
                <div class="py-2 mb-3" style="border-bottom:1px solid #f1f5f9">
                    <span class="text-muted small d-block mb-1">Catatan</span>
                    <span class="fw-500 text-dark">"{{ $booking->catatan }}"</span>
                </div>
                @endif

                @if($booking->fasilitas)
                <div class="py-2">
                    <span class="text-muted small d-block mb-2 text-primary fw-bold">Fasilitas Tambahan</span>
                    <div class="d-flex flex-wrap gap-1">
                        @foreach(explode(', ', $booking->fasilitas) as $f)
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2"><i class="bi bi-box-seam me-1"></i>{{ $f }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Detail Pelanggan & Pembayaran --}}
        <div class="col-lg-6">
            <div class="d-flex flex-column gap-3">
                {{-- Detail Pelanggan --}}
                <div class="table-card p-4">
                    <h6 class="fw-bold mb-4"><i class="bi bi-person-badge me-2 text-primary"></i>Informasi Pelanggan</h6>
                    @if($booking->is_offline)
                        <div class="mb-3 d-flex justify-content-between py-2" style="border-bottom:1px solid #f1f5f9">
                            <span class="text-muted small">Nama Pemesan</span>
                            <span class="fw-600 text-dark">{{ $booking->nama_pemesan_offline }} <span class="badge bg-secondary ms-1" style="font-size:.65rem">Offline</span></span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between py-2" style="border-bottom:1px solid #f1f5f9">
                            <span class="text-muted small">Nomor HP</span>
                            <span class="fw-600 text-dark">{{ $booking->no_hp_offline ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted small">Kategori</span>
                            <span class="fw-600 text-secondary">Booking Offline (Non-akun)</span>
                        </div>
                    @else
                        <div class="mb-3 d-flex justify-content-between py-2" style="border-bottom:1px solid #f1f5f9">
                            <span class="text-muted small">Nama Pelanggan</span>
                            <span class="fw-600 text-dark">{{ $booking->user?->name ?? '-' }} <span class="badge bg-info text-white ms-1" style="font-size:.65rem">Online</span></span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between py-2" style="border-bottom:1px solid #f1f5f9">
                            <span class="text-muted small">Email</span>
                            <span class="fw-600 text-dark">{{ $booking->user?->email ?? '-' }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between py-2" style="border-bottom:1px solid #f1f5f9">
                            <span class="text-muted small">Nomor HP</span>
                            <span class="fw-600 text-dark">{{ $booking->user?->nomor_hp ?? '-' }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between py-2" style="border-bottom:1px solid #f1f5f9">
                            <span class="text-muted small">Status Member</span>
                            <span class="badge bg-{{ $booking->user?->isMember() ? 'warning text-dark' : 'light text-muted border' }} px-2.5 py-1 text-uppercase" style="font-size: 0.68rem; font-weight: 700;">
                                {{ $booking->user?->isMember() ? 'Member (' . str_replace('_', ' ', $booking->user->kategori_member) . ')' : 'Non-Member' }}
                            </span>
                        </div>
                        <div class="d-flex justify-content-between py-2">
                            <span class="text-muted small">Segmen Pelanggan</span>
                            @php
                                $segmenBadge = [
                                    'visitor'  => 'bg-secondary',
                                    'ally'     => 'bg-info text-dark',
                                    'partner'  => 'bg-success',
                                    'loyalist' => 'bg-warning text-dark',
                                    'vip'      => 'bg-danger',
                                ][$booking->user?->segmen_pelanggan] ?? 'bg-secondary';
                            @endphp
                            <span class="badge {{ $segmenBadge }} px-2.5 py-1 text-white">
                                {{ $booking->user?->label_segmen ?? 'Visitor' }}
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Status Pembayaran --}}
                <div class="table-card p-4">
                    <h6 class="fw-bold mb-4"><i class="bi bi-credit-card-fill me-2 text-success"></i>Status Pembayaran</h6>
                    @if($booking->pembayaran)
                        <div class="text-center mb-4">
                            @if($booking->pembayaran->status_verifikasi === 'diverifikasi')
                                <div class="mb-2" style="font-size:3rem">✅</div>
                                <h6 class="text-success fw-bold">Pembayaran Diverifikasi</h6>
                                <p class="text-muted small">Pembayaran booking ini telah dikonfirmasi dan lunas.</p>
                            @elseif($booking->pembayaran->status_verifikasi === 'ditolak')
                                <div class="mb-2" style="font-size:3rem">❌</div>
                                <h6 class="text-danger fw-bold">Pembayaran Ditolak</h6>
                                @if($booking->pembayaran->catatan_admin)
                                    <p class="text-danger small bg-danger-subtle p-2 rounded-3 border border-danger-subtle">{{ $booking->pembayaran->catatan_admin }}</p>
                                @endif
                            @elseif($booking->pembayaran->status_verifikasi === 'kedaluwarsa')
                                <div class="mb-2" style="font-size:3rem">⌛</div>
                                <h6 class="text-secondary fw-bold">Pembayaran Kedaluwarsa</h6>
                                <p class="text-muted small">Waktu pembayaran telah melewati batas maksimal.</p>
                            @else
                                <div class="mb-2" style="font-size:3rem">⏳</div>
                                <h6 class="text-warning fw-bold">Menunggu Verifikasi</h6>
                                <p class="text-muted small">Silakan verifikasi bukti pembayaran di bawah.</p>
                            @endif
                        </div>

                        <div class="p-3 rounded-3 mb-3" style="background:#f8fafc; border:1px solid #e2e8f0;">
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Metode Pembayaran</small>
                                <span class="fw-bold text-dark">{{ strtoupper($booking->pembayaran->metode_pembayaran) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Jumlah Bayar</small>
                                <span class="fw-bold text-success">Rp {{ number_format($booking->pembayaran->jumlah_bayar, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        @if($booking->pembayaran->bukti_pembayaran)
                            <div class="mb-3">
                                <label class="form-label small fw-bold d-block text-muted mb-2">Bukti Pembayaran:</label>
                                <div class="text-center bg-light p-2 rounded-3 border">
                                    <a href="{{ asset('storage/' . $booking->pembayaran->bukti_pembayaran) }}" target="_blank">
                                        <img src="{{ asset('storage/' . $booking->pembayaran->bukti_pembayaran) }}" 
                                             alt="Bukti Pembayaran" class="img-fluid rounded border shadow-sm" style="max-height: 250px; object-fit: contain;">
                                    </a>
                                </div>
                                <a href="{{ asset('storage/' . $booking->pembayaran->bukti_pembayaran) }}"
                                   target="_blank" class="btn btn-sm btn-outline-primary w-100 mt-2 rounded-pill">
                                    <i class="bi bi-image me-1"></i>Lihat Bukti Ukuran Penuh
                                </a>
                            </div>
                        @else
                            <div class="alert alert-light text-center border rounded-3 py-3">
                                <i class="bi bi-exclamation-circle text-muted fs-4 d-block mb-1"></i>
                                <small class="text-muted">Bukti pembayaran belum diunggah</small>
                            </div>
                        @endif

                        {{-- Verifikasi Langsung dari Detail --}}
                        @if($booking->pembayaran->status_verifikasi === 'menunggu')
                            @php
                                $jadwalLewat = $booking->jadwal
                                    && \Carbon\Carbon::parse($booking->jadwal->tanggal)->isPast()
                                    && !\Carbon\Carbon::parse($booking->jadwal->tanggal)->isToday();
                            @endphp
                            @if(!$jadwalLewat)
                                <div class="border-top pt-3 mt-3">
                                    <form action="{{ route('admin.pembayaran.verifikasi', $booking->pembayaran->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="mb-3">
                                            <label class="form-label small fw-bold">Catatan Admin (opsional)</label>
                                            <input type="text" name="catatan_admin" class="form-control form-control-sm" placeholder="Masukkan catatan tambahan atau alasan ditolak...">
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="submit" name="status_verifikasi" value="diverifikasi" class="btn btn-success btn-sm flex-grow-1 rounded-pill fw-bold py-2">
                                                <i class="bi bi-check-lg me-1"></i> Terima & Verifikasi
                                            </button>
                                            <button type="submit" name="status_verifikasi" value="ditolak" class="btn btn-danger btn-sm flex-grow-1 rounded-pill fw-bold py-2">
                                                <i class="bi bi-x-lg me-1"></i> Tolak Pembayaran
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @else
                                <div class="alert alert-secondary text-center rounded-3 py-2 mt-3 mb-0" style="font-size: 0.8rem;">
                                    <i class="bi bi-clock-history me-1"></i> Jadwal booking sudah lewat, pembayaran tidak dapat diverifikasi.
                                </div>
                            @endif
                        @endif
                    @else
                        <div class="alert alert-light text-center border rounded-3 py-4">
                            <i class="bi bi-credit-card-2-back text-muted fs-3 d-block mb-2"></i>
                            <span class="text-muted d-block small">Belum ada transaksi pembayaran untuk booking ini</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
