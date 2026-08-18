<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <title>Data Pegawai</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 6px;
        }

        th {
            background-color: #eee;
            text-align: center;
        }
    </style>
</head>

<body>

    <h2>DATA PEGAWAI</h2>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NIP</th>
                <th>NUPTK</th>
                <th>Nama</th>
                <th>Jenis Kelamin</th>
                <th>Jenis Pegawai</th>
                <th>Jabatan</th>
                <th>Status Kepegawaian</th>
                <th>Status</th>
            </tr>
        </thead>

        <tbody>
            @forelse ($employees as $employee)

                <tr>
                    <td>{{ $loop->iteration }}</td>

                    <td>{{ $employee->nip ?? '-' }}</td>

                    <td>{{ $employee->nuptk ?? '-' }}</td>

                    <td>{{ $employee->name }}</td>

                    <td>
                        {{ $employee->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                    </td>

                    <td>
                        {{ $employee->employee_type ?? '-' }}
                    </td>

                    <td>
                        {{ $employee->position ?? '-' }}
                    </td>

                    <td>
                        {{ $employee->employment_status ?? '-' }}
                    </td>

                    <td>
                        {{ $employee->status ?? '-' }}
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="9" style="text-align: center;">
                        Belum ada data pegawai.
                    </td>
                </tr>

            @endforelse
        </tbody>
    </table>

</body>
</html>