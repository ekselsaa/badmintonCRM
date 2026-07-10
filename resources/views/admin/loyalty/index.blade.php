@extends('layouts.app')

@section('title', 'CRM - Loyalty Points')
@section('page_title', 'CRM - Loyalty & Rewards')

@section('content')
<style>
    /* ── Enhanced Stat Cards ── */
    .loyalty-stat {
        background: #fff;
        border-radius: 16px;
        padding: 1.5rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: transform .25s ease, box-shadow .25s ease;
        position: relative;
        overflow: hidden;
        height: 100%;
    }
    .loyalty-stat:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.03);
    }
    .loyalty-stat::before {
        content: '';
        position: absolute;
        left: 0; top: 0; bottom: 0;
        width: 5px;
        border-radius: 5px 0 0 5px;
    }
    .loyalty-stat.blue::before   { background: #3b82f6; }
    .loyalty-stat.indigo::before { background: #6366f1; }
    .loyalty-stat.emerald::before{ background: #10b981; }
    .loyalty-stat.violet::before { background: #8b5cf6; }

    .loyalty-stat .stat-num {
        font-size: 2.2rem;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -1px;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }
    .loyalty-stat .stat-sub {
        font-size: .82rem;
        color: #64748b;
        font-weight: 500;
    }
    .loyalty-stat .stat-icon-wrap {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem; flex-shrink: 0;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);
    }

    /* ── Dashboard Cards ── */
    .loyalty-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }
    .loyalty-card-header {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1.25rem 1.5rem;
        background: #fafafb;
        border-bottom: 1px solid #f1f5f9;
    }
    .loyalty-card-header h5 { font-size: 1.05rem; font-weight: 750; margin: 0; color: #1e293b; }

    /* ── Segment Grid ── */
    .segment-badge-row {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }
    .segment-card {
        flex: 1;
        min-width: 140px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.85rem 1rem;
        text-align: center;
        transition: all 0.2s;
        text-decoration: none;
        color: inherit;
    }
    .segment-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .segment-card.visitor { border-top: 4px solid #64748b; background: #f8fafc; }
    .segment-card.ally { border-top: 4px solid #0ea5e9; background: #f0f9ff; }
    .segment-card.partner { border-top: 4px solid #10b981; background: #f0fdf4; }
    .segment-card.loyalist { border-top: 4px solid #f59e0b; background: #fffbeb; }
    .segment-card.vip { border-top: 4px solid #ef4444; background: #fff5f5; }

    .segment-card .count {
        font-size: 1.4rem;
        font-weight: 800;
        margin: 0.2rem 0;
    }
    .segment-card .label {
        font-size: 0.72rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #64748b;
    }

    /* ── Custom forms style ── */
    .form-box {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 1.5rem;
        height: 100%;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .form-box-title {
        font-size: 1.05rem;
        font-weight: 750;
        margin-bottom: 1.25rem;
        color: #0f172a;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>

<div>
    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-3 rounded-3 py-3 px-4 mb-4 shadow-sm border-0">
            <i class="bi bi-check-circle-fill fs-4 flex-shrink-0"></i> 
            <div>{!! session('success') !!}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger d-flex align-items-center gap-3 rounded-3 py-3 px-4 mb-4 shadow-sm border-0">
            <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0"></i> 
            <div>{!! session('error') !!}</div>
        </div>
    @endif

    {{-- ── Baris 1: Stat Cards ── --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <div class="loyalty-stat blue">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrap" style="background:#e0f2fe;">
                        <i class="bi bi-star-fill text-primary"></i>
                    </div>
                </div>
                <div class="stat-num">{{ number_format($stats['total_poin_beredar'], 0, ',', '.') }}</div>
                <div class="stat-sub">Total Saldo Poin Pelanggan</div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="loyalty-stat indigo">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrap" style="background:#e0e7ff;">
                        <i class="bi bi-ticket-perforated-fill text-indigo" style="color:#6366f1;"></i>
                    </div>
                </div>
                <div class="stat-num">{{ number_format($stats['total_redemption'], 0, ',', '.') }}</div>
                <div class="stat-sub">Total Voucher Diterbitkan (Poin & Level)</div>
            </div>
        </div>

        <div class="col-12 col-md-4">
            <div class="loyalty-stat emerald">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrap" style="background:#d1fae5;">
                        <i class="bi bi-check2-circle text-emerald" style="color:#10b981;"></i>
                    </div>
                </div>
                <div class="stat-num">{{ number_format($stats['voucher_aktif'], 0, ',', '.') }}</div>
                <div class="stat-sub">Voucher Aktif (Belum Digunakan/Kadaluwarsa)</div>
            </div>
        </div>
    </div>

    {{-- ── Baris 2: Segmentasi Roadmap ── --}}
    <div class="loyalty-card p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart-steps me-2 text-primary"></i>Distribusi Level Segmentasi CRM</h5>
            <small class="text-muted">Diperbarui berdasarkan akumulasi poin bulanan berjalan</small>
        </div>
        <div class="row g-3">
            <div class="col">
                <a href="{{ route('admin.loyalty.index', ['segmen' => 'visitor']) }}" class="segment-card visitor d-block text-decoration-none">
                    <div class="label text-secondary">👤 Visitor</div>
                    <div class="count text-secondary">{{ $stats['segmen']['visitor'] }}</div>
                    <div style="font-size:10px;" class="text-muted">0 - 29 Poin</div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('admin.loyalty.index', ['segmen' => 'ally']) }}" class="segment-card ally d-block text-decoration-none">
                    <div class="label text-info">🤝 Ally</div>
                    <div class="count text-info">{{ $stats['segmen']['ally'] }}</div>
                    <div style="font-size:10px;" class="text-muted">30 - 79 Poin</div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('admin.loyalty.index', ['segmen' => 'partner']) }}" class="segment-card partner d-block text-decoration-none">
                    <div class="label text-success">🏸 Partner</div>
                    <div class="count text-success">{{ $stats['segmen']['partner'] }}</div>
                    <div style="font-size:10px;" class="text-muted">80 - 149 Poin</div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('admin.loyalty.index', ['segmen' => 'loyalist']) }}" class="segment-card loyalist d-block text-decoration-none">
                    <div class="label text-warning">👑 Loyalist</div>
                    <div class="count text-warning">{{ $stats['segmen']['loyalist'] }}</div>
                    <div style="font-size:10px;" class="text-muted">150 - 250 Poin</div>
                </a>
            </div>
            <div class="col">
                <a href="{{ route('admin.loyalty.index', ['segmen' => 'vip']) }}" class="segment-card vip d-block text-decoration-none">
                    <div class="label text-danger">💎 VIP</div>
                    <div class="count text-danger">{{ $stats['segmen']['vip'] }}</div>
                    <div style="font-size:10px;" class="text-muted">> 250 Poin</div>
                </a>
            </div>
        </div>
        @if(request('segmen'))
            <div class="mt-3 text-end">
                <a href="{{ route('admin.loyalty.index') }}" class="btn btn-xs btn-outline-secondary rounded-pill">
                    <i class="bi bi-x-circle me-1"></i>Hapus Filter Kategori: {{ ucfirst(request('segmen')) }}
                </a>
            </div>
        @endif
    </div>

    {{-- ── Baris 3: Formulir Kasir & Kredit Manual ── --}}
    <div class="row g-4 mb-4">
        {{-- Klaim Voucher di Kasir --}}
        <div class="col-12 col-lg-6">
            <div class="form-box">
                <div class="form-box-title">
                    <i class="bi bi-ticket-detailed text-primary fs-5"></i>
                    <span>Verifikasi & Klaim Voucher Kasir</span>
                </div>
                <form action="{{ route('admin.loyalty.klaim-voucher') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="kode_voucher" class="form-label fw-600 small text-secondary">Kode Voucher</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-qr-code text-muted"></i></span>
                            <input type="text" name="kode_voucher" id="kode_voucher" 
                                   class="form-control border-start-0 ps-0 @error('kode_voucher') is-invalid @enderror" 
                                   placeholder="Masukkan kode voucher atau 8 karakter pertama..."
                                   value="{{ old('kode_voucher') }}" required>
                        </div>
                        <small class="text-muted fs-7 mt-1 d-block">
                            Mendukung voucher penukaran poin (UUID) maupun voucher segmentasi (Ally, Partner, Loyalist, VIP).
                        </small>
                        @error('kode_voucher')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Proteksi VIP --}}
                    <div class="p-3 bg-light rounded-3 border mb-3" style="border-left: 4px solid #ef4444 !important;">
                        <div class="form-check d-flex align-items-start gap-2">
                            <input class="form-check-input mt-1" type="checkbox" name="is_member_renewal" id="is_member_renewal" value="1">
                            <label class="form-check-label small" for="is_member_renewal">
                                <strong class="text-danger d-block mb-0.5"><i class="bi bi-shield-lock-fill me-1"></i>Proteksi Verifikasi VIP</strong>
                                <span>Centang ini jika voucher yang diklaim adalah <b>VIP (Diskon Rp 100.000)</b> dan pelanggan telah mendaftar/memperpanjang member minimal 1 bulan.</span>
                            </label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold rounded-3">
                        <i class="bi bi-check2-circle me-1"></i>Verifikasi & Klaim Voucher
                    </button>
                </form>
            </div>
        </div>

        {{-- Kredit Poin Member Manual --}}
        <div class="col-12 col-lg-6">
            <div class="form-box">
                <div class="form-box-title">
                    <i class="bi bi-plus-circle text-success fs-5"></i>
                    <span>Kredit Poin Paket Member (Manual)</span>
                </div>
                <form action="{{ route('admin.loyalty.kredit-member') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="user_id" class="form-label fw-600 small text-secondary">Pilih Pelanggan</label>
                        @php
                            $allPelangganForSelect = \App\Models\User::where('role', 'pelanggan')->orderBy('name')->get();
                        @endphp
                        <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Pelanggan --</option>
                            @foreach($allPelangganForSelect as $usr)
                                <option value="{{ $usr->id }}" {{ old('user_id') == $usr->id ? 'selected' : '' }}>
                                    {{ $usr->name }} ({{ $usr->username ?? $usr->email }}) — Poin Saldo: {{ $usr->poin_saldo }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="jenis_paket" class="form-label fw-600 small text-secondary">Jenis Paket Member</label>
                        <select name="jenis_paket" id="jenis_paket" class="form-select @error('jenis_paket') is-invalid @enderror" required>
                            <option value="">-- Pilih Jenis Paket --</option>
                            <option value="pagi_siang" {{ old('jenis_paket') == 'pagi_siang' ? 'selected' : '' }}>
                                Paket Weekdays Pagi/Siang (+70 Poin)
                            </option>
                            <option value="malam" {{ old('jenis_paket') == 'malam' ? 'selected' : '' }}>
                                Paket Weekdays Malam (+100 Poin)
                            </option>
                            <option value="weekend" {{ old('jenis_paket') == 'weekend' ? 'selected' : '' }}>
                                Paket Weekend (+110 Poin)
                            </option>
                        </select>
                        @error('jenis_paket')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-success w-100 py-2 fw-semibold rounded-3">
                        <i class="bi bi-coin me-1"></i>Kreditkan Poin Paket Member
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ── Baris 4: Pencarian & Daftar Pelanggan ── --}}
    <div class="loyalty-card">
        <div class="loyalty-card-header d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-sm-items-center gap-2">
            <h5><i class="bi bi-people-fill me-2 text-primary"></i>Status Poin & Segmentasi Pelanggan</h5>
            <form method="GET" class="d-flex gap-2">
                @if(request('segmen'))
                    <input type="hidden" name="segmen" value="{{ request('segmen') }}">
                @endif
                <div class="input-group input-group-sm" style="width: 260px;">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama atau username..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.loyalty.index', request()->only('segmen')) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                @endif
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">PELANGGAN</th>
                        <th>KATEGORI MEMBER</th>
                        <th>LEVEL SEGMEN CRM</th>
                        <th>SALDO POIN TOTAL</th>
                        <th>POIN BULAN INI</th>
                        <th class="text-end pe-4">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pelanggan as $p)
                        <tr>
                            <td class="ps-4 py-3">
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                         style="width: 36px; height: 36px; background: #e0f2fe; color: #0284c7; font-size: 0.85rem;">
                                        {{ strtoupper(substr($p->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $p->name }}</div>
                                        <div class="text-muted small fs-7">{{ $p->username ?? $p->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($p->isMember())
                                    <span class="badge bg-success text-white rounded-pill px-2.5 py-1 text-uppercase" style="font-size: 0.68rem; font-weight: 700; background-color: #10b981 !important;">
                                        👑 {{ str_replace('_', ' ', $p->kategori_member) }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark rounded-pill px-2.5 py-1">Umum</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $segmenLabel = [
                                        'visitor'  => '👤 Visitor',
                                        'ally'     => '🤝 Ally',
                                        'partner'  => '🏸 Partner',
                                        'loyalist' => '👑 Loyalist',
                                        'vip'      => '💎 VIP',
                                    ][$p->segmen_pelanggan] ?? '👤 Visitor';
                                    
                                    $segmenBadge = [
                                        'visitor'  => 'bg-secondary',
                                        'ally'     => 'bg-info text-dark',
                                        'partner'  => 'bg-success',
                                        'loyalist' => 'bg-warning text-dark',
                                        'vip'      => 'bg-danger',
                                    ][$p->segmen_pelanggan] ?? 'bg-secondary';
                                @endphp
                                <span class="badge {{ $segmenBadge }} rounded-pill px-2.5 py-1 fw-semibold">
                                    {{ $segmenLabel }}
                                </span>
                            </td>
                            <td>
                                <strong class="text-primary"><i class="bi bi-star-fill text-warning me-1"></i>{{ number_format($p->poin_saldo, 0, ',', '.') }}</strong>
                            </td>
                            <td>
                                <span class="fw-bold text-secondary">{{ number_format($p->poin_bulanan, 0, ',', '.') }} Poin</span>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('admin.crm.pelanggan.detail', $p->id) }}" 
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1">
                                    <i class="bi bi-eye me-1"></i>Detail CRM
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i>
                                Tidak ada data pelanggan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pelanggan->hasPages())
            <div class="card-footer bg-white border-top-0 p-3">
                {{ $pelanggan->appends(request()->all())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
