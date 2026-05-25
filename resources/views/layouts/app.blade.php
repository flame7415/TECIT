<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'MCIMS - Municipal Complaint & Incident Management System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --dark: #1f2937;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-400: #9ca3af;
            --gray-500: #6b7280;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-800: #1f2937;
            --gray-900: #111827;
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            font-size: 14px;
            color: var(--gray-700);
            background: var(--gray-100);
            margin: 0;
        }

        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            background: var(--gray-900);
            width: 260px;
            position: fixed;
            left: 0;
            top: 0;
        }

        .sidebar-brand {
            padding: 24px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-brand h5 {
            color: #fff;
            font-size: 22px;
            font-weight: 700;
            margin: 0;
        }

        .sidebar-brand small {
            color: var(--gray-400);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .sidebar a {
            color: var(--gray-400);
            text-decoration: none;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            transition: all 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar a:hover {
            color: #fff;
            background: rgba(255,255,255,0.05);
        }

        .sidebar a.active {
            color: #fff;
            background: rgba(79, 70, 229, 0.2);
            border-left-color: var(--primary);
        }

        .sidebar a i { width: 20px; text-align: center; }

        .sidebar .btn-logout {
            margin: 20px;
            padding: 12px;
            background: transparent;
            border: 1px solid var(--gray-700);
            border-radius: 8px;
            color: var(--gray-400);
            font-weight: 500;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .sidebar .btn-logout:hover {
            background: var(--danger);
            border-color: var(--danger);
            color: #fff;
        }

        /* Main Content */
        .main-content {
            margin-left: 260px;
            min-height: 100vh;
        }

        .top-bar {
            background: #fff;
            padding: 16px 32px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--gray-800);
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-name { font-weight: 600; color: var(--gray-700); }

        .content-area {
            padding: 32px;
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--shadow);
            background: #fff;
            margin-bottom: 24px;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid var(--gray-100);
            padding: 20px 24px;
            font-weight: 600;
            font-size: 16px;
            color: var(--gray-800);
        }

        .card-body { padding: 24px; }

        /* Stats Cards */
        .stats-card {
            border-radius: 12px;
            padding: 24px;
            color: #fff;
            position: relative;
        }

        .stats-card.primary { background: var(--primary); }
        .stats-card.success { background: var(--success); }
        .stats-card.warning { background: var(--warning); }
        .stats-card.danger { background: var(--danger); }
        .stats-card.info { background: var(--info); }

        .stats-card h5 {
            font-size: 13px;
            font-weight: 500;
            opacity: 0.9;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stats-card h2 {
            font-size: 36px;
            font-weight: 700;
            margin: 0;
        }

        .stats-card .icon {
            position: absolute;
            bottom: 20px;
            right: 24px;
            font-size: 48px;
            opacity: 0.15;
        }

        /* Buttons */
        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary { background: var(--primary); color: #fff; }
        .btn-primary:hover { background: var(--primary-hover); color: #fff; }

        .btn-success { background: var(--success); color: #fff; }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-info { background: var(--info); color: #fff; }
        .btn-outline-secondary { border: 1px solid var(--gray-300); color: var(--gray-600); }

        .btn-sm { padding: 6px 12px; font-size: 13px; }

        /* Forms */
        .form-control, .form-select {
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 14px;
            transition: all 0.2s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
            outline: none;
        }

        .form-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 8px;
            font-size: 14px;
        }

        .input-group-text {
            background: var(--gray-50);
            border: 1px solid var(--gray-200);
            border-radius: 8px 0 0 8px;
            color: var(--gray-500);
        }

        /* Tables */
        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: var(--gray-50);
            color: var(--gray-600);
            font-weight: 600;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 16px;
            border: none;
        }

        .table tbody td {
            padding: 16px;
            vertical-align: middle;
            border-bottom: 1px solid var(--gray-100);
            color: var(--gray-700);
        }

        .table tbody tr:hover { background: var(--gray-50); }

        /* Badges */
        .badge {
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 20px;
        }

        .badge.bg-primary { background: var(--primary) !important; }
        .badge.bg-success { background: var(--success) !important; }
        .badge.bg-warning { background: var(--warning) !important; color: #fff; }
        .badge.bg-danger { background: var(--danger) !important; }
        .badge.bg-info { background: var(--info) !important; }
        .badge.bg-secondary { background: var(--gray-500) !important; }

        /* Priority */
        .priority-critical { background: var(--danger); color: #fff; }
        .priority-high { background: #f97316; color: #fff; }
        .priority-medium { background: var(--warning); color: #000; }
        .priority-low { background: var(--success); color: #fff; }

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 260px;
            width: 100%;
        }

        /* Filter Bar */
        .filter-bar {
            background: #fff;
            border-radius: 12px;
            padding: 20px 24px;
            box-shadow: var(--shadow);
            margin-bottom: 24px;
        }

        /* Action Bar */
        .action-bar {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        /* Pagination */
        .pagination .page-link {
            border: none;
            border-radius: 8px;
            padding: 8px 14px;
            font-weight: 500;
            color: var(--gray-600);
            background: #fff;
            margin: 0 3px;
            box-shadow: var(--shadow);
        }

        .pagination .page-link:hover {
            background: var(--primary);
            color: #fff;
        }

        .pagination .page-item.active .page-link {
            background: var(--primary);
            color: #fff;
        }

        /* Auth Pages */
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, var(--gray-800) 0%, var(--gray-900) 100%);
            padding: 20px;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .auth-header {
            background: var(--primary);
            padding: 32px;
            text-align: center;
            color: #fff;
        }

        .auth-header h4 { margin: 0; font-size: 22px; font-weight: 700; }
        .auth-header i { font-size: 40px; margin-bottom: 8px; display: block; }

        .auth-body { background: #fff; padding: 32px; }

        .demo-accounts {
            background: var(--gray-50);
            border-radius: 8px;
            padding: 16px;
            margin-top: 20px;
            border-left: 4px solid var(--primary);
        }

        .demo-accounts h6 { color: var(--primary); margin-bottom: 8px; font-weight: 600; }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--gray-500);
        }

        .empty-state i { font-size: 56px; margin-bottom: 16px; opacity: 0.3; }
        .empty-state h5 { color: var(--gray-700); margin-bottom: 8px; }

        /* Responsive - Mobile First */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                left: -280px;
                width: 280px;
                z-index: 1050;
                transition: left 0.3s ease;
            }

            .sidebar.active { left: 0; }

            .main-content {
                margin-left: 0;
                width: 100%;
            }

            .content-area {
                padding: 16px;
            }

            .top-bar {
                padding: 12px 16px;
                flex-wrap: wrap;
                gap: 10px;
            }

            .page-title {
                font-size: 18px;
            }

            .user-info {
                gap: 8px;
                flex-direction: column;
                align-items: flex-end;
                position: relative;
            }

            .user-name {
                font-size: 13px;
            }

            .user-sidebar-info {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: left;
                gap: 8px;
            }
            .user-name-sidebar {
                font-weight: 600;
                color: rgba(255,255,255,0.9);
                font-size: 13px;
                flex: 1;
                margin: 0;
            }
            .sidebar-role {
                font-size: 11px;
                flex-shrink: 0;
            }

            .mobile-user-menu {
                position: absolute;
                top: 100%;
                right: 0;
                background: var(--gray-900);
                border-radius: 8px;
                padding: 8px 0;
                box-shadow: var(--shadow-md);
                min-width: 160px;
                display: none;
                z-index: 1000;
            }

            .mobile-user-menu.show {
                display: block;
            }

            .mobile-user-menu .user-name {
                color: #fff;
                padding: 8px 16px;
                font-size: 14px;
                font-weight: 500;
            }

            .mobile-user-menu .role-badge {
                padding: 4px 8px;
                font-size: 11px;
                margin: 0 16px 8px;
            }

            .mobile-user-menu a {
                display: block;
                padding: 8px 16px;
                color: var(--gray-400);
                text-decoration: none;
                font-weight: 500;
            }

            .mobile-user-menu a:hover {
                background: rgba(255,255,255,0.1);
                color: #fff;
            }

            /* Mobile Menu Toggle */
            .menu-toggle {
                display: flex !important;
                align-items: center;
                justify-content: center;
                width: 40px;
                height: 40px;
                background: var(--primary);
                color: #fff;
                border: none;
                border-radius: 8px;
                font-size: 20px;
                cursor: pointer;
            }

            /* Overlay */
            .sidebar-overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.5);
                z-index: 1040;
            }

            .sidebar-overlay.active { display: block; }

        /* Stats Cards Mobile Horizontal - Always visible, no stack/hide */
        .stats-row {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto;
            gap: 12px;
            padding-bottom: 12px;
            scrollbar-width: thin;
            -webkit-overflow-scrolling: touch;
        }
        .stats-row .stats-card {
            flex: 0 0 220px;
            margin-bottom: 0;
            padding: 16px;
        }
        .stats-row::-webkit-scrollbar {
            height: 4px;
        }
        .stats-row::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 2px;
        }

            .stats-card h2 {
                font-size: 28px;
            }

            .stats-card .icon {
                font-size: 36px;
            }

            /* Tables Mobile - Fit "My Recent Complaints" */
            .recent-complaints-table {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            @media (max-width: 768px) {
                .recent-complaints-table thead th,
                .recent-complaints-table tbody td {
                    padding: 8px 6px;
                    font-size: 12px;
                    white-space: nowrap;
                }
                .recent-complaints-table .badge {
                    font-size: 10px;
                    padding: 3px 6px;
                }
                .recent-complaints-table .btn-sm {
                    padding: 4px 8px;
                    font-size: 11px;
                }
            }

            /* Auth Mobile */
            .auth-card {
                margin: 16px;
                max-width: calc(100% - 32px);
            }

            .auth-body {
                padding: 20px 16px;
            }

            /* Buttons Mobile */
            .btn {
                padding: 12px 16px;
                min-height: 44px;
                font-size: 14px;
            }

            .btn-sm {
                padding: 8px 12px;
                min-height: 36px;
            }

            /* Forms Mobile */
            .form-control, .form-select {
                padding: 12px 14px;
                font-size: 16px; /* Prevents zoom on iOS */
            }

            /* Cards Mobile */
            .card {
                margin-bottom: 16px;
                border-radius: 10px;
            }

            .card-header {
                padding: 16px;
                flex-direction: column;
                gap: 10px;
                align-items: flex-start !important;
            }

            .card-body {
                padding: 16px;
            }

            /* Demo Accounts Mobile */
            .demo-accounts {
                padding: 12px;
                font-size: 13px;
            }

            /* Badges Mobile */
            .badge {
                padding: 4px 8px;
                font-size: 11px;
            }
        }

        /* Extra Small Devices */
        @media (max-width: 480px) {
            .stats-card {
                padding: 14px;
            }

            .stats-card h2 {
                font-size: 24px;
            }

            .stats-card h5 {
                font-size: 11px;
            }

            .content-area {
                padding: 12px;
            }

            .page-title {
                font-size: 16px;
            }

            .auth-header {
                padding: 24px 16px;
            }

            .auth-header h4 {
                font-size: 20px;
            }
        }

        /* Always show menu toggle on mobile */
        .menu-toggle { display: none; }

        /* Landscape mobile */
        @media (max-height: 500px) and (orientation: landscape) {
            .sidebar {
                overflow-y: auto;
            }

            .sidebar a {
                padding: 10px 16px;
            }
        }

        /* Touch-friendly improvements */
        @media (pointer: coarse) {
            .btn, .page-link, .form-check-input {
                min-height: 44px;
                min-width: 44px;
            }

            a, button {
                min-height: 44px;
                min-width: 44px;
                display: inline-flex;
                align-items: center;
            }

            .sidebar a {
                min-height: auto;
                min-width: auto;
                display: flex;
            }

            .btn-logout {
                min-height: auto;
                min-width: auto;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    @auth
    <div class="sidebar">
        <div class="sidebar-brand">
            <h5><i class="fas fa-shield-alt me-2"></i>MCIMS</h5>
            <small>Complaint System</small>
            <div class="user-sidebar-info mt-3 pt-2 border-top border-dark border-opacity-25">
                <div class="user-name-sidebar">{{ Auth::user()->name }}</div>
                <span class="badge bg-primary sidebar-role">{{ ucfirst(Auth::user()->role) }}</span>
            </div>
        </div>
        <nav>
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Dashboard
            </a>
            @if(Auth::user()->isResident())
            <a href="{{ route('complaints.create') }}" class="{{ request()->routeIs('complaints.create') ? 'active' : '' }}">
                <i class="fas fa-plus-circle"></i> File Complaint
            </a>
            <a href="{{ route('complaints.my') }}" class="{{ request()->routeIs('complaints.my') ? 'active' : '' }}">
                <i class="fas fa-list"></i> My Complaints
            </a>
            <a href="{{ route('profile.edit') }}" class="{{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <i class="fas fa-user"></i> My Profile
            </a>
            @else
            <a href="{{ route('complaints.index') }}" class="{{ request()->routeIs('complaints.index') ? 'active' : '' }}">
                <i class="fas fa-list"></i> Reports
            </a>
            @if(Auth::user()->isMunicipalAdmin())
            <a href="{{ route('complaints.escalated') }}" class="{{ request()->routeIs('complaints.escalated') ? 'active' : '' }}">
                <i class="fas fa-exclamation-triangle"></i> Escalated
            </a>
            <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users-cog"></i> User Management
            </a>
            @endif
            @endif
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </nav>
    </div>

    <div class="sidebar-overlay" onclick="toggleSidebar()"></div>

    <div class="main-content">
        <div class="top-bar">
            <button class="menu-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <h3 class="page-title">@yield('page-title')</h3>
            <div class="user-info">
            </div>
        </div>
        <div class="content-area">
            @yield('content')
        </div>
    </div>
    @else
    @yield('content')
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            document.querySelector('.sidebar').classList.toggle('active');
            document.querySelector('.sidebar-overlay').classList.toggle('active');
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            const sidebar = document.querySelector('.sidebar');
            const toggle = document.querySelector('.menu-toggle');
            const overlay = document.querySelector('.sidebar-overlay');

            if (window.innerWidth <= 768) {
                if (!sidebar.contains(e.target) && !toggle.contains(e.target)) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                }
            }
        });

        // Handle window resize
        window.addEventListener('resize', function() {
            const sidebar = document.querySelector('.sidebar');
            const overlay = document.querySelector('.sidebar-overlay');

            if (window.innerWidth > 768) {
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
            }
        });

        function toggleUserMenu() {
            const menu = document.querySelector('.mobile-user-menu');
            menu.classList.toggle('show');
        }

        // Close user menu when clicking outside
        document.addEventListener('click', function(e) {
            const userInfo = document.querySelector('.user-info');
            const menu = document.querySelector('.mobile-user-menu');
            if (!userInfo.contains(e.target)) {
                menu.classList.remove('show');
            }
        });
    </script>
    @yield('scripts')
</body>
</html>

