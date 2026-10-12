<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>
        Nilai Akhir / Ijazah — {{ $student->name }} — Kelas {{ $class->grade_level }}{{ $class->name }}
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
            font-size: 7pt;
        }

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            padding: 0 30mm 8mm;
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
        .school-letterhead {
            display: block;
            width: 100%;
            height: auto;
            margin: 0 0 4mm;
        }

        /* ================================================
           JUDUL
        ================================================ */
        .report-title {
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            margin: 3mm 0 1mm;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .report-subtitle {
            text-align: center;
            font-size: 7pt;
            margin: 0 0 3mm;
            color: #444;
        }

        /* ================================================
           IDENTITAS SISWA
        ================================================ */
        .identity-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3mm;
        }

        .identity-table td {
            padding: 0.5mm 1mm;
            font-size: 7pt;
            vertical-align: top;
        }

        .identity-table td.label-col {
            width: 34mm;
            white-space: nowrap;
        }

        .identity-table td.colon-col {
            width: 3mm;
            text-align: center;
        }

        /* ================================================
           TABEL NILAI
        ================================================ */
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-bottom: 2mm;
        }

        .grades-table th,
        .grades-table td {
            border: 1px solid #000;
            padding: 1mm 1mm;
            font-size: 5.5pt;
            vertical-align: middle;
            overflow-wrap: break-word;
        }

        .grades-table th {
            text-align: center;
            font-weight: bold;
            background-color: #f0f0f0;
            font-size: 5.8pt;
        }

        .grades-table td.text-center {
            text-align: center;
        }

        .grades-table td.subject-name {
            text-align: left;
        }

        .grades-table td.group-header {
            font-weight: bold;
            background-color: #f5f5f5;
            font-size: 6pt;
        }

        .grades-table td.indented {
            padding-left: 3mm;
        }

        .grades-table td.score-cell {
            font-size: 5.5pt;
            white-space: nowrap;
        }

        .grades-table tr.avg-row td {
            font-weight: bold;
            background-color: #e8f4fd;
        }

        /* ================================================
           BAGIAN BAWAH (ABSENSI, STATUS, TANDA TANGAN)
        ================================================ */
        .bottom-section {
            margin-top: 2mm;
        }

        .bottom-section table {
            width: 100%;
            border-collapse: collapse;
        }

        .bottom-section td {
            padding: 0.5mm 1mm;
            font-size: 6.5pt;
            vertical-align: top;
        }

        .signature-section {
            margin-top: 4mm;
            width: 100%;
        }

        .signature-section table {
            width: 100%;
        }

        .signature-section td {
            padding: 1mm;
            font-size: 6.5pt;
            vertical-align: top;
            width: 50%;
        }

        .signature-block {
            text-align: center;
        }

        .signature-space {
            height: 14mm;
        }

        .status-box {
            border: 1px solid #000;
            padding: 1mm 2mm;
            display: inline-block;
            font-size: 8pt;
            font-weight: bold;
            letter-spacing: 1px;
            margin: 1mm 0;
        }
    </style>
</head>

<body>
    <div class="page">

        {{-- ================================================
           HEADER SEKOLAH
        ================================================ --}}
        <img
            class="school-letterhead"
            src="data:image/png;base64,{{ $letterheadImage }}"
            alt="Kop SD Negeri 204 Palembang"
        >


        {{-- ================================================
           JUDUL
        ================================================ --}}
        <div class="report-title">
            DAFTAR NILAI AKHIR / IJAZAH
        </div>
        <div class="report-subtitle">
            Tahun Pelajaran {{ $academicYear->name ?? '-' }}
        </div>


        {{-- ================================================
           IDENTITAS SISWA
        ================================================ --}}
        <table class="identity-table">
            <tr>
                <td class="label-col">Nama Peserta Didik</td>
                <td class="colon-col">:</td>
                <td><strong>{{ $student->name }}</strong></td>
            </tr>
            <tr>
                <td class="label-col">NISN / NIS</td>
                <td class="colon-col">:</td>
                <td>{{ $student->nisn ?? '-' }} / {{ $student->nis ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-col">Kelas</td>
                <td class="colon-col">:</td>
                <td>{{ $class->name }}</td>
            </tr>
        </table>


        {{-- ================================================
           TABEL NILAI AKHIR
        ================================================ --}}
        <table class="grades-table">
            <thead>
                <tr>
                    <th style="width: 6mm;">No</th>
                    <th style="width: 36mm;">Mata Pelajaran</th>
                    <th style="width: 12mm;">Nilai<br>Sekolah</th>
                    <th style="width: 44mm;">Capaian Pembelajaran<br>(Nilai Sekolah)</th>
                    <th style="width: 12mm;">Nilai<br>Akhir</th>
                    <th style="width: 40mm;">Capaian Pembelajaran<br>(Nilai Akhir)</th>
                </tr>
            </thead>

            <tbody>
                @php
                    $no = 0;
                    $currentGroup = null;
                @endphp

                @foreach($ijazahMapping as $item)
                    @php
                        $showGroupHeader = $item['group_name'] && $item['group_name'] !== $currentGroup;
                        if ($showGroupHeader) {
                            $currentGroup = $item['group_name'];
                        }

                        $no++;
                        $sid = $item['subject_id'];
                        $stored = $sid ? ($storedGrades[$sid] ?? null) : null;
                    @endphp

                    {{-- Header kelompok --}}
                    @if($showGroupHeader)
                        <tr>
                            <td colspan="6" class="group-header">
                                {{ $currentGroup }}
                            </td>
                        </tr>
                    @endif

                    <tr>
                        <td class="text-center">{{ $no }}</td>
                        <td class="{{ $item['group_name'] ? 'indented' : 'subject-name' }}">
                            {{ $item['ijazah_name'] }}
                        </td>
                        <td class="text-center score-cell">
                            {{ $stored && $stored->school_score !== null ? number_format($stored->school_score, 2) : '-' }}
                        </td>
                        <td>
                            {{ $stored->school_learning_outcome ?? '' }}
                        </td>
                        <td class="text-center score-cell">
                            {{ $stored && $stored->final_score !== null ? number_format($stored->final_score, 2) : '-' }}
                        </td>
                        <td>
                            {{ $stored->final_learning_outcome ?? '' }}
                        </td>
                    </tr>
                @endforeach

                {{-- Rata-rata --}}
                <tr class="avg-row">
                    <td colspan="2" class="text-center">Rata-rata</td>
                    <td class="text-center score-cell">
                        {{ $avgSchoolScore !== null ? number_format($avgSchoolScore, 2) : '-' }}
                    </td>
                    <td></td>
                    <td class="text-center score-cell">
                        {{ $avgFinalScore !== null ? number_format($avgFinalScore, 2) : '-' }}
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>


        {{-- ================================================
           KETIDAKHADIRAN
        ================================================ --}}
        <div class="bottom-section">
            <table>
                <tr>
                    <td style="width: 50%;">
                        <table>
                            <tr>
                                <td style="width: 120px;">Sakit</td>
                                <td style="width: 10px;">:</td>
                                <td>{{ $attendance ? $attendance->sakit . ' hari' : '-' }}</td>
                            </tr>
                            <tr>
                                <td>Izin</td>
                                <td>:</td>
                                <td>{{ $attendance ? $attendance->izin . ' hari' : '-' }}</td>
                            </tr>
                            <tr>
                                <td>Tanpa Keterangan</td>
                                <td>:</td>
                                <td>{{ $attendance ? $attendance->tanpa_keterangan . ' hari' : '-' }}</td>
                            </tr>
                        </table>
                    </td>

                    <td style="width: 50%;">
                        <table>
                            <tr>
                                <td style="width: 120px;">Status Akhir</td>
                                <td style="width: 10px;">:</td>
                                <td>
                                    @if($graduationStatus && $graduationStatus->status)
                                        <div class="status-box">
                                            {{ $graduationStatus->status }}
                                        </div>
                                    @else
                                        <em>Belum ditetapkan</em>
                                    @endif
                                </td>
                            </tr>
                            @if($graduationStatus && $graduationStatus->graduation_date)
                            <tr>
                                <td>Tanggal Penetapan</td>
                                <td>:</td>
                                <td>{{ $graduationStatus->graduation_date->translatedFormat('d F Y') }}</td>
                            </tr>
                            @endif
                            @if($graduationStatus && $graduationStatus->certificate_number)
                            <tr>
                                <td>Nomor Ijazah</td>
                                <td>:</td>
                                <td>{{ $graduationStatus->certificate_number }}</td>
                            </tr>
                            @endif
                            @if($graduationStatus && $graduationStatus->skhus_number)
                            <tr>
                                <td>Nomor SKHUS</td>
                                <td>:</td>
                                <td>{{ $graduationStatus->skhus_number }}</td>
                            </tr>
                            @endif
                        </table>
                    </td>
                </tr>
            </table>
        </div>


        {{-- ================================================
           TANDA TANGAN
        ================================================ --}}
        <div class="signature-section">
            <table>
                <tr>
                    <td>
                        <div class="signature-block">
                            <p>Wali Kelas</p>
                            <div class="signature-space"></div>
                            <p>
                                <strong><u>{{ $homeroomTeacher->name ?? '........................' }}</u></strong>
                                <br>
                                @if($homeroomTeacher && $homeroomTeacher->nip)
                                    NIP. {{ $homeroomTeacher->nip }}
                                @else
                                    NIP. ............................
                                @endif
                            </p>
                        </div>
                    </td>
                    <td>
                        <div class="signature-block">
                            <p>Kepala Sekolah</p>
                            <div class="signature-space"></div>
                            <p>
                                <strong><u>{{ $headmaster ?? '........................' }}</u></strong>
                                <br>
                                @if($headmasterNip)
                                    NIP. {{ $headmasterNip }}
                                @else
                                    NIP. ............................
                                @endif
                            </p>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

    </div>
</body>

</html>
