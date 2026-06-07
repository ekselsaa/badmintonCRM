@php
    $isPast     = \Carbon\Carbon::parse($tanggal.' '.$j->jam_mulai)->isPast();
    
    // Status and badge should reflect the database record status first
    $statusText = match($j->status) {
        'tersedia' => $isPast ? 'LEWAT' : 'TERSEDIA',
        'dipesan'  => 'DIPESAN',
        'pending'  => 'PENDING',
        'ditutup'  => 'DITUTUP',
        default    => strtoupper($j->status),
    };
    
    // Card styling class
    $slotClass  = $isPast ? 'st-lewat' : 'st-'.$j->status;
    $canBook    = !$isPast && $j->status === 'tersedia';
@endphp
<div class="col-slot">
    @if($canBook && auth()->check())
        @if(auth()->user()->isAdmin())
            {{-- Logged in Admin: Redirect to admin schedule management --}}
            <a href="{{ route('admin.jadwal.index', ['lapangan_id'=>$lapId,'tanggal'=>$tanggal,'jam_mulai'=>substr($j->jam_mulai,0,5),'jam_selesai'=>substr($j->jam_selesai,0,5),'tab'=>'offline']) }}" class="slot-card {{ $slotClass }}" style="display:flex">
                <div class="s-top">{{ $statusText }}</div>
                <div class="s-body">
                    <div class="s-time">{{ substr($j->jam_mulai,0,5) }}</div>
                    <div class="s-until">s/d {{ substr($j->jam_selesai,0,5) }}</div>
                    <span class="s-btn mt-1" style="background: linear-gradient(135deg, #fbbf24, #f59e0b);"><i class="bi bi-gear-fill"></i> Kelola</span>
                </div>
            </a>
        @else
            {{-- Logged in Customer: Normal Booking --}}
            <a href="{{ route('booking.create', ['lapangan_id'=>$lapId,'tanggal'=>$tanggal,'jam_mulai'=>substr($j->jam_mulai,0,5),'jam_selesai'=>substr($j->jam_selesai,0,5)]) }}" class="slot-card {{ $slotClass }}" style="display:flex">
                <div class="s-top">{{ $statusText }}</div>
                <div class="s-body">
                    <div class="s-time">{{ substr($j->jam_mulai,0,5) }}</div>
                    <div class="s-until">s/d {{ substr($j->jam_selesai,0,5) }}</div>
                    <span class="s-btn mt-1"><i class="bi bi-calendar-check"></i> Booking</span>
                </div>
            </a>
        @endif
    @elseif($canBook && !auth()->check())
        {{-- Guest: redirect ke login dengan membawa parameter jadwal --}}
        <a href="{{ route('login', ['lapangan_id'=>$lapId,'tanggal'=>$tanggal,'jam_mulai'=>substr($j->jam_mulai,0,5),'jam_selesai'=>substr($j->jam_selesai,0,5)]) }}" class="slot-card {{ $slotClass }}" style="display:flex">
            <div class="s-top">{{ $statusText }}</div>
            <div class="s-body">
                <div class="s-time">{{ substr($j->jam_mulai,0,5) }}</div>
                <div class="s-until">s/d {{ substr($j->jam_selesai,0,5) }}</div>
                <span class="s-btn mt-1"><i class="bi bi-box-arrow-in-right"></i> Masuk</span>
            </div>
        </a>
    @elseif($isPast && $j->status === 'tersedia')
        {{-- Past Available: shown as LEWAT / Selesai --}}
        <div class="slot-card {{ $slotClass }}">
            <div class="s-top">{{ $statusText }}</div>
            <div class="s-body">
                <div class="s-time">{{ substr($j->jam_mulai,0,5) }}</div>
                <div class="s-until">s/d {{ substr($j->jam_selesai,0,5) }}</div>
                <div class="s-finish"><i class="bi bi-clock"></i> Selesai</div>
            </div>
        </div>
    @else
        {{-- Dipesan / Pending / Ditutup (Both past and future) --}}
        <div class="slot-card {{ $slotClass }}">
            <div class="s-top">{{ $statusText }}</div>
            <div class="s-body">
                <div class="s-time">{{ substr($j->jam_mulai,0,5) }}</div>
                <div class="s-until">s/d {{ substr($j->jam_selesai,0,5) }}</div>
                @if(!empty($j->keterangan))
                    <div class="s-note" title="{{ $j->keterangan }}">{{ $j->keterangan }}</div>
                @endif
                @if($isPast)
                    <div class="s-finish mt-1"><i class="bi bi-check-circle-fill text-success"></i> Selesai</div>
                @endif
            </div>
        </div>
    @endif
</div>
