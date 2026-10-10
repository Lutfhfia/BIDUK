<!DOCTYPE html>

<html lang="id">

<head>

    <meta charset="UTF-8">

    <title>Rapor - {{ $student->name }}</title>

    <style>

        @page { margin: 6mm 8mm 6mm 8mm; size: F4 portrait; }

        * { box-sizing: border-box; }

        body { margin: 0; font-family: Arial, Helvetica, sans-serif; color: #111; font-size: 8px; }

        .page { width: 100%; min-height: 310mm; position: relative; page-break-after: always; }

        .page:last-child { page-break-after: auto; }

        h1,h2,h3,p { margin: 0; }

        .title { text-align: center; font-weight: 700; font-size: 9.5px; line-height: 1.1; margin-bottom: 3px; text-transform: uppercase; }

        .sub-title { text-align: center; font-weight: 700; font-size: 10px; margin-bottom: 8px; }

        .section-title { font-weight: 700; font-size: 8.5px; line-height: 1.05; margin: 3px 0 2px; }

        .identity { width: 100%; border-collapse: collapse; margin-bottom: 3px; table-layout: fixed; }

        .identity td { vertical-align: top; padding: 1px 2px; font-size: 7.8px; line-height: 1.05; }

        .identity .label-left,

        .identity .label-right { white-space: nowrap; }

        .identity .label-left { width: 19%; }

        .identity .colon-left { width: 2%; text-align: center; }

        .identity .value-left { width: 29%; }

        .identity .label-right { width: 14%; }

        .identity .colon-right { width: 2%; text-align: center; }

        .identity .value-right { width: 34%; }

        .identity .value-line {

            min-height: 10px;

            border-bottom: 1px dotted #444;

            line-height: 1.05;

            padding-bottom: 0;

        }

        table.report { width: 100%; border-collapse: collapse; table-layout: fixed; }

        .report th, .report td {

            border: 0.6px solid #222;

            padding: 1px 2px;

            font-size: 7.8px;

            line-height: 1.05;

            vertical-align: middle;

            overflow-wrap: break-word;

            word-wrap: break-word;

        }

        .report th { text-align: center; font-weight: 700; line-height: 1.0; }

        .center { text-align: center; }

        .subject { text-align: left; }

        .muted { color: #555; }

        .blank { color: transparent; }

        .small { font-size: 8px; }

        /* BAGIAN E - Ketidakhadiran dan kenaikan. Tabel utama mengikuti susunan formulir cetak; tabel kecil hanya mengatur pilihan kenaikan, kelas, dan tanggal. */

        .section-e-block {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .attendance-promotion-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 0;
            font-size: 6.8px;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .attendance-promotion-table tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        .attendance-promotion-table th,

        .attendance-promotion-table td {

            border: 0.6px solid #222;

            box-sizing: border-box;

        }

        .attendance-promotion-table th {

            text-align: center;

            font-weight: 700;

        }

        .e-left {

            text-align: center;

            vertical-align: middle;

            font-size: 7px;

            padding: 2px;

        }

        .e-semester {

            text-align: center;

            vertical-align: middle;

            font-size: 6.8px;

            padding: 1px 2px;

            height: 15px;

        }

        .e-attendance-label,

        .e-attendance-days {

            height: 16px;

            padding: 1px 2px;

            vertical-align: middle;

            white-space: nowrap;

            font-size: 6.6px;

        }

        .e-attendance-label {

            text-align: left;

        }

        .e-attendance-days {

            text-align: center;

            border-left: 0.6px solid #222 !important;

        }

        .e-signature {

            text-align: center;

            vertical-align: top;

            font-size: 6.5px;

            padding: 8px 2px 2px;

        }

        .signature-space {

            height: 20px;

        }

        .signature-line,

        .signature-nip {

            white-space: nowrap;

        }

        .signature-nip {

            margin-top: 1px;

        }

        .promotion-left {

            text-align: center;

            vertical-align: middle;

            font-size: 7px;

            padding: 2px;

            height: 44px;

        }

        .promotion-decision {

            padding: 3px;

            vertical-align: top;

            font-size: 6.4px;

            line-height: 1.05;

            height: 44px;

        }

        .promotion-decision strong {

            font-size: 6.8px;

        }

        .promotion-result {

            padding: 0;

            vertical-align: middle;

            height: 44px;

        }

        .promotion-inner {

            width: 100%;

            border-collapse: collapse;

            table-layout: fixed;

            font-size: 6.5px;

            line-height: 1.03;

        }

        .promotion-inner td {

            border: 0;

            padding: 1px 2px;

            height: 14px;

            vertical-align: middle;

        }

        .promotion-inner .result-title {

            border-bottom: 0.6px solid #222;

            font-weight: 700;

            text-align: left;

        }

        .promotion-inner .field {

            border-bottom: 0.6px solid #222;

        }

        .promotion-inner .last {

            border-bottom: 0;

        }

        .promotion-option {
            display: inline-block;
            margin-right: 5px;
        }

        .promotion-option.selected {
            font-weight: 700;
        }

        .promotion-option.crossed {
            text-decoration: line-through;
        }

        .print-only-hidden { display: none; }

    </style>

</head>

<body>

@php

    $schoolName = $schoolProfile['name'] ?? 'SDN 204 Palembang';

    $schoolAddress = $schoolProfile['address'] ?? '';

    $gradeLevel = (int) ($class->grade_level ?? 0);
    $fase = match ($gradeLevel) {
        1, 2 => 'A',
        3, 4 => 'B',
        5, 6 => 'C',
        default => '-',
    };

    // Pilihan semester berasal dari halaman Rekap Rapot.

    $semesterMode = request('semester', 'ganjil_genap');

    $semesterLabel = match ($semesterMode) {

        'ganjil' => 'Ganjil',

        'genap' => 'Genap',

        default => 'Ganjil & Genap',

    };

    $gradeMapGanjil = $grades->get($semesterGanjil?->id, collect())->keyBy('subject_id');

    $gradeMapGenap = $grades->get($semesterGenap?->id, collect())->keyBy('subject_id');

    $promotionStatus = $promotion?->promotion_status;
    $promotionDate = $promotion?->promotion_date;
    $promotionDateLabel = $promotionDate
        ? \Illuminate\Support\Carbon::parse($promotionDate)->format('d/m/Y')
        : '........................';

@endphp

<div class="page">

    <div class="title">IV. LAPORAN HASIL CAPAIAN PEMBELAJARAN PESERTA DIDIK KURIKULUM MERDEKA SEKOLAH DASAR</div>

    <div class="section-title">A. IDENTITAS PESERTA DIDIK</div>

    <table class="identity">

        <tr>

            <td class="label-left">Nama Peserta Didik</td>

            <td class="colon-left">:</td>

            <td class="value-left"><div class="value-line">{{ $student->name }}</div></td>

            <td class="label-right">Kelas</td>

            <td class="colon-right">:</td>

            <td class="value-right"><div class="value-line">{{ $class->name }}</div></td>

        </tr>

        <tr>

            <td class="label-left">NISN / NIS</td>

            <td class="colon-left">:</td>

            <td class="value-left"><div class="value-line">{{ $student->nisn ?? '-' }} / {{ $student->nis ?? '-' }}</div></td>

            <td class="label-right">Fase</td>

            <td class="colon-right">:</td>

            <td class="value-right"><div class="value-line">{{ $fase }}</div></td>

        </tr>

        <tr>

            <td class="label-left">Nama Sekolah</td>

            <td class="colon-left">:</td>

            <td class="value-left"><div class="value-line">{{ $schoolName }}</div></td>

            <td class="label-right">Semester</td>

            <td class="colon-right">:</td>

            <td class="value-right"><div class="value-line">{{ ucfirst($semesterLabel) }}</div></td>

        </tr>

        <tr>

            <td class="label-left">Alamat</td>

            <td class="colon-left">:</td>

            <td class="value-left"><div class="value-line">{{ $schoolAddress ?: '-' }}</div></td>

            <td class="label-right">Tahun Pelajaran</td>

            <td class="colon-right">:</td>

            <td class="value-right"><div class="value-line">{{ $academicYear->name ?? '-' }}</div></td>

        </tr>

    </table>

        <div class="section-title">B. INTRAKURIKULER</div>

        <table class="report">

            <colgroup>

                <col style="width:4%"><col style="width:22%"><col style="width:8%"><col style="width:29%"><col style="width:8%"><col style="width:29%">

            </colgroup>

            <thead>

                <tr>

                    <th rowspan="2">NO.</th>

                    <th rowspan="2">MATA PELAJARAN</th>

                    <th colspan="2">SEMESTER GANJIL</th>

                    <th colspan="2">SEMESTER GENAP</th>

                </tr>

                <tr>

                    <th>NILAI AKHIR</th>

                    <th>CAPAIAN PEMBELAJARAN</th>

                    <th>NILAI AKHIR</th>

                    <th>CAPAIAN PEMBELAJARAN</th>

                </tr>

            </thead>

            <tbody>

                @foreach($subjects as $index => $subject)

                    @php

                        $g = $gradeMapGanjil->get($subject->id);

                        $e = $gradeMapGenap->get($subject->id);

                    @endphp

                    <tr>

                        <td class="center">{{ $index + 1 }}</td>

                        <td class="subject">{{ $subject->name }}</td>

                        <td class="center">{{ $g?->score ?? '' }}</td>

                        <td>{{ $g?->learning_outcome ?? '' }}</td>

                        <td class="center">{{ $e?->score ?? '' }}</td>

                        <td>{{ $e?->learning_outcome ?? '' }}</td>

                    </tr>

                @endforeach

                <tr>

                    <th colspan="2" class="center">Jumlah Nilai</th>

                    <td class="center">{{ $gradeMapGanjil->sum(fn($g) => (float)$g->score) ?: '' }}</td>

                    <td></td>

                    <td class="center">{{ $gradeMapGenap->sum(fn($g) => (float)$g->score) ?: '' }}</td>

                    <td></td>

                </tr>

                <tr>

                    <th colspan="2" class="center">RATA - RATA</th>

                    <td class="center">{{ $gradeMapGanjil->count() ? number_format($gradeMapGanjil->avg(fn($g) => (float)$g->score), 2, ',', '.') : '' }}</td>

                    <td></td>

                    <td class="center">{{ $gradeMapGenap->count() ? number_format($gradeMapGenap->avg(fn($g) => (float)$g->score), 2, ',', '.') : '' }}</td>

                    <td></td>

                </tr>

            </tbody>

        </table>

    <div class="section-title">C. EKSTRAKURIKULER</div>

    <table class="report">

        <colgroup>

            <col style="width:4%">

            <col style="width:30%">

            <col style="width:33%">

            <col style="width:33%">

        </colgroup>

        <thead>

            <tr>

                <th>NO.</th>

                <th>KEGIATAN EKSTRAKURIKULER</th>

                <th>KETERANGAN</th>

                <th>KETERANGAN</th>

            </tr>

        </thead>

        <tbody>

            @foreach(range(1,3) as $i)

                <tr>

                    <td class="center">{{ $i }}</td>

                    <td></td>

                    <td></td>

                    <td></td>

                </tr>

            @endforeach

        </tbody>

    </table>

    <div class="section-title">D. PRESTASI</div>

    <table class="report">

        <colgroup>

            <col style="width:4%">

            <col style="width:30%">

            <col style="width:33%">

            <col style="width:33%">

        </colgroup>

        <thead>

            <tr>

                <th>NO.</th>

                <th>JENIS PRESTASI</th>

                <th>KETERANGAN</th>

                <th>KETERANGAN</th>

            </tr>

        </thead>

        <tbody>

            @foreach($achievements as $index => $achievement)

                <tr>

                    <td class="center">{{ $index + 1 }}</td>

                    <td>{{ $achievement->type }}</td>

                    <td>{{ $achievement->level ?? '' }}</td>

                    <td>{{ $achievement->description ?? '' }}</td>

                </tr>

            @endforeach

            @for($i = $achievements->count() + 1; $i <= 3; $i++)

                <tr>

                    <td class="center">{{ $i }}</td>

                    <td></td>

                    <td></td>

                    <td></td>

                </tr>

            @endfor

        </tbody>

    </table>

    <div class="section-e-block">

    <div class="section-title">E. KETIDAKHADIRAN DAN KENAIKAN</div>

    <table class="attendance-promotion-table">

        <colgroup>

            <col style="width:14.2857%">

            <col style="width:14.2857%">

            <col style="width:14.2857%">

            <col style="width:14.2857%">

            <col style="width:14.2857%">

            <col style="width:14.2857%">

            <col style="width:14.2857%">

        </colgroup>

        {{-- HEADER --}}

        <tr>

            <th class="e-left" rowspan="4">

                KETIDAKHADIRAN

            </th>

            <th class="e-semester" colspan="2">

                Semester Ganjil

            </th>

            <th class="e-semester" colspan="2">

                Semester Genap

            </th>

            <td class="e-signature" rowspan="5">

    Wali Kelas

    <div class="signature-space"></div>

    <div class="signature-line">

        {{ $homeroomTeacher?->name ?: '................................' }}

    </div>

    <div class="signature-nip">

        NIP. {{ $homeroomTeacher?->nip ?: '...........................' }}

    </div>

</td>

<td class="e-signature" rowspan="5">

    Mengetahui<br>

    Kepala Sekolah

    <div class="signature-space"></div>

    <div class="signature-line">

        {{ $headmaster?->name ?: '................................' }}

    </div>

    <div class="signature-nip">

        NIP. {{ $headmaster?->nip ?: '...........................' }}

    </div>

</td>

        </tr>

        {{-- SAKIT --}}

        <tr>

            <td class="e-attendance-label">

                1. Sakit

            </td>

            <td class="e-attendance-days">

                {{ $attendanceGanjil?->sakit ?? 0 }} Hari

            </td>

            <td class="e-attendance-label">

                1. Sakit

            </td>

            <td class="e-attendance-days">

                {{ $attendanceGenap?->sakit ?? 0 }} Hari

            </td>

        </tr>

        {{-- IZIN --}}

        <tr>

            <td class="e-attendance-label">

                2. Izin

            </td>

            <td class="e-attendance-days">

                {{ $attendanceGanjil?->izin ?? 0 }} Hari

            </td>

            <td class="e-attendance-label">

                2. Izin

            </td>

            <td class="e-attendance-days">

                {{ $attendanceGenap?->izin ?? 0 }} Hari

            </td>

        </tr>

        {{-- TANPA KETERANGAN --}}

        <tr>

            <td class="e-attendance-label">

                3. Tanpa Keterangan

            </td>

            <td class="e-attendance-days">

                {{ $attendanceGanjil?->tanpa_keterangan ?? 0 }} Hari

            </td>

            <td class="e-attendance-label">

                3. Tanpa Keterangan

            </td>

            <td class="e-attendance-days">

                {{ $attendanceGenap?->tanpa_keterangan ?? 0 }} Hari

            </td>

        </tr>

        {{-- KENAIKAN --}}

        <tr>

            <th class="promotion-left">

                KENAIKAN

            </th>

            <td class="promotion-decision" colspan="2">

                <strong>Keputusan</strong><br>

                Berdasarkan Capaian Pembelajaran pada<br>

                Semester I dan II, Peserta Didik<br>

                ditetapkan

            </td>

            <td class="promotion-result" colspan="2">

                <table class="promotion-inner">
                        <tr>
                            <td class="result-title">
                                <span class="promotion-option {{ $promotionStatus === 'Naik' ? 'selected' : ($promotionStatus === 'Tidak Naik' ? 'crossed' : '') }}">Naik</span>
                                /
                                <span class="promotion-option {{ $promotionStatus === 'Tidak Naik' ? 'selected' : ($promotionStatus === 'Naik' ? 'crossed' : '') }}">Tidak Naik</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="field">
                                Ke Kelas: {{ $promotion?->promotion_class ?: '........................' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="last">
                                Tanggal: {{ $promotionDateLabel }}
                            </td>
                        </tr>
                    </table>

            </td>

        </tr>

    </table>

    </div> {{-- /.section-e-block --}}

</div>

</body>

</html>
