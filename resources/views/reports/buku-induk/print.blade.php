<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Buku Induk {{ $student->name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #111;
            font-family: Arial, sans-serif;
            font-size: 11pt;
        }

        .page {
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        h1 {
            text-align: center;
            font-size: 16pt;
            margin: 0 0 18px;
        }

        h2 {
            font-size: 12pt;
            border-bottom: 1px solid #111;
            padding-bottom: 4px;
            margin: 18px 0 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        td,
        th {
            border: 1px solid #222;
            padding: 6px;
            vertical-align: top;
            word-wrap: break-word;
        }

        th {
            width: 32%;
            text-align: left;
            background: #f2f2f2;
        }

        .meta {
            text-align: center;
            margin-bottom: 14px;
        }

        .no-print {
            margin-bottom: 18px;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="no-print"><button onclick="window.print()">Print</button></div>
    <div class="page">
        <h1>BUKU INDUK PESERTA DIDIK</h1>
        <div class="meta">Data Peserta Didik</div>
        <h2>Identitas Siswa</h2>
        <table>
            <tr>
                <th>Nama Lengkap</th>
                <td>{{ $student->name }}</td>
            </tr>
            <tr>
                <th>NIS</th>
                <td>{{ $student->nis ?: '-' }}</td>
            </tr>
            <tr>
                <th>NISN</th>
                <td>{{ $student->nisn ?: '-' }}</td>
            </tr>
            <tr>
                <th>Jenis Kelamin</th>
                <td>{{ $student->gender ?: '-' }}</td>
            </tr>
            <tr>
                <th>Tempat, Tanggal Lahir</th>
                <td>{{ $student->birth_place ?: '-' }}, {{ $student->birth_date ?: '-' }}</td>
            </tr>
            <tr>
                <th>Agama</th>
                <td>{{ $student->religion ?: '-' }}</td>
            </tr>
            <tr>
                <th>Alamat</th>
                <td>{{ $student->address ?: '-' }}</td>
            </tr>
        </table>
        <h2>Data Akademik</h2>
        <table>
            <tr>
                <th>Tahun Ajaran</th>
                <td>{{ $academicYear?->name ?: '-' }}</td>
            </tr>
            <tr>
                <th>Semester</th>
                <td>{{ $semester?->name ?: 'Semua Semester' }}</td>
            </tr>
            <tr>
                <th>Kelas</th>
                <td>{{ $class?->name ?: '-' }}</td>
            </tr>
        </table>
    </div>
</body>

</html>
