<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jadwal Lapangan - Anbiyaa Sport</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --success: #10b981;
    --warning: #f59e0b;
    --danger: #ef4444;
}
body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f1f5f9; background-image: radial-gradient(at 40% 20%, #e0e7ff 0px, transparent 50%), radial-gradient(at 80% 0%, #dbeafe 0px, transparent 50%), radial-gradient(at 0% 50%, #f1f5f9 0px, transparent 50%); background-attachment: fixed; color: #1e293b; }
.header { background: rgba(255,255,255,.8); backdrop-filter: blur(14px); border-bottom: 1px solid rgba(226,232,240,.6); padding: .7rem 0; }
.filter-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 1rem 1.25rem; box-shadow: 0 10px 40px -10px rgba(0,0,0,.04); }
.f-pill { font-size: .75rem; font-weight: 600; padding: .25rem .85rem; border-radius: 20px; text-decoration: none; transition: all .15s; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px; }
.f-pill.solid { background: var(--primary); color: #fff; }
.f-pill:not(.solid) { background: #fff; color: #475569; border: 1px solid #e2e8f0; }
.f-pill:not(.solid):hover { border-color: var(--primary); color: var(--primary); }

.lap-card { background: #fff; border-radius: 16px; border: 1px solid #e2e8f0; overflow: hidden; margin-bottom: 1rem; box-shadow: 0 10px 40px -10px rgba(0,0,0,.04); }
.lap-header { background: linear-gradient(135deg, #1e293b, #0f172a); padding: .85rem 1.25rem; display: flex; align-items: center; justify-content: space-between; }

.hour-card { border-radius: 12px; padding: 0; overflow: hidden; border: 1.5px solid #e2e8f0; background: #fff; transition: all .2s; height: 100%; display: flex; flex-direction: column; }
.hour-card .top-accent { height: 4px; flex-shrink: 0; }
.hour-card .body { padding: .75rem .65rem .6rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between; }
.hour-card .time { font-weight: 700; font-size: 1rem; line-height: 1.2; }
.hour-card .time-sub { font-size: .68rem; color: #64748b; }
.hour-card .status-label { font-size: .7rem; font-weight: 600; padding: .15rem .55rem; border-radius: 6px; display: inline-block; margin-top: 2px; }
.hour-card .price { font-size: .75rem; font-weight: 600; color: #166534; margin-top: 4px; }

.hour-card.tersedia { cursor: pointer; text-decoration: none; color: inherit; display: flex; }
.hour-card.tersedia .top-accent { background: var(--success); }
.hour-card.tersedia .status-label { background: #d1fae5; color: #065f46; }
.hour-card.tersedia:hover { border-color: var(--success); box-shadow: 0 4px 20px rgba(16,185,129,.15); transform: translateY(-2px); }

.hour-card.dipesan .top-accent { background: var(--danger); }
.hour-card.dipesan .status-label { background: #fee2e2; color: #991b1b; }
.hour-card.pending .top-accent { background: var(--warning); }
.hour-card.pending .status-label { background: #fef3c7; color: #92400e; }
.hour-card.ditutup .top-accent { background: #6b7280; }
.hour-card.ditutup .status-label { background: #f3f4f6; color: #4b5563; }

.hour-card.dipesan, .hour-card.ditutup, .hour-card.pending { opacity: .7; }

.hour-card.past { opacity: .35; }
.hour-card.past.tersedia { opacity: .4; cursor: default; pointer-events: none; }

.rt-indicator { font-size: .65rem; color: #94a3b8; display: inline-flex; align-items: center; gap: 4px; }
.rt-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--success); animation: pulse-dot 2s infinite; }
@keyframes pulse-dot { 0%,100% { opacity: 1; } 50% { opacity: .3; } }
@keyframes fadeUp { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
.anim { animation: fadeUp .3s ease both; }
</style>
</head>
<body>

<div class="header">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="{{ route('home') }}" class="text-decoration-none fw-bold" style="color:#0f172a;font-size:.9rem">
            <i class="bi bi-trophy-fill me-1" style="color:#0ea5e9"></i>Anbiyaa
        </a>
        <div class="d-flex gap-1 align-items-center">
            <span class="rt-indicator me-2" id="rtIndicator"><span class="rt-dot"></span> Live</span>
            <a href="{{ route('home') }}" class="btn btn-sm rounded-pill" style="border:1px solid #e2e8f0;color:#475569;font-size:.75rem"><i class="bi bi-house"></i></a>
            @auth
                <a href="{{ route('booking.create') }}" class="btn btn-sm text-white rounded-pill" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));font-size:.75rem">Booking</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm text-white rounded-pill" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));font-size:.75rem">Masuk</a>
            @endauth
        </div>
    </div>
</div>

<div class="container py-3">

    <div class="d-flex align-items-center justify-content-between mb-3">
        <div class="d-flex align-items-center gap-2">
            <h1 class="fs-5 fw-bold mb-0">Jadwal Lapangan</h1>
            <span class="badge bg-white text-muted fw-normal border rounded-pill" style="font-size:.68rem">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d M Y') }}</span>
        </div>
    </div>

    <div class="filter-card mb-4">
        <form method="GET" id="filterForm" class="row g-2 align-items-center">
            <div class="col-lg-3 col-md-4">
                <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ $tanggal }}" min="{{ date('Y-m-d') }}" style="border-radius:8px;font-size:.8rem">
            </div>
            <div class="col-lg-3 col-md-4">
                <select name="lapangan_id" class="form-select form-select-sm" style="border-radius:8px;font-size:.8rem" onchange="this.form.submit()">
                    <option value="">Semua Lapangan</option>
                    @foreach($lapangans as $l)
                    <option value="{{ $l->id }}" {{ $lapangan_id == $l->id ? 'selected' : '' }}>{{ $l->nama_lapangan }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2 col-md-4">
                @php $today = now()->toDateString(); @endphp
                <div class="d-flex gap-1">
                    <a href="{{ route('jadwal.index', array_merge(request()->query(), ['tanggal' => $today])) }}" class="f-pill flex-fill text-center {{ $tanggal === $today ? 'solid' : '' }}">Hari Ini</a>
                    <a href="{{ route('jadwal.index', ['tanggal' => $today]) }}" class="f-pill flex-fill text-center">Reset</a>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
                <div class="d-flex gap-1 flex-wrap justify-content-lg-end">
                    <a href="{{ route('jadwal.index', array_merge(request()->except('status'), ['status' => ''])) }}" class="f-pill {{ !$status_filter ? 'solid' : '' }}">Semua</a>
                    <a href="{{ route('jadwal.index', array_merge(request()->query(), ['status' => 'tersedia'])) }}" class="f-pill {{ $status_filter === 'tersedia' ? 'solid' : '' }}"><span class="dot" style="background:var(--success)"></span>Tersedia</a>
                    <a href="{{ route('jadwal.index', array_merge(request()->query(), ['status' => 'pending'])) }}" class="f-pill {{ $status_filter === 'pending' ? 'solid' : '' }}"><span class="dot" style="background:var(--warning)"></span>Pending</a>
                    <a href="{{ route('jadwal.index', array_merge(request()->query(), ['status' => 'dipesan'])) }}" class="f-pill {{ $status_filter === 'dipesan' ? 'solid' : '' }}"><span class="dot" style="background:var(--danger)"></span>Dipesan</a>
                    <a href="{{ route('jadwal.index', array_merge(request()->query(), ['status' => 'ditutup'])) }}" class="f-pill {{ $status_filter === 'ditutup' ? 'solid' : '' }}"><span class="dot" style="background:#6b7280"></span>Ditutup</a>
                </div>
            </div>
        </form>
    </div>

    @guest
    <div class="text-end mb-3">
        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Ingin booking? <a href="{{ route('login') }}" class="fw-medium">Masuk</a> atau <a href="{{ route('register') }}" class="fw-medium">daftar</a> gratis.</small>
    </div>
    @endguest

    <div id="scheduleContainer" data-schedule-grid>
    @if($jadwals->count() > 0)

        @if($lapangan_id)
        @php $firstLap = $jadwals->first()->lapangan ?? null; @endphp
        @if($firstLap)
        <div class="d-flex align-items-center gap-2 mb-3">
            <h5 class="fw-bold mb-0" style="font-size:.95rem">{{ $firstLap->nama_lapangan }}</h5>
            <span class="badge rounded-pill" style="background:var(--primary-light);color:var(--primary);font-size:.68rem">Rp{{ number_format($firstLap->harga_per_jam ?? $firstLap->harga_weekday,0,',','.') }}/jam</span>
        </div>
        @endif
        <div class="row g-2">
            @foreach($jadwals as $idx => $j)
            @php
                $isPast = \Carbon\Carbon::parse($tanggal.' '.$j->jam_mulai)->isPast();
                $label = match($j->status) {'tersedia'=>'Tersedia','dipesan'=>'Dipesan','pending'=>'Pending','ditutup'=>$j->keterangan?:'Ditutup',default=>$j->status};
            @endphp
            <div class="col-lg-2 col-md-3 col-sm-4 col-6 anim" style="animation-delay:{{ $idx * 0.03 }}s">
                @if($j->status === 'tersedia' && !$isPast && auth()->check())
                    <a href="{{ route('booking.create', ['lapangan_id' => $j->lapangan_id, 'tanggal' => $tanggal, 'jam_mulai' => substr($j->jam_mulai,0,5), 'jam_selesai' => substr($j->jam_selesai,0,5)]) }}" class="hour-card tersedia">
                        <div class="top-accent"></div>
                        <div class="body">
                            <div>
                                <div class="time">{{ substr($j->jam_mulai,0,5) }}</div>
                                <div class="time-sub">{{ substr($j->jam_selesai,0,5) }}</div>
                            </div>
                            <div>
                                <span class="status-label">{{ $label }}</span>
                                <div class="price">Rp{{ number_format($j->harga ?? 0,0,',','.') }}</div>
                            </div>
                        </div>
                    </a>
                @elseif($j->status === 'tersedia' && !$isPast && !auth()->check())
                    <a href="{{ route('login') }}" class="hour-card tersedia">
                        <div class="top-accent"></div>
                        <div class="body">
                            <div>
                                <div class="time">{{ substr($j->jam_mulai,0,5) }}</div>
                                <div class="time-sub">{{ substr($j->jam_selesai,0,5) }}</div>
                            </div>
                            <div>
                                <span class="status-label">Tersedia</span>
                                <div class="price">Rp{{ number_format($j->harga ?? 0,0,',','.') }}</div>
                            </div>
                        </div>
                    </a>
                @elseif($j->status === 'tersedia' && $isPast)
                    <div class="hour-card tersedia past">
                        <div class="top-accent"></div>
                        <div class="body">
                            <div>
                                <div class="time">{{ substr($j->jam_mulai,0,5) }}</div>
                                <div class="time-sub">{{ substr($j->jam_selesai,0,5) }}</div>
                            </div>
                            <div>
                                <span class="status-label">Tersedia</span>
                                <div class="price">Rp{{ number_format($j->harga ?? 0,0,',','.') }}</div>
                            </div>
                        </div>
                    </div>
                @else
                <div class="hour-card {{ $j->status }}{{ $isPast ? ' past' : '' }}">
                    <div class="top-accent"></div>
                    <div class="body">
                        <div>
                            <div class="time">{{ substr($j->jam_mulai,0,5) }}</div>
                            <div class="time-sub">{{ substr($j->jam_selesai,0,5) }}</div>
                        </div>
                        <div>
                            <span class="status-label">{{ $label }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        @else
        @foreach($jadwalPerLapangan as $lapId => $slots)
        @php $lapInfo = $slots->first()->lapangan; $tc = $slots->where('status','tersedia')->count(); @endphp
        <div class="lap-card anim" style="animation-delay:{{ $loop->index * 0.06 }}s">
            <a href="{{ route('jadwal.show', ['id' => $lapInfo->id, 'tanggal' => $tanggal]) }}" class="text-decoration-none lap-header" style="cursor:pointer">
                <div>
                    <div class="text-white fw-semibold" style="font-size:.9rem">{{ $lapInfo->nama_lapangan }}</div>
                    <small style="color:#94a3b8;font-size:.7rem">Rp{{ number_format($lapInfo->harga_per_jam ?? $lapInfo->harga_weekday,0,',','.') }}/jam</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge rounded-pill" style="background:rgba(255,255,255,.1);color:#94a3b8;font-size:.68rem">{{ $slots->count() }} slot</span>
                    @if($tc > 0)
                    <span class="badge rounded-pill" style="background:rgba(16,185,129,.2);color:#6ee7b7;font-size:.68rem">{{ $tc }} tersedia</span>
                    @endif
                    <i class="bi bi-chevron-right" style="color:#64748b;font-size:.7rem"></i>
                </div>
            </a>
            <div class="p-2">
                <div class="row g-1">
                    @foreach($slots as $j)
                    @php
                        $isPast = \Carbon\Carbon::parse($tanggal.' '.$j->jam_mulai)->isPast();
                        $label = match($j->status) {'tersedia'=>'Tersedia','dipesan'=>'Dipesan','pending'=>'Pending','ditutup'=>$j->keterangan?:'Ditutup',default=>$j->status};
                    @endphp
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                        @if($j->status === 'tersedia' && !$isPast && auth()->check())
                            <a href="{{ route('booking.create', ['lapangan_id' => $lapInfo->id, 'tanggal' => $tanggal, 'jam_mulai' => substr($j->jam_mulai,0,5), 'jam_selesai' => substr($j->jam_selesai,0,5)]) }}" class="hour-card tersedia">
                                <div class="top-accent"></div>
                                <div class="body">
                                    <div>
                                        <div class="time">{{ substr($j->jam_mulai,0,5) }}</div>
                                        <div class="time-sub">{{ substr($j->jam_selesai,0,5) }}</div>
                                    </div>
                                    <div>
                                        <span class="status-label">{{ $label }}</span>
                                        <div class="price">Rp{{ number_format($j->harga ?? 0,0,',','.') }}</div>
                                    </div>
                                </div>
                            </a>
                        @elseif($j->status === 'tersedia' && !$isPast && !auth()->check())
                            <a href="{{ route('login') }}" class="hour-card tersedia">
                                <div class="top-accent"></div>
                                <div class="body">
                                    <div>
                                        <div class="time">{{ substr($j->jam_mulai,0,5) }}</div>
                                        <div class="time-sub">{{ substr($j->jam_selesai,0,5) }}</div>
                                    </div>
                                    <div>
                                        <span class="status-label">Tersedia</span>
                                        <div class="price">Rp{{ number_format($j->harga ?? 0,0,',','.') }}</div>
                                    </div>
                                </div>
                            </a>
                        @else
                        <div class="hour-card {{ $j->status }}{{ $isPast && $j->status === 'tersedia' ? ' past' : '' }}">
                            <div class="top-accent"></div>
                            <div class="body">
                                <div>
                                    <div class="time">{{ substr($j->jam_mulai,0,5) }}</div>
                                    <div class="time-sub">{{ substr($j->jam_selesai,0,5) }}</div>
                                </div>
                                <div>
                                    <span class="status-label">{{ $label }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
        @endif

    @else
    <div class="text-center py-5">
        <i class="bi bi-calendar-x fs-2 text-muted d-block mb-2"></i>
        <p class="text-muted mb-1">Tidak ada jadwal pada tanggal ini.</p>
        <a href="{{ route('jadwal.index') }}" class="btn btn-sm text-white rounded-pill" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark))">Coba Hari Lain</a>
    </div>
    @endif
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
    var scheduleContainer = document.getElementById('scheduleContainer');
    if (!scheduleContainer) return;

    var pollTimer, isPolling = false;

    function pollSchedule() {
        if (isPolling) return;
        isPolling = true;

        var url = window.location.href.split('#')[0];
        if (url.indexOf('?') > -1) url += '&_=' + Date.now();
        else url += '?_=' + Date.now();

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function(r) { return r.text(); })
            .then(function(html) {
                var parser = new DOMParser();
                var doc = parser.parseFromString(html, 'text/html');
                var newContainer = doc.getElementById('scheduleContainer');
                if (newContainer) {
                    scheduleContainer.innerHTML = newContainer.innerHTML;
                }
                isPolling = false;
            })
            .catch(function() { isPolling = false; });
    }

    pollTimer = setInterval(pollSchedule, 30000);

    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            clearInterval(pollTimer);
        } else {
            pollSchedule();
            pollTimer = setInterval(pollSchedule, 30000);
        }
    });
})();
</script>
</body>
</html>
