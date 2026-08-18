<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BIDUK') — BIDUK SDN 204</title>

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --biduk-primary: #1a7a4c;
            --biduk-primary-dark: #145e3a;
            --biduk-primary-light: #e8f5ee;
            --biduk-primary-gradient: linear-gradient(135deg, #1a7a4c 0%, #2ecc71 100%);
            --biduk-secondary: #16a085;
            --biduk-accent: #f39c12;
            --biduk-bg: #f0f2f5;
            --biduk-sidebar-width: 260px;
            --biduk-sidebar-collapsed: 0px;
            --biduk-card-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            --biduk-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: var(--biduk-bg);
            min-height: 100vh;
        }

        /* ============ SIDEBAR ============ */
        .biduk-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--biduk-sidebar-width);
            height: 100vh;
            background: var(--biduk-primary-gradient);
            color: #fff;
            z-index: 1050;
            transition: var(--biduk-transition);
            overflow-y: auto;
            overflow-x: hidden;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }

        .biduk-sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .biduk-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        .biduk-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
        }

        .sidebar-brand {
            padding: 1.25rem 1.25rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .sidebar-brand-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            font-weight: 700;
            flex-shrink: 0;
            backdrop-filter: blur(10px);
        }

        .sidebar-brand h5 {
            margin: 0;
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .sidebar-brand small {
            font-size: 0.7rem;
            opacity: 0.7;
            font-weight: 400;
        }

        .sidebar-section {
            padding: 1rem 0.75rem 0.25rem;
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.45);
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav .nav-item {
            margin: 2px 0.5rem;
        }

        .sidebar-nav .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.6rem 0.75rem;
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            transition: var(--biduk-transition);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 400;
        }

        .sidebar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            transform: translateX(2px);
        }

        .sidebar-nav .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .sidebar-nav .nav-link i {
            font-size: 1.1rem;
            width: 1.25rem;
            text-align: center;
            flex-shrink: 0;
        }

        /* ============ NAVBAR ============ */
        .biduk-navbar {
            position: fixed;
            top: 0;
            left: var(--biduk-sidebar-width);
            right: 0;
            height: 64px;
            background: #fff;
            z-index: 1040;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
            transition: var(--biduk-transition);
        }

        .biduk-navbar .navbar-left {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn-sidebar-toggle {
            border: none;
            background: none;
            font-size: 1.25rem;
            color: #6c757d;
            cursor: pointer;
            padding: 0.25rem;
            border-radius: 6px;
            transition: var(--biduk-transition);
            display: none;
        }

        .btn-sidebar-toggle:hover {
            background: var(--biduk-primary-light);
            color: var(--biduk-primary);
        }

        .biduk-navbar .breadcrumb {
            margin: 0;
            font-size: 0.85rem;
        }

        .biduk-navbar .breadcrumb-item a {
            color: var(--biduk-primary);
            text-decoration: none;
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .navbar-user-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--biduk-primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 600;
            font-size: 0.85rem;
        }

        /* ============ MAIN CONTENT ============ */
        .biduk-main {
            margin-left: var(--biduk-sidebar-width);
            padding-top: 64px;
            min-height: 100vh;
            transition: var(--biduk-transition);
        }

        .biduk-content {
            padding: 1.5rem;
        }

        /* ============ CARDS ============ */
        .biduk-card {
            background: #fff;
            border: none;
            border-radius: 12px;
            box-shadow: var(--biduk-card-shadow);
            transition: var(--biduk-transition);
        }

        .biduk-card:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        .biduk-card .card-header {
            background: transparent;
            border-bottom: 1px solid #f0f0f0;
            padding: 1rem 1.25rem;
            font-weight: 600;
        }

        /* ============ FORM / CARD SPACING ============ */
        .biduk-content .biduk-card .card-body {
            padding: 1.35rem 1.45rem;
        }

        .biduk-content form .card-body .row.g-3 {
            --bs-gutter-x: 1rem;
            --bs-gutter-y: 1rem;
        }

        .biduk-content form .card-body .row.g-3>[class*="col"] {
            padding-top: 0.2rem;
        }

        .biduk-content form .form-label {
            display: block;
            margin-bottom: 0.55rem;
            line-height: 1.38;
        }

        .biduk-content form .form-control,
        .biduk-content form .form-select,
        .biduk-content form textarea.form-control {
            min-height: 2.65rem;
            padding: 0.72rem 0.88rem;
            font-size: 0.86rem;
            line-height: 1.55;
        }

        .biduk-content form .form-text,
        .biduk-content form .invalid-feedback {
            font-size: 0.78rem;
            margin-top: 0.38rem;
        }

        .biduk-content form .btn {
            font-size: 0.86rem;
        }

        .biduk-content form .btn+.btn {
            margin-left: 0.45rem;
        }

        /* ============ TABLE ============ */
        .biduk-table {
            font-size: 0.875rem;
        }

        .biduk-table thead th {
            background: var(--biduk-primary-light);
            color: var(--biduk-primary-dark);
            font-weight: 600;
            border: none;
            padding: 0.75rem;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            white-space: nowrap;
        }

        .biduk-table tbody td {
            padding: 0.75rem;
            vertical-align: middle;
            border-bottom: 1px solid #f5f5f5;
        }

        .biduk-table tbody tr:hover {
            background-color: #fafbfc;
        }

        /* ============ BADGES ============ */
        .badge-aktif {
            background: #d4edda;
            color: #155724;
        }

        .badge-pindah {
            background: #fff3cd;
            color: #856404;
        }

        .badge-lulus {
            background: #cce5ff;
            color: #004085;
        }

        .badge-alumni {
            background: #e2e3e5;
            color: #383d41;
        }

        .badge-nonaktif {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-l {
            background: #cce5ff;
            color: #004085;
        }

        .badge-p {
            background: #f8d7da;
            color: #721c24;
        }

        /* ============ BUTTONS ============ */
        .btn-biduk-primary {
            background: var(--biduk-primary-gradient);
            border: none;
            color: #fff;
            font-weight: 500;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: var(--biduk-transition);
            font-size: 0.875rem;
        }

        .btn-biduk-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(26, 122, 76, 0.3);
            color: #fff;
        }

        .btn-biduk-outline {
            background: transparent;
            border: 1.5px solid var(--biduk-primary);
            color: var(--biduk-primary);
            font-weight: 500;
            border-radius: 8px;
            padding: 0.5rem 1rem;
            transition: var(--biduk-transition);
            font-size: 0.875rem;
        }

        .btn-biduk-outline:hover {
            background: var(--biduk-primary);
            color: #fff;
        }

        /* ============ FORM ============ */
        .form-control:focus,
        .form-select:focus {
            border-color: var(--biduk-primary);
            box-shadow: 0 0 0 0.2rem rgba(26, 122, 76, 0.15);
        }

        .form-label {
            font-weight: 500;
            font-size: 0.85rem;
            color: #495057;
        }

        /* ============ PAGINATION ============ */
        .pagination .page-link {
            color: var(--biduk-primary);
            border-radius: 6px;
            margin: 0 2px;
            font-size: 0.85rem;
        }

        .pagination .page-item.active .page-link {
            background: var(--biduk-primary);
            border-color: var(--biduk-primary);
        }

        /* ============ ACTION DROPDOWN ============ */
        .action-dropdown .dropdown-toggle::after {
            display: none;
        }

        .action-dropdown .dropdown-menu {
            border: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            border-radius: 8px;
            padding: 0.5rem;
            font-size: 0.85rem;
        }

        .action-dropdown .dropdown-item {
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .action-dropdown .dropdown-item:hover {
            background: var(--biduk-primary-light);
        }

        .action-dropdown .dropdown-item.text-danger:hover {
            background: #fff5f5;
        }

        /* ============ ALERT ============ */
        .alert {
            border: none;
            border-radius: 10px;
            font-size: 0.875rem;
        }

        /* ============ RESPONSIVE ============ */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1045;
            display: none;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }

        @media (max-width: 991.98px) {
            .biduk-sidebar {
                transform: translateX(-100%);
            }

            .biduk-sidebar.show {
                transform: translateX(0);
            }

            .biduk-navbar {
                left: 0;
            }

            .biduk-main {
                margin-left: 0;
            }

            .btn-sidebar-toggle {
                display: block;
            }
        }

        /* ============ ANIMATIONS ============ */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.4s ease-out;
        }

        /* ============ EMPTY STATE ============ */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #9ca3af;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h5 {
            color: #6b7280;
            font-weight: 600;
        }
    </style>
    @stack('styles')
</head>

<body>
    {{-- Sidebar Overlay (mobile) --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Sidebar --}}
    @include('layouts.sidebar')

    {{-- Navbar --}}
    <nav class="biduk-navbar">
        <div class="navbar-left">
            <button class="btn-sidebar-toggle" id="sidebarToggle">
                <i class="bi bi-list"></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    @yield('breadcrumb')
                </ol>
            </nav>
        </div>
        <div class="navbar-user">
            <div class="navbar-user-avatar">
                <i class="bi bi-person-fill"></i>
            </div>
            <div>
                <div style="font-size: 0.85rem; font-weight: 600; color: #1f2937;">
                    {{ auth()->user()?->name ?? 'Super Admin' }}
                </div>
                <div style="font-size: 0.7rem; color: #9ca3af;">
                    {{ auth()->user()?->role?->name ?? 'Administrator' }}
                </div>
            </div>
        </div>
    </nav>

    {{-- Main Content --}}
    <main class="biduk-main">
        <div class="biduk-content">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show fade-in-up" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show fade-in-up" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    {{-- Bootstrap 5 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar toggle for mobile
        const sidebar = document.querySelector('.biduk-sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                sidebar.classList.toggle('show');
                overlay.classList.toggle('show');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.remove('show');
                overlay.classList.remove('show');
            });
        }

        // Auto-dismiss alerts after 5 seconds
        document.querySelectorAll('.alert-dismissible').forEach(alert => {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, 5000);
        });
    </script>
    @stack('scripts')
</body>

</html>
