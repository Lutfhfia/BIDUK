<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Buku Induk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <main class="container py-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
            <div>
                <div class="text-muted small mb-1">Laporan / Buku Induk</div>
                <h1 class="h3 mb-1">{{ $class ? 'Buku Induk Kelas ' . $class->name : 'Buku Induk Semua Kelas' }}</h1>
                <p class="text-muted mb-0">{{ $students->count() }} siswa ditemukan pada parameter yang dipilih.</p>
            </div>
            <a href="{{ route('buku-induk.index', request()->only(['academic_year_id', 'semester_id', 'class_id', 'status'])) }}"
                class="btn btn-outline-secondary">
                Kembali ke Parameter
            </a>
        </div>

        @if ($students->isEmpty())
            <div class="alert alert-warning">
                Tidak ada siswa yang sesuai dengan kelas, tahun ajaran, dan status yang dipilih.
            </div>
        @else
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <strong>Daftar Siswa</strong>
                    <span class="badge text-bg-primary">{{ $students->count() }} siswa</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th class="ps-4">No.</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                @php
                                    $printQuery = http_build_query(
                                        request()->only(['academic_year_id', 'semester_id', 'class_id', 'status']),
                                    );
                                    $printUrl =
                                        route('buku-induk.print', $student) . ($printQuery ? '?' . $printQuery : '');
                                @endphp
                                <tr>
                                    <td class="ps-4">{{ $loop->iteration }}</td>
                                    <td class="fw-semibold">{{ $student->name }}</td>
                                    <td>{{ $student->nis }}</td>
                                    <td class="text-end pe-4">
                                        <a href="{{ $printUrl }}" target="_blank" class="btn btn-sm btn-primary">
                                            <i class="bi bi-printer me-1"></i>
                                            Buka Buku Induk
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </main>
</body>

</html>
