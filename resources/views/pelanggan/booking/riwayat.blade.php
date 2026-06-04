@extends('layouts.app')
@section('title', 'Riwayat Booking')
@section('page_title', 'Riwayat Booking')
@section('page_subtitle', 'Semua riwayat pemesanan Anda')
@section('topbar_actions')
    <a href="{{ route('booking.index') }}" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
        <i class="bi bi-plus-circle me-1"></i>Booking Baru
    </a>
@endsection

@section('content')
<div class="p-0">
            @if(session('success'))
                <div class="alert alert-success rounded-3 py-2 mb-3"><i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}</div>
            @endif

            @forelse($bookings as $b)
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden transition-hover">
                <div class="card-body p-0">
                    <div class="p-3 d-flex align-items-center justify-content-between" style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center justify-content-center rounded-circle shadow-sm" style="width: 48px; height: 48px; background: white;">
                                @if($b->status == 'selesai')
                                    <i class="bi bi-check-circle-fill fs-4 text-success"></i>
                                @elseif($b->status == 'dipesan')
                                    <i class="bi bi-calendar-event fs-4 text-primary"></i>
                                @elseif($b->status == 'pending')
                                    <i class="bi bi-clock-history fs-4 text-warning"></i>
                                @else
                                    <i class="bi bi-x-circle-fill fs-4 text-danger"></i>
                                @endif
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">{{ $b->lapangan->nama_lapangan }}</h6>
                                <span class="badge badge-{{ $b->status }} px-2 py-1 rounded-pill" style="font-size:0.75rem">
                                    {{ ucfirst($b->status) }}
                                </span>
                                @if($b->reward_applied)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill ms-1" style="font-size:0.75rem">
                                    <i class="bi bi-gift-fill me-1"></i>Loyalty Reward
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block mb-1" style="font-size: 0.8rem">
                                <i class="bi bi-clock me-1"></i>
                                @if($b->jadwal && $b->status == 'dipesan' && \Carbon\Carbon::parse($b->jadwal->tanggal)->isFuture())
                                    Akan datang {{ \Carbon\Carbon::parse($b->jadwal->tanggal)->diffForHumans() }}
                                @elseif($b->status == 'selesai')
                                    Selesai {{ $b->updated_at->diffForHumans() }}
                                @else
                                    Dibuat {{ $b->created_at->diffForHumans() }}
                                @endif
                            </small>
                            <span class="fw-bold text-dark fs-6">Rp {{ number_format($b->total_harga, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="p-4">
                        <div class="row align-items-center g-3">
                            <div class="col-md-5">
                                <div class="d-flex gap-3 align-items-start">
                                    <i class="bi bi-calendar-check text-muted mt-1 fs-5"></i>
                                    <div>
                                        <p class="mb-0 text-muted small">Jadwal Main</p>
                                        <p class="mb-0 fw-semibold text-dark">
                                            {{ $b->jadwal ? $b->jadwal->tanggal->translatedFormat('l, d F Y') : '-' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="d-flex gap-3 align-items-start">
                                    <i class="bi bi-clock text-muted mt-1 fs-5"></i>
                                    <div>
                                        <p class="mb-0 text-muted small">Waktu</p>
                                        <p class="mb-0 fw-semibold text-dark">
                                            @if($b->jadwal) 
                                                {{ \Carbon\Carbon::parse($b->jadwal->jam_mulai)->format('H:i') }} - 
                                                {{ $b->jadwal->jam_selesai == '24:00:00' ? '24:00' : \Carbon\Carbon::parse($b->jadwal->jam_selesai)->format('H:i') }} 
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end border-md-start">
                                @if($b->pembayaran)
                                    <div class="mb-2">
                                        <span class="text-muted small me-2">Pembayaran:</span>
                                        <span class="badge badge-{{ $b->pembayaran->status_verifikasi }}" style="font-size:0.75rem">
                                            <i class="bi bi-credit-card me-1"></i> {{ ucfirst($b->pembayaran->status_verifikasi) }}
                                        </span>
                                    </div>
                                @endif

                                <div class="d-flex gap-2 justify-content-md-end mt-3">
                                    @if($b->status == 'selesai' && !$b->rating)
                                        <button type="button" class="btn btn-warning text-dark rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#modalUlasan{{ $b->id }}">
                                            <i class="bi bi-star-fill me-1"></i>Beri Ulasan
                                        </button>
                                    @elseif($b->status == 'selesai' && $b->rating)
                                        <span class="badge bg-light text-dark border d-flex align-items-center gap-1 px-3 py-2 rounded-pill" title="Ulasan Anda: {{ $b->ulasan }}">
                                            <i class="bi bi-star-fill text-warning"></i> {{ $b->rating }}/5
                                        </span>
                                    @endif
                                    
                                    <a href="{{ route('booking.show', $b->id) }}" class="btn btn-outline-primary rounded-pill fw-semibold">
                                        <i class="bi bi-eye me-1"></i>Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                        @if($b->fasilitas)
                        <div class="mt-3 pt-3 border-top d-flex align-items-center gap-2">
                            <i class="bi bi-box-seam text-secondary"></i>
                            <span class="text-secondary small">Fasilitas Sewa:</span>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill" style="font-size:0.75rem">
                                {{ $b->fasilitas }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="table-card p-5 text-center">
                <i class="bi bi-calendar-x fs-1 text-muted d-block mb-3"></i>
                <h6 class="text-muted">Anda belum memiliki booking</h6>
                <p class="text-muted small mb-3">Mulai booking lapangan favorit Anda sekarang!</p>
                <a href="{{ route('booking.index') }}" class="btn btn-primary rounded-pill">
                    <i class="bi bi-calendar-plus me-1"></i>Buat Booking Pertama
                </a>
            </div>
            @endforelse

            @if($bookings->hasPages())
            <div class="mt-3">{{ $bookings->links() }}</div>
            @endif
        </div>
    </div>
</div>

@foreach($bookings as $b)
    @if($b->status == 'selesai' && !$b->rating)
    <!-- Modal Ulasan -->
    <div class="modal fade" id="modalUlasan{{ $b->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow-lg">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">Beri Ulasan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted small mb-3">Bagaimana pengalaman Anda bermain di <strong>{{ $b->lapangan->nama_lapangan }}</strong> pada {{ $b->jadwal ? $b->jadwal->tanggal->format('d M Y') : '-' }}?</p>
                    <form action="{{ route('booking.ulasan', $b->id) }}" method="POST">
                        @csrf
                        <div class="mb-4 text-center">
                            <label class="form-label fw-bold mb-2">Pilih Rating <span class="text-danger">*</span></label>
                            <div class="star-rating d-flex flex-row-reverse justify-content-center gap-2" onmouseleave="resetRatingText('{{ $b->id }}')">
                                <input type="radio" id="star5-{{ $b->id }}" name="rating" value="5" required />
                                <label for="star5-{{ $b->id }}" title="Sangat Bagus"
                                    onmouseover="setRatingText('{{ $b->id }}', 'Sangat Bagus 😍')" 
                                    onclick="confirmRatingText('{{ $b->id }}', 'Sangat Bagus 😍')"><i class="bi bi-star-fill"></i></label>
                                
                                <input type="radio" id="star4-{{ $b->id }}" name="rating" value="4" />
                                <label for="star4-{{ $b->id }}" title="Bagus"
                                    onmouseover="setRatingText('{{ $b->id }}', 'Bagus 😄')" 
                                    onclick="confirmRatingText('{{ $b->id }}', 'Bagus 😄')"><i class="bi bi-star-fill"></i></label>
                                
                                <input type="radio" id="star3-{{ $b->id }}" name="rating" value="3" />
                                <label for="star3-{{ $b->id }}" title="Cukup"
                                    onmouseover="setRatingText('{{ $b->id }}', 'Cukup 🙂')" 
                                    onclick="confirmRatingText('{{ $b->id }}', 'Cukup 🙂')"><i class="bi bi-star-fill"></i></label>
                                
                                <input type="radio" id="star2-{{ $b->id }}" name="rating" value="2" />
                                <label for="star2-{{ $b->id }}" title="Kurang"
                                    onmouseover="setRatingText('{{ $b->id }}', 'Kurang 😕')" 
                                    onclick="confirmRatingText('{{ $b->id }}', 'Kurang 😕')"><i class="bi bi-star-fill"></i></label>
                                
                                <input type="radio" id="star1-{{ $b->id }}" name="rating" value="1" />
                                <label for="star1-{{ $b->id }}" title="Sangat Kurang"
                                    onmouseover="setRatingText('{{ $b->id }}', 'Sangat Kurang 😠')" 
                                    onclick="confirmRatingText('{{ $b->id }}', 'Sangat Kurang 😠')"><i class="bi bi-star-fill"></i></label>
                            </div>
                            <div id="rating-text-{{ $b->id }}" class="mt-2 fw-semibold text-warning" style="min-height: 24px; font-size: 1.1rem; transition: 0.2s;"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Ulasan Singkat</label>
                            <textarea name="ulasan" class="form-control" rows="3" placeholder="Ceritakan pengalaman Anda di sini... (opsional)"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill">Kirim Ulasan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

<style>
.star-rating input {
    display: none;
}
.star-rating label {
    font-size: 2.2rem;
    color: #e5e7eb;
    cursor: pointer;
    transition: all 0.2s ease-in-out;
}
.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input:checked ~ label {
    color: #f59e0b;
}
.star-rating label:hover {
    transform: scale(1.15);
}
</style>

<script>
    let selectedRatings = {};

    function setRatingText(id, text) {
        document.getElementById('rating-text-' + id).innerText = text;
    }

    function confirmRatingText(id, text) {
        selectedRatings[id] = text;
        document.getElementById('rating-text-' + id).innerText = text;
    }

    function resetRatingText(id) {
        document.getElementById('rating-text-' + id).innerText = selectedRatings[id] || '';
    }
</script>

@endsection
