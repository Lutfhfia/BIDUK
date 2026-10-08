<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | BIDUK - SDN 204 Palembang</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            min-height: 100%;
        }

        body {
            font-family:
                "Segoe UI",
                Arial,
                Helvetica,
                sans-serif;
            background: #f3f6f4;
            color: #1f2937;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 32px 20px;
        }

        /* =====================================================
           CONTAINER
        ====================================================== */

        .login-wrapper {
            width: 100%;
            max-width: 920px;
            min-height: 560px;

            display: grid;
            grid-template-columns: 1fr 1fr;

            background: #ffffff;

            border: 1px solid #e2e8e4;
            border-radius: 12px;

            overflow: hidden;

            box-shadow:
                0 8px 30px rgba(31, 41, 55, 0.07);
        }

        /* =====================================================
           LEFT / BRANDING
        ====================================================== */

        .brand-panel {
            background: #198754;
            color: #ffffff;

            padding: 52px 48px;

            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .brand-top {
            display: flex;
            flex-direction: column;
        }

        .brand-mark {
            width: 58px;
            height: 58px;

            display: flex;
            align-items: center;
            justify-content: center;

            margin-bottom: 24px;

            border: 1px solid rgba(255, 255, 255, 0.35);
            border-radius: 10px;

            font-size: 23px;
            font-weight: 700;

            background: rgba(255, 255, 255, 0.08);
        }

        .brand-title {
            font-size: 30px;
            font-weight: 700;
            letter-spacing: 0.4px;

            margin-bottom: 8px;
        }

        .brand-subtitle {
            font-size: 14px;
            line-height: 1.6;

            color: rgba(255, 255, 255, 0.88);

            max-width: 280px;
        }

        .brand-divider {
            width: 45px;
            height: 2px;

            background: rgba(255, 255, 255, 0.55);

            margin: 28px 0;
        }

        .brand-description {
            max-width: 300px;

            font-size: 14px;
            line-height: 1.7;

            color: rgba(255, 255, 255, 0.86);
        }

        .brand-bottom {
            font-size: 12px;

            color: rgba(255, 255, 255, 0.7);

            padding-top: 32px;
        }

        /* =====================================================
           RIGHT / FORM
        ====================================================== */

        .form-panel {
            padding: 52px 48px;

            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-size: 25px;
            font-weight: 700;

            color: #1f2937;

            margin-bottom: 8px;
        }

        .form-header p {
            color: #6b7280;

            font-size: 14px;
            line-height: 1.6;
        }

        /* =====================================================
           ERROR
        ====================================================== */

        .alert-error {
            margin-bottom: 20px;

            padding: 12px 14px;

            background: #fff4f4;
            border: 1px solid #f1caca;
            border-radius: 7px;

            color: #b42318;

            font-size: 13px;
            line-height: 1.5;
        }

        /* =====================================================
           FORM
        ====================================================== */

        .form-group {
            margin-bottom: 19px;
        }

        .form-group label {
            display: block;

            margin-bottom: 8px;

            font-size: 13px;
            font-weight: 600;

            color: #374151;
        }

        .input-wrapper {
            position: relative;
        }

        .form-control {
            width: 100%;

            height: 46px;

            padding: 0 14px;

            border: 1px solid #d5ddd8;
            border-radius: 7px;

            outline: none;

            background: #ffffff;

            color: #1f2937;

            font-size: 14px;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .form-control.password-input {
            padding-right: 45px;
        }

        .form-control::placeholder {
            color: #9ca3af;
        }

        .form-control:focus {
            border-color: #198754;

            box-shadow:
                0 0 0 3px rgba(25, 135, 84, 0.10);
        }

        /* =====================================================
           PASSWORD TOGGLE
        ====================================================== */

        .toggle-password {
            position: absolute;

            top: 50%;
            right: 12px;

            transform: translateY(-50%);

            width: 30px;
            height: 30px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: none;
            background: transparent;

            color: #6b7280;

            cursor: pointer;

            border-radius: 5px;

            transition:
                background 0.2s ease,
                color 0.2s ease;
        }

        .toggle-password:hover {
            background: #f0f4f1;
            color: #198754;
        }

        .toggle-password svg {
            width: 17px;
            height: 17px;
        }

        /* =====================================================
           OPTIONS
        ====================================================== */

        .login-options {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-top: 4px;
            margin-bottom: 24px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 7px;

            font-size: 13px;
            color: #6b7280;

            cursor: pointer;
        }

        .remember input {
            width: 14px;
            height: 14px;

            accent-color: #198754;

            cursor: pointer;
        }

        .forgot {
            color: #198754;

            font-size: 13px;
            font-weight: 600;

            text-decoration: none;
        }

        .forgot:hover {
            text-decoration: underline;
        }

        /* =====================================================
           BUTTON
        ====================================================== */

        .btn-login {
            width: 100%;
            height: 46px;

            border: none;
            border-radius: 7px;

            background: #198754;
            color: #ffffff;

            font-size: 14px;
            font-weight: 600;

            cursor: pointer;

            transition:
                background 0.2s ease,
                transform 0.1s ease;
        }

        .btn-login:hover {
            background: #157347;
        }

        .btn-login:active {
            transform: translateY(1px);
        }

        .btn-back {
            display: block;

            width: 100%;
            height: 44px;

            margin-top: 11px;

            display: flex;
            align-items: center;
            justify-content: center;

            border: 1px solid #d5ddd8;
            border-radius: 7px;

            background: #ffffff;
            color: #4b5563;

            font-size: 13px;
            font-weight: 500;

            text-decoration: none;

            transition:
                border-color 0.2s ease,
                color 0.2s ease,
                background 0.2s ease;
        }

        .btn-back:hover {
            border-color: #198754;
            color: #198754;
            background: #f7faf8;
        }

        /* =====================================================
           FOOTER
        ====================================================== */

        .form-footer {
            margin-top: 28px;

            padding-top: 20px;

            border-top: 1px solid #edf0ee;

            text-align: center;

            font-size: 11px;

            color: #9ca3af;
        }

        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 760px) {

            body {
                padding: 20px;
            }

            .login-wrapper {
                max-width: 460px;
                min-height: auto;

                grid-template-columns: 1fr;
            }

            .brand-panel {
                padding: 32px;
            }

            .brand-description,
            .brand-divider {
                display: none;
            }

            .brand-bottom {
                padding-top: 26px;
            }

            .form-panel {
                padding: 34px 32px;
            }
        }

        @media (max-width: 420px) {

            body {
                padding: 12px;
            }

            .brand-panel {
                padding: 28px 24px;
            }

            .form-panel {
                padding: 30px 24px;
            }

            .brand-title {
                font-size: 26px;
            }

            .form-header h2 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>

<div class="login-wrapper">

    {{-- =====================================================
         BRANDING
    ====================================================== --}}
    <div class="brand-panel">

        <div class="brand-top">

            <div class="brand-mark">
                B
            </div>

            <div class="brand-title">
                BIDUK
            </div>

            <div class="brand-subtitle">
                Buku Induk Digital Sekolah
            </div>

            <div class="brand-divider"></div>

            <div class="brand-description">
                Sistem informasi untuk membantu pengelolaan
                data peserta didik dan administrasi sekolah
                secara terintegrasi.
            </div>

        </div>

        <div class="brand-bottom">
            SDN 204 Palembang
        </div>

    </div>


    {{-- =====================================================
         LOGIN FORM
    ====================================================== --}}
    <div class="form-panel">

        <div class="form-header">

            <h2>
                Masuk ke Sistem
            </h2>

            <p>
                Gunakan NIP atau username yang telah terdaftar
                untuk mengakses BIDUK.
            </p>

        </div>


        {{-- =================================================
             ERROR
        ================================================== --}}
        @if($errors->any())

            <div class="alert-error">
                {{ $errors->first() }}
            </div>

        @endif


        {{-- =================================================
             FORM
        ================================================== --}}
        <form
            action="{{ route('login.process') }}"
            method="POST"
        >

            @csrf


            {{-- NIP / USERNAME --}}
            <div class="form-group">

                <label for="login">
                    NIP / Username
                </label>

                <div class="input-wrapper">

                    <input
                        type="text"
                        id="login"
                        name="login"
                        class="form-control"
                        placeholder="Masukkan NIP atau username"
                        autocomplete="username"
                        value="{{ old('login') }}"
                        required
                    >

                </div>

            </div>


            {{-- PASSWORD --}}
            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <div class="input-wrapper">

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

                        {{-- Eye --}}
                        <svg
                            id="eyeIcon"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="1.8"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"/>
                            <circle cx="12" cy="12" r="2.7"/>
                        </svg>

                    </button>

                </div>

            </div>


            {{-- OPTIONS --}}
            <div class="login-options">

                <label class="remember">

                    <input
                        type="checkbox"
                        name="remember"
                        value="1"
                        {{ old('remember') ? 'checked' : '' }}
                    >

                    <span>
                        Ingat saya
                    </span>

                </label>


                <a
                    href="{{ route('password.forgot') }}"
                    class="forgot"
                >
                    Lupa Password?
                </a>

            </div>


            {{-- LOGIN --}}
            <button
                type="submit"
                class="btn-login"
            >
                Masuk
            </button>


            {{-- BACK --}}
            <a
                href="{{ url('/') }}"
                class="btn-back"
            >
                Kembali ke Halaman Utama
            </a>

        </form>


        {{-- FOOTER --}}
        <div class="form-footer">
            © {{ date('Y') }} SDN 204 Palembang · BIDUK
        </div>

    </div>

</div>


<script>
    function togglePassword() {

        const password =
            document.getElementById('password');

        const button =
            document.getElementById('togglePasswordButton');

        const icon =
            document.getElementById('eyeIcon');


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

</body>
</html>