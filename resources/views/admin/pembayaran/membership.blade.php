@extends('layouts.app')
@section('title', 'Verifikasi Pembayaran Membership')
@section('page_title', 'Verifikasi Pembayaran Membership')
@section('page_subtitle', 'Periksa dan verifikasi bukti pembayaran membership pelanggan')
@section('content')
<div class="p-0">
    {{-- Tabs Navigasi Transaksi --}}
    <ul class="nav nav-pills gap-2 mb-3 bg-white p-2 rounded-3 border" style="font-size: 0.82rem; width: max-content;">
        <li class="nav-item">
            <a class="nav-link px-3 py-2 fw-semibold text-secondary" href="{{ route('admin.booking.index') }}">
                <i class="bi bi-calendar-check me-2"></i>Daftar Booking
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 fw-semibold text-secondary" href="{{ route('admin.pembayaran.index') }}">
                <i class="bi bi-credit-card-2-front me-2"></i>Verifikasi Pembayaran
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active px-3 py-2 fw-semibold" href="{{ route('admin.pembayaran-membership.index') }}">
                <i class="bi bi-star me-2"></i>Verifikasi Pembayaran Member
            </a>
        </li>
    </ul>

    @if(session('success'))
        <div class="alert alert-success rounded-3 py-2 mb-3"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger rounded-3 py-2 mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Gagal!</strong>
            <ul class="mb-0 mt-1 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No.</th>
                        <th>Pelanggan</th>
                        <th>Paket Membership</th>
                        <th>Jumlah Bayar</th>
                        <th>Metode</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayarans as $p)
                    <tr>
                        <td>{{ $loop->iteration }}</td>

                        {{-- Pelanggan --}}
                        <td>
                            <div class="fw-600">{{ $p->user?->name ?? '-' }}</div>
                            <small class="text-muted">{{ $p->user?->username ?? '-' }}</small>
                            @if($p->user?->nomor_hp)
                                <br><small class="text-muted"><i class="bi bi-telephone me-1"></i>{{ $p->user->nomor_hp }}</small>
                            @endif
                        </td>

                        {{-- Paket --}}
                        <td>
                            <div class="fw-500">
                                @if($p->paket === 'weekday_pagi')
                                    <span class="badge bg-light text-primary border border-primary-subtle">Weekday (Pagi/Siang)</span>
                                @elseif($p->paket === 'weekday_malam')
                                    <span class="badge bg-light text-indigo border border-indigo-subtle" style="color: #6366f1; border-color: #c7d2fe !important;">Weekday (Malam)</span>
                                @else
                                    <span class="badge bg-light text-warning border border-warning-subtle" style="color: #d97706; border-color: #fde68a !important;">Weekend (Sabtu/Minggu)</span>
                                @endif
                            </div>
                            <small class="text-muted d-block mt-1">Diajukan: {{ $p->created_at->translatedFormat('d M Y H:i') }}</small>
                        </td>

                        {{-- Jumlah --}}
                        <td class="fw-bold text-success">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>

                        <td>
                            @if($p->metode_pembayaran === 'qris')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 0.72rem;">QRIS Instan</span>
                            @elseif($p->metode_pembayaran === 'tunai')
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-1" style="font-size: 0.72rem; color: #d97706 !important; border-color: #fde68a !important; background-color: #fef3c7 !important;">Tunai ke Kasir</span>
                            @else
                                <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 0.72rem;">{{ strtoupper($p->metode_pembayaran) }}</span>
                            @endif
                        </td>

                        {{-- Bukti --}}
                        <td>
                            @if($p->bukti_pembayaran)
                                <a href="{{ asset('storage/' . $p->bukti_pembayaran) }}" target="_blank">
                                    <img src="{{ asset('storage/' . $p->bukti_pembayaran) }}"
                                         class="rounded border shadow-sm"
                                         style="width:50px;height:50px;object-fit:cover;"
                                         title="Klik untuk lihat ukuran penuh">
                                </a>
                            @else
                                <span class="badge bg-light text-muted border">Belum Upload</span>
                            @endif
                        </td>

                        {{-- Status Badge --}}
                        <td>
                            @php
                                $statusClass = match($p->status_verifikasi) {
                                    'menunggu'    => 'warning text-dark',
                                    'diverifikasi'=> 'success',
                                    'ditolak'     => 'danger',
                                    default       => 'light text-dark',
                                };
                                $statusLabel = match($p->status_verifikasi) {
                                    'menunggu'    => '⏳ Menunggu',
                                    'diverifikasi'=> '✅ Diverifikasi',
                                    'ditolak'     => '❌ Ditolak',
                                    default       => ucfirst($p->status_verifikasi),
                                };
                            @endphp
                            <span class="badge bg-{{ $statusClass }} px-2 py-1 rounded-pill" style="font-size:.75rem">
                                {{ $statusLabel }}
                            </span>
                            @if($p->catatan_admin)
                                <br><small class="text-muted" style="font-size:.7rem">{{ $p->catatan_admin }}</small>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td>
                            @if($p->status_verifikasi === 'menunggu')
                                <form action="{{ route('admin.pembayaran-membership.verifikasi', $p->id) }}" method="POST">
                                    @csrf @method('PUT')
                                    <div class="d-flex gap-1 flex-wrap">
                                        <input type="text" name="catatan_admin" class="form-control form-control-sm"
                                               placeholder="Catatan (opsional)" style="min-width:110px;font-size:.8rem">
                                        <button type="submit" name="status_verifikasi" value="diverifikasi"
                                                class="btn btn-sm btn-success" title="Terima & Upgrade Keanggotaan">
                                            <i class="bi bi-check-lg"></i> Setujui
                                        </button>
                                        <button type="submit" name="status_verifikasi" value="ditolak"
                                                class="btn btn-sm btn-danger" title="Tolak">
                                            <i class="bi bi-x-lg"></i> Tolak
                                        </button>
                                    </div>
                                </form>
                            @else
                                {{-- Sudah diproses --}}
                                <small class="text-muted">
                                    Diverifikasi pada:<br>
                                    {{ $p->verified_at ? $p->verified_at->format('d/m/Y H:i') : '' }}
                                </small>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-check-circle text-success fs-1 d-block mb-2"></i>
                            <span class="text-muted">Tidak ada pembayaran membership yang perlu diverifikasi</span>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pembayarans->hasPages())
        <div class="px-4 pb-4 pt-1">{{ $pembayarans->links() }}</div>
        @endif
    </div>
</div>
@endsection
