@extends('layouts.app')

@section('title', 'Dashboard Admin')
@section('page_title', 'Dashboard Admin')

@section('content')
<style>
    /* ── Enhanced Stat Cards ── */
    .dash-stat {
        background: #fff;
        border-radius: 14px;
        padding: 1.25rem 1.4rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
        transition: transform .2s ease, box-shadow .2s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .dash-stat:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,.1);
    }
    .dash-stat::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 4px;
        border-radius: 4px 0 0 4px;
    }
    .dash-stat.blue::before  { background: #3b82f6; }
    .dash-stat.green::before { background: #10b981; }
    .dash-stat.amber::before { background: #f59e0b; }
    .dash-stat.red::before   { background: #ef4444; }
    .dash-stat.purple::before{ background: #8b5cf6; }

    .dash-stat .stat-num {
        font-size: 1.9rem;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -1px;
    }
    .dash-stat .stat-sub {
        font-size: .75rem;
        color: #94a3b8;
        margin-top: 2px;
    }
    .dash-stat .stat-badge {
        font-size: .7rem;
        padding: .2rem .55rem;
        border-radius: 20px;
        font-weight: 600;
    }
    .dash-stat .stat-icon-wrap {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.3rem; flex-shrink: 0;
    }

    /* ── Revenue Highlight ── */
    .revenue-card {
        background: linear-gradient(135deg, #1e3a5f 0%, #1d4ed8 60%, #0ea5e9 100%);
        border-radius: 14px;
        color: #fff;
        padding: 1.4rem 1.6rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 6px 24px rgba(29,78,216,.3);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .revenue-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 32px rgba(29,78,216,.45);
    }
    .revenue-card::after {
        content: '';
        position: absolute;
        width: 200px; height: 200px;
        background: rgba(255,255,255,.06);
        border-radius: 50%;
        top: -60px; right: -60px;
    }

    /* ── Quick Actions ── */
    .quick-action {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: .7rem 1rem;
        display: flex; align-items: center; gap:.65rem;
        text-decoration: none; color: #334155;
        font-size: .83rem; font-weight: 600;
        transition: all .2s;
    }
    .quick-action:hover {
        border-color: #3b82f6;
        color: #1d4ed8;
        background: #eff6ff;
        transform: translateY(-1px);
    }
    .quick-action i { font-size: 1.05rem; }

    /* ── Table Card ── */
    .dash-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
        overflow: hidden;
    }
    .dash-card-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .dash-card-header h6 { font-size: .9rem; font-weight: 700; margin: 0; }

    /* ── Avatar initials ── */
    .av {
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .8rem; color: #fff;
        flex-shrink: 0;
    }

    /* ── Status badge colours ── */
    .b-pending     { background:#fef3c7; color:#92400e; }
    .b-dikonfirmasi{ background:#dbeafe; color:#1e40af; }
    .b-dipesan     { background:#d1fae5; color:#065f46; }
    .b-selesai     { background:#f0fdf4; color:#166534; }
    .b-dibatalkan  { background:#fee2e2; color:#991b1b; }

    /* ── Alert stok habis ── */
    .stok-alert {
        background: linear-gradient(90deg,#fff1f2,#fff5f5);
        border: 1px solid #fecaca;
        border-left: 4px solid #ef4444;
        border-radius: 12px;
        padding: .75rem 1rem;
    }
</style>

<div>

    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 py-2 mb-3">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Alert stok habis --}}
    @if($fasilitasHabis > 0)
        <div class="stok-alert d-flex align-items-center gap-3 mb-4">
            <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i>
            <div class="flex-grow-1">
                <strong style="color:#991b1b;">{{ $fasilitasHabis }} item fasilitas stok habis!</strong>
                <span class="ms-2 text-muted" style="font-size:.83rem;">Segera tambah stok agar tidak mengganggu operasional.</span>
            </div>
            <a href="{{ route('admin.fasilitas.index') }}" class="btn btn-sm btn-danger rounded-pill px-3">
                Kelola Stok
            </a>
        </div>
    @endif

    {{-- ── Baris 1: Revenue + 5 Stat Cards ── --}}
    <div class="row g-3 mb-3">

        {{-- Revenue highlight (lebar 2 kolom) --}}
        <div class="col-12 col-xl-4">
            <a href="{{ route('admin.laporan.index') }}" class="text-decoration-none d-block h-100">
                <div class="revenue-card h-100">
                    <div class="d-flex align-items-start justify-content-between mb-3">
                        <div>
                            <div style="font-size:.78rem; opacity:.75; font-weight:600; letter-spacing:.5px; text-transform:uppercase;">
                                Pendapatan Bulan Ini
                            </div>
                            <div style="font-size:1.6rem; font-weight:800; letter-spacing:-1px; line-height:1.15; margin-top:.2rem;">
                                Rp {{ number_format($pendapatanBulan, 0, ',', '.') }}
                            </div>
                            <div style="font-size:.78rem; opacity:.7; margin-top:.2rem;">
                                Dari booking dikonfirmasi & selesai
                            </div>
                        </div>
                        <div style="background:rgba(255,255,255,.15); border-radius:12px; width:48px; height:48px;
                                    display:flex; align-items:center; justify-content:center; font-size:1.4rem;">
                            💰
                        </div>
                    </div>
                    <div class="d-flex gap-3 mt-3 pt-3" style="border-top:1px solid rgba(255,255,255,.15);">
                        <div>
                            <div style="font-size:1.15rem; font-weight:800;">{{ $bookingBulanIni }}</div>
                            <div style="font-size:.72rem; opacity:.7;">Booking bulan ini</div>
                        </div>
                        <div style="width:1px; background:rgba(255,255,255,.15);"></div>
                        <div>
                            <div style="font-size:1.15rem; font-weight:800;">{{ $bookingHariIni }}</div>
                            <div style="font-size:.72rem; opacity:.7;">Booking hari ini</div>
                        </div>
                        <div style="width:1px; background:rgba(255,255,255,.15);"></div>
                        <div>
                            <div style="font-size:1.15rem; font-weight:800;">{{ $pelangganBaru }}</div>
                            <div style="font-size:.72rem; opacity:.7;">Pelanggan baru</div>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- 5 stat cards --}}
        <div class="col-12 col-xl-8">
            <div class="row g-3 h-100">

                <div class="col-6 col-md-4">
                    <a href="{{ route('admin.crm.pelanggan') }}" class="text-decoration-none d-block h-100">
                        <div class="dash-stat blue">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="stat-icon-wrap" style="background:#dbeafe;">
                                    <i class="bi bi-people-fill" style="color:#1d4ed8;"></i>
                                </div>
                                <span class="stat-badge" style="background:#dbeafe; color:#1d4ed8;">
                                    +{{ $pelangganBaru }} baru
                                </span>
                            </div>
                            <div class="stat-num" style="color:#1e293b;">{{ $totalPelanggan }}</div>
                            <div class="stat-sub">Total Pelanggan</div>
                            <div style="font-size: 0.68rem; margin-top: 6px; display: flex; gap: 8px; font-weight: 600;">
                                <span class="text-primary"><i class="bi bi-globe me-0.5"></i> {{ $totalPelangganOnline }} Online</span>
                                <span class="text-secondary"><i class="bi bi-person-x-fill me-0.5"></i> {{ $totalPelangganOffline }} Offline</span>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md-4">
                    <a href="{{ route('admin.lapangan.index') }}" class="text-decoration-none d-block h-100">
                        <div class="dash-stat green">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="stat-icon-wrap" style="background:#d1fae5;">
                                    <i class="bi bi-grid-3x3-gap-fill" style="color:#059669;"></i>
                                </div>
                                <span class="stat-badge" style="background:#d1fae5; color:#065f46;">Aktif</span>
                            </div>
                            <div class="stat-num" style="color:#1e293b;">{{ $totalLapangan }}</div>
                            <div class="stat-sub">Jumlah Lapangan</div>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md-4">
                    <a href="{{ route('admin.booking.index') }}" class="text-decoration-none d-block h-100">
                        <div class="dash-stat amber">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="stat-icon-wrap" style="background:#fef3c7;">
                                    <i class="bi bi-receipt-cutoff" style="color:#d97706;"></i>
                                </div>
                                <span class="stat-badge" style="background:#fef3c7; color:#92400e;">
                                    {{ $bookingHariIni }} hari ini
                                </span>
                            </div>
                            <div class="stat-num" style="color:#1e293b;">{{ $totalBooking }}</div>
                            <div class="stat-sub">Total Booking</div>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md-4">
                    <a href="{{ $pendingVerifMembership > 0 && $pendingVerifBookings == 0 ? route('admin.pembayaran-membership.index') : route('admin.pembayaran.index') }}" class="text-decoration-none d-block h-100">
                        <div class="dash-stat red">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="stat-icon-wrap" style="background:#fee2e2;">
                                    <i class="bi bi-credit-card-2-front" style="color:#dc2626;"></i>
                                </div>
                                @if($pendingVerif > 0)
                                    <span class="stat-badge" style="background:#fee2e2; color:#991b1b;">
                                        <i class="bi bi-dot" style="font-size:1rem;"></i>Menunggu
                                    </span>
                                @else
                                    <span class="stat-badge" style="background:#d1fae5; color:#065f46;">Lunas</span>
                                @endif
                            </div>
                            <div class="stat-num" style="color:{{ $pendingVerif > 0 ? '#dc2626' : '#1e293b' }};">{{ $pendingVerif }}</div>
                            <div class="stat-sub">Perlu Diverifikasi</div>
                            @if($pendingVerif > 0)
                                <div style="font-size: 0.68rem; margin-top: 6px; display: flex; gap: 8px; font-weight: 600;">
                                    @if($pendingVerifBookings > 0)
                                        <span class="text-danger" title="{{ $pendingVerifBookings }} Booking pending"><i class="bi bi-calendar-event me-0.5"></i> {{ $pendingVerifBookings }} Booking</span>
                                    @endif
                                    @if($pendingVerifMembership > 0)
                                        <span class="text-warning" title="{{ $pendingVerifMembership }} Member pending"><i class="bi bi-star-fill me-0.5"></i> {{ $pendingVerifMembership }} Member</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md-4">
                    <a href="{{ route('admin.fasilitas.index') }}" class="text-decoration-none d-block h-100">
                        <div class="dash-stat purple">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="stat-icon-wrap" style="background:#ede9fe;">
                                    <i class="bi bi-bag-fill" style="color:#7c3aed;"></i>
                                </div>
                                @if($fasilitasHabis > 0)
                                    <span class="stat-badge" style="background:#fee2e2; color:#991b1b;">
                                        {{ $fasilitasHabis }} Habis
                                    </span>
                                @else
                                    <span class="stat-badge" style="background:#d1fae5; color:#065f46;">Semua Aman</span>
                                @endif
                            </div>
                            <div class="stat-num" style="color:#1e293b;">{{ $totalFasilitas }}</div>
                            <div class="stat-sub">Fasilitas Tambahan</div>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md-4">
                    <a href="{{ route('admin.laporan.index') }}" class="text-decoration-none d-block h-100">
                        <div class="dash-stat green">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="stat-icon-wrap" style="background:#d1fae5;">
                                    <i class="bi bi-bar-chart-fill" style="color:#059669;"></i>
                                </div>
                                <span class="stat-badge" style="background:#d1fae5; color:#065f46;">{{ now()->format('M') }}</span>
                            </div>
                            <div class="stat-num" style="color:#1e293b; font-size:1.2rem;">
                                {{ $bookingBulanIni }}
                            </div>
                            <div class="stat-sub">Booking Bulan Ini</div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- ── Quick Actions ── --}}
    <div class="mb-4">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="text-muted fw-600" style="font-size:.78rem; white-space:nowrap;">Aksi Cepat:</span>
            <a href="{{ route('admin.jadwal.index') }}" class="quick-action">
                <i class="bi bi-calendar-plus text-primary"></i> Tambah Jadwal
            </a>
            <a href="{{ route('admin.pembayaran.index') }}" class="quick-action">
                <i class="bi bi-shield-check text-warning"></i> Verifikasi Bayar
                @if($pendingVerif > 0)
                    <span class="badge bg-danger rounded-pill" style="font-size:.65rem;">{{ $pendingVerif }}</span>
                @endif
            </a>
            <a href="{{ route('admin.fasilitas.index') }}" class="quick-action">
                <i class="bi bi-bag-plus text-purple" style="color:#7c3aed;"></i> Kelola Fasilitas
            </a>
            <a href="{{ route('admin.laporan.index') }}" class="quick-action">
                <i class="bi bi-file-earmark-bar-graph text-success"></i> Laporan
            </a>
            <a href="{{ route('admin.crm.pelanggan') }}" class="quick-action">
                <i class="bi bi-person-lines-fill text-info"></i> Data Pelanggan
            </a>
        </div>
    </div>

    {{-- ── Baris: Grafik Analitik ── --}}
    <div class="row g-3 mb-4">
        {{-- Grafik Tren Pendapatan --}}
        <div class="col-lg-8">
            <div class="dash-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Tren Pendapatan & Booking</h6>
                        <div class="text-muted small" style="font-size: .75rem;">
                            Dihitung per: <strong class="text-primary">6 Bulan Terakhir</strong>
                        </div>
                    </div>
                </div>
                <div style="position: relative; height: 320px;">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Grafik Okupansi Lapangan --}}
        <div class="col-lg-4">
            <div class="dash-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-pie-chart-fill me-2 text-success"></i>Okupansi Lapangan</h6>
                        <div class="text-muted small" style="font-size: .75rem;">
                            Dihitung per: <strong class="text-success">{{ $courtFilter['label'] }}</strong>
                        </div>
                    </div>
                    
                    <!-- Dropdown Filter Okupansi -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" style="border-radius: 8px; font-size: .75rem;">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <div class="dropdown-menu p-3 dropdown-menu-end" style="width: 260px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; border-radius: 12px;">
                            <form action="{{ route('admin.dashboard') }}" method="GET">
                                <!-- Preserve other filters -->
                                <input type="hidden" name="peak_filter_type" value="{{ $peakFilter['type'] }}">
                                <input type="hidden" name="peak_filter_date" value="{{ $peakFilter['date'] }}">
                                <input type="hidden" name="peak_filter_month" value="{{ $peakFilter['month'] }}">
                                <input type="hidden" name="payment_filter_type" value="{{ $paymentFilter['type'] }}">
                                <input type="hidden" name="payment_filter_date" value="{{ $paymentFilter['date'] }}">
                                <input type="hidden" name="payment_filter_month" value="{{ $paymentFilter['month'] }}">
                                
                                <div class="mb-2">
                                    <label class="form-label text-muted fw-600 mb-1" style="font-size: .75rem;">Rentang Waktu</label>
                                    <select name="court_filter_type" class="form-select form-select-sm filter-type-select" style="border-radius: 8px; font-size: .8rem;">
                                        <option value="all" {{ $courtFilter['type'] === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                                        <option value="today" {{ $courtFilter['type'] === 'today' ? 'selected' : '' }}>Hari Ini</option>
                                        <option value="this_month" {{ $courtFilter['type'] === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                                        <option value="custom_date" {{ $courtFilter['type'] === 'custom_date' ? 'selected' : '' }}>Pilih Tanggal</option>
                                        <option value="custom_month" {{ $courtFilter['type'] === 'custom_month' ? 'selected' : '' }}>Pilih Bulan</option>
                                    </select>
                                </div>
                                
                                <div class="mb-2 date-input-container" style="display: {{ $courtFilter['type'] === 'custom_date' ? 'block' : 'none' }};">
                                    <label class="form-label text-muted fw-600 mb-1" style="font-size: .75rem;">Pilih Tanggal</label>
                                    <input type="date" name="court_filter_date" value="{{ $courtFilter['date'] }}" class="form-control form-control-sm" style="border-radius: 8px; font-size: .8rem;">
                                </div>
                                
                                <div class="mb-2 month-input-container" style="display: {{ $courtFilter['type'] === 'custom_month' ? 'block' : 'none' }};">
                                    <label class="form-label text-muted fw-600 mb-1" style="font-size: .75rem;">Pilih Bulan</label>
                                    <input type="month" name="court_filter_month" value="{{ $courtFilter['month'] }}" class="form-control form-control-sm" style="border-radius: 8px; font-size: .8rem;">
                                </div>
                                
                                <button type="submit" class="btn btn-success btn-sm w-100 mt-2 d-flex align-items-center justify-content-center gap-1" style="border-radius: 8px; font-weight: 600; font-size: .8rem;">
                                    Terapkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div style="position: relative; height: 320px;" class="d-flex align-items-center justify-content-center">
                    <canvas id="courtChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- Grafik Jam Sibuk --}}
        <div class="col-lg-7">
            <div class="dash-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-hourglass-split me-2 text-warning"></i>Jam Sibuk Booking</h6>
                        <div class="text-muted small" style="font-size: .75rem;">
                            Dihitung per: <strong class="text-warning">{{ $peakFilter['label'] }}</strong> (Jam Operasional)
                        </div>
                    </div>
                    
                    <!-- Dropdown Filter Jam Sibuk -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" style="border-radius: 8px; font-size: .75rem;">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <div class="dropdown-menu p-3 dropdown-menu-end" style="width: 260px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; border-radius: 12px;">
                            <form action="{{ route('admin.dashboard') }}" method="GET">
                                <!-- Preserve other filters -->
                                <input type="hidden" name="court_filter_type" value="{{ $courtFilter['type'] }}">
                                <input type="hidden" name="court_filter_date" value="{{ $courtFilter['date'] }}">
                                <input type="hidden" name="court_filter_month" value="{{ $courtFilter['month'] }}">
                                <input type="hidden" name="payment_filter_type" value="{{ $paymentFilter['type'] }}">
                                <input type="hidden" name="payment_filter_date" value="{{ $paymentFilter['date'] }}">
                                <input type="hidden" name="payment_filter_month" value="{{ $paymentFilter['month'] }}">
                                
                                <div class="mb-2">
                                    <label class="form-label text-muted fw-600 mb-1" style="font-size: .75rem;">Rentang Waktu</label>
                                    <select name="peak_filter_type" class="form-select form-select-sm filter-type-select" style="border-radius: 8px; font-size: .8rem;">
                                        <option value="all" {{ $peakFilter['type'] === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                                        <option value="today" {{ $peakFilter['type'] === 'today' ? 'selected' : '' }}>Hari Ini</option>
                                        <option value="this_month" {{ $peakFilter['type'] === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                                        <option value="custom_date" {{ $peakFilter['type'] === 'custom_date' ? 'selected' : '' }}>Pilih Tanggal</option>
                                        <option value="custom_month" {{ $peakFilter['type'] === 'custom_month' ? 'selected' : '' }}>Pilih Bulan</option>
                                    </select>
                                </div>
                                
                                <div class="mb-2 date-input-container" style="display: {{ $peakFilter['type'] === 'custom_date' ? 'block' : 'none' }};">
                                    <label class="form-label text-muted fw-600 mb-1" style="font-size: .75rem;">Pilih Tanggal</label>
                                    <input type="date" name="peak_filter_date" value="{{ $peakFilter['date'] }}" class="form-control form-control-sm" style="border-radius: 8px; font-size: .8rem;">
                                </div>
                                
                                <div class="mb-2 month-input-container" style="display: {{ $peakFilter['type'] === 'custom_month' ? 'block' : 'none' }};">
                                    <label class="form-label text-muted fw-600 mb-1" style="font-size: .75rem;">Pilih Bulan</label>
                                    <input type="month" name="peak_filter_month" value="{{ $peakFilter['month'] }}" class="form-control form-control-sm" style="border-radius: 8px; font-size: .8rem;">
                                </div>
                                
                                <button type="submit" class="btn btn-warning text-dark btn-sm w-100 mt-2 d-flex align-items-center justify-content-center gap-1" style="border-radius: 8px; font-weight: 600; font-size: .8rem;">
                                    Terapkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div style="position: relative; height: 280px;">
                    <canvas id="peakHoursChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Grafik Metode Pembayaran --}}
        <div class="col-lg-5">
            <div class="dash-card p-3 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="fw-bold mb-1 text-dark"><i class="bi bi-wallet2 me-2 text-purple" style="color: #7c3aed;"></i>Metode Pembayaran</h6>
                        <div class="text-muted small" style="font-size: .75rem;">
                            Dihitung per: <strong class="text-purple" style="color: #7c3aed;">{{ $paymentFilter['label'] }}</strong> (Transaksi Diverifikasi)
                        </div>
                    </div>
                    
                    <!-- Dropdown Filter Metode Pembayaran -->
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center gap-1" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside" style="border-radius: 8px; font-size: .75rem;">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <div class="dropdown-menu p-3 dropdown-menu-end" style="width: 260px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); border: 1px solid #e2e8f0; border-radius: 12px;">
                            <form action="{{ route('admin.dashboard') }}" method="GET">
                                <!-- Preserve other filters -->
                                <input type="hidden" name="court_filter_type" value="{{ $courtFilter['type'] }}">
                                <input type="hidden" name="court_filter_date" value="{{ $courtFilter['date'] }}">
                                <input type="hidden" name="court_filter_month" value="{{ $courtFilter['month'] }}">
                                <input type="hidden" name="peak_filter_type" value="{{ $peakFilter['type'] }}">
                                <input type="hidden" name="peak_filter_date" value="{{ $peakFilter['date'] }}">
                                <input type="hidden" name="peak_filter_month" value="{{ $peakFilter['month'] }}">
                                
                                <div class="mb-2">
                                    <label class="form-label text-muted fw-600 mb-1" style="font-size: .75rem;">Rentang Waktu</label>
                                    <select name="payment_filter_type" class="form-select form-select-sm filter-type-select" style="border-radius: 8px; font-size: .8rem;">
                                        <option value="all" {{ $paymentFilter['type'] === 'all' ? 'selected' : '' }}>Semua Waktu</option>
                                        <option value="today" {{ $paymentFilter['type'] === 'today' ? 'selected' : '' }}>Hari Ini</option>
                                        <option value="this_month" {{ $paymentFilter['type'] === 'this_month' ? 'selected' : '' }}>Bulan Ini</option>
                                        <option value="custom_date" {{ $paymentFilter['type'] === 'custom_date' ? 'selected' : '' }}>Pilih Tanggal</option>
                                        <option value="custom_month" {{ $paymentFilter['type'] === 'custom_month' ? 'selected' : '' }}>Pilih Bulan</option>
                                    </select>
                                </div>
                                
                                <div class="mb-2 date-input-container" style="display: {{ $paymentFilter['type'] === 'custom_date' ? 'block' : 'none' }};">
                                    <label class="form-label text-muted fw-600 mb-1" style="font-size: .75rem;">Pilih Tanggal</label>
                                    <input type="date" name="payment_filter_date" value="{{ $paymentFilter['date'] }}" class="form-control form-control-sm" style="border-radius: 8px; font-size: .8rem;">
                                </div>
                                
                                <div class="mb-2 month-input-container" style="display: {{ $paymentFilter['type'] === 'custom_month' ? 'block' : 'none' }};">
                                    <label class="form-label text-muted fw-600 mb-1" style="font-size: .75rem;">Pilih Bulan</label>
                                    <input type="month" name="payment_filter_month" value="{{ $paymentFilter['month'] }}" class="form-control form-control-sm" style="border-radius: 8px; font-size: .8rem;">
                                </div>
                                
                                <button type="submit" class="btn text-white btn-sm w-100 mt-2 d-flex align-items-center justify-content-center gap-1" style="background-color:#7c3aed; border-radius: 8px; font-weight: 600; font-size: .8rem;">
                                    Terapkan
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div style="position: relative; height: 280px;" class="d-flex align-items-center justify-content-center">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Baris: Booking Terbaru + Pembayaran Pending ── --}}
    <div class="row g-3">

        {{-- Booking Terbaru --}}
        <div class="col-lg-7">
            <div class="dash-card">
                <div class="dash-card-header">
                    <h6><i class="bi bi-receipt-cutoff me-2 text-primary"></i>Booking Terbaru</h6>
                    <a href="{{ route('admin.booking.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="font-size:.85rem;">
                        <thead style="background:#f8fafc;">
                            <tr>
                                <th class="ps-3 py-2 border-0 text-muted fw-600" style="font-size:.75rem;">PELANGGAN</th>
                                <th class="py-2 border-0 text-muted fw-600" style="font-size:.75rem;">LAPANGAN</th>
                                <th class="py-2 border-0 text-muted fw-600" style="font-size:.75rem;">JADWAL</th>
                                <th class="py-2 border-0 text-muted fw-600" style="font-size:.75rem;">STATUS</th>
                                <th class="py-2 border-0 text-muted fw-600 text-end pe-3" style="font-size:.75rem;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookingTerbaru as $b)
                            @php
                                $nama   = $b->is_offline
                                    ? ($b->nama_pemesan_offline ?? 'Offline')
                                    : ($b->user?->name ?? '-');
                                $initials = strtoupper(substr($nama, 0, 1));
                                $colors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899'];
                                $color  = $colors[crc32($nama) % count($colors)];
                            @endphp
                            <tr>
                                <td class="ps-3 py-2 align-middle">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="av" style="background:{{ $color }};">{{ $initials }}</div>
                                        <div>
                                            <div class="fw-600" style="font-size:.83rem;">
                                                {{ $nama }}
                                                @if($b->is_offline)
                                                    <span class="badge bg-secondary ms-1" style="font-size:.62rem;">Offline</span>
                                                @endif
                                            </div>
                                            <div class="text-muted" style="font-size:.72rem;">
                                                {{ $b->user?->email ?? ($b->no_hp_offline ?? '') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2 align-middle fw-600">{{ $b->lapangan->nama_lapangan ?? '-' }}</td>
                                <td class="py-2 align-middle">
                                    <div style="font-size:.82rem;">
                                        {{ $b->jadwal ? $b->jadwal->tanggal->format('d M Y') : '-' }}
                                    </div>
                                    <div class="text-muted" style="font-size:.72rem;">
                                        {{ $b->jadwal ? $b->jadwal->jam_mulai.' – '.$b->jadwal->jam_selesai : '' }}
                                    </div>
                                </td>
                                <td class="py-2 align-middle">
                                    @php
                                        $bClass = [
                                            'pending'      => 'b-pending',
                                            'dikonfirmasi' => 'b-dikonfirmasi',
                                            'dipesan'      => 'b-dipesan',
                                            'selesai'      => 'b-selesai',
                                            'dibatalkan'   => 'b-dibatalkan',
                                        ][$b->status] ?? 'b-pending';
                                    @endphp
                                    <span class="badge {{ $bClass }} rounded-pill px-2 py-1" style="font-size:.72rem;">
                                        {{ ucfirst($b->status) }}
                                    </span>
                                </td>
                                <td class="py-2 align-middle text-end pe-3">
                                    <a href="{{ route('admin.booking.show', $b->id) }}" class="btn btn-xs btn-outline-info rounded-pill px-2" style="font-size:0.7rem;">
                                        <i class="bi bi-eye-fill"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
                                    Belum ada booking
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Pembayaran Menunggu Verifikasi --}}
        <div class="col-lg-5">
            <div class="dash-card">
                <div class="dash-card-header">
                    <h6>
                        <i class="bi bi-clock-history me-2 text-warning"></i>Perlu Diverifikasi
                        @if($pendingVerif > 0)
                            <span class="badge bg-danger rounded-pill ms-1" style="font-size:.65rem;">{{ $pendingVerif }}</span>
                        @endif
                    </h6>
                    <a href="{{ route('admin.pembayaran.index') }}" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                        Lihat Semua
                    </a>
                </div>
                <div class="p-3">
                    @forelse($pembayaranPending as $p)
                    @php
                        $namaP  = $p->booking?->user?->name ?? ($p->booking?->nama_pemesan_offline ?? 'Offline');
                        $initP  = strtoupper(substr($namaP, 0, 1));
                        $colorsP = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899'];
                        $colorP = $colorsP[crc32($namaP) % count($colorsP)];
                        $agoP   = $p->created_at->diffForHumans();
                    @endphp
                    <div class="d-flex align-items-center gap-3 p-2 rounded-3 mb-2"
                         style="background:#f8fafc; transition:background .15s;"
                         onmouseover="this.style.background='#f0f9ff'"
                         onmouseout="this.style.background='#f8fafc'">
                        <div class="av" style="background:{{ $colorP }};">{{ $initP }}</div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="fw-bold text-truncate" style="font-size:.83rem;">{{ $namaP }}</div>
                            <div class="text-muted text-truncate" style="font-size:.73rem;">
                                {{ $p->booking->lapangan->nama_lapangan ?? '-' }}
                            </div>
                            <div class="text-muted" style="font-size:.68rem;">
                                <i class="bi bi-clock me-1"></i>{{ $agoP }}
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0 d-flex flex-column align-items-end gap-1">
                            <div class="fw-bold text-success" style="font-size:.85rem;">
                                Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                            </div>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.booking.show', $p->booking_id) }}"
                                   class="btn btn-xs btn-outline-info rounded-pill px-2 py-0"
                                   style="font-size:.65rem; line-height: 1.8;">
                                   <i class="bi bi-eye-fill"></i> Detail
                                </a>
                                <a href="{{ route('admin.pembayaran.index') }}"
                                   class="btn btn-xs btn-warning text-dark rounded-pill px-2 py-0"
                                   style="font-size:.65rem; line-height: 1.8;">
                                   <i class="bi bi-shield-check me-1"></i>Verifikasi
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-check-circle-fill text-success fs-2 d-block mb-2"></i>
                        <div style="font-size:.85rem;">Semua pembayaran sudah diverifikasi</div>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Parsing data dari backend Laravel
    const revenueData = @json($chartRevenue);
    const courtData = @json($courtOccupancy);
    const peakData = @json($chartPeakHours);
    const paymentData = @json($paymentMethods);

    // ── 1. GRAFIK TREN PENDAPATAN & BOOKING (Line + Bar Chart) ──
    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    const gradientRevenue = ctxRevenue.createLinearGradient(0, 0, 0, 300);
    gradientRevenue.addColorStop(0, 'rgba(37, 99, 235, 0.25)');
    gradientRevenue.addColorStop(1, 'rgba(37, 99, 235, 0.0)');

    new Chart(ctxRevenue, {
        data: {
            labels: revenueData.map(item => item.bulan),
            datasets: [
                {
                    label: 'Pendapatan (Rp)',
                    data: revenueData.map(item => item.total_pendapatan),
                    type: 'line',
                    borderColor: '#2563eb',
                    borderWidth: 3,
                    backgroundColor: gradientRevenue,
                    fill: true,
                    tension: 0.35,
                    yAxisID: 'y-revenue',
                    order: 1
                },
                {
                    label: 'Total Booking',
                    data: revenueData.map(item => item.total_booking),
                    type: 'bar',
                    backgroundColor: 'rgba(99, 102, 241, 0.55)',
                    hoverBackgroundColor: 'rgba(99, 102, 241, 0.75)',
                    borderRadius: 6,
                    barThickness: 20,
                    yAxisID: 'y-booking',
                    order: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        boxWidth: 8,
                        font: { family: 'Plus Jakarta Sans', size: 11, weight: 600 }
                    }
                },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: 700 },
                    bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.datasetIndex === 0) {
                                label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            } else {
                                label += context.raw + ' Booking';
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                'y-revenue': {
                    type: 'linear',
                    position: 'left',
                    grid: { drawBorder: false, color: '#f1f5f9' },
                    ticks: {
                        font: { family: 'Plus Jakarta Sans', size: 10 },
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                        }
                    }
                },
                'y-booking': {
                    type: 'linear',
                    position: 'right',
                    grid: { drawOnChartArea: false, drawBorder: false },
                    ticks: {
                        font: { family: 'Plus Jakarta Sans', size: 10 },
                        stepSize: 1
                    }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 10 } }
                }
            }
        }
    });

    // ── 2. GRAFIK OKUPANSI LAPANGAN (Doughnut Chart) ──
    const ctxCourt = document.getElementById('courtChart');
    if (courtData.length > 0) {
        new Chart(ctxCourt, {
            type: 'doughnut',
            data: {
                labels: courtData.map(item => item.nama_lapangan),
                datasets: [{
                    data: courtData.map(item => item.total_booking),
                    backgroundColor: ['#2563eb', '#10b981', '#8b5cf6', '#ec4899', '#f59e0b', '#06b6d4'],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            font: { family: 'Plus Jakarta Sans', size: 10, weight: 600 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.raw + ' Booking';
                            }
                        }
                    }
                }
            }
        });
    } else {
        ctxCourt.parentElement.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="bi bi-pie-chart fs-1 d-block mb-2 opacity-25"></i>
                <small>Belum ada data okupansi lapangan</small>
            </div>
        `;
    }

    // ── 3. GRAFIK JAM SIBUK BOOKING (Bar Chart) ──
    new Chart(document.getElementById('peakHoursChart'), {
        type: 'bar',
        data: {
            labels: peakData.map(item => item.jam),
            datasets: [{
                label: 'Total Sewa',
                data: peakData.map(item => item.total_booking),
                backgroundColor: 'rgba(139, 92, 246, 0.7)',
                hoverBackgroundColor: 'rgba(139, 92, 246, 0.9)',
                borderRadius: 5,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 10,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return ' Jam ' + context.label + ': ' + context.raw + ' Booking';
                        }
                    }
                }
            },
            scales: {
                y: {
                    grid: { drawBorder: false, color: '#f1f5f9' },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 10 }, stepSize: 1 }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { font: { family: 'Plus Jakarta Sans', size: 10 } }
                }
            }
        }
    });

    // ── 4. GRAFIK METODE PEMBAYARAN (Pie Chart) ──
    const ctxPayment = document.getElementById('paymentMethodChart');
    if (paymentData.length > 0) {
        new Chart(ctxPayment, {
            type: 'pie',
            data: {
                labels: paymentData.map(item => item.metode),
                datasets: [{
                    data: paymentData.map(item => item.total),
                    backgroundColor: ['#10b981', '#f59e0b', '#3b82f6'],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            font: { family: 'Plus Jakarta Sans', size: 10, weight: 600 }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.label + ': ' + context.raw + ' Transaksi';
                            }
                        }
                    }
                }
            }
        });
    } else {
        ctxPayment.parentElement.innerHTML = `
            <div class="text-center text-muted py-5">
                <i class="bi bi-wallet2 fs-1 d-block mb-2 opacity-25"></i>
                <small>Belum ada data transaksi diverifikasi</small>
            </div>
        `;
    }

    // Toggle input custom date/month di dropdown filter
    document.querySelectorAll('.filter-type-select').forEach(select => {
        select.addEventListener('change', function() {
            const form = this.closest('form');
            const dateContainer = form.querySelector('.date-input-container');
            const monthContainer = form.querySelector('.month-input-container');
            
            if (this.value === 'custom_date') {
                dateContainer.style.display = 'block';
                monthContainer.style.display = 'none';
            } else if (this.value === 'custom_month') {
                dateContainer.style.display = 'none';
                monthContainer.style.display = 'block';
            } else {
                dateContainer.style.display = 'none';
                monthContainer.style.display = 'none';
            }
        });
    });
});
</script>
@endpush
@endsection
