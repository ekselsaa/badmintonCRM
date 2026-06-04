{{-- Sidebar Admin --}}
<div class="sidebar">
    {{-- Brand --}}
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="text-decoration-none">
            <i class="bi bi-shield-fill-check brand-icon"></i>
            <div class="brand-text">
                <h5 class="fw-bold mb-0">Admin Panel</h5>
                <small class="text-muted">Badminton CRM</small>
            </div>
        </a>
        <button onclick="toggleSidebar()" class="btn-toggle-sidebar" aria-label="Toggle Sidebar">
            <i class="bi bi-chevron-left" style="font-size:.9rem"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-grow-1 py-2">
        <a href="{{ route('admin.dashboard') }}" data-title="Dashboard"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i><span class="link-text">Dashboard</span>
        </a>

        <a href="{{ route('admin.lapangan.index') }}" data-title="Data Lapangan"
           class="nav-link {{ request()->routeIs('admin.lapangan.*') ? 'active' : '' }}">
            <i class="bi bi-grid-3x3-gap-fill"></i><span class="link-text">Data Lapangan</span>
        </a>
        <a href="{{ route('admin.fasilitas.index') }}" data-title="Fasilitas Tambahan"
           class="nav-link {{ request()->routeIs('admin.fasilitas.*') ? 'active' : '' }}">
            <i class="bi bi-box-seam-fill"></i><span class="link-text">Fasilitas Tambahan</span>
        </a>
        <a href="{{ route('admin.jadwal.index') }}" data-title="Kelola Jadwal"
           class="nav-link {{ request()->routeIs('admin.jadwal.*') ? 'active' : '' }}">
            <i class="bi bi-calendar3"></i><span class="link-text">Kelola Jadwal</span>
        </a>

        <a href="{{ route('admin.booking.index') }}" data-title="Data Transaksi"
           class="nav-link {{ request()->routeIs('admin.booking.*') || request()->routeIs('admin.pembayaran.verifikasi') || request()->routeIs('admin.pembayaran-membership.*') ? 'active' : '' }}">
            <i class="bi bi-receipt-cutoff"></i><span class="link-text">Data Transaksi</span>
            @if((($sidebarPendingBooking ?? 0) + ($sidebarPendingMembership ?? 0)) > 0)
                <span class="sidebar-notif-badge">{{ ($sidebarPendingBooking ?? 0) + ($sidebarPendingMembership ?? 0) }}</span>
            @endif
        </a>

        <a href="{{ route('admin.crm.pelanggan') }}" data-title="Pelanggan & Ulasan"
           class="nav-link {{ request()->routeIs('admin.crm.*') || request()->routeIs('admin.ulasan.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i><span class="link-text">Pelanggan & Ulasan</span>
        </a>
        <a href="{{ route('admin.laporan.index') }}" data-title="Laporan"
           class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
            <i class="bi bi-bar-chart-line"></i><span class="link-text">Laporan</span>
        </a>
    </nav>

    {{-- User --}}
    <div class="sidebar-user shadow-sm">
        <div class="user-info sidebar-user-text">
            <div class="user-avatar">
                <i class="bi bi-shield-fill" style="color:#fff;font-size:.8rem"></i>
            </div>
            <div style="min-width:0">
                <div class="text-truncate" style="color:#e2e8f0;font-size:.78rem;font-weight:600">{{ auth()->user()->name }}</div>
                <div style="color:#64748b;font-size:.65rem">Administrator</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="sidebar-logout-form">
            @csrf
            <button type="submit" class="btn-logout">
                <i class="bi bi-box-arrow-left me-1"></i><span class="link-text">Logout</span>
            </button>
        </form>
        {{-- Collapsed: ikon logout saja --}}
        <form method="POST" action="{{ route('logout') }}" class="sidebar-logout-icon">
            @csrf
            <button class="btn-logout-icon" title="Logout" type="submit">
                <i class="bi bi-box-arrow-left"></i>
            </button>
        </form>
    </div>
</div>
