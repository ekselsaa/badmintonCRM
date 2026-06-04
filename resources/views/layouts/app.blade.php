<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Badminton CRM') - Anbiyaa Sport</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 24 24%22 fill=%22none%22 stroke=%22%230ea5e9%22 stroke-width=%222.8%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22><path d=%22M9 16c0 1.66 1.34 3 3 3s3-1.34 3-3%22 fill=%22%230ea5e9%22 stroke=%22%230ea5e9%22 stroke-width=%222.8%22/><path d=%22M8 14.5h8%22 stroke=%22%232563eb%22 stroke-width=%223%22/><path d=%22M7.5 13.5L5 5%22 stroke-width=%222.8%22/><path d=%22M12 13.5V4%22 stroke-width=%222.8%22/><path d=%22M16.5 13.5L19 5%22 stroke-width=%222.8%22/><path d=%22M6 9.5h12%22 stroke-width=%221.8%22 opacity=%220.75%22/></svg>">

    {{-- Bootstrap 5 CDN --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* ── Premium Modern Theme Variables ── */
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #eff6ff;
            --accent: #38bdf8;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --sidebar-bg: linear-gradient(185deg, #090d16 0%, #111827 100%);
            --sidebar-text: #94a3b8;
            --sidebar-active: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            --sidebar-hover: rgba(255, 255, 255, 0.05);
            --card-shadow: 0 10px 40px -10px rgba(0,0,0,0.04);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: 1px solid rgba(241, 245, 249, 0.9);
        }

        body { 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            background: #f1f5f9;
            background-image: radial-gradient(at 40% 20%, #e0e7ff 0px, transparent 50%),
                              radial-gradient(at 80% 0%, #dbeafe 0px, transparent 50%),
                              radial-gradient(at 0% 50%, #f1f5f9 0px, transparent 50%);
            background-attachment: fixed;
            color: #1e293b;
        }

        /* ── Sidebar Premium ── */
        .sidebar {
            width: 260px; height: 100vh;
            background: var(--sidebar-bg);
            position: fixed; top: 0; left: 0; z-index: 1000;
            transition: width 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            border-right: 1px solid rgba(255,255,255,0.03);
            overflow-x: hidden;
            display: flex; flex-direction: column;
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
        }
        .sidebar nav {
            overflow-y: auto;
            overflow-x: hidden;
        }
        .sidebar nav::-webkit-scrollbar { width: 4px; }
        .sidebar nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }

        /* Brand */
        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            display: flex; align-items: center; justify-content: space-between;
            min-height: 72px; flex-shrink: 0;
            background: rgba(0,0,0,0.2);
        }
        .sidebar-brand a { display: flex; align-items: center; gap: 0.8rem; text-decoration: none; }
        .sidebar-brand .brand-icon { 
            font-size: 1.6rem; color: #0ea5e9; flex-shrink: 0; line-height: 1;
            filter: drop-shadow(0 0 8px rgba(14, 165, 233, 0.6));
        }
        .sidebar-brand h5 { 
            color: #fff; font-weight: 800; margin: 0; font-size: 1.1rem; 
            letter-spacing: -0.5px; line-height: 1.2;
        }
        .sidebar-brand small { 
            color: #64748b; font-size: 0.65rem; text-transform: uppercase; 
            letter-spacing: 1.5px; display: block; margin-top: 2px; font-weight: 600;
        }
        .sidebar-brand .brand-text { transition: opacity 0.3s, width 0.3s; white-space: nowrap; overflow: hidden; }
        .sidebar-brand .btn-toggle-sidebar {
            color: #64748b; background: none; border: none; padding: 6px;
            border-radius: 8px; cursor: pointer; transition: all 0.2s; flex-shrink: 0; line-height: 1;
        }
        .sidebar-brand .btn-toggle-sidebar:hover { color: #f8fafc; background: rgba(255,255,255,0.1); }

        /* Nav Links */
        .sidebar .nav-link {
            color: var(--sidebar-text); 
            padding: 0.85rem 1.1rem;
            border-radius: 10px; margin: 4px 12px;
            font-size: 0.875rem; font-weight: 500;
            display: flex; align-items: center; gap: 0.75rem;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            white-space: nowrap; overflow: hidden;
            position: relative;
        }
        .sidebar .nav-link:hover { 
            background: var(--sidebar-hover); color: #f8fafc; 
            transform: translateX(4px);
        }
        .sidebar .nav-link.active {
            background: var(--sidebar-active); color: #ffffff;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.35);
            font-weight: 600;
        }
        .sidebar .nav-link.active::before {
            content: '';
            position: absolute; left: 0; top: 25%; height: 50%; width: 3px;
            background: #38bdf8; border-radius: 0 4px 4px 0;
            box-shadow: 0 0 8px rgba(56, 189, 248, 0.8);
        }
        .sidebar .nav-link i { 
            font-size: 1.15rem; min-width: 24px; text-align: center; 
            flex-shrink: 0; line-height: 1; transition: transform 0.2s;
        }
        .sidebar .nav-link:hover i { transform: scale(1.1); }
        .sidebar .nav-link .link-text { transition: opacity 0.2s; }

        /* Sidebar Notification Badge */
        .sidebar-notif-badge {
            background-color: var(--danger);
            color: white;
            font-size: 0.7rem;
            font-weight: 700;
            padding: 0.15rem 0.4rem;
            border-radius: 50px;
            margin-left: auto;
            line-height: 1;
            box-shadow: 0 2px 6px rgba(239, 68, 68, 0.4);
            transition: all 0.2s ease;
        }
        .sidebar.collapsed .sidebar-notif-badge {
            position: absolute;
            top: 4px;
            right: 4px;
            margin-left: 0;
            min-width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            font-size: 0.58rem;
            box-shadow: 0 0 0 2px #0f172a;
        }

        /* Sidebar Submenu */
        .sidebar-submenu .nav-link {
            padding: 0.55rem 0.85rem !important;
            margin: 2px 16px !important;
            background: rgba(255, 255, 255, 0.02) !important;
            font-size: 0.8rem !important;
            border-left: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: none !important;
        }
        .sidebar-submenu .nav-link:hover {
            background: rgba(255, 255, 255, 0.05) !important;
            color: #fff !important;
        }
        .sidebar-submenu .nav-link.active {
            background: rgba(255, 255, 255, 0.08) !important;
            border-left: 2px solid var(--accent) !important;
            font-weight: 600;
        }
        .sidebar.collapsed .sidebar-submenu {
            display: none !important;
        }

        /* Section Labels */
        .sidebar-section { 
            padding: 1.75rem 1.5rem 0.65rem; font-size: 0.65rem;
            text-transform: uppercase; letter-spacing: 2px; 
            color: #475569; font-weight: 800;
            white-space: nowrap; overflow: hidden;
            transition: opacity 0.2s;
            display: flex; align-items: center;
        }
        .sidebar-section::after {
            content: '';
            height: 1px; flex-grow: 1;
            background: rgba(255, 255, 255, 0.05);
            margin-left: 0.5rem;
        }

        /* User Card */
        .sidebar .sidebar-user { 
            padding: 0.85rem; flex-shrink: 0;
            border-top: 1px solid rgba(255,255,255,0.04);
            margin-top: auto;
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 14px;
            margin: 0.75rem;
            backdrop-filter: blur(10px);
        }
        .sidebar .sidebar-user .user-info {
            display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;
        }
        .sidebar .sidebar-user .user-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; background: linear-gradient(135deg, #38bdf8 0%, #2563eb 100%);
            color: white; font-weight: 700; box-shadow: 0 2px 10px rgba(37,99,235,0.3);
            position: relative;
        }
        .sidebar .sidebar-user .user-avatar::after {
            content: '';
            position: absolute; bottom: 0; right: 0;
            width: 9px; height: 9px;
            background: #10b981; border: 2px solid #0f172a;
            border-radius: 50%;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.4);
            animation: pulse-online 2s infinite;
        }
        @keyframes pulse-online {
            0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
            70% { box-shadow: 0 0 0 6px rgba(16, 185, 129, 0); }
            100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
        }
        .sidebar .sidebar-user-text { transition: opacity 0.2s; white-space: nowrap; overflow: hidden; }
        .sidebar .sidebar-user .sidebar-user-text div:first-child { color: white; font-weight: 600; font-size: 0.85rem; }
        .sidebar .sidebar-user .sidebar-user-text div:last-child { color: #64748b; font-size: 0.72rem; }
        
        .sidebar .sidebar-user .btn-logout {
            color: #cbd5e1; border: 1px solid rgba(255,255,255,0.06); 
            font-size: 0.78rem; border-radius: 8px; padding: 0.45rem;
            background: rgba(255,255,255,0.03); transition: all 0.2s; width: 100%;
            font-weight: 600;
        }
        .sidebar .sidebar-user .btn-logout:hover { 
            color: #f87171; border-color: rgba(239, 68, 68, 0.3); 
            background: rgba(239, 68, 68, 0.1);
        }
        .sidebar .sidebar-logout-icon { display: none; margin-top: 0.5rem; }
        .sidebar .btn-logout-icon {
            color: #cbd5e1; border: none; background: rgba(255,255,255,0.05);
            font-size: 1.2rem; padding: 0.6rem; border-radius: 10px;
            width: 100%; display: flex; align-items: center; justify-content: center;
            transition: all 0.2s; cursor: pointer;
        }
        .sidebar .btn-logout-icon:hover { color: #f87171; background: rgba(239, 68, 68, 0.15); transform: scale(1.05); }

        /* ── Collapsed ── */
        .sidebar.collapsed { width: 72px; }
        .sidebar.collapsed .brand-text,
        .sidebar.collapsed .link-text,
        .sidebar.collapsed .sidebar-section,
        .sidebar.collapsed .sidebar-user-text,
        .sidebar.collapsed .sidebar-logout-form,
        .sidebar.collapsed .sidebar-brand small,
        .sidebar.collapsed .btn-toggle-sidebar { 
            opacity: 0; width: 0; height: 0; overflow: hidden; padding: 0; margin: 0;
            pointer-events: none; position: absolute;
        }
        .sidebar.collapsed .sidebar-logout-icon { display: block; }
        .sidebar.collapsed .sidebar-brand { justify-content: center; padding: 1.2rem 0.5rem; }
        .sidebar.collapsed .sidebar-brand a { justify-content: center; }
        .sidebar.collapsed .nav-link { 
            justify-content: center; padding: 0.75rem; margin: 4px 10px; 
            border-radius: 12px; min-height: 44px;
        }
        .sidebar.collapsed .nav-link::before { display: none; }
        .sidebar.collapsed .nav-link:hover { transform: translateY(-2px); }
        .sidebar.collapsed .nav-link i { margin: 0; font-size: 1.25rem; }
        .sidebar.collapsed .sidebar-user { padding: 0.5rem; margin: 0.5rem; border-radius: 12px; }
        .sidebar.collapsed .sidebar-user .user-info { justify-content: center; margin-bottom: 0; }

        .sidebar.collapsed .nav-link { position: relative; }
        .sidebar.collapsed .nav-link:hover::after {
            content: attr(data-title);
            position: absolute; left: 70px; top: 50%; transform: translateY(-50%);
            background: #0f172a; color: #fff; padding: 0.5rem 1rem;
            border-radius: 8px; font-size: 0.8rem; font-weight: 600; white-space: nowrap;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3); z-index: 1100;
            border: 1px solid rgba(255,255,255,0.1);
        }

        /* ── Main Content & Glassmorphism Topbar ── */
        .main-content { margin-left: 260px; padding: 0; min-height: 100vh; transition: margin-left 0.4s cubic-bezier(0.16, 1, 0.3, 1), width 0.4s cubic-bezier(0.16, 1, 0.3, 1); width: calc(100% - 260px); }
        .topbar {
            background: var(--glass-bg); 
            backdrop-filter: blur(24px); -webkit-backdrop-filter: blur(24px);
            padding: 0.85rem 2rem;
            border-bottom: var(--glass-border);
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 50;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.02);
        }
        .content-area { padding: 2rem; }

        /* ── Topbar Elements (Search, Toggle, Dropdown) ── */
        .topbar-toggle-btn {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: center;
            color: #475569; cursor: pointer; transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        .topbar-toggle-btn:hover {
            background: #f8fafc;
            color: var(--primary);
            border-color: #cbd5e1;
            transform: scale(1.05);
        }
        .topbar-clock {
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 0.4rem 1rem 0.4rem 0.75rem;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            transition: all 0.25s ease;
        }
        .topbar-clock:hover {
            border-color: #cbd5e1;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
            transform: translateY(-1px);
        }
        .topbar-clock .clock-icon-wrap {
            width: 26px; height: 26px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 2px 6px rgba(37,99,235,0.25);
        }
        .topbar-clock .clock-icon-wrap i {
            font-size: 0.72rem;
            color: #fff;
        }
        .topbar-clock .clock-divider {
            width: 1px; height: 20px;
            background: #e2e8f0;
            margin: 0 0.15rem;
        }
        .topbar-clock .clock-date {
            font-size: 0.73rem;
            font-weight: 600;
            color: #64748b;
        }
        .topbar-clock .clock-time {
            font-size: 0.85rem;
            font-weight: 700;
            color: #1e293b;
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.5px;
        }
        .topbar-clock .clock-live-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: #10b981;
            flex-shrink: 0;
            box-shadow: 0 0 0 0 rgba(16,185,129,0.7);
            animation: live-pulse 2s infinite;
        }
        @keyframes live-pulse {
            0%   { box-shadow: 0 0 0 0 rgba(16,185,129,0.7); }
            70%  { box-shadow: 0 0 0 5px rgba(16,185,129,0); }
            100% { box-shadow: 0 0 0 0 rgba(16,185,129,0); }
        }
        .topbar-icon-btn {
            width: 38px; height: 38px;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #e2e8f0;
            display: flex; align-items: center; justify-content: center;
            color: #475569; position: relative;
            transition: all 0.2s;
            cursor: pointer;
        }
        .topbar-icon-btn:hover {
            background: #f8fafc;
            color: var(--primary);
            border-color: #cbd5e1;
            transform: translateY(-1px);
        }
        .topbar-icon-btn .badge-dot {
            position: absolute;
            top: -2px; right: -2px;
            min-width: 16px; height: 16px;
            padding: 0 4px;
            background: var(--danger);
            border: 2px solid #fff;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 0.58rem; font-weight: 700;
        }
        .bell-shake {
            animation: bell-ring 1.5s ease-in-out infinite;
        }
        @keyframes bell-ring {
            0%, 100% { transform: rotate(0); }
            10% { transform: rotate(15deg); }
            20% { transform: rotate(-10deg); }
            30% { transform: rotate(10deg); }
            40% { transform: rotate(-5deg); }
            50% { transform: rotate(5deg); }
            60% { transform: rotate(0); }
        }

        /* Profile Dropdown */
        .profile-dropdown-btn {
            display: flex; align-items: center; gap: 0.6rem;
            border: none; background: none; padding: 4px 10px 4px 4px;
            border-radius: 50px; cursor: pointer; transition: all 0.2s;
            text-align: left;
        }
        .profile-dropdown-btn:hover {
            background: rgba(0,0,0,0.03);
        }
        .profile-avatar-gradient {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 0.85rem; color: #fff;
            background: linear-gradient(135deg, #3b82f6 0%, #8b5cf6 100%);
            box-shadow: 0 4px 10px rgba(59, 130, 246, 0.25);
            transition: all 0.2s;
        }
        .profile-dropdown-btn:hover .profile-avatar-gradient {
            transform: scale(1.05);
        }

        /* Dropdowns Premium */
        .dropdown-menu-premium {
            border-radius: 14px !important;
            border: 1px solid rgba(226, 232, 240, 0.8) !important;
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.08) !important;
            padding: 0.4rem !important;
            animation: dropdownFadeIn 0.2s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            background: #ffffff !important;
        }
        @keyframes dropdownFadeIn {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .dropdown-menu-premium .dropdown-item {
            border-radius: 8px;
            padding: 0.5rem 0.85rem;
            font-size: 0.82rem;
            font-weight: 500;
            color: #475569;
            transition: all 0.2s;
        }
        .dropdown-menu-premium .dropdown-item:hover {
            background: var(--primary-light);
            color: var(--primary);
            transform: translateX(3px);
        }
        .dropdown-menu-premium .dropdown-item i {
            font-size: 1rem;
            margin-right: 0.5rem;
            color: #94a3b8;
            transition: color 0.2s;
            vertical-align: middle;
        }
        .dropdown-menu-premium .dropdown-item:hover i {
            color: var(--primary);
        }

        /* ── UI Components (Cards, Buttons, Tables) ── */
        .stat-card {
            background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);
            border-radius: 16px; padding: 1.5rem;
            border: var(--glass-border); transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            box-shadow: var(--card-shadow);
        }
        .stat-card:hover { box-shadow: 0 20px 40px -15px rgba(0,0,0,0.1); transform: translateY(-4px); border-color: rgba(37,99,235,0.2); }
        .stat-icon { 
            width: 54px; height: 54px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center; font-size: 1.6rem; 
            box-shadow: inset 0 2px 4px rgba(255,255,255,0.5), 0 4px 10px rgba(0,0,0,0.05);
        }

        .table-card { 
            background: #fff; border-radius: 16px; 
            border: 1px solid rgba(226, 232, 240, 0.8); 
            overflow: hidden; box-shadow: var(--card-shadow); 
        }
        .table-card-header { 
            padding: 1.25rem 1.5rem; border-bottom: 1px solid #f1f5f9;
            display: flex; align-items: center; justify-content: space-between;
            background: #ffffff;
        }
        .table thead th { 
            background: #f8fafc; font-size: 0.75rem;
            text-transform: uppercase; letter-spacing: 1px; color: #64748b;
            border-bottom: 2px solid #e2e8f0; font-weight: 700; padding: 1rem 1.5rem; 
        }
        .table tbody td { padding: 1rem 1.5rem; vertical-align: middle; font-size: 0.9rem; border-color: #f1f5f9; color: #334155; }
        .table tbody tr { transition: background 0.2s; }
        .table tbody tr:hover { background: var(--primary-light); }

        /* Premium Buttons */
        .btn { border-radius: 8px; font-weight: 600; padding: 0.5rem 1.25rem; transition: all 0.2s; letter-spacing: 0.3px; }
        .btn-primary { 
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); 
            border: none; box-shadow: 0 4px 10px rgba(37,99,235,0.3);
        }
        .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 15px rgba(37,99,235,0.4); background: linear-gradient(135deg, #3b82f6 0%, var(--primary) 100%); }
        .btn-light { background: #fff; border: 1px solid #e2e8f0; color: #475569; }
        .btn-light:hover { background: #f8fafc; color: #1e293b; border-color: #cbd5e1; }

        /* ── Badges Premium ── */
        .badge { padding: 0.4em 0.8em; font-weight: 700; border-radius: 6px; letter-spacing: 0.5px; }
        .badge-pending    { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-dikonfirmasi { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-dipesan    { background: #dbeafe; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-dibatalkan { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-selesai    { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
        .badge-menunggu   { background: #fef3c7; color: #b45309; border: 1px solid #fde68a; }
        .badge-diverifikasi { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .badge-ditolak    { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
        .badge-kedaluwarsa { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

        /* ── Premium Pagination ── */
        nav.d-flex.justify-items-center.justify-content-between {
            align-items: center; background: #fff; padding: 0.4rem 0.85rem;
            border-radius: 10px; border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 2px 8px rgba(0,0,0,0.02); margin-top: 0.5rem; width: 100%;
        }
        nav.d-flex p.small.text-muted { margin: 0; font-size: 0.75rem; color: #64748b !important; }
        nav.d-flex p.small.text-muted .fw-semibold { color: #0f172a; font-weight: 800 !important; }
        .pagination { gap: 4px; flex-wrap: wrap; margin-bottom: 0; }
        .page-item .page-link {
            border: 1px solid #e2e8f0; border-radius: 6px !important;
            color: #475569; font-weight: 600; padding: 0.25rem 0.6rem;
            transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1); background: #fff;
            font-size: 0.72rem; display: flex; align-items: center; justify-content: center; min-width: 26px;
        }
        .page-item .page-link:hover {
            background: #f8fafc; color: var(--primary); border-color: #cbd5e1;
            transform: translateY(-1px); box-shadow: 0 2px 5px rgba(0,0,0,0.04);
        }
        .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: #fff; border-color: transparent;
            box-shadow: 0 3px 8px rgba(37,99,235,0.25);
        }
        .page-item.disabled .page-link {
            background: #f8fafc; color: #cbd5e1; border-color: #f1f5f9; box-shadow: none;
        }

        /* ── Responsive & Toggle ── */
        .main-content.expanded { margin-left: 72px; width: calc(100% - 72px); }
        @media (max-width: 768px) {
            .sidebar { width: 260px; transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .sidebar.collapsed { transform: translateX(0); width: 72px; }
            .main-content { margin-left: 0; padding: 0; width: 100%; }
            .main-content.expanded { margin-left: 0; width: 100%; }
            .topbar { padding: 1rem; }
            .content-area { padding: 1rem; }
        }

        /* SweetAlert Premium Custom Animations & Styles */
        @keyframes popIn { 0% { transform: scale(0.5); opacity: 0; } 100% { transform: scale(1); opacity: 1; } }
        .swal-premium {
            border-radius: 24px !important; padding: 1.5rem !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
            border: 1px solid rgba(255,255,255,0.6) !important;
            background: rgba(255, 255, 255, 0.95) !important;
            backdrop-filter: blur(20px) !important; -webkit-backdrop-filter: blur(20px) !important;
        }
        .swal-btn-premium {
            border-radius: 50px !important; padding: 0.75rem 2rem !important;
            font-weight: 700 !important; font-family: 'Plus Jakarta Sans', sans-serif !important;
            letter-spacing: 0.5px !important; transition: all 0.3s ease !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
        }
        .swal-btn-premium:hover { transform: translateY(-2px) !important; box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important; }
    </style>
    @stack('styles')
</head>
<body>
    <div class="d-flex">
        {{-- Sidebar Global --}}
        @if(auth()->check())
            @if(auth()->user()->role == 'admin')
                @include('layouts.sidebar-admin')
            @else
                @include('layouts.sidebar-pelanggan')
            @endif
        @endif

        <div class="main-content flex-grow-1">
            {{-- Topbar Global --}}
            <div class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <button onclick="toggleSidebar()" class="topbar-toggle-btn shadow-sm" aria-label="Toggle Sidebar">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                    <div class="d-none d-md-flex align-items-center">
                        <div class="topbar-clock" id="liveClockWidget" title="Waktu Sistem Real-time">
                            <div class="clock-icon-wrap">
                                <i class="bi bi-clock-fill"></i>
                            </div>
                            <span class="clock-date" id="liveClockDate">Memuat...</span>
                            <div class="clock-divider"></div>
                            <span class="clock-time" id="liveClockTime">--:--:--</span>
                            <div class="clock-live-dot" title="Live"></div>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-3">
                    {{-- Judul Halaman Tengah/Kiri --}}
                    <div class="me-3 text-end d-none d-lg-block">
                        <h6 class="mb-0 fw-bold" style="letter-spacing: -0.2px;">@yield('page_title', 'Dashboard')</h6>
                    </div>

                    {{-- Bell Notifikasi khusus Admin --}}
                    @if(auth()->check() && auth()->user()->role == 'admin')
                        <div class="dropdown">
                            <div class="topbar-icon-btn shadow-sm" data-bs-toggle="dropdown" id="notificationDropdown" aria-expanded="false" title="Notifikasi Verifikasi">
                                <i class="bi bi-bell fs-5" id="bellIcon"></i>
                                <span class="badge-dot d-none" id="bellBadge">0</span>
                            </div>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-premium" aria-labelledby="notificationDropdown" style="width: 280px; margin-top: 10px;">
                                <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                    <span class="fw-bold" style="font-size: 0.85rem; color: #1e293b;">Notifikasi</span>
                                    <span class="badge bg-success rounded-pill" id="notifBadgeHeader" style="font-size: 0.65rem;">Semua Aman</span>
                                </div>
                                <div id="notifListContainer" class="py-1">
                                    <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.pembayaran.index') }}" style="white-space: normal;">
                                        <div class="rounded-circle bg-warning bg-opacity-10 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                            <i class="bi bi-credit-card-2-front text-warning m-0 fs-6"></i>
                                        </div>
                                        <div style="min-width: 0;">
                                            <div class="fw-bold text-dark text-truncate" style="font-size: 0.8rem;" id="notifTitle">Semua Terverifikasi</div>
                                            <div class="text-muted text-truncate" style="font-size: 0.7rem;" id="notifDesc">Tidak ada pembayaran tertunda</div>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Actions dari Halaman (jika ada) --}}
                    @yield('topbar_actions')

                    {{-- Profile Dropdown --}}
                    @if(auth()->check())
                        @php
                            $user = auth()->user();
                            $initials = strtoupper(substr($user->name, 0, 2));
                        @endphp
                        <div class="dropdown">
                            <button class="profile-dropdown-btn" data-bs-toggle="dropdown" id="profileDropdown" aria-expanded="false">
                                <div class="profile-avatar-gradient">
                                    {{ $initials }}
                                </div>
                                <div class="d-none d-md-block">
                                    <div class="fw-bold text-dark" style="font-size: 0.82rem; line-height: 1.2;">{{ $user->name }}</div>
                                    <div class="text-muted" style="font-size: 0.68rem;">{{ ucfirst($user->role) }}</div>
                                </div>
                                <i class="bi bi-chevron-down text-muted d-none d-md-block" style="font-size: 0.7rem;"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-premium" aria-labelledby="profileDropdown" style="min-width: 200px; margin-top: 10px;">
                                <li class="px-3 py-2 border-bottom d-md-none">
                                    <div class="fw-bold text-dark" style="font-size: 0.82rem;">{{ $user->name }}</div>
                                    <div class="text-muted" style="font-size: 0.68rem;">{{ ucfirst($user->role) }}</div>
                                </li>
                                @if($user->role === 'pelanggan')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profil.show') }}">
                                            <i class="bi bi-person-circle"></i> Profil Saya
                                        </a>
                                    </li>
                                @endif
                                @if($user->role === 'admin')
                                    <li>
                                        <a class="dropdown-item" href="{{ route('admin.profil.show') }}">
                                            <i class="bi bi-person-circle"></i> Kelola Akun
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <a class="dropdown-item" href="{{ route('home') }}">
                                        <i class="bi bi-house-door"></i> Lihat Website
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider" style="opacity: 0.08; margin: 0.35rem 0;"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger d-flex align-items-center btn-logout" style="width: 100%; border: none; background: none; text-align: left;">
                                            <i class="bi bi-box-arrow-left text-danger"></i> Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <div class="content-area">
                @yield('content')
            </div>
        </div>
    </div>

{{-- Bootstrap JS --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
{{-- SweetAlert2 CDN --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Sidebar Toggle Logic
    function toggleSidebar() {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        if (sidebar && mainContent) {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            localStorage.setItem('sidebarState', sidebar.classList.contains('collapsed') ? 'closed' : 'open');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        
        @if(session('success') && (str_contains(session('success'), 'Selamat datang') || str_contains(session('success'), 'Registrasi berhasil') || str_contains(session('success'), 'lanjutkan proses booking')))
            // User baru saja login → paksa sidebar tertutup
            localStorage.setItem('sidebarState', 'closed');
        @endif

        const state = localStorage.getItem('sidebarState');
        // Default ke closed jika belum ada state (baru masuk)
        if ((state === 'closed' || state === null) && sidebar && mainContent) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('expanded');
        }

        // Setup Premium Toast Model
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: '#ffffff',
            color: '#0f172a',
            customClass: { popup: 'swal-toast-premium' },
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({
                html: `
                    <div style="display:flex; align-items:center; gap:14px; text-align:left; padding:4px;">
                        <div style="width:36px; height:36px; border-radius:10px; background:rgba(16,185,129,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="bi bi-check-circle-fill" style="color:#10b981; font-size:1.1rem;"></i>
                        </div>
                        <div>
                            <h4 style="margin:0 0 2px 0; font-size:0.95rem; font-weight:700; color:#0f172a;">Berhasil</h4>
                            <p style="margin:0; font-size:0.8rem; color:#64748b; line-height:1.4">{{ session('success') }}</p>
                        </div>
                    </div>
                `
            });
        @endif

        @if(session('error'))
            Toast.fire({
                html: `
                    <div style="display:flex; align-items:center; gap:14px; text-align:left; padding:4px;">
                        <div style="width:36px; height:36px; border-radius:10px; background:rgba(239,68,68,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                            <i class="bi bi-x-circle-fill" style="color:#ef4444; font-size:1.1rem;"></i>
                        </div>
                        <div>
                            <h4 style="margin:0 0 2px 0; font-size:0.95rem; font-weight:700; color:#0f172a;">Gagal</h4>
                            <p style="margin:0; font-size:0.8rem; color:#64748b; line-height:1.4">{{ session('error') }}</p>
                        </div>
                    </div>
                `
            });
        @endif

        @if($errors->any())
            Toast.fire({
                timer: 6000,
                html: `
                    <div style="display:flex; align-items:flex-start; gap:14px; text-align:left; padding:4px;">
                        <div style="width:36px; height:36px; border-radius:10px; background:rgba(245,158,11,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; margin-top:2px;">
                            <i class="bi bi-exclamation-triangle-fill" style="color:#f59e0b; font-size:1.1rem;"></i>
                        </div>
                        <div>
                            <h4 style="margin:0 0 4px 0; font-size:0.95rem; font-weight:700; color:#0f172a;">Validasi Gagal</h4>
                            <div style="margin:0; font-size:0.8rem; color:#64748b; line-height:1.4">
                                @foreach($errors->all() as $error)
                                    <div style="margin-bottom:2px">• {{ $error }}</div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                `
            });
        @endif

        // ── Global Logout Handler (mengatasi 419 PAGE EXPIRED) ──
        // Ambil CSRF token dari meta tag (selalu fresh setiap load halaman)
        function doLogout(e) {
            if (e) e.preventDefault();

            // Reset form booking jika ada (cegah dialog "Leave page?")
            var bookingForm = document.getElementById('formBookingCreate');
            if (bookingForm) bookingForm.reset();
            window.onbeforeunload = null;

            var token = document.querySelector('meta[name="csrf-token"]');
            var csrfToken = token ? token.getAttribute('content') : '';

            fetch('{{ route("logout") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({}),
            })
            .then(function(response) {
                // Berhasil atau 419 (token expired) → redirect ke home
                window.location.href = '{{ route("home") }}';
            })
            .catch(function() {
                // Jika fetch gagal total, tetap redirect ke home
                window.location.href = '{{ route("home") }}';
            });
        }

        // Pasang doLogout ke semua tombol logout di halaman ini
        document.querySelectorAll('.btn-logout, .btn-logout-icon').forEach(function(btn) {
            btn.closest('form') && btn.closest('form').addEventListener('submit', doLogout);
        });

        // ── Widget Jam Digital Real-time ──
        function updateLiveClock() {
            const dateEl = document.getElementById('liveClockDate');
            const timeEl = document.getElementById('liveClockTime');
            if (!dateEl || !timeEl) return;
            
            const now = new Date();
            const days = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
            const months = [
                'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 
                'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
            ];
            
            const dayName = days[now.getDay()];
            const day = String(now.getDate()).padStart(2, '0');
            const monthName = months[now.getMonth()];
            
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            
            dateEl.textContent = `${dayName}, ${day} ${monthName}`;
            timeEl.textContent = `${hours}:${minutes}:${seconds}`;
        }
        
        setInterval(updateLiveClock, 1000);
        updateLiveClock(); // Jalankan langsung saat muat
    });
</script>

@if(auth()->check() && auth()->user()->role === 'admin')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Minta Izin Notifikasi Desktop ke Browser
        if ("Notification" in window) {
            if (Notification.permission !== "granted" && Notification.permission !== "denied") {
                Notification.requestPermission();
            }
        }

        let lastPendingCount = -1; // -1 agar pertama load langsung mensinkronkan data tanpa notif berlebih

        function checkPendingVerifications() {
            fetch('{{ route("admin.api.pending-verif") }}')
                .then(response => response.json())
                .then(data => {
                    // SINKRONISASI KE TOPBAR NOTIFICATION BELL
                    const bellIcon = document.getElementById('bellIcon');
                    const bellBadge = document.getElementById('bellBadge');
                    const notifBadgeHeader = document.getElementById('notifBadgeHeader');
                    const notifListContainer = document.getElementById('notifListContainer');
                    
                    if (data.pending_count > 0) {
                        if (bellIcon) {
                            bellIcon.classList.remove('bi-bell');
                            bellIcon.classList.add('bi-bell-fill', 'bell-shake');
                        }
                        if (bellBadge) {
                            bellBadge.textContent = data.pending_count;
                            bellBadge.classList.remove('d-none');
                        }
                        if (notifBadgeHeader) {
                            notifBadgeHeader.textContent = data.pending_count + ' Pending';
                            notifBadgeHeader.classList.remove('bg-success');
                            notifBadgeHeader.classList.add('bg-danger');
                        }
                    } else {
                        if (bellIcon) {
                            bellIcon.classList.remove('bi-bell-fill', 'bell-shake');
                            bellIcon.classList.add('bi-bell');
                        }
                        if (bellBadge) {
                            bellBadge.classList.add('d-none');
                        }
                        if (notifBadgeHeader) {
                            notifBadgeHeader.textContent = 'Semua Aman';
                            notifBadgeHeader.classList.remove('bg-danger');
                            notifBadgeHeader.classList.add('bg-success');
                        }
                    }

                    // DYNAMIC DROPDOWN CONTENT
                    if (notifListContainer) {
                        let html = '';
                        if (data.booking_count > 0) {
                            html += `
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.pembayaran.index') }}" style="white-space: normal;">
                                    <div class="rounded-circle bg-warning bg-opacity-10 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                        <i class="bi bi-credit-card-2-front text-warning m-0 fs-6"></i>
                                    </div>
                                    <div style="min-width: 0;">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.8rem;">Pembayaran Booking</div>
                                        <div class="text-muted text-truncate" style="font-size: 0.7rem;">Ada ${data.booking_count} transaksi pending</div>
                                    </div>
                                </a>
                            `;
                        }
                        if (data.membership_count > 0) {
                            html += `
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.pembayaran-membership.index') }}" style="white-space: normal;">
                                    <div class="rounded-circle bg-info bg-opacity-10 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                        <i class="bi bi-person-badge-fill text-info m-0 fs-6"></i>
                                    </div>
                                    <div style="min-width: 0;">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.8rem;">Pembayaran Membership</div>
                                        <div class="text-muted text-truncate" style="font-size: 0.7rem;">Ada ${data.membership_count} membership pending</div>
                                    </div>
                                </a>
                            `;
                        }
                        if (html === '') {
                            html = `
                                <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.pembayaran.index') }}" style="white-space: normal;">
                                    <div class="rounded-circle bg-success bg-opacity-10 p-2 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 32px; height: 32px;">
                                        <i class="bi bi-credit-card-2-front text-success m-0 fs-6"></i>
                                    </div>
                                    <div style="min-width: 0;">
                                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.8rem;">Semua Terverifikasi</div>
                                        <div class="text-muted text-truncate" style="font-size: 0.7rem;">Tidak ada pembayaran tertunda</div>
                                    </div>
                                </a>
                            `;
                        }
                        notifListContainer.innerHTML = html;
                    }

                    // Jika ini request pertama kali, cukup simpan jumlah saat ini
                    if (lastPendingCount === -1) {
                        lastPendingCount = data.pending_count;
                        return;
                    }

                    // Jika jumlah pending naik (ada yang baru bayar)
                    if (data.pending_count > lastPendingCount) {
                        if (Notification.permission === "granted") {
                            const notification = new Notification("Pembayaran Baru Menunggu Verifikasi", {
                                body: "Terdapat " + data.pending_count + " pembayaran yang menunggu divalidasi admin.",
                                icon: "https://cdn-icons-png.flaticon.com/512/1041/1041888.png"
                            });
                            
                            notification.onclick = function() {
                                window.focus();
                                if (data.booking_count > 0) {
                                    window.location.href = "{{ route('admin.pembayaran.index') }}";
                                } else if (data.membership_count > 0) {
                                    window.location.href = "{{ route('admin.pembayaran-membership.index') }}";
                                } else {
                                    window.location.href = "{{ route('admin.pembayaran.index') }}";
                                }
                            };
                        }
                    }
                    
                    lastPendingCount = data.pending_count;
                })
                .catch(err => console.log('Silently ignoring API notification error:', err));
        }

        // Cek secara berkala setiap 15 detik (Background Polling)
        setInterval(checkPendingVerifications, 15000);
        
        // Jalankan sekali saat baru load
        checkPendingVerifications();
    });
</script>
@endif

@stack('scripts')
</body>
</html>
