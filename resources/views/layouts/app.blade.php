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

.school-logo {
    width: 36px;
    height: 36px;
    object-fit: contain;
    border-radius: 6px;
    display: block;
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

        .biduk-content > .container-fluid,
        .biduk-content > .fade-in-up > .container-fluid {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }

        .biduk-content > :first-child {
            margin-top: 0 !important;
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
            background-color: #fff;
            border-radius: 6px;
            margin: 0 2px;
            font-size: 0.85rem;
        }

        .pagination .page-item.active .page-link {
            color: #fff !important;
            background-color: var(--biduk-primary);
            border-color: var(--biduk-primary);
        }

        .pagination .page-item.active .page-link:hover {
            color: #fff !important;
        }

        /* ============ ACTION DROPDOWN ============ */
        .action-dropdown .dropdown-toggle::after {
            display: none;
        }

        .master-action-dropdown .action-menu-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 34px;
            height: 34px;
            padding: 0;
            border-radius: 8px;
            color: var(--biduk-primary);
        }

        .master-action-dropdown .action-menu-toggle:hover,
        .master-action-dropdown .action-menu-toggle[aria-expanded="true"] {
            background: var(--biduk-primary-light);
            color: var(--biduk-primary-dark);
        }

        .master-action-dropdown .action-menu-toggle:focus-visible {
            border-color: var(--biduk-primary);
            box-shadow: 0 0 0 0.2rem rgba(26, 122, 76, 0.18);
        }

        .action-dropdown .dropdown-menu {
            border: none;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
            border-radius: 8px;
            padding: 0.5rem;
            font-size: 0.85rem;
        }

        .master-action-dropdown .dropdown-menu {
            min-width: 10rem;
            border: 1px solid #e7eeea;
            box-shadow: 0 10px 28px rgba(15, 45, 32, 0.12);
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

        .master-action-dropdown .dropdown-item:hover {
            color: var(--biduk-primary-dark);
            background: var(--biduk-primary-light);
        }

        .master-action-dropdown .dropdown-item:focus,
        .master-action-dropdown .dropdown-item:active {
            color: var(--biduk-primary-dark);
            background: var(--biduk-primary-light);
        }

        .master-action-dropdown .dropdown-item.text-danger:hover,
        .master-action-dropdown .dropdown-item.text-danger:focus,
        .master-action-dropdown .dropdown-item.text-danger:active {
            color: #bb2d3b;
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

            .biduk-content {
                padding: 1rem;
            }
        }

        @media (max-width: 767.98px) {
            .biduk-navbar {
                height: 58px;
                padding: 0 0.75rem;
            }

            .biduk-navbar .navbar-left {
                flex: 1;
                min-width: 0;
                gap: 0.5rem;
            }

            .biduk-navbar .navbar-left > nav {
                min-width: 0;
                overflow: hidden;
            }

            .biduk-navbar .breadcrumb {
                display: flex;
                flex-wrap: nowrap;
                overflow: hidden;
                font-size: 0.75rem;
                white-space: nowrap;
            }

            .biduk-navbar .breadcrumb-item:not(:last-child) {
                display: none;
            }

            .biduk-navbar .breadcrumb-item:last-child {
                max-width: 100%;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .biduk-navbar .breadcrumb-item:last-child::before {
                display: none;
            }

            .biduk-navbar > .dropdown {
                flex-shrink: 0;
            }

            .navbar-user {
                gap: 0.4rem;
            }

            .navbar-user-avatar {
                width: 32px;
                height: 32px;
            }

            .biduk-main {
                padding-top: 58px;
            }

            .biduk-content {
                padding: 0.75rem;
            }

            .biduk-content .biduk-card,
            .biduk-content .card {
                border-radius: 10px;
            }

            .biduk-content .biduk-card .card-header,
            .biduk-content .card > .card-header {
                padding: 0.7rem 0.85rem;
            }

            .biduk-content .biduk-card .card-body,
            .biduk-content .card > .card-body {
                padding: 0.85rem;
            }

            .biduk-content .biduk-card .card-footer,
            .biduk-content .card > .card-footer {
                padding: 0.7rem 0.85rem;
            }

            .biduk-content .row {
                --bs-gutter-x: 0.75rem;
                --bs-gutter-y: 0.75rem;
            }

            .biduk-content .table-responsive {
                max-width: 100%;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .biduk-content .table-responsive > table {
                min-width: 600px;
            }

            .biduk-content .table-responsive > table.table-sm,
            .biduk-content .table-responsive > table[data-mobile-compact] {
                min-width: 0;
            }

            .biduk-content .btn,
            .biduk-content form .btn {
                min-height: 38px;
            }

            .biduk-content .pagination {
                flex-wrap: wrap;
                row-gap: 0.35rem;
            }
        }

        @media (max-width: 575.98px) {
            .biduk-navbar .navbar-user .text-start {
                display: none;
            }

            .biduk-content .page-header,
            .biduk-content .d-flex.justify-content-between.align-items-center {
                flex-wrap: wrap;
                row-gap: 0.65rem;
            }

            .biduk-content form .card-body .row.g-3 {
                --bs-gutter-x: 0.65rem;
                --bs-gutter-y: 0.65rem;
            }

            .biduk-content form .form-control,
            .biduk-content form .form-select,
            .biduk-content form textarea.form-control {
                min-height: 2.5rem;
                padding: 0.6rem 0.7rem;
                font-size: 0.84rem;
            }

            .biduk-content .table-responsive > table {
                min-width: 520px;
            }

            .biduk-content .table-responsive > table.table-sm,
            .biduk-content .table-responsive > table[data-mobile-compact] {
                min-width: 0;
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

<style>
    /* Menu profil header BIDUK */
    .dropdown-menu[aria-labelledby="navbarUserDropdown"] {
        padding: 6px;
        border: 1px solid #dceee3 !important;
        border-radius: 12px !important;
        box-shadow: 0 8px 24px rgba(27, 54, 37, .12) !important;
    }

    /* Highlight menu saat disorot */
    .dropdown-menu[aria-labelledby="navbarUserDropdown"] .dropdown-item {
        padding: 9px 11px;
        border-radius: 7px;
        transition: background-color .18s ease, color .18s ease;
    }

    .dropdown-menu[aria-labelledby="navbarUserDropdown"] .dropdown-item:hover,
    .dropdown-menu[aria-labelledby="navbarUserDropdown"] .dropdown-item:focus {
        background-color: #e5f5eb !important;
        color: #106b40 !important;
    }

    /* Tombol Keluar juga hijau, bukan merah */
    .dropdown-menu[aria-labelledby="navbarUserDropdown"] .dropdown-item.text-danger {
        color: #16834f !important;
    }

    .dropdown-menu[aria-labelledby="navbarUserDropdown"] .dropdown-item.text-danger:hover,
    .dropdown-menu[aria-labelledby="navbarUserDropdown"] .dropdown-item.text-danger:focus {
        background-color: #e5f5eb !important;
        color: #106b40 !important;
    }

    /* Garis pembatas menu */
    .dropdown-menu[aria-labelledby="navbarUserDropdown"] .dropdown-divider {
        border-color: #dceee3;
    }

    /* Fokus tombol profil */
    #navbarUserDropdown:focus-visible {
        outline: 2px solid #16834f;
        outline-offset: 2px;
        box-shadow: 0 0 0 3px rgba(22, 131, 79, .15);
    }
</style>

</head>

<body>
    {{-- Sidebar Overlay (mobile) --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    {{-- Sidebar --}}
    @include('layouts.sidebar')

    {{-- Navbar --}}
    <nav class="biduk-navbar">
        <div class="navbar-left">
            <button
                type="button"
                class="btn-sidebar-toggle"
                id="sidebarToggle"
                aria-label="Buka menu navigasi"
                aria-controls="sidebar"
                aria-expanded="false"
            >
                <i class="bi bi-list"></i>
            </button>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    @php
                        $breadcrumbRoutes = [
                            'academic-years' => ['Akademik', 'Tahun Ajaran', 'academic-years.index'],
                            'semesters' => ['Master Data', 'Semester', 'semesters.index'],
                            'students' => ['Master Data', 'Data Siswa', 'students.index'],
                            'classes' => ['Master Data', 'Data Kelas', 'classes.index'],
                            'subjects' => ['Akademik', 'Mata Pelajaran', 'subjects.index'],
                            'pegawai' => ['Master Data', 'Data Pegawai', 'pegawai.index'],
                            'rekap-absensi' => ['Laporan', 'Rekap Absensi', 'rekap-absensi.index'],
                            'rekap-rapot' => ['Laporan', 'Rekap Rapot', 'rekap-rapot.index'],
                            'achievement-reports' => ['Laporan', 'Rekap Prestasi', 'achievement-reports.index'],
                            'buku-induk' => ['Laporan', 'Buku Induk', 'buku-induk.index'],
                            'report-card-grades' => ['Akademik', 'Nilai Rapot', 'report-card-grades.index'],
                            'final-grades' => ['Akademik', 'Nilai Akhir / Ijazah', null],
                            'users' => ['Manajemen', 'Manajemen User', 'users.index'],
                            'role-permissions' => ['Manajemen', 'Hak Akses', 'role-permissions.index'],
                            'school-profile' => ['Pengaturan', 'Profil Sekolah', 'school-profile.index'],
                            'activity-logs' => ['Administrasi', 'Log Aktivitas', 'activity-logs.index'],
                            'profile' => ['Akun', 'Profil Saya', 'profile.show'],
                        ];
                        $currentRouteName = request()->route()?->getName() ?? '';
                        $routePrefix = explode('.', $currentRouteName)[0];
                        $breadcrumbRoute = $breadcrumbRoutes[$routePrefix] ?? null;
                        $pageTitle = trim(strip_tags($__env->yieldContent('title')));
                        if ($pageTitle === '' && $breadcrumbRoute) {
                            $routeAction = substr($currentRouteName, strlen($routePrefix) + 1);
                            $pageTitle = match ($routeAction) {
                                'create' => 'Tambah ' . $breadcrumbRoute[1],
                                'edit' => 'Edit ' . $breadcrumbRoute[1],
                                'show' => 'Detail ' . $breadcrumbRoute[1],
                                default => $breadcrumbRoute[1],
                            };
                        }
                        $pageTitle = $pageTitle !== '' ? $pageTitle : 'Halaman';
                    @endphp

                    @if ($breadcrumbRoute && $routePrefix !== 'dashboard')
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">Dashboard</a>
                        </li>
                        <li class="breadcrumb-item">{{ $breadcrumbRoute[0] }}</li>
                        @if (strcasecmp($pageTitle, $breadcrumbRoute[1]) === 0)
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ $breadcrumbRoute[1] }}
                            </li>
                        @else
                            <li class="breadcrumb-item">
                                @if (!empty($breadcrumbRoute[2]) && Route::has($breadcrumbRoute[2]))
                                    <a href="{{ route($breadcrumbRoute[2]) }}">{{ $breadcrumbRoute[1] }}</a>
                                @else
                                    {{ $breadcrumbRoute[1] }}
                                @endif
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                {{ $pageTitle }}
                            </li>
                        @endif
                    @elseif ($__env->hasSection('breadcrumb'))
                        @yield('breadcrumb')
                    @else
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ $pageTitle }}
                        </li>
                    @endif
                </ol>
            </nav>
        </div>

<div class="dropdown">
    <button
        type="button"
        class="navbar-user dropdown-toggle"
        id="navbarUserDropdown"
        data-bs-toggle="dropdown"
        aria-expanded="false"
        aria-label="Buka menu profil pengguna"
        style="border: 0; background: transparent; padding: 0; cursor: pointer;"
    >
    <div class="navbar-user-avatar" style="overflow: hidden;">
    @if (auth()->user()?->profile_photo_path)
        <img
            src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}"
            alt="Foto Profil"
            style="width: 100%; height: 100%; object-fit: cover;"
        >
    @else
        <i class="bi bi-person-fill"></i>
    @endif
</div>

        <div class="text-start">
            <div style="font-size: 0.85rem; font-weight: 600; color: #1f2937;">
                {{ auth()->user()?->name ?? 'Pengguna BIDUK' }}
            </div>

            <div style="font-size: 0.7rem; color: #9ca3af;">
                {{ auth()->user()?->role?->name ?? 'Pengguna' }}
            </div>
        </div>
    </button>

    <ul
        class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2"
        aria-labelledby="navbarUserDropdown"
        style="min-width: 200px; border-radius: 10px;"
    >
        <li>
            <a class="dropdown-item py-2" href="{{ route('profile.show') }}">
                <i class="bi bi-person me-2"></i>
                Lihat Profil
            </a>
        </li>

        <li>
            <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                <i class="bi bi-pencil-square me-2"></i>
                Edit Profil
            </a>
        </li>

<li>
    <hr class="dropdown-divider my-1">
</li>

<li>
    <form action="{{ route('logout') }}" method="POST" class="m-0">
        @csrf

        <button type="submit"
                class="dropdown-item py-2 text-danger w-100 text-start">
            <i class="bi bi-box-arrow-right me-2"></i>
            Keluar
        </button>
    </form>
</li>

    </ul>
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
        (() => {
            const actionDropdownSelector = '.master-action-dropdown';
            const viewportPadding = 8;
            const gap = 4;

            const positionActionMenu = (toggle) => {
                const dropdown = toggle.closest(actionDropdownSelector);
                const menu = dropdown?.querySelector('.dropdown-menu');

                if (!menu || !menu.classList.contains('show')) return;

                menu.style.position = 'fixed';
                menu.style.inset = 'auto';
                menu.style.top = '0';
                menu.style.left = '0';
                menu.style.transform = 'none';
                menu.style.margin = '0';
                menu.style.maxHeight = `calc(100vh - ${viewportPadding * 2}px)`;
                menu.style.overflowY = 'auto';

                const toggleRect = toggle.getBoundingClientRect();
                const menuRect = menu.getBoundingClientRect();
                const belowTop = toggleRect.bottom + gap;
                const aboveTop = toggleRect.top - menuRect.height - gap;
                const opensBelow = belowTop + menuRect.height <= window.innerHeight - viewportPadding
                    || aboveTop < viewportPadding;
                const top = opensBelow
                    ? Math.min(belowTop, window.innerHeight - viewportPadding - menuRect.height)
                    : aboveTop;
                const maximumLeft = Math.max(
                    viewportPadding,
                    window.innerWidth - menuRect.width - viewportPadding
                );
                const left = Math.min(
                    Math.max(viewportPadding, toggleRect.right - menuRect.width),
                    maximumLeft
                );

                menu.style.top = `${Math.max(viewportPadding, top)}px`;
                menu.style.left = `${left}px`;
            };

            const positionOpenActionMenus = () => {
                document.querySelectorAll(`${actionDropdownSelector} .dropdown-menu.show`).forEach((menu) => {
                    const toggle = menu.closest(actionDropdownSelector)
                        ?.querySelector('[data-bs-toggle="dropdown"]');
                    if (toggle) positionActionMenu(toggle);
                });
            };

            document.addEventListener('shown.bs.dropdown', (event) => {
                if (event.target.closest(actionDropdownSelector)) {
                    positionActionMenu(event.target);
                }
            });

            document.addEventListener('hidden.bs.dropdown', (event) => {
                const dropdown = event.target.closest(actionDropdownSelector);
                const menu = dropdown?.querySelector('.dropdown-menu');
                if (menu) menu.removeAttribute('style');
            });

            window.addEventListener('resize', positionOpenActionMenus);
            window.addEventListener('scroll', positionOpenActionMenus, true);
        })();
    </script>

    <script>
        // Sidebar toggle for mobile
        const sidebar = document.querySelector('.biduk-sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');

        const closeSidebar = () => {
            if (!sidebar || !overlay || !toggleBtn) return;
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            toggleBtn.setAttribute('aria-expanded', 'false');
        };

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                if (!sidebar || !overlay) return;
                const isOpen = sidebar.classList.toggle('show');
                overlay.classList.toggle('show', isOpen);
                toggleBtn.setAttribute('aria-expanded', String(isOpen));
            });
        }

        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }

        if (sidebar) {
            sidebar.addEventListener('click', (event) => {
                if (event.target.closest('a')) closeSidebar();
            });
        }

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeSidebar();
        });

        // Auto-dismiss alerts after 5 seconds
        document.querySelectorAll('.alert-dismissible').forEach(alert => {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, 5000);
        });
    </script>
    @stack('scripts')
    @include('components.page-loader')
</body>

</html>
