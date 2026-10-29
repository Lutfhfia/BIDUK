<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Preview Buku Induk</title>
    <style>{!! $style !!}</style>
</head>
<body class="pdf-export">
    @forelse ($studentPages as $studentPage)
        {!! $studentPage !!}
    @empty
        <div style="padding: 48px 24px; text-align: center; font-family: Arial, sans-serif;">
            <h2>Data siswa masih kosong</h2>
            <p>Tidak ada siswa pada kelas dan parameter yang dipilih.</p>
        </div>
    @endforelse
</body>
</html>
