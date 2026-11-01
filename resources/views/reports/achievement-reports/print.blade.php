<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Prestasi</title>
    <style>
        @page { size: A4 landscape; margin: 12mm 10mm; }
        body { color: #222; font-family: Arial, sans-serif; font-size: 10px; }
        h1 { margin: 0 0 8px; text-align: center; font-size: 16px; }
        .filters { margin-bottom: 12px; text-align: center; color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 6px; border: 1px solid #444; text-align: left; }
        th { background: #e8f5ee; text-align: center; }
        .number { width: 30px; text-align: center; }
        .center { text-align: center; }
    </style>
</head>
<body>
    @php
        $filteredYear = $academicYears->firstWhere('id', $selectedAcademicYearId);
        $filteredClass = $classes->firstWhere('id', $selectedClassId);
    @endphp

    <h1>REKAP PRESTASI PESERTA DIDIK</h1>
    <div class="filters">
        Tahun ajaran: {{ $filteredYear?->name ?? 'Semua' }} |
        Kelas: {{ $filteredClass ? $filteredClass->grade_level . ' ' . $filteredClass->name : 'Semua' }}
    </div>

    <table>
        <thead>
            <tr>
                <th class="number">No.</th>
                <th>Nama Siswa</th>
                <th>NIS</th>
                <th>NISN</th>
                <th>Kelas</th>
                <th>Jenis Prestasi</th>
                <th>Tingkat</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($achievements as $index => $achievement)
                <tr>
                    <td class="number">{{ $index + 1 }}</td>
                    <td>{{ $achievement->student->name ?? '-' }}</td>
                    <td>{{ $achievement->student->nis ?? '-' }}</td>
                    <td>{{ $achievement->student->nisn ?? '-' }}</td>
                    <td class="center">{{ $achievement->schoolClass->name ?? '-' }}</td>
                    <td>{{ $achievement->type }}</td>
                    <td class="center">{{ $achievement->level ?: '-' }}</td>
                    <td>{{ $achievement->description ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="center">Tidak ada data prestasi berdasarkan filter yang dipilih.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
