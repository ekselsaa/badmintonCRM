@extends('layouts.app')
@section('title', 'Semua Booking')
@section('page_title', 'Semua Booking')
@section('page_subtitle', 'Kelola semua pemesanan lapangan')

@push('styles')
<style>
    /* ── Fit-to-screen overrides ── */
    .booking-page .content-area { padding: 0 !important; }
    .booking-wrapper { 
        display: flex; flex-direction: column; 
        height: calc(100vh - 65px); /* viewport minus topbar */
        overflow: hidden; padding: 12px 16px 0;
    }

    /* ── Compact Stat Strip ── */
    .stat-strip {
        display: flex; gap: 10px; flex-shrink: 0; margin-bottom: 10px;
    }
    .stat-chip {
        flex: 1; display: flex; align-items: center; gap: 10px;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 8px 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: all 0.2s;
    }
    .stat-chip:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-1px); }
    .stat-chip .chip-icon {
        width: 34px; height: 34px; border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem; flex-shrink: 0;
    }
    .stat-chip .chip-label { font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 700; color: #64748b; line-height: 1; }
    .stat-chip .chip-value { font-size: 1.05rem; font-weight: 800; color: #0f172a; line-height: 1.2; }

    /* ── Compact Filter Bar ── */
    .filter-bar {
        display: flex; align-items: center; gap: 8px; flex-shrink: 0;
        background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 8px 12px; margin-bottom: 10px; box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }
    .filter-bar .form-select, .filter-bar .form-control {
        font-size: 0.78rem; padding: 5px 10px; border-radius: 6px;
        border: 1px solid #e2e8f0; height: 32px;
    }
    .filter-bar .btn { font-size: 0.78rem; padding: 5px 14px; height: 32px; }
    .filter-bar label { font-size: 0.7rem; font-weight: 700; color: #64748b; white-space: nowrap; margin: 0; }

    /* ── Scrollable Table Container ── */
    .table-scroll-wrapper {
        flex: 1; min-height: 0; /* critical for flex overflow */
        background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04); overflow: hidden;
        display: flex; flex-direction: column;
        margin-bottom: 8px;
    }
    .table-scroll-inner {
        flex: 1; overflow-y: auto; overflow-x: auto;
    }
    .table-scroll-inner::-webkit-scrollbar { width: 5px; height: 5px; }
    .table-scroll-inner::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .table-scroll-inner::-webkit-scrollbar-track { background: transparent; }

    /* ── Compact Table ── */
    .tbl-compact thead th {
        background: #f8fafc; font-size: 0.68rem; text-transform: uppercase;
        letter-spacing: 0.8px; color: #64748b; font-weight: 700;
        padding: 8px 10px; border-bottom: 2px solid #e2e8f0;
        position: sticky; top: 0; z-index: 2;
    }
    .tbl-compact tbody td {
        padding: 6px 10px; vertical-align: middle; font-size: 0.8rem;
        border-color: #f1f5f9; color: #334155;
    }
    .tbl-compact tbody tr { transition: background 0.15s; }
    .tbl-compact tbody tr:hover { background: #eff6ff; }

    /* ── Compact customer avatar ── */
    .avatar-sm {
        width: 28px; height: 28px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 0.75rem;
    }

    @media (max-width: 768px) {
        .stat-strip { flex-wrap: wrap; }
        .stat-chip { min-width: calc(50% - 6px); }
        .filter-bar { flex-wrap: wrap; }
        .booking-wrapper { height: auto; overflow: visible; padding: 8px; }
        .table-scroll-wrapper { max-height: 60vh; }
    }
</style>
@endpush

@section('content')
<div class="booking-wrapper">
    {{-- Tabs Navigasi Transaksi --}}
    <ul class="nav nav-pills gap-2 mb-3 bg-white p-2 rounded-3 border" style="font-size: 0.82rem; width: max-content; flex-shrink:0;">
        <li class="nav-item">
            <a class="nav-link active px-3 py-2 fw-semibold" href="{{ route('admin.booking.index') }}">
                <i class="bi bi-calendar-check me-2"></i>Daftar Booking
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 fw-semibold text-secondary" href="{{ route('admin.pembayaran.index') }}">
                <i class="bi bi-credit-card-2-front me-2"></i>Verifikasi Pembayaran
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 fw-semibold text-secondary" href="{{ route('admin.pembayaran-membership.index') }}">
                <i class="bi bi-star me-2"></i>Verifikasi Pembayaran Member
            </a>
        </li>
    </ul>

    {{-- Compact Stat Strip --}}
    @php $isFiltered = request('status') || request('tanggal'); @endphp
    <div class="stat-strip">
        <div class="stat-chip">
            <div class="chip-icon bg-primary-subtle text-primary"><i class="bi bi-journal-text"></i></div>
            <div>
                <div class="chip-label d-flex align-items-center gap-1">
                    Total
                    @if($isFiltered)<span class="badge bg-primary-subtle text-primary" style="font-size:0.55rem;padding:2px 5px;border-radius:4px;">Filter</span>@endif
                </div>
                <div class="chip-value">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="stat-chip">
            <div class="chip-icon bg-warning-subtle text-warning"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <div class="chip-label d-flex align-items-center gap-1">
                    Pending
                    @if(request('tanggal'))<span class="badge bg-warning-subtle text-warning" style="font-size:0.55rem;padding:2px 5px;border-radius:4px;">Filter</span>@endif
                </div>
                <div class="chip-value">{{ $stats['pending'] }}</div>
            </div>
        </div>
        <div class="stat-chip">
            <div class="chip-icon bg-success-subtle text-success"><i class="bi bi-calendar-check"></i></div>
            <div>
                <div class="chip-label d-flex align-items-center gap-1">
                    Aktif
                    @if(request('tanggal'))<span class="badge bg-success-subtle text-success" style="font-size:0.55rem;padding:2px 5px;border-radius:4px;">Filter</span>@endif
                </div>
                <div class="chip-value">{{ $stats['aktif'] }}</div>
            </div>
        </div>
        <div class="stat-chip">
            <div class="chip-icon bg-info-subtle text-info"><i class="bi bi-cash-stack"></i></div>
            <div>
                <div class="chip-label d-flex align-items-center gap-1">
                    {{ $stats['pendapatan_label'] }}
                    @if($isFiltered)<span class="badge bg-info-subtle text-info" style="font-size:0.55rem;padding:2px 5px;border-radius:4px;">Filter</span>@endif
                </div>
                <div class="chip-value" style="font-size:0.85rem;">Rp {{ number_format($stats['pendapatan_bulan_ini'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Compact Filter Bar --}}
    <div class="filter-bar">
        <form method="GET" class="d-flex align-items-center gap-2 w-100 m-0">
            <label><i class="bi bi-funnel me-1 text-primary"></i>Status</label>
            <select name="status" class="form-select" style="width: 140px;">
                <option value="">Semua</option>
                @foreach(['pending','dikonfirmasi','dipesan','selesai','dibatalkan'] as $s)
                    <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <label><i class="bi bi-calendar3 me-1 text-primary"></i>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" style="width: 150px;" value="{{ request('tanggal') }}">
            <button class="btn btn-primary btn-sm d-flex align-items-center gap-1"><i class="bi bi-filter"></i> Filter</button>
            <a href="{{ route('admin.booking.index') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
        </form>
    </div>

    {{-- Scrollable Table --}}
    <div class="table-scroll-wrapper">
        <div class="table-scroll-inner">
            <table class="table table-hover align-middle mb-0 tbl-compact">
                <thead>
                    <tr>
                        <th style="width:3%">No.</th>
                        <th style="width:20%">Pelanggan</th>
                        <th style="width:17%">Lapangan</th>
                        <th style="width:14%">Jadwal</th>
                        <th style="width:11%">Total</th>
                        <th style="width:10%">Status</th>
                        <th style="width:10%">Bayar</th>
                        <th style="width:15%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                    <tr>
                        <td><span class="text-secondary fw-semibold">{{ $loop->iteration }}</span></td>
                        <td>
                            @if($b->is_offline)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm bg-primary-subtle text-primary"><i class="bi bi-person-workspace"></i></div>
                                    <div style="min-width:0">
                                        <div class="fw-semibold text-dark text-truncate" style="font-size:0.8rem;">
                                            {{ $b->nama_pemesan_offline ?? 'Offline' }}
                                            <span class="badge bg-primary" style="font-size:.55rem; padding:1px 4px; font-weight:500; vertical-align:middle;">OFF</span>
                                        </div>
                                        <div class="text-secondary text-truncate" style="font-size:0.68rem;"><i class="bi bi-telephone me-1"></i>{{ $b->no_hp_offline ?? '-' }}</div>
                                    </div>
                                </div>
                            @else
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-sm bg-info-subtle text-info"><i class="bi bi-person"></i></div>
                                    <div style="min-width:0">
                                        <div class="fw-semibold text-dark text-truncate" style="font-size:0.8rem;">
                                            {{ $b->user?->name ?? '-' }}
                                            <span class="badge bg-info text-white" style="font-size:.55rem; padding:1px 4px; font-weight:500; vertical-align:middle;">ON</span>
                                        </div>
                                        <div class="text-secondary text-truncate" style="font-size:0.68rem;"><i class="bi bi-person me-1"></i>{{ $b->user?->username ?? '-' }}</div>
                                    </div>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark" style="font-size:0.8rem;">{{ $b->lapangan->nama_lapangan ?? '-' }}</div>
                            @if($b->fasilitas)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size:.6rem; padding:1px 5px; margin-top:2px; display:inline-block;" title="{{ $b->fasilitas }}">
                                    <i class="bi bi-box-seam me-1"></i>{{ Str::limit($b->fasilitas, 20) }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark" style="font-size:0.78rem;">
                                <i class="bi bi-calendar3 me-1 text-primary" style="font-size:0.7rem;"></i>{{ $b->jadwal ? $b->jadwal->tanggal->format('d/m/Y') : '-' }}
                            </div>
                            <div class="text-secondary" style="font-size:0.7rem;">
                                <i class="bi bi-clock me-1 text-primary" style="font-size:0.65rem;"></i>{{ $b->jadwal ? \Carbon\Carbon::parse($b->jadwal->jam_mulai)->format('H:i') . '-' . ($b->jadwal->jam_selesai == '24:00:00' ? '24:00' : \Carbon\Carbon::parse($b->jadwal->jam_selesai)->format('H:i')) : '-' }}
                            </div>
                        </td>
                        <td>
                            <span class="text-primary fw-bold" style="font-size:0.82rem;">Rp {{ number_format($b->total_harga, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            <span class="badge badge-{{ $b->status }} px-2 py-1 rounded-pill" style="font-size:.65rem">
                                @if($b->status === 'pending') ⏳ Pending
                                @elseif($b->status === 'dikonfirmasi') ✅ Konfirmasi
                                @elseif($b->status === 'dipesan') 📌 Dipesan
                                @elseif($b->status === 'selesai') 🏆 Selesai
                                @elseif($b->status === 'dibatalkan') ❌ Batal
                                @else {{ ucfirst($b->status) }}
                                @endif
                            </span>
                        </td>
                        <td>
                            @if($b->pembayaran)
                                <span class="badge bg-{{ $b->pembayaran->status_verifikasi === 'diverifikasi' ? 'success' : ($b->pembayaran->status_verifikasi === 'menunggu' ? 'warning text-dark' : 'danger') }} px-2 py-1 rounded-pill" style="font-size:.65rem">
                                    {{ $b->pembayaran->status_verifikasi === 'diverifikasi' ? '✅ Lunas' : ($b->pembayaran->status_verifikasi === 'menunggu' ? '⏳ Pending' : '❌ Tolak') }}
                                </span>
                                <div class="text-secondary" style="font-size:0.62rem;"><i class="bi bi-credit-card me-1"></i>{{ strtoupper($b->pembayaran->metode_pembayaran) }}</div>
                            @elseif($b->catatan && str_contains($b->catatan, 'Sesi Rutin Member'))
                                <span class="badge bg-success px-2 py-1 rounded-pill" style="font-size:.65rem">
                                    ✅ Lunas
                                </span>
                                <div class="text-secondary" style="font-size:0.62rem;"><i class="bi bi-star-fill text-warning me-1"></i>MEMBER</div>
                            @else
                                <span class="badge bg-light text-muted border px-2 py-1 rounded-pill" style="font-size:.65rem">Belum bayar</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-1">
                                <a href="{{ route('admin.booking.show', $b->id) }}" class="btn btn-sm btn-outline-info d-flex align-items-center justify-content-center" title="Detail Booking"
                                   style="width:26px; height:26px; border-radius:5px; padding:0; font-size:0.75rem;">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                @if(in_array($b->status, ['dipesan', 'dikonfirmasi', 'selesai']))
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-ubah-fasilitas d-flex align-items-center justify-content-center" 
                                            data-id="{{ $b->id }}" title="Ubah Sewa Fasilitas"
                                            style="width:26px; height:26px; border-radius:5px; padding:0; font-size:0.75rem;">
                                        <i class="bi bi-cart-plus-fill"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-calendar-x fs-3 d-block mb-1 text-secondary"></i>
                        <span style="font-size:0.85rem;">Belum ada booking yang sesuai kriteria.</span>
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bookings->hasPages())
        <div class="px-4 pb-4 pt-1">{{ $bookings->appends(request()->all())->links() }}</div>
        @endif
    </div>
</div>

{{-- Modal Ubah Fasilitas --}}
<div class="modal fade" id="ubahFasilitasModal" tabindex="-1" aria-labelledby="ubahFasilitasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-light border-bottom-0 pb-0 pt-3 px-4">
                <h6 class="modal-title fw-bold text-dark" id="ubahFasilitasModalLabel">
                    <i class="bi bi-cart-plus me-2 text-primary"></i>Ubah Fasilitas Sewa
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formUbahFasilitas" method="POST">
                @csrf
                @method('PUT')
                
                <div class="modal-body px-4 pt-2 pb-3">
                    {{-- Detail Booking Info --}}
                    <div class="p-2 bg-light rounded-3 mb-3 border border-light-subtle" style="font-size: 0.8rem;">
                        <div class="row g-1">
                            <div class="col-6">
                                <div class="text-secondary" style="font-size:0.7rem;">Pemesan</div>
                                <div class="fw-bold text-dark" id="modal-nama-pemesan">-</div>
                            </div>
                            <div class="col-6">
                                <div class="text-secondary" style="font-size:0.7rem;">Tanggal</div>
                                <div class="fw-bold text-dark" id="modal-tanggal">-</div>
                            </div>
                            <div class="col-6">
                                <div class="text-secondary" style="font-size:0.7rem;">Waktu</div>
                                <div class="fw-bold text-dark" id="modal-slot-waktu">-</div>
                            </div>
                            <div class="col-6">
                                <div class="text-secondary" style="font-size:0.7rem;">Total Awal</div>
                                <div class="fw-bold text-primary" id="modal-total-awal">-</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="fw-bold text-dark mb-2" style="font-size:0.85rem;">Pilihan Fasilitas Tambahan</div>
                    
                    {{-- Container List Fasilitas --}}
                    <div id="modal-fasilitas-list" class="d-flex flex-column gap-2">
                        <div class="text-center py-3 text-secondary">
                            <div class="spinner-border spinner-border-sm text-primary mb-1" role="status"></div>
                            <div style="font-size:0.8rem;">Memuat data fasilitas...</div>
                        </div>
                    </div>
                    
                    <hr class="my-3 border-secondary-subtle">
                    
                    {{-- Ringkasan Biaya --}}
                    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:0.8rem;">
                        <span class="text-secondary">Biaya Fasilitas Baru:</span>
                        <span class="fw-bold text-dark" id="modal-total-baru">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:0.8rem;">
                        <span class="text-secondary">Biaya Fasilitas Lama:</span>
                        <span class="fw-bold text-muted text-decoration-line-through" id="modal-total-lama">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center p-2 rounded-3 bg-primary-subtle border border-primary-subtle mt-2">
                        <span class="fw-bold text-primary" style="font-size:0.85rem;">Selisih di Kasir:</span>
                        <span class="fw-bold text-primary" style="font-size:1.1rem;" id="modal-selisih-harga">Rp 0</span>
                    </div>
                </div>
                
                <div class="modal-footer bg-light border-top-0 pt-0 pb-3 px-4">
                    <button type="button" class="btn btn-secondary btn-sm rounded-3 px-3 py-1" data-bs-dismiss="modal" style="font-size:0.8rem;">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm rounded-3 px-3 py-1 fw-bold" id="btn-simpan-fasilitas" style="font-size:0.8rem;">
                        <i class="bi bi-save me-1"></i>Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add class to parent for CSS targeting
    const contentArea = document.querySelector('.content-area');
    if (contentArea) contentArea.closest('.main-content')?.classList.add('booking-page');
    // Override content-area padding for this page
    if (contentArea) contentArea.style.padding = '0';

    const modalEl = document.getElementById('ubahFasilitasModal');
    const modal = new bootstrap.Modal(modalEl);
    
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-ubah-fasilitas');
        if (btn) openFasilitasModal(btn.dataset.id);
    });
    
    let originalTotalHarga = 0, originalFasilitasCost = 0;
    let currentFasilitasPrices = {}, initialQuantities = {};
    
    function openFasilitasModal(bookingId) {
        const listContainer = document.getElementById('modal-fasilitas-list');
        listContainer.innerHTML = '<div class="text-center py-3 text-secondary"><div class="spinner-border spinner-border-sm text-primary mb-1" role="status"></div><div style="font-size:0.8rem;">Memuat...</div></div>';
        document.getElementById('btn-simpan-fasilitas').disabled = true;
        modal.show();
        
        fetch(`{{ url('/admin/booking') }}/${bookingId}/fasilitas`)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const b = res.booking;
                    document.getElementById('modal-nama-pemesan').textContent = b.nama_pemesan;
                    document.getElementById('modal-tanggal').textContent = formatDate(b.tanggal);
                    document.getElementById('modal-slot-waktu').textContent = `${b.jam_mulai} - ${b.jam_selesai}`;
                    originalTotalHarga = parseFloat(b.total_harga);
                    document.getElementById('modal-total-awal').textContent = 'Rp ' + originalTotalHarga.toLocaleString('id-ID');
                    document.getElementById('formUbahFasilitas').action = `{{ url('/admin/booking') }}/${bookingId}/fasilitas`;
                    
                    listContainer.innerHTML = '';
                    originalFasilitasCost = 0;
                    currentFasilitasPrices = {};
                    initialQuantities = {};
                    
                    res.fasilitas.forEach(f => {
                        currentFasilitasPrices[f.id] = parseFloat(f.harga);
                        initialQuantities[f.id] = parseInt(f.jumlah_dipesan) || 0;
                        originalFasilitasCost += initialQuantities[f.id] * currentFasilitasPrices[f.id];
                        const maxQty = initialQuantities[f.id] + parseInt(f.sisa_stok);
                        
                        listContainer.insertAdjacentHTML('beforeend', `
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 border bg-white" style="font-size:0.82rem;">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi ${f.icon || 'bi-box-seam'} text-primary" style="font-size:1.1rem;"></i>
                                    <div>
                                        <div class="fw-bold">${f.nama}</div>
                                        <div class="text-muted" style="font-size:0.7rem;">Rp ${f.harga.toLocaleString('id-ID')}/unit · Stok: ${f.sisa_stok}</div>
                                    </div>
                                </div>
                                <div class="input-group input-group-sm" style="width:90px;">
                                    <button class="btn btn-outline-secondary btn-modal-minus" type="button" data-id="${f.id}" style="padding:2px 6px;">-</button>
                                    <input type="text" name="fasilitas[${f.id}]" class="form-control text-center modal-qty-input" 
                                           id="modal-qty-${f.id}" data-id="${f.id}" data-harga="${f.harga}" data-max="${maxQty}"
                                           value="${initialQuantities[f.id]}" readonly style="padding:2px;">
                                    <button class="btn btn-outline-secondary btn-modal-plus" type="button" data-id="${f.id}" style="padding:2px 6px;">+</button>
                                </div>
                            </div>
                        `);
                    });
                    
                    listContainer.querySelectorAll('.btn-modal-plus').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const inp = document.getElementById(`modal-qty-${this.dataset.id}`);
                            if ((parseInt(inp.value)||0) < (parseInt(inp.dataset.max)||0)) { inp.value = (parseInt(inp.value)||0)+1; calculateCosts(); }
                        });
                    });
                    listContainer.querySelectorAll('.btn-modal-minus').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const inp = document.getElementById(`modal-qty-${this.dataset.id}`);
                            if ((parseInt(inp.value)||0) > 0) { inp.value = (parseInt(inp.value)||0)-1; calculateCosts(); }
                        });
                    });
                    
                    calculateCosts();
                    document.getElementById('btn-simpan-fasilitas').disabled = false;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: res.message || 'Terjadi kesalahan saat memuat data fasilitas.'
                    });
                    modal.hide();
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan Koneksi',
                    text: 'Gagal menghubungi server. Silakan coba lagi.'
                });
                modal.hide();
            });
    }
    
    function calculateCosts() {
        let total = 0;
        document.querySelectorAll('.modal-qty-input').forEach(inp => { total += (parseInt(inp.value)||0) * (parseFloat(inp.dataset.harga)||0); });
        const diff = total - originalFasilitasCost;
        document.getElementById('modal-total-baru').textContent = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('modal-total-lama').textContent = 'Rp ' + originalFasilitasCost.toLocaleString('id-ID');
        const el = document.getElementById('modal-selisih-harga');
        el.innerHTML = diff > 0 ? `<span class="text-success">+Rp ${diff.toLocaleString('id-ID')}</span>` : diff < 0 ? `<span class="text-danger">-Rp ${Math.abs(diff).toLocaleString('id-ID')}</span>` : 'Rp 0';
    }
    
    function formatDate(d) { if (!d) return '-'; const p=d.split('-'); return p.length===3 ? `${p[2]}/${p[1]}/${p[0]}` : d; }
});
</script>
@endsection
