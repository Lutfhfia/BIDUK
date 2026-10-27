<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Siswa - BIDUK</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 18px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
        }

        h2 {
            text-align: center;
            font-size: 15px;
            margin-bottom: 5px;
        }

        .printed-at {
            text-align: right;
            margin-bottom: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #777;
            padding: 5px;
            text-align: left;
        }

        th {
            background-color: #e8f4ec;
            text-align: center;
        }

        .center {
            text-align: center;
        }
    </style>
</head>
<body>
    <h2>DATA SISWA SDN 204 PALEMBANG</h2>

    <p class="printed-at">
        Dicetak: {{ $printedAt->format('d-m-Y H:i') }}
    </p>

    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>NIS</th>
                <th>NISN</th>
                <th>Nama Peserta Didik</th>
                <th>JK</th>
                <th>Tempat Lahir</th>
                <th>Tanggal Lahir</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($students as $student)
                <tr>
                    <td class="center">{{ $student['no'] }}</td>
                    <td>{{ $student['nis'] }}</td>
                    <td>{{ $student['nisn'] }}</td>
                    <td>{{ $student['name'] }}</td>
                    <td class="center">{{ $student['gender'] }}</td>
                    <td>{{ $student['birth_place'] }}</td>
                    <td>{{ $student['birth_date'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="center">
                        Belum ada data siswa.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>