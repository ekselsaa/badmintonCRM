@extends('layouts.app')
@section('title', 'Manajemen Ulasan')
@section('page_title', 'Ulasan Pelanggan')
@section('page_subtitle', 'Kelola testimoni yang akan ditampilkan di halaman utama')

@section('content')
<div class="p-0">
    {{-- Tabs Navigasi CRM & Pelanggan --}}
    <ul class="nav nav-pills gap-2 mb-3 bg-white p-2 rounded-3 border" style="font-size: 0.82rem; width: max-content;">
        <li class="nav-item">
            <a class="nav-link px-3 py-2 fw-semibold text-secondary" href="{{ route('admin.crm.pelanggan') }}">
                <i class="bi bi-people-fill me-2"></i>Data Pelanggan
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active px-3 py-2 fw-semibold" href="{{ route('admin.ulasan.index') }}">
                <i class="bi bi-star-fill me-2"></i>Ulasan Pelanggan
            </a>
        </li>
    </ul>

    @if(session('success'))
        <div class="alert alert-success rounded-3 py-2 mb-3"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
    @endif

    <div class="table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="align-middle">
                        <th class="text-nowrap" width="5%">No.</th>
                        <th class="text-nowrap" width="20%">Pelanggan</th>
                        <th class="text-nowrap" width="15%">Lapangan & Tanggal</th>
                        <th class="text-center text-nowrap" width="15%">Rating</th>
                        <th class="text-nowrap" width="30%">Isi Ulasan</th>
                        <th class="text-center text-nowrap" width="15%">Tampil di Beranda</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ulasans as $u)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <div class="fw-bold">{{ $u->nama_pemesan }}</div>
                            <small class="text-muted">{{ $u->user ? 'Pelanggan Terdaftar' : 'Offline' }}</small>
                        </td>
                        <td>
                            <div class="fw-bold text-nowrap">{{ $u->lapangan->nama_lapangan ?? '-' }}</div>
                            <small class="text-muted text-nowrap">{{ $u->tanggal_booking->format('d/m/Y') }}</small>
                        </td>
                        <td class="text-center">
                            <div class="text-warning text-nowrap">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $u->rating)
                                        <i class="bi bi-star-fill"></i>
                                    @else
                                        <i class="bi bi-star"></i>
                                    @endif
                                @endfor
                            </div>
                        </td>
                        <td>
                            <div class="text-truncate" style="max-width: 300px;" title="{{ $u->ulasan }}">
                                {{ $u->ulasan ?? '-' }}
                            </div>
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.ulasan.toggle-beranda', $u->id) }}" method="POST" class="mb-0">
                                @csrf @method('PUT')
                                @if($u->is_tampil_beranda)
                                    <button type="submit" class="btn btn-sm btn-status-toggle status-active px-3" title="Klik untuk menyembunyikan ulasan ini dari beranda">
                                        <span class="normal-text"><i class="bi bi-eye-fill me-1"></i> Ditampilkan</span>
                                        <span class="hover-text"><i class="bi bi-eye-slash-fill me-1"></i> Sembunyikan</span>
                                    </button>
                                @else
                                    <button type="submit" class="btn btn-sm btn-status-toggle status-inactive px-3" title="Klik untuk menampilkan ulasan ini di beranda">
                                        <span class="normal-text"><i class="bi bi-eye-slash me-1"></i> Disembunyikan</span>
                                        <span class="hover-text"><i class="bi bi-eye-fill me-1"></i> Tampilkan</span>
                                    </button>
                                @endif
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-chat-square-text fs-2 d-block mb-2"></i>
                            Belum ada ulasan dari pelanggan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($ulasans->hasPages())
        <div class="p-3 border-top">
            {{ $ulasans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Styling interaktif untuk toggle status beranda */
    .btn-status-toggle {
        min-height: 32px;
        transition: all 0.2s ease;
        font-weight: 600 !important;
        font-size: 0.78rem !important;
        border-radius: 50px !important;
        border: 1px solid transparent !important;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Tombol Aktif (Ditampilkan) */
    .btn-status-toggle.status-active {
        background-color: #ecfdf5 !important;
        border-color: #a7f3d0 !important;
        color: #059669 !important;
    }
    .btn-status-toggle.status-active .hover-text {
        display: none;
    }
    .btn-status-toggle.status-active:hover {
        background-color: #fef2f2 !important;
        border-color: #fca5a5 !important;
        color: #dc2626 !important;
    }
    .btn-status-toggle.status-active:hover .normal-text {
        display: none;
    }
    .btn-status-toggle.status-active:hover .hover-text {
        display: inline-block;
    }

    /* Tombol Nonaktif (Disembunyikan) */
    .btn-status-toggle.status-inactive {
        background-color: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #64748b !important;
    }
    .btn-status-toggle.status-inactive .hover-text {
        display: none;
    }
    .btn-status-toggle.status-inactive:hover {
        background-color: #eff6ff !important;
        border-color: #bfdbfe !important;
        color: #2563eb !important;
    }
    .btn-status-toggle.status-inactive:hover .normal-text {
        display: none;
    }
    .btn-status-toggle.status-inactive:hover .hover-text {
        display: inline-block;
    }
</style>
@endpush

