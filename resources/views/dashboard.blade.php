<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard BIDUK</title>
</head>
<body>

    <h1>Dashboard BIDUK</h1>

    <p>
        Selamat datang, {{ Auth::user()->name }}
    </p>

    <form action="{{ route('logout') }}" method="POST">
        @csrf

        <button type="submit">
            Logout
        </button>
    </form>

</body>
</html>