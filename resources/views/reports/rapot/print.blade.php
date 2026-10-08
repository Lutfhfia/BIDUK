<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>
        Rapot {{ $student->name }} — Kelas {{ $class->grade_level }}{{ $class->name }} — Semester {{ $semester->name }}
    </title>

    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            font-size: 9pt;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 12mm 14mm;
            background: #fff;
            position: relative;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        /* ================================================
           HEADER SEKOLAH
        ================================================ */
        .school-header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 2px solid #000;
            padding-bottom: 8px;
        }

        .school-header .school-name {
            font-size: 14pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .school-header .school-subtitle {
            font-size: 8pt;
            margin: 2px 0 0 0;
            color: #333;
        }

        /* ================================================
           JUDUL RAPOT
        ================================================ */
        .report-title {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            margin: 12px 0 6px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .report-subtitle {
            text-align: center;
            font-size: 9pt;
            margin: 0 0 14px 0;
            color: #444;
        }

        /* ================================================
           IDENTITAS SISWA
        ================================================ */
        .identity-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .identity-table td {
            padding: 2px 4px;
            font-size: 9pt;
            vertical-align: top;
        }

        .identity-table .label {
            width: 140px;
            font-weight: bold;
            white-space: nowrap;
        }

        .identity-table .colon {
            width: 10px;
            text-align: center;
        }

        .identity-table .value {
            border-bottom: 1px dotted #999;
        }

        /* ================================================
           TABEL NILAI
        ================================================ */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 14px;
        }

        .grades-table th,
        .grades-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            font-size: 8.5pt;
            text-align: left;
            vertical-align: top;
        }

        .grades-table th {
            background-color: #e8f5e9;
            font-weight: bold;
            text-align: center;
            font-size: 8pt;
        }

        .grades-table .col-no {
            width: 28px;
            text-align: center;
        }

        .grades-table .col-subject {
            width: auto;
        }

        .grades-table .col-score {
            width: 50px;
            text-align: center;
        }

        .grades-table .col-outcome {
            width: 45%;
        }

        .grades-table tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        .grades-table tfoot td {
            font-weight: bold;
            background-color: #e8f5e9;
        }

        /* ================================================
           KEHADIRAN
        ================================================ */
        .attendance-section {
            margin-bottom: 14px;
        }

        .attendance-section h3 {
            font-size: 9pt;
            font-weight: bold;
            margin: 0 0 6px 0;
        }

        .attendance-table {
            width: 50%;
            border-collapse: collapse;
        }

        .attendance-table td,
        .attendance-table th {
            border: 1px solid #000;
            padding: 3px 6px;
            font-size: 8.5pt;
        }

        .attendance-table th {
            background-color: #e8f5e9;
            text-align: left;
            font-weight: bold;
            width: 150px;
        }

        .attendance-table td {
            text-align: center;
            width: 60px;
        }

        /* ================================================
           PRESTASI
        ================================================ */
        .achievement-section {
            margin-bottom: 14px;
        }

        .achievement-section h3 {
            font-size: 9pt;
            font-weight: bold;
            margin: 0 0 6px 0;
        }

        .achievement-table {
            width: 100%;
            border-collapse: collapse;
        }

        .achievement-table th,
        .achievement-table td {
            border: 1px solid #000;
            padding: 3px 6px;
            font-size: 8.5pt;
        }

        .achievement-table th {
            background-color: #e8f5e9;
            font-weight: bold;
            text-align: center;
        }

        /* ================================================
           TANDA TANGAN
        ================================================ */
        .signature-section {
            margin-top: 20px;
            width: 100%;
        }

        .signature-row {
            display: table;
            width: 100%;
        }

        .signature-col {
            display: table-cell;
            width: 50%;
            text-align: center;
            vertical-align: top;
            font-size: 8.5pt;
        }

        .signature-col .title {
            font-weight: bold;
            margin-bottom: 50px;
        }

        .signature-col .name {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 160px;
            font-weight: bold;
        }

        .signature-col .nip {
            font-size: 7.5pt;
            margin-top: 2px;
        }

        /* ================================================
           CATATAN WALI KELAS
        ================================================ */
        .notes-box {
            border: 1px solid #000;
            padding: 8px 10px;
            margin-bottom: 14px;
            min-height: 50px;
            font-size: 8.5pt;
        }

        .notes-box .notes-title {
            font-weight: bold;
            margin-bottom: 4px;
        }
    </style>
</head>

<body>

    <div class="page">

        {{-- ================================================
            HEADER SEKOLAH
        ================================================ --}}
        <div class="school-header">
            <p class="school-name">
                {{ $school->name ?? 'SDN 204 Palembang' }}
            </p>
            <p class="school-subtitle">
                {{ $school->address ?? '' }}
                @if (!empty($school->phone))
                    | Telp: {{ $school->phone }}
                @endif
            </p>
        </div>

        {{-- ================================================
            JUDUL RAPOT
        ================================================ --}}
        <p class="report-title">
            LAPORAN HASIL BELAJAR SISWA
        </p>
        <p class="report-subtitle">
            Semester {{ $semester->name }} — Tahun Ajaran {{ $academicYear->name ?? '-' }}
        </p>

        {{-- ================================================
            IDENTITAS SISWA
        ================================================ --}}
        <table class="identity-table">
            <tr>
                <td class="label">Nama Peserta Didik</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->name }}</td>
                <td class="label">Kelas</td>
                <td class="colon">:</td>
                <td class="value">{{ $class->grade_level }}{{ $class->name }}</td>
            </tr>
            <tr>
                <td class="label">NIS</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->nis ?? '-' }}</td>
                <td class="label">Semester</td>
                <td class="colon">:</td>
                <td class="value">{{ $semester->name }}</td>
            </tr>
            <tr>
                <td class="label">NISN</td>
                <td class="colon">:</td>
                <td class="value">{{ $student->nisn ?? '-' }}</td>
                <td class="label">Tahun Ajaran</td>
                <td class="colon">:</td>
                <td class="value">{{ $academicYear->name ?? '-' }}</td>
            </tr>
        </table>

        {{-- ================================================
            TABEL NILAI
        ================================================ --}}
        <table class="grades-table">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-subject">Mata Pelajaran</th>
                    <th class="col-score">Nilai</th>
                    <th class="col-outcome">Capaian Kompetensi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($grades as $index => $grade)
                    <tr>
                        <td class="col-no">{{ $index + 1 }}</td>
                        <td>{{ $grade->subject->name ?? '-' }}</td>
                        <td class="col-score">
                            {{ $grade->score !== null ? number_format($grade->score, 0) : '-' }}
                        </td>
                        <td>{{ $grade->learning_outcome ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 12px; color: #999;">
                            Belum ada data nilai.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($grades->isNotEmpty())
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align: right;">Rata-rata</td>
                        <td class="col-score">
                            @php
                                $scored = $grades->filter(fn($g) => $g->score !== null);
                                $avg = $scored->count() > 0
                                    ? round($scored->avg('score'), 1)
                                    : '-';
                            @endphp
                            {{ $avg }}
                        </td>
                        <td></td>
                    </tr>
                </tfoot>
            @endif
        </table>

        {{-- ================================================
            KEHADIRAN
        ================================================ --}}
        <div class="attendance-section">
            <h3>Ketidakhadiran</h3>
            <table class="attendance-table">
                <tr>
                    <th>Sakit</th>
                    <td>{{ $attendance->sakit ?? 0 }} hari</td>
                </tr>
                <tr>
                    <th>Izin</th>
                    <td>{{ $attendance->izin ?? 0 }} hari</td>
                </tr>
                <tr>
                    <th>Tanpa Keterangan</th>
                    <td>{{ $attendance->tanpa_keterangan ?? 0 }} hari</td>
                </tr>
            </table>
        </div>

        {{-- ================================================
            PRESTASI (jika ada)
        ================================================ --}}
        @if ($achievements->isNotEmpty())
            <div class="achievement-section">
                <h3>Prestasi</h3>
                <table class="achievement-table">
                    <thead>
                        <tr>
                            <th style="width: 28px;">No</th>
                            <th>Jenis</th>
                            <th>Tingkat</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($achievements as $i => $ach)
                            <tr>
                                <td style="text-align: center;">{{ $i + 1 }}</td>
                                <td>{{ $ach->type ?? '-' }}</td>
                                <td>{{ $ach->level ?? '-' }}</td>
                                <td>{{ $ach->description ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        {{-- ================================================
            TANDA TANGAN
        ================================================ --}}
        <div class="signature-section">
            <div class="signature-row">

                <div class="signature-col">
                    <div class="title">Orang Tua / Wali Murid</div>
                    <div class="name">&nbsp;</div>
                </div>

                <div class="signature-col">
                    <div class="title">
                        Wali Kelas
                    </div>
                    <div class="name">
                        {{ $homeroomTeacher->name ?? '........................' }}
                    </div>
                    @if (!empty($homeroomTeacher->nip))
                        <div class="nip">NIP. {{ $homeroomTeacher->nip }}</div>
                    @endif
                </div>

            </div>

            <div style="text-align: center; margin-top: 20px;">
                <div class="title" style="font-weight: bold;">
                    Mengetahui,<br>
                    Kepala Sekolah
                </div>
                <div style="margin-top: 50px;">
                    <div class="name" style="border-bottom: 1px solid #000; display: inline-block; min-width: 160px; font-weight: bold;">
                        {{ $headmaster ?? '........................' }}
                    </div>
                    @if (!empty($headmasterNip))
                        <div class="nip" style="font-size: 7.5pt; margin-top: 2px;">NIP. {{ $headmasterNip }}</div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</body>

</html>
