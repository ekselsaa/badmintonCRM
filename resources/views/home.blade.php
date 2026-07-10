<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anbiyaa Sport – Booking Lapangan Bulutangkis Makassar</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%230ea5e9%22 stroke-width=%222.8%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M9 16c0 1.66 1.34 3 3 3s3-1.34 3-3%22 fill=%22%230ea5e9%22 stroke=%22%230ea5e9%22 stroke-width=%222.8%22/><path d=%22M8 14.5h8%22 stroke=%22%232563eb%22 stroke-width=%223%22/><path d=%22M7.5 13.5L5 5%22 stroke-width=%222.8%22/><path d=%22M12 13.5V4%22 stroke-width=%222.8%22/><path d=%22M16.5 13.5L19 5%22 stroke-width=%222.8%22/><path d=%22M6 9.5h12%22 stroke-width=%221.8%22 opacity=%220.75%22/></svg>">
    <meta name="description" content="Booking lapangan bulutangkis online di Anbiyaa Sport Makassar. Lihat jadwal real-time, pilih slot, dan konfirmasi dalam hitungan detik.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --navy:     #0f172a;
            --navy2:    #1e293b;
            --blue:     #2563eb;
            --blue-l:   #3b82f6;
            --sky:      #0ea5e9;
            --emerald:  #10b981;
            --amber:    #f59e0b;
            --red:      #ef4444;
            --s50:      #f8fafc;
            --s100:     #f1f5f9;
            --s200:     #e2e8f0;
            --s400:     #94a3b8;
            --s500:     #64748b;
            --s700:     #334155;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { scroll-behavior: smooth; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #fff; color: var(--navy); }

        /* ══ NAVBAR ══ */
        .navbar-custom {
            background: rgba(8, 12, 30, 0.82);
            backdrop-filter: blur(22px) saturate(180%);
            -webkit-backdrop-filter: blur(22px) saturate(180%);
            padding: .7rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            transition: background 0.4s, box-shadow 0.4s;
        }
        .navbar-custom.scrolled {
            background: rgba(8, 12, 30, 0.97);
            box-shadow: 0 8px 32px rgba(0,0,0,.3);
        }
        .brand-text {
            color: #fff; font-weight: 800; font-size: 1.25rem;
            letter-spacing: -.5px; text-decoration: none; transition: opacity .3s;
        }
        .brand-text:hover { opacity: .85; color: #fff; }
        .brand-text span { color: var(--sky); }
        .nav-link-custom {
            color: rgba(255,255,255,.6); font-size: .85rem; font-weight: 600;
            text-decoration: none; padding: .4rem 0; position: relative; transition: color .3s;
        }
        .nav-link-custom::after {
            content: ''; position: absolute; bottom: 0; left: 0;
            width: 0; height: 2px; background: var(--sky);
            border-radius: 2px; transition: width .3s;
        }
        .nav-link-custom:hover { color: #fff; }
        .nav-link-custom:hover::after { width: 100%; }
        .btn-nav-cta {
            background: linear-gradient(135deg, var(--blue), var(--sky));
            color: #fff; border: none; font-weight: 700; font-size: .83rem;
            padding: .5rem 1.4rem; border-radius: 50px;
            transition: all .3s; box-shadow: 0 4px 15px rgba(37,99,235,.35);
            text-decoration: none; display: inline-flex; align-items: center; gap: .4rem;
        }
        .btn-nav-cta:hover { transform: translateY(-2px); color: #fff; box-shadow: 0 6px 22px rgba(37,99,235,.55); }
        .btn-nav-ghost {
            background: transparent; color: rgba(255,255,255,.5);
            border: 1px solid rgba(255,255,255,.15); font-size: .8rem;
            padding: .45rem 1rem; border-radius: 50px; font-weight: 500;
            transition: all .3s; cursor: pointer;
        }
        .btn-nav-ghost:hover { color: #f87171; border-color: rgba(248,113,113,.4); background: rgba(248,113,113,.08); }

        /* ══ HERO ══ */
        .hero {
            min-height: 100vh; display: flex; align-items: center;
            position: relative; overflow: hidden; background: var(--navy);
        }
        .hero-bg {
            position: absolute; inset: 0; z-index: 0;
            background-size: cover; background-position: center;
            transition: opacity .8s;
        }
        .hero-bg::after {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg,
                rgba(8,12,30,.80) 0%,
                rgba(15,23,42,.55) 50%,
                rgba(30,58,95,.45) 100%);
        }
        /* Animated mesh */
        .hero-mesh {
            position: absolute; inset: 0; z-index: 1; pointer-events: none;
            background:
                radial-gradient(ellipse at 15% 60%, rgba(14,165,233,.12) 0%, transparent 55%),
                radial-gradient(ellipse at 85% 15%, rgba(139,92,246,.1) 0%, transparent 50%),
                radial-gradient(ellipse at 50% 100%, rgba(37,99,235,.08) 0%, transparent 60%);
        }
        .hero .inner {
            position: relative; z-index: 2; width: 100%;
        }
        .hero-eyebrow {
            display: inline-flex; align-items: center; gap: .5rem;
            background: rgba(14,165,233,.18); color: #7dd3fc;
            border: 1px solid rgba(14,165,233,.4);
            padding: .55rem 1.5rem; border-radius: 50px;
            font-size: .88rem; font-weight: 700;
            backdrop-filter: blur(4px);
            animation: fadeDown .6s ease both;
            margin: 0 auto 1.5rem auto;
        }
        .hero-eyebrow .dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--sky);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .4; transform: scale(.7); }
        }
        .hero h1 {
            font-size: clamp(2.4rem, 5.5vw, 4.2rem);
            font-weight: 800; color: #fff; line-height: 1.25;
            letter-spacing: -0.5px;
            text-shadow: 0 4px 20px rgba(0,0,0,.4);
            animation: fadeUp .7s .1s ease both;
            text-align: center;
            margin-bottom: 1.75rem !important;
        }
        .hero h1 .accent { color: var(--sky); }
        .hero-sub {
            color: rgba(255,255,255,.7); font-size: 1.12rem; font-weight: 500;
            max-width: 680px; line-height: 1.8;
            animation: fadeUp .7s .2s ease both;
            margin: 0 auto 2.5rem auto;
            text-align: center;
        }
        .hero-actions { 
            animation: fadeUp .7s .3s ease both; 
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1.25rem;
            margin-bottom: 4rem !important;
        }
        .btn-primary-hero {
            background: linear-gradient(135deg, var(--blue), var(--sky));
            color: #fff; border: none; padding: .9rem 2.2rem;
            border-radius: 14px; font-weight: 700; font-size: 1rem;
            text-decoration: none; transition: all .25s;
            display: inline-flex; align-items: center; gap: .5rem;
            box-shadow: 0 6px 24px rgba(37,99,235,.45);
        }
        .btn-primary-hero:hover { opacity: .92; color: #fff; transform: translateY(-3px); box-shadow: 0 10px 32px rgba(37,99,235,.55); }
        .btn-outline-hero {
            background: rgba(255,255,255,.07); color: #e2e8f0;
            border: 1.5px solid rgba(255,255,255,.22);
            padding: .9rem 2rem; border-radius: 14px;
            font-weight: 600; font-size: 1rem;
            text-decoration: none; transition: all .25s;
            display: inline-flex; align-items: center; gap: .5rem;
            backdrop-filter: blur(6px);
        }
        .btn-outline-hero:hover { background: rgba(255,255,255,.14); color: #fff; border-color: rgba(255,255,255,.4); }

        /* Stats strip */
        .hero-stats {
            display: flex; gap: 0.85rem; flex-wrap: wrap;
            animation: fadeUp .7s .4s ease both;
            justify-content: center;
        }
        .hero-stat-pill {
            display: flex; align-items: center; gap: .6rem;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.1);
            backdrop-filter: blur(8px);
            border-radius: 50px; padding: .55rem 1.25rem;
            color: #fff; font-size: .8rem; font-weight: 600;
            text-decoration: none; transition: all .2s;
        }
        a.hero-stat-pill:hover {
            background: rgba(255,255,255,.16);
            border-color: rgba(255,255,255,.25);
            transform: translateY(-2px);
            color: #fff;
        }
        .hero-stat-pill .num { font-size: 1.05rem; font-weight: 800; color: var(--sky); }

        @keyframes fadeUp   { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:none; } }
        @keyframes fadeDown { from { opacity:0; transform:translateY(-10px); } to { opacity:1; transform:none; } }

        /* ══ SECTION COMMONS ══ */
        .section-pill {
            display: inline-block; font-size: .72rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 2px; color: var(--sky);
            background: rgba(14,165,233,.1); border: 1px solid rgba(14,165,233,.2);
            border-radius: 50px; padding: .3rem .9rem; margin-bottom: .75rem;
        }
        .section-h { font-size: clamp(1.7rem,3vw,2.4rem); font-weight: 800; color: var(--navy); letter-spacing: -.5px; margin-bottom: .6rem; }
        .section-sub { color: var(--s500); font-size: .97rem; }

        /* ══ KEUNGGULAN ══ */
        .feature-card {
            background: #fff; border-radius: 20px;
            border: 1.5px solid var(--s200);
            padding: 1.75rem; height: 100%;
            transition: all .3s cubic-bezier(.34,1.56,.64,1);
            position: relative; overflow: hidden;
        }
        .feature-card::before {
            content: ''; position: absolute; inset: 0;
            background: linear-gradient(135deg, transparent 60%, rgba(37,99,235,.03) 100%);
            border-radius: inherit; transition: opacity .3s;
            opacity: 0; pointer-events: none;
        }
        .feature-card:hover { transform: translateY(-8px); box-shadow: 0 24px 48px rgba(0,0,0,.09); border-color: transparent; }
        .feature-card:hover::before { opacity: 1; }
        .feat-icon {
            width: 58px; height: 58px; border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.6rem; margin-bottom: 1.25rem;
            transition: transform .3s;
        }
        .feature-card:hover .feat-icon { transform: scale(1.12) rotate(-4deg); }
        .feat-num {
            position: absolute; top: 1rem; right: 1.25rem;
            font-size: 4rem; font-weight: 900; color: rgba(0,0,0,.03);
            line-height: 1; letter-spacing: -3px; user-select: none;
        }

        /* ══ LAPANGAN / COURT CARD ══ */
        .court-card {
            border-radius: 20px; overflow: hidden;
            border: 1.5px solid var(--s200);
            background: #fff; height: 100%;
            transition: all .3s cubic-bezier(.34,1.56,.64,1);
        }
        .court-card:hover { transform: translateY(-6px); box-shadow: 0 20px 48px rgba(0,0,0,.1); border-color: transparent; }
        .court-header {
            padding: 1.5rem;
            background: linear-gradient(135deg, var(--navy), #1e3a5f);
            position: relative; overflow: hidden; min-height: 100px;
        }
        .court-header::after {
            content: '🏸';
            position: absolute; right: 1rem; bottom: -.3rem;
            font-size: 4rem; opacity: .07; line-height: 1;
        }
        .court-header.inactive { background: linear-gradient(135deg, #374151, #6b7280); }
        .court-ribbon {
            position: absolute; top: .75rem; right: .75rem;
            background: var(--red); color: #fff;
            font-size: .65rem; font-weight: 700; padding: .25rem .7rem;
            border-radius: 50px; letter-spacing: .5px; text-transform: uppercase;
            box-shadow: 0 2px 10px rgba(239,68,68,.4);
        }
        .court-price-tag {
            display: inline-flex; align-items: baseline; gap: .25rem;
        }
        .court-price-tag .amt { font-size: 1.3rem; font-weight: 800; color: var(--blue); }
        .court-price-tag .unit { font-size: .75rem; color: var(--s400); font-weight: 500; }
        .court-badge-aktif {
            display: inline-flex; align-items: center; gap: .3rem;
            background: rgba(34,197,94,.12); color: #16a34a;
            font-size: .72rem; font-weight: 700; padding: .2rem .7rem;
            border-radius: 50px;
        }

        /* ══ KELENGKAPAN ══ */
        .item-gear {
            display: flex; align-items: center; gap: .75rem;
            background: var(--s50); border: 1.5px solid var(--s200);
            border-radius: 14px; padding: .85rem 1rem;
            transition: all .25s;
        }
        .item-gear:hover { border-color: var(--blue-l); background: #eff6ff; transform: translateX(4px); }
        .item-gear .icon-wrap {
            width: 44px; height: 44px; flex-shrink: 0;
            background: #fff; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 2px 8px rgba(0,0,0,.07); font-size: 1.2rem;
            color: var(--blue);
        }
        .item-gear .name { font-size: .83rem; font-weight: 700; color: var(--navy); }
        .item-gear .price { font-size: .78rem; font-weight: 600; color: var(--blue); }

        /* Social pills */
        .social-pill {
            display: inline-flex; align-items: center; gap: .4rem;
            font-size: .78rem; font-weight: 600; padding: .45rem 1rem;
            border-radius: 50px; text-decoration: none; transition: all .2s;
        }
        .social-pill.whatsapp { background: #f0fdf4; border: 1px solid #dcfce7; color: #16a34a; }
        .social-pill.whatsapp:hover { background: #dcfce7; color: #15803d; }
        .social-pill.instagram { background: #fff1f2; border: 1px solid #fecdd3; color: #e11d48; }
        .social-pill.instagram:hover { background: #fecdd3; color: #be123c; }

        /* ══ TESTIMONI ══ */
        .testi-track-wrap { position: relative; }
        .testi-track-wrap::before,
        .testi-track-wrap::after {
            content: ''; position: absolute; top: 0; bottom: 0;
            width: 60px; z-index: 2; pointer-events: none;
        }
        .testi-track-wrap::before { left: 0; background: linear-gradient(to right, #f8fafc, transparent); }
        .testi-track-wrap::after  { right: 0; background: linear-gradient(to left, #f8fafc, transparent); }
        .testi-track {
            display: flex; gap: 1.25rem;
            overflow-x: auto; padding: .5rem .5rem 1.5rem;
            scroll-snap-type: x mandatory;
            scrollbar-width: thin; scrollbar-color: #cbd5e1 transparent;
        }
        .testi-track::-webkit-scrollbar { height: 6px; }
        .testi-track::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 20px; }
        .testi-card {
            flex: 0 0 300px; scroll-snap-align: start;
            background: #fff; border-radius: 18px;
            border: 1.5px solid var(--s200);
            padding: 1.5rem;
            transition: all .3s;
            display: flex; flex-direction: column;
        }
        .testi-card:hover { box-shadow: 0 12px 32px rgba(0,0,0,.08); border-color: var(--blue-l); transform: translateY(-4px); }
        .testi-quote { font-size: .92rem; color: var(--s500); font-style: italic; line-height: 1.7; flex: 1; }
        .testi-quote::before { content: '"'; font-size: 1.8rem; color: var(--sky); line-height: .5; display: block; margin-bottom: .5rem; font-style: normal; }
        .testi-stars { color: var(--amber); font-size: .85rem; }
        .testi-avatar {
            width: 42px; height: 42px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: .9rem; color: #fff; flex-shrink: 0;
        }
        @media (max-width: 576px) { .testi-card { flex: 0 0 82vw; } }

        /* ══ LOKASI ══ */
        .location-info {
            display: flex; align-items: flex-start; gap: 1rem;
            background: var(--s50); border: 1.5px solid var(--s200);
            border-radius: 16px; padding: 1rem 1.25rem;
            transition: all .2s;
        }
        .location-info:hover { border-color: var(--blue-l); background: #eff6ff; }
        .loc-icon {
            width: 46px; height: 46px; border-radius: 13px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center; font-size: 1.25rem;
        }
        .map-frame {
            border-radius: 20px; overflow: hidden;
            border: 6px solid var(--s100);
            box-shadow: 0 8px 32px rgba(0,0,0,.1);
            height: 420px;
        }
        @media (max-width: 992px) {
            .map-frame { height: 300px; }
        }

        /* ══ CTA ══ */
        .cta-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            position: relative; overflow: hidden;
        }
        .cta-section::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse at 15% 50%, rgba(14,165,233,.15) 0%, transparent 55%),
                radial-gradient(ellipse at 85% 30%, rgba(139,92,246,.12) 0%, transparent 50%);
        }
        .cta-section .inner { position: relative; z-index: 1; }

        /* ══ FOOTER ══ */
        .footer-dark {
            background: var(--navy); color: var(--s400);
            border-top: 1px solid rgba(255,255,255,.06);
        }
        .footer-link { color: var(--s400); text-decoration: none; font-size: .8rem; transition: color .2s; }
        .footer-link:hover { color: var(--sky); }

        /* ══ ANIMATIONS ON SCROLL ══ */
        .reveal {
            opacity: 0; transform: translateY(28px);
            transition: opacity .6s ease, transform .6s ease;
        }
        .reveal.visible { opacity: 1; transform: none; }
        .reveal-delay-1 { transition-delay: .1s; }
        .reveal-delay-2 { transition-delay: .2s; }
        .reveal-delay-3 { transition-delay: .3s; }

        /* ══ CRM REWARD CARD ══ */
        .crm-reward-card {
            background: rgba(255,255,255,.07);
            border: 1.5px solid rgba(255,255,255,.14);
            border-radius: 24px; padding: 1.75rem;
            backdrop-filter: blur(18px);
            -webkit-backdrop-filter: blur(18px);
            min-width: 290px; max-width: 330px;
            position: relative; overflow: hidden;
            animation: fadeUp .7s .35s ease both;
        }
        .crm-reward-card::before {
            content: '';
            position: absolute; top: -60px; right: -60px;
            width: 160px; height: 160px; border-radius: 50%;
            background: radial-gradient(circle, rgba(251,191,36,.15) 0%, transparent 70%);
            pointer-events: none;
        }
        .crm-reward-header {
            display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;
        }
        .crm-reward-icon-ring {
            width: 48px; height: 48px; border-radius: 14px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, rgba(251,191,36,.25), rgba(245,158,11,.15));
            border: 1px solid rgba(251,191,36,.3);
            font-size: 1.4rem; color: #fbbf24;
        }
        .crm-reward-badge {
            font-size: .68rem; font-weight: 700; letter-spacing: .5px;
            background: rgba(251,191,36,.15); border: 1px solid rgba(251,191,36,.25);
            color: #fbbf24; padding: .3rem .85rem; border-radius: 50px;
        }
        .crm-reward-title {
            color: #fff; font-weight: 800; font-size: 1.25rem;
            line-height: 1.25; margin-bottom: .6rem; letter-spacing: -.3px;
        }
        .crm-reward-title span { color: #fbbf24; }
        .crm-reward-sub {
            color: rgba(255,255,255,.5); font-size: .8rem; line-height: 1.6; margin-bottom: 1rem;
        }
        .crm-benefit-list {
            list-style: none; padding: 0; margin: 0 0 1.25rem;
            display: flex; flex-direction: column; gap: .65rem;
        }
        .crm-benefit-list li {
            display: flex; align-items: center; gap: .75rem;
        }
        .crm-benefit-icon {
            width: 34px; height: 34px; border-radius: 10px; flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            font-size: .85rem;
        }
        .crm-benefit-name {
            font-size: .8rem; font-weight: 700; color: #fff; line-height: 1.2;
        }
        .crm-benefit-desc { font-size: .7rem; color: rgba(255,255,255,.4); }
        .crm-reward-cta {
            display: flex; align-items: center; gap: .6rem;
            background: linear-gradient(135deg, #f59e0b, #fbbf24);
            color: var(--navy); font-weight: 800; font-size: .85rem;
            padding: .75rem 1.1rem; border-radius: 14px;
            text-decoration: none; transition: all .25s;
            box-shadow: 0 4px 18px rgba(245,158,11,.4);
            margin-bottom: .65rem;
        }
        .crm-reward-cta:hover {
            transform: translateY(-2px); color: var(--navy);
            box-shadow: 0 8px 28px rgba(245,158,11,.55);
        }
        .crm-reward-login-hint {
            text-align: center; font-size: .73rem; color: rgba(255,255,255,.35);
        }
        .crm-reward-login-hint a {
            color: var(--sky); text-decoration: none; font-weight: 600;
        }
        .crm-reward-login-hint a:hover { text-decoration: underline; }

        :root {
            --navbar-height: 72px; /* default fallback */
            --footer-height: 80px; /* default fallback */
        }

        /* ══ SECTION SCROLL FIT ══
         * scroll-margin-top = tinggi navbar agar section
         * selalu fit tepat di bawah navbar saat di-scroll via #anchor
         */
        section[id] {
            scroll-margin-top: var(--navbar-height);
            min-height: calc(100vh - var(--navbar-height));
            display: flex;
            flex-direction: column;
        }
        section[id] > .container {
            width: 100%;
            margin-top: auto;
            margin-bottom: auto;
            padding-top: 3.5rem;
            padding-bottom: 3.5rem;
        }
        /* Hero tidak perlu min-height override (sudah 100vh sendiri) */
        section#home { min-height: 100vh; }
        section#home > .container { padding-top: 0; padding-bottom: 0; }

        /* Khusus lokasi agar pas dengan sisa tinggi layar dan tidak terpotong */
        section#lokasi > .container {
            position: relative;
            top: -1.25rem; /* Naikkan sedikit seluruh kartu informasi dan peta */
        }

        /* Naikkan sedikit seluruh kartu lapangan dan judul agar lebih fit di layar */
        section#lapangan > .container {
            position: relative;
            top: -2.5rem;
        }

        /* ══ RESPONSIVE ══ */
        @media (max-width: 768px) {
            .hero h1 { letter-spacing: -.5px; }
            .hero-stats { gap: .4rem; }
            section[id] { min-height: auto; }
            section[id] > .container {
                padding-top: 2rem;
                padding-bottom: 2rem;
                top: 0 !important; /* Reset pergeseran relatif di mobile agar tidak tertutup navbar */
            }
        }

        /* Floating Loyalty Button Pulse Effect */
        #floatingLoyaltyBtn {
            animation: pulse-gold 2.5s infinite;
            border: 2px solid rgba(255, 255, 255, 0.8) !important;
            box-shadow: 0 8px 30px rgba(245, 158, 11, 0.45);
        }
        #floatingLoyaltyBtn:hover {
            transform: scale(1.1) translateY(-3px) !important;
            box-shadow: 0 12px 35px rgba(245, 158, 11, 0.65);
        }
        @keyframes pulse-gold {
            0% {
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.8);
                transform: scale(1);
            }
            70% {
                box-shadow: 0 0 0 15px rgba(245, 158, 11, 0);
                transform: scale(1.05);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
                transform: scale(1);
            }
        }

        /* CRM Modal — fit di layar dengan reward scroll */
        #crmPromoModal .modal-dialog {
            max-height: 92vh;
        }
        #crmPromoModal .modal-content {
            max-height: 92vh;
            display: flex;
            flex-direction: column;
        }
        #crmPromoModal .modal-body {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 0;
        }
        #crmPromoModal .crm-reward-list-wrap {
            flex: 1;
            min-height: 0;
            overflow-y: auto;
            padding-right: 4px;
        }

        .crm-reward-badge-pill {
            background: rgba(251, 191, 36, 0.12);
            border: 1px solid rgba(251, 191, 36, 0.25);
            color: #fbbf24;
            font-size: 0.7rem;
            font-weight: 800;
            padding: 0.25rem 0.6rem;
            border-radius: 50px;
            display: inline-block;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .crm-reward-micro-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.07);
            border-radius: 12px;
            padding: 0.7rem 1rem;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            flex-shrink: 0;
        }
        .crm-reward-micro-card:hover {
            background: rgba(255, 255, 255, 0.07);
            border-color: rgba(251, 191, 36, 0.35);
            box-shadow: 0 6px 20px rgba(9, 13, 22, 0.5);
            transform: translateY(-2px);
        }
        .crm-reward-icon-wrapper {
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .crm-reward-icon-wrapper.gray  { background: rgba(203,213,225,.08); border: 1px solid rgba(203,213,225,.15); color: #cbd5e1; }
        .crm-reward-icon-wrapper.amber { background: rgba(245,158,11,.08);  border: 1px solid rgba(245,158,11,.15);  color: #fbbf24; }
        .crm-reward-icon-wrapper.sky   { background: rgba(14,165,233,.08);  border: 1px solid rgba(14,165,233,.15);  color: #0ea5e9; }
        .crm-reward-icon-wrapper.yellow{ background: rgba(234,179,8,.08);   border: 1px solid rgba(234,179,8,.15);   color: #eab308; }
        .crm-reward-icon-wrapper.red   { background: rgba(239,68,68,.08);   border: 1px solid rgba(239,68,68,.15);   color: #ef4444; }
        .crm-reward-icon-wrapper.green { background: rgba(34,197,94,.08);   border: 1px solid rgba(34,197,94,.15);   color: #22c55e; }
        
        .crm-reward-title {
            font-weight: 700;
            color: #fff;
            font-size: 0.83rem;
            line-height: 1.3;
            margin-bottom: 0.1rem;
            letter-spacing: -0.1px;
        }
        .crm-reward-subtitle {
            color: rgba(255,255,255,.45);
            font-size: 0.7rem;
            line-height: 1.25;
        }
        
        /* Custom Scrollbar inside Promo Modal */
        #crmPromoModal .overflow-y-auto::-webkit-scrollbar {
            width: 5px;
        }
        #crmPromoModal .overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }
        #crmPromoModal .overflow-y-auto::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.12);
            border-radius: 10px;
        }
        #crmPromoModal .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.22);
        }
    </style>
</head>
<body>

{{-- ══ NAVBAR ══ --}}
<nav class="navbar-custom fixed-top" id="mainNavbar">
    <div class="container d-flex align-items-center justify-content-between" style="padding:.1rem 0">
        <a class="brand-text d-flex align-items-center gap-2" href="{{ route('home') }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" class="brand-icon-svg" style="width: 1.5rem; height: 1.5rem; display: inline-block; vertical-align: middle; filter: drop-shadow(0 0 8px rgba(14, 165, 233, 0.6)); margin-right: 0.2rem;">
                <path d="M9 16c0 1.66 1.34 3 3 3s3-1.34 3-3" fill="#0ea5e9" stroke="#0ea5e9" stroke-width="2.8"/>
                <path d="M8 14.5h8" stroke="#2563eb" stroke-width="3"/>
                <path d="M7.5 13.5L5 5" stroke-width="2.8"/>
                <path d="M12 13.5V4" stroke-width="2.8"/>
                <path d="M16.5 13.5L19 5" stroke-width="2.8"/>
                <path d="M6 9.5h12" stroke-width="1.8" opacity="0.75"/>
            </svg>
            Anbiyaa<span>Sport</span>
        </a>

        <div class="d-none d-lg-flex align-items-center gap-4">
            <a class="nav-link-custom" href="#keunggulan">Keunggulan</a>
            <a class="nav-link-custom" href="#lapangan">Lapangan</a>
            <a class="nav-link-custom" href="#kelengkapan">Kelengkapan</a>
            <a class="nav-link-custom" href="#testimoni">Testimoni</a>
            <a class="nav-link-custom" href="#lokasi">Lokasi</a>
        </div>

        <div class="d-flex align-items-center gap-2">
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn-nav-cta">
                        <i class="bi bi-speedometer2"></i>Dashboard
                    </a>
                @else
                    <a href="{{ route('booking.index') }}" class="btn-nav-cta">
                        <i class="bi bi-calendar-plus"></i>Booking
                    </a>
                @endif
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-nav-ghost">
                        <i class="bi bi-box-arrow-left me-1"></i>Keluar
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn-nav-cta">
                    <i class="bi bi-box-arrow-in-right"></i>Masuk
                </a>
            @endauth
        </div>
    </div>
</nav>


{{-- ══ HERO ══ --}}
<section class="hero" id="home">
    <div class="hero-bg" id="heroBg"
         style="background-image: url('{{ asset('images/bg/lapangan-1.jpg') }}')"></div>
    <div class="hero-mesh"></div>

    <div class="inner">
        <div class="container">
            <div class="row align-items-center justify-content-center g-5 py-5" style="padding-top:90px!important">
                <div class="col-lg-10 col-xl-9 mx-auto text-center">
                    <div class="hero-eyebrow mb-3">
                        <span class="dot"></span>
                        Booking Online — Cepat &amp; Mudah
                    </div>
                    <h1 class="mb-3">
                        Lapangan Bulutangkis<br>
                        <span class="accent">Anbiyaa Sport</span>
                    </h1>
                    <p class="hero-sub mb-4 mx-auto">
                        Lihat jadwal real-time, pilih slot favorit, dan konfirmasi booking dalam hitungan detik.
                        Tanpa antri, tanpa ribet — dari mana saja.
                    </p>
                    <div class="hero-actions d-flex gap-3 flex-wrap justify-content-center mb-5">
                        @auth
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}" class="btn-primary-hero">
                                    <i class="bi bi-speedometer2"></i> Panel Admin
                                </a>
                            @else
                                <a href="{{ route('booking.index') }}" class="btn-primary-hero">
                                    <i class="bi bi-calendar-plus"></i> Booking Sekarang
                                </a>
                            @endif
                        @else
                            <a href="{{ route('login') }}" class="btn-primary-hero">
                                <i class="bi bi-box-arrow-in-right"></i> Masuk &amp; Booking
                            </a>
                        @endauth
                        <a href="{{ route('jadwal.index') }}" class="btn-outline-hero">
                            <i class="bi bi-calendar3"></i> Lihat Jadwal
                        </a>
                    </div>

                    {{-- Stats pills --}}
                    <div class="hero-stats justify-content-center">
                        <div class="hero-stat-pill">
                            <span class="num">07:00</span>
                            <span style="color:rgba(255,255,255,.5)">–</span>
                            <span class="num">24:00</span>
                            <span>Jam Buka</span>
                        </div>
                        <a href="#lapangan" class="hero-stat-pill">
                            <span class="num">{{ $lapangans->count() }}</span>
                            <span>Lapangan</span>
                        </a>
                        <div class="hero-stat-pill">
                            <i class="bi bi-shield-check text-success"></i>
                            <span>Anti Double Booking</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ══ KEUNGGULAN ══ --}}
<section id="keunggulan" style="background:var(--s50);">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <div class="section-pill">Keunggulan Kami</div>
            <h2 class="section-h">Kenapa Pilih Anbiyaa Sport?</h2>
            <p class="section-sub">Sistem booking modern yang aman, cepat, dan mudah digunakan</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 reveal reveal-delay-1">
                <div class="feature-card">
                    <div class="feat-num">01</div>
                    <div class="feat-icon" style="background:#dbeafe">
                        <i class="bi bi-broadcast" style="color:#1d4ed8"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Jadwal Real-Time</h5>
                    <p class="text-muted mb-3" style="font-size:.9rem">Lihat ketersediaan lapangan secara langsung tanpa perlu login. Update otomatis saat ada booking baru.</p>
                    <a href="{{ route('jadwal.index') }}" class="d-inline-flex align-items-center gap-1 text-primary fw-bold" style="font-size:.83rem; text-decoration:none;">
                        Lihat Jadwal <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-4 reveal reveal-delay-2">
                <div class="feature-card">
                    <div class="feat-num">02</div>
                    <div class="feat-icon" style="background:#d1fae5">
                        <i class="bi bi-shield-check" style="color:#059669"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Booking 100% Aman</h5>
                    <p class="text-muted mb-3" style="font-size:.9rem">Sistem otomatis mencegah double booking. Satu slot hanya bisa dipesan satu orang — terjamin.</p>
                    <span class="d-inline-flex align-items-center gap-1 text-success fw-bold" style="font-size:.83rem;">
                        <i class="bi bi-check-circle-fill"></i> Anti Double Booking
                    </span>
                </div>
            </div>
            <div class="col-md-4 reveal reveal-delay-3">
                <div class="feature-card">
                    <div class="feat-num">03</div>
                    <div class="feat-icon" style="background:#fef3c7">
                        <i class="bi bi-credit-card" style="color:#d97706"></i>
                    </div>
                    <h5 class="fw-bold mb-2">Pembayaran Mudah</h5>
                    <p class="text-muted mb-3" style="font-size:.9rem">Bayar via QRIS atau tunai. Upload bukti pembayaran langsung dari aplikasi — cepat dan praktis.</p>
                    <div class="d-flex gap-2">
                        <span style="background:#fef9c3; border:1px solid #fde68a; color:#854d0e; font-size:.72rem; font-weight:700; padding:.25rem .7rem; border-radius:50px;">QRIS</span>
                        <span style="background:var(--s100); border:1px solid var(--s200); color:var(--s700); font-size:.72rem; font-weight:700; padding:.25rem .7rem; border-radius:50px;">Tunai</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ══ LAPANGAN ══ --}}
<section id="lapangan" style="background:#fff;">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <div class="section-pill">Fasilitas</div>
            <h2 class="section-h">Lapangan Tersedia</h2>
            <p class="section-sub">Pilih lapangan sesuai kebutuhan dan budget Anda</p>
        </div>
        <div class="row g-4 justify-content-center">
            @forelse($lapangans as $l)
            @php
                $isAktif = $l->status === 'aktif';
                $isWeekend = \Carbon\Carbon::today()->isWeekend();
                $harga = $isWeekend ? $l->harga_weekend : $l->harga_weekday;
            @endphp
            <div class="col-md-6 col-lg-4 reveal">
                <div class="court-card {{ !$isAktif ? 'opacity-65' : '' }}">
                    <div class="court-header {{ !$isAktif ? 'inactive' : '' }}">
                        @unless($isAktif)
                            <div class="court-ribbon"><i class="bi bi-slash-circle me-1"></i>Tidak Aktif</div>
                        @endunless
                        <div>
                            <h5 class="text-white fw-bold mb-1">{{ $l->nama_lapangan }}</h5>
                            @if($isAktif)
                                <div class="court-badge-aktif">
                                    <i class="bi bi-circle-fill" style="font-size:.45rem"></i> Aktif Beroperasi
                                </div>
                            @else
                                <span style="background:rgba(0,0,0,.2);color:#d1d5db;font-size:.72rem;padding:.2rem .6rem;border-radius:50px">
                                    <i class="bi bi-circle-fill me-1" style="color:#ef4444;font-size:.45rem"></i>Tidak Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="p-4">
                        <p class="text-muted mb-3" style="font-size:.88rem; line-height:1.6">
                            {{ $l->deskripsi ?? 'Lapangan standar bulutangkis berkualitas dengan fasilitas lengkap.' }}
                        </p>
                        @if($isAktif)
                        <div class="mb-3 pb-3" style="border-bottom:1px solid var(--s100)">
                            <div style="font-size:.72rem; color:var(--s400); font-weight:600; text-transform:uppercase; letter-spacing:.5px; margin-bottom:.4rem">
                                Harga {{ $isWeekend ? 'Weekend' : 'Weekday' }}
                            </div>
                            <div class="court-price-tag">
                                <span class="amt">Rp {{ number_format($harga, 0, ',', '.') }}</span>
                                <span class="unit">/ jam</span>
                            </div>
                            <div style="font-size:.72rem; color:var(--s400); margin-top:.2rem">
                                Weekday: Rp {{ number_format($l->harga_weekday,0,',','.') }}
                                &nbsp;·&nbsp;
                                Weekend: Rp {{ number_format($l->harga_weekend,0,',','.') }}
                            </div>
                        </div>
                        @endif
                        <div class="d-flex gap-2">
                            <a href="{{ route('jadwal.show', $l->id) }}"
                               class="btn btn-outline-primary rounded-pill px-3 fw-600" style="font-size:.83rem; flex:1; text-align:center">
                                <i class="bi bi-calendar3 me-1"></i>Lihat Jadwal
                            </a>
                            @if($isAktif)
                                @auth
                                    @if(auth()->user()->isPelanggan())
                                        <a href="{{ route('booking.index', ['lapangan_id' => $l->id]) }}"
                                           class="btn btn-primary rounded-pill px-3 fw-700" style="font-size:.83rem;">
                                            <i class="bi bi-calendar-plus me-1"></i>Booking
                                        </a>
                                    @endif
                                @endauth
                            @else
                                <button class="btn btn-secondary rounded-pill px-3 disabled" disabled style="font-size:.83rem">
                                    <i class="bi bi-lock-fill me-1"></i>Nonaktif
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5 text-muted">
                <i class="bi bi-grid-3x3-gap fs-1 d-block mb-2 opacity-25"></i>
                Belum ada lapangan tersedia. Hubungi admin.
            </div>
            @endforelse
        </div>
    </div>
</section>


{{-- ══ KELENGKAPAN ══ --}}
<section id="kelengkapan" style="background:var(--s50);">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6 reveal">
                <div class="section-pill mb-2">Layanan Tambahan</div>
                <h2 class="section-h mb-2">Kelengkapan Bermain</h2>
                <p class="section-sub mb-4">Kami menyediakan persewaan alat dan perlengkapan berkualitas untuk menunjang permainan Anda.</p>

                <div class="row g-2 mb-4">
                    <div class="col-6">
                        <div class="item-gear">
                            <div class="icon-wrap">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="15" cy="9" rx="5" ry="6"/><path d="M11.5 13.5L4 21"/><path d="M3.5 21.5L5.5 19.5" stroke-width="3"/></svg>
                            </div>
                            <div>
                                <div class="name">Sewa Raket</div>
                                <div class="price">Rp 25.000 / Raket</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="item-gear">
                            <div class="icon-wrap">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 18C12 18 8 14 8 9C8 6.79086 9.79086 5 12 5C14.2091 5 16 6.79086 16 9C16 14 12 18 12 18Z"/><circle cx="12" cy="18" r="1.5" fill="currentColor"/></svg>
                            </div>
                            <div>
                                <div class="name">Kok Satuan</div>
                                <div class="price">Rp 15.000 / Pcs</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="item-gear">
                            <div class="icon-wrap">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect x="8" y="4" width="8" height="16" rx="1"/><circle cx="12" cy="4" r="2" stroke-width="1"/></svg>
                            </div>
                            <div>
                                <div class="name">Kok Slop</div>
                                <div class="price">Rp 135.000 / Slop</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="item-gear">
                            <div class="icon-wrap">
                                <i class="bi bi-droplet-fill" style="color:var(--sky)"></i>
                            </div>
                            <div>
                                <div class="name">Anbiyaa Water</div>
                                <div class="price">Rp 5.000 / Botol</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded-3" style="background:#fff; border:1.5px solid var(--s200)">
                    <div class="fw-bold text-dark mb-2" style="font-size:.85rem">
                        <i class="bi bi-chat-dots-fill text-success me-2"></i>Kontak &amp; Sosial Media
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="https://wa.me/6289529508023" target="_blank" class="social-pill whatsapp">
                            <i class="bi bi-whatsapp"></i>+62 895-2950-8023
                        </a>
                        <a href="https://wa.me/6282187485422" target="_blank" class="social-pill whatsapp">
                            <i class="bi bi-whatsapp"></i>+62 821-8748-5422
                        </a>
                        <a href="https://www.instagram.com/goranbiyaa_01?igsh=aDZwcW5iNnh3cjly" target="_blank" class="social-pill instagram">
                            <i class="bi bi-instagram"></i>@goranbiyaa_01
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 reveal reveal-delay-2">
                <div id="equipmentCarousel" class="carousel slide carousel-fade rounded-4 overflow-hidden" data-bs-ride="carousel" style="box-shadow:0 24px 64px rgba(0,0,0,.12); border:6px solid #fff; height:460px;">
                    <!-- Indicators/dots -->
                    <div class="carousel-indicators" style="margin-bottom: 1.5rem;">
                        <button type="button" data-bs-target="#equipmentCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                        <button type="button" data-bs-target="#equipmentCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                        <button type="button" data-bs-target="#equipmentCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    </div>

                    <!-- The slideshow/carousel -->
                    <div class="carousel-inner h-100">
                        <div class="carousel-item active h-100">
                            <img src="{{ asset('images/kelengkapan/raket_merah.jpg') }}" class="d-block w-100 h-100" style="object-fit:cover; object-position:center" alt="Raket Merah Premium">
                        </div>
                        <div class="carousel-item h-100">
                            <img src="{{ asset('images/kelengkapan/raket_putih.jpg') }}" class="d-block w-100 h-100" style="object-fit:cover; object-position:center;" alt="Raket Putih Premium">
                        </div>
                        <div class="carousel-item h-100">
                            <img src="{{ asset('images/kelengkapan/shuttlecock.png') }}" class="d-block w-100 h-100" style="object-fit:cover; object-position:center" alt="Shuttlecock Premium">
                        </div>
                    </div>

                    <!-- Left and right controls/icons -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#equipmentCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));"></span>
                        <span class="visually-hidden">Sebelumnya</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#equipmentCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.5));"></span>
                        <span class="visually-hidden">Selanjutnya</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- ══ TESTIMONI ══ --}}
{{-- ══ TESTIMONI ══ --}}
<section id="testimoni" style="background:var(--s50);">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <div class="section-pill">Testimoni</div>
            <h2 class="section-h">Suara Para Pemain</h2>
            <p class="section-sub">Apa yang mereka rasakan bermain di Anbiyaa Sport</p>
        </div>
        <div class="testi-track-wrap">
            <div class="testi-track" id="testiTrack">
                @forelse($testimonis as $t)
                @php
                    $nama = $t->nama_pemesan ?? 'Pelanggan';
                    $initial = strtoupper(substr($nama, 0, 1));
                    $gradients = ['135deg,#1a56db,#0ea5e9','135deg,#16a34a,#22c55e','135deg,#d97706,#f59e0b','135deg,#7c3aed,#8b5cf6'];
                    $grad = $gradients[$loop->index % count($gradients)];
                @endphp
                <div class="testi-card">
                    <div class="testi-stars mb-2">
                        @for($i=1;$i<=5;$i++)
                            <i class="bi bi-star{{ $i<=$t->rating ? '-fill' : '' }}"></i>
                        @endfor
                    </div>
                    <p class="testi-quote mb-3">{{ $t->ulasan ?? 'Sangat memuaskan!' }}</p>
                    <div class="d-flex align-items-center gap-2 mt-auto">
                        <div class="testi-avatar" style="background:linear-gradient({{ $grad }})">{{ $initial }}</div>
                        <div>
                            <div class="fw-bold" style="font-size:.85rem">{{ $nama }}</div>
                            <div class="text-muted" style="font-size:.73rem">{{ $t->user ? ($t->user->isMember() ? 'Member' : 'Pelanggan') : 'Offline' }}</div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="w-100 text-center py-5 d-flex flex-column align-items-center justify-content-center">
                    <div style="width:64px;height:64px;border-radius:50%;background:rgba(37,99,235,.06);display:flex;align-items:center;justify-content:center;margin:0 auto 1rem">
                        <i class="bi bi-chat-left-heart fs-3 text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-1" style="color:var(--n800,#1e293b);">Belum Ada Ulasan</h5>
                    <p class="text-muted mb-0" style="font-size:.85rem;max-width:380px;line-height:1.6">Belum ada testimoni dari para pemain. Ulasan pertama akan muncul di sini setelah ada pelanggan yang menyelesaikan sesi bermain dan menulis ulasan!</p>
                </div>
                @endforelse
            </div>
        </div>
        {{-- Nav dots --}}
        <div class="d-flex justify-content-center gap-2 mt-4" id="testiDots"></div>
    </div>
</section>


{{-- ══ LOKASI ══ --}}
<section id="lokasi" style="background:#fff;">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <div class="section-pill mb-2">Lokasi</div>
                <h2 class="section-h mb-2">Kunjungi Kami</h2>
                <p class="section-sub mb-4">Datang dan rasakan pengalaman bermain badminton terbaik. Lokasi strategis, nyaman, dan mudah dijangkau.</p>

                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="location-info">
                        <div class="loc-icon" style="background:#dbeafe">
                            <i class="bi bi-geo-alt-fill" style="color:#1d4ed8"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1" style="font-size:.9rem">Alamat</h6>
                            <p class="text-muted mb-0" style="font-size:.85rem">
                                Anbiyaa Sport Badminton Court<br>
                                Jl. Berua Raya, Daya, Kec. Biringkanaya<br>
                                Kota Makassar, Sulawesi Selatan
                            </p>
                        </div>
                    </div>
                    <div class="location-info">
                        <div class="loc-icon" style="background:#fef3c7">
                            <i class="bi bi-clock-fill" style="color:#d97706"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-1" style="font-size:.9rem">Jam Operasional</h6>
                            <p class="text-muted mb-0" style="font-size:.85rem">
                                Buka Setiap Hari<br>
                                <strong class="text-dark">07:00 – 24:00 WITA</strong>
                            </p>
                        </div>
                    </div>

                </div>

                <a href="https://maps.app.goo.gl/qzezcjJDLxEkd4JF8?g_st=ac" target="_blank"
                   class="btn btn-primary rounded-pill px-4 py-2 fw-bold" style="font-size:.9rem">
                    <i class="bi bi-map-fill me-2"></i>Buka di Google Maps
                </a>
            </div>

            <div class="col-lg-7">
                <div class="map-frame">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3973.8058448833076!2d119.5317377!3d-5.1388656!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dbefc6e615e5757%3A0xc07c4b4d6b63d6b0!2sAnbiyaa%20Sport!5e0!3m2!1sid!2sid!4v1714740000000!5m2!1sid!2sid"
                        width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- ══ FOOTER ══ --}}
<footer class="footer-dark py-3">
    <div class="container">
        <div class="row align-items-center gy-3 gx-0 text-center text-md-start">
            <div class="col-md-6">
                <a href="{{ route('home') }}" class="brand-text d-flex align-items-center justify-content-center justify-content-md-start gap-2 mb-1" style="font-size:1.1rem">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" class="brand-icon-svg" style="width: 1.25rem; height: 1.25rem; display: inline-block; vertical-align: middle; filter: drop-shadow(0 0 6px rgba(14, 165, 233, 0.5)); margin-right: 0.1rem;">
                        <path d="M9 16c0 1.66 1.34 3 3 3s3-1.34 3-3" fill="#0ea5e9" stroke="#0ea5e9" stroke-width="2.8"/>
                        <path d="M8 14.5h8" stroke="#2563eb" stroke-width="3"/>
                        <path d="M7.5 13.5L5 5" stroke-width="2.8"/>
                        <path d="M12 13.5V4" stroke-width="2.8"/>
                        <path d="M16.5 13.5L19 5" stroke-width="2.8"/>
                        <path d="M6 9.5h12" stroke-width="1.8" opacity="0.75"/>
                    </svg>
                    Anbiyaa<span>Sport</span>
                </a>
                <p style="font-size:.73rem; color:var(--s400); margin:0">Booking Lapangan Bulutangkis — Makassar</p>
            </div>
            <div class="col-md-6 d-flex align-items-center gap-3 justify-content-center justify-content-md-end">
                <a href="https://www.instagram.com/goranbiyaa_01?igsh=aDZwcW5iNnh3cjly" target="_blank" class="footer-link" style="font-size:1.15rem">
                    <i class="bi bi-instagram"></i>
                </a>
                <a href="https://wa.me/6289529508023" target="_blank" class="footer-link" style="font-size:1.15rem">
                    <i class="bi bi-whatsapp"></i>
                </a>
            </div>
        </div>
    </div>
</footer>

<div class="modal fade" id="crmPromoModal" tabindex="-1" aria-labelledby="crmPromoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden text-white" style="background: linear-gradient(135deg, #090d16 0%, #151233 100%);">
            <div class="modal-header border-0 pb-0 justify-content-end">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body px-4 pb-4 pt-2">
                <div class="text-center mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2" 
                         style="width: 52px; height: 52px; background: linear-gradient(135deg, rgba(251,191,36,.25), rgba(245,158,11,.15)); border: 1.5px solid rgba(251,191,36,.3); color: #fbbf24; font-size: 1.4rem;">
                        <i class="bi bi-gem"></i>
                    </div>
                    <h4 class="fw-extrabold text-white mb-1" style="font-weight: 800; font-size: 1.45rem; letter-spacing: -0.5px;">
                        Program Loyalty <span class="text-warning">Anbiyaa Points</span>
                    </h4>
                    <p class="text-white-50 small mb-0 px-2">
                        Kumpulkan poin dari setiap pemesanan &amp; tukarkan dengan berbagai keuntungan bermain gratis!
                    </p>
                </div>

                <div class="p-3 rounded-4 d-flex flex-column" style="background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); min-height: 0; flex: 1;">
                    <h6 class="fw-bold text-success mb-2 d-flex align-items-center gap-2 flex-shrink-0" style="font-size: 0.9rem;">
                        <i class="bi bi-gift-fill"></i> Pilihan Penukaran Hadiah
                    </h6>
                    <div class="crm-reward-list-wrap d-flex flex-column gap-2">
                        <!-- Reward item 1 -->
                        <div class="crm-reward-micro-card d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="crm-reward-icon-wrapper gray">
                                    <i class="bi bi-circle-fill" style="color: #cbd5e1; font-size: 0.75rem;"></i>
                                </div>
                                <div>
                                    <div class="crm-reward-title">1 Shuttlecock Satuan</div>
                                    <div class="crm-reward-subtitle">Senilai Rp 15.000</div>
                                </div>
                            </div>
                            <span class="crm-reward-badge-pill">20 Pts</span>
                        </div>
                        
                        <!-- Reward item 2 -->
                        <div class="crm-reward-micro-card d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="crm-reward-icon-wrapper amber">
                                    <i class="bi bi-award-fill"></i>
                                </div>
                                <div>
                                    <div class="crm-reward-title">Sewa Raket 1 Sesi</div>
                                    <div class="crm-reward-subtitle">Senilai Rp 25.000</div>
                                </div>
                            </div>
                            <span class="crm-reward-badge-pill">35 Pts</span>
                        </div>
                        
                        <!-- Reward item 3 -->
                        <div class="crm-reward-micro-card d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="crm-reward-icon-wrapper sky">
                                    <i class="bi bi-ticket-perforated-fill"></i>
                                </div>
                                <div>
                                    <div class="crm-reward-title">Voucher Potongan Rp 50k</div>
                                    <div class="crm-reward-subtitle">Sewa Lapangan Eceran</div>
                                </div>
                            </div>
                            <span class="crm-reward-badge-pill">75 Pts</span>
                        </div>
                        
                        <!-- Reward item 4 -->
                        <div class="crm-reward-micro-card d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="crm-reward-icon-wrapper yellow">
                                    <i class="bi bi-lightning-charge-fill"></i>
                                </div>
                                <div>
                                    <div class="crm-reward-title">Free 1 Jam Off-Peak</div>
                                    <div class="crm-reward-subtitle">Bermain Weekdays (07:00 - 16:00)</div>
                                </div>
                            </div>
                            <span class="crm-reward-badge-pill">80 Pts</span>
                        </div>
                        
                        <!-- Reward item 5 -->
                        <div class="crm-reward-micro-card d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="crm-reward-icon-wrapper red">
                                    <i class="bi bi-clock-history"></i>
                                </div>
                                <div>
                                    <div class="crm-reward-title">Free 1 Jam Peak-Time</div>
                                    <div class="crm-reward-subtitle">Weekdays Malam / Weekend</div>
                                </div>
                            </div>
                            <span class="crm-reward-badge-pill">100 Pts</span>
                        </div>
                        
                        <!-- Reward item 6 -->
                        <div class="crm-reward-micro-card d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div class="crm-reward-icon-wrapper green">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div>
                                    <div class="crm-reward-title">Potongan Member Rp 100k</div>
                                    <div class="crm-reward-subtitle">Perpanjangan Bulan Depan</div>
                                </div>
                            </div>
                            <span class="crm-reward-badge-pill">180 Pts</span>
                        </div>
                    </div><!-- end crm-reward-list-wrap -->
                </div><!-- end reward box -->

                <!-- Footer Action Buttons -->
                <div class="mt-3 pt-3 border-top border-secondary-subtle flex-shrink-0">
                    @guest
                    <a href="{{ route('register') }}" class="btn w-100 py-2 fw-bold rounded-3 text-dark d-flex align-items-center justify-content-center gap-2" 
                       style="background: linear-gradient(135deg, #f59e0b, #fbbf24); box-shadow: 0 4px 18px rgba(245,158,11,.25); border:none; font-size: 0.9rem;">
                        <i class="bi bi-person-plus-fill"></i> Daftar Akun &amp; Mulai Kumpulkan Poin <i class="bi bi-arrow-right ms-auto"></i>
                    </a>
                    <div class="text-center mt-2" style="font-size: 0.73rem; color: rgba(255,255,255,0.4);">
                        Sudah memiliki akun? <a href="{{ route('login') }}" class="text-info fw-bold text-decoration-none">Masuk di sini</a>
                    </div>
                    @else
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="btn w-100 py-2 fw-bold rounded-3 text-dark d-flex align-items-center justify-content-center gap-2" 
                               style="background: linear-gradient(135deg, #f59e0b, #fbbf24); box-shadow: 0 4px 18px rgba(245,158,11,.25); border:none; font-size: 0.83rem;">
                                <i class="bi bi-speedometer2"></i> Panel Admin <i class="bi bi-arrow-right ms-auto"></i>
                            </a>
                        @else
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="{{ route('loyalty.index') }}" class="btn btn-outline-light w-100 py-2 fw-bold rounded-3" style="font-size: 0.83rem; border-color: rgba(255,255,255,0.15); background: rgba(255,255,255,0.03);">
                                        <i class="bi bi-person-badge-fill me-1 text-warning"></i> Loyalty Saya
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('booking.index') }}" class="btn w-100 py-2 fw-bold rounded-3 text-dark d-flex align-items-center justify-content-center gap-2" 
                                       style="background: linear-gradient(135deg, #f59e0b, #fbbf24); box-shadow: 0 4px 18px rgba(245,158,11,.25); border:none; font-size: 0.83rem;">
                                        <i class="bi bi-calendar-plus"></i> Booking Sekarang
                                    </a>
                                </div>
                            </div>
                        @endif
                    @endguest
                </div>
            </div><!-- end modal-body -->
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ── Navbar scroll ──
window.addEventListener('scroll', function() {
    document.getElementById('mainNavbar').classList.toggle('scrolled', window.scrollY > 50);
});

// ── Smooth scroll & active link ──
(function() {
    const navbar = document.getElementById('mainNavbar');
    function navH() { return navbar ? navbar.offsetHeight : 72; }

    // Set scroll-margin-top dinamis sesuai tinggi navbar & footer aktual via CSS custom property
    function applyNavbarHeight() {
        const h = navH();
        document.documentElement.style.setProperty('--navbar-height', h + 'px');
        const footer = document.querySelector('footer');
        if (footer) {
            document.documentElement.style.setProperty('--footer-height', footer.offsetHeight + 'px');
        }
    }
    
    // Jalankan sesegera mungkin, dan daftarkan pada event resize & load
    applyNavbarHeight();
    window.addEventListener('resize', applyNavbarHeight);
    window.addEventListener('load', applyNavbarHeight);
    document.addEventListener('DOMContentLoaded', applyNavbarHeight);

    // Klik anchor: scroll TEPAT sehingga section fit di layar
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', e => {
            const href = link.getAttribute('href');
            if (!href || href === '#') return;
            const target = document.querySelector(href);
            if (!target) return;
            e.preventDefault();

            const navbarHeight = navH();
            const targetPosition = target.getBoundingClientRect().top + window.scrollY;
            const offsetPosition = targetPosition - navbarHeight;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });

            // Update URL hash secara rapi tanpa trigger native jump
            if (history.pushState) {
                history.pushState(null, null, href);
            } else {
                location.hash = href;
            }
        });
    });

    // Active nav highlight
    window.addEventListener('scroll', () => {
        const h = navH();
        let cur = '';
        document.querySelectorAll('section[id]').forEach(s => {
            if (window.scrollY >= s.offsetTop - h - 20) cur = s.id;
        });
        document.querySelectorAll('.nav-link-custom').forEach(l => {
            l.style.color = l.getAttribute('href') === '#' + cur ? 'var(--sky)' : '';
        });
    });
})();

// ── Reveal on scroll ──
const observer = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// Force reveal at bottom of the page
window.addEventListener('scroll', () => {
    if ((window.innerHeight + window.scrollY) >= document.documentElement.scrollHeight - 50) {
        document.querySelectorAll('.reveal').forEach(el => el.classList.add('visible'));
    }
}, { passive: true });

// ── Testimoni auto-scroll ──
(function() {
    const track = document.getElementById('testiTrack');
    if (!track) return;
    const dotsWrap = document.getElementById('testiDots');
    const cards = track.querySelectorAll('.testi-card');
    if (!cards.length) return;

    // Build dots
    let dots = [];
    cards.forEach((_, i) => {
        const d = document.createElement('button');
        d.style.cssText = `width:8px;height:8px;border-radius:50%;border:none;background:var(--s300,#cbd5e1);padding:0;transition:all .3s;cursor:pointer`;
        d.addEventListener('click', () => scrollTo(i));
        dotsWrap.appendChild(d);
        dots.push(d);
    });

    function scrollTo(i) {
        const card = cards[i];
        track.scrollTo({ left: card.offsetLeft - 20, behavior: 'smooth' });
    }

    function updateDots() {
        const cw = cards[0]?.offsetWidth + 20;
        const idx = Math.round(track.scrollLeft / (cw || 320));
        dots.forEach((d, i) => {
            d.style.background = i === idx ? 'var(--blue)' : 'var(--s200,#e2e8f0)';
            d.style.width = i === idx ? '24px' : '8px';
            d.style.borderRadius = i === idx ? '50px' : '50%';
        });
    }
    track.addEventListener('scroll', updateDots, { passive: true });
    updateDots();

    // Auto play
    let cur = 0;
    setInterval(() => {
        cur = (cur + 1) % cards.length;
        scrollTo(cur);
    }, 4000);
})();

// ── Hero bg crossfade ──
(function() {
    const bg = document.getElementById('heroBg');
    if (!bg) return;
    const imgs = [
        '{{ asset("images/bg/lapangan-1.jpg") }}',
        '{{ asset("images/bg/lapangan-2.jpg") }}'
    ];
    let i = 0;
    setInterval(() => {
        i = (i + 1) % imgs.length;
        bg.style.opacity = 0;
        setTimeout(() => {
            bg.style.backgroundImage = `url('${imgs[i]}')`;
            bg.style.opacity = 1;
        }, 500);
    }, 6000);
})();

// ── Logout handler ──
function doLogout(e) {
    if (e) e.preventDefault();
    window.onbeforeunload = null;
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    fetch('{{ route("logout") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
        body: JSON.stringify({})
    }).finally(() => { window.location.href = '{{ route("home") }}'; });
}
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('form[action*="logout"]').forEach(f => f.addEventListener('submit', doLogout));

    // Trigger CRM Promo Modal untuk pengunjung baru secara instan (sekali per sesi)
    if (!sessionStorage.getItem('hasSeenCrmPromo')) {
        const promoModal = new bootstrap.Modal(document.getElementById('crmPromoModal'));
        promoModal.show();
        sessionStorage.setItem('hasSeenCrmPromo', 'true');
    }
});
</script>

<!-- Floating Loyalty Promo Button -->
<div class="position-fixed bottom-0 end-0 m-4" style="z-index: 1050;">
    <button class="btn btn-warning rounded-circle d-flex align-items-center justify-content-center" 
            style="width: 60px; height: 60px; transition: all 0.3s;"
            data-bs-toggle="modal" data-bs-target="#crmPromoModal"
            id="floatingLoyaltyBtn"
            title="Lihat Promo Loyalty Poin">
        <i class="bi bi-gift-fill text-dark fs-4"></i>
    </button>
</div>

</body>
</html>
