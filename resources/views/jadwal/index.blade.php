<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Jadwal Lapangan - Anbiyaa Sport</title>
<link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%230ea5e9%22 stroke-width=%222.8%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M9 16c0 1.66 1.34 3 3 3s3-1.34 3-3%22 fill=%22%230ea5e9%22 stroke=%22%230ea5e9%22 stroke-width=%222.8%22/><path d=%22M8 14.5h8%22 stroke=%22%232563eb%22 stroke-width=%223%22/><path d=%22M7.5 13.5L5 5%22 stroke-width=%222.8%22/><path d=%22M12 13.5V4%22 stroke-width=%222.8%22/><path d=%22M16.5 13.5L19 5%22 stroke-width=%222.8%22/><path d=%22M6 9.5h12%22 stroke-width=%221.8%22 opacity=%220.75%22/></svg>">
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
    color: #1e293b;
    min-height: 100vh;
}

/* ── Topbar ── */
.top-nav {
    background: rgba(8, 12, 30, 0.88);
    backdrop-filter: blur(20px);
    border-bottom: 1px solid rgba(255,255,255,0.06);
    padding: .65rem 0;
    position: sticky; top: 0; z-index: 100;
    box-shadow: 0 4px 20px rgba(0,0,0,.15);
}
.rt-dot {
    width: 6px; height: 6px; border-radius: 50%; background: #10b981;
    display: inline-block; box-shadow: 0 0 0 0 rgba(16,185,129,0.7);
    animation: live-pulse 2s infinite;
}
@keyframes live-pulse {
    0%   { box-shadow: 0 0 0 0 rgba(16,185,129,0.7); }
    70%  { box-shadow: 0 0 0 5px rgba(16,185,129,0); }
    100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
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
    position: absolute;
    top: -40px; right: -40px;
    width: 180px; height: 180px;
    background: radial-gradient(circle, rgba(37,99,235,.2) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}
.page-hero .hero-icon {
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
    color: #fff; margin: 0;
    letter-spacing: -.4px; line-height: 1.2;
}
.page-hero .sub {
    font-size: .82rem; color: rgba(255,255,255,.85);
    margin: .25rem 0 0; font-weight: 600;
}
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
    background: #10b981;
    border-radius: 50%;
    animation: pulse-live 1.5s ease-in-out infinite;
    flex-shrink: 0;
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
.filter-divider {
    width: 1px; background: #e2e8f0;
    align-self: stretch; margin: 0 .25rem;
}
.f-pill {
    font-size: .75rem; font-weight: 700; padding: .4rem 1rem;
    border-radius: 50px; text-decoration: none; transition: all .25s cubic-bezier(0.4, 0, 0.2, 1);
    white-space: nowrap; display: inline-flex; align-items: center; gap: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.03);
}
/* Default Active / Solid style */
.f-pill.solid {
    background: linear-gradient(135deg, #2563eb, #0ea5e9);
    color: #fff !important;
    box-shadow: 0 4px 12px rgba(37,99,235,0.3);
    border: none;
}
.f-pill.solid:hover {
    transform: translateY(-1.5px);
    box-shadow: 0 6px 16px rgba(37,99,235,0.45);
    color: #fff !important;
}

/* Custom Active Colors for Statuses */
.f-pill.solid.pill-tersedia {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 4px 12px rgba(16,185,129,0.3);
}
.f-pill.solid.pill-tersedia:hover {
    box-shadow: 0 6px 16px rgba(16,185,129,0.45);
}

.f-pill.solid.pill-pending {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    box-shadow: 0 4px 12px rgba(245,158,11,0.3);
}
.f-pill.solid.pill-pending:hover {
    box-shadow: 0 6px 16px rgba(245,158,11,0.45);
}

.f-pill.solid.pill-dipesan {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    box-shadow: 0 4px 12px rgba(239,68,68,0.3);
}
.f-pill.solid.pill-dipesan:hover {
    box-shadow: 0 6px 16px rgba(239,68,68,0.45);
}

.f-pill.solid.pill-ditutup {
    background: linear-gradient(135deg, #6b7280, #4b5563);
    box-shadow: 0 4px 12px rgba(107,114,128,0.3);
}
.f-pill.solid.pill-ditutup:hover {
    box-shadow: 0 6px 16px rgba(107,114,128,0.45);
}

/* Default Inactive style */
.f-pill:not(.solid) {
    background: #ffffff;
    color: #475569;
    border: 1.5px solid #e2e8f0;
}
.f-pill:not(.solid):hover {
    transform: translateY(-1.5px);
}

/* Custom Inactive Colors for Statuses */
.f-pill:not(.solid).pill-semua, .f-pill:not(.solid).pill-hari-ini {
    background: #eff6ff;
    color: #2563eb;
    border-color: #bfdbfe;
}
.f-pill:not(.solid).pill-semua:hover, .f-pill:not(.solid).pill-hari-ini:hover {
    background: #dbeafe;
    border-color: #3b82f6;
    color: #1d4ed8;
    box-shadow: 0 4px 12px rgba(37,99,235,0.15);
}

.f-pill:not(.solid).pill-tersedia {
    background: #e6fbf1;
    color: #047857;
    border-color: #a7f3d0;
}
.f-pill:not(.solid).pill-tersedia:hover {
    background: #d1fae5;
    border-color: #34d399;
    color: #065f46;
    box-shadow: 0 4px 12px rgba(16,185,129,0.15);
}

.f-pill:not(.solid).pill-pending {
    background: #fffbeb;
    color: #b45309;
    border-color: #fde68a;
}
.f-pill:not(.solid).pill-pending:hover {
    background: #fef3c7;
    border-color: #fbbf24;
    color: #92400e;
    box-shadow: 0 4px 12px rgba(245,158,11,0.15);
}

.f-pill:not(.solid).pill-dipesan {
    background: #fef2f2;
    color: #b91c1c;
    border-color: #fecaca;
}
.f-pill:not(.solid).pill-dipesan:hover {
    background: #fee2e2;
    border-color: #fca5a5;
    color: #991b1b;
    box-shadow: 0 4px 12px rgba(239,68,68,0.15);
}

.f-pill:not(.solid).pill-ditutup {
    background: #f8fafc;
    color: #475569;
    border-color: #cbd5e1;
}
.f-pill:not(.solid).pill-ditutup:hover {
    background: #e2e8f0;
    border-color: #94a3b8;
    color: #334155;
    box-shadow: 0 4px 12px rgba(107,114,128,0.15);
}

.f-pill.solid .dot {
    background: #ffffff !important;
}

.dot { width: 7px; height: 7px; border-radius: 50%; display: inline-block; flex-shrink: 0; }

/* ── Form controls ── */
.fc-custom {
    border-radius: 50px !important;
    font-size: .8rem !important;
    font-weight: 700 !important;
    color: #1e293b !important;
    border: 1.5px solid #cbd5e1 !important;
    background: #ffffff !important;
    transition: all .25s ease !important;
    padding-left: 1.1rem !important;
    padding-right: 1.1rem !important;
    height: 38px !important;
    display: inline-flex !important;
    align-items: center !important;
}
.fc-custom:focus, .fc-custom:hover {
    border-color: #2563eb !important;
    background: #fff !important;
    box-shadow: 0 0 0 3px rgba(37,99,235,.15) !important;
}

/* ── Guest info bar ── */
.guest-bar {
    background: linear-gradient(135deg, #eff6ff 0%, #e0f2fe 100%);
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    padding: .7rem 1rem;
    font-size: .78rem;
    display: flex; align-items: center; gap: .6rem;
    margin-bottom: 1rem;
}
.guest-bar .gi-icon {
    width: 30px; height: 30px;
    background: #2563eb;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: .85rem; flex-shrink: 0;
}
.guest-bar a { color: #1d4ed8; font-weight: 700; text-decoration: none; transition: color .15s; }
.guest-bar a:hover { color: #1e40af; text-decoration: underline; }

/* ── Lapangan block ── */
.lap-block {
    background: #fff;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 1.25rem;
    box-shadow: 0 6px 30px -6px rgba(15,23,42,.14);
    border: 1px solid #e2e8f0;
}
.lap-block-header {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    padding: .85rem 1.25rem;
    display: flex; align-items: center; justify-content: space-between;
    cursor: pointer; text-decoration: none;
    transition: opacity .15s;
}
.lap-block-header:hover { opacity: .92; }
.lap-block-body {
    padding: .85rem;
}

/* ── Slot cards ── */
.slot-wrap { padding: 0; }
.slot-card {
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    background: #fff;
    overflow: hidden;
    display: flex; flex-direction: column;
    transition: all .18s ease;
    height: 100%;
    text-decoration: none; color: inherit;
    position: relative;
}
.slot-card .s-top {
    font-size: .68rem; font-weight: 700; letter-spacing: .06em;
    text-transform: uppercase; padding: .25rem .4rem; text-align: center;
}
.slot-card .s-body {
    padding: .45rem .5rem .45rem;
    flex: 1; display: flex; flex-direction: column;
    align-items: center; text-align: center;
    gap: 0;
}
.slot-card .s-time-group {
    margin-top: auto; margin-bottom: auto;
    display: flex; flex-direction: column; align-items: center; gap: 2px;
}
.slot-card .s-time {
    font-size: 1.15rem; font-weight: 800; line-height: 1.1; color: #0f172a;
    letter-spacing: -.5px; margin-top: .05rem;
}
.slot-card .s-until {
    font-size: .68rem; color: #64748b; font-weight: 500;
}
.slot-card .s-action { width: 100%; margin-top: auto; padding-top: .3rem; }
.s-btn {
    display: block; width: 100%; text-align: center;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff; font-size: .73rem; font-weight: 700;
    padding: .3rem .35rem; border-radius: 7px;
    text-decoration: none; transition: opacity .15s; border: none;
}
.s-btn:hover { opacity: .88; color: #fff; }
.s-note {
    font-size: .73rem; color: #334155; font-weight: 600; margin-top: .15rem; overflow: hidden;
    text-overflow: ellipsis; white-space: nowrap; max-width: 100%;
}
.s-finish { font-size: .68rem; color: #64748b; padding-top: .25rem; }

/* ── Status variants ── */
.slot-card.st-tersedia { border-color: #bbf7d0; }
.slot-card.st-tersedia .s-top { background: #d1fae5; color: #065f46; }
.slot-card.st-tersedia:hover {
    border-color: var(--success);
    box-shadow: 0 4px 18px rgba(16,185,129,.18);
    transform: translateY(-2px);
}
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
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.2);
    border-radius: 20px;
    padding: .2rem .75rem;
    font-size: .72rem; font-weight: 700; color: #fff;
    white-space: nowrap;
}
.hdr-badge.green { background: rgba(16,185,129,.25); border-color: rgba(16,185,129,.45); color: #6ee7b7; }

/* ── Footer ── */
.site-footer {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-top: 1px solid rgba(255,255,255,.06);
    padding: 1.25rem 0;
    margin-top: 2.5rem;
}
.site-footer .brand-text {
    color: #fff; font-weight: 800; font-size: 1rem;
    text-decoration: none; letter-spacing: -.3px; transition: opacity .2s;
}
.site-footer .brand-text:hover { opacity: .85; }
.site-footer .brand-text span { color: #0ea5e9; }
.site-footer .footer-link {
    width: 34px; height: 34px; border-radius: 50%;
    background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1);
    display: flex; align-items: center; justify-content: center;
    color: rgba(255,255,255,.6); font-size: .95rem;
    text-decoration: none; transition: all .2s;
}
.site-footer .footer-link:hover {
    background: rgba(14,165,233,.2); border-color: rgba(14,165,233,.4);
    color: #0ea5e9; transform: translateY(-2px);
}
.site-footer .footer-nav a {
    font-size: .72rem; color: rgba(255,255,255,.45); text-decoration: none;
    transition: color .2s; font-weight: 500;
}
.site-footer .footer-nav a:hover { color: #0ea5e9; }
.site-footer .footer-divider {
    height: 1px; background: rgba(255,255,255,.06);
    margin: .85rem 0;
}
.site-footer .copyright {
    font-size: .65rem; color: rgba(255,255,255,.25); margin: 0;
}
@keyframes pulse-dot { 0%,100%{opacity:1}50%{opacity:.3} }
@keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
.anim { animation: fadeUp .28s ease both; }

/* ── Custom grid for 8-col ── */
.col-slot { flex: 0 0 auto; width: 12.5%; padding: .25rem; }
@media (max-width:1199px) { .col-slot { width: 16.666%; } }
@media (max-width:991px)  { .col-slot { width: 20%; } }
@media (max-width:767px)  { .col-slot { width: 25%; } }
@media (max-width:575px)  { .col-slot { width: 33.333%; } }
</style>
</head>
<body>

<!-- ── Topbar ── -->
<div class="top-nav">
    <div class="container-fluid px-4 d-flex justify-content-between align-items-center">
        <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" class="brand-icon-svg" style="width: 1.5rem; height: 1.5rem; display: inline-block; vertical-align: middle; filter: drop-shadow(0 0 8px rgba(14, 165, 233, 0.6));">
                <path d="M9 16c0 1.66 1.34 3 3 3s3-1.34 3-3" fill="#0ea5e9" stroke="#0ea5e9" stroke-width="2.8"/>
                <path d="M8 14.5h8" stroke="#2563eb" stroke-width="3"/>
                <path d="M7.5 13.5L5 5" stroke-width="2.8"/>
                <path d="M12 13.5V4" stroke-width="2.8"/>
                <path d="M16.5 13.5L19 5" stroke-width="2.8"/>
                <path d="M6 9.5h12" stroke-width="1.8" opacity="0.75"/>
            </svg>
            <span class="fw-bold" style="color:#fff;font-size:1.1rem;letter-spacing:-.4px">Anbiyaa<span style="color:#0ea5e9">Sport</span></span>
        </a>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('home') }}" class="btn btn-sm" style="border:1px solid rgba(255,255,255,0.15);color:rgba(255,255,255,0.73);font-size:.75rem;border-radius:50px;padding:.33rem .9rem;transition:all 0.2s;background:rgba(255,255,255,0.05)" onmouseover="this.style.color='#fff';this.style.borderColor='rgba(255,255,255,0.4)';this.style.background='rgba(255,255,255,0.15)'" onmouseout="this.style.color='rgba(255,255,255,0.73)';this.style.borderColor='rgba(255,255,255,0.15)';this.style.background='rgba(255,255,255,0.05)'"><i class="bi bi-house me-1"></i>Beranda</a>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-sm text-white" style="background:linear-gradient(135deg,#2563eb,#0ea5e9);font-size:.75rem;border-radius:50px;padding:.35rem 1.15rem;border:none;font-weight:700;box-shadow:0 4px 12px rgba(37,99,235,0.35);transition:all 0.25s" onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 16px rgba(37,99,235,0.5)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 12px rgba(37,99,235,0.35)'">
                        <i class="bi bi-speedometer2 me-1"></i>Panel Admin
                    </a>
                @else
                    <a href="{{ route('booking.create') }}" class="btn btn-sm text-white" style="background:linear-gradient(135deg,#2563eb,#0ea5e9);font-size:.75rem;border-radius:50px;padding:.35rem 1.15rem;border:none;font-weight:700;box-shadow:0 4px 12px rgba(37,99,235,0.35);transition:all 0.25s" onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 16px rgba(37,99,235,0.5)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 12px rgba(37,99,235,0.35)'">
                        <i class="bi bi-calendar-plus me-1"></i>Booking
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="btn btn-sm text-white" style="background:linear-gradient(135deg,#2563eb,#0ea5e9);font-size:.75rem;border-radius:50px;padding:.35rem 1.15rem;border:none;font-weight:700;box-shadow:0 4px 12px rgba(37,99,235,0.35);transition:all 0.25s" onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 6px 16px rgba(37,99,235,0.5)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 12px rgba(37,99,235,0.35)'">
                    <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
                </a>
            @endauth
        </div>
    </div>
</div>

<div class="container-fluid px-4 py-3">

    <!-- ── Hero Page Header ── -->
    <div class="page-hero anim mb-3">
        <div class="d-flex align-items-center justify-content-between gap-3 position-relative" style="z-index:1">
            <div class="d-flex align-items-center gap-3">
                <div class="hero-icon">
                    <i class="bi bi-calendar3" style="color:#7dd3fc"></i>
                </div>
                <div>
                    <h1>Jadwal Lapangan</h1>
                    <p class="sub"><i class="bi bi-calendar-event me-1" style="color:#7dd3fc"></i>{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}</p>
                </div>
            </div>
            @if($totalSlotTersedia > 0)
            <div class="hero-slot-badge">
                <span class="pulse"></span>
                {{ $totalSlotTersedia }} slot tersedia
            </div>
            @endif
        </div>
    </div>

    <!-- ── Filter ── -->
    <div class="filter-card mb-3 anim" style="animation-delay:.05s">
        <form method="GET" id="filterForm">
            <div class="d-flex flex-wrap align-items-center gap-2">
                {{-- Date picker --}}
                <input type="date" name="tanggal" class="form-control form-control-sm fc-custom" value="{{ $tanggal }}"
                       min="{{ date('Y-m-d') }}" onchange="this.form.submit()"
                       style="width:auto;min-width:140px">

                {{-- Lapangan select --}}
                <select name="lapangan_id" class="form-select form-select-sm fc-custom" onchange="this.form.submit()"
                        style="width:auto;min-width:155px">
                    <option value="">Semua Lapangan</option>
                    @foreach($lapangans as $l)
                    <option value="{{ $l->id }}" {{ $lapangan_id == $l->id ? 'selected' : '' }}>{{ $l->nama_lapangan }}</option>
                    @endforeach
                </select>

                {{-- Quick date buttons --}}
                @php $today = now()->toDateString(); @endphp
                <a href="{{ route('jadwal.index', array_merge(request()->query(), ['tanggal'=>$today])) }}"
                   class="f-pill pill-hari-ini {{ $tanggal===$today?'solid':'' }}">
                    <i class="bi bi-calendar-check" style="font-size:.65rem"></i>Hari Ini
                </a>
                <a href="{{ route('jadwal.index') }}" class="f-pill pill-ditutup">
                    <i class="bi bi-arrow-counterclockwise" style="font-size:.65rem"></i>Reset
                </a>

                {{-- Divider --}}
                <div class="filter-divider d-none d-md-block"></div>

                {{-- Status filters --}}
                <div class="d-flex flex-wrap gap-1">
                    <a href="{{ route('jadwal.index', array_merge(request()->except('status'))) }}"
                       class="f-pill pill-semua {{ !$status_filter?'solid':'' }}">Semua</a>
                    <a href="{{ route('jadwal.index', array_merge(request()->query(),['status'=>'tersedia'])) }}"
                       class="f-pill pill-tersedia {{ $status_filter==='tersedia'?'solid':'' }}">
                        <span class="dot" style="background:#10b981"></span>Tersedia
                    </a>
                    <a href="{{ route('jadwal.index', array_merge(request()->query(),['status'=>'pending'])) }}"
                       class="f-pill pill-pending {{ $status_filter==='pending'?'solid':'' }}">
                        <span class="dot" style="background:#f59e0b"></span>Pending
                    </a>
                    <a href="{{ route('jadwal.index', array_merge(request()->query(),['status'=>'dipesan'])) }}"
                       class="f-pill pill-dipesan {{ $status_filter==='dipesan'?'solid':'' }}">
                        <span class="dot" style="background:#ef4444"></span>Dipesan
                    </a>
                    <a href="{{ route('jadwal.index', array_merge(request()->query(),['status'=>'ditutup'])) }}"
                       class="f-pill pill-ditutup {{ $status_filter==='ditutup'?'solid':'' }}">
                        <span class="dot" style="background:#6b7280"></span>Ditutup
                    </a>
                </div>
            </div>
        </form>
    </div>



    <!-- ── Schedule ── -->
    <div id="scheduleContainer" data-schedule-grid>
    @if($jadwals->count() > 0)

    {{-- Single lapangan mode --}}
    @if($lapangan_id)
    @php
        $firstLap = null;
        foreach($jadwalPerLapangan as $slots) { $firstLap = $slots->first()->lapangan ?? null; break; }
        $tc = collect($jadwals)->filter(fn($j) => $j->status==='tersedia' && !\Carbon\Carbon::parse($tanggal.' '.$j->jam_mulai)->isPast())->count();
    @endphp
    @if($firstLap)
    <div class="lap-block anim">
        <a href="{{ route('jadwal.show', ['id'=>$firstLap->id,'tanggal'=>$tanggal]) }}" class="lap-block-header">
            <div>
                <div class="fw-bold text-white mb-0" style="font-size:.95rem;letter-spacing:-.2px">
                    <i class="bi bi-geo-alt-fill me-1" style="color:#0ea5e9;font-size:.8rem"></i>{{ $firstLap->nama_lapangan }}
                </div>
                <div style="font-size:.68rem;color:#64748b;margin-top:2px">
                    Weekday: Rp{{ number_format($firstLap->harga_weekday,0,',','.') }} &bull; Weekend: Rp{{ number_format($firstLap->harga_weekend,0,',','.') }}/jam
                </div>
            </div>
            <div class="hdr-badge {{ $tc>0?'green':'' }}">{{ $tc>0 ? $tc.' slot tersedia' : 'Penuh' }}</div>
        </a>
        <div class="lap-block-body">
            <div class="d-flex flex-wrap" style="margin:-0.25rem">
                @foreach($jadwals as $idx => $j)
                @include('jadwal._slot_card', ['j'=>$j, 'tanggal'=>$tanggal, 'lapId'=>$firstLap->id])
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- All lapangan mode --}}
    @else
    @foreach($jadwalPerLapangan as $lapId => $slots)
    @php
        $lapInfo = $slots->first()->lapangan ?? null;
        if (!$lapInfo) continue;
        $tc = $slots->filter(fn($s) => $s->status==='tersedia' && !\Carbon\Carbon::parse($tanggal.' '.$s->jam_mulai)->isPast())->count();
    @endphp
    <div class="lap-block anim" style="animation-delay:{{ $loop->index*0.06 }}s">
        <a href="{{ route('jadwal.show', ['id'=>$lapInfo->id,'tanggal'=>$tanggal]) }}" class="lap-block-header">
            <div>
                <div class="fw-bold text-white mb-0" style="font-size:.95rem;letter-spacing:-.2px">
                    <i class="bi bi-geo-alt-fill me-1" style="color:#0ea5e9;font-size:.8rem"></i>{{ $lapInfo->nama_lapangan }}
                </div>
                <div style="font-size:.68rem;color:#64748b;margin-top:2px">
                    Weekday: Rp{{ number_format($lapInfo->harga_weekday,0,',','.') }} &bull; Weekend: Rp{{ number_format($lapInfo->harga_weekend,0,',','.') }}/jam
                </div>
            </div>
            <div class="hdr-badge {{ $tc>0?'green':'' }}">{{ $tc>0 ? $tc.' slot tersedia' : 'Penuh' }}</div>
        </a>
        <div class="lap-block-body">
            <div class="d-flex flex-wrap" style="margin:-0.25rem">
                @foreach($slots as $idx => $j)
                @include('jadwal._slot_card', ['j'=>$j, 'tanggal'=>$tanggal, 'lapId'=>$lapInfo->id])
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
    @endif

    @else
    <div class="text-center py-5">
        <div style="width:56px;height:56px;border-radius:14px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
            <i class="bi bi-calendar-x fs-4 text-muted"></i>
        </div>
        @if($status_filter)
            <p class="text-muted mb-1" style="font-size:.85rem">Tidak ada slot dengan status <strong>{{ $status_filter }}</strong> pada tanggal ini.</p>
        @else
            <p class="text-muted mb-1" style="font-size:.85rem">Tidak ada jadwal pada tanggal ini.</p>
        @endif
        <div class="mt-3 d-flex justify-content-center gap-2">
            <a href="{{ route('jadwal.index') }}" class="btn btn-sm text-white" style="background:linear-gradient(135deg,#2563eb,#0ea5e9);border-radius:50px;border:none;padding:.45rem 1.25rem;font-weight:700;box-shadow:0 4px 12px rgba(37,99,235,0.3);transition:all 0.25s" onmouseover="this.style.transform='translateY(-1.5px)';this.style.boxShadow='0 6px 16px rgba(37,99,235,0.45)'" onmouseout="this.style.transform='none';this.style.boxShadow='0 4px 12px rgba(37,99,235,0.3)'">Reset Filter</a>
        </div>
    </div>
    @endif
    </div>

</div><!-- end container-fluid -->

<!-- ── Footer ── -->
<footer class="site-footer">
    <div class="container-fluid px-4">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <a href="{{ route('home') }}" class="brand-text d-flex align-items-center gap-2 mb-1" style="color:#fff;font-weight:800;font-size:1.05rem;text-decoration:none;letter-spacing:-.3px;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" class="brand-icon-svg" style="width: 1.25rem; height: 1.25rem; display: inline-block; vertical-align: middle; filter: drop-shadow(0 0 6px rgba(14, 165, 233, 0.5));">
                        <path d="M9 16c0 1.66 1.34 3 3 3s3-1.34 3-3" fill="#0ea5e9" stroke="#0ea5e9" stroke-width="2.8"/>
                        <path d="M8 14.5h8" stroke="#2563eb" stroke-width="3"/>
                        <path d="M7.5 13.5L5 5" stroke-width="2.8"/>
                        <path d="M12 13.5V4" stroke-width="2.8"/>
                        <path d="M16.5 13.5L19 5" stroke-width="2.8"/>
                        <path d="M6 9.5h12" stroke-width="1.8" opacity="0.75"/>
                    </svg>
                    Anbiyaa<span style="color:#0ea5e9">Sport</span>
                </a>
                <p style="font-size:.68rem;color:rgba(255,255,255,.35);margin:0">Booking Lapangan Bulutangkis — Makassar</p>
            </div>
            <div class="footer-nav d-flex align-items-center gap-3 flex-wrap">
                <a href="{{ route('home') }}">Beranda</a>
                <a href="{{ route('jadwal.index') }}">Jadwal</a>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}">Panel Admin</a>
                    @else
                        <a href="{{ route('booking.index') }}">Booking Saya</a>
                    @endif
                @endauth
                @guest <a href="{{ route('register') }}">Daftar</a> @endguest
            </div>
            <div class="d-flex align-items-center gap-2">
                <a href="https://www.instagram.com/goranbiyaa_01" target="_blank" class="footer-link" title="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="https://wa.me/6289529508023" target="_blank" class="footer-link" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
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
// Auto-refresh dinonaktifkan (tidak diperlukan)
</script>
</body>
</html>
