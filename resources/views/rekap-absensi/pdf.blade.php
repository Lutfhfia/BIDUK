<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 11mm 14mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #111;
            background: #fff;
            font-family: DejaVu Sans, sans-serif;
            font-size: 9pt;
        }

        .document-title {
            margin: 0 0 5mm;
            text-align: center;
            font-size: 16pt;
            font-weight: 700;
            letter-spacing: .4px;
        }

        .filter-info {
            width: 100%;
            margin: 0 0 4mm;
            border-collapse: collapse;
            font-size: 8pt;
        }

        .filter-info td {
            padding: 1mm 2mm 1mm 0;
            vertical-align: top;
        }

        .filter-label {
            width: 23mm;
            font-weight: 700;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .attendance-table th,
        .attendance-table td {
            padding: 2.2mm 1.5mm;
            border: .5pt solid #333;
            color: #111;
            overflow-wrap: anywhere;
            vertical-align: middle;
        }

        .attendance-table thead {
            display: table-header-group;
        }

        .attendance-table th {
            background: #fff;
            text-align: center;
            font-size: 7.5pt;
            font-weight: 700;
        }

        .attendance-table td {
            font-size: 8pt;
        }

        .attendance-table tbody tr {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .col-no { width: 4%; }
        .col-nisn { width: 10%; }
        .col-name { width: 19%; }
        .col-class { width: 7%; }
        .col-sakit { width: 8%; }
        .col-izin { width: 7%; }
        .col-tanpa { width: 11%; }

        .empty-row {
            padding: 8mm !important;
            text-align: center;
            font-style: italic;
        }

    </style>
</head>
<body>
    <h1 class="document-title">REKAP ABSENSI</h1>

    <table class="filter-info">
        <tr>
            <td class="filter-label">Tahun Ajaran</td>
            <td>: {{ $selectedAcademicYear?->name ?? 'Semua Tahun Ajaran' }}</td>
            <td class="filter-label">Kelas</td>
            <td>: {{ $selectedClass?->name ?? 'Semua Kelas' }}</td>
        </tr>
        @if ($search !== '')
            <tr>
                <td class="filter-label">Pencarian</td>
                <td colspan="3">: {{ $search }}</td>
            </tr>
        @endif
    </table>

    <table class="attendance-table">
        <colgroup>
            <col class="col-no">
            <col class="col-nisn">
            <col class="col-name">
            <col class="col-class">
            <col class="col-sakit">
            <col class="col-izin">
            <col class="col-tanpa">
            <col class="col-sakit">
            <col class="col-izin">
            <col class="col-tanpa">
        </colgroup>
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">NISN</th>
                <th rowspan="2">Nama Siswa</th>
                <th rowspan="2">Kelas</th>
                <th colspan="3">Semester Ganjil</th>
                <th colspan="3">Semester Genap</th>
            </tr>
            <tr>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Tanpa Keterangan</th>
                <th>Sakit</th>
                <th>Izin</th>
                <th>Tanpa Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rekapAbsensi as $rekap)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $rekap->student?->nisn ?? '-' }}</td>
                    <td class="text-left">{{ $rekap->student?->name ?? '-' }}</td>
                    <td class="text-center">{{ $rekap->schoolClass?->name ?? '-' }}</td>
                    <td class="text-center">{{ $rekap->ganjil?->sakit ?? '-' }}</td>
                    <td class="text-center">{{ $rekap->ganjil?->izin ?? '-' }}</td>
                    <td class="text-center">{{ $rekap->ganjil?->tanpa_keterangan ?? '-' }}</td>
                    <td class="text-center">{{ $rekap->genap?->sakit ?? '-' }}</td>
                    <td class="text-center">{{ $rekap->genap?->izin ?? '-' }}</td>
                    <td class="text-center">{{ $rekap->genap?->tanpa_keterangan ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="empty-row">Tidak ada data rekap absensi untuk filter yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
