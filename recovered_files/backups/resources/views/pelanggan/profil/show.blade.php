@extends('layouts.app')

@section('title', $activeTab === 'loyalty' ? 'Loyalty Points Saya' : 'Profil Saya')

@section('page_title', $activeTab === 'loyalty' ? 'Loyalty Points' : 'Profil Saya')
@section('page_subtitle', $activeTab === 'loyalty' ? 'Kumpulkan poin sewa dan tukarkan dengan hadiah menarik' : 'Kelola informasi akun dan pantau aktivitas Anda')
@section('topbar_actions')
    <span class="badge bg-primary rounded-pill px-3 py-2 shadow-sm">
        <i class="bi bi-person-badge-fill me-1"></i>{{ $user->isMember() ? 'MEMBER (' . strtoupper(str_replace('_', ' ', $user->kategori_member)) . ')' : 'NON-MEMBER' }}
    </span>
@endsection

@section('content')
@php
    // Hitung persentase kelengkapan profil
    $completeness = 30; // default (nama terisi)
    $completeness += 30; // email terisi
    if ($user->nomor_hp) $completeness += 15;
    if ($user->alamat) $completeness += 15;
    if ($user->foto_profil) $completeness += 10;
@endphp

<style>
    /* ── Combined Premium Styling ── */
    .profile-sidebar-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        overflow: hidden;
    }

    .profile-hero {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 2.5rem 1.5rem;
        position: relative;
        text-align: center;
        color: #fff;
    }

    .profile-hero::after {
        content: '';
        position: absolute;
        width: 150px; height: 150px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 50%;
        top: -30px; right: -30px;
    }

    .avatar-wrapper {
        position: relative;
        width: 100px;
        height: 100px;
        margin: 0 auto 1rem;
    }

    .avatar-img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        transition: all 0.3s ease;
    }

    .avatar-img:hover {
        border-color: var(--primary);
        transform: scale(1.03);
    }

    .avatar-placeholder {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border: 4px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #fff;
        margin: 0 auto;
    }

    /* ── Progress Card ── */
    .progress-card {
        background: #f8fafc;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 1rem 1.25rem;
    }

    /* ── Stats block ── */
    .p-stat-box {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 1.2rem;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        text-align: center;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .p-stat-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.06);
    }

    .p-stat-box::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 3px;
    }

    .p-stat-box.blue::before   { background: #3b82f6; }
    .p-stat-box.green::before  { background: #10b981; }
    .p-stat-box.warning::before{ background: #f59e0b; }
    .p-stat-box.purple::before { background: #8b5cf6; }

    .p-stat-box .num {
        font-size: 1.8rem;
        font-weight: 800;
        line-height: 1.1;
    }

    .p-stat-box .lbl {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.25rem;
        font-weight: 600;
    }

    .p-stat-box .icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 0.75rem;
        font-size: 1rem;
    }



    /* ── Premium Form Inputs ── */
    .input-group-premium {
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        overflow: hidden;
        transition: all 0.2s;
    }

    .input-group-premium:focus-within {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    }

    .input-group-premium .input-group-text {
        background: #f8fafc;
        border: none;
        border-right: 1px solid #e2e8f0;
        color: #64748b;
        padding: 0.6rem 1rem;
    }

    .input-group-premium .form-control {
        border: none;
        padding: 0.6rem 1rem;
        box-shadow: none !important;
    }

    /* ── Membership Card ── */
    .member-banner {
        border-radius: 16px;
        background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%);
        color: #fff;
        padding: 1.25rem;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 20px rgba(29, 78, 216, 0.15);
    }

    .member-banner::after {
        content: '';
        position: absolute;
        width: 120px; height: 120px;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 50%;
        bottom: -40px; right: -40px;
    }

    /* ── Image Upload Preview ── */
    .image-preview-box {
        width: 70px; height: 70px;
        border-radius: 50%;
        border: 2px dashed #cbd5e1;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden;
        background: #f8fafc;
        flex-shrink: 0;
    }

    /* ── Loyalty Points Premium Theme ─────────────────────────── */
    .virtual-card-container {
        perspective: 1000px;
        width: 100%;
        max-width: 440px;
        margin: 0 auto;
    }
    .virtual-card {
        width: 100%;
        height: 280px;
        position: relative;
        transform-style: preserve-3d;
        transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
        border-radius: 20px;
        cursor: pointer;
        box-shadow: 0 15px 35px -10px rgba(0,0,0,0.3);
    }
    .virtual-card.flipped {
        transform: rotateY(180deg);
    }
    .card-side {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        border-radius: 20px;
        padding: 1.25rem 1.5rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        overflow: hidden;
        border: 1.5px solid rgba(255,255,255,0.15);
    }
    .card-front {
        z-index: 2;
    }
    .card-back {
        transform: rotateY(180deg);
        background: #0f172a;
        z-index: 1;
        color: white;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .card-glare {
        position: absolute;
        inset: 0;
        pointer-events: none;
        border-radius: 20px;
        z-index: 10;
        mix-blend-mode: overlay;
        background: radial-gradient(circle at 50% 50%, rgba(255,255,255,0.15) 0%, transparent 60%);
    }

    /* Tier Backgrounds */
    .card-member {
        background: linear-gradient(135deg, #18181b 0%, #78350f 65%, #b45309 100%);
        border-color: rgba(245, 158, 11, 0.6);
        box-shadow: 0 15px 35px -10px rgba(120, 53, 15, 0.45);
    }
    .card-vip {
        background: linear-gradient(135deg, #4c0519 0%, #881337 50%, #e11d48 100%);
        border-color: rgba(225, 29, 72, 0.5);
        box-shadow: 0 15px 35px -10px rgba(136, 19, 55, 0.45);
    }
    .card-loyalist {
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 50%, #d97706 100%);
        border-color: rgba(245, 158, 11, 0.6);
        box-shadow: 0 15px 35px -10px rgba(120, 53, 15, 0.45);
    }
    .card-partner {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 50%, #10b981 100%);
        border-color: rgba(16, 185, 129, 0.45);
        box-shadow: 0 15px 35px -10px rgba(6, 95, 70, 0.45);
    }
    .card-ally {
        background: linear-gradient(135deg, #090d16 0%, #1e293b 50%, #2563eb 100%);
        border-color: rgba(56, 189, 248, 0.4);
    }
    .card-visitor {
        background: linear-gradient(135deg, #0f172a 0%, #18181b 60%, #3f3f46 100%);
        border-color: rgba(255, 255, 255, 0.15);
    }

    .card-chip {
        width: 38px; height: 28px;
        background: linear-gradient(135deg, #cbd5e1, #94a3b8);
        border-radius: 6px;
        position: relative;
        border: 1px solid rgba(0,0,0,0.15);
        overflow: hidden;
    }
    .card-chip::before {
        content: '';
        position: absolute;
        inset: 4px;
        border: 1px solid rgba(0,0,0,0.15);
        border-radius: 4px;
    }

    .poin-saldo-display {
        font-size: 2.5rem;
        font-weight: 900;
        line-height: 1;
        letter-spacing: -1.5px;
        text-shadow: 0 2px 8px rgba(0,0,0,0.25);
    }
    .segmen-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 700;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.2);
    }
    .segmen-member   { background: rgba(245, 158, 11, 0.25); color: #fef3c7; border: 1px solid rgba(245, 158, 11, 0.4); }
    .segmen-visitor  { background: rgba(100, 116, 139, 0.25); color: #e2e8f0; }
    .segmen-ally     { background: rgba(14, 165, 233, 0.25); color: #bae6fd; }
    .segmen-partner  { background: rgba(16, 185, 129, 0.25); color: #a7f3d0; }
    .segmen-loyalist { background: rgba(245, 158, 11, 0.25); color: #fef3c7; border: 1px solid rgba(245, 158, 11, 0.4); }
    .segmen-vip      { background: rgba(225, 29, 72, 0.25); color: #fecdd3; border: 1px solid rgba(225, 29, 72, 0.4); }

    /* ── Roadmap Stepper ────────────────────────────────────────── */
    .roadmap-container {
        background: #ffffff;
        border: 1px solid rgba(226, 232, 240, 0.8);
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 30px rgba(37, 99, 235, 0.03);
    }
    .roadmap-stepper {
        display: flex;
        justify-content: space-between;
        position: relative;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .roadmap-line {
        position: absolute;
        top: 17px;
        left: 24px;
        right: 24px;
        height: 6px;
        background: #e2e8f0;
        z-index: 1;
        border-radius: 10px;
    }
    .roadmap-progress-line {
        position: absolute;
        top: 17px;
        left: 24px;
        height: 6px;
        background: linear-gradient(90deg, #3b82f6 0%, #10b981 33%, #f59e0b 66%, #ef4444 100%);
        z-index: 2;
        border-radius: 10px;
        transition: width 1.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .roadmap-step {
        position: relative;
        z-index: 3;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }
    .roadmap-dot {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #ffffff;
        border: 3.5px solid #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        cursor: pointer;
    }
    .roadmap-dot:hover {
        transform: translateY(-3px) scale(1.1);
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }
    .roadmap-step.active .roadmap-dot {
        transform: scale(1.2);
        box-shadow: 0 0 20px rgba(37, 99, 235, 0.35);
    }
    .roadmap-step.completed .roadmap-dot {
        border-color: #10b981;
        background: #f0fdf4;
    }

    /* Specific Active/Completed Theme Styles for Steps */
    .roadmap-step.visitor.active .roadmap-dot { border-color: #64748b; background: #f8fafc; color: #475569; }
    .roadmap-step.ally.active .roadmap-dot { border-color: #0ea5e9; background: #f0f9ff; color: #0284c7; }
    .roadmap-step.partner.active .roadmap-dot { border-color: #10b981; background: #f0fdf4; color: #15803d; }
    .roadmap-step.loyalist.active .roadmap-dot { border-color: #f59e0b; background: #fffbeb; color: #d97706; }
    .roadmap-step.vip.active .roadmap-dot { 
        border-color: #e11d48; 
        background: #fff5f5; 
        color: #e11d48;
        animation: pulse-vip-dot 1.5s infinite alternate;
    }

    @keyframes pulse-vip-dot {
        0% { transform: scale(1.15); box-shadow: 0 0 10px rgba(225, 29, 72, 0.3); }
        100% { transform: scale(1.25); box-shadow: 0 0 22px rgba(225, 29, 72, 0.6); }
    }

    .roadmap-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: #64748b;
        margin-top: 10px;
        text-align: center;
        transition: all 0.3s ease;
    }
    .roadmap-step.active .roadmap-label {
        color: #0f172a;
        font-weight: 800;
        transform: scale(1.05);
    }

    /* ── Segmen Card Themes ──────────────────────────────────────── */
    .segmen-card {
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
    }
    .segmen-card-visitor {
        background: #f8fafc;
        border-color: #cbd5e1 !important;
    }
    .segmen-card-visitor .segmen-icon-wrapper {
        background: #e2e8f0;
        color: #475569;
    }
    .segmen-card-visitor .header-text {
        color: #334155;
    }
    .segmen-card-visitor .desc-text {
        color: #475569;
    }

    .segmen-card-ally {
        background: #f0f9ff;
        border-color: #bae6fd !important;
    }
    .segmen-card-ally .segmen-icon-wrapper {
        background: #e0f2fe;
        color: #0284c7;
    }
    .segmen-card-ally .header-text {
        color: #0369a1;
    }
    .segmen-card-ally .desc-text {
        color: #075985;
    }

    .segmen-card-partner {
        background: #f0fdf4;
        border-color: #bbf7d0 !important;
    }
    .segmen-card-partner .segmen-icon-wrapper {
        background: #d1fae5;
        color: #15803d;
    }
    .segmen-card-partner .header-text {
        color: #166534;
    }
    .segmen-card-partner .desc-text {
        color: #14532d;
    }

    .segmen-card-loyalist {
        background: #fffbeb;
        border-color: #fde68a !important;
    }
    .segmen-card-loyalist .segmen-icon-wrapper {
        background: #fef3c7;
        color: #d97706;
    }
    .segmen-card-loyalist .header-text {
        color: #92400e;
    }
    .segmen-card-loyalist .desc-text {
        color: #78350f;
    }

    .segmen-card-vip {
        background: #fff5f5;
        border-color: #fecdd3 !important;
        animation: vip-glow 2s infinite alternate;
    }
    .segmen-card-vip .segmen-icon-wrapper {
        background: #ffe4e6;
        color: #e11d48;
    }
    .segmen-card-vip .header-text {
        color: #9f1239;
    }
    .segmen-card-vip .desc-text {
        color: #881337;
    }

    @keyframes vip-glow {
        0% { box-shadow: 0 4px 15px rgba(225, 29, 72, 0.05); border-color: #fecdd3 !important; }
        100% { box-shadow: 0 8px 25px rgba(225, 29, 72, 0.15); border-color: #fda4af !important; }
    }

    /* ── SVG Chart ────────────────────────────────────────────── */
    .chart-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem;
        height: 100%;
    }
    .chart-container {
        position: relative;
        width: 100%;
        height: 150px;
        margin-top: 10px;
    }
    .chart-tooltip {
        position: absolute;
        background: rgba(15, 23, 42, 0.95);
        color: #fff;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.72rem;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.15s ease, transform 0.15s ease;
        z-index: 100;
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        border: 1px solid rgba(255,255,255,0.1);
        transform: translate(-50%, -100%);
    }

    /* ── Reward Cards & Filtering ────────────────────────────── */
    /* ── Reward Cards & Filtering ────────────────────────────── */
    .reward-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 1.25rem;
        max-height: 480px;
        overflow-y: auto;
        padding-right: 4px;
    }
    .reward-card {
        background: #fff;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.25rem;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 275px;
        height: auto;
    }
    .reward-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2563eb, #7c3aed);
        transform: scaleX(0);
        transition: transform 0.3s ease;
        transform-origin: left;
    }
    .reward-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.06);
        border-color: #cbd5e1;
    }
    .reward-card:hover::before { transform: scaleX(1); }
    .reward-card.can-redeem { border-color: #10b981; }
    .reward-card.can-redeem::before {
        background: linear-gradient(90deg, #10b981, #059669);
        transform: scaleX(1);
    }
    
    .reward-card.estimator-unlocked {
        border-color: #10b981;
        box-shadow: 0 0 15px rgba(16,185,129,0.25);
        animation: glowPulse 2s infinite alternate;
    }

    .reward-icon {
        width: 44px; height: 44px;
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.4rem;
        margin-bottom: 0.25rem;
    }
    .reward-poin-badge {
        font-size: 1.45rem; font-weight: 900;
        color: #1d4ed8; line-height: 1;
    }
    .reward-poin-unit {
        font-size: 0.68rem; color: #94a3b8;
        font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
    }
    .btn-tukar {
        width: 100%; border-radius: 10px;
        padding: 0.55rem; font-size: 0.8rem;
        font-weight: 700; transition: all 0.2s;
        border: none; cursor: pointer;
    }
    .btn-tukar-active {
        background: linear-gradient(135deg, #2563eb, #7c3aed);
        color: white;
        box-shadow: 0 4px 10px rgba(37,99,235,0.2);
    }
    .btn-tukar-active:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(37,99,235,0.3);
        color: white;
    }
    .btn-tukar-disabled {
        background: #f1f5f9; color: #94a3b8; cursor: not-allowed;
    }
    .btn-tukar-estimator {
        background: #e6fdf5; color: #10b981; border: 1px dashed #10b981;
    }
    .btn-tukar-member-only {
        background: linear-gradient(135deg, #f3e8ff, #ede9fe);
        color: #7c3aed;
        border: 1.5px solid #c4b5fd;
        cursor: not-allowed;
        font-size: 0.8rem;
    }
    .reward-card.member-locked {
        border-color: #c4b5fd;
        background: linear-gradient(135deg, #faf5ff 0%, #f5f3ff 100%);
    }
    .reward-card.member-locked::before {
        background: linear-gradient(90deg, #7c3aed, #a855f7);
        transform: scaleX(1);
    }
    .member-only-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.62rem;
        font-weight: 700;
        color: #7c3aed;
        background: #f3e8ff;
        border: 1px solid #c4b5fd;
        border-radius: 20px;
        padding: 2px 8px;
        width: fit-content;
        margin-top: 4px;
    }

    /* ── Voucher Ticket Style ───────────────────────────────────── */
    .voucher-ticket {
        position: relative;
        background: #ffffff;
        border: 1.5px solid #e2e8f0;
        border-radius: 16px;
        display: flex;
        align-items: stretch;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        /* overflow: hidden; */ /* Disabled to show top/bottom notches */
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    }
    .voucher-ticket:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(16,185,129,0.12);
        border-color: #10b981;
    }
    /* Notch cutouts (top and bottom center of the ticket seam) */
    .voucher-ticket::before, .voucher-ticket::after {
        content: '';
        position: absolute;
        width: 16px; height: 16px;
        background: #ffffff;
        border-radius: 50%;
        z-index: 5;
        border: 1.5px solid #e2e8f0;
    }
    .voucher-ticket::before {
        top: -9px;
        left: 51px;
    }
    .voucher-ticket::after {
        bottom: -9px;
        left: 51px;
    }

    .voucher-icon-col {
        width: 60px;
        min-width: 60px;
        background: linear-gradient(160deg, #ecfdf5, #d1fae5);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        border-right: 1.5px dashed #a7f3d0;
        border-top-left-radius: 14px;
        border-bottom-left-radius: 14px;
        flex-shrink: 0;
        padding: 0.75rem 0;
        z-index: 2;
    }
    .voucher-content-col {
        flex-grow: 1;
        min-width: 0;
        padding: 0.75rem 0.85rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        z-index: 2;
    }
    .voucher-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1.35;
        word-wrap: break-word;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .voucher-exp-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.62rem;
        font-weight: 600;
        color: #64748b;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 2px 8px;
        width: fit-content;
    }
    .voucher-btn-use {
        display: block;
        width: 100%;
        padding: 6px 10px;
        font-size: 0.72rem;
        font-weight: 700;
        color: #fff;
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        border-radius: 8px;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
        letter-spacing: 0.3px;
    }
    .voucher-btn-use:hover {
        background: linear-gradient(135deg, #059669, #047857);
        box-shadow: 0 4px 12px rgba(16,185,129,0.35);
        transform: translateY(-1px);
    }
    /* keep old class for compat but hide it */
    .voucher-icon-area { display: none; }
    .voucher-content-area { display: none; }

    /* ── Estimator Widget ───────────────────────────────────────── */
    .estimator-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 16px;
        padding: 1.25rem; height: 100%; transition: all 0.2s;
    }
    .estimator-card:hover { border-color: #cbd5e1; box-shadow: 0 10px 25px rgba(0,0,0,0.03); }
    .estimator-badge {
        font-size: 2.2rem; font-weight: 900; color: #1d4ed8; line-height: 1;
        transition: transform 0.2s ease; display: inline-block;
    }
    .estimator-badge.pulse { animation: badgePulse 0.4s ease; }
    .estimator-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 6px;
        border-radius: 5px;
        background: #cbd5e1;
        outline: none;
    }
    .estimator-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #2563eb;
        cursor: pointer;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .estimator-label { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 3px; }

    .poin-masuk  { color: #10b981; font-weight: 700; }
    .poin-keluar { color: #ef4444; font-weight: 700; }
    
    .filter-pills { display: flex; gap: 6px; margin-bottom: 1rem; flex-wrap: wrap; }
    .filter-pill {
        border-radius: 50px; font-size: 0.75rem; font-weight: 700;
        padding: 5px 12px; border: 1.5px solid #e2e8f0; background: #fff;
        color: #64748b; cursor: pointer; transition: all 0.2s ease;
    }
    .filter-pill:hover { border-color: #cbd5e1; color: #475569; }
    .filter-pill.active { color: #fff; border-color: transparent; }
    .filter-pill.active.all { background: linear-gradient(135deg, #1d4ed8, #3b82f6); box-shadow: 0 4px 10px rgba(29,78,216,0.2); }
    .filter-pill.active.redeemable { background: linear-gradient(135deg, #059669, #10b981); box-shadow: 0 4px 10px rgba(5,150,105,0.25); }
    .filter-pill.active.locked { background: linear-gradient(135deg, #64748b, #94a3b8); box-shadow: 0 4px 10px rgba(100,116,139,0.2); }

    /* ── Popups & Overlays ────────────────────────────────── */
    .redemption-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.55);
        display: flex; align-items: center; justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(6px);
    }
    .redemption-popup {
        background: white;
        border-radius: 24px;
        padding: 2rem;
        max-width: 400px; width: 90%;
        text-align: center;
        box-shadow: 0 25px 60px rgba(0,0,0,0.3);
        animation: popInLoyalty 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    .redemption-kode-display {
        font-family: 'Courier New', monospace;
        font-size: 1.25rem;
        font-weight: 900;
        letter-spacing: 2px;
        color: #1d4ed8;
        background: #eff6ff;
        padding: 10px 15px;
        border-radius: 12px;
        border: 2px dashed #93c5fd;
        margin: 1rem 0;
        word-break: break-all;
    }
    .konfirmasi-overlay {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.55);
        display: none; align-items: center; justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(6px);
    }
    .konfirmasi-overlay.show { display: flex; }
    .konfirmasi-popup {
        background: white;
        border-radius: 20px;
        padding: 1.75rem;
        max-width: 380px; width: 90%;
        text-align: center;
        box-shadow: 0 20px 50px rgba(0,0,0,0.25);
        animation: popInLoyalty 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }
    /* barcode-svg removed */
    
    /* ── Profile Navigation Tabs (Premium Grid Cards) ── */
    /* ── Profile Navigation Tabs (Premium Grid Cards) ── */
    .profile-tabs {
        display: grid !important;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 12px !important;
        padding: 8px !important;
        background: #f1f5f9;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        width: 100%;
        margin-bottom: 0.5rem;
    }

    @media (max-width: 1200px) {
        .profile-tabs {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .profile-tabs {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 576px) {
        .profile-tabs {
            grid-template-columns: 1fr;
        }
    }

    .profile-tabs .nav-item {
        margin: 0;
        width: 100%;
    }

    .profile-tabs .nav-link {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        width: 100% !important;
        padding: 16px 10px !important;
        border-radius: 14px !important;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1) !important;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        border: 1px solid #e2e8f0 !important;
        background: #ffffff !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02) !important;
        height: 100% !important;
    }

    .tab-btn-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        width: 100%;
        text-align: center;
    }

    .tab-btn-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .tab-btn-info {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    .tab-btn-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.3;
        margin-bottom: 2px;
        word-break: break-word;
        transition: color 0.3s ease;
    }

    .tab-btn-subtitle {
        font-size: 0.7rem;
        font-weight: 600;
        color: #64748b;
        line-height: 1.2;
        word-break: break-word;
        transition: color 0.3s ease;
    }

    /* Hover effect */
    .profile-tabs .nav-link:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
        border-color: #cbd5e1 !important;
    }

    /* Theme colors for icons (Inactive state) */
    .profile-tabs .nav-link.tab-loyalty .tab-btn-icon  { background: #ecfdf5; color: #10b981; }
    .profile-tabs .nav-link.tab-profil .tab-btn-icon   { background: #eff6ff; color: #3b82f6; }
    .profile-tabs .nav-link.tab-keamanan .tab-btn-icon { background: #fef2f2; color: #ef4444; }
    .profile-tabs .nav-link.tab-booking .tab-btn-icon  { background: #f5f3ff; color: #8b5cf6; }
    .profile-tabs .nav-link.tab-poin .tab-btn-icon     { background: #fffbeb; color: #f59e0b; }

    /* Hover theme highlights */
    .profile-tabs .nav-link.tab-loyalty:hover  { border-color: #86efac !important; background: #f9fefb !important; }
    .profile-tabs .nav-link.tab-profil:hover   { border-color: #93c5fd !important; background: #f9fbfe !important; }
    .profile-tabs .nav-link.tab-keamanan:hover { border-color: #fca5a5 !important; background: #fffbfb !important; }
    .profile-tabs .nav-link.tab-booking:hover  { border-color: #d8b4fe !important; background: #faf9fe !important; }
    .profile-tabs .nav-link.tab-poin:hover     { border-color: #fde68a !important; background: #fffdf9 !important; }

    /* Active overrides (Unified Premium Brand Theme) */
    .profile-tabs .nav-link.active {
        background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%) !important;
        border-color: transparent !important;
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.25) !important;
    }

    .profile-tabs .nav-link.active .tab-btn-title {
        color: #ffffff !important;
    }
    .profile-tabs .nav-link.active .tab-btn-subtitle,
    .profile-tabs .nav-link.active .tab-btn-subtitle span {
        color: rgba(255, 255, 255, 0.85) !important;
        background: transparent !important;
    }
    .profile-tabs .nav-link.active .tab-btn-icon {
        background: rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
        transform: scale(1.1);
        box-shadow: 0 0 10px rgba(255,255,255,0.1);
    }
</style>

{{-- ── Modal Sukses Penukaran ── --}}
@if(session('success_redemption'))
@php $sr = session('success_redemption'); @endphp
<div class="redemption-overlay" id="redemptionOverlay" onclick="document.getElementById('redemptionOverlay').remove()">
    <div class="redemption-popup" onclick="event.stopPropagation()">
        <div style="font-size:3.5rem;margin-bottom:0.5rem">🎉</div>
        <h5 class="fw-bold text-dark mb-1">Penukaran Berhasil!</h5>
        <p class="text-muted small mb-1">{{ $sr['label'] }}</p>
        <p class="text-muted" style="font-size:0.75rem;margin-bottom:1.25rem">
            <i class="bi bi-clock me-1"></i>Berlaku hingga <strong>{{ $sr['expired_at'] }}</strong><br>
            Voucher Anda siap digunakan! Gunakan voucher ini saat melakukan <strong>Booking Online</strong> lapangan.
        </p>
        <div class="d-flex gap-2 w-100">
            <button class="btn btn-light flex-fill" onclick="document.getElementById('redemptionOverlay').remove()">
                Tutup
            </button>
            <a class="btn btn-primary flex-fill text-white d-flex align-items-center justify-content-center gap-1" href="{{ route('booking.index', ['voucher_id' => $sr['id'] ?? '']) }}">
                <i class="bi bi-calendar-plus"></i> Gunakan
            </a>
        </div>
    </div>
</div>
@endif

{{-- ── Modal Konfirmasi Tukar Poin ── --}}
<div class="konfirmasi-overlay" id="konfirmasiOverlay">
    <div class="konfirmasi-popup">
        <div style="font-size:3rem;margin-bottom:0.5rem" id="konfirmasiIcon">🎁</div>
        <h6 class="fw-bold text-dark mb-2" id="konfirmasiJudul">Konfirmasi Penukaran</h6>
        <p class="text-muted small mb-1" id="konfirmasiLabel"></p>
        <p class="mb-3" style="font-size:0.8rem;color:#64748b">
            Penukaran poin <strong>tidak dapat dibatalkan</strong>. Lanjutkan?
        </p>
        <div class="d-flex gap-2">
            <button class="btn btn-light flex-fill btn-sm" onclick="tutupKonfirmasi()">
                <i class="bi bi-x-circle me-1"></i>Batal
            </button>
            <button class="btn btn-primary flex-fill btn-sm" id="konfirmasiSubmitBtn">
                <i class="bi bi-lightning-charge-fill me-1"></i>Ya, Tukar!
            </button>
        </div>
    </div>
</div>

{{-- ── Modal Detail Voucher (Gunakan saat Booking) ── --}}
<div class="konfirmasi-overlay" id="barcodeModal" onclick="tutupBarcodeModal()">
    <div class="konfirmasi-popup" onclick="event.stopPropagation()" style="max-width: 360px; border-radius: 24px; padding: 0; overflow: hidden;">
        {{-- Top accent strip --}}
        <div style="background: linear-gradient(135deg, #10b981, #059669); padding: 1.25rem 1.5rem 1rem; text-align: center; position: relative;">
            <button type="button" class="btn-close btn-close-white" onclick="tutupBarcodeModal()"
                    style="position:absolute; top:0.75rem; right:0.75rem; opacity:0.8;"></button>
            <div id="barcodeModalIcon" style="font-size:2.8rem; line-height:1; margin-bottom:0.4rem;">🎫</div>
            <div class="fw-bold text-white" style="font-size:0.95rem;" id="barcodeModalLabel">Voucher</div>
            <div style="font-size:0.68rem; color:rgba(255,255,255,0.8); margin-top:2px;">GOR Anbiyaa Sport</div>
        </div>

        {{-- Body --}}
        <div style="padding: 1.25rem 1.5rem;">
            {{-- Status badge --}}
            <div class="d-flex justify-content-center mb-3">
                <span class="d-inline-flex align-items-center gap-2 px-4 py-2 rounded-pill fw-bold"
                      style="background:#dcfce7; border:1.5px solid #86efac; color:#15803d; font-size:0.78rem; letter-spacing:0.5px;">
                    <span style="width:8px;height:8px;background:#22c55e;border-radius:50%;display:inline-block;box-shadow:0 0 0 3px rgba(34,197,94,0.3);"></span>
                    VOUCHER AKTIF
                </span>
            </div>

            {{-- Info rows --}}
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:0.85rem 1rem; margin-bottom:1rem;">
                <div class="d-flex align-items-center gap-2 py-1" style="font-size:0.78rem;">
                    <i class="bi bi-gift-fill text-success" style="width:16px; flex-shrink:0;"></i>
                    <span class="text-muted">Hadiah:</span>
                    <span class="fw-semibold text-dark" id="barcodeModalLabelInfo" style="flex:1; text-align:right;"></span>
                </div>
                <hr style="margin:6px 0; border-color:#e2e8f0;">
                <div class="d-flex align-items-center gap-2 py-1" style="font-size:0.78rem;">
                    <i class="bi bi-calendar-check text-primary" style="width:16px; flex-shrink:0;"></i>
                    <span class="text-muted">Berlaku s/d:</span>
                    <span class="fw-semibold text-dark" style="flex:1; text-align:right;" id="barcodeModalExpiry">-</span>
                </div>
            </div>

            {{-- Instruction --}}
            <p class="text-center text-muted mb-3" style="font-size:0.72rem; line-height:1.5;">
                <i class="bi bi-info-circle me-1 text-primary"></i>
                Voucher ini hanya dapat digunakan saat melakukan <strong>Booking Online</strong> dalam sistem.<br>
                Pilih voucher ini pada form pemesanan lapangan.
            </p>

            <a id="btnGunakanBooking" href="{{ route('booking.index') }}" class="btn btn-primary w-100 rounded-3 fw-semibold mb-2" style="font-size:0.82rem;">
                <i class="bi bi-calendar-plus me-1"></i>Gunakan saat Booking
            </a>

            <button class="btn btn-secondary w-100 rounded-3 fw-semibold" style="font-size:0.82rem;" onclick="tutupBarcodeModal()">
                <i class="bi bi-x-circle me-1"></i>Tutup
            </button>
        </div>
    </div>
</div>

<div>
    <div class="row g-4">
        {{-- Kolom Kiri: Member Card & Profil Detail --}}
        <div class="col-lg-4">
            {{-- Kartu Virtual Member 3D --}}
            <div class="virtual-card-container mb-4">
                <div class="virtual-card" id="virtualCard" onclick="flipCard()">
                    <div class="card-glare" id="cardGlare"></div>
                    
                    @php
                        $isMember = $user->isMember();
                        $cardThemeClass = $isMember ? 'member' : $user->segmen_pelanggan;
                        $cardLabelHeader = $isMember ? 'Member Card' : 'Loyalty Card';
                        $badgeLabel = $isMember ? '👑 ' . strtoupper(str_replace('_', ' ', $user->kategori_member)) : $user->label_segmen;
                        $badgeClass = $isMember ? 'member' : $user->segmen_pelanggan;
                    @endphp
                    <!-- Card Front -->
                    <div class="card-side card-front card-{{ $cardThemeClass }}">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="fw-bold text-white-50" style="font-size: 0.65rem; letter-spacing: 1px; text-transform: uppercase;">{{ $cardLabelHeader }}</span>
                                <h6 class="fw-bold text-white mb-0" style="font-size: 0.95rem; letter-spacing: -0.5px;">ANBIYAA SPORT CLUB</h6>
                            </div>
                            <div class="card-chip"></div>
                        </div>
                        
                        <div>
                            <div class="text-white-50 small mb-1" style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 1px;">Saldo Poin Aktif</div>
                            <div class="poin-saldo-display text-white mb-1">{{ number_format($user->poin_saldo) }} <span style="font-size:1.2rem; font-weight: 500;">Pts</span></div>
                            
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="segmen-badge segmen-{{ $badgeClass }}">
                                    {{ $badgeLabel }}
                                </span>
                                @if($poinSegera > 0)
                                <span class="segmen-badge bg-warning-subtle text-warning border-warning-subtle" style="font-size:0.65rem;" title="Ada poin yang akan kadaluwarsa">
                                    ⚠️ {{ $poinSegera }} pts Exp
                                </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-end pt-2 border-top border-white-10">
                            <div style="min-width: 0;">
                                <div class="text-white fw-bold text-truncate" style="font-size:0.75rem; letter-spacing:0.5px;">{{ strtoupper($user->name) }}</div>
                                <div class="text-white-50" style="font-size:0.6rem; font-family: monospace;">ANB-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}</div>
                            </div>
                            <button class="btn btn-xs btn-light py-1 px-2.5 fw-bold text-dark rounded-pill" style="font-size: 0.65rem; z-index: 20;" onclick="event.stopPropagation(); flipCard()">
                                <i class="bi bi-info-circle me-1"></i>Info Poin
                            </button>
                        </div>
                    </div>
                    
                    <!-- Card Back -->
                    <div class="card-side card-back" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); text-align: left; align-items: stretch; justify-content: space-between; padding: 1.35rem 1.5rem;">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-1.5 border-bottom border-white-10">
                                <span class="fw-bold text-white-50" style="font-size: 0.68rem; letter-spacing: 1px; text-transform: uppercase;">Ketentuan &amp; Perolehan Poin</span>
                                <i class="bi bi-info-circle text-white-50" style="font-size: 0.85rem;"></i>
                            </div>
                            <div class="d-flex flex-column gap-2.5" style="font-size: 0.68rem; color: #cbd5e1; line-height: 1.45;">
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-circle-fill text-white-50" style="font-size: 0.35rem; margin-top: 5px;"></i>
                                    <div><strong>Rasio Standard:</strong> Setiap kelipatan transaksi sewa lapangan sebesar Rp 5.000 bernilai 1 Poin.</div>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-circle-fill text-white-50" style="font-size: 0.35rem; margin-top: 5px;"></i>
                                    <div><strong>Double Poin (2x):</strong> Berlaku khusus penyewaan lapangan pada jam Off-Peak Weekdays (Senin–Jumat, 07:00–16:00).</div>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-circle-fill text-white-50" style="font-size: 0.35rem; margin-top: 5px;"></i>
                                    <div><strong>Fasilitas Tambahan:</strong> Mendapatkan +5 Poin per sewa raket, +3 Poin per kok satuan, dan +27 Poin per kok slop.</div>
                                </div>
                                <div class="d-flex align-items-start gap-2">
                                    <i class="bi bi-circle-fill text-white-50" style="font-size: 0.35rem; margin-top: 5px;"></i>
                                    <div><strong>Masa Berlaku Poin:</strong> Poin yang diperoleh akan kadaluwarsa secara otomatis dalam 6 bulan.</div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end pt-1.5 border-top border-white-10">
                            <button class="btn btn-xs btn-outline-light rounded-pill px-3 py-0.5 fw-bold" style="font-size:0.65rem;" onclick="event.stopPropagation(); flipCard()">
                                <i class="bi bi-arrow-left-right me-1"></i>Kembali
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kartu Info Profil --}}
            <div class="profile-sidebar-card mb-4">
                <div class="profile-hero">
                    <div class="avatar-wrapper">
                        @if($user->foto_profil)
                            <img src="{{ asset('storage/' . $user->foto_profil) }}"
                                 alt="Foto Profil" class="avatar-img" id="sidebarAvatar">
                        @else
                            <div class="avatar-placeholder">
                                <i class="bi bi-person-fill"></i>
                            </div>
                        @endif
                    </div>
                    <h6 class="fw-bold mt-2 mb-1">{{ $user->name }}</h6>
                    <p class="mb-0 text-white-50 small" style="font-size: 0.75rem;"><i class="bi bi-envelope me-1"></i>{{ $user->email }}</p>
                </div>

                <div class="p-4">
                    {{-- Progress Kelengkapan Profil --}}
                    <div class="progress-card mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="small fw-bold text-dark" style="font-size:.75rem;">Kelengkapan Profil</span>
                            <span class="small fw-800 text-primary" style="font-size:.75rem;">{{ $completeness }}%</span>
                        </div>
                        <div class="progress" style="height: 6px; background: #e2e8f0; border-radius: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" 
                                 style="width: {{ $completeness }}%; background: linear-gradient(135deg, #2563eb 0%, #10b981 100%); border-radius: 10px;"></div>
                        </div>
                        @if($completeness < 100)
                            <small class="text-muted d-block mt-2" style="font-size: .68rem;">
                                Lengkapi nomor HP & alamat untuk kemudahan booking.
                            </small>
                        @else
                            <small class="text-success fw-bold d-block mt-2" style="font-size: .68rem;">
                                <i class="bi bi-check-circle-fill me-1"></i>Profil Anda lengkap!
                            </small>
                        @endif
                    </div>

                    {{-- Informasi Detail --}}
                    <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary" style="width: 30px; height: 30px;">
                            <i class="bi bi-telephone-fill" style="font-size: .8rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: .68rem; line-height: 1;">No. Telepon</small>
                            <span class="fw-600 text-dark" style="font-size: .8rem;">{{ $user->nomor_hp ?? 'Belum diisi' }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 py-2 border-bottom">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary" style="width: 30px; height: 30px;">
                            <i class="bi bi-geo-alt-fill" style="font-size: .8rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: .68rem; line-height: 1;">Alamat Rumah</small>
                            <span class="fw-600 text-dark text-truncate d-block" style="font-size: .8rem; max-width: 180px;" title="{{ $user->alamat }}">{{ $user->alamat ?? 'Belum diisi' }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 py-2">
                        <div class="rounded-circle d-flex align-items-center justify-content-center bg-light text-primary" style="width: 30px; height: 30px;">
                            <i class="bi bi-calendar-check-fill" style="font-size: .8rem;"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: .68rem; line-height: 1;">Bergabung Sejak</small>
                            <span class="fw-600 text-dark" style="font-size: .8rem;">{{ $user->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Banner Membership --}}
            <div class="member-banner mb-4">
                @if($user->isMember())
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-success rounded-pill" style="font-size:.6rem; letter-spacing:0.5px;">MEMBER AKTIF</span>
                        <i class="bi bi-patch-check-fill text-info fs-6"></i>
                    </div>
                    <h6 class="fw-bold mb-1" style="font-size: 0.85rem;">Anbiyaa Exclusive Slot</h6>
                    <p class="mb-3 small" style="opacity: 0.85; font-size:.72rem;">
                        Anda menikmati prioritas booking, harga spesial member, dan jadwal tetap mingguan.
                    </p>
                    <a href="{{ route('membership.index') }}" class="btn btn-xs btn-light w-100 rounded-pill py-1.5 fw-bold" style="font-size:.7rem;">
                        Lihat Keuntungan <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                @else
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="badge bg-secondary rounded-pill" style="font-size:.6rem; letter-spacing:0.5px;">NON-MEMBER</span>
                        <i class="bi bi-star-fill text-warning fs-6"></i>
                    </div>
                    <h6 class="fw-bold mb-1" style="font-size: 0.85rem;">Upgrade ke Member GOR</h6>
                    <p class="mb-3 small" style="opacity: 0.85; font-size:.72rem;">
                        Dapatkan slot jadwal tetap mingguan dan harga hemat rutin bulanan.
                    </p>
                    <a href="{{ route('membership.index') }}" class="btn btn-xs btn-light w-100 rounded-pill py-1.5 fw-bold" style="font-size:.7rem;">
                        Daftar Member Sekarang <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Statistik & Tabs --}}
        <div class="col-lg-8">
            {{-- Row Statistik --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <a href="{{ route('booking.riwayat') }}" class="text-decoration-none text-dark d-block h-100">
                        <div class="p-stat-box blue">
                            <div class="icon" style="background:#e0e7ff; color:#2563eb;">
                                <i class="bi bi-calendar-event"></i>
                            </div>
                            <div class="num text-primary" style="font-size: 1.5rem;">{{ $stats['total_booking'] }}</div>
                            <div class="lbl" style="font-size: 0.7rem;">Total Booking</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('booking.riwayat', ['status' => 'selesai']) }}" class="text-decoration-none text-dark d-block h-100">
                        <div class="p-stat-box green">
                            <div class="icon" style="background:#d1fae5; color:#10b981;">
                                <i class="bi bi-check-circle-fill"></i>
                            </div>
                            <div class="num text-success" style="font-size: 1.5rem;">{{ $stats['booking_selesai'] }}</div>
                            <div class="lbl" style="font-size: 0.7rem;">Selesai</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('booking.riwayat', ['status' => 'pending']) }}" class="text-decoration-none text-dark d-block h-100">
                        <div class="p-stat-box warning">
                            <div class="icon" style="background:#fef3c7; color:#d97706;">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <div class="num text-warning" style="font-size: 1.5rem;">{{ $stats['booking_pending'] }}</div>
                            <div class="lbl" style="font-size: 0.7rem;">Pending</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-3">
                    <a href="{{ route('booking.riwayat') }}" class="text-decoration-none text-dark d-block h-100">
                        <div class="p-stat-box purple">
                            <div class="icon" style="background:#f3e8ff; color:#8b5cf6;">
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <div class="num text-purple" style="font-size: 1.05rem; font-weight:800; padding: 0.3rem 0;">
                                Rp {{ number_format($stats['total_bayar'],0,',','.') }}
                            </div>
                            <div class="lbl" style="font-size: 0.7rem;">Total Pengeluaran</div>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Card Navigasi & Form --}}
            <div class="table-card">
                <div class="table-card-header bg-light border-bottom py-2 px-3">
                    <ul class="nav nav-pills profile-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link tab-loyalty {{ $activeTab === 'loyalty' ? 'active' : '' }}" id="loyalty-rewards-tab" data-bs-toggle="tab" 
                                    data-bs-target="#loyalty-rewards" type="button" role="tab" 
                                    aria-controls="loyalty-rewards" aria-selected="{{ $activeTab === 'loyalty' ? 'true' : 'false' }}">
                                <div class="tab-btn-content">
                                    <div class="tab-btn-icon">
                                        <i class="bi bi-gift-fill"></i>
                                    </div>
                                    <div class="tab-btn-info">
                                        <div class="tab-btn-title">Loyalty &amp; Voucher</div>
                                        <div class="tab-btn-subtitle">
                                            @php
                                                $activeVouchersCount = $vouchers->count() + \App\Models\Voucher::where('user_id', $user->id)->where('status', 'aktif')->where('expired_date', '>', now())->count();
                                            @endphp
                                            @if($activeVouchersCount > 0)
                                                <span>{{ $activeVouchersCount }} Voucher Aktif</span>
                                            @else
                                                <span class="text-muted">Klaim Reward</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link tab-profil {{ $activeTab === 'profil' ? 'active' : '' }}" id="edit-profile-tab" data-bs-toggle="tab" 
                                    data-bs-target="#edit-profile" type="button" role="tab" 
                                    aria-controls="edit-profile" aria-selected="{{ $activeTab === 'profil' ? 'true' : 'false' }}">
                                <div class="tab-btn-content">
                                    <div class="tab-btn-icon">
                                        <i class="bi bi-person-fill-gear"></i>
                                    </div>
                                    <div class="tab-btn-info">
                                        <div class="tab-btn-title">Ubah Profil</div>
                                        <div class="tab-btn-subtitle">
                                            <span class="text-muted">Kelola Akun</span>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link tab-keamanan {{ $activeTab === 'keamanan' ? 'active' : '' }}" id="change-password-tab" data-bs-toggle="tab" 
                                    data-bs-target="#change-password" type="button" role="tab" 
                                    aria-controls="change-password" aria-selected="{{ $activeTab === 'keamanan' ? 'true' : 'false' }}">
                                <div class="tab-btn-content">
                                    <div class="tab-btn-icon">
                                        <i class="bi bi-shield-lock-fill"></i>
                                    </div>
                                    <div class="tab-btn-info">
                                        <div class="tab-btn-title">Keamanan</div>
                                        <div class="tab-btn-subtitle">
                                            <span class="text-muted">Sandi &amp; PIN</span>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link tab-booking {{ $activeTab === 'booking' ? 'active' : '' }}" id="booking-history-tab" data-bs-toggle="tab" 
                                    data-bs-target="#booking-history" type="button" role="tab" 
                                    aria-controls="booking-history" aria-selected="{{ $activeTab === 'booking' ? 'true' : 'false' }}">
                                <div class="tab-btn-content">
                                    <div class="tab-btn-icon">
                                        <i class="bi bi-calendar3"></i>
                                    </div>
                                    <div class="tab-btn-info">
                                        <div class="tab-btn-title">Riwayat Booking</div>
                                        <div class="tab-btn-subtitle">
                                            @if($user->bookings->count() > 0)
                                                <span>{{ $user->bookings->count() }} Booking</span>
                                            @else
                                                <span class="text-muted">0 Booking</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link tab-poin {{ $activeTab === 'poin' ? 'active' : '' }}" id="point-history-tab" data-bs-toggle="tab" 
                                    data-bs-target="#point-history" type="button" role="tab" 
                                    aria-controls="point-history" aria-selected="{{ $activeTab === 'poin' ? 'true' : 'false' }}">
                                <div class="tab-btn-content">
                                    <div class="tab-btn-icon">
                                        <i class="bi bi-gem"></i>
                                    </div>
                                    <div class="tab-btn-info">
                                        <div class="tab-btn-title">Riwayat Poin</div>
                                        <div class="tab-btn-subtitle">
                                            <span>{{ number_format($user->poin_saldo) }} pts</span>
                                        </div>
                                    </div>
                                </div>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="p-4">
                    <div class="tab-content" id="profileTabsContent">
                        
                        {{-- Tab 1: Loyalty & Rewards --}}
                        <div class="tab-pane fade {{ $activeTab === 'loyalty' ? 'show active' : '' }}" id="loyalty-rewards" role="tabpanel" aria-labelledby="loyalty-rewards-tab">
                            
                            {{-- Roadmap Stepper --}}
                            <div class="roadmap-container">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold text-dark mb-0" style="font-size: 0.88rem;">🎖️ Road to VIP Player</h6>
                                        <small class="text-muted" style="font-size:0.72rem;">Akumulasi poin periode bulan ini</small>
                                    </div>
                                    <span class="badge bg-primary-subtle text-primary fw-bold" style="font-size:0.72rem;">
                                        @php
                                            $pb = $user->poin_bulanan;
                                        @endphp
                                        +{{ number_format($pb) }} Poin Bulan Ini
                                    </span>
                                </div>
                                
                                @php
                                    if ($pb <= 30) {
                                        $width = ($pb / 30) * 25;
                                    } elseif ($pb <= 80) {
                                        $width = 25 + (($pb - 30) / 50) * 25;
                                    } elseif ($pb <= 150) {
                                        $width = 50 + (($pb - 80) / 70) * 25;
                                    } elseif ($pb <= 250) {
                                        $width = 75 + (($pb - 150) / 100) * 25;
                                    } else {
                                        $width = 100;
                                    }
                                    
                                    $statusVisitor  = $pb >= 30 ? 'completed' : 'active';
                                    $statusAlly     = $pb >= 80 ? 'completed' : ($pb >= 30 ? 'active' : '');
                                    $statusPartner  = $pb >= 150 ? 'completed' : ($pb >= 80 ? 'active' : '');
                                    $statusLoyalist = $pb > 250 ? 'completed' : ($pb >= 150 ? 'active' : '');
                                    $statusVip      = $pb > 250 ? 'active' : '';
                                @endphp
                                
                                <div class="roadmap-stepper">
                                    <div class="roadmap-line"></div>
                                    <div class="roadmap-progress-line" style="width: {{ $width }}%"></div>
                                    
                                    <div class="roadmap-step visitor {{ $statusVisitor }}">
                                        <div class="roadmap-dot position-relative" title="Visitor - Akumulasi poin di bawah 30 pts">
                                            <span class="step-icon">👤</span>
                                            @if($pb >= 30)
                                            <span class="position-absolute translate-middle badge rounded-circle bg-success d-flex align-items-center justify-content-center" style="top: 5px; right: -8px; width: 15px; height: 15px; font-size: 0.55rem; padding: 0; border: 1.5px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 10;">
                                                <i class="bi bi-check-lg text-white"></i>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="roadmap-label">
                                            <span class="d-block fw-bold text-dark" style="font-size: 0.72rem;">Visitor</span>
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-1.5 py-0.5 rounded-pill" style="font-size:0.55rem;">&lt;30 pts</span>
                                        </div>
                                    </div>
                                    
                                    <div class="roadmap-step ally {{ $statusAlly }}">
                                        <div class="roadmap-dot position-relative" title="Ally - Mulai 30 pts. Dapat voucher Gratis Anbiyaa Water!">
                                            <span class="step-icon">🤝</span>
                                            @if($pb >= 80)
                                            <span class="position-absolute translate-middle badge rounded-circle bg-success d-flex align-items-center justify-content-center" style="top: 5px; right: -8px; width: 15px; height: 15px; font-size: 0.55rem; padding: 0; border: 1.5px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 10;">
                                                <i class="bi bi-check-lg text-white"></i>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="roadmap-label">
                                            <span class="d-block fw-bold text-dark" style="font-size: 0.72rem;">Ally</span>
                                            <span class="badge bg-info-subtle text-info border border-info-subtle px-1.5 py-0.5 rounded-pill" style="font-size:0.55rem;">30 pts</span>
                                        </div>
                                    </div>
                                    
                                    <div class="roadmap-step partner {{ $statusPartner }}">
                                        <div class="roadmap-dot position-relative" title="Partner - Mulai 80 pts. Dapat voucher sewa raket gratis!">
                                            <span class="step-icon">🏸</span>
                                            @if($pb >= 150)
                                            <span class="position-absolute translate-middle badge rounded-circle bg-success d-flex align-items-center justify-content-center" style="top: 5px; right: -8px; width: 15px; height: 15px; font-size: 0.55rem; padding: 0; border: 1.5px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 10;">
                                                <i class="bi bi-check-lg text-white"></i>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="roadmap-label">
                                            <span class="d-block fw-bold text-dark" style="font-size: 0.72rem;">Partner</span>
                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-1.5 py-0.5 rounded-pill" style="font-size:0.55rem;">80 pts</span>
                                        </div>
                                    </div>
                                    
                                    <div class="roadmap-step loyalist {{ $statusLoyalist }}">
                                        <div class="roadmap-dot position-relative" title="Loyalist - Mulai 150 pts. Dapat voucher sewa lapangan 1 jam gratis!">
                                            <span class="step-icon">👑</span>
                                            @if($pb > 250)
                                            <span class="position-absolute translate-middle badge rounded-circle bg-success d-flex align-items-center justify-content-center" style="top: 5px; right: -8px; width: 15px; height: 15px; font-size: 0.55rem; padding: 0; border: 1.5px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 10;">
                                                <i class="bi bi-check-lg text-white"></i>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="roadmap-label">
                                            <span class="d-block fw-bold text-dark" style="font-size: 0.72rem;">Loyalist</span>
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-1.5 py-0.5 rounded-pill" style="font-size:0.55rem;">150 pts</span>
                                        </div>
                                    </div>
                                    
                                    <div class="roadmap-step vip {{ $statusVip }}">
                                        <div class="roadmap-dot position-relative" title="VIP - Mulai 251 pts. Dapat voucher potongan Rp 100.000!">
                                            <span class="step-icon">💎</span>
                                            @if($pb > 250)
                                            <span class="position-absolute translate-middle badge rounded-circle bg-success d-flex align-items-center justify-content-center" style="top: 5px; right: -8px; width: 15px; height: 15px; font-size: 0.55rem; padding: 0; border: 1.5px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 10;">
                                                <i class="bi bi-check-lg text-white"></i>
                                            </span>
                                            @endif
                                        </div>
                                        <div class="roadmap-label">
                                            <span class="d-block fw-bold text-dark" style="font-size: 0.72rem;">VIP</span>
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5 rounded-pill" style="font-size:0.55rem;">250+ pts</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="segmen-card segmen-card-{{ $user->segmen_pelanggan }} mt-4 p-3 rounded-3 border d-flex gap-3 align-items-center">
                                    <div class="segmen-icon-wrapper rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.5rem;">
                                        @if($user->segmen_pelanggan === 'vip') 💎
                                        @elseif($user->segmen_pelanggan === 'loyalist') 👑
                                        @elseif($user->segmen_pelanggan === 'partner') 🏸
                                        @elseif($user->segmen_pelanggan === 'ally') 🤝
                                        @else 👤
                                        @endif
                                    </div>
                                    <div>
                                        <div class="fw-bold mb-1 header-text" style="font-size: 0.88rem;">
                                            @if($user->segmen_pelanggan === 'vip')
                                                🔥 Luar Biasa! Anda adalah VIP GOR Anbiyaa!
                                            @elseif($user->segmen_pelanggan === 'loyalist')
                                                ⚡ Hebat! Status Anda: Loyalist GOR Anbiyaa!
                                            @elseif($user->segmen_pelanggan === 'partner')
                                                🏸 Mantap! Anda Kini Berstatus Partner GOR Anbiyaa!
                                            @elseif($user->segmen_pelanggan === 'ally')
                                                💧 Keren! Status Anda Naik Menjadi Ally GOR Anbiyaa!
                                            @else
                                                🏸 Selamat Datang di Lapangan, Visitor GOR Anbiyaa!
                                            @endif
                                        </div>
                                        <p class="mb-0 desc-text" style="font-size: 0.76rem; line-height: 1.4;">
                                            @if($user->segmen_pelanggan === 'vip')
                                                Mahkota tertinggi telah di tangan Anda! Nikmati keistimewaan <strong>Double Points (2x)</strong> di jam off-peak dan klaim <strong>Voucher Potongan Rp 100.000</strong> untuk perpanjangan member bulanan Anda. Terus pertahankan performa juara Anda di lapangan!
                                            @elseif($user->segmen_pelanggan === 'loyalist')
                                                Anda berhak menikmati keuntungan ekstra berupa <strong>Double Points (2x)</strong> off-peak dan <strong>Voucher Gratis 1 Jam Sewa Lapangan Off-Peak</strong>! Cukup kumpulkan <strong>{{ max(1, 251 - $pb) }} poin</strong> lagi bulan ini untuk mengeklaim takhta kasta tertinggi: <strong>VIP Player</strong>!
                                            @elseif($user->segmen_pelanggan === 'partner')
                                                Siap-siap bermain lebih seru dengan bonus <strong>Voucher Gratis Sewa Raket (1 Sesi)</strong>! Ayo tingkatkan intensitas latihanmu; kumpulkan <strong>{{ max(1, 150 - $pb) }} poin</strong> lagi bulan ini untuk naik kelas menjadi <strong>Loyalist GOR</strong>.
                                            @elseif($user->segmen_pelanggan === 'ally')
                                                Segarkan tubuhmu setelah rally panjang dengan <strong>Voucher Gratis Anbiyaa Water Dingin</strong>! Target berikutnya sudah di depan mata: kumpulkan <strong>{{ max(1, 80 - $pb) }} poin</strong> lagi bulan ini untuk membuka status <strong>Partner GOR</strong>!
                                            @else
                                                Ini adalah langkah awal Anda menuju juara! Kumpulkan <strong>{{ max(1, 30 - $pb) }} poin</strong> lagi di bulan ini untuk naik level menjadi <strong>Ally</strong> dan dapatkan <strong>Voucher Gratis Anbiyaa Water Dingin</strong> sebagai booster energi Anda!
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>

                            {{-- Voucher Aktif ── Collapsible if count > 1 ── --}}
                            @php
                                $membershipVouchers = \App\Models\Voucher::where('user_id', $user->id)
                                    ->where('status', 'aktif')
                                    ->where('expired_date', '>', now())
                                    ->orderBy('created_at', 'desc')
                                    ->get();
                                $semuaVoucher = $vouchers->concat($membershipVouchers);
                            @endphp

                            @if($semuaVoucher->isNotEmpty())
                            <div class="mb-4">
                                @if($semuaVoucher->count() > 1)
                                    <div class="d-flex justify-content-between align-items-center mb-2.5">
                                        <h6 class="fw-bold text-dark mb-0" style="font-size:0.85rem;">
                                            <i class="bi bi-ticket-perforated-fill text-success me-1.5"></i>
                                            Voucher Aktif Saya ({{ $semuaVoucher->count() }})
                                        </h6>
                                        <button class="btn btn-xs btn-outline-success rounded-pill px-3 py-1 fw-bold" 
                                                type="button" 
                                                data-bs-toggle="collapse" 
                                                data-bs-target="#collapseVoucherList" 
                                                aria-expanded="false" 
                                                aria-controls="collapseVoucherList"
                                                id="btnToggleVouchers"
                                                style="font-size: 0.72rem;">
                                            <span class="btn-text">Lihat Voucher</span> <i class="bi bi-chevron-down ms-1"></i>
                                        </button>
                                    </div>
                                    <div class="collapse" id="collapseVoucherList">
                                        <div class="row g-2 mt-1 pb-2">
                                            @foreach($semuaVoucher as $v)
                                            @php
                                                $expDate = isset($v->kode_expired_at) ? $v->kode_expired_at : $v->expired_date;
                                                $fullCode = isset($v->kode_voucher) ? $v->kode_voucher : $v->voucher_code;
                                            @endphp
                                            <div class="col-12 col-sm-6">
                                                <div class="voucher-ticket">
                                                    {{-- Icon Column --}}
                                                    <div class="voucher-icon-col">{{ $v->icon_hadiah }}</div>
                                                    {{-- Content Column --}}
                                                    <div class="voucher-content-col">
                                                        <div class="voucher-label">{{ $v->label_hadiah }}</div>
                                                        <div class="voucher-exp-badge">
                                                            <i class="bi bi-clock" style="font-size:0.6rem;"></i>
                                                            Berlaku s/d {{ $expDate?->translatedFormat('d M Y') ?? 'N/A' }}
                                                        </div>
                                                        <button class="voucher-btn-use mt-1"
                                                                onclick="bukaVoucherModal('{{ $v->label_hadiah }}', '{{ $v->icon_hadiah }}', '{{ $expDate?->translatedFormat('d M Y') ?? 'N/A' }}', '{{ $v->id }}', {{ isset($v->kode_voucher) ? 'false' : 'true' }})">
                                                            <i class="bi bi-ticket-perforated me-1"></i>Detail & Gunakan
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <h6 class="fw-bold text-dark mb-2.5" style="font-size:0.85rem;">
                                        <i class="bi bi-ticket-perforated-fill text-success me-1.5"></i>
                                        Voucher Aktif Saya (1)
                                    </h6>
                                    <div class="row g-2">
                                        @php 
                                            $v = $semuaVoucher->first(); 
                                            $expDate = isset($v->kode_expired_at) ? $v->kode_expired_at : $v->expired_date;
                                        @endphp
                                        <div class="col-12 col-sm-6">
                                            <div class="voucher-ticket">
                                                {{-- Icon Column --}}
                                                <div class="voucher-icon-col">{{ $v->icon_hadiah }}</div>
                                                {{-- Content Column --}}
                                                <div class="voucher-content-col">
                                                    <div class="voucher-label">{{ $v->label_hadiah }}</div>
                                                    <div class="voucher-exp-badge">
                                                        <i class="bi bi-clock" style="font-size:0.6rem;"></i>
                                                        Berlaku s/d {{ $expDate?->translatedFormat('d M Y') ?? 'N/A' }}
                                                    </div>
                                                    <button class="voucher-btn-use mt-1"
                                                            onclick="bukaVoucherModal('{{ $v->label_hadiah }}', '{{ $v->icon_hadiah }}', '{{ $expDate?->translatedFormat('d M Y') ?? 'N/A' }}', '{{ $v->id }}', {{ isset($v->kode_voucher) ? 'false' : 'true' }})">
                                                        <i class="bi bi-ticket-perforated me-1"></i>Detail & Gunakan
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            @endif

                            {{-- Menu Penukaran Poin --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2.5">
                                    <h6 class="fw-bold mb-0 text-dark" style="font-size:0.85rem;">🎁 Hadiah Penukaran Poin</h6>
                                    <span class="badge bg-primary-subtle text-primary" style="font-size:0.7rem; padding: 5px 10px;">
                                        Saldo: {{ number_format($user->poin_saldo) }} Pts
                                    </span>
                                </div>

                                @php
                                    $jumlahBisaDitukar = collect($menuTukar)->filter(function($item, $key) use ($user) {
                                        $cukupPoin = $user->poin_saldo >= $item['poin'];
                                        $bisaAkses = empty($item['member_only']) || $user->isMember();
                                        return $cukupPoin && $bisaAkses;
                                    })->count();
                                    $jumlahKurangPoin = collect($menuTukar)->filter(fn($item) => $user->poin_saldo < $item['poin'])->count();
                                @endphp
                                <div class="filter-pills">
                                    <button type="button" class="filter-pill active all" onclick="filterGifts(event, 'all')">
                                        Semua ({{ count($menuTukar) }})
                                    </button>
                                    <button type="button" class="filter-pill redeemable" onclick="filterGifts(event, 'redeemable')">
                                        Bisa Ditukar ({{ $jumlahBisaDitukar }})
                                    </button>
                                    <button type="button" class="filter-pill locked" onclick="filterGifts(event, 'locked')">
                                        Kurang Poin ({{ $jumlahKurangPoin }})
                                    </button>
                                </div>

                                <div class="reward-grid">
                                    @foreach($menuTukar as $key => $item)
                                    @php
                                        $cukupPoin   = $user->poin_saldo >= $item['poin'];
                                        $memberOnly  = !empty($item['member_only']);
                                        $isMember    = $user->isMember();
                                        // Bisa ditukar hanya jika poin cukup DAN (bukan member_only ATAU sudah member)
                                        $bisa        = $cukupPoin && (!$memberOnly || $isMember);
                                        // Member-locked: poin cukup tapi bukan member
                                        $memberLocked = $memberOnly && !$isMember;
                                        $cardClass   = $bisa ? 'can-redeem' : ($memberLocked ? 'member-locked' : 'cannot-redeem');
                                    @endphp
                                    <div class="reward-card-item reward-card {{ $cardClass }}"
                                         data-bisa="{{ $bisa ? 'true' : 'false' }}"
                                         data-poin="{{ $item['poin'] }}"
                                         data-member-only="{{ $memberOnly ? 'true' : 'false' }}"
                                         id="reward-card-{{ $key }}">

                                        <div class="d-flex flex-column" style="flex: 1; gap: 6px;">
                                            <div class="reward-icon">{{ $item['icon'] }}</div>
                                            <div class="d-flex align-items-baseline gap-1 mt-1">
                                                <span class="reward-poin-badge">{{ number_format($item['poin']) }}</span>
                                                <span class="reward-poin-unit">Poin</span>
                                            </div>
                                            <div class="fw-bold text-dark" style="font-size:0.83rem; line-height:1.35;">
                                                {{ $item['label'] }}
                                            </div>
                                            <div class="text-muted" style="font-size:0.7rem; line-height:1.4; flex-grow: 1;">
                                                {{ $item['deskripsi'] }}
                                            </div>
                                            @if($memberOnly)
                                            <div class="member-only-badge">
                                                <i class="bi bi-shield-lock-fill"></i> Khusus Member
                                            </div>
                                            @endif
                                        </div>

                                        <div class="mt-3">
                                            @if($bisa)
                                            {{-- Poin cukup + sudah member (jika diperlukan) --}}
                                            <form method="POST" action="{{ route('loyalty.tukar') }}"
                                                  class="form-tukar-poin d-none" id="form-tukar-{{ $key }}">
                                                @csrf
                                                <input type="hidden" name="jenis_hadiah" value="{{ $key }}">
                                            </form>
                                            <button type="button" class="btn-tukar btn-tukar-active"
                                                    onclick="bukaKonfirmasi('{{ $key }}', '{{ $item['poin'] }}', '{{ addslashes($item['label']) }}', '{{ $item['icon'] }}')">
                                                Tukar Sekarang
                                            </button>
                                            @elseif($memberLocked)
                                            {{-- Poin cukup tapi bukan member --}}
                                            <button class="btn-tukar btn-tukar-member-only" disabled>
                                                <i class="bi bi-shield-lock-fill me-1"></i>Daftar Member Dulu
                                            </button>
                                            @else
                                            {{-- Poin tidak cukup --}}
                                            <button class="btn-tukar btn-tukar-disabled" disabled id="btn-lock-{{ $key }}">
                                                Butuh {{ $item['poin'] - $user->poin_saldo }} pts lagi
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Simulator & Chart --}}
                            <div class="row g-3">
                                {{-- Simulator --}}
                                <div class="col-md-6">
                                    <div class="estimator-card d-flex flex-column justify-content-between">
                                        <div>
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <div style="font-size:1.2rem">🧮</div>
                                                <div>
                                                    <h6 class="fw-bold mb-0 text-dark" style="font-size:0.82rem;">Simulator Poin &amp; Hadiah</h6>
                                                    <small class="text-muted" style="font-size:0.65rem;">Cek hadiah ter-unlock saat transaksi</small>
                                                </div>
                                            </div>

                                            <div class="p-2.5 bg-light rounded-3 text-center mb-2.5">
                                                <div class="estimator-badge" id="estimatorDisplay">0</div>
                                                <div style="font-size:0.6rem;font-weight:700;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;">Estimasi Poin Diperoleh</div>
                                                <div class="mt-1 text-success fw-bold d-none" id="estimatorUnlocksText" style="font-size:0.68rem;">
                                                    <i class="bi bi-unlock-fill me-1"></i>Hadiah Baru Terbuka!
                                                </div>
                                            </div>

                                            <div class="d-flex flex-column gap-2.5">
                                                <div>
                                                    <label class="estimator-label d-flex justify-content-between">
                                                        <span>Durasi Bermain</span>
                                                        <span class="text-primary fw-bold" id="estDurasiVal">1 Jam</span>
                                                    </label>
                                                    <input type="range" class="estimator-slider w-100" id="estDurasi" min="1" max="8" value="2" oninput="hitungEstimasi()">
                                                </div>

                                                <div>
                                                    <label class="estimator-label d-block">Shift Waktu</label>
                                                    <select class="form-select form-select-sm w-100" id="estShift" onchange="hitungEstimasi()" style="font-size:0.75rem;">
                                                        <option value="2" selected>🌅 Off-Peak Weekdays (Double! 2x)</option>
                                                        <option value="1">⭐ Peak / Weekend (Standard 1x)</option>
                                                    </select>
                                                </div>

                                                <div style="border-top:1px dashed #e2e8f0; padding-top:8px">
                                                    <label class="estimator-label d-block mb-1.5">Fasilitas Tambahan</label>
                                                    <div class="d-flex flex-column gap-1.5">
                                                        <div class="d-flex align-items-center justify-content-between" style="font-size:0.75rem">
                                                            <span class="text-muted">Sewa Raket (+5 Pts)</span>
                                                            <div class="d-flex align-items-center gap-1.5">
                                                                <button class="btn btn-xs btn-outline-secondary py-0 px-1.5" onclick="adjustInput('estRaket', -1)">-</button>
                                                                <input type="text" class="text-center bg-transparent border-0 fw-bold" style="width:24px; font-size:0.75rem" id="estRaket" value="0" readonly>
                                                                <button class="btn btn-xs btn-outline-secondary py-0 px-1.5" onclick="adjustInput('estRaket', 1)">+</button>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between" style="font-size:0.75rem">
                                                            <span class="text-muted">Kok Satuan (+3 Pts)</span>
                                                            <div class="d-flex align-items-center gap-1.5">
                                                                <button class="btn btn-xs btn-outline-secondary py-0 px-1.5" onclick="adjustInput('estKok', -1)">-</button>
                                                                <input type="text" class="text-center bg-transparent border-0 fw-bold" style="width:24px; font-size:0.75rem" id="estKok" value="0" readonly>
                                                                <button class="btn btn-xs btn-outline-secondary py-0 px-1.5" onclick="adjustInput('estKok', 1)">+</button>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center justify-content-between" style="font-size:0.75rem">
                                                            <span class="text-muted">Kok Slop (+27 Pts)</span>
                                                            <div class="d-flex align-items-center gap-1.5">
                                                                <button class="btn btn-xs btn-outline-secondary py-0 px-1.5" onclick="adjustInput('estSlop', -1)">-</button>
                                                                <input type="text" class="text-center bg-transparent border-0 fw-bold" style="width:24px; font-size:0.75rem" id="estSlop" value="0" readonly>
                                                                <button class="btn btn-xs btn-outline-secondary py-0 px-1.5" onclick="adjustInput('estSlop', 1)">+</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Grafik --}}
                                <div class="col-md-6">
                                    <div class="chart-card">
                                        <h6 class="fw-bold mb-0 text-dark" style="font-size:0.82rem;">
                                            <i class="bi bi-graph-up-arrow me-1.5 text-primary"></i>Tren Poin Bulanan
                                        </h6>
                                        <small class="text-muted" style="font-size:0.65rem;">Total perolehan 6 bulan terakhir</small>
                                        
                                        <div class="chart-container" id="svgChartWrapper">
                                            @php
                                                $maxPoints = max(collect($monthlyPoints)->pluck('points')->toArray() ?: [100]);
                                                if ($maxPoints <= 0) $maxPoints = 100;
                                                
                                                $coords = [];
                                                $xStart = 35;
                                                $xInterval = 55;
                                                foreach ($monthlyPoints as $index => $mp) {
                                                    $x = $xStart + ($index * $xInterval);
                                                    $y = 110 - ($mp['points'] / $maxPoints) * 85;
                                                    $coords[] = ['x' => $x, 'y' => $y, 'points' => $mp['points'], 'label' => $mp['label']];
                                                }
                                                
                                                $pathD = '';
                                                $areaD = 'M ' . $coords[0]['x'] . ' 110';
                                                foreach ($coords as $i => $c) {
                                                    if ($i === 0) {
                                                        $pathD .= 'M ' . $c['x'] . ' ' . $c['y'];
                                                    } else {
                                                        $pathD .= ' L ' . $c['x'] . ' ' . $c['y'];
                                                    }
                                                    $areaD .= ' L ' . $c['x'] . ' ' . $c['y'];
                                                }
                                                $areaD .= ' L ' . end($coords)['x'] . ' 110 Z';
                                            @endphp
                                            <svg viewBox="0 0 320 130" width="100%" height="100%">
                                                <defs>
                                                    <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                                                        <stop offset="0%" stop-color="#3b82f6" stop-opacity="0.3"/>
                                                        <stop offset="100%" stop-color="#3b82f6" stop-opacity="0.0"/>
                                                    </linearGradient>
                                                </defs>
                                                <line x1="30" y1="20" x2="300" y2="20" stroke="#f8fafc" stroke-width="1"/>
                                                <line x1="30" y1="65" x2="300" y2="65" stroke="#f8fafc" stroke-width="1"/>
                                                <line x1="30" y1="110" x2="300" y2="110" stroke="#e2e8f0" stroke-width="1.5"/>
                                                
                                                <path d="{{ $areaD }}" fill="url(#chartGrad)"/>
                                                <path d="{{ $pathD }}" fill="none" stroke="#2563eb" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                
                                                @foreach($coords as $c)
                                                <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}" r="4" fill="#fff" stroke="#2563eb" stroke-width="2.5" style="cursor:pointer;"/>
                                                <circle cx="{{ $c['x'] }}" cy="{{ $c['y'] }}" r="10" fill="transparent" style="cursor:pointer;"
                                                        onmouseover="showChartTooltip(event, '{{ $c['label'] }}', '{{ $c['points'] }}')"
                                                        onmouseout="hideChartTooltip()"/>
                                                @endforeach
                                            </svg>
                                            <div class="chart-tooltip" id="chartTooltip"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Tab 2: Edit Profile --}}
                        <div class="tab-pane fade {{ $activeTab === 'profil' ? 'show active' : '' }}" id="edit-profile" role="tabpanel" aria-labelledby="edit-profile-tab">
                            <form method="POST" action="{{ route('profil.update') }}" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                {{-- Foto Profil --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark mb-2" style="font-size: .83rem;">Foto Profil Baru</label>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="image-preview-box" id="avatarPreviewContainer">
                                            @if($user->foto_profil)
                                                <img src="{{ asset('storage/' . $user->foto_profil) }}" id="avatarPreview" style="width:100%;height:100%;object-fit:cover;">
                                            @else
                                                <i class="bi bi-camera text-muted fs-4" id="avatarCameraIcon"></i>
                                                <img id="avatarPreview" style="width:100%;height:100%;object-fit:cover;display:none;">
                                            @endif
                                        </div>
                                        <div>
                                            <input type="file" name="foto_profil" id="foto_profil_input"
                                                   class="form-control form-control-sm" accept="image/*"
                                                   style="max-width:280px;">
                                            <small class="text-muted d-block mt-1">Format JPG, JPEG, PNG. Maksimal 2MB.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3 mb-3">
                                    {{-- Nama --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark" style="font-size: .83rem;">Nama Lengkap <span class="text-danger">*</span></label>
                                        <div class="input-group input-group-premium">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" name="name" class="form-control"
                                                   value="{{ old('name', $user->name) }}" required>
                                        </div>
                                    </div>

                                    {{-- No HP --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark" style="font-size: .83rem;">Nomor WhatsApp/HP</label>
                                        <div class="input-group input-group-premium">
                                            <span class="input-group-text"><i class="bi bi-whatsapp"></i></span>
                                            <input type="text" name="nomor_hp" class="form-control"
                                                   value="{{ old('nomor_hp', $user->nomor_hp) }}" placeholder="08xxxxxxxxxx">
                                        </div>
                                    </div>
                                </div>

                                {{-- Alamat --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold text-dark" style="font-size: .83rem;">Alamat Rumah</label>
                                    <div class="input-group input-group-premium">
                                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" name="alamat" class="form-control"
                                               value="{{ old('alamat', $user->alamat) }}" placeholder="Alamat lengkap Anda">
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm btn-sm">
                                        <i class="bi bi-cloud-arrow-up-fill me-1.5"></i>Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- Tab 3: Change Password --}}
                        <div class="tab-pane fade" id="change-password" role="tabpanel" aria-labelledby="change-password-tab">
                            <form method="POST" action="{{ route('profil.update') }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="name" value="{{ $user->name }}">
                                <input type="hidden" name="nomor_hp" value="{{ $user->nomor_hp }}">
                                <input type="hidden" name="alamat" value="{{ $user->alamat }}">

                                <div class="alert alert-info rounded-3 py-2.5 mb-4 border-0" style="background:#eff6ff; color:#1e40af;">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    <span style="font-size: .78rem;">Pastikan password baru minimal 6 karakter dan konfirmasi password cocok.</span>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark" style="font-size: .83rem;">Password Baru</label>
                                        <div class="input-group input-group-premium">
                                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                                            <input type="password" name="password" class="form-control"
                                                   placeholder="Minimal 6 karakter" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark" style="font-size: .83rem;">Konfirmasi Password Baru</label>
                                        <div class="input-group input-group-premium">
                                            <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                            <input type="password" name="password_confirmation" class="form-control"
                                                   placeholder="Ulangi password baru" required>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 shadow-sm fw-bold btn-sm">
                                    <i class="bi bi-shield-lock-fill me-1.5"></i>Perbarui Password
                                </button>
                            </form>
                        </div>

                        {{-- Tab 4: Booking History --}}
                        <div class="tab-pane fade" id="booking-history" role="tabpanel" aria-labelledby="booking-history-tab">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0 text-dark" style="font-size:.85rem;"><i class="bi bi-calendar3 me-2 text-primary"></i>5 Booking Terakhir</h6>
                                <a href="{{ route('booking.riwayat') }}" class="btn btn-xs btn-outline-primary rounded-pill px-3" style="font-size:.7rem;">
                                    Semua Riwayat <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size:.8rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Lapangan</th>
                                            <th>Jadwal</th>
                                            <th>Harga</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($bookingTerbaru as $b)
                                        <tr>
                                            <td>
                                                <div class="fw-600 text-dark">{{ $b->lapangan->nama_lapangan ?? '-' }}</div>
                                                <small class="text-muted" style="font-size:.7rem;">
                                                    <i class="bi bi-calendar me-1"></i>{{ $b->tanggal_booking ? $b->tanggal_booking->format('d M Y') : $b->created_at->format('d M Y') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="small fw-500 text-dark">
                                                    {{ $b->jadwal ? $b->jadwal->jam_mulai . ' - ' . $b->jadwal->jam_selesai : '-' }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">Rp {{ number_format($b->total_harga,0,',','.') }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $bClass = [
                                                        'pending'      => 'badge-pending',
                                                        'dikonfirmasi' => 'badge-dikonfirmasi',
                                                        'dipesan'      => 'badge-dipesan',
                                                        'selesai'      => 'badge-selesai',
                                                        'dibatalkan'   => 'badge-dibatalkan',
                                                    ][$b->status] ?? 'badge-pending';
                                                @endphp
                                                <span class="badge {{ $bClass }} rounded-pill px-2.5 py-1" style="font-size:.68rem;">
                                                    {{ ucfirst($b->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox fs-4 d-block mb-2 opacity-25"></i>
                                                Belum ada riwayat booking.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Tab 5: Riwayat Poin --}}
                        <div class="tab-pane fade" id="point-history" role="tabpanel" aria-labelledby="point-history-tab">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0" style="font-size:.8rem;">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Tanggal</th>
                                            <th>Keterangan</th>
                                            <th>Sumber</th>
                                            <th class="text-end">Poin</th>
                                            <th class="text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($riwayat as $r)
                                        <tr>
                                            <td style="font-size:0.75rem; color:#64748b; white-space:nowrap">
                                                {{ $r->created_at->translatedFormat('d M Y') }}<br>
                                                <small>{{ $r->created_at->format('H:i') }}</small>
                                            </td>
                                            <td style="font-size:0.78rem; max-width:250px;">
                                                {{ $r->keterangan ?? '-' }}
                                                @if($r->tipe === 'kredit' && $r->expired_at)
                                                <br><small class="text-muted" style="font-size:0.65rem">
                                                    <i class="bi bi-hourglass me-1"></i>Exp: {{ $r->expired_at->translatedFormat('d M Y') }}
                                                </small>
                                                @endif
                                            </td>
                                            <td><small>{{ $r->label_sumber }}</small></td>
                                            <td class="text-end">
                                                <span class="{{ $r->tipe === 'kredit' ? 'poin-masuk' : 'poin-keluar' }}">
                                                    {{ $r->poin_formatted }}
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($r->tipe === 'kredit' && $r->is_expired)
                                                    <span class="badge badge-kedaluwarsa">Kadaluwarsa</span>
                                                @elseif($r->tipe === 'kredit')
                                                    <span class="badge badge-diverifikasi">Aktif</span>
                                                @else
                                                    <span class="badge" style="background:#fff0f0;color:#b91c1c; border:1px solid #fecaca; font-size:.65rem; padding: 2px 6px;">Digunakan</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-4 text-muted">
                                                <i class="bi bi-inbox fs-4 d-block mb-2 opacity-25"></i>
                                                Belum ada riwayat transaksi poin.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            @if($riwayat->hasPages())
                            <div class="p-3 border-top">
                                {{ $riwayat->appends(['tab' => 'loyalty'])->links() }}
                            </div>
                            @endif
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Avatar Image Preview ---
        const fileInput = document.getElementById('foto_profil_input');
        const preview = document.getElementById('avatarPreview');
        const cameraIcon = document.getElementById('avatarCameraIcon');

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                        if (cameraIcon) {
                            cameraIcon.style.display = 'none';
                        }
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        // --- Active Tab Handlers based on parameter or session ---
        // Jika activeTab dikirim dari backend, kita pastikan bootstrap tab ter-trigger
        const activeTabStr = '{{ $activeTab }}';
        if (activeTabStr === 'loyalty') {
            const triggerEl = document.querySelector('#loyalty-rewards-tab');
            if (triggerEl) {
                bootstrap.Tab.getInstance(triggerEl)?.show() || new bootstrap.Tab(triggerEl).show();
            }
        }
    });

    // ── Konfirmasi Tukar Poin ──
    let formTukarTarget = null;

    function bukaKonfirmasi(key, poin, label, icon) {
        formTukarTarget = document.getElementById('form-tukar-' + key);

        document.getElementById('konfirmasiIcon').textContent  = icon;
        document.getElementById('konfirmasiJudul').textContent = 'Tukar ' + poin + ' Poin';
        document.getElementById('konfirmasiLabel').textContent = label;

        document.getElementById('konfirmasiOverlay').classList.add('show');
    }

    function tutupKonfirmasi() {
        document.getElementById('konfirmasiOverlay').classList.remove('show');
        formTukarTarget = null;
    }

    document.getElementById('konfirmasiSubmitBtn').addEventListener('click', function () {
        if (formTukarTarget) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const jenisHadiah = formTukarTarget.querySelector('input[name="jenis_hadiah"]').value;
            
            const submitBtn = document.getElementById('konfirmasiSubmitBtn');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>...';
            
            fetch('{{ route("loyalty.tukar") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    jenis_hadiah: jenisHadiah
                })
            })
            .then(response => response.json())
            .then(res => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
                tutupKonfirmasi();
                
                if (res.success) {
                    Swal.fire({
                        title: '🎉 Penukaran Berhasil!',
                        html: `
                            <div class="text-center">
                                <div style="font-size:3rem; margin-bottom: 0.5rem;">${res.data.icon}</div>
                                <p class="text-muted mb-2">${res.data.label}</p>
                                <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-2" style="background:#dcfce7; border:1px solid #86efac; color:#15803d; font-size:0.75rem; font-weight:600;">
                                    <span style="width:7px;height:7px;background:#22c55e;border-radius:50%;display:inline-block;"></span>
                                    Voucher berhasil ditambahkan
                                </div>
                                <p class="small text-muted mt-1">
                                    <i class="bi bi-clock me-1"></i>Berlaku hingga <strong>${res.data.expired_at}</strong><br>
                                    Voucher siap digunakan! Gunakan voucher ini saat melakukan <strong>Booking Online</strong> lapangan.
                                </p>
                            </div>
                        `,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: '<i class="bi bi-calendar-plus me-1"></i>Gunakan Sekarang',
                        cancelButtonText: 'Tutup',
                        confirmButtonColor: '#2563eb',
                        cancelButtonColor: '#64748b',
                        customClass: {
                            popup: 'swal-premium',
                            confirmButton: 'swal-btn-premium btn btn-primary px-4',
                            cancelButton: 'btn btn-secondary px-4'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `{{ route('booking.index') }}?voucher_id=${res.data.id || ''}`;
                        } else {
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Gagal Menukar',
                        text: res.message || 'Terjadi kesalahan sistem.',
                        icon: 'error',
                        confirmButtonColor: '#ef4444',
                        customClass: {
                            popup: 'swal-premium',
                            confirmButton: 'swal-btn-premium btn btn-danger px-4'
                        }
                    });
                }
            })
            .catch(err => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalHtml;
                tutupKonfirmasi();
                
                Swal.fire({
                    title: 'Koneksi Bermasalah',
                    text: 'Gagal terhubung ke server. Silakan coba beberapa saat lagi.',
                    icon: 'error',
                    confirmButtonColor: '#ef4444',
                    customClass: {
                        popup: 'swal-premium',
                        confirmButton: 'swal-btn-premium btn btn-danger px-4'
                    }
                });
            });
        }
    });

    document.getElementById('konfirmasiOverlay').addEventListener('click', function (e) {
        if (e.target === this) tutupKonfirmasi();
    });

    // ── Voucher Detail Modal ──
    function bukaVoucherModal(label, icon, expiry, voucherId, isMembership) {
        document.getElementById('barcodeModalLabel').textContent = label;
        document.getElementById('barcodeModalIcon').textContent = icon;
        const infoEl = document.getElementById('barcodeModalLabelInfo');
        if (infoEl) infoEl.textContent = label;
        const expEl = document.getElementById('barcodeModalExpiry');
        if (expEl) expEl.textContent = expiry || '-';
        
        const btnGunakan = document.getElementById('btnGunakanBooking');
        if (btnGunakan && voucherId) {
            if (isMembership) {
                btnGunakan.href = `{{ route('booking.index') }}?membership_voucher_id=${voucherId}`;
            } else {
                btnGunakan.href = `{{ route('booking.index') }}?voucher_id=${voucherId}`;
            }
        }
        
        document.getElementById('barcodeModal').classList.add('show');
    }
    // Legacy alias (backward compat jika masih ada call lama)
    function bukaBarcodeModal(label, code, icon) { bukaVoucherModal(label, icon, ''); }

    function tutupBarcodeModal() {
        document.getElementById('barcodeModal').classList.remove('show');
    }

    document.getElementById('barcodeModal').addEventListener('click', function (e) {
        if (e.target === this) tutupBarcodeModal();
    });

    // ── Salin Kode Voucher ──
    function salinKode(btn) {
        const kode = btn.getAttribute('data-kode');
        navigator.clipboard.writeText(kode).then(function () {
            const origHtml = btn.innerHTML;
            btn.innerHTML = 'Berhasil';
            btn.classList.add('btn-light-success');
            setTimeout(function () {
                btn.innerHTML = origHtml;
                btn.classList.remove('btn-light-success');
            }, 2000);
        }).catch(function () {
            Swal.fire({
                title: 'Kode Voucher',
                text: kode,
                icon: 'info',
                confirmButtonColor: '#3b82f6',
            });
        });
    }

    // ── Filter Hadiah ──
    function filterGifts(event, type) {
        document.querySelectorAll('.filter-pill').forEach(btn => btn.classList.remove('active', 'all', 'redeemable', 'locked'));
        
        const clickedBtn = event.currentTarget;
        clickedBtn.classList.add('active');
        
        if (type === 'all') {
            clickedBtn.classList.add('all');
            document.querySelectorAll('.reward-card-item').forEach(card => card.style.display = 'flex');
        } else if (type === 'redeemable') {
            clickedBtn.classList.add('redeemable');
            document.querySelectorAll('.reward-card-item').forEach(card => {
                card.style.display = card.dataset.bisa === 'true' ? 'flex' : 'none';
            });
        } else if (type === 'locked') {
            clickedBtn.classList.add('locked');
            document.querySelectorAll('.reward-card-item').forEach(card => {
                card.style.display = card.dataset.bisa === 'false' ? 'flex' : 'none';
            });
        }
    }

    // ── 3D Card Rotasi & Hologram Effect ──
    const card = document.getElementById('virtualCard');
    const glare = document.getElementById('cardGlare');

    if (card) {
        card.addEventListener('mousemove', function(e) {
            if (card.classList.contains('flipped')) {
                glare.style.background = 'transparent';
                return;
            }
            
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const xc = rect.width / 2;
            const yc = rect.height / 2;
            
            const rotateX = (yc - y) / 8;
            const rotateY = (x - xc) / 8;
            
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            
            const glareX = (x / rect.width) * 100;
            const glareY = (y / rect.height) * 100;
            glare.style.background = `
                radial-gradient(circle at ${glareX}% ${glareY}%, rgba(255,255,255,0.18) 0%, transparent 55%),
                linear-gradient(${135 + rotateY * 2.5}deg, rgba(255,0,128,0.05) 0%, rgba(0,255,240,0.05) 45%, rgba(255,230,0,0.05) 80%, transparent 100%)
            `;
        });
        
        card.addEventListener('mouseleave', function() {
            if (card.classList.contains('flipped')) return;
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
            glare.style.background = 'transparent';
        });
    }

    function flipCard() {
        card.classList.toggle('flipped');
        if (card.classList.contains('flipped')) {
            card.style.transform = 'perspective(1000px) rotateY(180deg)';
        } else {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg)';
        }
    }

    // ── SVG Chart Tooltip ──
    function showChartTooltip(e, label, val) {
        const tooltip = document.getElementById('chartTooltip');
        const wrapper = document.getElementById('svgChartWrapper');
        const rect = wrapper.getBoundingClientRect();
        
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        
        tooltip.innerHTML = `<strong>${label}</strong><br><i class="bi bi-gem text-primary me-1"></i>+${parseInt(val).toLocaleString('id-ID')} Pts`;
        tooltip.style.left = x + 'px';
        tooltip.style.top = (y - 10) + 'px';
        tooltip.style.opacity = '1';
    }

    function hideChartTooltip() {
        document.getElementById('chartTooltip').style.opacity = '0';
    }

    // ── Estimator & Gamifikasi Hadiah ──
    const userPoinSaldo = {{ $user->poin_saldo }};
    const menuTukar = @json($menuTukar);

    function adjustInput(id, amt) {
        const input = document.getElementById(id);
        let val = parseInt(input.value) || 0;
        val = Math.max(0, val + amt);
        input.value = val;
        hitungEstimasi();
    }

    function hitungEstimasi() {
        const durasi = parseInt(document.getElementById('estDurasi').value) || 1;
        document.getElementById('estDurasiVal').textContent = durasi + ' Jam';
        
        const shift = parseInt(document.getElementById('estShift').value) || 1;
        const raket = parseInt(document.getElementById('estRaket').value) || 0;
        const kok = parseInt(document.getElementById('estKok').value) || 0;
        const slop = parseInt(document.getElementById('estSlop').value) || 0;
        
        const hargaLapanganEstimasi = durasi * 55000;
        const poinSewa = Math.floor(hargaLapanganEstimasi / 5000) * shift;
        const poinFasilitas = (raket * 5) + (kok * 3) + (slop * 27);
        const totalPoinEstimasi = poinSewa + poinFasilitas;
        
        const display = document.getElementById('estimatorDisplay');
        const oldVal = parseInt(display.textContent) || 0;
        
        display.textContent = totalPoinEstimasi.toLocaleString('id-ID');
        
        if (oldVal !== totalPoinEstimasi) {
            display.classList.remove('pulse');
            void display.offsetWidth;
            display.classList.add('pulse');
        }
        
        const totalPoinAkumulasi = userPoinSaldo + totalPoinEstimasi;
        let unlockedAnyNew = false;
        
        const isMember = {{ $user->isMember() ? 'true' : 'false' }};

        Object.keys(menuTukar).forEach(key => {
            const reward = menuTukar[key];
            const cardElement = document.getElementById('reward-card-' + key);
            const lockBtn = document.getElementById('btn-lock-' + key);

            if (!cardElement) return;

            // Skip: hadiah member_only tidak bisa di-estimasi jika bukan member
            if (reward.member_only && !isMember) return;

            cardElement.classList.remove('estimator-unlocked');
            if (lockBtn) {
                lockBtn.classList.remove('btn-tukar-estimator');
                lockBtn.innerHTML = `Butuh ${reward.poin - userPoinSaldo} pts lagi`;
            }

            if (userPoinSaldo < reward.poin && totalPoinAkumulasi >= reward.poin) {
                cardElement.classList.add('estimator-unlocked');
                unlockedAnyNew = true;

                if (lockBtn) {
                    lockBtn.classList.add('btn-tukar-estimator');
                    lockBtn.innerHTML = `<i class="bi bi-unlock-fill me-1"></i> Terbuka Setelah Main!`;
                }
            }
        });
        
        const unlockText = document.getElementById('estimatorUnlocksText');
        if (unlockText) {
            if (unlockedAnyNew && totalPoinEstimasi > 0) {
                unlockText.classList.remove('d-none');
            } else {
                unlockText.classList.add('d-none');
            }
        }
    }

    hitungEstimasi();

    // ── Handle Toggle collapseVoucherList ──
    const collapseVoucherList = document.getElementById('collapseVoucherList');
    if (collapseVoucherList) {
        collapseVoucherList.addEventListener('show.bs.collapse', function () {
            const btn = document.getElementById('btnToggleVouchers');
            if (btn) {
                btn.innerHTML = '<span class="btn-text">Tutup Voucher</span> <i class="bi bi-chevron-up ms-1"></i>';
                btn.classList.replace('btn-outline-success', 'btn-success');
            }
        });
        collapseVoucherList.addEventListener('hide.bs.collapse', function () {
            const btn = document.getElementById('btnToggleVouchers');
            if (btn) {
                btn.innerHTML = '<span class="btn-text">Lihat Voucher</span> <i class="bi bi-chevron-down ms-1"></i>';
                btn.classList.replace('btn-success', 'btn-outline-success');
            }
        });
    }
</script>
@endpush
@endsection
