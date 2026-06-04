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
                    <tr>
                        <th width="5%">#</th>
                        <th width="20%">Pelanggan</th>
                        <th width="15%">Lapangan & Tanggal</th>
                        <th width="15%">Rating</th>
                        <th width="30%">Isi Ulasan</th>
                        <th width="15%">Tampil di Beranda</th>
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
                            <div class="fw-bold">{{ $u->lapangan->nama_lapangan ?? '-' }}</div>
                            <small class="text-muted">{{ $u->tanggal_booking->format('d/m/Y') }}</small>
                        </td>
                        <td>
                            <div class="text-warning">
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
                        <td>
                            <form action="{{ route('admin.ulasan.toggle-beranda', $u->id) }}" method="POST">
                                @csrf @method('PUT')
                                @if($u->is_tampil_beranda)
                                    <button class="btn btn-sm btn-success rounded-pill px-3 w-100">
                                        <i class="bi bi-check-circle me-1"></i> Ditampilkan
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 w-100">
                                        <i class="bi bi-eye-slash me-1"></i> Sembunyi
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
