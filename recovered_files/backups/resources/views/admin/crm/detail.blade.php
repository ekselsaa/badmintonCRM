@extends('layouts.app')
@section('title', 'Detail Pelanggan - ' . $pelanggan->name)
@section('page_title', 'Detail Pelanggan')
@section('page_subtitle', 'Riwayat lengkap: ' . $pelanggan->name)
@section('topbar_actions')
    <a href="{{ route('admin.crm.pelanggan') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
@endsection

@section('content')
<div class="p-0">
    <div class="row g-4 mb-4">
        {{-- Profil Pelanggan --}}
        <div class="col-lg-4">
            <div class="table-card p-4 p-md-5 text-center position-relative h-100">
                @if($pelanggan->isMember())
                    <span class="position-absolute top-0 end-0 m-3 badge text-uppercase" style="background:#fef3c7; color:#d97706; border:1px solid #fde68a; font-size: 0.72rem; font-weight: 700;">
                        <i class="bi bi-star-fill me-1"></i>Member ({{ str_replace('_', ' ', $pelanggan->kategori_member) }})
                    </span>
                @else
                    <span class="position-absolute top-0 end-0 m-3 badge bg-light text-secondary border">
                        Non-Member
                    </span>
                @endif

                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 mt-2"
                     style="width:72px;height:72px;background:linear-gradient(135deg,#1a56db,#0ea5e9);color:#fff;font-size:1.8rem;font-weight:700">
                    {{ strtoupper(substr($pelanggan->name, 0, 1)) }}
                </div>
                <h5 class="fw-bold mb-1 text-dark">{{ $pelanggan->name }}</h5>
                <p class="text-muted mb-2" style="font-size:.875rem">{{ $pelanggan->email }}</p>
                
                @php
                    $segmenBadge = [
                        'visitor'  => 'bg-secondary',
                        'ally'     => 'bg-info text-dark',
                        'partner'  => 'bg-success',
                        'loyalist' => 'bg-warning text-dark',
                        'vip'      => 'bg-danger',
                    ][$pelanggan->segmen_pelanggan] ?? 'bg-secondary';
                @endphp
                <div class="mb-3">
                    <span class="badge {{ $segmenBadge }} rounded-pill px-3 py-1 fw-bold">
                        {{ $pelanggan->label_segmen }}
                    </span>
                </div>

                {{-- Interactive Loyalty Point Widget --}}
                <div class="d-flex align-items-center justify-content-center gap-3 mb-3 p-2.5 rounded-3 border" style="background-color: #fffbeb !important; border-color: #fde68a !important; box-shadow: 0 4px 12px rgba(245,158,11,0.06);">
                    <div class="d-flex align-items-center justify-content-center rounded-circle text-warning flex-shrink-0" style="width:40px; height:40px; background:#fef3c7; font-size: 1.25rem; border: 1px solid #fde68a;">
                        <i class="bi bi-gift-fill text-warning"></i>
                    </div>
                    <div class="text-start">
                        <small class="text-muted d-block fw-bold text-uppercase" style="font-size:0.6rem; letter-spacing:0.05em;">Saldo Loyalty Points</small>
                        <span class="fw-bold text-dark" style="font-size:1.25rem;">{{ number_format($pelanggan->poin_saldo ?? 0, 0, ',', '.') }} <span class="text-secondary fw-semibold" style="font-size: 0.8rem;">Poin</span></span>
                    </div>
                </div>
                <div class="px-2 d-flex flex-column gap-2 mb-3">
                    <form action="{{ route('admin.crm.pelanggan.toggle-member', $pelanggan->id) }}" method="POST" class="m-0">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-sm w-100 rounded-pill {{ $pelanggan->isMember() ? 'btn-outline-secondary' : 'btn-warning fw-bold text-dark' }}" 
                                onclick="return confirm('Ubah status member pelanggan ini?')">
                            <i class="bi {{ $pelanggan->isMember() ? 'bi-star' : 'bi-star-fill' }} me-1"></i>
                            {{ $pelanggan->isMember() ? 'Batalkan Member' : 'Jadikan Member' }}
                        </button>
                    </form>
                </div>
                <hr>
                <div class="text-start">
                    @if($pelanggan->nomor_hp)
                    <div class="mb-2">
                        <small class="text-muted d-block">Nomor HP</small>
                        <span class="fw-500"><i class="bi bi-telephone me-1"></i>{{ $pelanggan->nomor_hp }}</span>
                    </div>
                    @endif
                    @if($pelanggan->alamat)
                    <div class="mb-2">
                        <small class="text-muted d-block">Alamat</small>
                        <span class="fw-500" style="font-size:.875rem"><i class="bi bi-geo-alt me-1"></i>{{ $pelanggan->alamat }}</span>
                    </div>
                    @endif
                    <div>
                        <small class="text-muted d-block">Bergabung</small>
                        <span class="fw-500"><i class="bi bi-calendar me-1"></i>{{ $pelanggan->created_at->format('d M Y') }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Statistik & Tab Detail --}}
        <div class="col-lg-8">
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto mb-2" style="background:#dbeafe">
                            <i class="bi bi-receipt-cutoff" style="color:#1d4ed8"></i>
                        </div>
                        <h3 class="fw-bold mb-0">{{ $stats['total_booking'] }}</h3>
                        <small class="text-muted">Total Booking</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto mb-2" style="background:#d1fae5">
                            <i class="bi bi-cash-stack" style="color:#059669"></i>
                        </div>
                        <h4 class="fw-bold mb-0" style="font-size:1.1rem">Rp {{ number_format($stats['total_bayar'], 0, ',', '.') }}</h4>
                        <small class="text-muted">Total Transaksi</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card text-center">
                        <div class="stat-icon mx-auto mb-2" style="background:#fef3c7">
                            <i class="bi bi-check-circle-fill" style="color:#d97706"></i>
                        </div>
                        <h3 class="fw-bold mb-0">{{ $stats['booking_selesai'] }}</h3>
                        <small class="text-muted">Booking Selesai</small>
                    </div>
                </div>
            </div>

            {{-- Navigation Tabs --}}
            <ul class="nav nav-tabs mb-0 gap-2 border-bottom-0" id="crmDetailTabs" role="tablist" style="font-size: 0.85rem;">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold px-3 py-2.5 text-secondary border rounded-top-3" id="booking-tab" data-bs-toggle="tab" data-bs-target="#booking-pane" type="button" role="tab" aria-controls="booking-pane" aria-selected="true" style="margin-bottom: -1px; background:#f8fafc;">
                        <i class="bi bi-clock-history me-1.5 text-primary"></i>Riwayat Booking
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-3 py-2.5 text-secondary border rounded-top-3" id="points-tab" data-bs-toggle="tab" data-bs-target="#points-pane" type="button" role="tab" aria-controls="points-pane" aria-selected="false" style="margin-bottom: -1px; background:#f8fafc;">
                        <i class="bi bi-star-fill me-1.5 text-warning"></i>Riwayat Loyalty Poin
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold px-3 py-2.5 text-secondary border rounded-top-3" id="vouchers-tab" data-bs-toggle="tab" data-bs-target="#vouchers-pane" type="button" role="tab" aria-controls="vouchers-pane" aria-selected="false" style="margin-bottom: -1px; background:#f8fafc;">
                        <i class="bi bi-ticket-perforated-fill me-1.5 text-danger"></i>Voucher Terklaim
                    </button>
                </li>
            </ul>

            {{-- Tab Content --}}
            <div class="tab-content shadow-sm rounded-bottom-4 border" id="crmDetailTabsContent" style="background: #fff; border-top: none !important; border-radius: 0 0 16px 16px; overflow:hidden;">
                
                {{-- Tab 1: Booking --}}
                <div class="tab-pane fade show active" id="booking-pane" role="tabpanel" aria-labelledby="booking-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr><th>Tanggal</th><th>Lapangan</th><th>Jam</th><th>Total</th><th>Status</th><th>Bayar</th><th class="text-end pe-3">Aksi</th></tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $b)
                                <tr>
                                    <td><small class="fw-500">{{ $b->jadwal ? $b->jadwal->tanggal->format('d/m/Y') : '-' }}</small></td>
                                    <td><small class="fw-600 text-dark">{{ $b->lapangan->nama_lapangan }}</small></td>
                                    <td><small>{{ $b->jadwal ? substr($b->jadwal->jam_mulai, 0, 5) . ' - ' . substr($b->jadwal->jam_selesai, 0, 5) : '-' }}</small></td>
                                    <td><small class="fw-bold text-success">Rp {{ number_format($b->total_harga, 0, ',', '.') }}</small></td>
                                    <td>
                                        <span class="badge badge-{{ $b->status }} px-2 py-1 rounded-pill" style="font-size:.7rem">{{ ucfirst($b->status) }}</span>
                                    </td>
                                    <td>
                                        @if($b->pembayaran)
                                            <span class="badge badge-{{ $b->pembayaran->status_verifikasi }}" style="font-size:.7rem">{{ ucfirst($b->pembayaran->status_verifikasi) }}</span>
                                        @else
                                            <span class="text-muted" style="font-size:.75rem">-</span>
                                        @endif
                                    </td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('admin.booking.show', $b->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-2.5 py-1" style="font-size:0.7rem; font-weight:600;" title="Detail Booking">
                                            <i class="bi bi-eye-fill me-1"></i>Detail
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada booking.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($bookings->hasPages())
                    <div class="p-3 border-top bg-light">{{ $bookings->links() }}</div>
                    @endif
                </div>

                {{-- Tab 2: Poin --}}
                <div class="tab-pane fade" id="points-pane" role="tabpanel" aria-labelledby="points-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal & Waktu</th>
                                    <th>Tipe</th>
                                    <th>Poin</th>
                                    <th>Sumber</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pointHistories as $ph)
                                <tr>
                                    <td><small class="text-muted">{{ $ph->created_at->format('d/m/Y H:i') }}</small></td>
                                    <td>
                                        <span class="badge" style="font-size: 0.72rem; padding: 4px 10px; border-radius: 30px; {{ $ph->tipe === 'kredit' ? 'background-color: rgba(16, 185, 129, 0.1); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.2);' : 'background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2);' }}">
                                            {{ $ph->label_tipe }}
                                        </span>
                                    </td>
                                    <td class="fw-bold {{ $ph->tipe === 'kredit' ? 'text-success' : 'text-danger' }}">
                                        {{ $ph->poin_formatted }} Poin
                                    </td>
                                    <td>
                                        <small class="fw-600 text-dark">{{ $ph->label_sumber }}</small>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $ph->keterangan ?? '-' }}</small>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada riwayat transaksi poin.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab 3: Voucher --}}
                <div class="tab-pane fade" id="vouchers-pane" role="tabpanel" aria-labelledby="vouchers-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Kode Voucher</th>
                                    <th>Hadiah</th>
                                    <th>Poin Ditukar</th>
                                    <th>Status</th>
                                    <th>Masa Berlaku / Penggunaan</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $membershipVouchers = \App\Models\Voucher::where('user_id', $pelanggan->id)
                                        ->orderBy('created_at', 'desc')
                                        ->take(20)
                                        ->get();
                                    $semuaVoucher = $redemptions->concat($membershipVouchers)->sortByDesc('created_at');
                                @endphp
                                @forelse($semuaVoucher as $rd)
                                @php
                                    $isRedemp = isset($rd->jenis_hadiah);
                                    $exp = $isRedemp ? $rd->kode_expired_at : $rd->expired_date;
                                    $code = $isRedemp ? $rd->kode_voucher : $rd->voucher_code;
                                    $isExpired = $exp && $exp->isPast();
                                    $isAktif = $rd->status === 'aktif' && !$isExpired;
                                @endphp
                                <tr>
                                    <td>
                                        <code class="fw-bold text-primary" style="font-size: 0.82rem;">{{ $rd->kode_display }}</code>
                                    </td>
                                    <td>
                                        <span style="font-size: 1.1rem; margin-right: 4px;">{{ $rd->icon_hadiah }}</span>
                                        <small class="fw-600 text-dark">{{ $rd->label_hadiah }}</small>
                                    </td>
                                    <td class="fw-bold text-warning">
                                        @if($isRedemp)
                                            -{{ number_format($rd->poin_digunakan) }} Poin
                                        @else
                                            <span class="text-success" style="font-size:0.75rem;">Level Reward</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($isAktif)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1" style="font-size:.7rem">
                                                Aktif
                                            </span>
                                        @elseif($rd->status === 'digunakan')
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-2 py-1" style="font-size:.7rem">
                                                Sudah Dipakai
                                            </span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1" style="font-size:.7rem">
                                                Expired
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($rd->status === 'digunakan')
                                            <small class="text-muted">Dipakai pada: <br>{{ $rd->digunakan_pada?->format('d/m/Y H:i') ?? '-' }}</small>
                                        @else
                                            <small class="text-muted">Expired: <br>{{ $exp ? $exp->format('d/m/Y') : '-' }}</small>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($isAktif)
                                            @if(!$isRedemp && $rd->tipe_voucher === 'vip')
                                                <a href="{{ route('admin.loyalty.index', ['kode_voucher' => $rd->voucher_code]) }}" class="btn btn-sm btn-warning text-dark rounded-pill px-3 py-1" style="font-size: 0.72rem; font-weight: 600;">
                                                    <i class="bi bi-shield-check me-1"></i>Klaim di Kasir
                                                </a>
                                            @else
                                                <form action="{{ route('admin.loyalty.klaim-voucher') }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin memproses klaim voucher ini dan menandainya sebagai DIGUNAKAN?')">
                                                    @csrf
                                                    <input type="hidden" name="kode_voucher" value="{{ $code }}">
                                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 py-1" style="font-size: 0.72rem; font-weight: 600;">
                                                        <i class="bi bi-check-circle me-1"></i>Klaim
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <span class="text-muted small">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada voucher yang aktif atau digunakan.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    body, select, input, textarea, button, h5, h6, table, th, td, a {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
    }
    .table-card {
        border-radius: 16px !important;
        border: 1px solid rgba(226, 232, 240, 0.8) !important;
        box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05) !important;
    }
    #crmDetailTabs .nav-link {
        border-color: rgba(226, 232, 240, 0.8) !important;
        border-bottom-color: transparent !important;
        transition: all 0.25s ease;
        color: #64748b;
        border-radius: 12px 12px 0 0 !important;
    }
    #crmDetailTabs .nav-link.active {
        background: #fff !important;
        color: #0f172a !important;
        border-bottom-color: #fff !important;
        border-width: 1px 1px 0 1px !important;
        box-shadow: 0 -3px 10px rgba(0,0,0,0.02);
    }
    #crmDetailTabs .nav-link:hover:not(.active) {
        background: #f1f5f9 !important;
        color: #1e293b;
    }
</style>@endpush
@endsection
