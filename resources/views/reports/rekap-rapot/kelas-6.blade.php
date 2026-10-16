<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>Rekap Rapor Kelas 6 - {{ $student->name }}</title>

    <style>
        @page {
            size: F4 portrait;
            margin: 8mm 9mm 8mm 9mm;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            color: #111;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 8px;
        }

        body {
            width: 100%;
        }

        .page {
            width: 100%;
            margin: 0 auto;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        th,
        td {
            border: 0.6px solid #222;
        }

        .center {
            text-align: center;
        }

        .left {
            text-align: left;
        }

        .middle {
            vertical-align: middle;
        }

        .top {
            vertical-align: top;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
        }

        /* =========================================================
           JUDUL
        ========================================================= */

        .title {
            width: 100%;
            margin: 0 0 5px;
            font-size: 9px;
            font-weight: bold;
            line-height: 1.2;
            text-align: center;
            text-transform: uppercase;
        }

        .section-title {
            margin: 4px 0 3px;
            font-size: 7.8px;
            font-weight: bold;
            line-height: 1.2;
        }

        /* =========================================================
           IDENTITAS
        ========================================================= */

        .identity {
            width: 100%;
            margin-bottom: 4px;
            border: 0;
        }

        .identity td {
            border: 0;
            padding: 1.5px 2px;
            font-size: 7.1px;
            line-height: 1.15;
            vertical-align: top;
        }

        .identity .label {
            width: 14%;
            white-space: nowrap;
        }

        .identity .colon {
            width: 1.5%;
            text-align: center;
        }

        .identity .value {
            width: 34.5%;
            border-bottom: 0.5px dotted #555;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        /* =========================================================
           TABEL INTRAKURIKULER
        ========================================================= */

        .report-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .report-table th,
        .report-table td {
            border: 0.6px solid #222;
            padding: 2px 3px;
            font-size: 6.2px;
            line-height: 1.08;
            vertical-align: middle;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .report-table th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .report-table .number {
            width: 4%;
            text-align: center;
        }

        .report-table .subject {
            width: 21%;
            text-align: left;
        }

        .report-table .score {
            text-align: center;
            white-space: nowrap;
        }

        .report-table .outcome {
            text-align: left;
            font-size: 6px;
        }

        .report-table .group-row td {
            background: #e8e8e8;
            font-weight: bold;
            text-align: left;
        }

        .report-table .total-row td,
        .report-table .average-row td {
            background: #dceff7;
            font-weight: bold;
        }

        /* =========================================================
           BAGIAN BAWAH
           SATU LAYOUT BESAR:
           KIRI = TABEL NILAI + ABSENSI + WALI/STATUS
           KANAN = KEPUTUSAN + KEPALA SEKOLAH
        ========================================================= */

        .bottom-wrapper {
            width: 94%;
            margin: 7px auto 0;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .bottom-wrapper > tbody > tr > td {
            padding: 0;
            vertical-align: top;
        }

        .bottom-left {
            width: 73%;
            padding-right: 5px !important;
            border: 0 !important;
        }

        .bottom-right {
            width: 27%;
            padding: 5px 6px !important;
            border: 0.7px solid #222 !important;
            vertical-align: top;
        }

        /* =========================================================
           JUDUL TABEL BAWAH
        ========================================================= */

        .bottom-heading {
            margin: 0 0 3px;
            font-size: 7.3px;
            font-weight: bold;
            line-height: 1.2;
        }

        /* =========================================================
           TABEL NILAI SEKOLAH + NILAI AKHIR
        ========================================================= */

        .final-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .final-table th,
        .final-table td {
            border: 0.6px solid #222;
            padding: 2.3px 3px;
            font-size: 6.1px;
            line-height: 1.08;
            vertical-align: middle;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .final-table th {
            background: #f0f0f0;
            font-weight: bold;
            text-align: center;
        }

        .final-table .number-col {
            width: 7%;
        }

        .final-table .subject-col {
            width: 27%;
        }

        .final-table .score-col {
            width: 12%;
        }

        .final-table .outcome-col {
            width: 21%;
        }

        .final-table .subject {
            text-align: left;
        }

        .final-table .score {
            text-align: center;
            white-space: nowrap;
        }

        .final-table .outcome {
            text-align: left;
            font-size: 5.9px;
        }

        .final-table .group-row td {
            background: #e8e8e8;
            font-weight: bold;
            text-align: left;
        }

        .final-table .average-row td {
            background: #dceff7;
            font-weight: bold;
        }

        /* =========================================================
           KEPUTUSAN
           BOX KANAN MEMANJANG SEPANJANG BAGIAN BAWAH
        ========================================================= */

        .decision-title {
            margin: 0 0 8px;
            font-size: 7.2px;
            font-weight: bold;
            text-align: center;
        }

        .decision-description {
            margin: 0 0 8px;
            font-size: 6.1px;
            line-height: 1.35;
            text-align: left;
        }

        .decision-status {
            width: 100%;
            margin: 5px 0 9px;
            padding: 5px 2px;
            border: 0.7px solid #222;
            font-size: 7.3px;
            font-weight: bold;
            text-align: center;
        }

        .decision-field {
            margin: 6px 0 0;
            font-size: 6.1px;
            line-height: 1.3;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .decision-headmaster {
            margin-top: 37mm;
            font-size: 6.2px;
            line-height: 1.25;
            text-align: center;
        }

        .decision-signature-space {
            height: 17mm;
        }

        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }

        /* =========================================================
           KETIDAKHADIRAN
           MENYERUPAI FORMULIR FISIK
        ========================================================= */

        .attendance-section {
            width: 100%;
            margin-top: 5px;
        }

        .attendance-title {
            margin: 0 0 2px;
            font-size: 7px;
            font-weight: bold;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .attendance-table td {
            border: 0.6px solid #222;
            padding: 2.5px 3px;
            font-size: 6.2px;
            line-height: 1.15;
            vertical-align: middle;
        }

        .attendance-table .attendance-label {
            width: 20%;
            text-align: center;
            font-weight: bold;
        }

        .attendance-table .attendance-type {
            width: 25%;
            text-align: left;
        }

        .attendance-table .attendance-value {
            width: 55%;
            text-align: right;
            white-space: nowrap;
        }

        /* =========================================================
           TANDA TANGAN / STATUS AKHIR
        ========================================================= */

        .signature-table {
            width: 100%;
            margin-top: 5px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .signature-table td {
            border: 0;
            padding: 2px 3px;
            font-size: 6.2px;
            line-height: 1.2;
            text-align: center;
            vertical-align: top;
        }

        .signature-table .homeroom {
            width: 30%;
        }

        .signature-table .final-status {
            width: 70%;
        }

        .signature-space {
            height: 10mm;
        }

        .status-box {
            width: 100%;
            border: 0.7px solid #222;
        }

        .status-title {
            padding: 4px 2px;
            border-bottom: 0.6px solid #222;
            font-weight: bold;
            text-align: center;
        }

        .status-value {
            padding: 5px 2px;
            font-size: 7.8px;
            font-weight: bold;
            text-align: center;
        }

        .status-date {
            margin-top: 5px;
            text-align: center;
        }

        /* =========================================================
           CATATAN
        ========================================================= */

        .note {
            width: 94%;
            margin: 4px auto 0;
            color: #555;
            font-size: 5.8px;
            line-height: 1.15;
        }
    </style>
</head>

<body>

@php
    /*
    |--------------------------------------------------------------------------
    | DATA DASAR
    |--------------------------------------------------------------------------
    */

    $schoolProfile = $schoolProfile ?? [];

    $schoolName = $schoolProfile['name']
        ?? 'SDN 204 Palembang';

    $schoolAddress = $schoolProfile['address']
        ?? '';

    $ijazahMapping = $ijazahMapping ?? collect();

    $finalGrades = $finalGrades ?? collect();

    $reportAverageScores = $reportAverageScores ?? [];

    $graduationStatus = $graduationStatus ?? null;


    /*
    |--------------------------------------------------------------------------
    | MAP NILAI RAPOR
    |--------------------------------------------------------------------------
    */

    $gradeMapGanjil = $grades
        ->get($semesterGanjil?->id, collect())
        ->keyBy('subject_id');

    $gradeMapGenap = $grades
        ->get($semesterGenap?->id, collect())
        ->keyBy('subject_id');


    /*
    |--------------------------------------------------------------------------
    | FASE
    |--------------------------------------------------------------------------
    */

    $gradeLevel = (int) ($class->grade_level ?? 6);

    $fase = match ($gradeLevel) {
        1, 2 => 'A',
        3, 4 => 'B',
        5, 6 => 'C',
        default => '-',
    };


    /*
    |--------------------------------------------------------------------------
    | FORMAT NILAI
    |--------------------------------------------------------------------------
    */

    $formatScore = function ($value) {
        return ($value !== null && $value !== '')
            ? number_format((float) $value, 2, ',', '.')
            : '-';
    };


    /*
    |--------------------------------------------------------------------------
    | NILAI RATA-RATA RAPOR
    |--------------------------------------------------------------------------
    |
    | Bagian atas mengikuti mata pelajaran yang tersedia
    | pada fitur Mata Pelajaran.
    |
    */

    $reportScoreValues = $subjects
        ->map(function ($subject) use ($reportAverageScores) {
            return $reportAverageScores[$subject->id] ?? null;
        })
        ->filter(function ($value) {
            return $value !== null && $value !== '';
        })
        ->values();


    /*
    |--------------------------------------------------------------------------
    | NILAI SEKOLAH DAN NILAI AKHIR
    |--------------------------------------------------------------------------
    |
    | Bagian bawah HARUS mengikuti mapping mata pelajaran
    | dari fitur Nilai Ijazah.
    |
    */

    $mappedSchoolValues = collect();

    $mappedFinalValues = collect();

    foreach ($ijazahMapping as $mappedSubject) {

        $mappedSubjectId = $mappedSubject['subject_id'] ?? null;

        $mappedGrade = $mappedSubjectId
            ? $finalGrades->get($mappedSubjectId)
            : null;

        if (
            $mappedGrade &&
            $mappedGrade->school_score !== null &&
            $mappedGrade->school_score !== ''
        ) {
            $mappedSchoolValues->push(
                (float) $mappedGrade->school_score
            );
        }

        if (
            $mappedGrade &&
            $mappedGrade->final_score !== null &&
            $mappedGrade->final_score !== ''
        ) {
            $mappedFinalValues->push(
                (float) $mappedGrade->final_score
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | TANGGAL KELULUSAN
    |--------------------------------------------------------------------------
    */

    $graduationDateLabel = '........................';

    if ($graduationStatus?->graduation_date) {
        $graduationDateLabel =
            \Illuminate\Support\Carbon::parse(
                $graduationStatus->graduation_date
            )->format('d/m/Y');
    }


    /*
    |--------------------------------------------------------------------------
    | KETIDAKHADIRAN
    |--------------------------------------------------------------------------
    */

    $attendanceSakit =
        (int) ($attendanceGanjil?->sakit ?? 0)
        +
        (int) ($attendanceGenap?->sakit ?? 0);

    $attendanceIzin =
        (int) ($attendanceGanjil?->izin ?? 0)
        +
        (int) ($attendanceGenap?->izin ?? 0);

    $attendanceTanpaKeterangan =
        (int) ($attendanceGanjil?->tanpa_keterangan ?? 0)
        +
        (int) ($attendanceGenap?->tanpa_keterangan ?? 0);

@endphp


<div class="page">

    {{-- =========================================================
         JUDUL
    ========================================================== --}}

    <div class="title">
        IV. LAPORAN HASIL CAPAIAN PEMBELAJARAN PESERTA DIDIK
        KURIKULUM MERDEKA SEKOLAH DASAR (SD)
    </div>


    {{-- =========================================================
         A. IDENTITAS
    ========================================================== --}}

    <div class="section-title">
        A. IDENTITAS PESERTA DIDIK
    </div>

    <table class="identity">
        <tbody>

            <tr>
                <td class="label">
                    Nama Peserta Didik
                </td>

                <td class="colon">
                    :
                </td>

                <td class="value">
                    {{ $student->name }}
                </td>

                <td class="label">
                    Kelas
                </td>

                <td class="colon">
                    :
                </td>

                <td class="value">
                    {{ $class->name }}
                </td>
            </tr>


            <tr>
                <td class="label">
                    NISN / NIS
                </td>

                <td class="colon">
                    :
                </td>

                <td class="value">
                    {{ $student->nisn ?? '-' }}
                    /
                    {{ $student->nis ?? '-' }}
                </td>

                <td class="label">
                    Fase
                </td>

                <td class="colon">
                    :
                </td>

                <td class="value">
                    {{ $fase }}
                </td>
            </tr>


            <tr>
                <td class="label">
                    Nama Sekolah
                </td>

                <td class="colon">
                    :
                </td>

                <td class="value">
                    {{ $schoolName }}
                </td>

                <td class="label">
                    Semester
                </td>

                <td class="colon">
                    :
                </td>

                <td class="value">
                    Ganjil dan Genap
                </td>
            </tr>


            <tr>
                <td class="label">
                    Alamat
                </td>

                <td class="colon">
                    :
                </td>

                <td class="value">
                    {{ $schoolAddress ?: '-' }}
                </td>

                <td class="label">
                    Tahun Pelajaran
                </td>

                <td class="colon">
                    :
                </td>

                <td class="value">
                    {{ $academicYear->name ?? '-' }}
                </td>
            </tr>

        </tbody>
    </table>


    {{-- =========================================================
         B. INTRAKURIKULER
    ========================================================== --}}

    <div class="section-title">
        B. INTRAKURIKULER
    </div>

    <table class="report-table">

        <colgroup>

            <col style="width: 4%">

            <col style="width: 21%">

            <col style="width: 7%">

            <col style="width: 18%">

            <col style="width: 7%">

            <col style="width: 18%">

            <col style="width: 7%">

            <col style="width: 18%">

        </colgroup>


        <thead>

            <tr>

                <th rowspan="2">
                    NO.
                </th>

                <th rowspan="2">
                    MATA PELAJARAN
                </th>

                <th colspan="2">
                    NILAI RATA-RATA RAPOR
                </th>

                <th colspan="2">
                    NILAI UJIAN SEKOLAH
                </th>

                <th colspan="2">
                    NILAI SEKOLAH
                </th>

            </tr>


            <tr>

                <th>
                    NILAI<br>AKHIR
                </th>

                <th>
                    CAPAIAN<br>PEMBELAJARAN
                </th>

                <th>
                    NILAI<br>AKHIR
                </th>

                <th>
                    CAPAIAN<br>PEMBELAJARAN
                </th>

                <th>
                    NILAI<br>AKHIR
                </th>

                <th>
                    CAPAIAN<br>PEMBELAJARAN
                </th>

            </tr>

        </thead>


        <tbody>

            @php
                $currentTopGroup = null;
            @endphp


            @foreach ($subjects as $index => $subject)

                @php

                    $gradeGanjil =
                        $gradeMapGanjil->get($subject->id);

                    $gradeGenap =
                        $gradeMapGenap->get($subject->id);

                    $storedGrade =
                        $finalGrades->get($subject->id);

                    $reportAverage =
                        $reportAverageScores[$subject->id]
                        ?? null;

                    $reportOutcome =
                        $gradeGenap?->learning_outcome
                        ?:
                        ($gradeGanjil?->learning_outcome ?? '');

                    $subjectGroup =
                        trim(
                            (string)
                            ($subject->group_name ?? '')
                        );

                @endphp


                @if (
                    $subjectGroup !== ''
                    &&
                    $subjectGroup !== $currentTopGroup
                )

                    @php
                        $currentTopGroup =
                            $subjectGroup;
                    @endphp

                    <tr class="group-row">

                        <td colspan="8">
                            {{ $currentTopGroup }}
                        </td>

                    </tr>

                @endif


                <tr>

                    <td class="center">
                        {{ $index + 1 }}
                    </td>


                    <td class="left">
                        {{ $subject->name }}
                    </td>


                    <td class="score">
                        {{ $formatScore($reportAverage) }}
                    </td>


                    <td class="outcome">
                        {{ $reportOutcome }}
                    </td>


                    <td class="score">
                        {{ $formatScore($storedGrade?->exam_score) }}
                    </td>


                    <td class="outcome">
                    </td>


                    <td class="score">
                        {{ $formatScore($storedGrade?->school_score) }}
                    </td>


                    <td class="outcome">
                        {{ $storedGrade?->school_learning_outcome ?? '' }}
                    </td>

                </tr>

            @endforeach


            <tr class="total-row">

                <td colspan="2" class="center">
                    JUMLAH NILAI
                </td>

                <td class="score">
                    {{
                        $reportScoreValues->isNotEmpty()
                            ? $formatScore(
                                $reportScoreValues->sum()
                            )
                            : '-'
                    }}
                </td>

                <td></td>

                <td></td>

                <td></td>

                <td></td>

                <td></td>

            </tr>


            <tr class="average-row">

                <td colspan="2" class="center">
                    RATA-RATA
                </td>

                <td class="score">
                    {{
                        $reportScoreValues->isNotEmpty()
                            ? $formatScore(
                                $reportScoreValues->avg()
                            )
                            : '-'
                    }}
                </td>

                <td></td>

                <td></td>

                <td></td>

                <td></td>

                <td></td>

            </tr>

        </tbody>

    </table>


    {{-- =========================================================
         BAGIAN BAWAH
    ========================================================== --}}

    <div class="bottom-heading" style="margin-top: 6px;">
        Nilai Sekolah = Nilai Rata-Rata + Nilai Ujian Sekolah
    </div>


    <table class="bottom-wrapper">

        <tbody>

            <tr>

                {{-- =================================================
                     KOLOM KIRI
                ================================================== --}}

                <td class="bottom-left">

                    {{-- =============================================
                         TABEL NILAI SEKOLAH / NILAI AKHIR
                    ============================================== --}}

                    <table class="final-table">

                        <colgroup>

                            <col class="number-col">

                            <col class="subject-col">

                            <col class="score-col">

                            <col class="outcome-col">

                            <col class="score-col">

                            <col class="outcome-col">

                        </colgroup>


                        <thead>

                            <tr>

                                <th rowspan="2">
                                    NO.
                                </th>

                                <th rowspan="2">
                                    MATA PELAJARAN
                                </th>

                                <th colspan="2">
                                    NILAI SEKOLAH
                                </th>

                                <th colspan="2">
                                    NILAI AKHIR
                                </th>

                            </tr>


                            <tr>

                                <th>
                                    NILAI<br>AKHIR
                                </th>

                                <th>
                                    CAPAIAN<br>PEMBELAJARAN
                                </th>

                                <th>
                                    NILAI<br>AKHIR
                                </th>

                                <th>
                                    CAPAIAN<br>PEMBELAJARAN
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @php

                                $currentGroup = null;

                                $subjectNumber = 0;

                            @endphp


                            {{-- =====================================
                                 MATA PELAJARAN MENGIKUTI
                                 NILAI IJAZAH
                            ====================================== --}}

                            @foreach ($ijazahMapping as $item)

                                @php

                                    $groupName =
                                        $item['group_name']
                                        ?? null;

                                    $subjectId =
                                        $item['subject_id']
                                        ?? null;

                                    $storedGrade =
                                        $subjectId
                                            ? $finalGrades->get(
                                                $subjectId
                                            )
                                            : null;

                                @endphp


                                @if (
                                    $groupName
                                    &&
                                    $groupName !== $currentGroup
                                )

                                    @php
                                        $currentGroup =
                                            $groupName;
                                    @endphp

                                    <tr class="group-row">

                                        <td colspan="6">
                                            {{ $currentGroup }}
                                        </td>

                                    </tr>

                                @endif


                                <tr>

                                    <td class="center">
                                        {{ ++$subjectNumber }}
                                    </td>


                                    <td class="subject">
                                        {{
                                            $item['ijazah_name']
                                            ?? '-'
                                        }}
                                    </td>


                                    <td class="score">
                                        {{
                                            $formatScore(
                                                $storedGrade?->school_score
                                            )
                                        }}
                                    </td>


                                    <td class="outcome">
                                        {{
                                            $storedGrade
                                                ?->school_learning_outcome
                                            ?? ''
                                        }}
                                    </td>


                                    <td class="score">
                                        {{
                                            $formatScore(
                                                $storedGrade?->final_score
                                            )
                                        }}
                                    </td>


                                    <td class="outcome">
                                        {{
                                            $storedGrade
                                                ?->final_learning_outcome
                                            ?? ''
                                        }}
                                    </td>

                                </tr>

                            @endforeach


                            {{-- =====================================
                                 RATA-RATA
                            ====================================== --}}

                            <tr class="average-row">

                                <td colspan="2" class="center">
                                    RATA-RATA
                                </td>


                                <td class="score">
                                    {{
                                        $mappedSchoolValues->isNotEmpty()
                                            ? $formatScore(
                                                $mappedSchoolValues->avg()
                                            )
                                            : '-'
                                    }}
                                </td>


                                <td></td>


                                <td class="score">
                                    {{
                                        $mappedFinalValues->isNotEmpty()
                                            ? $formatScore(
                                                $mappedFinalValues->avg()
                                            )
                                            : '-'
                                    }}
                                </td>


                                <td></td>

                            </tr>

                        </tbody>

                    </table>


                    {{-- =============================================
                         KETIDAKHADIRAN
                    ============================================== --}}

                    <div class="attendance-section">

                        <div class="attendance-title">
                            KETIDAKHADIRAN
                        </div>


                        <table class="attendance-table">

                            <colgroup>

                                <col style="width: 20%">

                                <col style="width: 25%">

                                <col style="width: 55%">

                            </colgroup>


                            <tbody>

                                <tr>

                                    <td
                                        rowspan="3"
                                        class="attendance-label"
                                    >
                                        KETIDAKHADIRAN
                                    </td>


                                    <td class="attendance-type">
                                        1. Sakit
                                    </td>


                                    <td class="attendance-value">
                                        {{ $attendanceSakit }} Hari
                                    </td>

                                </tr>


                                <tr>

                                    <td class="attendance-type">
                                        2. Izin
                                    </td>


                                    <td class="attendance-value">
                                        {{ $attendanceIzin }} Hari
                                    </td>

                                </tr>


                                <tr>

                                    <td class="attendance-type">
                                        3. Tanpa Keterangan
                                    </td>


                                    <td class="attendance-value">
                                        {{ $attendanceTanpaKeterangan }} Hari
                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>


                    {{-- =============================================
                         WALI KELAS + STATUS AKHIR
                    ============================================== --}}

                    <table class="signature-table">

                        <tbody>

                            <tr>

                                {{-- WALI KELAS --}}

                                <td class="homeroom">

                                    Wali Kelas


                                    <div class="signature-space">
                                    </div>


                                    <div class="signature-name">

                                        {{
                                            $homeroomTeacher?->name
                                            ?: '................................'
                                        }}

                                    </div>


                                    <div>

                                        NIP.
                                        {{
                                            $homeroomTeacher?->nip
                                            ?: '............................'
                                        }}

                                    </div>

                                </td>


                                {{-- STATUS AKHIR --}}

                                <td class="final-status">

                                    <div class="status-box">

                                        <div class="status-title">
                                            STATUS AKHIR
                                        </div>


                                        <div class="status-value">

                                            {{
                                                $graduationStatus?->status
                                                ?: 'BELUM DITETAPKAN'
                                            }}

                                        </div>

                                    </div>


                                    <div class="status-date">

                                        Tanggal:
                                        {{ $graduationDateLabel }}

                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </td>


                {{-- =================================================
                     KOLOM KANAN: KEPUTUSAN
                ================================================== --}}

                <td class="bottom-right">

                    <div class="decision-title">
                        Keputusan:
                    </div>


                    <div class="decision-description">

                        Berdasarkan hasil belajar yang dicapai,
                        peserta didik ditetapkan:

                    </div>


                    <div class="decision-status">

                        {{
                            $graduationStatus?->status
                            ?: 'BELUM DITETAPKAN'
                        }}

                    </div>


                    <div class="decision-field">

                        No. Ijazah:
                        {{
                            $graduationStatus?->certificate_number
                            ?: '........................'
                        }}

                    </div>


                    <div class="decision-field">

                        No. SKHUS:
                        {{
                            $graduationStatus?->skhus_number
                            ?: '........................'
                        }}

                    </div>


                    <div class="decision-field">

                        Tgl / Bin / Thn:
                        {{ $graduationDateLabel }}

                    </div>


                    {{-- =============================================
                         KEPALA SEKOLAH
                    ============================================== --}}

                    <div class="decision-headmaster">

                        Mengetahui
                        <br>
                        Kepala Sekolah


                        <div class="decision-signature-space">
                        </div>


                        <div class="signature-name">

                            {{
                                $headmaster?->name
                                ?: '................................'
                            }}

                        </div>


                        <div>

                            NIP.
                            {{
                                $headmaster?->nip
                                ?: '............................'
                            }}

                        </div>

                    </div>

                </td>

            </tr>

        </tbody>

    </table>


    {{-- =========================================================
         CATATAN
    ========================================================== --}}

    <div class="note">

        Catatan: Nilai yang belum tersedia ditampilkan sebagai tanda "-".
        Kolom Nilai Ujian Sekolah Standar Daerah tidak digunakan.

    </div>

</div>

</body>
</html>