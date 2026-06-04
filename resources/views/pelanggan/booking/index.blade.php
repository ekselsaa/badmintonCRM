@extends('layouts.app')
@section('title', 'Booking Lapangan')
@section('page_title', 'Booking Lapangan')
@section('page_subtitle', 'Pilih lapangan dan jadwal yang tersedia')


@push('styles')
<style>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

@media (min-width: 769px) {
    .main-content {
        width: calc(100% - 260px) !important;
    }
    .main-content.expanded {
        width: calc(100% - 72px) !important;
    }
}

@media (min-width: 992px) {
    /* Lock viewport height and layout scrolling */
    html, body {
        overflow: hidden !important;
        height: 100vh !important;
    }
    
    .main-content {
        height: 100vh;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    
    .content-area {
        flex: 1;
        min-height: 0;
        padding: 12px 24px !important;
        display: flex;
        flex-direction: column;
    }
    
    /* Flex chain down to dashboard */
    .content-area > .p-0,
    .content-area > .p-0 > #formBookingCreate {
        height: 100%;
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
    }
    
    .booking-dashboard {
        font-family: 'Plus Jakarta Sans', sans-serif;
        display: flex;
        gap: 24px;
        flex: 1;
        min-height: 0;
        height: auto !important;
        overflow: hidden;
        padding-bottom: 8px;
    }
}

@media (max-width: 991px) {
    .content-area {
        padding: 1rem !important;
    }
    .booking-dashboard {
        font-family: 'Plus Jakarta Sans', sans-serif;
        display: flex;
        flex-direction: column;
        height: auto;
        overflow: visible;
        padding: 0;
    }
}

.dashboard-col-left {
    flex: 1.25;
    overflow-y: auto;
    max-height: 100%;
    padding-right: 8px;
}

.dashboard-col-right {
    flex: 0.75;
    overflow: hidden;
    max-height: 100%;
    display: flex;
    flex-direction: column;
}

.court-visualizer-container {
    flex-shrink: 0;
}

@media (max-width: 991px) {
    .dashboard-col-left, .dashboard-col-right {
        overflow-y: visible;
        max-height: none;
        padding-right: 0;
    }
}

/* Custom Scrollbar for columns */
.dashboard-col-left::-webkit-scrollbar, .dashboard-col-right::-webkit-scrollbar {
    width: 6px;
}
.dashboard-col-left::-webkit-scrollbar-track, .dashboard-col-right::-webkit-scrollbar-track {
    background: transparent;
}
.dashboard-col-left::-webkit-scrollbar-thumb, .dashboard-col-right::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

/* Input Styles */
.bk-label { font-size: .75rem; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #64748b; margin-bottom: 6px; }
.bk-input {
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: .9rem;
    padding: 10px 14px;
    transition: border-color .2s, box-shadow .2s;
    background: #f8fafc;
}
.bk-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 3px rgba(59,130,246,.12); background:#fff; outline:none; }

/* Premium Card Selectors */
.court-option-card {
    background: #fff;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px 16px;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    display: flex;
    align-items: center;
    gap: 12px;
}
.court-option-card:hover {
    border-color: #cbd5e1;
    transform: translateY(-2px);
}
.court-option-card.active {
    border-color: #2563eb;
    background: #eff6ff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);
}
.court-option-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0; height: 0;
}

/* Payment Option Cards */
.payment-selector-cards {
    display: flex;
    gap: 12px;
}
.payment-selector-card {
    flex: 1;
    background: #fff;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px 16px;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    text-align: center;
    position: relative;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.payment-selector-card:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
    transform: translateY(-1px);
}
.payment-selector-card.active {
    border-color: #2563eb;
    background: #eff6ff;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.08);
}
.payment-selector-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    width: 0; height: 0;
}

/* Add-on Stepper Item */
.addon-stepper-card {
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: all 0.2s;
}
.addon-stepper-card:hover {
    border-color: #cbd5e1;
    background: #f8fafc;
}
.addon-stepper-icon {
    width: 36px; height: 36px;
    border-radius: 8px;
    background: #eff6ff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    color: #2563eb;
}

.qty-stepper { display:flex; align-items:center; gap:6px; }
.qty-btn {
    width: 28px; height: 28px; border-radius: 8px; border: 1.5px solid #e2e8f0;
    background: #fff; font-size: 1rem; line-height: 1;
    display:flex; align-items:center; justify-content:center;
    cursor:pointer; transition: all .15s; color: #475569; font-weight:700;
}
.qty-btn:hover { border-color: #3b82f6; color: #1d4ed8; background: #eff6ff; }
.qty-val { width: 30px; text-align:center; font-weight: 700; font-size: .9rem; color: #1e293b; transition: transform 0.15s; }

/* Micro-animation for stepper values */
@keyframes popScale {
    0% { transform: scale(1); }
    50% { transform: scale(1.3); }
    100% { transform: scale(1); }
}
.pop-active {
    animation: popScale 0.18s cubic-bezier(0.16, 1, 0.3, 1);
}

/* Tab buttons styled premium */
.premium-nav-pills {
    display: flex;
    background: #f1f5f9;
    padding: 4px;
    border-radius: 12px;
    margin-bottom: 14px;
    border: 1px solid #e2e8f0;
}
.premium-nav-pills .nav-link {
    flex: 1;
    border: none;
    background: transparent;
    color: #64748b;
    font-weight: 700;
    font-size: 0.8rem;
    padding: 6px 10px;
    border-radius: 8px;
    transition: all 0.2s;
    text-align: center;
}
.premium-nav-pills .nav-link.active {
    background: #fff;
    color: #2563eb;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
}

/* Live Checkout invoice style */
.checkout-panel {
    background: #fff;
    border-radius: 20px;
    border: 1px solid rgba(226,232,240,0.8);
    box-shadow: 0 10px 30px -10px rgba(0,0,0,0.05);
    padding: 16px 20px;
    display: flex;
    flex-direction: column;
    flex: 1;
    min-height: 0;
}
.checkout-tab-content {
    flex-grow: 1;
    overflow-y: auto;
    max-height: 100%;
    margin-bottom: 8px;
    padding-right: 4px;
    min-height: 0;
}
.checkout-tab-content .tab-pane {
    height: 100%;
}
.checkout-tab-content::-webkit-scrollbar {
    width: 4px;
}
.checkout-tab-content::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.invoice-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.82rem;
    color: #475569;
    padding: 6px 0;
    border-bottom: 1px solid #f1f5f9;
}
.invoice-total-box {
    background: linear-gradient(135deg, #eff6ff, #f0fdf4);
    border: 1.5px solid #bfdbfe;
    border-radius: 12px;
    padding: 12px 14px;
    margin-top: 12px;
    position: relative;
    box-shadow: 0 4px 12px rgba(37,99,235,0.04);
}

/* Visual Badminton Court Diagrams */
.visual-court-item {
    flex: 1;
    min-width: 90px;
    max-width: 140px;
    height: 54px;
    border-radius: 6px;
    overflow: hidden;
    position: relative;
    background: #0f172a;
    border: 2px solid rgba(255,255,255,0.08);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.court-mat {
    position: absolute;
    top: 3px;
    bottom: 3px;
    left: 3px;
    right: 3px;
    background: linear-gradient(135deg, #0d9488, #115e59);
    border: 1px solid rgba(255, 255, 255, 0.85); /* Doubles sidelines and baseline */
}

.court-line {
    position: absolute;
    background: rgba(255, 255, 255, 0.8);
}

/* Singles sidelines (horizontal, inner) */
.singles-sideline-top {
    top: 2px;
    left: 0;
    right: 0;
    height: 1px;
}
.singles-sideline-bottom {
    bottom: 2px;
    left: 0;
    right: 0;
    height: 1px;
}

/* Short service lines (vertical) */
.short-service-left {
    left: 32%;
    top: 0;
    bottom: 0;
    width: 1px;
}
.short-service-right {
    right: 32%;
    top: 0;
    bottom: 0;
    width: 1px;
}

/* Center lines (horizontal, from short service to back boundary) */
.center-line-left {
    left: 0;
    width: 32%;
    top: 50%;
    height: 1px;
    transform: translateY(-50%);
}
.center-line-right {
    right: 0;
    width: 32%;
    top: 50%;
    height: 1px;
    transform: translateY(-50%);
}

/* Doubles long service lines (vertical, inner from back boundary) */
.doubles-long-left {
    left: 4px;
    top: 0;
    bottom: 0;
    width: 1px;
}
.doubles-long-right {
    right: 4px;
    top: 0;
    bottom: 0;
    width: 1px;
}

/* Net (vertical center net line) */
.court-net {
    position: absolute;
    left: 50%;
    top: -2px;
    bottom: -2px;
    width: 2px;
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 0 4px rgba(255,255,255,0.6);
    z-index: 2;
}
.court-net::after {
    content: '';
    position: absolute;
    top: 0;
    bottom: 0;
    left: -2px;
    right: -2px;
    background: repeating-linear-gradient(
        0deg,
        transparent,
        transparent 2px,
        rgba(0, 0, 0, 0.35) 2px,
        rgba(0, 0, 0, 0.35) 4px
    );
}

/* Overlay info */
.court-overlay-info {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    background: rgba(15, 23, 42, 0.35);
    backdrop-filter: blur(0.5px);
    z-index: 5;
    transition: all 0.2s;
    gap: 2px;
}

.visual-court-item:hover .court-overlay-info {
    background: rgba(15, 23, 42, 0.15);
}

.court-name {
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.5px;
    text-shadow: 0 2px 4px rgba(0,0,0,0.85);
}

.court-status-badge {
    font-size: 0.55rem !important;
    font-weight: 800;
    padding: 2px 5px !important;
    border-radius: 3px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

/* Court Status Styling */
.visual-court-item.status-available {
    border-color: rgba(16, 185, 129, 0.15);
}
.visual-court-item.status-available .court-status-badge {
    background-color: rgba(16, 185, 129, 0.15);
    color: #10b981;
    border: 1px solid rgba(16, 185, 129, 0.3);
}
.visual-court-item.status-available:hover {
    box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
    border-color: #10b981;
    transform: translateY(-2px);
}

.visual-court-item.status-pending {
    border-color: rgba(245, 158, 11, 0.15);
}
.visual-court-item.status-pending .court-overlay-info {
    background: rgba(15, 23, 42, 0.6);
}
.visual-court-item.status-pending .court-status-badge {
    background-color: rgba(245, 158, 11, 0.15);
    color: #f59e0b;
    border: 1px solid rgba(245, 158, 11, 0.3);
}

.visual-court-item.status-dipesan {
    border-color: rgba(239, 68, 68, 0.15);
}
.visual-court-item.status-dipesan .court-overlay-info {
    background: rgba(239, 68, 68, 0.2);
}
.visual-court-item.status-dipesan .court-status-badge {
    background-color: #ef4444;
    color: #fff;
    border: 1px solid #ef4444;
}

/* Court Active Style */
.visual-court-item.active {
    border-color: #38bdf8 !important;
    box-shadow: 0 0 20px rgba(56, 189, 248, 0.5);
    transform: scale(1.03) translateY(-1px);
}
.visual-court-item.active .court-overlay-info {
    background: rgba(15, 23, 42, 0.1);
}

/* Timeline component */
.timeline-container {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px;
}
.timeline-scroll-wrapper {
    overflow-x: auto;
    width: 100%;
    scrollbar-width: thin;
    padding-bottom: 4px;
}
.timeline-scroll-wrapper::-webkit-scrollbar {
    height: 5px;
}
.timeline-scroll-wrapper::-webkit-scrollbar-track {
    background: transparent;
}
.timeline-scroll-wrapper::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.timeline-slots-grid {
    display: flex;
    gap: 6px;
    min-width: 680px;
}
.timeline-slot {
    flex: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #fff;
    border: 1.5px solid #e2e8f0;
    border-radius: 8px;
    padding: 6px 4px;
    cursor: pointer;
    transition: all 0.2s;
    user-select: none;
    min-width: 50px;
}
.timeline-slot:hover:not(.disabled) {
    border-color: #3b82f6;
    background: #eff6ff;
    transform: translateY(-1px);
}
.timeline-slot.disabled {
    cursor: not-allowed;
    background: #f1f5f9;
    border-color: #e2e8f0;
    opacity: 0.75;
}
.timeline-slot.status-booked {
    background: #fecaca !important;
    border-color: #fca5a5 !important;
    color: #b91c1c;
}
.timeline-slot.status-pending {
    background: #fef3c7 !important;
    border-color: #fde68a !important;
    color: #b45309;
}
.timeline-slot.status-selected {
    background: #2563eb !important;
    border-color: #1d4ed8 !important;
    color: #fff !important;
    box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
}
.timeline-slot-time {
    font-size: 0.72rem;
    font-weight: 700;
}
.timeline-slot-status {
    font-size: 0.55rem;
    font-weight: 600;
    opacity: 0.8;
    margin-top: 1px;
}
.legend-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}
.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

.stat-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border-radius: 14px;
    padding: 14px;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.voucher-item-card {
    transition: all 0.2s ease-in-out;
    cursor: pointer;
    user-select: none;
}
.voucher-item-card:hover {
    border-color: #93c5fd !important;
    background-color: #f0f9ff !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.06);
}
.voucher-item-card.active {
    border-color: #2563eb !important;
    background-color: #eff6ff !important;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.12);
}
.disabled-voucher-card {
    opacity: 0.5;
    pointer-events: none;
    background-color: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
}

/* Custom premium checkbox for voucher cards */
.voucher-checkbox {
    flex-shrink: 0;
    width: 17px !important;
    height: 17px !important;
    border: 2px solid #cbd5e1 !important;
    border-radius: 5px !important;
    background-color: #fff !important;
    cursor: pointer !important;
    transition: all 0.18s ease !important;
    appearance: none;
    -webkit-appearance: none;
    position: relative;
    margin-top: 0 !important;
}
.voucher-checkbox:checked {
    background-color: #2563eb !important;
    border-color: #2563eb !important;
}
.voucher-checkbox:checked::after {
    content: '';
    position: absolute;
    left: 4px;
    top: 1px;
    width: 5px;
    height: 9px;
    border: 2px solid #fff;
    border-top: none;
    border-left: none;
    transform: rotate(45deg);
}
.voucher-checkbox:hover:not(:checked) {
    border-color: #93c5fd !important;
    background-color: #eff6ff !important;
}

/* Fix: collapse wrapper must NOT have d-flex — Bootstrap sets display:none */
#collapseVoucherList .voucher-collapse-inner {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    padding-top: 0;
}

/* Toggle voucher button premium styling */
#btnToggleVouchers {
    font-size: 0.72rem;
    font-weight: 700;
    letter-spacing: 0.2px;
    padding: 7px 16px;
    border-radius: 50px;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}
#btnToggleVouchers:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
}

/* Utility: allow flex children to shrink below content size */
.min-width-0 {
    min-width: 0;
}
</style>
@endpush

@section('content')
<div class="p-0">
    <form id="formBookingCreate" action="{{ route('booking.store') }}" method="POST">
        @csrf

        {{-- Hidden fields synced dynamically --}}
        <input type="hidden" id="filter_lapangan_create" value="{{ request('lapangan_id') }}">
        <input type="hidden" id="filter_tanggal_create" value="{{ request('tanggal', $tanggal) }}">
        <input type="hidden" id="filter_status_create" value="">

        <div class="booking-dashboard">
            
            {{-- KOLOM KIRI: Form Parameter & Addons --}}
            <div class="dashboard-col-left">
                
                {{-- Card 1: Lapangan --}}
                <div class="table-card p-4 mb-3" style="background:#fff;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width:32px; height:32px;">
                            <i class="bi bi-grid text-primary" style="font-size: 0.85rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Pilih Lapangan</h6>
                    </div>
                    
                    <div class="row g-3">
                        @foreach($lapangans as $l)
                        <div class="col-sm-6">
                            <label class="court-option-card d-block {{ request('lapangan_id') == $l->id ? 'active' : '' }}" id="court-card-{{ $l->id }}">
                                <input type="radio" name="lapangan_id" value="{{ $l->id }}"
                                       data-weekday="{{ $l->harga_weekday }}" 
                                       data-weekend="{{ $l->harga_weekend }}"
                                       data-nama="{{ $l->nama_lapangan }}"
                                       {{ request('lapangan_id') == $l->id ? 'checked' : '' }}
                                       required
                                       onchange="selectCourt('{{ $l->id }}')">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width:36px; height:36px;">
                                        <i class="bi bi-hash text-primary fw-bold" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.82rem;">{{ $l->nama_lapangan }}</div>
                                        <div class="text-muted small" style="font-size: 0.72rem;">
                                            Rp {{ number_format($l->harga_weekday, 0, ',', '.') }} / Jam
                                        </div>
                                    </div>
                                </div>
                            </label>
                        </div>
                        @endforeach
                    </div>
                    
                    {{-- Badge harga aktif --}}
                    <div id="hargaBadgeCreate" class="mt-3 d-none">
                        <span id="hargaBadgeLabelCreate" class="badge rounded-pill px-3 py-2" style="font-size:0.8rem;"></span>
                        <span class="text-muted ms-2" style="font-size:0.75rem;" id="hargaBadgeSubCreate"></span>
                    </div>
                </div>

                {{-- Card 2: Tanggal & Waktu --}}
                <div class="table-card p-4 mb-3" style="background:#fff;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width:32px; height:32px;">
                            <i class="bi bi-calendar3 text-primary" style="font-size: 0.85rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Jadwal Booking</h6>
                    </div>
                    
                    <div class="mb-3">
                        <div class="bk-label">Pilih Tanggal</div>
                        <input type="date" name="tanggal" class="form-control bk-input" 
                               value="{{ request('tanggal', $tanggal) }}" 
                               min="{{ date('Y-m-d') }}" required 
                               onchange="onDateChange(this.value)">
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="bk-label">Jam Mulai</div>
                            <select name="jam_mulai" class="form-select bk-input" required>
                                <option value="">-- Jam Mulai --</option>
                                @php $times = ['07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00']; @endphp
                                @foreach($times as $t)
                                <option value="{{ $t }}" {{ request('jam_mulai') == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                                                <div class="col-6">
                            <div class="bk-label">Jam Selesai</div>
                            <select name="jam_selesai" class="form-select bk-input" required>
                                <option value="">-- Jam Selesai --</option>
                                @php $timesEnd = ['07:00','08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00','19:00','20:00','21:00','22:00','23:00','23:59']; @endphp
                                @foreach($timesEnd as $t)
                                <option value="{{ $t }}" {{ request('jam_selesai') == $t ? 'selected' : '' }}>{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    {{-- Timeline Jam Interaktif --}}
                    <div class="mt-3 pt-3 border-top" style="border-top: 1.5px dashed #e2e8f0 !important;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="bk-label mb-0">Timeline Jam (Klik untuk Memilih)</div>
                            <span class="badge bg-light text-secondary border" style="font-size: 0.65rem;" id="timeline-stage-badge">Pilih Mulai</span>
                        </div>
                        <div class="timeline-container">
                            <div class="timeline-scroll-wrapper">
                                <div class="timeline-slots-grid" id="timelineSlotsGrid">
                                    <!-- Diisi via Javascript -->
                                </div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap justify-content-between align-items-center mt-2 gap-2 px-1">
                            <div class="d-flex flex-wrap gap-2.5 small" style="font-size: 0.68rem; color: #64748b;">
                                <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:#fff; border:1px solid #cbd5e1;"></span> Kosong</span>
                                <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:#2563eb;"></span> Pilihan Anda</span>
                                <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:#fecaca; border:1px solid #fca5a5;"></span> Dipesan</span>
                                <span class="d-flex align-items-center gap-1"><span class="legend-dot" style="background:#fef3c7; border:1px solid #fde68a;"></span> Pending</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Fasilitas Tambahan --}}
                <div class="table-card p-4 mb-3" style="background:#fff;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width:32px; height:32px;">
                            <i class="bi bi-box-seam text-primary" style="font-size: 0.85rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Fasilitas Tambahan (Opsional)</h6>
                    </div>
                    
                    <div class="d-flex flex-column gap-2">
                        @foreach($fasilitas_list as $f)
                        <div>
                            <div class="addon-stepper-card">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="addon-stepper-icon">
                                        <i class="bi {{ $f->icon }}"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark" style="font-size: 0.82rem;">{{ $f->nama }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            +Rp {{ number_format($f->harga, 0, ',', '.') }}
                                            <span id="badge-stok-{{ $f->id }}">
                                                @if($f->stok > 0)
                                                    <span class="badge bg-light text-primary ms-1" style="font-size:0.6rem; border:1px solid #bfdbfe;">Sisa: {{ $f->stok }}</span>
                                                @else
                                                    <span class="badge bg-light text-danger ms-1" style="font-size:0.6rem; border:1px solid #fecaca;">Habis</span>
                                                @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="qty-stepper">
                                    <button type="button" class="qty-btn btn-minus" id="btn-minus-{{ $f->id }}" disabled>−</button>
                                    <span class="qty-val" id="qty-val-{{ $f->id }}">
                                        @php
                                            $pt = 0;
                                            $nameNorm = strtolower($f->nama);
                                            if (str_contains($nameNorm, 'raket')) $pt = 5;
                                            elseif (str_contains($nameNorm, 'slop') || str_contains($nameNorm, 'dos')) $pt = 27;
                                            elseif (str_contains($nameNorm, 'kok') || str_contains($nameNorm, 'shuttlecock')) $pt = 3;
                                        @endphp
                                        <input type="text" name="fasilitas[{{ $f->id }}]" class="qty-input" 
                                               id="qty-input-{{ $f->id }}"
                                               data-id="{{ $f->id }}" 
                                               data-harga="{{ $f->harga }}" 
                                               data-nama="{{ $f->nama }}"
                                               data-max="{{ $f->stok }}"
                                               data-poin="{{ $pt }}"
                                               value="0" readonly
                                               style="width:30px;border:none;background:transparent;text-align:center;font-weight:700;font-size:.9rem;color:#1e293b;">
                                    </span>
                                    <button type="button" class="qty-btn btn-plus" id="btn-plus-{{ $f->id }}">+</button>
                                </div>
                            </div>
                            <div id="info-stok-{{ $f->id }}" class="small text-muted d-none mt-1" style="font-size:0.75rem; margin-left: 50px;"></div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Card 4: Loyalty & Voucher --}}
                <div class="table-card p-4 mb-3" style="background:#fff;">
                    <div class="p-3 rounded-3" style="background:#f8fafc; border:1.5px solid #e2e8f0;">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <div class="rounded-circle bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width:28px; height:28px;">
                                <i class="bi bi-gem text-primary" style="font-size: 0.8rem;"></i>
                            </div>
                            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.85rem;">Loyalty Points &amp; Voucher</h6>
                        </div>
                        <p class="text-muted mb-2.5" style="font-size:0.72rem; line-height: 1.4;">
                            Saldo Anda: <strong>{{ number_format(auth()->user()->poin_saldo) }} Poin</strong>. Gunakan voucher diskon yang telah diklaim di menu <a href="{{ route('loyalty.index') }}" class="text-primary fw-bold text-decoration-underline">Loyalty Points</a>.
                        </p>

                         <div class="mb-1">
                             <label class="form-label small fw-bold text-secondary mb-2" style="font-size:0.75rem; display:block;">Pilih Voucher Aktif (Bisa pilih lebih dari satu):</label>

                             @if($vouchers->isEmpty() && $membershipVouchers->isEmpty())
                                 <div class="text-center py-3 text-muted" style="border: 1.5px dashed #cbd5e1; border-radius: 10px; font-size: 0.78rem; background: #fff;">
                                     <i class="bi bi-ticket-perforated d-block mb-1 fs-5"></i>
                                     Anda tidak memiliki voucher aktif
                                 </div>
                             @else
                                  @php
                                      $allVouchersData = [];
                                      foreach($vouchers as $v) {
                                          $allVouchersData[] = [
                                              'id' => 'r_' . $v->id,
                                              'input_name' => 'voucher_ids[]',
                                              'input_value' => $v->id,
                                              'data_jenis' => $v->jenis_hadiah,
                                              'data_label' => $v->label_hadiah,
                                              'icon' => $v->icon_hadiah,
                                              'label' => $v->label_hadiah,
                                              'expired_at' => $v->kode_expired_at?->translatedFormat('d M Y') ?? 'N/A',
                                              'checked' => request('voucher_id') == $v->id
                                          ];
                                      }
                                      foreach($membershipVouchers as $mv) {
                                          $label = match($mv->tipe_voucher) {
                                              'ally' => 'Gratis Anbiyaa Water',
                                              'partner' => 'Gratis Sewa Raket 1 Sesi',
                                              'loyalist' => 'Gratis 1 Jam Lapangan Off-Peak',
                                              'vip' => 'Voucher VIP Potongan Rp 100.000',
                                              default => 'Voucher Keanggotaan ' . ucfirst($mv->tipe_voucher),
                                          };
                                          $icon = match($mv->tipe_voucher) {
                                              'ally' => '💧',
                                              'partner' => '🎾',
                                              'loyalist' => '☀️',
                                              'vip' => '👑',
                                              default => '🎫',
                                          };
                                          $allVouchersData[] = [
                                              'id' => 'm_' . $mv->id,
                                              'input_name' => 'membership_voucher_ids[]',
                                              'input_value' => $mv->id,
                                              'data_jenis' => 'membership_' . $mv->tipe_voucher,
                                              'data_label' => $label,
                                              'icon' => $icon,
                                              'label' => $label,
                                              'expired_at' => $mv->expired_date?->translatedFormat('d M Y') ?? 'N/A',
                                              'checked' => request('membership_voucher_id') == $mv->id
                                          ];
                                      }

                                      $anyCollapsedChecked = false;
                                      if (count($allVouchersData) > 1) {
                                          foreach(array_slice($allVouchersData, 1) as $vData) {
                                              if ($vData['checked']) {
                                                  $anyCollapsedChecked = true;
                                                  break;
                                              }
                                          }
                                      }
                                  @endphp

                                  <div class="voucher-list d-flex flex-column gap-2" id="voucherContainer">
                                      @php $firstV = $allVouchersData[0]; @endphp
                                      <div class="voucher-item-card rounded-3 d-flex align-items-center border" 
                                           id="card_{{ $firstV['id'] }}"
                                           data-expired="{{ $firstV['expired_at'] }}"
                                           style="background: #fff; border: 1.5px solid #e2e8f0; font-size: 0.8rem; padding: 10px 12px;" 
                                           onclick="toggleVoucherItem('{{ $firstV['id'] }}', false)">
                                          <div class="d-flex align-items-center gap-3 w-100">
                                              <input type="checkbox" name="{{ $firstV['input_name'] }}" value="{{ $firstV['input_value'] }}" id="chk_{{ $firstV['id'] }}"
                                                     class="voucher-checkbox"
                                                     data-jenis="{{ $firstV['data_jenis'] }}" 
                                                     data-label="{{ $firstV['data_label'] }}"
                                                     onclick="event.stopPropagation(); toggleVoucherItem('{{ $firstV['id'] }}', true)"
                                                     {{ $firstV['checked'] ? 'checked' : '' }}>
                                              <span class="flex-shrink-0" style="font-size: 1.25rem; line-height: 1;">{{ $firstV['icon'] }}</span>
                                              <div class="flex-grow-1 min-width-0">
                                                  <div class="fw-semibold text-dark" style="font-size:0.78rem; line-height:1.3;">{{ $firstV['label'] }}</div>
                                                  <div class="text-muted" id="msg_{{ $firstV['id'] }}" style="font-size: 0.65rem; margin-top:1px;">Berlaku s.d {{ $firstV['expired_at'] }}</div>
                                              </div>
                                          </div>
                                      </div>

                                      @if(count($allVouchersData) > 1)
                                          <div class="collapse {{ $anyCollapsedChecked ? 'show' : '' }}" id="collapseVoucherList">
                                              <div class="voucher-collapse-inner mt-1">
                                              @foreach(array_slice($allVouchersData, 1) as $vData)
                                                  <div class="voucher-item-card rounded-3 d-flex align-items-center border" 
                                                       id="card_{{ $vData['id'] }}"
                                                       data-expired="{{ $vData['expired_at'] }}"
                                                       style="background: #fff; border: 1.5px solid #e2e8f0; font-size: 0.8rem; padding: 10px 12px;" 
                                                       onclick="toggleVoucherItem('{{ $vData['id'] }}', false)">
                                                      <div class="d-flex align-items-center gap-3 w-100">
                                                          <input type="checkbox" name="{{ $vData['input_name'] }}" value="{{ $vData['input_value'] }}" id="chk_{{ $vData['id'] }}"
                                                                 class="voucher-checkbox"
                                                                 data-jenis="{{ $vData['data_jenis'] }}" 
                                                                 data-label="{{ $vData['data_label'] }}"
                                                                 onclick="event.stopPropagation(); toggleVoucherItem('{{ $vData['id'] }}', true)"
                                                                 {{ $vData['checked'] ? 'checked' : '' }}>
                                                          <span class="flex-shrink-0" style="font-size: 1.25rem; line-height: 1;">{{ $vData['icon'] }}</span>
                                                          <div class="flex-grow-1 min-width-0">
                                                              <div class="fw-semibold text-dark" style="font-size:0.78rem; line-height:1.3;">{{ $vData['label'] }}</div>
                                                              <div class="text-muted" id="msg_{{ $vData['id'] }}" style="font-size: 0.65rem; margin-top:1px;">Berlaku s.d {{ $vData['expired_at'] }}</div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              @endforeach
                                              </div>
                                          </div>

                                          <button type="button" class="btn btn-sm {{ $anyCollapsedChecked ? 'btn-primary' : 'btn-outline-primary' }} mt-1 w-100 rounded-pill d-flex align-items-center justify-content-center gap-1.5 fw-bold" 
                                                  id="btnToggleVouchers"
                                                  data-bs-toggle="collapse" 
                                                  data-bs-target="#collapseVoucherList" 
                                                  aria-expanded="{{ $anyCollapsedChecked ? 'true' : 'false' }}"
                                                  style="font-size: 0.72rem; transition: all 0.2s;">
                                              <span class="btn-text">{{ $anyCollapsedChecked ? 'Tutup Voucher' : 'Lihat Semua Voucher' }}</span> 
                                              <i class="bi {{ $anyCollapsedChecked ? 'bi-chevron-up' : 'bi-chevron-down' }} ms-1 toggle-icon" style="transition: transform 0.2s;"></i>
                                          </button>
                                      @endif
                                  </div>
                             @endif
                         </div>
                    </div>
                </div>

                {{-- Card 5: Metode Pembayaran --}}
                <div class="table-card p-4 mb-3" style="background:#fff;">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="rounded-circle bg-primary bg-opacity-10 p-2 d-flex align-items-center justify-content-center" style="width:32px; height:32px;">
                            <i class="bi bi-wallet2 text-primary" style="font-size: 0.85rem;"></i>
                        </div>
                        <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;">Metode Pembayaran</h6>
                    </div>
                    
                                        <div class="payment-selector-cards">
                        <label class="payment-selector-card active" id="card-qris">
                            <input type="radio" name="metode_pembayaran" value="qris" checked onchange="setPayment('qris')">
                            <div class="payment-card-icon mb-2">
                                <i class="bi bi-qr-code-scan text-primary fs-3"></i>
                            </div>
                            <div class="fw-bold text-dark" style="font-size: 0.8rem;">QRIS</div>
                            <div class="text-muted small" style="font-size: 0.65rem;">Konfirmasi Instan</div>
                        </label>
                        <label class="payment-selector-card" id="card-tunai">
                            <input type="radio" name="metode_pembayaran" value="tunai" onchange="setPayment('tunai')">
                            <div class="payment-card-icon mb-2">
                                <i class="bi bi-cash-coin text-success fs-3"></i>
                            </div>
                            <div class="fw-bold text-dark" style="font-size: 0.8rem;">Tunai</div>
                            <div class="text-muted small" style="font-size: 0.65rem;">Bayar di Tempat</div>
                        </label>
                    </div>
                </div>
            </div>
            
            {{-- KOLOM KANAN: Checkout Summary & Occupied Slots --}}
            <div class="dashboard-col-right">
                

                {{-- Checkout Invoice Panel --}}
                <div class="checkout-panel">
                    
                    {{-- Nav Tab --}}
                    <ul class="nav nav-pills premium-nav-pills" id="checkoutTab" role="tablist">
                        <li class="nav-item" role="presentation" style="flex: 1;">
                            <button class="nav-link active" id="summary-tab" data-bs-toggle="tab" data-bs-target="#summary-tab-pane" type="button" role="tab" aria-controls="summary-tab-pane" aria-selected="true">
                                <i class="bi bi-receipt me-1"></i>Ringkasan Bayar
                            </button>
                        </li>
                        <li class="nav-item" role="presentation" style="flex: 1;">
                            <button class="nav-link" id="occupied-tab" data-bs-toggle="tab" data-bs-target="#occupied-tab-pane" type="button" role="tab" aria-controls="occupied-tab-pane" aria-selected="false">
                                <i class="bi bi-calendar-x me-1"></i>Jadwal Terisi
                            </button>
                        </li>
                    </ul>
                    
                    {{-- Tab Contents --}}
                    <div class="tab-content checkout-tab-content" id="checkoutTabContent">
                        
                        {{-- Tab 1: Ringkasan Bayar --}}
                        <div class="tab-pane fade show active" id="summary-tab-pane" role="tabpanel" aria-labelledby="summary-tab" tabindex="0">
                            <div class="d-flex flex-column h-100">
                                
                                <div id="checkout-empty-state" class="text-center py-4 text-muted small">
                                    <i class="bi bi-cart-x fs-3 d-block mb-2 text-secondary"></i>
                                    Pilih lapangan dan jadwal jam untuk melihat rincian biaya.
                                </div>
                                
                                <div id="checkout-details" class="d-none">
                                    <h6 class="fw-bold mb-2 text-dark pb-1 border-bottom" style="font-size: 0.85rem;">Rincian Booking</h6>
                                    
                                    <div class="invoice-item">
                                        <span>Lapangan</span>
                                        <span id="invoice-court-name" class="fw-bold text-dark">-</span>
                                    </div>
                                    <div class="invoice-item">
                                        <span>Tanggal</span>
                                        <span id="invoice-date" class="fw-bold text-dark">-</span>
                                    </div>
                                    <div class="invoice-item">
                                        <span>Durasi Sewa</span>
                                        <span id="invoice-time-range" class="fw-bold text-dark">-</span>
                                    </div>
                                    <div class="invoice-item">
                                        <span>Metode Bayar</span>
                                        <span id="invoice-payment" class="fw-bold text-dark">-</span>
                                    </div>
                                    
                                    {{-- Dinamis list fasilitas tambahan --}}
                                    <div id="invoice-facilities-list"></div>
                                    
                                    {{-- Diskon --}}
                                    <div id="invoice-discounts-list"></div>
                                </div>
                                
                                {{-- Input Catatan --}}
                                <div class="mt-1.5">
                                    <div class="bk-label" style="margin-bottom: 4px;">Catatan (Opsional)</div>
                                    <input type="text" name="catatan" class="form-control bk-input" placeholder="Tulis catatan jika ada..." style="font-size: 0.78rem; padding: 6px 10px; border-radius: 8px;">
                                </div>
                                
                                {{-- Invoice Total Box --}}
                                <div id="containerTotal" class="invoice-total-box d-none mt-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small fw-bold text-secondary">Estimasi Total Bayar:</span>
                                        <span class="h5 fw-bold text-primary mb-0" id="displayTotal" style="font-size: 1.3rem;">Rp 0</span>
                                    </div>
                                    <div class="text-muted mt-1" style="font-size: 0.72rem;" id="detailHitung"></div>
                                    
                                    {{-- Estimasi Poin --}}
                                    <div class="pt-2 mt-2 border-top d-flex justify-content-between align-items-center" style="border-top: 1.5px dashed #bfdbfe !important;">
                                        <span class="small fw-bold text-success" style="font-size:0.75rem;">
                                            <i class="bi bi-gem me-1"></i>Estimasi Poin Didapat:
                                        </span>
                                        <span class="badge bg-success text-white fw-bold px-2 py-1" style="font-size:0.75rem;" id="displayPoinEstimasi">+0 Poin</span>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        
                        {{-- Tab 2: Jadwal Terisi --}}
                        <div class="tab-pane fade" id="occupied-tab-pane" role="tabpanel" aria-labelledby="occupied-tab" tabindex="0">
                            <h6 class="fw-bold mb-3 text-danger pb-1 border-bottom" style="font-size: 0.85rem;" id="occupiedSchedulesHeader">
                                <i class="bi bi-info-circle me-1"></i>Jadwal Terisi
                            </h6>
                            <p class="text-muted small mb-3">Harap hindari memilih jam yang bertabrakan dengan jadwal di bawah ini.</p>
                            <div id="occupiedSchedulesList">
                                {{-- AJAX content --}}
                            </div>
                        </div>
                        
                    </div>
                    
                    {{-- Ticket Divider Line --}}
                    <div style="display: flex; align-items: center; margin: 4px -12px; position: relative;">
                        <div style="width: 10px; height: 20px; background: #f1f5f9; border-radius: 0 10px 10px 0; border: 1px solid rgba(226, 232, 240, 0.8); border-left: none; position: absolute; left: 0; z-index: 10;"></div>
                        <div style="flex-grow: 1; border-top: 2px dashed #e2e8f0; height: 1px;"></div>
                        <div style="width: 10px; height: 20px; background: #f1f5f9; border-radius: 10px 0 0 10px; border: 1px solid rgba(226, 232, 240, 0.8); border-right: none; position: absolute; right: 0; z-index: 10;"></div>
                    </div>

                    {{-- Form Submit Button --}}
                    <div class="mt-auto pt-1.5">
                        <button type="submit" id="btnSubmitForm" class="btn btn-primary w-100 py-1.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 shadow-lg" style="box-shadow: 0 4px 14px rgba(37,99,235,0.4) !important;">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>Konfirmasi & Bayar Sekarang</span>
                        </button>
                    </div>

                </div>

            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// Global state
window.occupiedJadwals = [];
window.timelineSelectionStage = 0; // 0 = select start, 1 = select end

// Micro-feedback scale pop on steppers
function triggerPop(elementId) {
    const el = document.getElementById(elementId);
    if (el) {
        el.classList.remove('pop-active');
        void el.offsetWidth; // Trigger reflow
        el.classList.add('pop-active');
    }
}

// Toggle payment selection
function setPayment(val) {
    document.getElementById('card-qris').classList.toggle('active', val === 'qris');
    document.getElementById('card-tunai').classList.toggle('active', val === 'tunai');
    hitungTotal();
}

// Check overlap helper (HH:MM vs HH:MM)
function isOverlap(start1, end1, start2, end2) {
    const toMins = (timeStr) => {
        if (!timeStr) return 0;
        const parts = timeStr.split(':');
        const h = parseInt(parts[0], 10);
        const m = parts[1] ? parseInt(parts[1], 10) : 0;
        return h * 60 + m;
    };
    return toMins(start1) < toMins(end2) && toMins(end1) > toMins(start2);
}

function updateVouchersState() {
    const tanggalInput = document.querySelector('input[name="tanggal"]');
    const jamMulaiInput = document.querySelector('select[name="jam_mulai"]');
    const jamSelesaiInput = document.querySelector('select[name="jam_selesai"]');
    const courtRadio = document.querySelector('input[name="lapangan_id"]:checked');

    const hasBookingSlot = courtRadio && courtRadio.value && tanggalInput && tanggalInput.value && jamMulaiInput && jamMulaiInput.value && jamSelesaiInput && jamSelesaiInput.value;

    let isWeekend = false;
    let mulaiHour = 0;
    let selesaiHour = 0;
    let durasi = 0;
    if (hasBookingSlot) {
        isWeekend = [0, 6].includes(new Date(tanggalInput.value).getDay());
        mulaiHour = parseInt(jamMulaiInput.value.split(':')[0]);
        selesaiHour = parseInt(jamSelesaiInput.value.split(':')[0]);
        if (jamSelesaiInput.value.endsWith('59')) {
            selesaiHour += 1;
        }
        durasi = selesaiHour - mulaiHour;
    }

    // Get facility counts
    let qtyRaket = 0;
    let qtyKok = 0;
    let qtyWater = 0;
    document.querySelectorAll('.qty-input').forEach(input => {
        const qty = parseInt(input.value) || 0;
        const nama = input.dataset.nama.toLowerCase();
        if (nama.includes('raket')) qtyRaket = qty;
        else if (nama.includes('kok') && !nama.includes('slop')) qtyKok = qty;
        else if (nama.includes('mineral') || nama.includes('water')) qtyWater = qty;
    });

    let remainingRaket = qtyRaket;
    let remainingKok = qtyKok;
    let remainingWater = qtyWater;
    let remainingHours = durasi;

    const vouchers = document.querySelectorAll('.voucher-checkbox');
    
    // First pass: Process checked vouchers to deduct resources
    vouchers.forEach(chk => {
        const cardId = chk.id.replace('chk_', '');
        const card = document.getElementById('card_' + cardId);
        const jenis = chk.dataset.jenis;

        if (chk.checked) {
            let isValid = true;

            // Check if slot required
            if (jenis === 'lapangan_offpeak' || jenis === 'membership_loyalist' || jenis === 'lapangan_peak') {
                if (!hasBookingSlot) {
                    isValid = false;
                }
            }

            // Check specific voucher types
            if (isValid) {
                if (jenis === 'lapangan_offpeak' || jenis === 'membership_loyalist') {
                    const isWeekday = !isWeekend;
                    const isOffPeak = isWeekday && (mulaiHour >= 7 && mulaiHour < 16);
                    if (!isOffPeak || remainingHours <= 0) {
                        isValid = false;
                    } else {
                        remainingHours--;
                    }
                } else if (jenis === 'lapangan_peak') {
                    if (remainingHours <= 0) {
                        isValid = false;
                    } else {
                        remainingHours--;
                    }
                } else if (jenis === 'raket' || jenis === 'membership_partner') {
                    if (remainingRaket <= 0) {
                        isValid = false;
                    } else {
                        remainingRaket--;
                    }
                } else if (jenis === 'kok_satuan') {
                    if (remainingKok <= 0) {
                        isValid = false;
                    } else {
                        remainingKok--;
                    }
                } else if (jenis === 'membership_ally' || jenis === 'anbiyaa_water') {
                    if (remainingWater <= 0) {
                        isValid = false;
                    } else {
                        remainingWater--;
                    }
                }
            }

            if (!isValid) {
                chk.checked = false;
                if (card) card.classList.remove('active');
            }
        }
    });

    // Second pass: Update UI state (enable/disable cards) based on remaining resources
    vouchers.forEach(chk => {
        const cardId = chk.id.replace('chk_', '');
        const card = document.getElementById('card_' + cardId);
        const msgEl = document.getElementById('msg_' + cardId);
        const jenis = chk.dataset.jenis;
        const expiredText = card ? card.dataset.expired : '';

        let canBeSelected = true;
        let warningMsg = '';

        if (!chk.checked) {
            if (jenis === 'lapangan_offpeak' || jenis === 'membership_loyalist' || jenis === 'lapangan_peak') {
                if (!hasBookingSlot) {
                    canBeSelected = false;
                    warningMsg = '(Pilih Jadwal Booking)';
                }
            }

            if (canBeSelected) {
                if (jenis === 'lapangan_offpeak' || jenis === 'membership_loyalist') {
                    const isWeekday = !isWeekend;
                    const isOffPeak = isWeekday && (mulaiHour >= 7 && mulaiHour < 16);
                    if (!isOffPeak) {
                        canBeSelected = false;
                        warningMsg = !isWeekday ? '(Hanya Weekdays)' : '(Hanya Jam 07:00-16:00)';
                    } else if (remainingHours <= 0) {
                        canBeSelected = false;
                        warningMsg = '(Melebihi Durasi Sewa)';
                    }
                } else if (jenis === 'lapangan_peak') {
                    if (remainingHours <= 0) {
                        canBeSelected = false;
                        warningMsg = '(Melebihi Durasi Sewa)';
                    }
                } else if (jenis === 'raket' || jenis === 'membership_partner') {
                    if (remainingRaket <= 0) {
                        canBeSelected = false;
                        warningMsg = '(Tambahkan Sewa Raket)';
                    }
                } else if (jenis === 'kok_satuan') {
                    if (remainingKok <= 0) {
                        canBeSelected = false;
                        warningMsg = '(Tambahkan Kok Satuan)';
                    }
                } else if (jenis === 'membership_ally' || jenis === 'anbiyaa_water') {
                    if (remainingWater <= 0) {
                        canBeSelected = false;
                        warningMsg = '(Tambahkan Air Mineral)';
                    }
                }
            }
        }

        if (card && msgEl) {
            if (canBeSelected) {
                card.classList.remove('disabled-voucher-card');
                card.style.opacity = '1';
                card.style.pointerEvents = 'auto';
                chk.disabled = false;
                msgEl.innerHTML = `Berlaku s.d ${expiredText}`;
            } else {
                card.classList.add('disabled-voucher-card');
                card.style.opacity = '0.5';
                card.style.pointerEvents = 'none';
                chk.disabled = true;
                msgEl.innerHTML = `Berlaku s.d ${expiredText} · <span class="text-danger fw-semibold">${warningMsg}</span>`;
            }
        }
    });
}

// Select active court from card or visualizer click
function selectCourt(id) {
    const courtElement = document.getElementById(`vis-court-${id}`);
    
    // Check if court is booked/pending at selected times
    if (courtElement) {
        if (courtElement.classList.contains('status-dipesan')) {
            Swal.fire({
                title: 'Jadwal Bentrok',
                text: 'Lapangan ini sudah dipesan pada jam yang Anda pilih. Silakan pilih lapangan lain atau ubah jam booking Anda.',
                icon: 'warning',
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Tutup'
            });
            return;
        }
        if (courtElement.classList.contains('status-pending')) {
            Swal.fire({
                title: 'Menunggu Pembayaran',
                text: 'Lapangan ini sedang dalam proses pembayaran oleh pelanggan lain. Silakan pilih lapangan lain atau ubah jam booking Anda.',
                icon: 'warning',
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Tutup'
            });
            return;
        }
    }

    // 1. Sync radio cards on left form
    document.querySelectorAll('.court-option-card').forEach(card => {
        card.classList.remove('active');
    });
    const activeCard = document.getElementById(`court-card-${id}`);
    if (activeCard) {
        activeCard.classList.add('active');
        const radioInput = activeCard.querySelector('input[type="radio"]');
        if (radioInput) radioInput.checked = true;
    }
    
    // 2. Sync hidden filter input
    const filterLap = document.getElementById('filter_lapangan_create');
    if (filterLap) {
        filterLap.value = id;
    }

    // 3. Sync visualizer courts on right panel
    document.querySelectorAll('.visual-court-item').forEach(item => {
        item.classList.remove('active');
    });
    const activeVisCourt = document.getElementById(`vis-court-${id}`);
    if (activeVisCourt) {
        activeVisCourt.classList.add('active');
        const badge = document.getElementById('visualizer-active-badge');
        const radio = document.querySelector(`input[name="lapangan_id"][value="${id}"]`);
        if (badge && radio) {
            badge.textContent = radio.dataset.nama.toUpperCase();
        }
    }

    // Filter occupied schedules tab to only show for this court
    filterOccupiedSchedulesTab();

    updateHargaBadgeCreate();
    hitungTotal();
    renderTimeline();
}

// Filter the occupied schedules list tab on client side
function filterOccupiedSchedulesTab() {
    const selectedCourtId = document.getElementById('filter_lapangan_create').value;
    const filtered = window.occupiedJadwals.filter(j => !selectedCourtId || j.lapangan_id == selectedCourtId);
    rebuildOccupiedSchedulesList(filtered);
}

// Date change handler
function onDateChange(val) {
    const filterTgl = document.getElementById('filter_tanggal_create');
    if (filterTgl) {
        filterTgl.value = val;
    }
    disablePastHours();
    fetchOccupiedSchedules();
}

// Nonaktifkan Jam Lampau
function disablePastHours() {
    const tanggalInput = document.querySelector('input[name="tanggal"]');
    if (!tanggalInput) return;

    const dateVal = tanggalInput.value;
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const todayStr = `${yyyy}-${mm}-${dd}`;
    
    const isToday = (dateVal === todayStr);
    const isPast = (dateVal < todayStr);
    const currentHour = today.getHours();

    document.querySelectorAll('select[name="jam_mulai"] option, select[name="jam_selesai"] option').forEach(opt => {
        if (!opt.value) return;
        const optHour = parseInt(opt.value.split(':')[0]);
        if (isPast || (isToday && optHour <= currentHour)) {
            opt.disabled = true;
            if (opt.selected) {
                opt.parentElement.value = '';
            }
        } else {
            opt.disabled = false;
        }
    });
}

// Update visual court active status overlays based on selected times and occupied list
function updateVisualCourtsStatus() {
    const jamMulai = document.querySelector('select[name="jam_mulai"]').value;
    const jamSelesai = document.querySelector('select[name="jam_selesai"]').value;
    
    document.querySelectorAll('.visual-court-item').forEach(item => {
        const courtId = item.dataset.id;
        const badge = document.getElementById(`vis-badge-${courtId}`);
        
        // Default available
        let status = 'available';
        
        if (jamMulai && jamSelesai && window.occupiedJadwals && window.occupiedJadwals.length > 0) {
            const overlap = window.occupiedJadwals.find(j => {
                if (j.lapangan_id != courtId) return false;
                
                // Normalisasi
                const jStart = formatTime(j.jam_mulai);
                const jEnd = formatTime(j.jam_selesai);
                return isOverlap(jStart, jEnd, jamMulai, jamSelesai);
            });
            
            if (overlap) {
                status = overlap.status; // 'pending' atau 'dipesan'/'ditutup'
            }
        }
        
        // Reset classes
        item.classList.remove('status-available', 'status-pending', 'status-dipesan');
        
        if (status === 'available') {
            item.classList.add('status-available');
            if (badge) {
                badge.textContent = 'Tersedia';
                badge.className = 'court-status-badge bg-success-subtle text-success';
            }
        } else if (status === 'pending') {
            item.classList.add('status-pending');
            if (badge) {
                badge.textContent = 'Pending';
                badge.className = 'court-status-badge bg-warning-subtle text-warning';
            }
        } else {
            item.classList.add('status-dipesan');
            if (badge) {
                badge.textContent = 'Dipesan';
                badge.className = 'court-status-badge bg-danger text-white';
            }
        }
    });
}

// Operational hours list
const operationalHours = [
    { start: "07:00", end: "08:00", hour: 7 },
    { start: "08:00", end: "09:00", hour: 8 },
    { start: "09:00", end: "10:00", hour: 9 },
    { start: "10:00", end: "11:00", hour: 10 },
    { start: "11:00", end: "12:00", hour: 11 },
    { start: "12:00", end: "13:00", hour: 12 },
    { start: "13:00", end: "14:00", hour: 13 },
    { start: "14:00", end: "15:00", hour: 14 },
    { start: "15:00", end: "16:00", hour: 15 },
    { start: "16:00", end: "17:00", hour: 16 },
    { start: "17:00", end: "18:00", hour: 17 },
    { start: "18:00", end: "19:00", hour: 18 },
    { start: "19:00", end: "20:00", hour: 19 },
    { start: "20:00", end: "21:00", hour: 20 },
    { start: "21:00", end: "22:00", hour: 21 },
    { start: "22:00", end: "23:00", hour: 22 },
    { start: "23:00", end: "23:59", hour: 23 }
];

// Helper to check if date is today
function isDateToday() {
    const tanggalInput = document.querySelector('input[name="tanggal"]');
    if (!tanggalInput) return false;
    const dateVal = tanggalInput.value;
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const todayStr = yyyy + '-' + mm + '-' + dd;
    return dateVal === todayStr;
}

// Render the timeline component
function renderTimeline() {
    const grid = document.getElementById('timelineSlotsGrid');
    if (!grid) return;

    const selectedCourtId = document.getElementById('filter_lapangan_create').value;
    const jamMulai = document.querySelector('select[name="jam_mulai"]').value;
    const jamSelesai = document.querySelector('select[name="jam_selesai"]').value;
    
    const today = new Date();
    const currentHour = today.getHours();
    
    // Handled properly for timezone offsets
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const todayStr = `${yyyy}-${mm}-${dd}`;
    const dateVal = document.querySelector('input[name="tanggal"]').value;
    const isToday = (dateVal === todayStr);
    const isPastDate = (dateVal < todayStr);

    grid.innerHTML = '';
    
    // Update selection stage badge in UI
    const stageBadge = document.getElementById('timeline-stage-badge');
    if (stageBadge) {
        if (!jamMulai && !jamSelesai) {
            stageBadge.textContent = 'Pilih Mulai';
            stageBadge.className = 'badge bg-primary text-white border-0';
        } else if (jamMulai && (!jamSelesai || jamSelesai === jamMulai || window.timelineSelectionStage === 1)) {
            stageBadge.textContent = 'Pilih Selesai';
            stageBadge.className = 'badge bg-warning text-dark border-0 animate-pulse';
        } else {
            stageBadge.textContent = 'Terpilih';
            stageBadge.className = 'badge bg-success text-white border-0';
        }
    }

    operationalHours.forEach(slot => {
        let isPast = isPastDate || (isToday && slot.hour <= currentHour);
        let isBooked = false;
        let isPending = false;

        // Check if court has overlapping booking
        if (selectedCourtId && window.occupiedJadwals && window.occupiedJadwals.length > 0) {
            const overlap = window.occupiedJadwals.find(j => {
                if (j.lapangan_id != selectedCourtId) return false;
                const jStart = formatTime(j.jam_mulai);
                const jEnd = formatTime(j.jam_selesai);
                return isOverlap(jStart, jEnd, slot.start, slot.end);
            });

            if (overlap) {
                if (overlap.status === 'pending') {
                    isPending = true;
                } else {
                    isBooked = true;
                }
            }
        }

        // Check if currently selected
        let isSelected = false;
        if (jamMulai && jamSelesai) {
            const selectStartVal = jamMulai;
            let selectEndVal = jamSelesai;
            
            // Normalize selected times to mins from midnight
            const toMins = (t) => {
                const parts = t.split(':');
                return parseInt(parts[0], 10) * 60 + (parts[1] ? parseInt(parts[1], 10) : 0);
            };
            
            const slotStartMins = toMins(slot.start);
            const slotEndMins = toMins(slot.end);
            const selStartMins = toMins(selectStartVal);
            const selEndMins = toMins(selectEndVal);

            if (slotStartMins >= selStartMins && slotEndMins <= selEndMins) {
                isSelected = true;
            }
        }

        // Create slot element
        const slotEl = document.createElement('div');
        slotEl.className = 'timeline-slot';
        
        let statusText = 'Kosong';
        if (isPast) {
            slotEl.classList.add('disabled');
            statusText = 'Terlewat';
        } else if (isBooked) {
            slotEl.classList.add('disabled', 'status-booked');
            statusText = 'Dipesan';
        } else if (isPending) {
            slotEl.classList.add('disabled', 'status-pending');
            statusText = 'Pending';
        } else if (isSelected) {
            slotEl.classList.add('status-selected');
            statusText = 'Pilihan';
        }

        slotEl.innerHTML = `
            <span class="timeline-slot-time">${slot.start}</span>
            <span class="timeline-slot-status">${statusText}</span>
        `;

        if (!isPast && !isBooked && !isPending) {
            slotEl.addEventListener('click', () => onSlotClick(slot));
        }

        grid.appendChild(slotEl);
    });
}

// Handle timeline slot clicks for easy range selection
function onSlotClick(slot) {
    const jamMulaiSelect = document.querySelector('select[name="jam_mulai"]');
    const jamSelesaiSelect = document.querySelector('select[name="jam_selesai"]');
    const selectedCourtId = document.getElementById('filter_lapangan_create').value;

    const toMins = (t) => {
        const parts = t.split(':');
        return parseInt(parts[0], 10) * 60 + (parts[1] ? parseInt(parts[1], 10) : 0);
    };

    const startVal = jamMulaiSelect.value;
    const endVal = jamSelesaiSelect.value;

    if (window.timelineSelectionStage === 0 || !startVal || (startVal && endVal && toMins(slot.start) < toMins(startVal))) {
        // Start fresh selection
        jamMulaiSelect.value = slot.start;
        jamSelesaiSelect.value = slot.end;
        window.timelineSelectionStage = 1;
    } else {
        // Extending selection
        const startMins = toMins(startVal);
        const clickedEndMins = toMins(slot.end);

        if (clickedEndMins <= startMins) {
            // Clicked before or equal to start, reset to clicked slot
            jamMulaiSelect.value = slot.start;
            jamSelesaiSelect.value = slot.end;
            window.timelineSelectionStage = 1;
        } else {
            // Check if there are booked/pending hours in between
            let blocked = false;
            if (selectedCourtId && window.occupiedJadwals && window.occupiedJadwals.length > 0) {
                blocked = window.occupiedJadwals.some(j => {
                    if (j.lapangan_id != selectedCourtId) return false;
                    const jStart = formatTime(j.jam_mulai);
                    const jEnd = formatTime(j.jam_selesai);
                    return isOverlap(jStart, jEnd, startVal, slot.end);
                });
            }

            const today = new Date();
            const currentHour = today.getHours();
            
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const todayStr = `${yyyy}-${mm}-${dd}`;
            const isToday = (document.querySelector('input[name="tanggal"]').value === todayStr);

            // Check if there are past hours in between
            for (let hourIndex = startMins / 60; hourIndex < clickedEndMins / 60; hourIndex++) {
                if (isToday && hourIndex <= currentHour) {
                    blocked = true;
                }
            }

            if (blocked) {
                // There is a booking or past hour in between, reset start to clicked slot
                jamMulaiSelect.value = slot.start;
                jamSelesaiSelect.value = slot.end;
                window.timelineSelectionStage = 1;
            } else {
                // Valid range extension
                jamSelesaiSelect.value = slot.end;
                window.timelineSelectionStage = 0; // complete
            }
        }
    }

    // Trigger select change events to update invoices and availability
    jamMulaiSelect.dispatchEvent(new Event('change'));
    jamSelesaiSelect.dispatchEvent(new Event('change'));
    
    // Re-render timeline to show selection highlights
    renderTimeline();
}

// Normalisasi time string helper
const formatTime = (timeStr) => {
    if (!timeStr) return '';
    const parts = timeStr.split(':');
    return parts.length >= 2 ? `${parts[0]}:${parts[1]}` : timeStr;
};

// Kalkulasi Estimasi Total
function hitungTotal() {
    // Selalu perbarui status voucher sebelum menghitung total
    updateVouchersState();

    const courtRadio = document.querySelector('input[name="lapangan_id"]:checked');
    const tanggal = document.querySelector('input[name="tanggal"]');
    const jamMulai = document.querySelector('select[name="jam_mulai"]');
    const jamSelesai = document.querySelector('select[name="jam_selesai"]');
    
    const container = document.getElementById('containerTotal');
    const display = document.getElementById('displayTotal');
    const detail = document.getElementById('detailHitung');
    
    const emptyState = document.getElementById('checkout-empty-state');
    const detailsWrap = document.getElementById('checkout-details');

    if (courtRadio && courtRadio.value && tanggal && tanggal.value && jamMulai && jamMulai.value && jamSelesai && jamSelesai.value) {
        if (emptyState) emptyState.classList.add('d-none');
        if (detailsWrap) detailsWrap.classList.remove('d-none');

        // Set invoice values
        document.getElementById('invoice-court-name').textContent = courtRadio.dataset.nama;
        document.getElementById('invoice-date').textContent = tanggal.value;
        document.getElementById('invoice-time-range').textContent = `${jamMulai.value} - ${jamSelesai.value}`;
        
        const paymentVal = document.querySelector('input[name="metode_pembayaran"]:checked').value;
        document.getElementById('invoice-payment').textContent = paymentVal === 'qris' ? 'QRIS' : 'Tunai';

        const isWeekend = [0,6].includes(new Date(tanggal.value).getDay());
        const hargaPerJam = isWeekend ? parseInt(courtRadio.dataset.weekend) : parseInt(courtRadio.dataset.weekday);
        
        let mulai = parseInt(jamMulai.value.split(':')[0]);
        let selesai = parseInt(jamSelesai.value.split(':')[0]);
        if (jamSelesai.value.endsWith('59')) {
            selesai += 1;
        }
        
        if (selesai > mulai) {
            const durasi = selesai - mulai;
            let total = durasi * hargaPerJam;
            let totalMurniLapangan = durasi * hargaPerJam;
            let parts = [`${durasi} Jam × Rp ${hargaPerJam.toLocaleString('id-ID')}`];

            // Hitung Fasilitas
            let qtyRaket = 0;
            let qtyKok = 0;
            let qtyWater = 0;
            let raketHarga = 0;
            let kokHarga = 0;
            let waterHarga = 0;
            let pointFasilitas = 0;
            let facilitiesHtml = '';

            const inputs = document.querySelectorAll('.qty-input');
            inputs.forEach(input => {
                const qty = parseInt(input.value) || 0;
                if(qty > 0) {
                    const harga = parseInt(input.dataset.harga);
                    const nama = input.dataset.nama;
                    const ptPerUnit = parseInt(input.dataset.poin) || 0;

                    total += (qty * harga);
                    parts.push(`${nama}×${qty}`);
                    pointFasilitas += (qty * ptPerUnit);

                    facilitiesHtml += `
                        <div class="invoice-item text-secondary" style="font-size: 0.78rem; border-bottom: none; padding: 4px 0;">
                            <span>${nama} × ${qty}</span>
                            <span>Rp ${(qty * harga).toLocaleString('id-ID')}</span>
                        </div>
                    `;

                    if (nama.toLowerCase().includes('raket')) {
                        qtyRaket = qty;
                        raketHarga = harga;
                    }
                    if (nama.toLowerCase().includes('kok') && !nama.toLowerCase().includes('slop')) {
                        qtyKok = qty;
                        kokHarga = harga;
                    }
                    if (nama.toLowerCase().includes('mineral') || nama.toLowerCase().includes('water')) {
                        qtyWater = qty;
                        waterHarga = harga;
                    }
                }
            });
            
            document.getElementById('invoice-facilities-list').innerHTML = facilitiesHtml;

            // ─── LOYALTY VOUCHER LOGIC (MULTI-VOUCHER) ───
            let totalDiscount = 0;
            let discountsHtml = '';
            
            const checkedVouchers = document.querySelectorAll('.voucher-checkbox:checked');
            
            let appliedRaketVouchers = 0;
            let appliedKokVouchers = 0;
            let appliedOffPeakVouchers = 0;
            let appliedPeakVouchers = 0;
            let appliedWaterVouchers = 0;
            
            checkedVouchers.forEach(chk => {
                const jenis = chk.dataset.jenis;
                const label = chk.dataset.label;
                
                let discAmount = 0;

                if (jenis === 'voucher_50k') {
                    discAmount = 50000;
                } else if (jenis === 'voucher_member') {
                    discAmount = 100000;
                } else if (jenis === 'membership_vip') {
                    discAmount = 100000;
                } else if (jenis === 'lapangan_offpeak' || jenis === 'membership_loyalist') {
                    discAmount = hargaPerJam;
                    appliedOffPeakVouchers++;
                } else if (jenis === 'lapangan_peak') {
                    discAmount = hargaPerJam;
                    appliedPeakVouchers++;
                } else if (jenis === 'raket' || jenis === 'membership_partner') {
                    discAmount = raketHarga;
                    appliedRaketVouchers++;
                } else if (jenis === 'kok_satuan') {
                    discAmount = kokHarga;
                    appliedKokVouchers++;
                } else if (jenis === 'membership_ally' || jenis === 'anbiyaa_water') {
                    discAmount = waterHarga;
                    appliedWaterVouchers++;
                }

                totalDiscount += discAmount;
                discountsHtml += `
                    <div class="invoice-item text-success fw-bold" style="font-size: 0.82rem; padding: 6px 0; border-bottom: 1px solid #f1f5f9;">
                        <span>${label}</span>
                        <span>-Rp ${discAmount.toLocaleString('id-ID')}</span>
                    </div>
                `;
            });

            document.getElementById('invoice-discounts-list').innerHTML = discountsHtml;

            let finalTotal = Math.max(0, total - totalDiscount);

            container.classList.remove('d-none');
            display.innerText = 'Rp ' + finalTotal.toLocaleString('id-ID');
            detail.innerHTML = `<span style="opacity:.8">${parts.join(' + ')}</span>`;

            // ─── HITUNG ESTIMASI POIN YANG DIDAPAT ───
            const isWeekday = !isWeekend;
            const isOffPeak = isWeekday && (mulai >= 7 && mulai < 16);
            const multiplier = isOffPeak ? 2 : 1;

            const pointSewa = Math.floor(totalMurniLapangan / 5000) * multiplier;
            const totalPointsEarned = pointSewa + pointFasilitas;

            const displayPoin = document.getElementById('displayPoinEstimasi');
            if (displayPoin) {
                let note = isOffPeak ? ' (Jam Off-Peak Double Points!)' : '';
                displayPoin.innerHTML = `+${totalPointsEarned.toLocaleString('id-ID')} Poin${note}`;
            }
            return;
        }
    }
    
    // Reset checkout preview
    if (emptyState) emptyState.classList.remove('d-none');
    if (detailsWrap) detailsWrap.classList.add('d-none');
    if (container) container.classList.add('d-none');
    const discList = document.getElementById('invoice-discounts-list');
    if (discList) discList.innerHTML = '';
}

// Badge harga aktif berdasarkan tanggal (create)
function updateHargaBadgeCreate() {
    const activeRadio = document.querySelector('input[name="lapangan_id"]:checked');
    const tanggalInput = document.querySelector('input[name="tanggal"]');
    const badgeWrap = document.getElementById('hargaBadgeCreate');
    const badgeLabel = document.getElementById('hargaBadgeLabelCreate');
    const badgeSub = document.getElementById('hargaBadgeSubCreate');

    if (!activeRadio || !tanggalInput || !tanggalInput.value) {
        if (badgeWrap) badgeWrap.classList.add('d-none');
        return;
    }

    const hargaWeekday = parseInt(activeRadio.dataset.weekday);
    const hargaWeekend = parseInt(activeRadio.dataset.weekend);
    const date = new Date(tanggalInput.value);
    const day = date.getDay();
    const isWeekend = (day === 0 || day === 6);

    const harga = isWeekend ? hargaWeekend : hargaWeekday;
    const tipe = isWeekend ? 'Weekend' : 'Weekday';
    const warna = isWeekend ? '#7c3aed' : '#0ea5e9';
    const bgWarna = isWeekend ? '#ede9fe' : '#e0f2fe';

    badgeLabel.textContent = 'Rp ' + harga.toLocaleString('id-ID') + ' / jam';
    badgeLabel.style.background = bgWarna;
    badgeLabel.style.color = warna;
    badgeSub.textContent = '(' + tipe + ' · ' + activeRadio.dataset.nama + ')';
    badgeWrap.classList.remove('d-none');
}

// AJAX Ketersediaan Fasilitas
function updateFasilitasAvailability() {
    const tanggal = document.querySelector('input[name="tanggal"]').value;
    const jamMulai = document.querySelector('select[name="jam_mulai"]').value;
    const jamSelesai = document.querySelector('select[name="jam_selesai"]').value;

    if (!tanggal || !jamMulai || !jamSelesai) {
        return;
    }

    const url = `{{ route('booking.cek-fasilitas') }}?tanggal=${tanggal}&jam_mulai=${jamMulai}&jam_selesai=${jamSelesai}`;

    fetch(url)
        .then(response => response.json())
        .then(res => {
            if (res.success) {
                Object.values(res.data).forEach(f => {
                    const badge = document.getElementById(`badge-stok-${f.id}`);
                    const btnMinus = document.getElementById(`btn-minus-${f.id}`);
                    const btnPlus = document.getElementById(`btn-plus-${f.id}`);
                    const input = document.getElementById(`qty-input-${f.id}`);
                    const info = document.getElementById(`info-stok-${f.id}`);

                    if (badge) {
                        if (f.sisa_stok > 0) {
                            badge.innerHTML = `<span class="badge bg-light text-primary ms-1" style="font-size:0.6rem; border:1px solid #bfdbfe;">Sisa: ${f.sisa_stok}</span>`;
                        } else {
                            badge.innerHTML = `<span class="badge bg-light text-danger ms-1" style="font-size:0.6rem; border:1px solid #fecaca;">Habis</span>`;
                        }
                    }

                    if (input) {
                        input.dataset.max = f.sisa_stok;
                        if (parseInt(input.value) > f.sisa_stok) {
                            input.value = f.sisa_stok;
                        }
                    }

                    if (btnMinus && btnPlus) {
                        btnMinus.disabled = (parseInt(input.value) === 0);
                        btnPlus.disabled = false;
                    }

                    if (info) {
                        if (f.tersedia_pada) {
                            info.innerHTML = `<i class="bi bi-info-circle me-1 text-warning"></i>Akan tersedia pada jam ${f.tersedia_pada}`;
                            info.classList.remove('d-none');
                        } else {
                            info.innerHTML = '';
                            info.classList.add('d-none');
                        }
                    }
                });

                hitungTotal();
            }
        })
        .catch(err => console.error("Gagal mengambil ketersediaan fasilitas:", err));
}

// Fetch occupied schedules via AJAX
let pollIntervalId = null;

function fetchOccupiedSchedules(isSilent = false) {
    const tgl = document.getElementById('filter_tanggal_create').value;

    if (!tgl) return;

    // Panggil booking.index via AJAX untuk memuat seluruh jadwal terisi pada tanggal tersebut
    const url = `{{ route('booking.index') }}?tanggal=${tgl}`;

    fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(res => {
        if (res.success) {
            window.occupiedJadwals = res.jadwals;
            
            // Saring dan bangun list jadwal terisi di kolom kanan
            filterOccupiedSchedulesTab();

            const header = document.getElementById('occupiedSchedulesHeader');
            if (header) {
                header.innerHTML = `<i class="bi bi-info-circle me-1"></i>Jadwal Terisi pada ${res.formatted_tanggal}`;
            }

            // Update status visual lapangan bulutangkis
            updateVisualCourtsStatus();

            // Render/update timeline jam
            renderTimeline();
        }
    })
    .catch(err => console.error("Gagal mengambil jadwal terisi:", err));
}

function rebuildOccupiedSchedulesList(jadwals) {
    const container = document.getElementById('occupiedSchedulesList');
    if (!container) return;

    if (jadwals.length > 0) {
        let html = '<div class="row g-2">';
        jadwals.forEach(j => {
            const isPending = j.status === 'pending';
            const borderWarna = isPending ? '#fde68a' : '#fecaca';
            const textWarna = isPending ? 'text-warning' : 'text-danger';
            const labelText = isPending ? 'Pending' : 'Dipesan';
            const badgeHtml = isPending 
                ? `<span class="badge" style="background:#fef3c7;color:#92400e; font-size: 0.65rem;">${labelText}</span>`
                : `<span class="badge bg-danger" style="font-size: 0.65rem;">${labelText}</span>`;

            const jamMulai = formatTime(j.jam_mulai);
            const jamSelesai = formatTime(j.jam_selesai);

            html += `
                <div class="col-12">
                    <div class="stat-card p-3 shadow-none border animate__animated animate__fadeIn" style="background-color: #f8fafc; border-color: ${borderWarna} !important;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="fw-bold mb-1" style="font-size: 0.82rem; color:#1e293b;">${j.lapangan ? j.lapangan.nama_lapangan : 'Lapangan'}</h6>
                                <div class="fw-bold ${textWarna}" style="font-size:0.85rem">
                                    <i class="bi bi-clock me-1"></i> ${jamMulai} - ${jamSelesai}
                                </div>
                            </div>
                            ${badgeHtml}
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    } else {
        container.innerHTML = `
            <div class="p-4 text-center text-muted animate__animated animate__fadeIn" style="border: 2px dashed #e2e8f0; border-radius: 12px; margin-top: 10px;">
                <i class="bi bi-calendar2-check fs-3 text-success d-block mb-2"></i>
                <h6 class="text-success fw-bold" style="font-size: 0.85rem;">Jadwal Kosong</h6>
                <p class="text-muted small mb-0" style="font-size: 0.72rem;">Belum ada lapangan yang dibooking pada tanggal ini.</p>
            </div>
        `;
    }
}

function startPollingCreate() {
    if (pollIntervalId) {
        clearInterval(pollIntervalId);
    }
    pollIntervalId = setInterval(() => {
        fetchOccupiedSchedules(true);
    }, 10000);
}

// Plus minus button controls with visual bounce pop animation
document.querySelectorAll('.btn-plus').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.closest('.qty-stepper').querySelector('.qty-input');
        const max = parseInt(input.dataset.max) || 0;
        const currentVal = parseInt(input.value) || 0;
        
        if (currentVal < max) {
            input.value = currentVal + 1;
            triggerPop(`qty-val-${input.dataset.id}`);
            hitungTotal();
            const btnMinus = this.closest('.qty-stepper').querySelector('.btn-minus');
            if (btnMinus) btnMinus.disabled = false;
        } else {
            const nama = input.dataset.nama;
            const infoEl = document.getElementById(`info-stok-${input.dataset.id}`);
            let infoMsg = '';
            if (infoEl && !infoEl.classList.contains('d-none')) {
                infoMsg = `<br><span class="text-warning fw-bold">${infoEl.innerHTML}</span>`;
            }
            
            Swal.fire({
                title: `Stok ${nama} Terbatas`,
                html: `Maaf, jumlah ${nama} yang tersedia saat ini adalah ${max} unit untuk slot waktu yang Anda pilih.${infoMsg}`,
                icon: 'warning',
                confirmButtonColor: '#2563eb',
                confirmButtonText: 'Baik, Saya Mengerti',
                customClass: {
                    popup: 'rounded-4'
                }
            });
        }
    });
});

document.querySelectorAll('.btn-minus').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.closest('.qty-stepper').querySelector('.qty-input');
        const currentVal = parseInt(input.value) || 0;
        if (currentVal > 0) {
            input.value = currentVal - 1;
            triggerPop(`qty-val-${input.dataset.id}`);
            hitungTotal();
            if (parseInt(input.value) === 0) {
                this.disabled = true;
            }
        }
    });
});

// Event Listeners for inputs
document.querySelectorAll('select, input[type="date"], select[name="jam_mulai"], select[name="jam_selesai"]').forEach(el => {
    if (el) {
        el.addEventListener('change', () => {
            hitungTotal();
            updateVisualCourtsStatus();
            renderTimeline();
        });
    }
});

document.querySelectorAll('select[name="jam_mulai"], select[name="jam_selesai"]').forEach(el => {
    el.addEventListener('change', updateFasilitasAvailability);
});

function toggleVoucherItem(id, fromCheckbox = false) {
    const card = document.getElementById('card_' + id);
    const chk = document.getElementById('chk_' + id);
    if (!card || !chk) return;
    if (chk.disabled) return;
    
    if (!fromCheckbox) {
        chk.checked = !chk.checked;
    }
    card.classList.toggle('active', chk.checked);
    hitungTotal();
}

// Initialization
window.addEventListener('DOMContentLoaded', function() {
    // Add active class to checked voucher cards on page load
    document.querySelectorAll('.voucher-checkbox').forEach(chk => {
        if (chk.checked) {
            const card = document.getElementById(chk.id.replace('chk_', 'card_'));
            if (card) card.classList.add('active');
        }
    });
    disablePastHours();
    updateHargaBadgeCreate();
    updateFasilitasAvailability();
    hitungTotal();
    startPollingCreate();
    
    // Auto sync from checkbox selections on load
    const checkedRadio = document.querySelector('input[name="lapangan_id"]:checked');
    if (checkedRadio) {
        selectCourt(checkedRadio.value);
    } else {
        // Initial fetch to load the grid
        fetchOccupiedSchedules();
    }

    // ── Handle Toggle collapseVoucherList on Booking Page ──
    const collapseVoucherList = document.getElementById('collapseVoucherList');
    if (collapseVoucherList) {
        collapseVoucherList.addEventListener('show.bs.collapse', function () {
            const btn = document.getElementById('btnToggleVouchers');
            if (btn) {
                btn.innerHTML = '<span class="btn-text">Tutup Voucher</span> <i class="bi bi-chevron-up ms-1"></i>';
                btn.classList.replace('btn-outline-primary', 'btn-primary');
            }
        });
        collapseVoucherList.addEventListener('hide.bs.collapse', function () {
            const btn = document.getElementById('btnToggleVouchers');
            if (btn) {
                btn.innerHTML = '<span class="btn-text">Lihat Semua Voucher</span> <i class="bi bi-chevron-down ms-1"></i>';
                btn.classList.replace('btn-primary', 'btn-outline-primary');
            }
        });
    }
});

// Form submit confirmation
document.getElementById('formBookingCreate').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('btnSubmitForm');
    if (btn.disabled) {
        return; // Prevent multiple submissions
    }

    Swal.fire({
        title: 'Konfirmasi Booking',
        text: 'Apakah Anda yakin jadwal, lapangan, dan jam sudah sesuai?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#2563eb',
        cancelButtonColor: '#dc2626',
        confirmButtonText: 'Ya, Lanjutkan!',
        cancelButtonText: 'Cek Lagi',
        background: '#fff',
        customClass: {
            popup: 'rounded-4'
        },
        showLoaderOnConfirm: true,
        preConfirm: () => {
            btn.disabled = true;
            btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <span>Memproses...</span>`;
            return true;
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            this.submit();
        } else {
            btn.disabled = false;
            btn.innerHTML = `<i class="bi bi-check-circle-fill"></i> <span>Konfirmasi & Bayar Sekarang</span>`;
        }
    });
});

// Prevent leave-page warning on logout
document.querySelectorAll('.btn-logout, .btn-logout-icon').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var bookingForm = document.getElementById('formBookingCreate');
        if (bookingForm) { bookingForm.reset(); }
        window.onbeforeunload = null;
    });
});
</script>
@endpush