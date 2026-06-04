@extends('layouts.app')
@section('title', 'Verifikasi Pembayaran')
@section('page_title', 'Verifikasi Pembayaran')
@section('page_subtitle', 'Periksa dan verifikasi bukti pembayaran pelanggan')
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
            <a class="nav-link active px-3 py-2 fw-semibold" href="{{ route('admin.pembayaran.index') }}">
                <i class="bi bi-credit-card-2-front me-2"></i>Verifikasi Pembayaran
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link px-3 py-2 fw-semibold text-secondary" href="{{ route('admin.pembayaran-membership.index') }}">
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
                        <th>#</th>
                        <th>Pelanggan</th>
                        <th>Lapangan & Jadwal</th>
                        <th>Jumlah Bayar</th>
                        <th>Metode</th>
                        <th>Bukti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pembayarans as $p)
                    @php
                        // Cek apakah jadwal sudah lewat
                        $jadwalLewat = $p->booking?->jadwal
                            && \Carbon\Carbon::parse($p->booking->jadwal->tanggal)->isPast()
                            && !\Carbon\Carbon::parse($p->booking->jadwal->tanggal)->isToday();
                    @endphp
                    <tr class="{{ $jadwalLewat && $p->status_verifikasi === 'menunggu' ? 'table-secondary opacity-75' : '' }}">
                        <td>{{ $loop->iteration }}</td>

                        {{-- Pelanggan --}}
                        <td>
                            <div class="fw-600">{{ $p->booking?->user?->name ?? '-' }}</div>
                            <small class="text-muted">{{ $p->booking?->user?->email ?? '-' }}</small>
                        </td>

                        {{-- Lapangan & Jadwal --}}
                        <td>
                            <div class="fw-500">{{ $p->booking?->lapangan?->nama_lapangan ?? '-' }}</div>
                            @if($p->booking?->jadwal)
                                @php
                                    $tgl = \Carbon\Carbon::parse($p->booking->jadwal->tanggal);
                                    $isLewat = $tgl->isPast() && !$tgl->isToday();
                                @endphp
                                <small class="d-block {{ $isLewat ? 'text-danger' : 'text-muted' }}">
                                    <i class="bi bi-calendar{{ $isLewat ? '-x' : '3' }} me-1"></i>
                                    {{ $tgl->translatedFormat('d M Y') }}
                                    — {{ substr($p->booking->jadwal->jam_mulai,0,5) }}–{{ substr($p->booking->jadwal->jam_selesai,0,5) }}
                                    @if($isLewat)
                                        <span class="badge bg-danger ms-1" style="font-size:.6rem">Sudah Lewat</span>
                                    @endif
                                </small>
                            @endif
                            
                            @if(!empty($p->booking?->fasilitas))
                                <div class="mt-1 p-1 rounded" style="background:#eff6ff; border: 1px solid #bfdbfe; font-size: 0.75rem;">
                                    <span class="text-primary fw-bold"><i class="bi bi-box-seam me-1"></i>Fasilitas:</span> 
                                    <span class="text-dark">{{ $p->booking->fasilitas }}</span>
                                </div>
                            @endif
                            
                            @if(!empty($p->booking?->catatan))
                                <div class="mt-1 p-1 rounded" style="background:#f8fafc; border: 1px dashed #cbd5e1; font-size: 0.75rem;">
                                    <span class="text-secondary fw-bold"><i class="bi bi-chat-dots me-1"></i>Catatan:</span> 
                                    <span class="text-muted fst-italic">"{{ $p->booking->catatan }}"</span>
                                </div>
                            @endif
                        </td>

                        {{-- Jumlah --}}
                        <td class="fw-bold text-success">Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}</td>

                        {{-- Metode --}}
                        <td><span class="badge bg-light text-dark border">{{ ucfirst($p->metode_pembayaran) }}</span></td>

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
                                    'kedaluwarsa' => 'secondary',
                                    default       => 'light text-dark',
                                };
                                $statusLabel = match($p->status_verifikasi) {
                                    'menunggu'    => '⏳ Menunggu',
                                    'diverifikasi'=> '✅ Diverifikasi',
                                    'ditolak'     => '❌ Ditolak',
                                    'kedaluwarsa' => '⌛ Kedaluwarsa',
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
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <a href="{{ route('admin.booking.show', $p->booking_id) }}" class="btn btn-sm btn-outline-info" title="Detail Booking" style="padding: 0.25rem 0.5rem; font-size: 0.75rem; border-radius: 6px;">
                                    <i class="bi bi-eye-fill"></i> Detail
                                </a>
                                @if($p->status_verifikasi === 'menunggu' && !$jadwalLewat)
                                    {{-- Jadwal masih valid: tampilkan tombol verifikasi --}}
                                    <form action="{{ route('admin.pembayaran.verifikasi', $p->id) }}" method="POST" class="m-0">
                                        @csrf @method('PUT')
                                        <div class="d-flex gap-1">
                                            <input type="text" name="catatan_admin" class="form-control form-control-sm"
                                                   placeholder="Catatan" style="max-width:110px;font-size:.75rem;height: 28px;">
                                            <button type="submit" name="status_verifikasi" value="diverifikasi"
                                                    class="btn btn-sm btn-success" title="Terima & Konfirmasi" style="padding: 2px 8px; height: 28px;">
                                                <i class="bi bi-check-lg" style="font-size: 0.75rem;"></i>
                                            </button>
                                            <button type="submit" name="status_verifikasi" value="ditolak"
                                                    class="btn btn-sm btn-danger" title="Tolak" style="padding: 2px 8px; height: 28px;">
                                                <i class="bi bi-x-lg" style="font-size: 0.75rem;"></i>
                                            </button>
                                        </div>
                                    </form>
                                @elseif($p->status_verifikasi === 'menunggu' && $jadwalLewat)
                                    {{-- Jadwal sudah lewat: tidak bisa diverifikasi --}}
                                    <span class="text-muted small">
                                        <i class="bi bi-clock-history me-1"></i>Jadwal sudah lewat
                                    </span>
                                @else
                                    {{-- Sudah diproses --}}
                                    <small class="text-muted">
                                        {{ $p->verified_at ? $p->verified_at->format('d/m/Y H:i') : '' }}
                                    </small>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-check-circle text-success fs-1 d-block mb-2"></i>
                            <span class="text-muted">Tidak ada pembayaran yang perlu diverifikasi</span>
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
