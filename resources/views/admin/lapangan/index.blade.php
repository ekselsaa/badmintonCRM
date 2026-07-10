@extends('layouts.app')
@section('title', 'Kelola Lapangan')
@section('page_title', 'Kelola Lapangan')
@section('page_subtitle', 'Manajemen data lapangan bulutangkis')
@section('topbar_actions')
    <a href="{{ route('admin.lapangan.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah Lapangan
    </a>
@endsection

@section('content')
<style>
    .court-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 2px 8px rgba(0,0,0,.05);
        transition: transform .2s ease, box-shadow .2s ease;
        overflow: hidden;
    }
    .court-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 32px rgba(0,0,0,.1);
    }
    .court-card .court-header {
        padding: 1.2rem 1.3rem 1rem;
        position: relative;
    }
    .court-badge-num {
        width: 44px; height: 44px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1.1rem; color: #fff;
        flex-shrink: 0;
    }
    .court-card .price-row {
        display: flex; gap: .5rem; flex-wrap: wrap;
        padding: .75rem 1.3rem;
        border-top: 1px solid #f8fafc;
        background: #fafbfc;
    }
    .price-chip {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: .3rem .75rem;
        font-size: .75rem;
    }
    .price-chip .day { color: #94a3b8; font-size: .68rem; display: block; }
    .price-chip .amt { color: #059669; font-weight: 700; }
    .court-card .action-row {
        display: flex; gap: .5rem;
        padding: .75rem 1.3rem;
        border-top: 1px solid #f1f5f9;
    }
    .stat-mini {
        background: #fff;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
        padding: 1rem 1.25rem;
        text-align: center;
    }
    .stat-mini .num { font-size: 1.8rem; font-weight: 800; line-height: 1; }
    .stat-mini .lbl { font-size: .75rem; color: #94a3b8; margin-top: .2rem; }
</style>

<div>
    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success d-flex align-items-center gap-2 rounded-3 py-2 mb-4">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    {{-- ── Mini Stats Bar ── --}}
    @php
        $totalL    = $lapangans->total();
        $aktifL    = $lapangans->filter(fn($l) => $l->status === 'aktif')->count();
        $nonAktifL = $lapangans->filter(fn($l) => $l->status !== 'aktif')->count();
        $totalBook = $lapangans->sum('bookings_count');
        $colors = ['#3b82f6','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4','#ec4899','#f97316'];
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-mini">
                <div class="num" style="color:#1e293b;">{{ $totalL }}</div>
                <div class="lbl">Total Lapangan</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-mini">
                <div class="num" style="color:#10b981;">{{ $aktifL }}</div>
                <div class="lbl">Lapangan Aktif</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-mini">
                <div class="num" style="color:#ef4444;">{{ $nonAktifL }}</div>
                <div class="lbl">Non-Aktif</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-mini">
                <div class="num" style="color:#3b82f6;">{{ $totalBook }}</div>
                <div class="lbl">Total Booking</div>
            </div>
        </div>
    </div>

    {{-- ── Card Grid Lapangan ── --}}
    @if($lapangans->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-grid-3x3-gap fs-1 d-block mb-3 opacity-25"></i>
            <p>Belum ada lapangan terdaftar.</p>
            <a href="{{ route('admin.lapangan.create') }}" class="btn btn-primary rounded-pill px-4">
                <i class="bi bi-plus-circle me-1"></i> Tambah Lapangan Pertama
            </a>
        </div>
    @else
        <div class="row g-3">
            @foreach($lapangans as $l)
            @php
                $color     = $colors[($loop->index) % count($colors)];
                $isAktif   = $l->status === 'aktif';
            @endphp
            <div class="col-sm-6 col-xl-4">
                <div class="court-card">
                    <div class="court-header">
                        <div class="d-flex align-items-start gap-3">
                            <div class="court-badge-num" style="background:{{ $color }};">
                                {{ $loop->iteration }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <h6 class="fw-bold mb-0" style="font-size:.95rem;">{{ $l->nama_lapangan }}</h6>
                                    @if($isAktif)
                                        <span class="badge rounded-pill" style="background:#d1fae5;color:#065f46;font-size:.65rem;">
                                            <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>Aktif
                                        </span>
                                    @else
                                        <span class="badge rounded-pill" style="background:#fee2e2;color:#991b1b;font-size:.65rem;">
                                            <i class="bi bi-circle-fill me-1" style="font-size:.45rem;"></i>Non-Aktif
                                        </span>
                                    @endif
                                </div>
                                @if($l->deskripsi)
                                    <div class="text-muted mt-1" style="font-size:.76rem; line-height:1.4;">
                                        {{ Str::limit($l->deskripsi, 55) }}
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Booking count --}}
                        <div class="d-flex align-items-center gap-2 mt-3">
                            <i class="bi bi-receipt-cutoff text-muted" style="font-size:.85rem;"></i>
                            <span style="font-size:.8rem; color:#64748b;">
                                <strong style="color:#1e293b;">{{ $l->bookings_count }}</strong> total booking
                            </span>
                            {{-- Progress bar visual --}}
                            @if($totalBook > 0)
                                <div class="flex-grow-1" style="height:4px; background:#f1f5f9; border-radius:4px; overflow:hidden;">
                                    <div style="height:100%; width:{{ min(100, round($l->bookings_count / max($totalBook,1) * 100)) }}%;
                                                background:{{ $color }}; border-radius:4px;"></div>
                                </div>
                                <span class="text-muted" style="font-size:.7rem;">
                                    {{ $totalBook > 0 ? round($l->bookings_count / $totalBook * 100) : 0 }}%
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Price chips --}}
                    <div class="price-row">
                        <div class="price-chip">
                            <span class="day">Senin – Jumat</span>
                            <span class="amt">Rp {{ number_format($l->harga_weekday, 0, ',', '.') }}</span>
                        </div>
                        <div class="price-chip">
                            <span class="day">Sabtu – Minggu</span>
                            <span class="amt">Rp {{ number_format($l->harga_weekend, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    {{-- Action row --}}
                    <div class="action-row">
                        <a href="{{ route('admin.lapangan.edit', $l->id) }}"
                           class="btn btn-sm btn-outline-primary rounded-pill flex-grow-1">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <a href="{{ route('admin.jadwal.index', ['lapangan_id' => $l->id]) }}"
                           class="btn btn-sm btn-outline-success rounded-pill flex-grow-1">
                            <i class="bi bi-calendar3 me-1"></i>Jadwal
                        </a>
                        <form action="{{ route('admin.lapangan.destroy', $l->id) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill btn-delete"
                                    data-nama="{{ $l->nama_lapangan }}"
                                    title="Hapus Lapangan">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach

            {{-- Card Tambah Lapangan --}}
            <div class="col-sm-6 col-xl-4">
                <a href="{{ route('admin.lapangan.create') }}" class="text-decoration-none d-block h-100">
                    <div class="court-card d-flex align-items-center justify-content-center h-100"
                         style="min-height:180px; border: 2px dashed #cbd5e1; background:#f8fafc; box-shadow:none;">
                        <div class="text-center text-muted">
                            <div style="width:52px;height:52px;border-radius:14px;background:#e2e8f0;
                                        display:flex;align-items:center;justify-content:center;
                                        margin:0 auto .8rem;font-size:1.4rem;">
                                <i class="bi bi-plus-lg" style="color:#64748b;"></i>
                            </div>
                            <div class="fw-600" style="font-size:.88rem; color:#475569;">Tambah Lapangan</div>
                            <div style="font-size:.75rem; color:#94a3b8; margin-top:.2rem;">Klik untuk menambah baru</div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        @if($lapangans->hasPages())
            <div class="mt-4">{{ $lapangans->links() }}</div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            const namaLapangan = this.getAttribute('data-nama');
            
            Swal.fire({
                title: 'Hapus Lapangan?',
                text: `Apakah Anda yakin ingin menghapus lapangan "${namaLapangan}"? Semua jadwal terkait juga akan terhapus. Tindakan ini tidak dapat dibatalkan.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Danger Red
                cancelButtonColor: '#6b7280', // Gray
                confirmButtonText: 'Ya, Hapus!',
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
</script>
@endpush
