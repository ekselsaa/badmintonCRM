<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $lapangan->nama_lapangan }} - Jadwal - Anbiyaa Sport</title>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%230ea5e9%22 stroke-width=%222.8%22><path d=%22M9 16c0 1.66 1.34 3 3 3s3-1.34 3-3%22 fill=%22%230ea5e9%22/></svg>">
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
* { box-sizing: border-box; }
body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    background: #f1f5f9;
    background-image: radial-gradient(at 40% 20%, #e0e7ff 0px, transparent 50%),
                      radial-gradient(at 80% 0%, #dbeafe 0px, transparent 50%);
    background-attachment: fixed;
    color: #1e293b; min-height: 100vh;
}

/* ── Topbar ── */
.top-nav {
    background: rgba(255,255,255,.88);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(226,232,240,.8);
    padding: .6rem 0;
    position: sticky; top: 0; z-index: 100;
    box-shadow: 0 2px 12px rgba(0,0,0,.03);
}

/* ── Hero Header ── */
.page-hero {
    background: linear-gradient(135deg, #0f172a 0%, #1e3a5f 50%, #1e293b 100%);
    border-radius: 20px;
    padding: 1.5rem 1.75rem;
    margin-bottom: 1rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px -8px rgba(15,23,42,.35);
}
.page-hero::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse at 80% 50%, rgba(37,99,235,.25) 0%, transparent 60%),
                radial-gradient(ellipse at 10% 80%, rgba(14,165,233,.15) 0%, transparent 50%);
    pointer-events: none;
}
.page-hero::after {
    content: '';
    position: absolute; top: -40px; right: -40px;
    width: 180px; height: 180px;
    background: radial-gradient(circle, rgba(37,99,235,.2) 0%, transparent 70%);
    border-radius: 50%; pointer-events: none;
}
.hero-icon {
    width: 48px; height: 48px;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem;
    backdrop-filter: blur(8px);
    flex-shrink: 0;
}
.page-hero h1 {
    font-size: 1.35rem; font-weight: 800;
    color: #fff; margin: 0; letter-spacing: -.4px; line-height: 1.2;
}
.page-hero .sub {
    font-size: .75rem; color: rgba(255,255,255,.55);
    margin: .2rem 0 0; font-weight: 500;
}
.hero-breadcrumb {
    font-size: .68rem; color: rgba(255,255,255,.4); font-weight: 500;
    display: flex; align-items: center; gap: 4px; margin-bottom: .5rem;
}
.hero-breadcrumb a { color: rgba(255,255,255,.5); text-decoration: none; transition: color .15s; }
.hero-breadcrumb a:hover { color: rgba(255,255,255,.8); }
.hero-slot-badge {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(16,185,129,.18);
    border: 1px solid rgba(16,185,129,.35);
    color: #6ee7b7;
    padding: .35rem .85rem;
    border-radius: 30px;
    font-size: .75rem; font-weight: 700;
    white-space: nowrap;
    backdrop-filter: blur(8px);
}
.hero-slot-badge .pulse {
    width: 7px; height: 7px;
    background: #10b981; border-radius: 50%;
    animation: pulse-live 1.5s ease-in-out infinite;
    flex-shrink: 0;
}
.hero-price-badge {
    display: inline-flex; align-items: center; gap: .4rem;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.15);
    color: rgba(255,255,255,.75);
    padding: .3rem .8rem;
    border-radius: 30px;
    font-size: .72rem; font-weight: 600;
    backdrop-filter: blur(8px);
}
@keyframes pulse-live {
    0%,100% { opacity:1; transform:scale(1); box-shadow: 0 0 0 0 rgba(16,185,129,.6); }
    50% { opacity:.7; transform:scale(1.2); box-shadow: 0 0 0 4px rgba(16,185,129,0); }
}

/* ── Filter card ── */
.filter-card {
    background: rgba(255,255,255,.92);
    backdrop-filter: blur(16px);
    border-radius: 16px;
    border: 1px solid rgba(226,232,240,.8);
    padding: 1rem 1.2rem;
    box-shadow: 0 4px 24px -4px rgba(0,0,0,.07);
}
.f-pill {
    font-size: .72rem; font-weight: 600; padding: .25rem .8rem;
    border-radius: 20px; text-decoration: none; transition: all .18s;
    white-space: nowrap; display: inline-flex; align-items: center; gap: 4px;
}
.f-pill.solid { background: var(--primary); color: #fff; box-shadow: 0 2px 8px rgba(37,99,235,.3); }
.f-pill:not(.solid) { background: #f8fafc; color: #475569; border: 1px solid #e2e8f0; }
.f-pill:not(.solid):hover { border-color: var(--primary); color: var(--primary); background: #eff6ff; }

.fc-custom {
    border-radius: 10px !important;
    font-size: .8rem !important;
    border-color: #e2e8f0 !important;
    background: #f8fafc !important;
    transition: all .18s !important;
}
.fc-custom:focus {
    border-color: var(--primary) !important;
    background: #fff !important;
    box-shadow: 0 0 0 3px rgba(37,99,235,.1) !important;
}

/* ── Lapangan block ── */
.lap-block {
    background: #fff; border-radius: 16px; overflow: hidden;
    box-shadow: 0 6px 30px -6px rgba(15,23,42,.14); border: 1px solid #e2e8f0;
}
.lap-block-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    padding: .85rem 1.25rem;
}
.lap-block-body { padding: .85rem; }

/* ── Slot cards ── */
.slot-card {
    border-radius: 10px; border: 1.5px solid #e2e8f0; background: #fff;
    overflow: hidden; display: flex; flex-direction: column;
    transition: all .18s ease; height: 100%;
    text-decoration: none; color: inherit;
}
.slot-card .s-top {
    font-size: .68rem; font-weight: 700; letter-spacing: .06em;
    text-transform: uppercase; padding: .25rem .4rem; text-align: center;
}
.slot-card .s-body {
    padding: .45rem .5rem .45rem;
    flex: 1; display: flex; flex-direction: column;
    align-items: center; text-align: center; gap: 0;
}
.slot-card .s-time {
    font-size: 1.15rem; font-weight: 800; line-height: 1.1; color: #0f172a;
    letter-spacing: -.5px; margin-top: .05rem;
}
.slot-card .s-until { font-size: .68rem; color: #64748b; font-weight: 500; }
.slot-card .s-action { width: 100%; margin-top: auto; padding-top: .3rem; }
.s-btn {
    display: block; width: 100%; text-align: center;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff; font-size: .73rem; font-weight: 700;
    padding: .3rem .35rem; border-radius: 7px;
    text-decoration: none; transition: opacity .15s; border: none;
}
.s-btn:hover { opacity: .88; color: #fff; }
.s-note { font-size: .73rem; color: #334155; font-weight: 600; margin-top: .15rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 100%; }
.s-finish { font-size: .68rem; color: #64748b; margin-top: auto; padding-top: .25rem; }

/* ── Status variants ── */
.slot-card.st-tersedia { border-color: #bbf7d0; }
.slot-card.st-tersedia .s-top { background: #d1fae5; color: #065f46; }
.slot-card.st-tersedia:hover { border-color: var(--success); box-shadow: 0 4px 18px rgba(16,185,129,.18); transform: translateY(-2px); }
.slot-card.st-lewat { opacity: .42; pointer-events: none; border-color: #f1f5f9; }
.slot-card.st-lewat .s-top { background: #f1f5f9; color: #94a3b8; }
.slot-card.st-lewat .s-time { color: #94a3b8; text-decoration: line-through; }
.slot-card.st-dipesan { border-color: #fecaca; opacity: .75; }
.slot-card.st-dipesan .s-top { background: #fee2e2; color: #991b1b; }
.slot-card.st-pending { border-color: #fde68a; opacity: .78; }
.slot-card.st-pending .s-top { background: #fef3c7; color: #92400e; }
.slot-card.st-ditutup { border-color: #f1f5f9; opacity: .6; }
.slot-card.st-ditutup .s-top { background: #f1f5f9; color: #64748b; }

/* ── Header badge ── */
.hdr-badge {
    background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
    border-radius: 20px; padding: .2rem .75rem;
    font-size: .72rem; font-weight: 700; color: #fff; white-space: nowrap;
}
.hdr-badge.green { background: rgba(16,185,129,.25); border-color: rgba(16,185,129,.45); color: #6ee7b7; }

/* ── Live dot ── */
.rt-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--success); animation: pulse-dot 2s infinite; display: inline-block; }
@keyframes pulse-dot { 0%,100%{opacity:1}50%{opacity:.3} }
@keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
.anim { animation: fadeUp .28s ease both; }

/* ── Custom grid: 8 per row ── */
.col-slot { flex: 0 0 auto; width: 12.5%; padding: .25rem; }
@media (max-width:1199px) { .col-slot { width: 16.666%; } }
@media (max-width:991px)  { .col-slot { width: 20%; } }
@media (max-width:767px)  { .col-slot { width: 25%; } }
@media (max-width:575px)  { .col-slot { width: 33.333%; } }

/* ── Footer ── */
.site-footer { background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-top: 1px solid rgba(255,255,255,.06); padding: 1.25rem 0; margin-top: 2.5rem; }
.site-footer .brand-text { color: #fff; font-weight: 800; font-size: 1rem; text-decoration: none; letter-spacing: -.3px; transition: opacity .2s; }
.site-footer .brand-text span { color: #0ea5e9; }
.site-footer .footer-link { width: 34px; height: 34px; border-radius: 50%; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,.6); font-size: .95rem; text-decoration: none; transition: all .2s; }
.site-footer .footer-link:hover { background: rgba(14,165,233,.2); border-color: rgba(14,165,233,.4); color: #0ea5e9; transform: translateY(-2px); }
.site-footer .footer-nav a { font-size: .72rem; color: rgba(255,255,255,.45); text-decoration: none; transition: color .2s; font-weight: 500; }
.site-footer .footer-nav a:hover { color: #0ea5e9; }
.site-footer .footer-divider { height: 1px; background: rgba(255,255,255,.06); margin: .85rem 0; }
.site-footer .copyright { font-size: .65rem; color: rgba(255,255,255,.25); margin: 0; }
</style>
</head>
<body>

<!-- ── Topbar ── -->
<div class="top-nav">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center gap-2">
            <i class="bi bi-trophy-fill" style="color:#0ea5e9;font-size:1.1rem"></i>
            <span class="fw-bold" style="color:#0f172a;font-size:.9rem;letter-spacing:-.3px">Anbiyaa Sport</span>
        </a>
        <div class="d-flex align-items-center gap-2">
            <span class="d-flex align-items-center gap-1" style="font-size:.65rem;color:#94a3b8"><span class="rt-dot"></span> Live</span>
            <a href="{{ route('jadwal.index', ['tanggal'=>$tanggal]) }}" class="btn btn-sm" style="border:1px solid #e2e8f0;color:#475569;font-size:.73rem;border-radius:8px;padding:.28rem .7rem">
                <i class="bi bi-grid me-1"></i>Semua
            </a>
            @auth
                <a href="{{ route('booking.create', ['lapangan_id'=>$lapangan->id]) }}" class="btn btn-sm text-white" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);font-size:.73rem;border-radius:8px;padding:.28rem .8rem;border:none">
                    <i class="bi bi-calendar-plus me-1"></i>Booking
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-sm text-white" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);font-size:.73rem;border-radius:8px;padding:.28rem .8rem;border:none">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                </a>
            @endauth
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-3">

    <!-- ── Hero Header ── -->
    @php
        $isWeekend = \Carbon\Carbon::parse($tanggal)->isWeekend();
        $slotTersedia = $jadwals->filter(fn($j) => $j->status==='tersedia' && !\Carbon\Carbon::parse($tanggal.' '.$j->jam_mulai)->isPast())->count();
        $hargaAktif = $isWeekend ? $lapangan->harga_weekend : $lapangan->harga_weekday;
        $hargaLabel  = $isWeekend ? 'Weekend' : 'Weekday';
    @endphp
    <div class="page-hero anim mb-3">
        <div class="d-flex align-items-center justify-content-between gap-3 position-relative flex-wrap" style="z-index:1">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon">
                    <i class="bi bi-geo-alt-fill" style="color:#7dd3fc"></i>
                </div>
                <div>
                    {{-- Breadcrumb --}}
                    <div class="hero-breadcrumb">
                        <a href="{{ route('jadwal.index', ['tanggal'=>$tanggal]) }}">Jadwal</a>
                        <i class="bi bi-chevron-right" style="font-size:.55rem"></i>
                        <span style="color:rgba(255,255,255,.65)">{{ $lapangan->nama_lapangan }}</span>
                    </div>
                    <h1>{{ $lapangan->nama_lapangan }}</h1>
                    <p class="sub">
                        <i class="bi bi-calendar-event me-1" style="color:#7dd3fc"></i>
                        {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                {{-- Harga badge --}}
                <div class="hero-price-badge">
                    <i class="bi bi-tag-fill" style="color:#7dd3fc;font-size:.7rem"></i>
                    {{ $hargaLabel }}: Rp {{ number_format($hargaAktif, 0, ',', '.') }}/jam
                </div>
                {{-- Slot badge --}}
                @if($slotTersedia > 0)
                <div class="hero-slot-badge">
                    <span class="pulse"></span>
                    {{ $slotTersedia }} slot tersedia
                </div>
                @else
                <div class="hero-slot-badge" style="background:rgba(239,68,68,.15);border-color:rgba(239,68,68,.3);color:#fca5a5;">
                    <i class="bi bi-x-circle" style="font-size:.75rem"></i>
                    Penuh hari ini
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- ── Filter ── -->
    <div class="filter-card mb-3 anim" style="animation-delay:.05s">
        <form method="GET" class="d-flex flex-wrap align-items-center gap-2">
            <input type="hidden" name="lapangan_id" value="{{ $lapangan->id }}">
            <input type="date" name="tanggal" class="form-control form-control-sm fc-custom" value="{{ $tanggal }}"
                   min="{{ date('Y-m-d') }}" onchange="this.form.submit()"
                   style="width:auto;min-width:140px">
            @php $today = now()->toDateString(); @endphp
            <a href="{{ route('jadwal.show', ['id'=>$lapangan->id,'tanggal'=>$today]) }}"
               class="f-pill {{ $tanggal===$today?'solid':'' }}">
                <i class="bi bi-calendar-check" style="font-size:.65rem"></i>Hari Ini
            </a>
            <a href="{{ route('jadwal.index',['tanggal'=>$tanggal]) }}" class="f-pill">
                <i class="bi bi-grid" style="font-size:.65rem"></i>Semua Lapangan
            </a>
            <button class="f-pill solid" type="submit">
                <i class="bi bi-search" style="font-size:.65rem"></i>Tampilkan
            </button>
            <div class="ms-auto d-none d-lg-flex flex-wrap align-items-center gap-2">
                <span style="font-size:.68rem;color:#64748b">Status:</span>
                @foreach([['#10b981','Tersedia'],['#f59e0b','Pending'],['#ef4444','Dipesan'],['#6b7280','Ditutup']] as [$c,$l])
                <span class="d-flex align-items-center gap-1" style="font-size:.68rem">
                    <span style="width:7px;height:7px;border-radius:50%;background:{{ $c }};display:inline-block"></span>{{ $l }}
                </span>
                @endforeach
            </div>
        </form>
    </div>

    <!-- ── Lapangan block ── -->
    <div id="scheduleContainer" data-schedule-grid>
    @if($jadwals->count())
    <div class="lap-block anim" style="animation-delay:.08s">
        <div class="lap-block-header d-flex align-items-center justify-content-between">
            <div>
                <div class="fw-bold text-white" style="font-size:.95rem;letter-spacing:-.2px">
                    <i class="bi bi-geo-alt-fill me-1" style="color:#0ea5e9;font-size:.8rem"></i>{{ $lapangan->nama_lapangan }}
                </div>
                <div style="font-size:.68rem;color:#64748b;margin-top:2px">
                    Weekday: Rp{{ number_format($lapangan->harga_weekday,0,',','.') }} &bull; Weekend: Rp{{ number_format($lapangan->harga_weekend,0,',','.') }}/jam
                </div>
            </div>
            <div class="hdr-badge {{ $slotTersedia>0?'green':'' }}">
                {{ $slotTersedia>0 ? $slotTersedia.' slot tersedia' : 'Penuh' }}
            </div>
        </div>
        <div class="lap-block-body">
            <div class="d-flex flex-wrap" style="margin:-0.25rem">
                @foreach($jadwals as $idx => $j)
                @include('jadwal._slot_card', ['j'=>$j, 'tanggal'=>$tanggal, 'lapId'=>$lapangan->id])
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-5">
        <div style="width:56px;height:56px;border-radius:14px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
            <i class="bi bi-calendar-x fs-4 text-muted"></i>
        </div>
        <p class="text-muted mb-1" style="font-size:.85rem">Tidak ada jadwal untuk tanggal ini.</p>
        <a href="{{ route('jadwal.show', ['id'=>$lapangan->id]) }}" class="btn btn-sm text-white" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);border-radius:8px;border:none">Coba Hari Lain</a>
    </div>
    @endif
    </div>

</div>

<!-- ── Footer ── -->
<footer class="site-footer">
    <div class="container-fluid px-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <a href="{{ route('home') }}" class="brand-text d-flex align-items-center gap-2 mb-1">
                    <i class="bi bi-trophy-fill" style="color:#0ea5e9;font-size:1rem"></i>
                    Anbiyaa<span>Sport</span>
                </a>
                <p style="font-size:.68rem;color:rgba(255,255,255,.35);margin:0">Booking Lapangan Bulutangkis — Makassar</p>
            </div>
            <div class="footer-nav d-flex align-items-center gap-3 flex-wrap">
                <a href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('jadwal.index') }}">Jadwal</a>
                @auth <a href="{{ route('booking.index') }}">Booking Saya</a> @endauth
                @guest <a href="{{ route('register') }}">Daftar</a> @endguest
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="https://www.instagram.com/goranbiyaa_01" target="_blank" class="footer-link"><i class="bi bi-instagram"></i></a>
                <a href="https://wa.me/6289529508023" target="_blank" class="footer-link"><i class="bi bi-whatsapp"></i></a>
            </div>
        </div>
        <div class="footer-divider"></div>
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">
            <p class="copyright">© {{ date('Y') }} Anbiyaa Sport. Semua Hak Dilindungi.</p>
            <p class="copyright">Jl. Berua Raya, Daya, Biringkanaya, Makassar</p>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Auto-refresh dinonaktifkan
</script>
</body>
</html>
