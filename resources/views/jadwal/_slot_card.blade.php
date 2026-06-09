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
                    <div class="s-time-group">
                        <div class="s-time">{{ substr($j->jam_mulai,0,5) }}</div>
                        <div class="s-until">s/d {{ substr($j->jam_selesai,0,5) }}</div>
                    </div>
                    <span class="s-btn" style="background: linear-gradient(135deg, #fbbf24, #f59e0b);"><i class="bi bi-gear-fill"></i> Kelola</span>
                </div>
            </a>
        @else
            {{-- Logged in Customer: Normal Booking --}}
            <a href="{{ route('booking.create', ['lapangan_id'=>$lapId,'tanggal'=>$tanggal,'jam_mulai'=>substr($j->jam_mulai,0,5),'jam_selesai'=>substr($j->jam_selesai,0,5)]) }}" class="slot-card {{ $slotClass }}" style="display:flex">
                <div class="s-top">{{ $statusText }}</div>
                <div class="s-body">
                    <div class="s-time-group">
                        <div class="s-time">{{ substr($j->jam_mulai,0,5) }}</div>
                        <div class="s-until">s/d {{ substr($j->jam_selesai,0,5) }}</div>
                    </div>
                    <span class="s-btn">Pesan</span>
                </div>
            </a>
        @endif
    @elseif($canBook && !auth()->check())
        {{-- Guest: redirect ke login dengan membawa parameter jadwal --}}
        <a href="{{ route('login', ['lapangan_id'=>$lapId,'tanggal'=>$tanggal,'jam_mulai'=>substr($j->jam_mulai,0,5),'jam_selesai'=>substr($j->jam_selesai,0,5)]) }}" class="slot-card {{ $slotClass }}" style="display:flex">
            <div class="s-top">{{ $statusText }}</div>
            <div class="s-body">
                <div class="s-time-group">
                    <div class="s-time">{{ substr($j->jam_mulai,0,5) }}</div>
                    <div class="s-until">s/d {{ substr($j->jam_selesai,0,5) }}</div>
                </div>
                <span class="s-btn">Pesan</span>
            </div>
        </a>
    @elseif($isPast && $j->status === 'tersedia')
        {{-- Past Available: shown as LEWAT / Selesai --}}
        <div class="slot-card {{ $slotClass }}">
            <div class="s-top">{{ $statusText }}</div>
            <div class="s-body">
                <div class="s-time-group">
                    <div class="s-time">{{ substr($j->jam_mulai,0,5) }}</div>
                    <div class="s-until">s/d {{ substr($j->jam_selesai,0,5) }}</div>
                </div>
                <div class="s-finish"><i class="bi bi-clock"></i> Selesai</div>
            </div>
        </div>
    @else
        {{-- Dipesan / Pending / Ditutup (Both past and future) --}}
        <div class="slot-card {{ $slotClass }}">
            <div class="s-top">{{ $statusText }}</div>
            <div class="s-body">
                <div class="s-time-group">
                    <div class="s-time">{{ substr($j->jam_mulai,0,5) }}</div>
                    <div class="s-until">s/d {{ substr($j->jam_selesai,0,5) }}</div>
                </div>
                @if(!empty($j->keterangan))
                    @php
                        $btnBg = match($j->status) {
                            'pending' => 'linear-gradient(135deg, #f59e0b, #d97706)',
                            'ditutup' => 'linear-gradient(135deg, #6b7280, #4b5563)',
                            default   => 'linear-gradient(135deg, #ef4444, #dc2626)' // dipesan
                        };
                    @endphp
                    <span class="s-btn text-truncate" title="{{ $j->keterangan }}" style="background: {{ $btnBg }}; border: none; cursor: default; display: block; max-width: 100%; font-size: .7rem !important; font-weight: 600 !important; line-height: 1.2 !important; padding: .28rem .4rem !important; text-transform: capitalize;">
                        {{ $j->keterangan }}
                    </span>
                @endif
                @if($isPast)
                    <div class="s-finish mt-1"><i class="bi bi-check-circle-fill text-success"></i> Selesai</div>
                @endif
            </div>
        </div>
    @endif
</div>
