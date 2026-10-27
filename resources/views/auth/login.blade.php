
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login | BIDUK - SDN 204 Palembang</title>

    <style>
        :root {
            --green-dark: #075e42;
            --green-main: #168653;
            --green-light: #22b573;
            --green-pale: #eaf8ef;
            --text-dark: #142f43;
            --text-muted: #718196;
            --border: #dce6e4;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            min-height: 100%;
        }

        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 36px 24px;
            font-family: "Inter", "Segoe UI", Arial, sans-serif;
            color: var(--text-dark);
            background:
                radial-gradient(
                    circle at 5% 10%,
                    rgba(34, 181, 115, 0.11),
                    transparent 28%
                ),
                radial-gradient(
                    circle at 95% 90%,
                    rgba(20, 150, 107, 0.10),
                    transparent 30%
                ),
                #f3f8f5;
        }

        button,
        input {
            font: inherit;
        }

        .login-wrapper {
            width: 100%;
            max-width: 1210px;
            min-height: 760px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            overflow: hidden;
            background: #ffffff;
            border: 1px solid rgba(220, 230, 228, 0.85);
            border-radius: 27px;
            box-shadow:
                0 28px 75px rgba(22, 67, 49, 0.12),
                0 5px 18px rgba(22, 67, 49, 0.04);
        }

        /* ========================================
           PANEL KIRI: IDENTITAS BIDUK
        ======================================== */

        .brand-panel {
            position: relative;
            isolation: isolate;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 56px 52px 38px;
            color: #ffffff;
            background:
                radial-gradient(
                    ellipse at 92% 10%,
                    rgba(109, 232, 158, 0.35),
                    transparent 38%
                ),
                linear-gradient(
                    145deg,
                    #075c40 0%,
                    #087c50 54%,
                    #087664 100%
                );
        }

        .brand-panel::before {
            content: "";
            position: absolute;
            z-index: -1;
            width: 430px;
            height: 430px;
            top: -190px;
            right: -140px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 50%;
            box-shadow:
                0 0 0 35px rgba(255, 255, 255, 0.035),
                0 0 0 75px rgba(255, 255, 255, 0.025);
        }

        .brand-panel::after {
            content: "";
            position: absolute;
            z-index: -1;
            bottom: -85px;
            left: -40px;
            width: 120%;
            height: 245px;
            background: linear-gradient(
                135deg,
                rgba(62, 218, 124, 0.40),
                rgba(1, 89, 73, 0.18)
            );
            border-radius: 48% 52% 0 0 / 23% 35% 0 0;
            transform: rotate(-5deg);
        }

        .brand-top,
        .brand-bottom {
            position: relative;
            z-index: 2;
        }

        .brand-logo {
            width: 68px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 28px;
            padding: 5px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.72);
            border-radius: 17px;
            background: #ffffff;
            box-shadow: 0 9px 25px rgba(0, 0, 0, 0.12);
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .brand-logo-fallback {
            font-size: 34px;
            font-weight: 800;
            color: var(--green-main);
        }

        .brand-title {
            margin-bottom: 2px;
            font-size: clamp(42px, 4vw, 53px);
            font-weight: 800;
            letter-spacing: -2px;
            line-height: 1.2;
        }

        .brand-tagline {
            margin-top: 7px;
            font-size: 19px;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.96);
        }

        .school-name {
            margin-top: 8px;
            font-size: 15px;
            color: rgba(255, 255, 255, 0.78);
            letter-spacing: 0.2px;
        }

        .brand-divider {
            width: 55px;
            height: 4px;
            margin: 32px 0 27px;
            border-radius: 10px;
            background: #ffffff;
        }

        .brand-description {
            max-width: 360px;
            font-size: 15px;
            line-height: 1.85;
            color: rgba(255, 255, 255, 0.88);
        }

        /* Ilustrasi gedung dekoratif tanpa gambar eksternal */

        .school-illustration {
            position: absolute;
            z-index: 0;
            right: -15px;
            bottom: 100px;
            width: 100%;
            height: 260px;
            opacity: 0.12;
            color: #ffffff;
            pointer-events: none;
        }

        .brand-features {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 15px;
            margin-top: 45px;
        }

        .feature-item {
            min-width: 0;
            text-align: left;
        }

        .feature-icon {
            width: 46px;
            height: 46px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            border: 1px solid rgba(255, 255, 255, 0.20);
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.13);
            color: #ffffff;
        }

        .feature-icon svg {
            width: 22px;
            height: 22px;
        }

        .feature-text {
            font-size: 12px;
            font-weight: 600;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.93);
        }

        .brand-bottom {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-top: 35px;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.74);
        }

        .brand-bottom strong {
            color: #ffffff;
            font-weight: 600;
        }

        /* ========================================
           PANEL KANAN: FORM LOGIN
        ======================================== */

        .form-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 64px 67px;
            background:
                radial-gradient(
                    circle at 100% 0%,
                    rgba(236, 253, 243, 0.72),
                    transparent 32%
                ),
                #ffffff;
        }

        .form-inner {
            width: 100%;
            max-width: 455px;
            margin: auto;
        }

        .form-eyebrow {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 25px;
            color: var(--green-main);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 1.4px;
            text-transform: uppercase;
        }

        .eyebrow-line {
            width: 46px;
            height: 4px;
            flex-shrink: 0;
            border-radius: 8px;
            background: linear-gradient(
                90deg,
                #6bd18a,
                #168653
            );
        }

        .form-header {
            margin-bottom: 34px;
        }

        .form-header h2 {
            margin-bottom: 12px;
            font-size: clamp(25px, 2.8vw, 31px);
            font-weight: 800;
            letter-spacing: -0.8px;
            line-height: 1.3;
            color: var(--text-dark);
        }

        .form-header p {
            max-width: 390px;
            font-size: 14px;
            line-height: 1.8;
            color: var(--text-muted);
        }

        .alert-error {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 23px;
            padding: 13px 15px;
            border: 1px solid #fecaca;
            border-radius: 12px;
            background: #fff5f5;
            color: #b42318;
            font-size: 13px;
            line-height: 1.6;
        }

        .alert-error svg {
            width: 19px;
            height: 19px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .form-group {
            margin-bottom: 23px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 700;
            color: #23374a;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 21px;
            height: 21px;
            color: #8293a4;
            transform: translateY(-50%);
            pointer-events: none;
            transition: color 0.2s ease;
        }

        .input-icon svg {
            width: 21px;
            height: 21px;
        }

        .input-wrapper:focus-within .input-icon {
            color: var(--green-main);
        }

        .form-control {
            width: 100%;
            height: 58px;
            padding: 0 17px 0 51px;
            border: 1px solid #d5e0e3;
            border-radius: 13px;
            outline: none;
            background: #fbfdfc;
            color: #19364a;
            font-size: 14px;
            box-shadow: inset 0 1px 2px rgba(15, 40, 30, 0.02);
            transition:
                border-color 0.22s ease,
                box-shadow 0.22s ease,
                background 0.22s ease;
        }

        .form-control.password-input {
            padding-right: 53px;
        }

        .form-control::placeholder {
            color: #96a3b1;
        }

        .form-control:hover {
            border-color: #b4cfc0;
        }

        .form-control:focus {
            border-color: #37aa70;
            background: #ffffff;
            box-shadow:
                0 0 0 4px rgba(34, 181, 115, 0.12),
                0 3px 10px rgba(22, 134, 83, 0.04);
        }

        .toggle-password {
            position: absolute;
            top: 50%;
            right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 35px;
            height: 35px;
            border: none;
            border-radius: 9px;
            background: transparent;
            color: #758697;
            cursor: pointer;
            transform: translateY(-50%);
            transition: background 0.2s ease, color 0.2s ease;
        }

        .toggle-password:hover {
            color: var(--green-main);
            background: var(--green-pale);
        }

        .toggle-password:focus-visible {
            outline: 2px solid var(--green-main);
            outline-offset: 2px;
        }

        .toggle-password svg {
            width: 20px;
            height: 20px;
        }

        /* ========================================
           INGAT SAYA & LUPA PASSWORD
        ======================================== */

        .login-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: 2px;
            margin-bottom: 28px;
        }

        .remember {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            font-size: 13px;
            color: #708093;
            cursor: pointer;
        }

        .remember input {
            width: 18px;
            height: 18px;
            margin: 0;
            accent-color: var(--green-main);
            cursor: pointer;
        }

        .forgot {
            color: var(--green-main);
            font-size: 13px;
            font-weight: 700;
            text-align: right;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .forgot:hover {
            color: var(--green-dark);
            text-decoration: underline;
        }

        /* ========================================
           TOMBOL
        ======================================== */

        .btn-login {
            width: 100%;
            min-height: 59px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            border: none;
            border-radius: 15px;
            background: linear-gradient(
                110deg,
                #31a766 0%,
                #168f66 60%,
                #078f83 100%
            );
            color: #ffffff;
            font-size: 15px;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 9px 20px rgba(22, 134, 83, 0.20);
            transition:
                transform 0.22s ease,
                box-shadow 0.22s ease,
                filter 0.22s ease;
        }

        .btn-login svg {
            width: 20px;
            height: 20px;
        }

        .btn-login:hover {
            filter: brightness(1.04);
            transform: translateY(-2px);
            box-shadow: 0 13px 25px rgba(22, 134, 83, 0.26);
        }

        .btn-login:active {
            transform: translateY(0);
            box-shadow: 0 4px 12px rgba(22, 134, 83, 0.17);
        }

        .btn-login:focus-visible,
        .btn-back:focus-visible {
            outline: 3px solid rgba(34, 181, 115, 0.35);
            outline-offset: 3px;
        }

        .separator {
            display: flex;
            align-items: center;
            gap: 14px;
            margin: 25px 0 20px;
            color: #7d8b9a;
            font-size: 13px;
        }

        .separator::before,
        .separator::after {
            content: "";
            height: 1px;
            flex: 1;
            background: #dce5e5;
        }

        .btn-back {
            width: 100%;
            min-height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 12px 16px;
            border: 1px solid #d7e2e4;
            border-radius: 14px;
            background: #ffffff;
            color: #4c6072;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            transition:
                background 0.2s ease,
                border-color 0.2s ease,
                color 0.2s ease,
                transform 0.2s ease;
        }

        .btn-back svg {
            width: 19px;
            height: 19px;
        }

        .btn-back:hover {
            border-color: #80c9a0;
            background: #f2fbf5;
            color: var(--green-dark);
            transform: translateY(-1px);
        }

        .form-footer {
            margin-top: 35px;
            padding-top: 22px;
            border-top: 1px solid #e5eceb;
            color: #8a9aaa;
            font-size: 12px;
            text-align: center;
            line-height: 1.7;
        }

        .form-footer strong {
            color: #617587;
            font-weight: 700;
        }

        /* ========================================
           RESPONSIVE TABLET
        ======================================== */

        @media (max-width: 1050px) {
            body {
                padding: 26px 18px;
            }

            .login-wrapper {
                max-width: 940px;
                min-height: 690px;
            }

            .brand-panel {
                padding: 46px 36px 32px;
            }

            .form-panel {
                padding: 48px 36px;
            }

            .brand-title {
                font-size: 44px;
            }

            .brand-features {
                gap: 10px;
            }

            .feature-text {
                font-size: 11px;
            }
        }

        /* ========================================
           RESPONSIVE MOBILE
        ======================================== */

        @media (max-width: 760px) {
            body {
                align-items: flex-start;
                padding: 18px 12px;
            }

            .login-wrapper {
                max-width: 510px;
                min-height: auto;
                grid-template-columns: 1fr;
                border-radius: 22px;
            }

            .brand-panel {
                min-height: auto;
                padding: 31px 30px 27px;
            }

            .brand-logo {
                width: 57px;
                height: 57px;
                margin-bottom: 16px;
                border-radius: 14px;
            }

            .brand-title {
                font-size: 35px;
                letter-spacing: -1px;
            }

            .brand-tagline {
                font-size: 16px;
            }

            .school-name {
                font-size: 13px;
            }

            .brand-divider {
                margin: 22px 0 18px;
            }

            .brand-description {
                max-width: 100%;
                font-size: 13px;
                line-height: 1.7;
            }

            .brand-features {
                margin-top: 25px;
                gap: 12px;
            }

            .feature-icon {
                width: 38px;
                height: 38px;
                margin-bottom: 7px;
            }

            .feature-icon svg {
                width: 19px;
                height: 19px;
            }

            .feature-text {
                font-size: 10px;
            }

            .brand-bottom {
                margin-top: 22px;
                padding-top: 16px;
                border-top: 1px solid rgba(255, 255, 255, 0.16);
            }

            .school-illustration {
                height: 190px;
                bottom: 75px;
            }

            .form-panel {
                padding: 35px 30px 31px;
            }

            .form-eyebrow {
                margin-bottom: 20px;
                font-size: 10px;
            }

            .form-header {
                margin-bottom: 27px;
            }

            .form-header h2 {
                font-size: 27px;
            }

            .form-group {
                margin-bottom: 20px;
            }

            .form-footer {
                margin-top: 27px;
            }
        }

        @media (max-width: 400px) {
            body {
                padding: 10px;
            }

            .brand-panel {
                padding: 25px 22px 22px;
            }

            .form-panel {
                padding: 29px 21px 25px;
            }

            .brand-title {
                font-size: 32px;
            }

            .form-header h2 {
                font-size: 25px;
            }

            .form-control {
                height: 54px;
                font-size: 13px;
            }

            .login-options {
                gap: 8px;
            }

            .remember,
            .forgot {
                font-size: 12px;
            }

            .brand-features {
                gap: 7px;
            }

            .feature-text {
                font-size: 9px;
            }

            .btn-login {
                min-height: 55px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *,
            *::before,
            *::after {
                scroll-behavior: auto !important;
                transition-duration: 0.01ms !important;
                animation-duration: 0.01ms !important;
            }
        }
    </style>
</head>

<body>
@php
    $storage = \Illuminate\Support\Facades\Storage::disk('public');

    $schoolLogo = \App\Models\SchoolProfile::query()->value('logo');
    $schoolLogoSrc = null;

    if ($schoolLogo && $storage->exists($schoolLogo)) {
        $mimeType = $storage->mimeType($schoolLogo) ?? 'image/png';

        $schoolLogoSrc = 'data:' . $mimeType . ';base64,'
            . base64_encode($storage->get($schoolLogo));
    }
@endphp

<div class="login-wrapper">

    <!-- PANEL KIRI: IDENTITAS BIDUK -->
    <section class="brand-panel" aria-label="Identitas BIDUK">

        <svg
            class="school-illustration"
            viewBox="0 0 700 300"
            fill="none"
            xmlns="http://www.w3.org/2000/svg"
            aria-hidden="true"
            preserveAspectRatio="xMidYMax meet"
        >
            <path d="M22 126L350 12L678 126" stroke="currentColor" stroke-width="9" stroke-linejoin="round"/>
            <path d="M55 126H645V270H55V126Z" stroke="currentColor" stroke-width="6"/>
            <path d="M35 126H665" stroke="currentColor" stroke-width="7"/>
            <path d="M100 151V267M195 151V267M290 151V267M385 151V267M480 151V267M575 151V267" stroke="currentColor" stroke-width="7"/>
            <path d="M70 177H625M70 224H625" stroke="currentColor" stroke-width="4"/>
            <path d="M25 270H675" stroke="currentColor" stroke-width="9" stroke-linecap="round"/>
            <path d="M310 267V207Q350 172 390 207V267" stroke="currentColor" stroke-width="6"/>
            <path d="M324 221H376M350 205V267" stroke="currentColor" stroke-width="4"/>
        </svg>

        <div class="brand-top">

            <div class="brand-logo">
                @if ($schoolLogoSrc)
                    <img
                        src="{{ $schoolLogoSrc }}"
                        alt="Logo SDN 204 Palembang"
                    >
                @else
                    <span class="brand-logo-fallback">B</span>
                @endif
            </div>

            <h1 class="brand-title">BIDUK</h1>

            <p class="brand-tagline">Buku Induk Digital Unggul dan Kolaboratif</p>

            <p class="school-name">SDN 204 Palembang</p>

            <div class="brand-divider"></div>

            <p class="brand-description">
                Sistem informasi untuk membantu pengelolaan
                data peserta didik dan administrasi sekolah
                secara terintegrasi.
            </p>

            <div class="brand-features">

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="9" cy="8" r="3.1"/>
                            <path d="M3 19v-1.3a5.8 5.8 0 0 1 11.6 0V19H3Z"/>
                            <path d="M16 5.5a3.1 3.1 0 0 1 0 6"/>
                            <path d="M17 14a4.8 4.8 0 0 1 4 4.7V19h-3"/>
                        </svg>
                    </div>
                    <p class="feature-text">Data<br>Terintegrasi</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 3 20 6v5.5c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6l8-3Z"/>
                            <path d="m8.5 12 2.3 2.3 4.8-5"/>
                        </svg>
                    </div>
                    <p class="feature-text">Aman &amp;<br>Terpercaya</p>
                </div>

                <div class="feature-item">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="1.8"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 20V11h4v9M10 20V5h4v15M16 20v-8h4v8"/>
                            <path d="M2 20h20"/>
                        </svg>
                    </div>
                    <p class="feature-text">Administrasi<br>Sekolah</p>
                </div>

            </div>
        </div>

        <div class="brand-bottom">
            <strong>SDN 204 Palembang</strong>
            <span>Sistem Informasi Buku Induk Siswa</span>
        </div>

    </section>

    <!-- PANEL KANAN: FORM LOGIN -->
    <main class="form-panel">
        <div class="form-inner">

            <div class="form-eyebrow">
                <span class="eyebrow-line"></span>
                <span>Portal Administrasi Sekolah</span>
            </div>

            <header class="form-header">
                <h2>Selamat Datang Kembali</h2>
                <p>
                    Masuk menggunakan NIP atau username yang
                    telah terdaftar untuk mengakses BIDUK.
                </p>
            </header>

            @if ($errors->any())
                <div class="alert-error" role="alert">
                    <svg viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <circle cx="12" cy="12" r="9"/>
                        <path d="M12 8v4"/>
                        <path d="M12 16h.01"/>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST">
                @csrf

                <!-- NIP / USERNAME -->
                <div class="form-group">
                    <label for="login">NIP / Username</label>

                    <div class="input-wrapper">
                        <span class="input-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="8" r="4"/>
                                <path d="M4 21v-2a8 8 0 0 1 16 0v2"/>
                            </svg>
                        </span>

                        <input
                            type="text"
                            id="login"
                            name="login"
                            class="form-control"
                            placeholder="Masukkan NIP atau username"
                            autocomplete="username"
                            value="{{ old('login') }}"
                            required
                            autofocus
                        >
                    </div>
                </div>

                <!-- PASSWORD -->
                <div class="form-group">
                    <label for="password">Password</label>

                    <div class="input-wrapper">
                        <span class="input-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="1.8"
                                stroke-linecap="round" stroke-linejoin="round">
                                <rect x="4" y="10" width="16" height="11" rx="2"/>
                                <path d="M8 10V7a4 4 0 1 1 8 0v3"/>
                                <path d="M12 14v3"/>
                            </svg>
                        </span>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control password-input"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            onclick="togglePassword()"
                            aria-label="Tampilkan password"
                            id="togglePasswordButton"
                        >
                            <svg
                                id="eyeIcon"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.8"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                aria-hidden="true"
                            >
                                <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/>
                                <circle cx="12" cy="12" r="2.7"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- INGAT SAYA & LUPA PASSWORD -->
                <div class="login-options">
                    <label class="remember" for="remember">
                        <input
                            type="checkbox"
                            id="remember"
                            name="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                        >
                        <span>Ingat saya</span>
                    </label>

                    <a
                        href="{{ route('password.forgot') }}"
                        class="forgot"
                    >
                        Lupa Password?
                    </a>
                </div>

                <!-- TOMBOL LOGIN -->
                <button type="submit" class="btn-login">
                    <svg viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <path d="M10 17l5-5-5-5"/>
                        <path d="M15 12H3"/>
                        <path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/>
                    </svg>
                    <span>Masuk</span>
                </button>

                <div class="separator">
                    <span>atau</span>
                </div>

                <!-- KEMBALI KE HALAMAN UTAMA -->
                <a href="{{ url('/') }}" class="btn-back">
                    <svg viewBox="0 0 24 24" fill="none"
                        stroke="currentColor" stroke-width="1.8"
                        stroke-linecap="round" stroke-linejoin="round"
                        aria-hidden="true">
                        <path d="m3 10 9-7 9 7"/>
                        <path d="M5 9v11h14V9"/>
                        <path d="M9 20v-7h6v7"/>
                    </svg>
                    <span>Kembali ke Halaman Utama</span>
                </a>
            </form>

            <footer class="form-footer">
                &copy; {{ date('Y') }}
                <strong>SDN 204 Palembang &middot; BIDUK</strong>
                <br>
                Buku Induk Digital Sekolah
            </footer>

        </div>
    </main>

</div>

<script>
    function togglePassword() {
        const password = document.getElementById('password');
        const button = document.getElementById('togglePasswordButton');
        const icon = document.getElementById('eyeIcon');

        if (!password || !button || !icon) {
            return;
        }

        if (password.type === 'password') {
            password.type = 'text';

            button.setAttribute(
                'aria-label',
                'Sembunyikan password'
            );

            icon.innerHTML = `
                <path d="M3 3l18 18"/>
                <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"/>
                <path d="M9.9 5.2A10.7 10.7 0 0 1 12 5c6 0 9.5 7 9.5 7a16.7 16.7 0 0 1-3.2 4.3"/>
                <path d="M6.2 6.2C3.7 7.7 2.5 12 2.5 12s3.5 6 9.5 6c1.5 0 2.8-.3 4-.8"/>
            `;
        } else {
            password.type = 'password';

            button.setAttribute(
                'aria-label',
                'Tampilkan password'
            );

            icon.innerHTML = `
                <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/>
                <circle cx="12" cy="12" r="2.7"/>
            `;
        }
    }
</script>
@include('components.page-loader')
</body>
</html>
