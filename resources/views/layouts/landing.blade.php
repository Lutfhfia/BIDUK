<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title', 'BIDUK - SDN 204')</title>


    {{-- Bootstrap CSS --}}
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    {{-- Bootstrap Icons --}}
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    >


    <style>

        /* =====================================================
           DASAR
        ===================================================== */

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #20252b;
            background: #ffffff;
            overflow-x: hidden;
        }


        /* =====================================================
           NAVBAR
        ===================================================== */

        .navbar-biduk {
            width: 100%;
            background: #ffffff;
            border-bottom: 1px solid #e8ecea;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 14px 24px;
        }


        /* Logo */

        .brand-biduk {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #178c57;
        }

        .brand-logo {
            width: 62px;
            height: 62px;
            border-radius: 15px;
            background: #178c57;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #ffffff;
            font-size: 30px;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .brand-name {
            font-size: 27px;
            font-weight: 700;
            color: #178c57;
        }

        .brand-school {
            margin-top: 5px;
            font-size: 16px;
            color: #6c757d;
        }


        /* Menu */

        .navbar-menu {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 42px;
        }

        .navbar-menu a,
        .navbar-dropdown-button {
            text-decoration: none;
            color: #30353b;
            font-size: 18px;
            font-weight: 500;
            transition: 0.2s ease;
        }

        .navbar-menu a:hover,
        .navbar-dropdown-button:hover {
            color: #178c57;
        }

        .navbar-menu .active {
            color: #178c57;
        }


        /* Dropdown */

        .navbar-dropdown-button {
            border: none;
            background: transparent;
            padding: 0;
        }

        .navbar-dropdown-button i {
            font-size: 13px;
            margin-left: 5px;
        }

        .dropdown-menu {
            border: 1px solid #e5ebe7;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border-radius: 10px;
            padding: 8px;
        }

        .dropdown-item {
            padding: 10px 14px;
            border-radius: 7px;
        }

        .dropdown-item:hover {
            background: #eaf7f0;
            color: #178c57;
        }


        /* Login */

        .btn-login {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            background: #178c57;
            color: #ffffff;

            padding: 14px 28px;
            border-radius: 10px;

            font-size: 17px;
            font-weight: 700;

            text-decoration: none;

            transition: 0.2s ease;
        }

        .btn-login:hover {
            background: #117448;
            color: #ffffff;
            transform: translateY(-1px);
        }


        /* =====================================================
           CONTAINER LANDING PAGE
        ===================================================== */

        .landing-container {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding-left: 24px;
            padding-right: 24px;
        }


        /* =====================================================
           HERO
        ===================================================== */

        .hero-section {
            width: 100%;
            padding: 70px 0 80px;

            background: linear-gradient(
                135deg,
                #ffffff 0%,
                #f3faf6 100%
            );
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;

            padding: 12px 20px;
            border-radius: 30px;

            background: #e8f7ef;
            color: #178c57;

            font-size: 17px;
            font-weight: 700;
        }

        .hero-title {
            margin-top: 30px;
            margin-bottom: 25px;

            font-size: clamp(48px, 6vw, 78px);
            line-height: 1.05;
            font-weight: 700;
            letter-spacing: -2px;
        }

        .hero-title span {
            display: block;
            color: #178c57;
        }

        .hero-description {
            max-width: 650px;

            margin-bottom: 32px;

            color: #64748b;

            font-size: 20px;
            line-height: 1.8;
        }

        .hero-buttons {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-hero-primary,
        .btn-hero-secondary {
            min-height: 55px;
            padding: 14px 28px;

            border-radius: 10px;

            font-size: 17px;
            font-weight: 700;

            transition: 0.2s ease;
        }

        .btn-hero-primary {
            background: #178c57;
            color: #ffffff;
            border: 2px solid #178c57;
        }

        .btn-hero-primary:hover {
            background: #117448;
            border-color: #117448;
            color: #ffffff;
        }

        .btn-hero-secondary {
            background: #ffffff;
            color: #178c57;
            border: 2px solid #178c57;
        }

        .btn-hero-secondary:hover {
            background: #eaf7f0;
            color: #117448;
        }


        /* =====================================================
           PLACEHOLDER FOTO HERO
        ===================================================== */

        .hero-image-placeholder {
            width: 100%;
            min-height: 500px;

            border: 3px dashed #83cda8;
            border-radius: 30px;

            background:
                radial-gradient(
                    circle at 85% 15%,
                    #dcefe5 0,
                    #dcefe5 100px,
                    transparent 101px
                ),
                radial-gradient(
                    circle at 10% 90%,
                    #dcefe5 0,
                    #dcefe5 100px,
                    transparent 101px
                ),
                #eaf8f1;

            display: flex;
            align-items: center;
            justify-content: center;

            text-align: center;
        }

        .placeholder-content {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .placeholder-icon {
            width: 105px;
            height: 105px;

            border-radius: 25px;

            background: #ffffff;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #178c57;

            font-size: 48px;

            box-shadow: 0 10px 30px rgba(23, 140, 87, 0.10);

            margin-bottom: 25px;
        }

        .placeholder-content h5 {
            margin-bottom: 8px;

            color: #087747;

            font-size: 27px;
            font-weight: 700;
        }

        .placeholder-content p {
            margin: 0;

            color: #64748b;

            font-size: 17px;
        }


        /* =====================================================
           INFORMASI SEKOLAH
        ===================================================== */

        .school-info-section {
            width: 100%;
            padding: 95px 0;

            background: #ffffff;
        }

        .section-heading {
            max-width: 850px;
            margin: 0 auto 60px;
        }

        .section-label {
            display: inline-flex;
            align-items: center;

            color: #178c57;

            font-size: 18px;
            font-weight: 700;
        }

        .section-heading h2 {
            margin-top: 15px;
            margin-bottom: 15px;

            font-size: 48px;
            font-weight: 700;
        }

        .section-heading p {
            margin: 0;

            color: #64748b;

            font-size: 19px;
            line-height: 1.7;
        }


        /* =====================================================
           FOTO INFORMASI SEKOLAH
        ===================================================== */

        .school-info-image {
            width: 100%;
            min-height: 450px;

            border: 3px dashed #83cda8;
            border-radius: 25px;

            background: #f0faf5;

            display: flex;
            align-items: center;
            justify-content: center;

            text-align: center;
        }


        /* =====================================================
           INFORMASI DETAIL SEKOLAH
        ===================================================== */

        .school-info-content {
            padding-left: 20px;
        }

        .school-info-content h3 {
            margin-bottom: 20px;

            font-size: 38px;
            font-weight: 700;
        }
        .profile-section {
            padding: 70px 0;
        }

        .profile-section .section-heading {
            margin-bottom: 50px;
        }

        .school-description {
            margin-bottom: 35px;

            color: #64748b;

            font-size: 18px;
            line-height: 1.8;
        }

        .school-details {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .school-detail-item {
            display: flex;
            align-items: center;
            gap: 18px;

            padding: 18px 20px;

            border: 1px solid #e5eee9;
            border-radius: 15px;

            background: #ffffff;

            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.04);
        }

        .detail-icon {
            flex-shrink: 0;

            width: 50px;
            height: 50px;

            border-radius: 12px;

            background: #eaf7f0;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #178c57;

            font-size: 22px;
        }

        .school-detail-item small {
            display: block;

            margin-bottom: 4px;

            color: #6c757d;

            font-size: 14px;
        }

        .school-detail-item strong {
            display: block;

            color: #20252b;

            font-size: 17px;
        }


        /* =====================================================
           FOOTER
        ===================================================== */

        .footer-biduk {
            padding: 35px 0;

            background: #117448;
            color: #ffffff;

            text-align: center;
        }

        .footer-biduk p {
            margin: 0;
            font-size: 15px;
        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 991px) {

            .navbar-menu {
                gap: 20px;
            }

            .hero-section {
                padding: 55px 0;
            }

            .hero-image-placeholder {
                min-height: 400px;
            }

            .school-info-content {
                padding-left: 0;
            }

        }


        @media (max-width: 768px) {

            .navbar-container {
                padding: 12px 18px;
            }

            .navbar-menu {
                display: none;
            }

            .brand-logo {
                width: 52px;
                height: 52px;
                font-size: 25px;
            }

            .brand-name {
                font-size: 23px;
            }

            .brand-school {
                font-size: 14px;
            }

            .btn-login {
                padding: 11px 18px;
                font-size: 15px;
            }

            .landing-container {
                padding-left: 18px;
                padding-right: 18px;
            }

            .hero-section {
                padding: 50px 0 60px;
            }

            .hero-title {
                font-size: 50px;
                letter-spacing: -1px;
            }

            .hero-description {
                font-size: 17px;
            }

            .hero-image-placeholder {
                min-height: 350px;
            }

            .school-info-section {
                padding: 65px 0;
            }

            .section-heading h2 {
                font-size: 38px;
            }

            .school-info-image {
                min-height: 350px;
            }

            .school-info-content h3 {
                font-size: 32px;
            }

        }
        /* =========================
   NAVBAR BIDUK
========================= */

.navbar {
    min-height: 86px;
}

.navbar-brand {
    text-decoration: none;
}

.brand-logo {
    width: 62px;
    height: 62px;
    background: #168b5b;
    color: white;
    border-radius: 14px;

    display: flex;
    align-items: center;
    justify-content: center;

    font-size: 30px;
}

.brand-text {
    display: flex;
    flex-direction: column;
    line-height: 1.1;
}

.brand-text strong {
    color: #168b5b;
    font-size: 28px;
    font-weight: 700;
}

.brand-text small {
    color: #6c757d;
    font-size: 16px;
    margin-top: 4px;
}


/* Menu */

.navbar .nav-link {
    color: #292d32;
    font-size: 17px;
    font-weight: 500;
    padding: 10px 14px !important;
}

.navbar .nav-link:hover,
.navbar .nav-link.active {
    color: #168b5b;
}


/* Dropdown */

.dropdown-menu {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 10px;
    min-width: 245px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
}

.dropdown-item {
    padding: 11px 14px;
    border-radius: 8px;
    color: #343a40;
}

.dropdown-item:hover {
    background: #eaf7f1;
    color: #168b5b;
}

.dropdown-item i {
    color: #168b5b;
}


/* Login */

.btn-login {
    background: #168b5b;
    color: white;
    border-radius: 10px;
    padding: 12px 25px;
    font-weight: 600;
    border: none;
}

.btn-login:hover {
    background: #11754c;
    color: white;
}
/* =====================================================
   JARAK ANTAR SECTION
===================================================== */

.profile-section {
    width: 100%;
    padding: 100px 0;
}

.profile-section .section-heading {
    margin-bottom: 70px;
}

.section-light {
    background: #f8fbf9;
}
    </style>
    

    @stack('styles')

</head>


<body>


    {{-- =====================================================
         NAVBAR
    ====================================================== --}}

    <nav class="navbar navbar-expand-lg bg-white shadow-sm sticky-top">
    <div class="container">

        {{-- Logo / Nama Sekolah --}}
        <a class="navbar-brand d-flex align-items-center gap-2" href="#beranda">

            {{-- Logo sementara --}}
            <div class="brand-logo">
                <i class="bi bi-building"></i>
            </div>

            <div class="brand-text">
                <strong>BIDUK</strong>
                <small>SDN 204</small>
            </div>

        </a>


        {{-- Tombol Mobile --}}
        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarBIDUK"
                aria-controls="navbarBIDUK"
                aria-expanded="false"
                aria-label="Toggle navigation">

            <span class="navbar-toggler-icon"></span>

        </button>


        {{-- Menu --}}
        <div class="collapse navbar-collapse" id="navbarBIDUK">

            <ul class="navbar-nav mx-auto align-items-lg-center gap-lg-3">

                {{-- Beranda --}}
                <li class="nav-item">
                    <a class="nav-link active" href="#beranda">
                        Beranda
                    </a>
                </li>


                {{-- Profil Sekolah --}}
                <li class="nav-item dropdown">

                    <a class="nav-link dropdown-toggle"
                       href="#"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">

                        Profil Sekolah

                    </a>


                    <ul class="dropdown-menu">

                        <li>
                            <a class="dropdown-item" href="#informasi-sekolah">
                                <i class="bi bi-building me-2"></i>
                                Informasi Sekolah
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#visi-misi">
                                <i class="bi bi-bullseye me-2"></i>
                                Visi & Misi
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#struktur-organisasi">
                                <i class="bi bi-diagram-3 me-2"></i>
                                Struktur Organisasi
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#prestasi">
                                <i class="bi bi-trophy me-2"></i>
                                Prestasi Sekolah
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="#ekstrakurikuler">
                                <i class="bi bi-people me-2"></i>
                                Ekstrakurikuler
                            </a>
                        </li>

                    </ul>

                </li>


                {{-- Berita --}}
                <li class="nav-item">
                    <a class="nav-link" href="#berita">
                        Berita
                    </a>
                </li>


                {{-- Galeri --}}
                <li class="nav-item">
                    <a class="nav-link" href="#galeri">
                        Galeri
                    </a>
                </li>

            </ul>


            {{-- Login --}}
            <div class="d-flex">

                <a href="{{ url('/login') }}" class="btn btn-login">
                    <i class="bi bi-box-arrow-in-right me-2"></i>
                    Login
                </a>

            </div>

        </div>

    </div>
</nav>


    {{-- =====================================================
         CONTENT
    ====================================================== --}}

    @yield('content')


    {{-- =====================================================
         FOOTER
    ====================================================== --}}

    <footer class="footer-biduk">

        <div class="landing-container">

            <p>
                © {{ date('Y') }} BIDUK - SDN 204.
                Semua hak dilindungi.
            </p>

        </div>

    </footer>


    {{-- Bootstrap JS --}}
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js">
    </script>


    @stack('scripts')

</body>

</html>