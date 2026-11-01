<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Kos') - KostKu</title>
    <meta name="description" content="Sistem Informasi Pelaporan dan Pengelolaan Pengaduan Penghuni Kos">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #2563EB;
            --primary-dark: #1d4ed8;
            --primary-light: #eff6ff;
            --sidebar-width: 250px;
            --sidebar-bg: #ffffff;
            --sidebar-border: #eef2f6;
            --sidebar-text: #475569;
            --sidebar-hover-bg: #f8fafc;
            --sidebar-hover-text: #0f172a;
            --sidebar-active-bg: #eff6ff;
            --sidebar-active-text: #1d4ed8;
            --navbar-height: 64px;
            --body-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #64748b;
            --border-color: #e2e8f0;
            --border-light: #f1f5f9;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: var(--body-bg);
            color: var(--text-primary);
            overflow-x: hidden;
            font-size: 14px;
            line-height: 1.5;
        }

        /* ── Sidebar (Pure White Clean Style) ── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--sidebar-border);
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform 0.25s ease;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #e2e8f0 transparent;
        }

        .sidebar-brand {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-light);
            background: #ffffff;
        }

        .sidebar-brand .brand-icon {
            width: 38px; height: 38px;
            background: var(--primary);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; color: white;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.2);
            flex-shrink: 0;
        }

        .sidebar-brand .brand-name {
            font-size: 15px; font-weight: 700;
            color: #0f172a; line-height: 1.2;
            letter-spacing: -0.2px;
        }
        .sidebar-brand .brand-sub {
            font-size: 11px; color: var(--text-secondary);
            font-weight: 500;
        }

        .sidebar-menu {
            padding: 12px 0;
            flex: 1;
        }

        .sidebar-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #94a3b8;
            padding: 14px 20px 4px;
            display: block;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 9px 12px;
            margin: 2px 10px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.15s ease;
            gap: 10px;
            position: relative;
        }

        .sidebar-link:hover {
            color: var(--sidebar-hover-text);
            background: var(--sidebar-hover-bg);
        }

        .sidebar-link.active {
            color: var(--sidebar-active-text);
            background: var(--sidebar-active-bg);
            font-weight: 600;
        }

        .sidebar-link.active .sidebar-icon {
            color: var(--primary);
        }

        .sidebar-icon {
            font-size: 16px;
            color: #64748b;
            flex-shrink: 0;
            transition: color 0.15s;
        }

        .sidebar-link:hover .sidebar-icon {
            color: #1e293b;
        }

        .sidebar-badge {
            margin-left: auto;
            background: #fee2e2;
            color: #b91c1c;
            font-size: 11px;
            font-weight: 600;
            padding: 1px 7px;
            border-radius: 999px;
            min-width: 18px;
            text-align: center;
        }

        .sidebar-footer {
            padding: 12px 14px;
            border-top: 1px solid var(--border-light);
            background: #ffffff;
        }

        .sidebar-footer-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 100%;
            padding: 9px 12px;
            border-radius: 8px;
            background: none;
            border: none;
            color: #dc2626;
            font-size: 13.5px;
            font-weight: 500;
            cursor: pointer;
            text-align: left;
            transition: all 0.15s ease;
        }
        .sidebar-footer-btn:hover {
            background: #fef2f2;
            color: #b91c1c;
        }

        /* ── Navbar ── */
        .main-navbar {
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            height: var(--navbar-height);
            background: #ffffff;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex;
            align-items: center;
            padding: 0 24px;
            z-index: 999;
            gap: 16px;
        }

        .navbar-title {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
            flex: 1;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-avatar {
            width: 34px;
            height: 34px;
            background: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2563eb;
            font-weight: 600;
            font-size: 13px;
        }

        .notif-btn {
            position: relative;
            width: 36px;
            height: 36px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 17px;
            transition: all 0.15s;
        }
        .notif-btn:hover {
            background: #f8fafc;
            color: #0f172a;
            border-color: #cbd5e1;
        }
        .notif-dot {
            position: absolute;
            top: -4px;
            right: -4px;
            width: 17px;
            height: 17px;
            background: var(--danger);
            color: white;
            font-size: 10px;
            font-weight: 700;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }

        /* ── Main Content ── */
        .main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--navbar-height);
            padding: 24px;
            min-height: calc(100vh - var(--navbar-height));
        }

        /* ── Clean Real Cards ── */
        .card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
            background: #ffffff;
        }

        .card-header {
            background: #ffffff;
            border-bottom: 1px solid var(--border-light);
            padding: 16px 20px;
            font-weight: 600;
            color: #1e293b;
        }

        .stat-card {
            border-radius: 12px;
            padding: 20px;
            border: 1px solid var(--border-color);
            background: #ffffff;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.04);
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 24, 40, 0.06);
        }

        .stat-card .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }

        .stat-card .stat-value {
            font-size: 26px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.1;
        }

        .stat-card .stat-label {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* ── Human-designed Soft Badges ── */
        .badge {
            font-weight: 500;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
        }
        .badge.bg-success, .badge.text-bg-success {
            background-color: #ecfdf5 !important;
            color: #047857 !important;
            border: 1px solid #a7f3d0;
        }
        .badge.bg-warning, .badge.text-bg-warning {
            background-color: #fffbeb !important;
            color: #b45309 !important;
            border: 1px solid #fde68a;
        }
        .badge.bg-danger, .badge.text-bg-danger {
            background-color: #fef2f2 !important;
            color: #b91c1c !important;
            border: 1px solid #fecaca;
        }
        .badge.bg-info, .badge.text-bg-info {
            background-color: #eff6ff !important;
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe;
        }
        .badge.bg-secondary, .badge.text-bg-secondary {
            background-color: #f1f5f9 !important;
            color: #475569 !important;
            border: 1px solid #e2e8f0;
        }
        .badge.bg-light, .badge.text-bg-light {
            background-color: #f8fafc !important;
            color: #334155 !important;
            border: 1px solid #e2e8f0;
        }

        /* ── Tables ── */
        .table {
            font-size: 13.5px;
            margin-bottom: 0;
        }
        .table th {
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #64748b;
            background: #f8fafc;
            border-bottom: 1px solid var(--border-color);
            padding: 12px 16px;
        }
        .table td {
            vertical-align: middle;
            padding: 13px 16px;
            border-bottom: 1px solid var(--border-light);
            color: #1e293b;
        }
        .table tbody tr:hover td {
            background: #fbfcfe;
        }

        /* ── Flash Messages ── */
        .alert {
            border-radius: 8px;
            font-size: 13.5px;
            border: 1px solid transparent;
            padding: 12px 16px;
        }
        .alert-success {
            background-color: #ecfdf5;
            border-color: #a7f3d0;
            color: #065f46;
        }
        .alert-warning {
            background-color: #fffbeb;
            border-color: #fde68a;
            color: #92400e;
        }
        .alert-danger {
            background-color: #fef2f2;
            border-color: #fecaca;
            color: #991b1b;
        }

        /* ── Buttons ── */
        .btn {
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 500;
            padding: 8px 14px;
            transition: all 0.15s ease;
        }
        .btn-sm {
            font-size: 12px;
            padding: 5px 10px;
        }
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
        }

        /* ── Forms ── */
        .form-control, .form-select {
            border-radius: 8px;
            border-color: #cbd5e1;
            font-size: 13.5px;
            padding: 8px 12px;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }
        .form-label {
            font-size: 13px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 6px;
        }

        /* ── Page Header ── */
        .page-header {
            margin-bottom: 22px;
        }
        .page-header h1 {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            letter-spacing: -0.2px;
        }
        .page-header .breadcrumb {
            font-size: 12.5px;
            margin: 0;
        }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .main-navbar { left: 0; }
        }

        /* ── Sidebar Toggle ── */
        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 22px;
            color: var(--text-secondary);
            cursor: pointer;
            padding: 0;
        }
        @media (max-width: 991.98px) { .sidebar-toggle { display: block; } }

        /* ── Empty State ── */
        .empty-state {
            text-align: center;
            padding: 48px 20px;
        }
        .empty-state i {
            font-size: 48px;
            color: #cbd5e1;
            margin-bottom: 12px;
        }
        .empty-state h5 {
            color: #334155;
            font-weight: 600;
            font-size: 15px;
        }
        .empty-state p {
            color: #94a3b8;
            font-size: 13px;
        }

        /* ── Dropdown ── */
        .dropdown-menu {
            border-radius: 10px;
            border-color: var(--border-color);
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
            font-size: 13.5px;
            padding: 6px;
        }
        .dropdown-item {
            padding: 7px 12px;
            border-radius: 6px;
            color: #334155;
        }
        .dropdown-item:hover {
            background: #f8fafc;
            color: #0f172a;
        }

        /* ── Notification Dropdown ── */
        .notif-dropdown-wrapper {
            position: relative;
        }
        .notif-dropdown-panel {
            position: absolute;
            top: calc(100% + 8px);
            right: 0;
            width: 380px;
            max-height: 480px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.10), 0 2px 8px rgba(0, 0, 0, 0.04);
            z-index: 2000;
            display: none;
            overflow: hidden;
            animation: notifSlideIn 0.18s ease-out;
        }
        .notif-dropdown-panel.show {
            display: block;
        }
        @keyframes notifSlideIn {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .notif-dropdown-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 16px 10px;
            border-bottom: 1px solid var(--border-light);
        }
        .notif-dropdown-header h6 {
            font-size: 14px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 0;
        }
        .notif-dropdown-header .notif-mark-all {
            font-size: 12px;
            font-weight: 500;
            color: var(--primary);
            background: none;
            border: none;
            cursor: pointer;
            padding: 2px 6px;
            border-radius: 4px;
            transition: background 0.15s;
        }
        .notif-dropdown-header .notif-mark-all:hover {
            background: var(--primary-light);
        }
        .notif-dropdown-body {
            max-height: 360px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #e2e8f0 transparent;
        }
        .notif-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border-light);
            cursor: pointer;
            transition: background 0.12s;
            text-decoration: none;
            color: inherit;
        }
        .notif-item:hover {
            background: #f8fafc;
            color: inherit;
            text-decoration: none;
        }
        .notif-item.unread {
            background: #eff6ff;
        }
        .notif-item.unread:hover {
            background: #dbeafe;
        }
        .notif-item-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            flex-shrink: 0;
            margin-top: 2px;
        }
        .notif-item.unread .notif-item-icon {
            background: var(--primary);
            color: #fff;
        }
        .notif-item:not(.unread) .notif-item-icon {
            background: #f1f5f9;
            color: #94a3b8;
            border: 1px solid #e2e8f0;
        }
        .notif-item-content {
            flex: 1;
            min-width: 0;
        }
        .notif-item-title {
            font-size: 13px;
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 2px;
            line-height: 1.3;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .notif-item.unread .notif-item-title {
            color: #1d4ed8;
        }
        .notif-item-title .notif-new-badge {
            font-size: 9px;
            font-weight: 700;
            background: var(--danger);
            color: white;
            padding: 1px 5px;
            border-radius: 3px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            flex-shrink: 0;
        }
        .notif-item-message {
            font-size: 12px;
            color: var(--text-secondary);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            margin-bottom: 3px;
        }
        .notif-item-time {
            font-size: 11px;
            color: #94a3b8;
        }
        .notif-dropdown-footer {
            padding: 10px 16px;
            border-top: 1px solid var(--border-light);
            text-align: center;
        }
        .notif-dropdown-footer a {
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 4px;
            border-radius: 6px;
            transition: background 0.15s;
        }
        .notif-dropdown-footer a:hover {
            background: var(--primary-light);
        }
        .notif-empty {
            text-align: center;
            padding: 36px 20px;
            color: #94a3b8;
        }
        .notif-empty i {
            font-size: 36px;
            margin-bottom: 8px;
            display: block;
            color: #cbd5e1;
        }
        .notif-empty p {
            font-size: 13px;
            margin: 0;
            color: #94a3b8;
        }
        .notif-loading {
            text-align: center;
            padding: 30px 20px;
            color: #94a3b8;
            font-size: 13px;
        }
        .notif-loading .spinner-border {
            width: 20px;
            height: 20px;
            border-width: 2px;
        }

        @media (max-width: 575.98px) {
            .notif-dropdown-panel {
                width: calc(100vw - 24px);
                right: -60px;
            }
        }

        /* ── Pagination (Minimalist & Professional) ── */
        .pagination {
            margin: 0;
            gap: 5px;
            flex-wrap: wrap;
            align-items: center;
        }
        .pagination .page-item {
            display: inline-block;
        }
        .pagination .page-item .page-link {
            font-size: 13px;
            font-weight: 500;
            padding: 6px 12px;
            min-width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px !important;
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            background: #ffffff;
            transition: all 0.15s ease;
            line-height: 1;
            text-decoration: none;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
        }
        .pagination .page-item .page-link svg {
            width: 14px;
            height: 14px;
            fill: currentColor;
        }
        .pagination .page-item .page-link:hover {
            background: var(--primary-light);
            border-color: #bfdbfe;
            color: var(--primary);
            box-shadow: 0 2px 4px rgba(37,99,235,0.08);
        }
        .pagination .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: #ffffff;
            font-weight: 600;
            box-shadow: 0 2px 6px rgba(37,99,235,0.25);
        }
        .pagination .page-item.disabled .page-link {
            background: #f8fafc;
            border-color: #e2e8f0;
            color: #94a3b8;
            cursor: not-allowed;
            opacity: 0.65;
            box-shadow: none;
        }
        /* Mobile pagination wrapper */
        nav .justify-between .pagination {
            margin-bottom: 0;
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar Overlay (mobile) -->
<div class="sidebar-overlay d-lg-none" id="sidebarOverlay" style="display:none!important;position:fixed;inset:0;background:rgba(15,23,42,0.4);z-index:999;" onclick="toggleSidebar()"></div>

<!-- Sidebar (Pure White) -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-3">
            <div class="brand-icon"><i class="bi bi-house-door-fill"></i></div>
            <div>
                <div class="brand-name">KostKu</div>
                <div class="brand-sub">{{ auth()->user()->isOwner() ? 'Panel Pemilik Kos' : 'Panel Penghuni' }}</div>
            </div>
        </div>
    </div>

    <div class="sidebar-menu">
        @hasSection('sidebar-menu')
            @yield('sidebar-menu')
        @else
            @if(auth()->check() && auth()->user()->isOwner())
                @include('layouts.partials.owner-sidebar')
            @elseif(auth()->check() && auth()->user()->isTenant())
                @include('layouts.partials.tenant-sidebar')
            @endif
        @endif
    </div>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sidebar-footer-btn">
                <i class="bi bi-box-arrow-left"></i>
                Keluar
            </button>
        </form>
    </div>
</aside>

<!-- Navbar -->
<nav class="main-navbar">
    <button class="sidebar-toggle" onclick="toggleSidebar()">
        <i class="bi bi-list"></i>
    </button>

    <div class="navbar-title">@yield('page-title', 'Dashboard')</div>

    <div class="d-flex align-items-center gap-3">
        <!-- Notifikasi Bell Dropdown -->
        @php $unreadCount = \App\Models\AppNotification::where('pengguna_id', auth()->id())->where('is_dibaca', false)->count(); @endphp
        <div class="notif-dropdown-wrapper" id="notifDropdownWrapper">
            <button class="notif-btn" id="notifBellBtn" title="Notifikasi" type="button">
                <i class="bi bi-bell"></i>
                <span class="notif-dot" id="notifBadge" style="{{ $unreadCount > 0 ? '' : 'display:none' }}">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
            </button>
            <div class="notif-dropdown-panel" id="notifDropdownPanel">
                <div class="notif-dropdown-header">
                    <h6><i class="bi bi-bell me-1"></i> Notifikasi</h6>
                    <button class="notif-mark-all" id="notifMarkAllBtn" title="Tandai semua sudah dibaca">
                        <i class="bi bi-check2-all me-1"></i> Tandai dibaca
                    </button>
                </div>
                <div class="notif-dropdown-body" id="notifDropdownBody">
                    <div class="notif-loading">
                        <div class="spinner-border text-primary" role="status"></div>
                        <div class="mt-2">Memuat notifikasi...</div>
                    </div>
                </div>
                <div class="notif-dropdown-footer">
                    <a href="{{ route('notifications.index') }}">
                        Lihat Semua Notifikasi <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- User Dropdown -->
        <div class="dropdown">
            <button class="btn d-flex align-items-center gap-2 p-1 pe-3 border rounded-3" data-bs-toggle="dropdown" style="background:#ffffff;font-size:13px;font-weight:500;">
                <div class="navbar-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                <div class="text-start d-none d-md-block">
                    <div style="font-size:13px;font-weight:600;color:var(--text-primary);">{{ Str::limit(auth()->user()->name, 20) }}</div>
                    <div style="font-size:11px;color:var(--text-secondary);">{{ auth()->user()->isOwner() ? 'Pemilik Kos' : 'Penghuni' }}</div>
                </div>
                <i class="bi bi-chevron-down" style="font-size:11px;color:var(--text-secondary);"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end mt-1">
                @if(auth()->user()->isOwner())
                <li><a class="dropdown-item" href="{{ route('owner.profile.edit') }}"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
                @else
                <li><a class="dropdown-item" href="{{ route('tenant.profile.edit') }}"><i class="bi bi-person me-2"></i>Profil Saya</a></li>
                @endif
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-left me-2"></i>Keluar</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="main-content">
    <!-- Flash Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="bi bi-check-circle-fill"></i>
        {{ session('success') }}
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('warning'))
    <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        {{ session('warning') }}
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center gap-2 mb-1"><i class="bi bi-x-circle-fill"></i><strong>Terdapat kesalahan:</strong></div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @yield('content')
</main>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

<script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('show');
    overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
}

// ── Notification Dropdown ──
(function() {
    const bellBtn = document.getElementById('notifBellBtn');
    const panel = document.getElementById('notifDropdownPanel');
    const body = document.getElementById('notifDropdownBody');
    const badge = document.getElementById('notifBadge');
    const markAllBtn = document.getElementById('notifMarkAllBtn');
    const wrapper = document.getElementById('notifDropdownWrapper');
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!bellBtn || !panel) return;

    let isOpen = false;
    let loaded = false;

    // Toggle dropdown
    bellBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        isOpen = !isOpen;
        panel.classList.toggle('show', isOpen);
        if (isOpen) {
            loaded = false;
            loadNotifications();
        }
    });

    // Close on outside click
    document.addEventListener('click', function(e) {
        if (isOpen && !wrapper.contains(e.target)) {
            isOpen = false;
            panel.classList.remove('show');
        }
    });

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && isOpen) {
            isOpen = false;
            panel.classList.remove('show');
        }
    });

    function getNotifIcon(tipe) {
        switch(tipe) {
            case 'status_changed': return 'bi-sliders';
            case 'new_response': return 'bi-chat-dots';
            default: return 'bi-bell';
        }
    }

    function truncateText(text, maxLen) {
        if (!text) return '';
        return text.length > maxLen ? text.substring(0, maxLen) + '…' : text;
    }

    function loadNotifications() {
        body.innerHTML = '<div class="notif-loading"><div class="spinner-border text-primary" role="status"></div><div class="mt-2">Memuat notifikasi...</div></div>';
        fetch("{{ route('notifications.recent') }}", {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            loaded = true;
            updateBadge(data.unread_count);
            renderNotifications(data.notifications);
        })
        .catch(() => {
            body.innerHTML = '<div class="notif-empty"><i class="bi bi-exclamation-circle"></i><p>Gagal memuat notifikasi</p></div>';
        });
    }

    function renderNotifications(notifications) {
        if (!notifications || notifications.length === 0) {
            body.innerHTML = '<div class="notif-empty"><i class="bi bi-bell-slash"></i><p>Tidak ada notifikasi</p></div>';
            return;
        }

        let html = '';
        notifications.forEach(notif => {
            const unreadClass = notif.is_dibaca ? '' : ' unread';
            const icon = getNotifIcon(notif.tipe);
            const newBadge = notif.is_dibaca ? '' : '<span class="notif-new-badge">Baru</span>';

            html += `
                <div class="notif-item${unreadClass}" data-notif-id="${notif.id}" data-url="${notif.url || ''}" onclick="handleNotifClick(this)">
                    <div class="notif-item-icon"><i class="bi ${icon}"></i></div>
                    <div class="notif-item-content">
                        <div class="notif-item-title">${truncateText(notif.judul, 50)} ${newBadge}</div>
                        <div class="notif-item-message">${truncateText(notif.pesan, 100)}</div>
                        <div class="notif-item-time"><i class="bi bi-clock me-1"></i>${notif.waktu} &middot; ${notif.tanggal}</div>
                    </div>
                </div>
            `;
        });
        body.innerHTML = html;
    }

    function updateBadge(count) {
        if (count > 0) {
            badge.style.display = 'flex';
            badge.textContent = count > 9 ? '9+' : count;
        } else {
            badge.style.display = 'none';
        }
    }

    // Mark all as read
    markAllBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        fetch("{{ route('notifications.read-all') }}", {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(() => {
            // Mark all items as read visually
            body.querySelectorAll('.notif-item.unread').forEach(item => {
                item.classList.remove('unread');
                const iconEl = item.querySelector('.notif-item-icon');
                if (iconEl) {
                    iconEl.style.background = '#f1f5f9';
                    iconEl.style.color = '#94a3b8';
                    iconEl.style.border = '1px solid #e2e8f0';
                }
                const titleEl = item.querySelector('.notif-item-title');
                if (titleEl) titleEl.style.color = '';
                const badgeEl = item.querySelector('.notif-new-badge');
                if (badgeEl) badgeEl.remove();
            });
            updateBadge(0);
        });
    });

    // Click on notification item
    window.handleNotifClick = function(el) {
        const notifId = el.dataset.notifId;
        const url = el.dataset.url;

        // Mark as read
        if (el.classList.contains('unread')) {
            fetch(`/notifikasi/${notifId}/baca-json`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(() => {
                el.classList.remove('unread');
                const iconEl = el.querySelector('.notif-item-icon');
                if (iconEl) {
                    iconEl.style.background = '#f1f5f9';
                    iconEl.style.color = '#94a3b8';
                    iconEl.style.border = '1px solid #e2e8f0';
                }
                const titleEl = el.querySelector('.notif-item-title');
                if (titleEl) titleEl.style.color = '';
                const badgeEl = el.querySelector('.notif-new-badge');
                if (badgeEl) badgeEl.remove();

                // Update badge count
                const currentCount = parseInt(badge.textContent) || 0;
                updateBadge(Math.max(0, currentCount - 1));
            });
        }

        // Navigate if URL exists
        if (url) {
            setTimeout(() => { window.location.href = url; }, 150);
        }
    };

})();
</script>

@stack('scripts')
</body>
</html>
