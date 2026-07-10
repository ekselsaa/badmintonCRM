{{-- Sidebar Pelanggan --}}
<div class="sidebar {{ ($sidebarState ?? 'closed') === 'closed' ? 'collapsed' : '' }}">
    {{-- Brand --}}
    <div class="sidebar-brand">
        <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="#0ea5e9" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" class="brand-icon" style="width: 1.6rem; height: 1.6rem; display: inline-block; vertical-align: middle; filter: drop-shadow(0 0 8px rgba(14, 165, 233, 0.6));">
                <!-- Cork / Base -->
                <path d="M9 16c0 1.66 1.34 3 3 3s3-1.34 3-3" fill="#0ea5e9" stroke="#0ea5e9" stroke-width="2.8"/>
                <!-- Collar Band -->
                <path d="M8 14.5h8" stroke="#2563eb" stroke-width="3"/>
                <!-- Feathers -->
                <path d="M7.5 13.5L5 5" stroke-width="2.8"/>
                <path d="M12 13.5V4" stroke-width="2.8"/>
                <path d="M16.5 13.5L19 5" stroke-width="2.8"/>
                <!-- Connecting Thread -->
                <path d="M6 9.5h12" stroke-width="1.8" opacity="0.75"/>
            </svg>
            <div class="brand-text">
                <h5>Anbiyaa Sport</h5>
                <small>Booking Lapangan</small>
            </div>
        </a>
        <button onclick="toggleSidebar()" class="btn-toggle-sidebar">
            <i class="bi bi-chevron-left" style="font-size:.9rem"></i>
        </button>
    </div>

    {{-- Navigation --}}
    <nav class="flex-grow-1 py-2">
        <div class="sidebar-section">Menu</div>
        <a href="{{ route('booking.index') }}" data-title="Booking Lapangan"
           class="nav-link {{ request()->routeIs('booking.index') || request()->routeIs('booking.create') ? 'active' : '' }}">
            <i class="bi bi-calendar-check"></i><span class="link-text">Booking Lapangan</span>
        </a>
        <a href="{{ route('booking.riwayat') }}" data-title="Riwayat Booking"
           class="nav-link {{ request()->routeIs('booking.riwayat') || request()->routeIs('booking.show') ? 'active' : '' }}">
            <i class="bi bi-clock-history"></i><span class="link-text">Riwayat Booking</span>
        </a>
        <a href="{{ route('profil.show') }}" data-title="Profil Saya"
           class="nav-link {{ request()->routeIs('profil.*') ? 'active' : '' }}">
            <i class="bi bi-person-circle"></i><span class="link-text">Profil Saya</span>
        </a>

        <div class="sidebar-section">Lainnya</div>
        <a href="{{ route('membership.index') }}" data-title="Membership"
           class="nav-link {{ request()->routeIs('membership.index') ? 'active' : '' }}">
            <i class="bi bi-star"></i><span class="link-text">Membership</span>
        </a>
    </nav>

    {{-- User --}}
    <div class="sidebar-user shadow-sm">
        <div class="user-info sidebar-user-text">
            <div class="user-avatar">
                <i class="bi bi-person-fill" style="color:#fff;font-size:.8rem"></i>
            </div>
            <div style="min-width:0">
                <div class="text-truncate" style="color:#e2e8f0;font-size:.78rem;font-weight:600">{{ auth()->user()->name }}</div>
                <div style="color:#64748b;font-size:.65rem">Pelanggan</div>
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
