<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') — BIDUK {{ $school->name ?? 'SDN 204' }}</title>

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --biduk-primary: #1a7a4c;
            --biduk-primary-dark: #145e3a;
            --biduk-primary-light: #e8f5ee;
            --biduk-primary-gradient: linear-gradient(135deg, #1a7a4c 0%, #2ecc71 100%);
            --biduk-secondary: #16a085;
            --biduk-accent: #f39c12;
            --biduk-bg: #f8faf9;
            --biduk-card-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
            --biduk-transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 80px;
        }

        body {
            background-color: #ffffff;
            color: #1f2937;
            overflow-x: hidden;
        }

        /* ============ NAVBAR ============ */
        .landing-navbar {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            padding: 0.85rem 0;
            transition: var(--biduk-transition);
            z-index: 1040;
        }

        .landing-navbar.scrolled {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 0.6rem 0;
        }

        .brand-logo-wrap {
            width: 46px;
            height: 46px;
            border-radius: 12px;
            background: var(--biduk-primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.35rem;
            font-weight: 700;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(26, 122, 76, 0.2);
            flex-shrink: 0;
        }

        .brand-logo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-name-wrap {
            line-height: 1.15;
        }

        .brand-name-wrap .title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--biduk-primary);
            letter-spacing: -0.02em;
            display: block;
        }

        .brand-name-wrap .subtitle {
            font-size: 0.75rem;
            color: #6b7280;
            font-weight: 500;
            display: block;
        }

        .navbar-nav .nav-link {
            color: #374151;
            font-weight: 500;
            font-size: 0.925rem;
            padding: 0.5rem 0.85rem !important;
            border-radius: 8px;
            transition: var(--biduk-transition);
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--biduk-primary);
            background: var(--biduk-primary-light);
        }

        .dropdown-menu {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            padding: 0.5rem;
            min-width: 220px;
        }

        .dropdown-item {
            border-radius: 8px;
            padding: 0.55rem 0.85rem;
            font-size: 0.875rem;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-weight: 500;
        }

        .dropdown-item:hover {
            background: var(--biduk-primary-light);
            color: var(--biduk-primary);
        }

        /* ============ BUTTONS ============ */
        .btn-biduk-primary {
            background: var(--biduk-primary-gradient);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 10px;
            padding: 0.65rem 1.35rem;
            transition: var(--biduk-transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-biduk-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(26, 122, 76, 0.35);
            color: #fff;
        }

        .btn-biduk-outline {
            background: #ffffff;
            border: 1.5px solid var(--biduk-primary);
            color: var(--biduk-primary);
            font-weight: 600;
            border-radius: 10px;
            padding: 0.65rem 1.35rem;
            transition: var(--biduk-transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-biduk-outline:hover {
            background: var(--biduk-primary-light);
            color: var(--biduk-primary-dark);
            transform: translateY(-2px);
        }

        /* ============ SECTION BASE ============ */
        .landing-section {
            padding: 90px 0;
            position: relative;
        }

        .section-light {
            background-color: #f8faf9;
        }

        .section-header {
            max-width: 720px;
            margin: 0 auto 55px;
            text-align: center;
        }

        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.4rem 0.9rem;
            border-radius: 30px;
            background: var(--biduk-primary-light);
            color: var(--biduk-primary);
            font-size: 0.825rem;
            font-weight: 700;
            margin-bottom: 0.85rem;
            letter-spacing: 0.02em;
            text-transform: uppercase;
        }

        .section-title {
            font-size: 2.25rem;
            font-weight: 800;
            color: #111827;
            letter-spacing: -0.02em;
            margin-bottom: 0.85rem;
        }

        .section-description {
            color: #6b7280;
            font-size: 1.05rem;
            line-height: 1.7;
            margin: 0;
        }

        /* ============ CARDS ============ */
        .biduk-feature-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 1.75rem;
            box-shadow: var(--biduk-card-shadow);
            transition: var(--biduk-transition);
            height: 100%;
        }

        .biduk-feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(26, 122, 76, 0.12);
            border-color: #a7f3d0;
        }

        /* ============ FOOTER ============ */
        .footer-biduk {
            background: #111827;
            color: #9ca3af;
            padding: 70px 0 30px;
            border-top: 4px solid var(--biduk-primary);
        }

        .footer-title {
            color: #ffffff;
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 1.25rem;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 0.65rem;
        }

        .footer-links a {
            color: #9ca3af;
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--biduk-transition);
        }

        .footer-links a:hover {
            color: #34d399;
            padding-left: 4px;
        }

        .footer-bottom {
            margin-top: 50px;
            padding-top: 25px;
            border-top: 1px solid #1f2937;
            text-align: center;
            font-size: 0.85rem;
        }
    </style>
    @stack('styles')
</head>

<body>

    {{-- ==================== NAVBAR ==================== --}}
    <nav class="navbar navbar-expand-lg landing-navbar sticky-top">
        <div class="container">
            {{-- Brand Logo & Name --}}
            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="#beranda">
                <div class="brand-logo-wrap">
                    @if($school->logo && file_exists(public_path('storage/' . $school->logo)))
                        <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo {{ $school->name }}">
                    @else
                        <i class="bi bi-book-half"></i>
                    @endif
                </div>
                <div class="brand-name-wrap">
                    <span class="title">BIDUK</span>
                    <span class="subtitle">{{ $school->name ?? 'SDN 204 Palembang' }}</span>
                </div>
            </a>

            {{-- Mobile Toggle --}}
            <button class="navbar-toggler border-0 shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarLandingContent" aria-controls="navbarLandingContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="bi bi-list fs-2 text-dark"></i>
            </button>

            {{-- Menu Links --}}
            <div class="collapse navbar-collapse" id="navbarLandingContent">
                <ul class="navbar-nav mx-auto align-items-lg-center gap-lg-1 my-3 my-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="#beranda">
                            <i class="bi bi-house-door me-1"></i>Beranda
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-info-circle me-1"></i>Profil Sekolah
                        </a>
                        <ul class="dropdown-menu shadow">
                            <li>
                                <a class="dropdown-item" href="#informasi-sekolah">
                                    <i class="bi bi-building text-success"></i> Informasi Sekolah
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#visi-misi">
                                    <i class="bi bi-bullseye text-success"></i> Visi & Misi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#struktur-organisasi">
                                    <i class="bi bi-diagram-3 text-success"></i> Struktur Organisasi
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#prestasi">
                                    <i class="bi bi-trophy text-success"></i> Prestasi Sekolah
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#ekstrakurikuler">
                                    <i class="bi bi-people text-success"></i> Ekstrakurikuler
                                </a>
                            </li>
                        </ul>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#berita">
                            <i class="bi bi-newspaper me-1"></i>Berita
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="#galeri">
                            <i class="bi bi-images me-1"></i>Galeri
                        </a>
                    </li>
                </ul>

                {{-- Action / Login CTA --}}
                <div class="d-flex align-items-center gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-biduk-primary">
                            <i class="bi bi-grid-1x2-fill"></i>
                            Dashboard Admin
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-biduk-primary">
                            <i class="bi bi-box-arrow-in-right"></i>
                            Login Petugas
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- ==================== MAIN CONTENT ==================== --}}
    @yield('content')

    {{-- ==================== FOOTER ==================== --}}
    <footer class="footer-biduk">
        <div class="container">
            <div class="row g-4 justify-content-between">
                {{-- Col 1: Brand & Desc --}}
                <div class="col-lg-5">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="brand-logo-wrap" style="width: 42px; height: 42px;">
                            @if($school->logo && file_exists(public_path('storage/' . $school->logo)))
                                <img src="{{ asset('storage/' . $school->logo) }}" alt="Logo {{ $school->name }}">
                            @else
                                <i class="bi bi-book-half fs-5"></i>
                            @endif
                        </div>
                        <div>
                            <h5 class="text-white mb-0 fw-bold">BIDUK</h5>
                            <small class="text-secondary">{{ $school->name ?? 'SD Negeri 204 Palembang' }}</small>
                        </div>
                    </div>
                    <p class="text-secondary" style="font-size: 0.9rem; line-height: 1.7;">
                        {{ $school->description ? Str::limit($school->description, 210) : 'Sistem Informasi Buku Induk dan Portal Resmi SD Negeri 204 Palembang dalam mendukung tata kelola pendidikan yang modern, transparan, dan terpercaya.' }}
                    </p>
                </div>

                {{-- Col 2: Navigasi Cepat --}}
                <div class="col-6 col-lg-3">
                    <h6 class="footer-title">Navigasi Profil</h6>
                    <ul class="footer-links">
                        <li><a href="#beranda"><i class="bi bi-chevron-right me-1"></i> Beranda</a></li>
                        <li><a href="#informasi-sekolah"><i class="bi bi-chevron-right me-1"></i> Informasi Sekolah</a></li>
                        <li><a href="#visi-misi"><i class="bi bi-chevron-right me-1"></i> Visi & Misi</a></li>
                        <li><a href="#struktur-organisasi"><i class="bi bi-chevron-right me-1"></i> Struktur Organisasi</a></li>
                        <li><a href="#prestasi"><i class="bi bi-chevron-right me-1"></i> Prestasi & Ekskul</a></li>
                    </ul>
                </div>

                {{-- Col 3: Kontak Resmi --}}
                <div class="col-6 col-lg-4">
                    <h6 class="footer-title">Kontak & Alamat</h6>
                    <div class="d-flex flex-column gap-2 text-secondary" style="font-size: 0.88rem;">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-geo-alt-fill text-success mt-1"></i>
                            <span>{{ $school->address ?? 'Kota Palembang, Sumatera Selatan' }}</span>
                        </div>
                        @if($school->phone)
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-telephone-fill text-success"></i>
                                <span>{{ $school->phone }}</span>
                            </div>
                        @endif
                        @if($school->email)
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-envelope-fill text-success"></i>
                                <span>{{ $school->email }}</span>
                            </div>
                        @endif
                        @if($school->npsn)
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-card-text text-success"></i>
                                <span>NPSN: {{ $school->npsn }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 text-secondary">
                    <span>© {{ date('Y') }} <strong>BIDUK - {{ $school->name ?? 'SDN 204' }}</strong>. Semua Hak Dilindungi.</span>
                    <span class="small">Sistem Informasi Buku Induk Siswa & Profil Sekolah</span>
                </div>
            </div>
        </div>
    </footer>

    {{-- Bootstrap 5 JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Navbar shadow on scroll
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('.landing-navbar');
            if (window.scrollY > 20) {
                nav.classList.add('scrolled');
            } else {
                nav.classList.remove('scrolled');
            }
        });

        // Highlight active navbar link on scroll
        const sections = document.querySelectorAll('section[id]');
        window.addEventListener('scroll', function() {
            const scrollY = window.pageYOffset;
            sections.forEach(current => {
                const sectionHeight = current.offsetHeight;
                const sectionTop = current.offsetTop - 100;
                const sectionId = current.getAttribute('id');
                const link = document.querySelector('.navbar-nav a[href*=' + sectionId + ']');
                if (link && scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                    document.querySelectorAll('.navbar-nav .nav-link').forEach(n => n.classList.remove('active'));
                    link.classList.add('active');
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>