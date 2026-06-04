<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $lapangan->nama_lapangan }} - Jadwal - Anbiyaa Sport</title>
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
.court-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 1.1rem 1.25rem; box-shadow: 0 10px 40px -10px rgba(0,0,0,.04); }
.filter-card { background: #fff; border-radius: 14px; border: 1px solid #e2e8f0; padding: 1rem 1.25rem; box-shadow: 0 10px 40px -10px rgba(0,0,0,.04); }
.f-pill { font-size: .73rem; font-weight: 600; padding: .22rem .8rem; border-radius: 20px; text-decoration: none; transition: all .15s; white-space: nowrap; display: inline-flex; align-items: center; gap: 4px; }
.f-pill.solid { background: var(--primary); color: #fff; }
.f-pill:not(.solid) { background: #fff; color: #475569; border: 1px solid #e2e8f0; }
.f-pill:not(.solid):hover { border-color: var(--primary); color: var(--primary); }

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

.legend-dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; margin-right: 3px; }
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
            <span class="rt-indicator me-2"><span class="rt-dot"></span> Live</span>
            <a href="{{ route('jadwal.index', ['tanggal' => $tanggal]) }}" class="btn btn-sm rounded-pill" style="border:1px solid #e2e8f0;color:#475569;font-size:.75rem"><i class="bi bi-grid me-1"></i>Semua</a>
            @auth
                <a href="{{ route('booking.create', ['lapangan_id' => $lapangan->id]) }}" class="btn btn-sm text-white rounded-pill" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));font-size:.75rem">Booking</a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm text-white rounded-pill" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark));font-size:.75rem">Masuk</a>
            @endauth
        </div>
    </div>
</div>

<div class="container py-3">

    <div class="court-card mb-3 anim">
        <div class="d-flex align-items-start justify-content-between flex-wrap gap-1">
            <div>
                <h5 class="fw-bold mb-0" style="font-size:1rem">{{ $lapangan->nama_lapangan }}</h5>
                <p class="text-muted small mb-0" style="font-size:.75rem">{{ $lapangan->deskripsi ?? 'Lapangan bulutangkis standar' }}</p>
            </div>
            <div class="text-end">
                <div class="fw-bold" style="font-size:1.1rem;color:#0ea5e9">Rp{{ number_format($lapangan->harga_per_jam ?? $lapangan->harga_weekday,0,',','.') }}</div>
                <small class="text-muted" style="font-size:.65rem">per jam</small>
            </div>
        </div>
    </div>

    <div class="filter-card mb-3 anim">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-5 col-lg-3">
                <input type="date" name="tanggal" class="form-control form-control-sm" value="{{ $tanggal }}" min="{{ date('Y-m-d') }}" style="border-radius:8px;font-size:.8rem">
                <input type="hidden" name="lapangan_id" value="{{ $lapangan->id }}">
            </div>
            <div class="col-md-3 col-lg-2">
                @php $today = now()->toDateString(); @endphp
                <a href="{{ route('jadwal.show', array_merge(['lapangan_id' => $lapangan->id], ['tanggal' => $today])) }}" class="f-pill d-block text-center {{ $tanggal === $today ? 'solid' : '' }}">Hari Ini</a>
            </div>
            <div class="col-md-4 col-lg-2">
                <button class="btn btn-sm text-white w-100" style="border-radius:8px;font-size:.78rem;background:linear-gradient(135deg,var(--primary),var(--primary-dark))"><i class="bi bi-search me-1"></i>Tampilkan</button>
            </div>
            <div class="col-lg-5 d-none d-lg-flex flex-wrap align-items-center gap-2 justify-content-end">
                <span class="small text-muted" style="font-size:.7rem">Status:</span>
                <span class="small d-flex align-items-center" style="font-size:.7rem"><span class="legend-dot" style="background:var(--success)"></span>Tersedia</span>
                <span class="small d-flex align-items-center" style="font-size:.7rem"><span class="legend-dot" style="background:var(--warning)"></span>Pending</span>
                <span class="small d-flex align-items-center" style="font-size:.7rem"><span class="legend-dot" style="background:var(--danger)"></span>Dipesan</span>
                <span class="small d-flex align-items-center" style="font-size:.7rem"><span class="legend-dot" style="background:#6b7280"></span>Ditutup</span>
            </div>
        </form>
        <div class="d-flex d-lg-none flex-wrap align-items-center gap-2 mt-2 pt-2" style="border-top:1px solid #f1f5f9">
            <span class="small text-muted" style="font-size:.7rem">Status:</span>
            <span class="small d-flex align-items-center" style="font-size:.7rem"><span class="legend-dot" style="background:var(--success)"></span>Tersedia</span>
            <span class="small d-flex align-items-center" style="font-size:.7rem"><span class="legend-dot" style="background:var(--warning)"></span>Pending</span>
            <span class="small d-flex align-items-center" style="font-size:.7rem"><span class="legend-dot" style="background:var(--danger)"></span>Dipesan</span>
            <span class="small d-flex align-items-center" style="font-size:.7rem"><span class="legend-dot" style="background:#6b7280"></span>Ditutup</span>
        </div>
    </div>

    <div id="scheduleContainer" data-schedule-grid>
    @if($jadwals->count())
    <div class="row g-2">
        @foreach($jadwals as $idx => $j)
        @php
            $isPast = \Carbon\Carbon::parse($tanggal.' '.$j->jam_mulai)->isPast();
            $label = match($j->status) {'tersedia'=>'Tersedia','dipesan'=>'Dipesan','pending'=>'Pending','ditutup'=>$j->keterangan?:'Ditutup',default=>$j->status};
        @endphp
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 anim" style="animation-delay:{{ $idx * 0.03 }}s">
            @if($j->status === 'tersedia' && !$isPast && auth()->check())
                <a href="{{ route('booking.create', ['lapangan_id' => $lapangan->id, 'tanggal' => $tanggal, 'jam_mulai' => substr($j->jam_mulai,0,5), 'jam_selesai' => substr($j->jam_selesai,0,5)]) }}" class="hour-card tersedia">
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
    <div class="bg-white border rounded-3 p-5 text-center shadow-sm anim">
        <i class="bi bi-calendar-x fs-2 text-muted d-block mb-2"></i>
        <p class="text-muted mb-1">Tidak ada jadwal untuk tanggal ini.</p>
        <a href="{{ route('jadwal.show', ['id' => $lapangan->id]) }}" class="btn btn-sm text-white rounded-pill" style="background:linear-gradient(135deg,var(--primary),var(--primary-dark))">Coba Hari Lain</a>
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
