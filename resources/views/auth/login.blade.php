<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - BIDUK</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f4f8f5;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 430px;
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px;
            border-radius: 18px;
            background: #198754;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 28px;
            font-weight: bold;
        }

        .logo h1 {
            color: #198754;
            font-size: 28px;
            margin-bottom: 7px;
        }

        .logo p {
            color: #777;
            font-size: 14px;
        }

        .welcome {
            margin-bottom: 25px;
        }

        .welcome h2 {
            font-size: 22px;
            color: #222;
            margin-bottom: 7px;
        }

        .welcome p {
            color: #777;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 600;
            color: #333;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper input {
            width: 100%;
            padding: 13px 45px 13px 14px;
            border: 1px solid #d9d9d9;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
            transition: 0.2s;
        }

        .input-wrapper input:focus {
            border-color: #198754;
            box-shadow: 0 0 0 3px rgba(25, 135, 84, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            color: #777;
        }

        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 5px 0 25px;
            font-size: 13px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 7px;
            color: #666;
        }

        .forgot {
            color: #198754;
            text-decoration: none;
            font-weight: 600;
        }

        .forgot:hover {
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #198754;
            color: white;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-login:hover {
            background: #157347;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            font-size: 12px;
            color: #999;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 25px;
            }
        }
    </style>
</head>

<body>

<div class="login-container">

    <div class="logo">
        <div class="logo-icon">
            B
        </div>

        <h1>BIDUK</h1>
        <p>Buku Induk Digital Sekolah</p>
    </div>

    <div class="welcome">
        <h2>Selamat Datang 👋</h2>
        <p>Silakan masuk untuk mengakses sistem BIDUK.</p>
    </div>

    <form action="{{ route('login.process') }}" method="POST">
    @csrf


        <div class="form-group">
            <label for="nip">NIP</label>

            <div class="input-wrapper">
                <input
                    type="text"
                    id="nip"
                    name="nip"
                    placeholder="Masukkan NIP"
                    autocomplete="username"
                >
            </div>
        </div>

        <div class="form-group">
            <label for="password">Password</label>

            <div class="input-wrapper">
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                >

                <button
                    type="button"
                    class="toggle-password"
                    onclick="togglePassword()"
                >
                    👁
                </button>
            </div>
        </div>

        <div class="login-options">

            <label class="remember">
                <input type="checkbox" name="remember">
                Ingat saya
            </label>

            <a
                href="{{ route('password.forgot') }}"
                class="forgot"
                target="_blank"
            >
                Lupa Password?
            </a>

        </div>

        <button type="submit" class="btn-login">
            Masuk
        </button>

    </form>

    <div class="footer">
        © {{ date('Y') }} BIDUK - SDN 204
    </div>

</div>

<script>
    function togglePassword() {
        const password = document.getElementById('password');

        if (password.type === 'password') {
            password.type = 'text';
        } else {
            password.type = 'password';
        }
    }
</script>

</body>
</html>