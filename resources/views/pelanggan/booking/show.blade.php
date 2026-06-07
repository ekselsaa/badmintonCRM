@extends('layouts.app')
@section('title', 'Detail Booking #' . $booking->id)
@section('page_title', 'Detail Booking #' . $booking->id)
@section('page_subtitle', 'Status: ' . ucfirst($booking->status))
@section('topbar_actions')
    <a href="{{ route('booking.riwayat') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Riwayat
    </a>
@endsection

@section('content')
<div class="p-0">
            @if(session('success'))
                <div class="alert alert-success rounded-3 py-2 mb-3"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
            @endif

            <div class="row g-3">
                {{-- Info Booking --}}
                <div class="col-lg-6">
                    <div class="table-card p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h6 class="fw-bold mb-0"><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Informasi Booking</h6>
                            @if($booking->status === 'pending' && (!$booking->pembayaran || !$booking->pembayaran->bukti_pembayaran))
                                <a href="{{ route('booking.edit', $booking->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </a>
                            @endif
                        </div>

                        <div class="mb-3 d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid #f1f5f9">
                            <span class="text-muted small">Status Booking</span>
                            <span class="badge badge-{{ $booking->status }} px-3 py-2 rounded-pill">{{ ucfirst($booking->status) }}</span>
                        </div>
                        <div class="mb-3 d-flex justify-content-between py-2" style="border-bottom:1px solid #f1f5f9">
                            <span class="text-muted small">Lapangan</span>
                            <span class="fw-600">{{ $booking->lapangan->nama_lapangan }}</span>
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

                        {{-- Poin Diterima / Estimasi Poin --}}
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
                        <div class="py-2" style="border-bottom:1px solid #f1f5f9">
                            <span class="text-muted small d-block mb-1">Catatan</span>
                            <span>{{ $booking->catatan }}</span>
                        </div>
                        @endif
                        @if($booking->fasilitas)
                        <div class="py-2">
                            <span class="text-muted small d-block mb-1 text-primary fw-bold">Fasilitas Tambahan</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">{{ $booking->fasilitas }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Pembayaran --}}
                <div class="col-lg-6">
                    {{-- Tampilkan Status hanya jika SUDAH upload bukti ATAU SUDAH diverifikasi/ditolak ATAU jika ini sesi rutin member --}}
                    @if($booking->catatan && str_contains($booking->catatan, 'Sesi Rutin Member'))
                    <div class="table-card p-4">
                        <h6 class="fw-bold mb-4"><i class="bi bi-credit-card me-2 text-success"></i>Status Pembayaran</h6>
                        <div class="text-center mb-3">
                            <div class="mb-2" style="font-size:3rem">✅</div>
                            <h6 class="text-success fw-bold">Sesi Member Aktif</h6>
                            <p class="text-muted small">Sesi ini bagian dari paket membership Anda.</p>
                        </div>
                        <div class="p-3 rounded-3" style="background:#f8fafc">
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Metode</small>
                                <span class="fw-500">Membership</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Jumlah Bayar</small>
                                <span class="fw-bold text-success">Rp 0 (Sesi Free)</span>
                            </div>
                        </div>
                    </div>
                    @elseif($booking->pembayaran && ($booking->pembayaran->bukti_pembayaran || $booking->pembayaran->status_verifikasi !== 'menunggu'))
                    {{-- Sudah Upload Bukti / Selesai Proses --}}
                    <div class="table-card p-4">
                        <h6 class="fw-bold mb-4"><i class="bi bi-credit-card me-2 text-success"></i>Status Pembayaran</h6>
                        <div class="text-center mb-3">
                            @if($booking->pembayaran->status_verifikasi === 'diverifikasi')
                                <div class="mb-2" style="font-size:3rem">✅</div>
                                <h6 class="text-success fw-bold">Pembayaran Diverifikasi</h6>
                                <p class="text-muted small">Booking Anda telah dikonfirmasi admin</p>
                            @elseif($booking->pembayaran->status_verifikasi === 'ditolak')
                                <div class="mb-2" style="font-size:3rem">❌</div>
                                <h6 class="text-danger fw-bold">Pembayaran Ditolak</h6>
                                @if($booking->pembayaran->catatan_admin)
                                <p class="text-muted small">{{ $booking->pembayaran->catatan_admin }}</p>
                                @endif
                            @else
                                <div class="mb-2" style="font-size:3rem">⏳</div>
                                <h6 class="text-warning fw-bold">Menunggu Verifikasi</h6>
                                <p class="text-muted small">Admin sedang memproses pembayaran Anda</p>
                            @endif
                        </div>
                        <div class="p-3 rounded-3" style="background:#f8fafc">
                            <div class="d-flex justify-content-between mb-2">
                                <small class="text-muted">Metode</small>
                                <span class="fw-500">{{ ucfirst($booking->pembayaran->metode_pembayaran) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">Jumlah Bayar</small>
                                <span class="fw-bold text-success">Rp {{ number_format($booking->pembayaran->jumlah_bayar, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        @if($booking->pembayaran->bukti_pembayaran)
                        <a href="{{ asset('storage/' . $booking->pembayaran->bukti_pembayaran) }}"
                           target="_blank" class="btn btn-outline-primary w-100 mt-3 rounded-pill">
                            <i class="bi bi-image me-1"></i>Lihat Bukti Pembayaran
                        </a>
                        @endif
                    </div>
                    @else
                    {{-- Form Pembayaran --}}
                    <div class="table-card p-4">
                        <h6 class="fw-bold mb-4"><i class="bi bi-credit-card me-2 text-warning"></i>Selesaikan Pembayaran</h6>

                        @php
                            $isTunai = $booking->pembayaran && $booking->pembayaran->metode_pembayaran === 'tunai';
                            if ($isTunai && $booking->jadwal) {
                                $jadwalDateTime = \Carbon\Carbon::parse($booking->jadwal->tanggal->format('Y-m-d') . ' ' . $booking->jadwal->jam_mulai);
                                $deadline = $jadwalDateTime->subMinutes(45);
                            } else {
                                $deadline = $booking->created_at->addMinutes(15);
                            }
                            $isExpired = now()->greaterThanOrEqualTo($deadline);
                        @endphp

                        @if(!$isExpired)
                        {{-- Countdown untuk kedua metode --}}
                        <div id="countdownBanner" class="rounded-3 p-3 mb-4 text-center" style="background:#fff7ed;border:1.5px solid #fed7aa;">
                            <div class="small fw-bold mb-1" style="color:#c2410c">
                                <i class="bi bi-clock-history me-1"></i> Batas Waktu Pembayaran
                            </div>
                            <div class="fw-bold" style="font-size:1.6rem;color:#ea580c;letter-spacing:2px" id="timerDisplay">--:--:--</div>
                            <div class="small mt-1" style="color:#9a3412">Selesaikan pembayaran sebelum <strong>{{ $deadline->format('d M Y, H:i') }}</strong></div>
                            @if($isTunai)
                            <div class="small mt-2 p-2 rounded" style="background:rgba(234,88,12,0.1);color:#c2410c;border:1px solid rgba(234,88,12,0.2)">
                                <i class="bi bi-info-circle me-1"></i> Pembayaran tunai <b>wajib</b> diselesaikan maksimal 45 menit sebelum jadwal Anda dimulai. Tunjukkan ID Booking <strong>#{{ $booking->id }}</strong> ke kasir.
                            </div>
                            @endif
                        </div>
                        @else
                        {{-- Waktu habis --}}
                        <div class="p-4 mb-4 text-center position-relative overflow-hidden" style="background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 100%); border: 1px solid #fecdd3; border-radius: 20px; box-shadow: 0 10px 25px -5px rgba(225, 29, 72, 0.1);">
                            <!-- Hiasan Background -->
                            <div class="position-absolute" style="top: -20px; left: -20px; opacity: 0.05; transform: rotate(-15deg);">
                                <i class="bi bi-clock-history" style="font-size: 8rem;"></i>
                            </div>

                            <div class="position-relative z-1">
                                <div class="mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px; background: #e11d48; border-radius: 50%; box-shadow: 0 0 0 8px rgba(225, 29, 72, 0.15);">
                                    <i class="bi bi-hourglass-bottom text-white fs-2"></i>
                                </div>
                                <h4 class="fw-bold mb-2" style="color: #be123c;">Waktu Pembayaran Habis</h4>
                                <p class="text-muted mb-4 px-lg-3" style="font-size: 0.95rem; line-height: 1.5;">
                                    Maaf, lapangan dan jadwal yang Anda pilih telah <strong>dibebaskan kembali</strong> untuk pelanggan lain karena batas waktu pembayaran Anda telah terlewati.
                                </p>
                                
                                <a href="{{ route('jadwal.index') }}" class="btn px-4 py-2 rounded-pill fw-bold" style="background: #e11d48; color: white; border: none; box-shadow: 0 4px 14px 0 rgba(225, 29, 72, 0.39); transition: all 0.2s; letter-spacing: 0.5px;">
                                    <i class="bi bi-calendar-plus me-2"></i> Lihat Jadwal Lapangan
                                </a>
                            </div>
                        </div>
                        @endif


                        {{-- Info Nominal --}}
                        <div class="alert rounded-3 py-3 mb-4 text-center" style="background:#f0fdf4;color:#166534;border:1px solid #bbf7d0;">
                            <div class="small text-muted mb-1">Total yang harus dibayar</div>
                            <div style="font-size:1.8rem;font-weight:800">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</div>
                        </div>

                        <div class="mb-4">
                            <div class="mt-3">
                                <form action="{{ route('booking.upload.pembayaran', $booking->id) }}" method="POST" enctype="multipart/form-data" id="formPembayaran">
                                    @csrf
                                    {{-- Tampilkan pilihan hanya jika belum ada metode yang tersimpan atau ingin diubah --}}
                                    @if(!$booking->pembayaran || !$booking->pembayaran->metode_pembayaran)
                                    <div class="mb-3">
                                        <label class="form-label small fw-600">Metode Pembayaran <span class="text-danger">*</span></label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <input type="radio" class="btn-check" name="metode_pembayaran" id="metodeQris" value="qris" required>
                                                <label class="btn btn-outline-primary w-100 rounded-3 py-3" for="metodeQris">
                                                    <i class="bi bi-qr-code-scan d-block fs-4 mb-1"></i>
                                                    <span class="fw-bold">QRIS</span>
                                                </label>
                                            </div>
                                            <div class="col-6">
                                                <input type="radio" class="btn-check" name="metode_pembayaran" id="metodeTunai" value="tunai" required>
                                                <label class="btn btn-outline-success w-100 rounded-3 py-3" for="metodeTunai">
                                                    <i class="bi bi-cash-stack d-block fs-4 mb-1"></i>
                                                    <span class="fw-bold">Tunai</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="mb-4 p-3 rounded-3 d-flex justify-content-between align-items-center" style="background:#f8fafc; border:1px solid #e2e8f0;">
                                        <div>
                                            <small class="text-muted d-block" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.5px;">Metode Terpilih:</small>
                                            <span class="fw-bold fs-5">
                                                @if($booking->pembayaran->metode_pembayaran == 'qris')
                                                    <i class="bi bi-qr-code-scan me-1 text-primary"></i> QRIS
                                                @else
                                                    <i class="bi bi-cash-stack me-1 text-success"></i> Tunai (Cash)
                                                @endif
                                            </span>
                                        </div>
                                        <input type="hidden" name="metode_pembayaran" value="{{ $booking->pembayaran ? $booking->pembayaran->metode_pembayaran : '' }}">
                                    </div>
                                    @endif
                            {{-- Info QRIS Manual --}}
                            <div id="infoQris" class="{{ ($booking->pembayaran && $booking->pembayaran->metode_pembayaran == 'qris') || (!$booking->pembayaran) ? '' : 'd-none' }}">
                                <div class="alert rounded-4 p-4 mb-3 text-center" style="background:#eff6ff;color:#1e40af;border:2px solid #bfdbfe;">
                                    <div class="fw-bold mb-3"><i class="bi bi-qr-code-scan me-2"></i>Pembayaran QRIS Manual</div>
                                    
                                    {{-- Area Barcode --}}
                                    <div class="bg-white p-3 rounded-4 shadow-sm d-inline-block mb-3 border">
                                        <img src="{{ asset('images/pembayaran/qris.jpg') }}" 
                                             alt="QRIS Anbiyaa Sport" class="img-fluid" style="max-width:280px; border-radius: 10px;">
                                    </div>

                                    <p class="small mb-0 fw-bold">Anbiyaa Sport Badminton</p>
                                    <p class="small mb-3">Silakan scan barcode di atas melalui aplikasi m-Banking atau E-Wallet (GoPay, OVO, Dana, dll).</p>
                                    
                                    <div class="p-3 bg-white rounded-3 border text-start mb-3">
                                        <div class="small fw-bold text-muted mb-1">Cara Bayar:</div>
                                        <ol class="small mb-0 ps-3">
                                            <li>Scan QRIS di atas dengan aplikasi pembayaran Anda.</li>
                                            <li>Masukkan nominal <strong>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</strong>.</li>
                                            <li>Simpan screenshot bukti pembayaran.</li>
                                            <li>Upload bukti tersebut pada formulir di bawah ini.</li>
                                        </ol>
                                    </div>

                                    {{-- Form upload manual --}}
                                    <div class="text-start">
                                        <label class="form-label small fw-bold">Upload Bukti Pembayaran <span class="text-danger">*</span></label>
                                        <input type="file" name="bukti_pembayaran" id="inputBukti" class="form-control mb-2" required>
                                        <button type="submit" class="btn btn-primary w-100 rounded-pill fw-bold py-2 shadow-sm">
                                            <i class="bi bi-cloud-upload me-1"></i>Konfirmasi Pembayaran
                                        </button>
                                    </div>
                                </div>
                            </div>

                            {{-- Info Tunai --}}
                            <div id="infoTunai" class="{{ $booking->pembayaran && $booking->pembayaran->metode_pembayaran == 'tunai' ? '' : 'd-none' }}">
                                <div class="alert rounded-4 p-4 mb-4 text-center" style="background:#f0fdf4;color:#166534;border:2px solid #bbf7d0;">
                                    <div class="fw-bold mb-2"><i class="bi bi-cash-stack me-2"></i>Pembayaran Tunai</div>
                                    <p class="small mb-3">Silakan lakukan pembayaran tunai langsung di lokasi (kasir) Anbiyaa Sport.</p>
                                    
                                    <div class="p-3 bg-white rounded-3 mb-3 text-start border shadow-sm">
                                        <div class="small fw-bold text-muted mb-1">Langkah Konfirmasi:</div>
                                        <ol class="small mb-0 ps-3">
                                            <li>Tunjukkan ID Booking <strong>#{{ $booking->id }}</strong> ke kasir.</li>
                                            <li>Bayar sesuai nominal <strong>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</strong>.</li>
                                            <li>Admin akan memverifikasi dan status lapangan akan berubah menjadi "Dipesan".</li>
                                        </ol>
                                    </div>

                                    @php
                                        $admins = config('badminton.admin_whatsapp', []);
                                        $message = "Halo Admin Anbiyaa Sport, saya ingin konfirmasi pembayaran TUNAI untuk Booking #{$booking->id} atas nama " . auth()->user()->name . ". Mohon bantuannya untuk verifikasi.";
                                    @endphp

                                    <div class="row g-2">
                                        @foreach($admins as $admin)
                                            <div class="col-12">
                                                <a href="https://wa.me/{{ $admin['number'] }}?text={{ urlencode($message) }}" 
                                                   target="_blank" class="btn btn-success w-100 rounded-pill fw-bold py-2 shadow-sm d-flex align-items-center justify-content-center">
                                                    <i class="bi bi-whatsapp me-2 fs-5"></i>
                                                    <div class="text-start">
                                                        <span class="d-block small" style="font-size: 0.7rem; opacity: 0.8; font-weight: normal;">Konfirmasi ke:</span>
                                                        <span>{{ $admin['name'] }}</span>
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                    <small class="d-block mt-3 text-muted" style="font-size: 0.75rem;">Silakan klik salah satu tombol admin di atas untuk mengirim bukti/konfirmasi.</small>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
// Fungsi untuk menampilkan info sesuai pilihan
function refreshPaymentInfo() {
    let method = '';
    const selectedRadio = document.querySelector('input[name="metode_pembayaran"]:checked');
    const hiddenInput = document.querySelector('input[name="metode_pembayaran"][type="hidden"]');
    
    if (selectedRadio) {
        method = selectedRadio.value;
    } else if (hiddenInput) {
        method = hiddenInput.value;
    }

    if (method) {
        document.getElementById('infoQris').classList.add('d-none');
        document.getElementById('infoTunai').classList.add('d-none');
        if (method === 'qris') {
            document.getElementById('infoQris').classList.remove('d-none');
        } else if (method === 'tunai') {
            document.getElementById('infoTunai').classList.remove('d-none');
        }
    }
}

// Picu saat ada perubahan
document.querySelectorAll('input[name="metode_pembayaran"]').forEach(function(radio) {
    radio.addEventListener('change', refreshPaymentInfo);
});

// Jalankan saat halaman pertama kali dimuat
window.addEventListener('DOMContentLoaded', refreshPaymentInfo);

// ── Countdown Timer Pembayaran ──
@php
    $isTunaiJs = $booking->pembayaran && $booking->pembayaran->metode_pembayaran === 'tunai';
    if ($isTunaiJs && $booking->jadwal) {
        $jadwalDateTimeJs = \Carbon\Carbon::parse($booking->jadwal->tanggal->format('Y-m-d') . ' ' . $booking->jadwal->jam_mulai);
        $deadlineTs = $jadwalDateTimeJs->subMinutes(45)->timestamp;
    } else {
        $deadlineTs = $booking->created_at->addMinutes(15)->timestamp;
    }
    $isExpiredPhp = now()->timestamp >= $deadlineTs;
@endphp
@if(!$isExpiredPhp && $booking->status === 'pending')
(function() {
    var deadlineTs = {{ $deadlineTs }} * 1000; // ms
    var display    = document.getElementById('timerDisplay');
    if (!display) return;

    function pad(n) { return String(n).padStart(2, '0'); }

    var timerInterval = setInterval(tick, 1000);

    function tick() {
        var now  = Date.now();
        var diff = deadlineTs - now;

        if (diff <= 0) {
            display.textContent = '00:00:00';
            document.getElementById('countdownBanner').style.background = '#fef2f2';
            document.getElementById('countdownBanner').style.borderColor = '#fecaca';
            
            clearInterval(timerInterval);

            Swal.fire({
                html: `
                    <div class="text-center pt-3 pb-2">
                        <div class="mx-auto mb-4 d-flex align-items-center justify-content-center position-relative" style="width: 80px; height: 80px; background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%); border-radius: 50%; box-shadow: 0 0 0 10px rgba(239, 68, 68, 0.1);">
                            <i class="bi bi-hourglass-bottom fs-1 text-danger"></i>
                        </div>
                        <h3 class="fw-bold mb-3" style="color: #1e293b; letter-spacing: -0.5px;">Waktu Habis!</h3>
                        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px;" class="p-3 mb-3 text-start">
                            <div class="d-flex align-items-start gap-2">
                                <i class="bi bi-info-circle-fill text-warning mt-1"></i>
                                <span style="color: #475569; font-size: 0.9rem; line-height: 1.5;">Kesempatan pembayaran Anda telah kedaluwarsa. <strong>Lapangan kini tersedia kembali untuk publik.</strong></span>
                            </div>
                        </div>
                        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 0;">
                            Jangan khawatir, Anda masih bisa mencari jadwal lapangan lain yang masih kosong.
                        </p>
                    </div>
                `,
                showCancelButton: false,
                confirmButtonText: '<i class="bi bi-calendar-check me-2"></i>Cari Jadwal Lapangan Baru',
                buttonsStyling: false,
                customClass: {
                    popup: 'swal-premium border-0',
                    confirmButton: 'btn btn-danger px-4 py-3 rounded-pill fw-bold w-100 shadow-sm mt-3'
                },
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('jadwal.index') }}";
                }
            });
            return;
        }

        var d = Math.floor(diff / 86400000);
        var h = Math.floor((diff % 86400000) / 3600000);
        var m = Math.floor((diff % 3600000) / 60000);
        var s = Math.floor((diff % 60000) / 1000);
        
        var timerString = '';
        if (d > 0) timerString += d + ' Hari ';
        timerString += pad(h) + ':' + pad(m) + ':' + pad(s);
        
        display.textContent = timerString;

        // Ubah warna jadi merah ketika sisa < 1 jam
        if (diff < 3600000) {
            display.style.color = '#dc2626';
        }
    }

    tick();
})();
@endif
</script>
@endpush
