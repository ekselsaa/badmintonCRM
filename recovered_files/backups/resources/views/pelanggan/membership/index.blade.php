@extends('layouts.app')
@section('title', 'Informasi Membership')
@section('page_title', 'Informasi Membership')
@section('page_subtitle', 'Bergabung menjadi member Anbiyaa Sport')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body, select, input, textarea, button, h2, h5, h6, label {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    /* Styling Premium untuk Formulir Membership */
    .table-card {
        border-radius: 20px !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05) !important;
    }
    .membership-card-option {
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        position: relative;
        overflow: hidden;
    }
    .membership-card-option:hover {
        transform: translateY(-4px);
        border-color: #cbd5e1;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
    .membership-card-option.active {
        border-color: #2563eb;
        background: #f8fafc;
        box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.15);
    }
    .membership-card-option.active::after {
        content: "\F272"; /* bi-check-circle-fill */
        font-family: "bootstrap-icons";
        position: absolute;
        top: 12px;
        right: 12px;
        color: #2563eb;
        font-size: 1.25rem;
        font-weight: bold;
    }
    .payment-method-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
    }
    .payment-method-card:hover {
        border-color: #cbd5e1;
        background: #fafafa;
    }
    .payment-method-card.active {
        border-color: #059669;
        background: #f0fdf4;
        box-shadow: 0 4px 12px rgba(5, 150, 105, 0.1);
    }
    .payment-method-card.active::after {
        content: "\F272"; /* bi-check-circle-fill */
        font-family: "bootstrap-icons";
        position: absolute;
        top: 14px;
        right: 16px;
        color: #059669;
        font-size: 1.1rem;
    }
    .upload-zone {
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        background: #f8fafc;
        transition: all 0.25s ease;
        cursor: pointer;
    }
    .upload-zone:hover {
        border-color: #2563eb;
        background: #eff6ff;
    }
    #image-preview {
        max-height: 200px;
        object-fit: contain;
        border-radius: 8px;
    }
</style>

<div class="p-0">
    <div class="row g-4 g-lg-5">
        {{-- Banner Hero --}}
        <div class="col-12">
            <div class="table-card p-5 text-white border-0 shadow-lg" 
                 style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-radius: 24px; position: relative; overflow: hidden;">
                <div style="position: absolute; top: -20px; right: -20px; opacity: 0.1;">
                    <i class="bi bi-star-fill" style="font-size: 200px;"></i>
                </div>
                <div class="row align-items-center">
                    <div class="col-lg-8 position-relative">
                        <span class="badge bg-primary px-3 py-2 rounded-pill mb-3">ANBIYAA EXCLUSIVE</span>
                        @if(auth()->user()->isMember())
                            <h2 class="fw-800 mb-3" style="font-size: 2.5rem; color: #facc15;"><i class="bi bi-patch-check-fill me-2"></i>Anda adalah Member Premium!</h2>
                            <p class="text-muted-light mb-4" style="color: #cbd5e1; font-size: 1.1rem;">
                                Terima kasih telah bergabung sebagai member tetap Anbiyaa Sport. Nikmati potongan harga khusus member pada setiap booking lapangan dan prioritas penjadwalan.
                            </p>
                            <span class="btn btn-warning btn-lg rounded-pill px-5 fw-bold shadow-sm" style="pointer-events: none;">
                                <i class="bi bi-gemini me-2"></i>Status: Member Premium Aktif
                            </span>
                        @else
                            <h2 class="fw-800 mb-3" style="font-size: 2.5rem;">Upgrade ke Member & Nikmati Keuntungannya!</h2>
                            <p class="text-muted-light mb-4" style="color: #94a3b8; font-size: 1.1rem;">
                                Dapatkan harga lebih hemat, prioritas jadwal, dan layanan eksklusif lainnya dengan menjadi bagian dari member tetap kami.
                            </p>
                            <a href="#pembayaran-section" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                <i class="bi bi-credit-card-2-front me-2"></i>Daftar & Bayar Sekarang
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerts --}}
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm py-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success fs-4 me-3"></i>
                        <div>
                            <strong class="d-block text-success">Berhasil!</strong>
                            {{ session('success') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm py-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-danger fs-4 me-3"></i>
                        <div>
                            <strong class="d-block text-danger">Gagal!</strong>
                            {{ session('error') }}
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm py-3" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-x-circle-fill text-danger fs-4 me-3"></i>
                        <div>
                            <strong class="d-block text-danger">Terjadi Kesalahan Validasi:</strong>
                            <ul class="mb-0 mt-1 ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
        </div>

        {{-- Main Layout --}}
        <div class="col-lg-8">
            {{-- Ketentuan & Harga --}}
            <div class="table-card p-4 p-md-5 mb-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-list-check me-2 text-primary"></i>Ketentuan & Aturan Member</h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px">
                                <i class="bi bi-calendar-event text-primary"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Jadwal Rutin</h6>
                                <p class="small text-muted">Member mendapatkan slot waktu tetap sebanyak 4 kali dalam sebulan (1x seminggu).</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px">
                                <i class="bi bi-clock text-primary"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Sistem Sesi</h6>
                                <p class="small text-muted">Satu sesi member berdurasi 3 jam penuh sesuai pembagian shift operasional.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px">
                                <i class="bi bi-cash-stack text-primary"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Pembayaran di Awal</h6>
                                <p class="small text-muted">Iuran member dibayarkan di awal bulan untuk mengamankan slot jadwal selama satu bulan.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center flex-shrink-0" style="width:40px;height:40px">
                                <i class="bi bi-shield-check text-primary"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">Aktivasi Instan</h6>
                                <p class="small text-muted">Kirimkan bukti pembayaran Anda langsung di sistem ini untuk ditinjau oleh administrator.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="fw-bold mb-3">Daftar Harga Paket Member (4 Sesi/Bulan):</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Kategori Waktu</th>
                                <th>Sesi</th>
                                <th>Harga Paket</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Weekday (Pagi/Siang)</td>
                                <td>07:00 - 16:00</td>
                                <td class="fw-bold text-primary">Rp 350.000</td>
                            </tr>
                            <tr>
                                <td>Weekday (Malam)</td>
                                <td>18:00 - 24:00</td>
                                <td class="fw-bold text-primary">Rp 500.000</td>
                            </tr>
                            <tr>
                                <td>Weekend (Sabtu/Minggu)</td>
                                <td>Bebas Shift</td>
                                <td class="fw-bold text-primary">Rp 550.000</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Bagian Pembayaran --}}
            @if(!auth()->user()->isMember())
                <div id="pembayaran-section" class="table-card p-4 p-md-5 border-primary border-top border-4 mb-4">
                    <h5 class="fw-bold mb-4">
                        <i class="bi bi-wallet2 me-2 text-primary"></i>Formulir Aktivasi & Pembayaran
                    </h5>

                    @if($latestPayment && $latestPayment->status_verifikasi === 'menunggu')
                        {{-- Pembayaran Sedang Diverifikasi --}}
                        <div class="p-4 rounded-3 text-center" style="background: #fffbeb; border: 1px solid #fef3c7;">
                            <div class="spinner-grow text-warning mb-3" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h6 class="fw-bold text-warning mb-2">⏳ Pembayaran Anda Sedang Ditinjau Admin</h6>
                            <p class="text-muted small mb-3">
                                Anda telah mengajukan pembayaran membership pada <strong>{{ $latestPayment->created_at->translatedFormat('d M Y H:i') }}</strong>.
                                Admin sedang memverifikasi bukti transaksi Anda. Status keanggotaan akan diperbarui secara otomatis setelah disetujui.
                            </p>
                            <div class="bg-white p-3 rounded border text-start mx-auto" style="max-width: 400px; font-size: 0.85rem;">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Paket:</span>
                                    <span class="fw-bold">
                                        {{ $latestPayment->paket === 'weekday_pagi' ? 'Weekday (Pagi/Siang)' : ($latestPayment->paket === 'weekday_malam' ? 'Weekday (Malam)' : 'Weekend') }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Nominal:</span>
                                    <span class="fw-bold text-success">Rp {{ number_format($latestPayment->jumlah_bayar, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">Metode:</span>
                                    <span class="badge bg-light text-dark border">{{ $latestPayment->metode_pembayaran === 'qris' ? 'QRIS Instan' : 'Tunai ke Kasir' }}</span>
                                </div>
                                <div class="mt-2 text-center">
                                    <a href="{{ asset('storage/' . $latestPayment->bukti_pembayaran) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-1 px-3">
                                        <i class="bi bi-image me-1"></i>Lihat Bukti Pembayaran
                                    </a>
                                </div>
                            </div>
                        </div>

                    @else
                        {{-- Formulir Input Pembayaran --}}
                        @if($latestPayment && $latestPayment->status_verifikasi === 'ditolak')
                            <div class="alert alert-danger border-0 rounded-3 mb-4 shadow-sm" style="background-color:#fef2f2">
                                <div class="d-flex">
                                    <i class="bi bi-x-circle-fill text-danger fs-4 me-3"></i>
                                    <div>
                                        <strong class="d-block text-danger">Pengajuan Pembayaran Sebelumnya Ditolak</strong>
                                        <span class="small text-muted d-block mt-1">
                                            Alasan Penolakan: <span class="text-dark fw-bold">"{{ $latestPayment->catatan_admin ?? 'Tidak ada catatan khusus.' }}"</span>
                                        </span>
                                        <span class="small text-muted d-block mt-1">Silakan lakukan pembayaran ulang dan kirimkan kembali bukti pembayaran terbaru yang sah.</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <form action="{{ route('membership.bayar') }}" method="POST" enctype="multipart/form-data" id="form-pembayaran">
                            @csrf
                            
                            {{-- Pilihan Paket Teroptimasi --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block mb-3">1. Pilih Kategori Paket Member <span class="text-danger">*</span></label>
                                <input type="hidden" name="paket" id="input-paket" required>
                                                               <div class="row g-4">
                                    {{-- Paket Pagi --}}
                                    <div class="col-md-4">
                                        <div class="membership-card-option p-4 h-100" data-value="weekday_pagi" onclick="selectPackage(this)">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <div class="rounded bg-primary-subtle p-2 text-primary">
                                                    <i class="bi bi-sun fs-5"></i>
                                                </div>
                                                <span class="fw-bold small text-muted">Weekday</span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">Pagi/Siang</h6>
                                            <div class="text-primary fw-800 fs-5 mb-2">Rp 350.000</div>
                                            <ul class="list-unstyled small text-muted mb-0" style="font-size: 0.75rem;">
                                                <li><i class="bi bi-clock-history me-1"></i>07:00 - 16:00</li>
                                                <li><i class="bi bi-check2-circle text-success me-1"></i>4x Sesi Sebulan</li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    {{-- Paket Malam --}}
                                    <div class="col-md-4">
                                        <div class="membership-card-option p-4 h-100" data-value="weekday_malam" onclick="selectPackage(this)">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <div class="rounded bg-indigo-subtle p-2 text-indigo" style="color: #6366f1; background-color: #e0e7ff;">
                                                    <i class="bi bi-moon-stars fs-5"></i>
                                                </div>
                                                <span class="fw-bold small text-muted">Weekday</span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">Malam</h6>
                                            <div class="text-primary fw-800 fs-5 mb-2">Rp 500.000</div>
                                            <ul class="list-unstyled small text-muted mb-0" style="font-size: 0.75rem;">
                                                <li><i class="bi bi-clock-history me-1"></i>18:00 - 24:00</li>
                                                <li><i class="bi bi-check2-circle text-success me-1"></i>4x Sesi Sebulan</li>
                                            </ul>
                                        </div>
                                    </div>
 
                                    {{-- Paket Weekend --}}
                                    <div class="col-md-4">
                                        <div class="membership-card-option p-4 h-100" data-value="weekend" onclick="selectPackage(this)">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <div class="rounded bg-warning-subtle p-2 text-warning" style="color: #d97706; background-color: #fef3c7;">
                                                    <i class="bi bi-calendar-range fs-5"></i>
                                                </div>
                                                <span class="fw-bold small text-muted">Weekend</span>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-1">Sabtu/Minggu</h6>
                                            <div class="text-primary fw-800 fs-5 mb-2">Rp 550.000</div>
                                            <ul class="list-unstyled small text-muted mb-0" style="font-size: 0.75rem;">
                                                <li><i class="bi bi-clock-history me-1"></i>Bebas Shift</li>
                                                <li><i class="bi bi-check2-circle text-success me-1"></i>4x Sesi Sebulan</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tentukan Jadwal Rutin Mingguan --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block mb-3">2. Tentukan Jadwal Rutin Mingguan Anda <span class="text-danger">*</span></label>
                                <div class="row g-3">
                                    {{-- Lapangan --}}
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-secondary">Pilih Lapangan</label>
                                        <select name="lapangan_id" id="select-lapangan" class="form-select" required onchange="checkFormStatus()">
                                            <option value="">-- Pilih Lapangan --</option>
                                            @foreach($lapangans as $lap)
                                                <option value="{{ $lap->id }}" {{ old('lapangan_id') == $lap->id ? 'selected' : '' }}>{{ $lap->nama_lapangan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- Hari --}}
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-secondary">Pilih Hari</label>
                                        <select name="hari" id="select-hari" class="form-select" required onchange="onHariChange()">
                                            <option value="">-- Pilih Hari --</option>
                                            <option value="senin">Senin</option>
                                            <option value="selasa">Selasa</option>
                                            <option value="rabu">Rabu</option>
                                            <option value="kamis">Kamis</option>
                                            <option value="jumat">Jumat</option>
                                            <option value="sabtu">Sabtu</option>
                                            <option value="minggu">Minggu</option>
                                        </select>
                                    </div>
                                    {{-- Sesi --}}
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-secondary">Pilih Sesi (Shift 3 Jam)</label>
                                        <select name="sesi" id="select-sesi" class="form-select" required onchange="checkFormStatus()" disabled>
                                            <option value="">-- Pilih Sesi --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-text text-muted small mt-2">
                                    * Ketersediaan hari dan sesi menyesuaikan dengan kategori paket member yang dipilih.
                                </div>
                            </div>

                            {{-- Pilihan Metode Pembayaran Teroptimasi (Hanya QRIS & Tunai) --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block mb-3">3. Pilih Metode Pembayaran <span class="text-danger">*</span></label>
                                <input type="hidden" name="metode_pembayaran" id="input-metode" required>
                                
                                <div class="row g-4">
                                    {{-- QRIS --}}
                                    <div class="col-md-6">
                                        <div class="payment-method-card" data-value="qris" onclick="selectPaymentMethod(this)">
                                            <div class="rounded bg-success-subtle p-2 text-success">
                                                <i class="bi bi-qr-code fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0">QRIS Instan</h6>
                                                <small class="text-muted" style="font-size: 0.72rem;">Scan QR code otomatis</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    {{-- Tunai --}}
                                    <div class="col-md-6">
                                        <div class="payment-method-card" data-value="tunai" onclick="selectPaymentMethod(this)">
                                            <div class="rounded bg-warning-subtle p-2 text-warning" style="color: #d97706; background-color: #fef3c7;">
                                                <i class="bi bi-cash-coin fs-4"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold text-dark mb-0">Tunai ke Kasir</h6>
                                                <small class="text-muted" style="font-size: 0.72rem;">Bayar cash langsung di tempat</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Wadah Instruksi QRIS --}}
                            <div id="instruksi-qris" class="mb-4 d-none">
                                <div class="p-4 rounded-3 text-center border-0 shadow-sm" style="background: #fafafa; border-radius: 20px !important;">
                                    <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-qr-code-scan me-2 text-primary"></i>Pindai QRIS Resmi Anbiyaa Sport</h6>
                                    <p class="text-muted small mb-3">Scan kode QR di bawah melalui aplikasi e-wallet Anda (Gopay, OVO, Dana, ShopeePay) atau m-Banking.</p>
                                    <div class="my-3 text-center">
                                        <img src="{{ asset('images/pembayaran/qris.jpg') }}" alt="QRIS Anbiyaa Sport" class="img-fluid rounded border shadow-sm" style="max-width: 240px; border-radius: 12px !important; transition: all 0.3s ease;">
                                    </div>
                                    <p class="text-muted small mb-0 px-md-4">Simpan struk/bukti transaksi sukses dari e-wallet untuk diunggah di bawah sebagai bukti verifikasi.</p>
                                </div>
                            </div>

                            {{-- Wadah Instruksi Tunai --}}
                            <div id="instruksi-tunai" class="mb-4 d-none">
                                <div class="p-3 rounded-3 border-0 shadow-sm" style="background: #eff6ff; border-radius: 20px !important;">
                                    <h6 class="fw-bold text-primary mb-2"><i class="bi bi-info-circle-fill me-2"></i>Petunjuk Pembayaran Tunai</h6>
                                    <p class="small text-muted mb-2">
                                        Silakan serahkan uang iuran sesuai harga paket yang dipilih ke kasir/admin di lapangan **Anbiyaa Sport**.
                                    </p>
                                    <p class="small text-muted mb-0">
                                        Setelah kasir menerima pembayaran Anda, kasir akan memberikan nota transaksi fisik atau konfirmasi verbal. Harap **unggah foto nota** tersebut atau **foto tanda terima kasir** pada form di bawah agar administrator dapat memverifikasi dan mengaktifkan akun member Anda di sistem secara permanen.
                                    </p>
                                </div>
                            </div>

                            {{-- Unggah Bukti Pembayaran Teroptimasi --}}
                            <div class="mb-4 d-none" id="upload-container">
                                <label class="form-label fw-bold mb-2">4. Unggah Bukti Pembayaran <span class="text-danger">*</span></label>
                                
                                <div class="upload-zone p-5 text-center" onclick="triggerFileInput()">
                                    <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" class="d-none" accept="image/*" required onchange="previewImage(this)">
                                    
                                    <div id="upload-prompt">
                                        <i class="bi bi-cloud-arrow-up text-primary" style="font-size: 3rem;"></i>
                                        <h6 class="fw-bold text-dark mt-2 mb-1">Pilih File Bukti Transaksi</h6>
                                        <p class="text-muted small mb-0">Seret file ke sini atau klik untuk mencari gambar (JPG, JPEG, PNG, maks 2MB)</p>
                                    </div>
                                    
                                    <div id="preview-container" class="d-none mt-2">
                                        <img id="image-preview" src="#" alt="Pratinjau Bukti Pembayaran" class="img-fluid border mb-2">
                                        <div class="small text-primary fw-bold" id="file-name-label">Nama File</div>
                                        <button type="button" class="btn btn-sm btn-outline-danger mt-2 px-3 rounded-pill" onclick="resetFileSelection(event)">
                                            <i class="bi bi-trash me-1"></i>Ganti Gambar
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mt-4 text-end">
                                <button type="submit" class="btn btn-primary px-5 py-2.5 rounded-pill fw-bold shadow" id="btn-submit" disabled>
                                    <i class="bi bi-check-circle-fill me-2"></i>Kirim Formulir Pembayaran
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            @endif
        </div>

        {{-- Sidebar Info --}}
        <div class="col-lg-4">
            <div class="table-card p-4 p-md-5 mb-4" style="background:#f8fafc">
                <h6 class="fw-bold mb-3"><i class="bi bi-question-circle me-2 text-primary"></i>Alur Aktivasi</h6>
                <div class="timeline-simple">
                    <div class="mb-4 ps-3 border-start border-primary" style="position:relative">
                        <div style="position:absolute;left:-5px;top:0;width:9px;height:9px;background:#1a56db;border-radius:50%"></div>
                        <div class="small fw-bold">1. Pilih & Bayar Paket</div>
                        <div class="small text-muted">Pilih paket membership impian Anda lalu lakukan pembayaran instan via QRIS atau tunai ke kasir.</div>
                    </div>
                    <div class="mb-4 ps-3 border-start border-primary" style="position:relative">
                        <div style="position:absolute;left:-5px;top:0;width:9px;height:9px;background:#1a56db;border-radius:50%"></div>
                        <div class="small fw-bold">2. Kirim Bukti Transaksi</div>
                        <div class="small text-muted">Isi form terintegrasi dan unggah tangkapan layar e-wallet atau foto nota fisik kasir.</div>
                    </div>
                    <div class="mb-0 ps-3 border-start border-primary" style="position:relative">
                        <div style="position:absolute;left:-5px;top:0;width:9px;height:9px;background:#1a56db;border-radius:50%"></div>
                        <div class="small fw-bold">3. Verifikasi Instan</div>
                        <div class="small text-muted">Admin memeriksa berkas pembayaran. Begitu cocok, status member Premium Anda langsung aktif!</div>
                    </div>
                </div>
            </div>

            {{-- Hubungi Admin Card --}}
            <div class="table-card p-4 p-md-5 shadow-sm border-0" style="background:linear-gradient(135deg,#0f172a,#1e3a5f);color:#fff">
                <h6 class="fw-bold mb-3"><i class="bi bi-whatsapp me-2 text-success"></i>Butuh Bantuan? Tanya Admin</h6>
                <p class="small text-muted mb-3" style="color: #94a3b8 !important;">Jika Anda mengalami kendala saat melakukan pembayaran atau ingin menanyakan jadwal rutin yang kosong, silakan hubungi kontak berikut:</p>
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-success" style="width:48px; height:48px; background:rgba(34,197,94,0.1);">
                        <i class="bi bi-whatsapp fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold small">Admin 1 (Aktivasi)</div>
                        <a href="https://wa.me/6282187485422?text=Halo%20Admin%20Anbiyaa%20Sport,%20saya%20ada%20kendala%20mengenai%20pembayaran%20member." target="_blank" class="text-success fw-bold text-decoration-none">
                            +62 821-8748-5422
                        </a>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 pt-3 border-top" style="border-color: rgba(255,255,255,0.1) !important;">
                    <div class="rounded-3 d-flex align-items-center justify-content-center text-success" style="width:48px; height:48px; background:rgba(34,197,94,0.1);">
                        <i class="bi bi-whatsapp fs-4"></i>
                    </div>
                    <div>
                        <div class="fw-bold small">Admin 2</div>
                        <a href="https://wa.me/6289529508023?text=Halo%20Admin%20Anbiyaa%20Sport,%20saya%20ingin%20bertanya%20jadwal%20member." target="_blank" class="text-success fw-bold text-decoration-none">
                            +62 895-2950-8023
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Memilih Paket Membership
    function selectPackage(element) {
        // Hapus class active dari pilihan paket lain
        document.querySelectorAll('.membership-card-option').forEach(card => {
            card.classList.remove('active');
        });
        
        // Tambahkan class active ke paket yang dipilih
        element.classList.add('active');
        
        // Simpan nilai pilihan di hidden input
        const val = element.getAttribute('data-value');
        document.getElementById('input-paket').value = val;

        // Reset hari dan sesi pilihan
        document.getElementById('select-hari').value = '';
        const sesiSelect = document.getElementById('select-sesi');
        sesiSelect.innerHTML = '<option value="">-- Pilih Sesi --</option>';
        sesiSelect.disabled = true;

        // Filter opsi hari agar hanya yang sesuai ketentuan yang bisa dipilih
        updateHariOptions(val);

        checkFormStatus();
    }

    // Mengupdate pilihan hari berdasarkan paket yang dipilih
    function updateHariOptions(paket) {
        const hariSelect = document.getElementById('select-hari');
        const options = hariSelect.options;

        for (let i = 1; i < options.length; i++) {
            const opt = options[i];
            const val = opt.value;
            const isWeekend = ['sabtu', 'minggu'].includes(val);

            if (!paket) {
                opt.style.display = 'block';
                opt.disabled = false;
            } else if (paket === 'weekend') {
                if (isWeekend) {
                    opt.style.display = 'block';
                    opt.disabled = false;
                } else {
                    opt.style.display = 'none';
                    opt.disabled = true;
                }
            } else { // weekday_pagi or weekday_malam
                if (!isWeekend) {
                    opt.style.display = 'block';
                    opt.disabled = false;
                } else {
                    opt.style.display = 'none';
                    opt.disabled = true;
                }
            }
        }
    }

    // Memilih Metode Pembayaran
    function selectPaymentMethod(element) {
        // Hapus class active dari pilihan metode lain
        document.querySelectorAll('.payment-method-card').forEach(card => {
            card.classList.remove('active');
        });
        
        // Tambahkan class active ke metode yang dipilih
        element.classList.add('active');
        
        // Simpan nilai pilihan di hidden input
        const val = element.getAttribute('data-value');
        document.getElementById('input-metode').value = val;

        // Tampilkan/sembunyikan wadah instruksi dan file upload
        const qrisSec = document.getElementById('instruksi-qris');
        const tunaiSec = document.getElementById('instruksi-tunai');
        const uploadCont = document.getElementById('upload-container');

        if (val === 'qris') {
            qrisSec.classList.remove('d-none');
            tunaiSec.classList.add('d-none');
        } else if (val === 'tunai') {
            tunaiSec.classList.remove('d-none');
            qrisSec.classList.add('d-none');
        }

        // Tampilkan input upload begitu metode pembayaran dipilih
        uploadCont.classList.remove('d-none');

        checkFormStatus();
    }

    // Mengubah pilihan hari & memuat opsi sesi yang relevan
    function onHariChange() {
        const paket = document.getElementById('input-paket').value;
        const hariSelect = document.getElementById('select-hari');
        const hari = hariSelect.value;
        const sesiSelect = document.getElementById('select-sesi');

        // Reset dan matikan dropdown sesi secara default
        sesiSelect.innerHTML = '<option value="">-- Pilih Sesi --</option>';
        sesiSelect.disabled = true;

        if (!paket) {
            alert('Silakan pilih kategori paket member terlebih dahulu.');
            hariSelect.value = '';
            checkFormStatus();
            return;
        }

        if (!hari) {
            checkFormStatus();
            return;
        }

        const isWeekend = ['sabtu', 'minggu'].includes(hari);

        // Validasi keselarasan hari dan paket
        if (paket === 'weekend' && !isWeekend) {
            alert('Paket weekend hanya boleh memilih hari Sabtu atau Minggu.');
            hariSelect.value = '';
            checkFormStatus();
            return;
        }
        if ((paket === 'weekday_pagi' || paket === 'weekday_malam') && isWeekend) {
            alert('Paket weekday hanya boleh memilih hari Senin sampai Jumat.');
            hariSelect.value = '';
            checkFormStatus();
            return;
        }

        let options = [];
        if (paket === 'weekday_pagi') {
            options = [
                { val: '07:00-10:00', text: '07:00 - 10:00 (Pagi)' },
                { val: '10:00-13:00', text: '10:00 - 13:00 (Siang)' },
                { val: '13:00-16:00', text: '13:00 - 16:00 (Sore)' }
            ];
        } else if (paket === 'weekday_malam') {
            options = [
                { val: '18:00-21:00', text: '18:00 - 21:00 (Malam)' },
                { val: '21:00-24:00', text: '21:00 - 24:00 (Malam)' }
            ];
        } else if (paket === 'weekend') {
            options = [
                { val: '07:00-10:00', text: '07:00 - 10:00 (Pagi)' },
                { val: '10:00-13:00', text: '10:00 - 13:00 (Siang)' },
                { val: '13:00-16:00', text: '13:00 - 16:00 (Sore)' },
                { val: '16:00-19:00', text: '16:00 - 19:00 (Sore/Malam)' },
                { val: '19:00-22:00', text: '19:00 - 22:00 (Malam)' },
                { val: '21:00-24:00', text: '21:00 - 24:00 (Malam)' }
            ];
        }

        options.forEach(opt => {
            const el = document.createElement('option');
            el.value = opt.val;
            el.textContent = opt.text;
            sesiSelect.appendChild(el);
        });

        sesiSelect.disabled = false;
        checkFormStatus();
    }

    // Memicu klik pada file input tersembunyi
    function triggerFileInput() {
        document.getElementById('bukti_pembayaran').click();
    }

    // Menampilkan pratinjau gambar bukti transfer
    function previewImage(input) {
        const file = input.files[0];
        if (file) {
            // Validasi ukuran berkas (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                alert("Ukuran berkas maksimal 2MB. Berkas yang Anda pilih berukuran " + (file.size / (1024 * 1024)).toFixed(2) + "MB.");
                resetFileSelection();
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('upload-prompt').classList.add('d-none');
                document.getElementById('preview-container').classList.remove('d-none');
                document.getElementById('file-name-label').innerText = file.name;
                checkFormStatus();
            }
            reader.readAsDataURL(file);
        }
    }

    // Mereset berkas bukti yang terpilih
    function resetFileSelection(event) {
        if (event) {
            event.stopPropagation(); // Cegah memicu klik upload-zone kembali
        }
        document.getElementById('bukti_pembayaran').value = "";
        document.getElementById('upload-prompt').classList.remove('d-none');
        document.getElementById('preview-container').classList.add('d-none');
        document.getElementById('image-preview').src = "#";
        checkFormStatus();
    }

    // Memeriksa kelengkapan form untuk mengaktifkan tombol submit
    function checkFormStatus() {
        const paketSelected = document.getElementById('input-paket').value;
        const metodeSelected = document.getElementById('input-metode').value;
        const fileUploaded = document.getElementById('bukti_pembayaran').files.length > 0;
        const lapSelected = document.getElementById('select-lapangan').value;
        const hariSelected = document.getElementById('select-hari').value;
        const sesiSelected = document.getElementById('select-sesi').value;
        const btnSubmit = document.getElementById('btn-submit');

        if (paketSelected && metodeSelected && fileUploaded && lapSelected && hariSelected && sesiSelected) {
            btnSubmit.removeAttribute('disabled');
        } else {
            btnSubmit.setAttribute('disabled', 'disabled');
        }
    }
</script>
@endsection
