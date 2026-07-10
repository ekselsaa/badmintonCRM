@extends('layouts.app')
@section('title', 'CRM - Data Pelanggan')
@section('page_title', 'CRM - Data Pelanggan')

@section('content')
<div class="p-0">
    {{-- Tabs Navigasi CRM & Pelanggan --}}
    <ul class="nav nav-pills gap-2 mb-4 bg-white p-2.5 rounded-3 border" style="font-size: 0.82rem; width: fit-content; max-width: 100%; overflow-x: auto; flex-wrap: nowrap;">
        <li class="nav-item">
            <a class="nav-link active px-3 py-2 fw-semibold" href="{{ route('admin.crm.pelanggan') }}">
                <i class="bi bi-people-fill me-2"></i>Data Pelanggan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 fw-semibold text-secondary" href="{{ route('admin.ulasan.index') }}">
                <i class="bi bi-star-fill me-2"></i>Ulasan Pelanggan
            </a>
        </li>
    </ul>

            {{-- Top Pelanggan --}}
            <div class="table-card p-4 p-md-5 mb-4" style="background:linear-gradient(135deg,#0f172a,#1e3a5f);color:#fff">
                <h6 class="fw-bold mb-3"><i class="bi bi-trophy-fill me-2" style="color:#f59e0b"></i>Top 5 Pelanggan Paling Aktif</h6>
                <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-5 g-4">
                    @foreach($topPelanggan as $i => $tp)
                    @php
                        $isOffline = isset($tp->is_offline) && $tp->is_offline;
                    @endphp
                    <div class="col">
                        <a href="{{ $isOffline ? 'javascript:void(0)' : route('admin.crm.pelanggan.detail', $tp->id) }}" 
                           class="text-decoration-none p-3 rounded-3 h-100 d-flex flex-column justify-content-between top-customer-card" 
                           style="background:rgba(255,255,255,.05); border: 1px solid rgba(255,255,255,.08); cursor:{{ $isOffline ? 'default' : 'pointer' }}; transition: all .25s ease;"
                           data-bs-toggle="popover" 
                           data-bs-trigger="hover focus"
                           data-bs-placement="bottom"
                           data-bs-html="true"
                           title="<i class='bi bi-person-circle me-2'></i>{{ $tp->name }}"
                           data-bs-content="
                                <div class='small py-1'>
                                    <div class='mb-2 text-muted'><i class='bi bi-person me-2'></i>{{ $tp->username }}</div>
                                    <div class='mb-2 text-muted'><i class='bi bi-telephone me-2'></i>{{ $tp->nomor_hp ?? '-' }}</div>
                                    <div class='mb-2 text-warning'><i class='bi bi-star-fill me-1'></i>Saldo Poin: <b>{{ number_format($tp->poin_saldo ?? 0) }} Poin</b></div>
                                    <div class='mb-2'><i class='bi bi-currency-dollar me-2 text-success'></i>Total Transaksi: <br><b class='text-success'>Rp {{ number_format($tp->bookings_sum_total_harga ?? 0, 0, ',', '.') }}</b></div>
                                    <hr class='my-2 opacity-10'>
                                    <div class='text-muted' style='font-size:11px'>{{ $isOffline ? 'Pelanggan Offline (Tanpa Akun)' : 'Klik untuk lihat riwayat lengkap' }}</div>
                                </div>
                             ">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-3 min-w-0">
                                    <span class="badge rounded-circle d-flex align-items-center justify-content-center fw-bold text-white flex-shrink-0"
                                          style="width:26px; height:26px; font-size: 0.8rem; background:{{ $i==0 ? 'linear-gradient(135deg, #f59e0b, #d97706)' : ($i==1 ? 'linear-gradient(135deg, #94a3b8, #64748b)' : ($i==2 ? 'linear-gradient(135deg, #cd7c2e, #b45309)' : 'linear-gradient(135deg, #475569, #334155)')) }}; box-shadow: 0 2px 4px rgba(0,0,0,0.15);">
                                        {{ $i+1 }}
                                    </span>
                                    <div class="d-flex align-items-center gap-2 min-w-0 flex-grow-1">
                                        <span class="fw-semibold text-white text-truncate" style="font-size:.9rem;" title="{{ $tp->name }}">{{ $tp->name }}</span>
                                        @if($isOffline)
                                            <span class="badge bg-secondary flex-shrink-0" style="font-size:.6rem; padding: 3px 6px; opacity: 0.85;">Offline</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="mt-auto">
                                <div class="pt-3" style="border-top: 1px solid rgba(255,255,255,0.08);">
                                    <div class="d-flex align-items-center justify-content-between mb-2" style="font-size: 0.78rem;">
                                        <span class="text-white-50 d-flex align-items-center gap-2">
                                            <i class="bi bi-calendar-check opacity-75"></i>
                                            <span>Booking</span>
                                        </span>
                                        <span class="fw-semibold text-white">{{ $tp->bookings_count }}</span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between" style="font-size: 0.78rem;">
                                        <span class="text-white-50 d-flex align-items-center gap-2">
                                            <i class="bi bi-star-fill text-warning opacity-90"></i>
                                            <span class="text-warning">Poin</span>
                                        </span>
                                        <span class="fw-bold text-warning">{{ number_format($tp->poin_saldo ?? 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Pencarian --}}
            <div class="table-card p-4 mb-4">
                <form method="GET" class="d-flex flex-column flex-sm-row gap-2">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" class="form-control"
                               placeholder="Cari nama atau nomor HP..." value="{{ request('search') }}">
                    </div>
                    <div class="d-flex gap-2 justify-content-end">
                        <button class="btn btn-primary px-4 flex-grow-1 flex-sm-grow-0">Cari</button>
                        @if(request('search'))
                            <a href="{{ route('admin.crm.pelanggan') }}" class="btn btn-outline-secondary flex-grow-1 flex-sm-grow-0 text-center">Reset</a>
                        @endif
                    </div>
                </form>
            </div>

            {{-- Tabel Pelanggan --}}
            <div class="table-card mb-4">
                <div class="table-card-header d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-people-fill me-2 text-primary"></i>Daftar Pelanggan</h6>
                    <small class="text-muted">Total: {{ $pelanggan->total() }} pelanggan ({{ $totalOnline }} Online, {{ $totalOffline }} Offline)</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle table-compact">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Nama Pelanggan</th>
                                <th>Kontak</th>
                                <th class="d-none d-xl-table-cell">Alamat</th>
                                <th>Poin Saldo</th>
                                <th>Total Booking</th>
                                <th>Total Transaksi</th>
                                <th class="d-none d-lg-table-cell">Bergabung</th>
                                <th class="text-center" style="width: 120px; min-width: 120px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pelanggan as $p)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 d-none d-md-flex"
                                             style="width:32px;height:32px;background:#dbeafe;color:#1d4ed8;font-weight:700;font-size:.8rem">
                                            {{ strtoupper(substr($p->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-600 text-dark d-flex align-items-center gap-2" style="line-height: 1.2;">
                                                {{ $p->name }}
                                                @if(isset($p->is_offline) && $p->is_offline)
                                                    <span class="badge bg-secondary" style="font-size: 0.62rem;">Offline</span>
                                                @endif
                                            </div>
                                            <small class="text-muted" style="font-size: 0.72rem;">{{ $p->username }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($p->nomor_hp)
                                        <div style="font-size: 0.8rem;" class="d-flex align-items-center gap-2 flex-wrap">
                                            <span class="text-dark"><i class="bi bi-telephone me-1 text-muted"></i>{{ $p->nomor_hp }}</span>
                                            @php
                                                $waMessage = "Halo " . $p->name . ", kami dari Admin Anbiyaa Sport ingin menghubungi Anda terkait akun pendaftaran Anda.";
                                            @endphp
                                            <a href="https://wa.me/{{ $p->nomor_hp }}?text={{ rawurlencode($waMessage) }}" target="_blank" class="badge bg-success text-white rounded px-2 py-1 ms-1 text-decoration-none d-inline-flex align-items-center gap-1 shadow-sm" style="font-size: 0.65rem;" title="Hubungi via WhatsApp">
                                                <i class="bi bi-whatsapp" style="font-size: 0.75rem;"></i> Chat
                                            </a>
                                        </div>
                                    @else
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td class="d-none d-xl-table-cell">
                                    <small class="text-muted">{{ Str::limit($p->alamat, 25) ?? '-' }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 fw-semibold text-warning" style="font-size: 0.8rem; background: #fffbeb; padding: 2px 8px; border-radius: 20px; border: 1px solid #fef3c7; width: max-content;">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <span>{{ number_format($p->poin_saldo ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-2.5 py-1"
                                          style="background:#dbeafe;color:#1d4ed8;font-weight:600;font-size: 0.72rem;">
                                        {{ $p->bookings_count }} booking
                                    </span>
                                </td>
                                <td class="fw-bold text-success" style="font-size: 0.82rem;">
                                    Rp {{ number_format($p->bookings_sum_total_harga ?? 0, 0, ',', '.') }}
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    <small class="text-muted">
                                        {{ $p->created_at instanceof \Carbon\Carbon ? $p->created_at->format('d/m/Y') : \Carbon\Carbon::parse($p->created_at)->format('d/m/Y') }}
                                    </small>
                                </td>
                                 <td class="text-center">
                                     @if(isset($p->is_offline) && $p->is_offline)
                                         <form action="{{ route('admin.crm.pelanggan.destroy-offline') }}" method="POST" class="d-inline">
                                             @csrf
                                             @method('DELETE')
                                             <input type="hidden" name="name" value="{{ $p->name }}">
                                             <input type="hidden" name="nomor_hp" value="{{ $p->nomor_hp }}">
                                             <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5 py-0.5 btn-delete-offline" style="font-size: 0.75rem;" data-nama="{{ $p->name }}">
                                                 <i class="bi bi-trash me-1"></i>Hapus
                                             </button>
                                         </form>
                                     @else
                                         <a href="{{ route('admin.crm.pelanggan.detail', $p->id) }}"
                                             class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-0.5" style="font-size: 0.75rem;">
                                              <i class="bi bi-eye me-1"></i>Detail
                                         </a>
                                     @endif
                                 </td>
                            </tr>
                            @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">Belum ada pelanggan terdaftar.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($pelanggan->hasPages())
                <div class="p-3">{{ $pelanggan->appends(request()->all())->links() }}</div>
                @endif
            </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl)
    })

    // SweetAlert delete confirmation for Offline Customer bookings
    document.querySelectorAll('.btn-delete-offline').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const nama = this.getAttribute('data-nama');
            
            Swal.fire({
                title: 'Hapus Booking Pelanggan?',
                text: `Apakah Anda yakin ingin menghapus semua data booking untuk pelanggan offline "${nama}"? Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Danger Red
                cancelButtonColor: '#6b7280', // Gray
                confirmButtonText: 'Ya, Hapus Semua!',
                cancelButtonText: 'Batal',
                background: '#fff',
                customClass: {
                    popup: 'rounded-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });
});
</script>
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

body, select, input, textarea, button, h6, table, th, td, a {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}
.table-card {
    border-radius: 16px !important;
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05) !important;
}
.top-customer-card:hover {
    background: rgba(255,255,255,.15) !important;
    transform: translateY(-3px);
}
.popover {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    border: 1px solid #e2e8f0;
}
.popover-header {
    background: #f8fafc;
    font-weight: 700;
    border-bottom: 1px solid #f1f5f9;
    border-radius: 12px 12px 0 0;
}
.table-compact th,
.table-compact td {
    padding: 0.65rem 0.75rem !important;
    font-size: 0.8rem !important;
}
.table-compact th {
    white-space: nowrap !important;
}
.table-compact th:first-child,
.table-compact td:first-child {
    padding-left: 1.5rem !important;
}
.table-compact th:last-child,
.table-compact td:last-child {
    padding-right: 1.5rem !important;
}
</style>
@endpush
