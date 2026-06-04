@extends('layouts.app')
@section('title', 'Kelola Jadwal')
@section('page_title', 'Kelola Jadwal')
@section('page_subtitle', 'Atur slot waktu lapangan & catat pemesanan offline')

@push('styles')
<style>
    /* Custom tab navigation styling */
    .custom-pills {
        background: #f1f5f9;
        padding: 6px;
        border-radius: 12px;
        gap: 4px;
    }
    .custom-pills .nav-link {
        color: #64748b;
        background: transparent;
        border-radius: 8px !important;
        font-size: 0.85rem;
        font-weight: 600;
        padding: 8px 16px;
        transition: all 0.25s ease;
        border: 1px solid transparent;
    }
    .custom-pills .nav-link:hover {
        color: #3b82f6;
        background: rgba(255, 255, 255, 0.5);
    }
    .custom-pills .nav-link.active {
        background: #ffffff !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    #offline-tab.active {
        color: #2563eb !important;
        border-bottom: 2px solid #2563eb !important;
    }
    #blokir-tab.active {
        color: #d97706 !important;
        border-bottom: 2px solid #f59e0b !important;
    }
    #libur-tab.active {
        color: #dc2626 !important;
        border-bottom: 2px solid #ef4444 !important;
    }
    
    /* Form controls styling */
    .form-control, .form-select {
        border: 1px solid #cbd5e1;
        padding: 0.65rem 0.75rem;
        font-size: 0.875rem;
        border-radius: 10px;
        transition: all 0.2s ease-in-out;
        background-color: #f8fafc;
    }
    .form-control:focus, .form-select:focus {
        background-color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.12);
        color: #0f172a;
    }
    
    .input-group-text {
        background-color: #e2e8f0;
        border: 1px solid #cbd5e1;
        border-radius: 10px 0 0 10px;
        color: #475569;
        font-weight: 600;
        font-size: 0.875rem;
    }
    
    .input-group > .form-control {
        border-radius: 0 10px 10px 0;
    }
    
    /* Custom button transition and gradients */
    .btn-gradient-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        color: white;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        transition: all 0.3s ease;
    }
    .btn-gradient-primary:hover {
        background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
        color: white;
    }
    
    .btn-gradient-warning {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        border: none;
        color: white;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
        transition: all 0.3s ease;
    }
    .btn-gradient-warning:hover {
        background: linear-gradient(135deg, #fcd34d 0%, #fbbf24 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.35);
        color: white;
    }

    .btn-gradient-danger {
        background: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
        border: none;
        color: white;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.25);
        transition: all 0.3s ease;
    }
    .btn-gradient-danger:hover {
        background: linear-gradient(135deg, #fca5a5 0%, #f87171 100%);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.35);
        color: white;
    }
    
    .btn-action-delete {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #ef4444;
        background: #fef2f2;
        border: 1px solid #fee2e2;
        border-radius: 50%;
        transition: all 0.2s ease;
        padding: 0;
    }
    .btn-action-delete:hover {
        color: white;
        background: #ef4444;
        border-color: #ef4444;
        transform: scale(1.08);
        box-shadow: 0 4px 10px rgba(239, 68, 68, 0.15);
    }

    .table-card-header-custom {
        padding: 1.25rem 1.5rem;
        background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
        border-bottom: 1px solid #fee2e2;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
</style>
@endpush

@section('content')
<div class="p-0">
    @if(session('success'))
        <div class="alert alert-success rounded-3 py-2 mb-3"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3 py-2 mb-3"><i class="bi bi-x-circle-fill me-2"></i>{{ session('error') }}</div>
    @endif

    <div class="row g-4">
        {{-- ════ Kolom Kiri: Form-form dalam Tab ════ --}}
        <div class="col-lg-5">
            <div class="table-card p-4">
                {{-- Nav Tabs --}}
                <ul class="nav nav-pills custom-pills mb-4" id="jadwalFormTab" role="tablist">
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link w-100 active" id="offline-tab" data-bs-toggle="tab" data-bs-target="#offline-pane" type="button" role="tab" aria-controls="offline-pane" aria-selected="true">
                            <i class="bi bi-person-check-fill me-1"></i> Offline
                        </button>
                    </li>
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link w-100" id="blokir-tab" data-bs-toggle="tab" data-bs-target="#blokir-pane" type="button" role="tab" aria-controls="blokir-pane" aria-selected="false">
                            <i class="bi bi-lock-fill me-1"></i> Blokir Jam
                        </button>
                    </li>
                    <li class="nav-item flex-fill" role="presentation">
                        <button class="nav-link w-100" id="libur-tab" data-bs-toggle="tab" data-bs-target="#libur-pane" type="button" role="tab" aria-controls="libur-pane" aria-selected="false">
                            <i class="bi bi-calendar-x me-1"></i> Libur
                        </button>
                    </li>
                </ul>

                {{-- Tab Content --}}
                <div class="tab-content" id="jadwalFormTabContent">
                    {{-- ── Tab 1: Booking Offline ── --}}
                    <div class="tab-pane fade show active" id="offline-pane" role="tabpanel" aria-labelledby="offline-tab">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2 py-1"><i class="bi bi-person-check-fill"></i></span>
                            <h6 class="fw-bold mb-0 text-dark">Input Pemesanan Offline</h6>
                        </div>
                        <p class="text-muted mb-4" style="font-size: 0.8rem; line-height: 1.4;">Catat pesanan pelanggan yang datang langsung / via telepon. Jadwal otomatis terkunci sebagai <strong>Dipesan</strong>.</p>
                        
                        <form action="{{ route('admin.jadwal.booking-offline.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="offline">
                            
                            <div class="mb-3">
                                <label class="form-label fw-600 small text-secondary">Lapangan <span class="text-danger">*</span></label>
                                <select name="lapangan_id" id="off_lapangan_id" class="form-select @error('lapangan_id') is-invalid @enderror" required onchange="autoHarga()">
                                    <option value="">-- Pilih Lapangan --</option>
                                    @foreach($lapangans as $l)
                                        <option value="{{ $l->id }}"
                                            data-weekday="{{ $l->harga_weekday }}"
                                            data-weekend="{{ $l->harga_weekend }}"
                                            {{ old('lapangan_id') == $l->id ? 'selected' : '' }}>
                                            {{ $l->nama_lapangan }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('lapangan_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600 small text-secondary">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="off_tanggal"
                                    class="form-control @error('tanggal') is-invalid @enderror"
                                    value="{{ old('tanggal', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}"
                                    required onchange="autoHarga()">
                                @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="row g-3 mb-1">
                                <div class="col-6">
                                    <label class="form-label fw-600 small text-secondary">Jam Mulai <span class="text-danger">*</span></label>
                                    <input type="time" name="jam_mulai" id="off_jam_mulai" class="form-control @error('jam_mulai') is-invalid @enderror"
                                        value="{{ old('jam_mulai', '07:00') }}" required onchange="autoHarga()">
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-600 small text-secondary">Jam Selesai <span class="text-danger">*</span></label>
                                    <input type="time" name="jam_selesai" id="off_jam_selesai" class="form-control @error('jam_selesai') is-invalid @enderror"
                                        value="{{ old('jam_selesai', '08:00') }}" required onchange="autoHarga()">
                                    @error('jam_selesai')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                                </div>
                            </div>
                            <div id="off_time_warning" class="text-danger small mb-3 d-none fw-bold" style="transition: all 0.3s ease;">
                                <i class="bi bi-exclamation-triangle-fill me-1 animate-pulse"></i> Waktu booking tidak boleh di masa lalu!
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600 small text-secondary">Nama Pemesan <span class="text-danger">*</span></label>
                                <input type="text" name="nama_pemesan_offline"
                                    class="form-control @error('nama_pemesan_offline') is-invalid @enderror"
                                    placeholder="Cth: Budi Santoso" value="{{ old('nama_pemesan_offline') }}" required>
                                @error('nama_pemesan_offline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600 small text-secondary">No. HP <span class="text-muted">(Opsional)</span></label>
                                <input type="text" name="no_hp_offline" class="form-control"
                                    placeholder="Cth: 08123456789" value="{{ old('no_hp_offline') }}">
                            </div>

                            {{-- Fasilitas Tambahan - Collapsible --}}
                            <div class="mb-3">
                                <button class="btn btn-outline-secondary btn-sm w-100 rounded-3 mb-2 d-flex justify-content-between align-items-center" 
                                        type="button" data-bs-toggle="collapse" data-bs-target="#collapseFasilitasOffline" aria-expanded="false" aria-controls="collapseFasilitasOffline">
                                    <span class="fw-bold"><i class="bi bi-plus-circle me-1"></i>Tambah Raket / Kok (Opsional)</span>
                                    <i class="bi bi-chevron-down"></i>
                                </button>

                                <div class="collapse" id="collapseFasilitasOffline">
                                    <div class="d-flex flex-column gap-2 mt-2">
                                        @foreach($fasilitas_list ?? [] as $f)
                                        <div class="d-flex flex-column p-2 rounded-3 border bg-white shadow-sm mb-2" style="font-size: 0.85rem;">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center gap-2">
                                                    <i class="bi {{ $f->icon }} text-primary"></i>
                                                    <span>{{ $f->nama }} (+Rp {{ number_format($f->harga, 0, ',', '.') }})
                                                        <span id="off-badge-stok-{{ $f->id }}">
                                                            @if($f->stok > 0)
                                                                <span class="badge bg-light text-primary ms-1" style="font-size:0.6rem; border:1px solid #bfdbfe;">Sisa: {{ $f->stok }}</span>
                                                            @else
                                                                <span class="badge bg-light text-danger ms-1" style="font-size:0.6rem; border:1px solid #fecaca;">Habis</span>
                                                            @endif
                                                        </span>
                                                    </span>
                                                </div>
                                                <div class="input-group input-group-sm" style="width: 100px;">
                                                    <button class="btn btn-outline-secondary off-btn-minus" type="button" id="off-btn-minus-{{ $f->id }}" {{ old('fasilitas.' . $f->id, 0) == 0 ? 'disabled' : '' }}>-</button>
                                                    <input type="text" name="fasilitas[{{ $f->id }}]" class="form-control text-center off-qty-input" 
                                                           id="off-qty-input-{{ $f->id }}"
                                                           data-id="{{ $f->id }}" 
                                                           data-harga="{{ $f->harga }}" 
                                                           data-nama="{{ $f->nama }}"
                                                           data-max="{{ $f->stok }}"
                                                           value="{{ old('fasilitas.' . $f->id, 0) }}" readonly>
                                                    <button class="btn btn-outline-secondary off-btn-plus" type="button" id="off-btn-plus-{{ $f->id }}">+</button>
                                                </div>
                                            </div>
                                            <div id="off-info-stok-{{ $f->id }}" class="small mt-1 text-muted d-none" style="font-size:0.75rem; margin-left: 24px;"></div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600 small text-secondary">Total Harga <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="total_harga" id="off_total_harga"
                                        class="form-control @error('total_harga') is-invalid @enderror"
                                        placeholder="Otomatis dari harga lapangan"
                                        value="{{ old('total_harga', 0) }}" min="0" required>
                                </div>
                                <div class="mt-2 p-2 bg-light rounded border border-light-subtle d-flex align-items-center gap-1">
                                    <i class="bi bi-info-circle text-primary small"></i>
                                    <small class="text-muted" id="off_harga_info" style="font-size:.75rem;">Silakan lengkapi form di atas.</small>
                                </div>
                                @error('total_harga')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-600 small text-secondary">Catatan <span class="text-muted">(Opsional)</span></label>
                                <input type="text" name="catatan" class="form-control"
                                    placeholder="Cth: Bayar tunai / sudah lunas" value="{{ old('catatan') }}">
                            </div>

                            <button type="submit" id="off_booking_submit" class="btn btn-gradient-primary w-100 fw-bold py-2-5">
                                <i class="bi bi-calendar-check me-2"></i>Simpan Booking Offline
                            </button>
                        </form>
                    </div>

                    {{-- ── Tab 2: Blokir Jam ── --}}
                    <div class="tab-pane fade" id="blokir-pane" role="tabpanel" aria-labelledby="blokir-tab">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2 py-1"><i class="bi bi-lock-fill"></i></span>
                            <h6 class="fw-bold mb-0 text-dark">Blokir Jam (Tutup/Maintenance)</h6>
                        </div>
                        <p class="text-muted mb-4" style="font-size: 0.8rem; line-height: 1.4;">Blokir slot waktu karena renovasi, latihan VIP, atau alasan lain. Tampil sebagai <strong>Ditutup</strong> di jadwal publik.</p>
                        
                        <form action="{{ route('admin.jadwal.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="blokir">
                            
                            <div class="mb-3">
                                <label class="form-label fw-600 small text-secondary">Lapangan <span class="text-danger">*</span></label>
                                <select name="lapangan_id" class="form-select @error('lapangan_id') is-invalid @enderror" required>
                                    <option value="">-- Pilih Lapangan --</option>
                                    @foreach($lapangans as $l)
                                        <option value="{{ $l->id }}" {{ old('lapangan_id') == $l->id ? 'selected' : '' }}>
                                            {{ $l->nama_lapangan }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600 small text-secondary">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" id="blokir_tanggal" class="form-control"
                                    value="{{ old('tanggal', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="row g-3 mb-1">
                                <div class="col-6">
                                    <label class="form-label fw-600 small text-secondary">Jam Mulai <span class="text-danger">*</span></label>
                                    <input type="time" name="jam_mulai" id="blokir_jam_mulai" class="form-control"
                                        value="{{ old('jam_mulai', '07:00') }}" required>
                                </div>
                                <div class="col-6">
                                    <label class="form-label fw-600 small text-secondary">Jam Selesai <span class="text-danger">*</span></label>
                                    <input type="time" name="jam_selesai" id="blokir_jam_selesai" class="form-control"
                                        value="{{ old('jam_selesai', '08:00') }}" required>
                                </div>
                            </div>
                            <div id="blokir_time_warning" class="text-danger small mb-3 d-none fw-bold" style="transition: all 0.3s ease;">
                                <i class="bi bi-exclamation-triangle-fill me-1 animate-pulse"></i> Waktu blokir tidak boleh di masa lalu!
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-600 small text-secondary">Keterangan <span class="text-muted">(Opsional)</span></label>
                                <input type="text" name="keterangan" class="form-control"
                                    placeholder="Cth: Latihan VIP / Renovasi">
                            </div>

                            <button type="submit" id="blokir_booking_submit" class="btn btn-gradient-warning w-100 fw-bold py-2-5">
                                <i class="bi bi-lock me-2"></i>Blokir Jam Lapangan
                            </button>
                        </form>
                    </div>

                    {{-- ── Tab 3: Hari Libur ── --}}
                    <div class="tab-pane fade" id="libur-pane" role="tabpanel" aria-labelledby="libur-tab">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1"><i class="bi bi-calendar-x"></i></span>
                            <h6 class="fw-bold mb-0 text-dark">Tetapkan Hari Libur / Tutup</h6>
                        </div>
                        <p class="text-muted mb-4" style="font-size: 0.8rem; line-height: 1.4;">Tutup semua atau satu lapangan untuk seharian penuh.</p>
                        
                        <form action="{{ route('admin.libur.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="form_type" value="libur">
                            
                            <div class="mb-3">
                                <label class="form-label fw-600 small text-secondary">Tanggal <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control"
                                    value="{{ old('tanggal') }}" min="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-600 small text-secondary">Lapangan <span class="text-muted">(Opsional)</span></label>
                                <select name="lapangan_id" class="form-select">
                                    <option value="">-- Semua Lapangan --</option>
                                    @foreach($lapangans as $l)
                                        <option value="{{ $l->id }}">{{ $l->nama_lapangan }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted mt-1 d-block" style="font-size:.725rem;">Kosongkan jika semua lapangan tutup.</small>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-600 small text-secondary">Keterangan</label>
                                <input type="text" name="keterangan" class="form-control" placeholder="Cth: Libur Nasional / Renovasi">
                            </div>

                            <button type="submit" class="btn btn-gradient-danger w-100 fw-bold py-2-5">
                                <i class="bi bi-shield-x me-2"></i>Simpan Hari Libur
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ════ Kolom Kanan: Tabel Hari Libur ════ --}}
        <div class="col-lg-7">
            {{-- Tabel Hari Libur --}}
            <div class="table-card">
                <div class="table-card-header-custom">
                    <h6 class="mb-0 fw-bold text-danger"><i class="bi bi-calendar-x me-2"></i>Daftar Hari Libur / Tutup</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Lapangan</th>
                                <th>Keterangan</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($liburs ?? [] as $l)
                            <tr>
                                <td class="fw-600 text-danger">
                                    <i class="bi bi-calendar-check me-2"></i>{{ \Carbon\Carbon::parse($l->tanggal)->format('d/m/Y') }}
                                </td>
                                <td>
                                    @if($l->lapangan_id)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $l->lapangan->nama_lapangan }}</span>
                                    @else
                                        <span class="badge bg-dark-subtle text-dark border border-dark-subtle">Semua Lapangan</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $l->keterangan ?? '-' }}</td>
                                <td class="text-center">
                                    <form action="{{ route('admin.libur.destroy', $l->id) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Hapus hari libur ini? Jadwal akan kembali normal.')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-action-delete" title="Hapus"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-5">Belum ada hari libur yang ditetapkan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
/**
 * Isi otomatis total harga berdasarkan lapangan dan tanggal yang dipilih.
 */
function autoHarga() {
    const sel = document.getElementById('off_lapangan_id');
    const tgl = document.getElementById('off_tanggal');
    const jamMulai = document.getElementById('off_jam_mulai');
    const jamSelesai = document.getElementById('off_jam_selesai');
    const hargaInput = document.getElementById('off_total_harga');
    const infoEl = document.getElementById('off_harga_info');

    if (!sel || !tgl || !jamMulai || !jamSelesai || !sel.value || !tgl.value || !jamMulai.value || !jamSelesai.value) return;

    const opt = sel.options[sel.selectedIndex];
    if (!opt.value) return;

    const weekday = parseInt(opt.dataset.weekday || 0);
    const weekend = parseInt(opt.dataset.weekend || 0);

    const d = new Date(tgl.value);
    const day = d.getDay(); // 0=Minggu, 6=Sabtu
    const isWeekend = (day === 0 || day === 6);
    const hargaPerJam = isWeekend ? weekend : weekday;

    const mulaiArr = jamMulai.value.split(':');
    const selesaiArr = jamSelesai.value.split(':');
    
    let mulaiMins = parseInt(mulaiArr[0]) * 60 + parseInt(mulaiArr[1]);
    let selesaiMins = parseInt(selesaiArr[0]) * 60 + parseInt(selesaiArr[1]);
    
    if (selesaiMins < mulaiMins) {
        selesaiMins += 24 * 60; // if spans across midnight
    }
    
    let durasiJam = (selesaiMins - mulaiMins) / 60;

    if (durasiJam <= 0) {
        hargaInput.value = 0;
        infoEl.textContent = 'Durasi tidak valid';
        return;
    }

    let totalHarga = Math.round(hargaPerJam * durasiJam);
    let detailText = isWeekend
        ? `Harga Weekend: Rp ${hargaPerJam.toLocaleString('id-ID')} / jam &times; ${durasiJam} jam`
        : `Harga Weekday: Rp ${hargaPerJam.toLocaleString('id-ID')} / jam &times; ${durasiJam} jam`;

    // Hitung tambahan fasilitas offline
    const inputs = document.querySelectorAll('.off-qty-input');
    inputs.forEach(input => {
        const qty = parseInt(input.value) || 0;
        if (qty > 0) {
            const harga = parseInt(input.dataset.harga);
            const nama = input.dataset.nama;
            totalHarga += (qty * harga);
            detailText += ` + ${nama} (${qty})`;
        }
    });

    hargaInput.value = totalHarga;
    infoEl.innerHTML = detailText;
}

// Fungsi AJAX Ketersediaan Fasilitas Offline
function updateFasilitasAvailabilityOffline() {
    const tanggal = document.getElementById('off_tanggal').value;
    const jamMulai = document.getElementById('off_jam_mulai').value;
    const jamSelesai = document.getElementById('off_jam_selesai').value;

    if (!tanggal || !jamMulai || !jamSelesai) {
        return;
    }

    const url = `{{ route('booking.cek-fasilitas') }}?tanggal=${tanggal}&jam_mulai=${jamMulai}&jam_selesai=${jamSelesai}`;

    fetch(url)
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                Object.values(res.data).forEach(f => {
                    const badge = document.getElementById(`off-badge-stok-${f.id}`);
                    const btnMinus = document.getElementById(`off-btn-minus-${f.id}`);
                    const btnPlus = document.getElementById(`off-btn-plus-${f.id}`);
                    const input = document.getElementById(`off-qty-input-${f.id}`);
                    const info = document.getElementById(`off-info-stok-${f.id}`);

                    if (badge) {
                        if (f.sisa_stok > 0) {
                            badge.innerHTML = `<span class="badge bg-light text-primary ms-1" style="font-size:0.6rem; border:1px solid #bfdbfe;">Sisa: ${f.sisa_stok}</span>`;
                        } else {
                            badge.innerHTML = `<span class="badge bg-light text-danger ms-1" style="font-size:0.6rem; border:1px solid #fecaca;">Habis</span>`;
                        }
                    }

                    if (input) {
                        input.dataset.max = f.sisa_stok;
                        if (parseInt(input.value) > f.sisa_stok) {
                            input.value = f.sisa_stok;
                        }
                    }

                    if (btnMinus && btnPlus) {
                        btnMinus.disabled = (parseInt(input.value) === 0);
                        btnPlus.disabled = false;
                    }

                    if (info) {
                        if (f.tersedia_pada) {
                            info.innerHTML = `<i class="bi bi-info-circle me-1 text-warning"></i>Akan tersedia pada jam ${f.tersedia_pada}`;
                            info.classList.remove('d-none');
                        } else {
                            info.innerHTML = '';
                            info.classList.add('d-none');
                        }
                    }
                });

                autoHarga();
            }
        })
        .catch(err => console.error("Gagal mengambil status ketersediaan fasilitas offline:", err));
}

// Fungsi Real-time Clock GOR (Indonesian Format)
function updateClock() {
    const now = new Date();
    
    // Format Jam: HH:MM:SS
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const clockStr = `${hours}:${minutes}:${seconds}`;
    
    const clockEl = document.getElementById('realtime-clock');
    if (clockEl) clockEl.textContent = clockStr;
    
    // Format Tanggal: Hari, DD Bulan YYYY
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    const dayName = days[now.getDay()];
    const day = now.getDate();
    const monthName = months[now.getMonth()];
    const year = now.getFullYear();
    
    const dateStr = `${dayName}, ${day} ${monthName} ${year}`;
    const dateEl = document.getElementById('realtime-date');
    if (dateEl) dateEl.textContent = dateStr;
}

// Fungsi Penyesuaian Default Jam agar tidak terlewat saat baru load
function adjustDefaultTimes(prefix) {
    const dateEl = document.getElementById(`${prefix}_tanggal`);
    const startEl = document.getElementById(`${prefix}_jam_mulai`);
    const endEl = document.getElementById(`${prefix}_jam_selesai`);

    if (!dateEl || !startEl || !endEl) return;

    const now = new Date();
    const todayStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');

    if (dateEl.value === todayStr) {
        const currentHours = now.getHours();
        
        // Mengambil jam default (misal "07")
        const defaultStartHour = parseInt(startEl.value.split(':')[0]);
        
        if (currentHours >= defaultStartHour) {
            // Set jam mulai ke jam berikutnya
            let nextHour = currentHours + 1;
            if (nextHour >= 24) nextHour = 7; // Reset ke jam buka GOR jika tengah malam
            
            const nextHourStr = String(nextHour).padStart(2, '0') + ':00';
            let nextEndHour = nextHour + 1;
            if (nextEndHour >= 24) nextEndHour = 8;
            const nextEndHourStr = String(nextEndHour).padStart(2, '0') + ':00';

            startEl.value = nextHourStr;
            endEl.value = nextEndHourStr;
        }
    }
}

// Fungsi Validasi Waktu Masa Lalu (Real-time di Frontend)
function validateFormTime(prefix) {
    const dateEl = document.getElementById(`${prefix}_tanggal`);
    const startEl = document.getElementById(`${prefix}_jam_mulai`);
    const endEl = document.getElementById(`${prefix}_jam_selesai`);
    const warningEl = document.getElementById(`${prefix}_time_warning`);
    const submitEl = document.getElementById(`${prefix}_booking_submit`);

    if (!dateEl || !startEl || !endEl || !submitEl) return;

    const selectedDate = dateEl.value;
    const selectedStart = startEl.value;
    const selectedEnd = endEl.value;

    if (!selectedDate || !selectedStart) return;

    const now = new Date();
    const todayStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
    
    let isValid = true;
    let isPastTime = false;

    // Jika memilih hari ini, cek apakah jam mulai di masa lalu
    if (selectedDate === todayStr) {
        const currentHours = now.getHours();
        const currentMinutes = now.getMinutes();
        const currentTimeStr = `${String(currentHours).padStart(2, '0')}:${String(currentMinutes).padStart(2, '0')}`;

        // Set batasan minimum jam mulai
        startEl.setAttribute('min', currentTimeStr);

        if (selectedStart < currentTimeStr) {
            isPastTime = true;
            isValid = false;
        }
    } else {
        // Jika hari esok, batasan minimum adalah jam operasional (07:00)
        startEl.setAttribute('min', '07:00');
    }

    // Validasi jam selesai harus lebih besar dari jam mulai
    if (selectedStart && selectedEnd && selectedEnd <= selectedStart) {
        isValid = false;
    }

    // Berikan feedback visual & kunci tombol jika tidak valid
    if (isPastTime) {
        const currentHours = now.getHours();
        const currentMinutes = now.getMinutes();
        const currentTimeFormatted = `${String(currentHours).padStart(2, '0')}:${String(currentMinutes).padStart(2, '0')}`;
        const selectedFormatted = selectedStart;

        if (warningEl) {
            warningEl.classList.remove('d-none');
            warningEl.innerHTML = `
                <i class="bi bi-clock-history me-1"></i>
                Jam <strong>${selectedFormatted}</strong> sudah terlewat — sekarang pukul <strong>${currentTimeFormatted}</strong>. Pilih jam yang belum berlalu.`;
        }
        startEl.classList.add('is-invalid');
        startEl.style.borderColor = '#ef4444';
        startEl.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.15)';
        submitEl.disabled = true;
        submitEl.style.opacity = '0.6';
    } else {
        if (warningEl) warningEl.classList.add('d-none');
        startEl.classList.remove('is-invalid');
        startEl.style.borderColor = '';
        startEl.style.boxShadow = '';

        // Cek jam selesai harus setelah jam mulai
        if (selectedStart && selectedEnd && selectedEnd <= selectedStart) {
            if (warningEl) {
                warningEl.classList.remove('d-none');
                warningEl.innerHTML = `
                    <i class="bi bi-arrow-left-right me-1"></i>
                    Jam selesai (<strong>${selectedEnd}</strong>) harus lebih lambat dari jam mulai (<strong>${selectedStart}</strong>).`;
            }
            endEl.classList.add('is-invalid');
            endEl.style.borderColor = '#ef4444';
            endEl.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.15)';
            submitEl.disabled = true;
            submitEl.style.opacity = '0.6';
        } else {
            if (warningEl) warningEl.classList.add('d-none');
            endEl.classList.remove('is-invalid');
            endEl.style.borderColor = '';
            endEl.style.boxShadow = '';

            if (isValid) {
                submitEl.disabled = false;
                submitEl.style.opacity = '1';
            } else {
                submitEl.disabled = true;
                submitEl.style.opacity = '0.6';
            }
        }
    }
}

// Auto-switch to the appropriate tab in case of validation errors or backend error sessions
document.addEventListener('DOMContentLoaded', function() {

    // Sesuaikan default jam agar tidak otomatis merah di awal
    adjustDefaultTimes('off');
    adjustDefaultTimes('blokir');

    // Jalankan validasi awal
    validateFormTime('off');
    validateFormTime('blokir');

    // Run price calculator and facilities check once
    autoHarga();
    updateFasilitasAvailabilityOffline();

    // Event listeners untuk input form booking offline agar memicu check ketersediaan fasilitas & validasi waktu
    document.getElementById('off_tanggal').addEventListener('change', function() {
        validateFormTime('off');
        autoHarga();
        updateFasilitasAvailabilityOffline();
    });
    document.getElementById('off_jam_mulai').addEventListener('change', function() {
        validateFormTime('off');
        autoHarga();
        updateFasilitasAvailabilityOffline();
    });
    document.getElementById('off_jam_selesai').addEventListener('change', function() {
        validateFormTime('off');
        autoHarga();
        updateFasilitasAvailabilityOffline();
    });

    // Event listeners untuk input form blokir jam
    document.getElementById('blokir_tanggal').addEventListener('change', () => validateFormTime('blokir'));
    document.getElementById('blokir_jam_mulai').addEventListener('change', () => validateFormTime('blokir'));
    document.getElementById('blokir_jam_selesai').addEventListener('change', () => validateFormTime('blokir'));

    // Event listeners plus/minus sewa fasilitas offline
    document.querySelectorAll('.off-btn-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('.off-qty-input');
            const max = parseInt(input.dataset.max) || 0;
            const currentVal = parseInt(input.value) || 0;
            if (currentVal < max) {
                input.value = currentVal + 1;
                autoHarga();
                const btnMinus = this.closest('.input-group').querySelector('.off-btn-minus');
                if (btnMinus) btnMinus.disabled = false;
            } else {
                const nama = input.dataset.nama;
                const infoEl = document.getElementById(`off-info-stok-${input.dataset.id}`);
                let infoMsg = '';
                if (infoEl && !infoEl.classList.contains('d-none')) {
                    infoMsg = `<br><span class="text-warning fw-bold">${infoEl.innerHTML}</span>`;
                }
                
                Swal.fire({
                    title: `Stok ${nama} Terbatas`,
                    html: `Maaf, jumlah ${nama} yang tersedia saat ini adalah ${max} unit untuk slot waktu yang Anda pilih.${infoMsg}`,
                    icon: 'warning',
                    confirmButtonColor: '#2563eb',
                    confirmButtonText: 'Baik, Saya Mengerti',
                    customClass: {
                        popup: 'rounded-4'
                    }
                });
            }
        });
    });

    document.querySelectorAll('.off-btn-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.closest('.input-group').querySelector('.off-qty-input');
            const currentVal = parseInt(input.value) || 0;
            if (currentVal > 0) {
                input.value = currentVal - 1;
                autoHarga();
                if (parseInt(input.value) === 0) {
                    this.disabled = true;
                }
            }
        });
    });

    let activeTabId = 'offline-tab';
    
    @if(old('form_type') === 'blokir' || (session('error') && strpos(session('error'), 'Gagal memblokir') !== false))
        activeTabId = 'blokir-tab';
    @elseif(old('form_type') === 'libur')
        activeTabId = 'libur-tab';
    @elseif(old('form_type') === 'offline' || (session('error') && strpos(session('error'), 'Gagal! Slot waktu') !== false) || (session('error') && strpos(session('error'), 'Gagal mencatat booking offline') !== false))
        activeTabId = 'offline-tab';
    @endif
    
    const tabEl = document.getElementById(activeTabId);
    if (tabEl) {
        const tab = new bootstrap.Tab(tabEl);
        tab.show();
    }
});
</script>
@endsection
