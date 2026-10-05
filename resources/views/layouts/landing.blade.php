<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') — BIDUK {{ $school->name ?? 'SDN 204 Palembang' }}</title>

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Bootstrap Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --biduk-primary: #15803d;
            --biduk-primary-dark: #14532d;
            --biduk-primary-light: #f0fdf4;
            --biduk-primary-subtle: #dcfce7;
            --biduk-primary-gradient: linear-gradient(135deg, #15803d 0%, #22c55e 100%);
            --biduk-secondary: #0f766e;
            --biduk-accent: #f59e0b;
            --biduk-bg: #f8fafc;
            --biduk-text-main: #0f172a;
            --biduk-text-muted: #475569;
            --biduk-card-shadow: 0 4px 20px rgba(15, 23, 42, 0.05);
            --biduk-card-hover: 0 16px 36px rgba(21, 128, 61, 0.12);
            --biduk-transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        h1, h2, h3, h4, h5, h6, .hero-title, .section-title, .title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.025em;
        }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 85px;
        }

        body {
            background-color: #ffffff;
            color: var(--biduk-text-main);
            overflow-x: hidden;
        }

        /* ============ SCROLL PROGRESS BAR ============ */
        #scroll-progress {
            position: fixed;
            top: 0;
            left: 0;
            height: 3.5px;
            background: var(--biduk-primary-gradient);
            width: 0%;
            z-index: 1060;
            transition: width 0.1s ease-out;
        }

        /* ============ NAVBAR ============ */
        .landing-navbar {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            padding: 0.9rem 0;
            transition: var(--biduk-transition);
            z-index: 1040;
        }

        .landing-navbar.scrolled {
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
            padding: 0.65rem 0;
            border-bottom-color: rgba(226, 232, 240, 1);
        }

        .brand-logo-wrap {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--biduk-primary-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 1.35rem;
            font-weight: 700;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(21, 128, 61, 0.25);
            flex-shrink: 0;
            transition: var(--biduk-transition);
        }

        .brand-logo-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .brand-name-wrap {
            line-height: 1.2;
        }

        .brand-name-wrap .title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--biduk-primary);
            display: block;
        }

        .brand-name-wrap .subtitle {
            font-size: 0.775rem;
            color: var(--biduk-text-muted);
            font-weight: 500;
            display: block;
        }

        .navbar-nav .nav-link {
            color: #334155;
            font-weight: 500;
            font-size: 0.925rem;
            padding: 0.5rem 0.9rem !important;
            border-radius: 8px;
            transition: var(--biduk-transition);
        }

        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link.active {
            color: var(--biduk-primary);
            background: var(--biduk-primary-light);
        }

        a {
            color: var(--biduk-primary);
            text-decoration: none;
            transition: var(--biduk-transition);
        }

        a:hover, a:focus {
            color: var(--biduk-primary-dark);
        }

        .dropdown-menu {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.1);
            padding: 0.6rem;
            min-width: 240px;
            animation: fadeInDropdown 0.25s ease-out;
        }

        @keyframes fadeInDropdown {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dropdown-item {
            border-radius: 9px;
            padding: 0.65rem 0.95rem;
            font-size: 0.885rem;
            color: #334155;
            display: flex;
            align-items: center;
            gap: 0.65rem;
            font-weight: 500;
            transition: var(--biduk-transition);
        }

        .dropdown-item i {
            color: var(--biduk-primary) !important;
            font-size: 1.05rem;
            transition: var(--biduk-transition);
        }

        .dropdown-item:hover {
            background: var(--biduk-primary-light) !important;
            color: var(--biduk-primary) !important;
            transform: translateX(4px);
        }

        .dropdown-item:hover i {
            color: var(--biduk-primary) !important;
        }

        .dropdown-item:focus, .dropdown-item:active, .dropdown-item.active {
            background: var(--biduk-primary-gradient) !important;
            color: #ffffff !important;
        }

        .dropdown-item:focus i, .dropdown-item:active i, .dropdown-item.active i {
            color: #ffffff !important;
        }

        .dropdown-toggle:focus, .dropdown-toggle:active, .dropdown-toggle.show {
            color: var(--biduk-primary) !important;
            background: var(--biduk-primary-light) !important;
        }

        /* ============ BUTTONS ============ */
        .btn-biduk-primary {
            background: var(--biduk-primary-gradient);
            border: none;
            color: #fff;
            font-weight: 600;
            border-radius: 11px;
            padding: 0.7rem 1.45rem;
            transition: var(--biduk-transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            box-shadow: 0 4px 14px rgba(21, 128, 61, 0.22);
        }

        .btn-biduk-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(21, 128, 61, 0.35);
            color: #fff;
        }

        .btn-biduk-outline {
            background: #ffffff;
            border: 1.5px solid var(--biduk-primary);
            color: var(--biduk-primary);
            font-weight: 600;
            border-radius: 11px;
            padding: 0.7rem 1.45rem;
            transition: var(--biduk-transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        }

        .btn-biduk-outline:hover {
            background: var(--biduk-primary-light);
            color: var(--biduk-primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(21, 128, 61, 0.15);
        }

        /* ============ SECTION BASE ============ */
        .landing-section {
            padding: 95px 0;
            position: relative;
        }

        .section-light {
            background-color: var(--biduk-bg);
        }

        .section-header {
            max-width: 720px;
            margin: 0 auto 55px;
            text-align: center;
        }

        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            padding: 0.42rem 1rem;
            border-radius: 30px;
            background: var(--biduk-primary-light);
            color: var(--biduk-primary);
            border: 1px solid var(--biduk-primary-subtle);
            font-size: 0.825rem;
            font-weight: 700;
            margin-bottom: 0.95rem;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .section-title {
            font-size: 2.35rem;
            font-weight: 800;
            color: var(--biduk-text-main);
            letter-spacing: -0.025em;
            margin-bottom: 0.95rem;
            line-height: 1.25;
        }

        .section-description {
            color: var(--biduk-text-muted);
            font-size: 1.05rem;
            line-height: 1.75;
            margin: 0;
        }

        /* ============ CARDS ============ */
        .biduk-feature-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            padding: 1.85rem;
            box-shadow: var(--biduk-card-shadow);
            transition: var(--biduk-transition);
            height: 100%;
        }

        .biduk-feature-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--biduk-card-hover);
            border-color: #86efac;
        }

        /* ============ FOOTER ============ */
        .footer-biduk {
            background: #090e17;
            color: #94a3b8;
            padding: 75px 0 35px;
            border-top: 4px solid var(--biduk-primary);
            position: relative;
        }

        .footer-title {
            color: #ffffff;
            font-size: 1.05rem;
            font-weight: 700;
            margin-bottom: 1.35rem;
        }

        .footer-links {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .footer-links li {
            margin-bottom: 0.75rem;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.915rem;
            transition: var(--biduk-transition);
            display: inline-block;
        }

        .footer-links a:hover {
            color: #4ade80;
            transform: translateX(4px);
        }

        .footer-bottom {
            margin-top: 55px;
            padding-top: 25px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            text-align: center;
            font-size: 0.865rem;
        }

        /* ============ BIDIRECTIONAL SCROLL REVEAL ANIMATIONS ============ */
        .reveal {
            opacity: 0;
            transform: translateY(35px);
            transition: opacity 0.75s cubic-bezier(0.16, 1, 0.3, 1), transform 0.75s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        .reveal-left {
            opacity: 0;
            transform: translateX(-45px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }

        .reveal-left.active {
            opacity: 1;
            transform: translateX(0);
        }

        .reveal-right {
            opacity: 0;
            transform: translateX(45px);
            transition: opacity 0.8s cubic-bezier(0.16, 1, 0.3, 1), transform 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }

        .reveal-right.active {
            opacity: 1;
            transform: translateX(0);
        }

        .reveal-scale {
            opacity: 0;
            transform: scale(0.93);
            transition: opacity 0.75s cubic-bezier(0.16, 1, 0.3, 1), transform 0.75s cubic-bezier(0.16, 1, 0.3, 1);
            will-change: opacity, transform;
        }

        .reveal-scale.active {
            opacity: 1;
            transform: scale(1);
        }

        .delay-100 { transition-delay: 0.1s; }
        .delay-200 { transition-delay: 0.2s; }
        .delay-300 { transition-delay: 0.3s; }
        .delay-400 { transition-delay: 0.4s; }
        .delay-500 { transition-delay: 0.5s; }

        /* ============ BACK TO TOP BUTTON ============ */
        #btn-back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: var(--biduk-primary);
            color: #ffffff;
            border: none;
            box-shadow: 0 8px 24px rgba(21, 128, 61, 0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            cursor: pointer;
            z-index: 1030;
            opacity: 0;
            visibility: hidden;
            transform: translateY(20px);
            transition: var(--biduk-transition);
        }

        #btn-back-to-top.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        #btn-back-to-top:hover {
            background: var(--biduk-primary-dark);
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 12px 28px rgba(21, 128, 61, 0.45);
        }
    </style>
    @stack('styles')
</head>

<body>

    {{-- Scroll Progress Indicator --}}
    <div id="scroll-progress"></div>

    {{-- ==================== NAVBAR ==================== --}}
    <nav class="landing-navbar navbar navbar-expand-lg sticky-top">
        <div class="container">
            {{-- Brand Logo & Name --}}
            <a class="navbar-brand d-flex align-items-center gap-2 text-decoration-none" href="#beranda">
                <div class="brand-logo-wrap">
                    @if($school->logo_url)
                        <img src="{{ $school->logo_url }}" alt="Logo {{ $school->name }}">
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
                                    <i class="bi bi-building text-success"></i> Identitas Sekolah
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
                                    <i class="bi bi-trophy text-success"></i> Prestasi Siswa & Sekolah
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="#ekstrakurikuler">
                                    <i class="bi bi-people text-success"></i> Kegiatan Ekstrakurikuler
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
                    <a href="{{ route('login') }}" class="btn btn-biduk-primary">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Login
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- ==================== MAIN CONTENT ==================== --}}
    @yield('content')

    {{-- Floating Back to Top Button --}}
    <button id="btn-back-to-top" aria-label="Kembali ke atas">
        <i class="bi bi-arrow-up"></i>
    </button>

    {{-- ==================== FOOTER ==================== --}}
    <footer class="footer-biduk">
        <div class="container">
            <div class="row g-4 justify-content-between">
                {{-- Col 1: Brand & Desc --}}
                <div class="col-lg-5">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <div class="brand-logo-wrap" style="width: 42px; height: 42px;">
                            @if($school->logo_url)
                                <img src="{{ $school->logo_url }}" alt="Logo {{ $school->name }}">
                            @else
                                <i class="bi bi-book-half fs-5"></i>
                            @endif
                        </div>
                        <div>
                            <h5 class="text-white mb-0 fw-bold">BIDUK</h5>
                            <small class="text-secondary">{{ $school->name ?? 'SD Negeri 204 Palembang' }}</small>
                        </div>
                    </div>
                    <p class="text-secondary" style="font-size: 0.9rem; line-height: 1.75;">
                        {{ $school->description ? Str::limit($school->description, 210) : 'Sistem Informasi Buku Induk dan Portal Resmi SD Negeri 204 Palembang dalam mendukung tata kelola pendidikan yang modern, transparan, dan terpercaya.' }}
                    </p>
                </div>

                {{-- Col 2: Navigasi Cepat --}}
                <div class="col-6 col-lg-3">
                    <h6 class="footer-title">Navigasi Profil</h6>
                    <ul class="footer-links">
                        <li><a href="#beranda"><i class="bi bi-chevron-right me-1"></i> Beranda</a></li>
                        <li><a href="#informasi-sekolah"><i class="bi bi-chevron-right me-1"></i> Identitas Sekolah</a></li>
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
        document.addEventListener('DOMContentLoaded', function() {
            // Scroll Progress Indicator
            const progressBar = document.getElementById('scroll-progress');
            window.addEventListener('scroll', function() {
                const winScroll = document.documentElement.scrollTop || document.body.scrollTop;
                const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                const scrolled = (height > 0) ? (winScroll / height) * 100 : 0;
                if (progressBar) progressBar.style.width = scrolled + '%';
            });

            // Navbar shadow & back to top visibility
            const nav = document.querySelector('.landing-navbar');
            const backToTopBtn = document.getElementById('btn-back-to-top');

            window.addEventListener('scroll', function() {
                if (window.scrollY > 25) {
                    nav.classList.add('scrolled');
                } else {
                    nav.classList.remove('scrolled');
                }

                if (window.scrollY > 350) {
                    backToTopBtn.classList.add('show');
                } else {
                    backToTopBtn.classList.remove('show');
                }
            });

            if (backToTopBtn) {
                backToTopBtn.addEventListener('click', function() {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            }

            // Highlight active navbar link on scroll
            const sections = document.querySelectorAll('section[id]');
            window.addEventListener('scroll', function() {
                const scrollY = window.pageYOffset;
                sections.forEach(current => {
                    const sectionHeight = current.offsetHeight;
                    const sectionTop = current.offsetTop - 120;
                    const sectionId = current.getAttribute('id');
                    const link = document.querySelector('.navbar-nav a[href*=' + sectionId + ']');
                    if (link && scrollY > sectionTop && scrollY <= sectionTop + sectionHeight) {
                        document.querySelectorAll('.navbar-nav .nav-link').forEach(n => n.classList.remove('active'));
                        link.classList.add('active');
                    }
                });
            });

            // Bidirectional Scroll Reveal Observer (triggers on scroll down & up)
            const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-scale');
            
            const revealObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('active');
                    } else {
                        // Re-trigger animation when scrolled out and back in
                        if (entry.boundingClientRect.top > 0) {
                            entry.target.classList.remove('active');
                        }
                    }
                });
            }, {
                threshold: 0.12,
                rootMargin: '0px 0px -40px 0px'
            });

            revealElements.forEach(el => revealObserver.observe(el));
        });
    </script>
    @stack('scripts')
</body>

</html>