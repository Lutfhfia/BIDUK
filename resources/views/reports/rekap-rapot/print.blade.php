<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor - {{ $student->name }}</title>
    <style>
        @page { margin: 10mm 9mm 10mm 9mm; size: F4 portrait; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Arial, Helvetica, sans-serif; color: #111; font-size: 9px; }
        .page { width: 100%; min-height: 310mm; position: relative; page-break-after: always; }
        .page:last-child { page-break-after: auto; }
        h1,h2,h3,p { margin: 0; }
        .title { text-align: center; font-weight: 700; font-size: 11px; margin-bottom: 5px; text-transform: uppercase; }
        .sub-title { text-align: center; font-weight: 700; font-size: 10px; margin-bottom: 8px; }
        .section-title { font-weight: 700; font-size: 9.5px; margin: 6px 0 3px; }
        .identity { width: 100%; border-collapse: collapse; margin-bottom: 6px; table-layout: fixed; }
        .identity td { vertical-align: top; padding: 1.5px 2px; }
        .identity .label-left,
        .identity .label-right { white-space: nowrap; }
        .identity .label-left { width: 19%; }
        .identity .colon-left { width: 2%; text-align: center; }
        .identity .value-left { width: 29%; }
        .identity .label-right { width: 14%; }
        .identity .colon-right { width: 2%; text-align: center; }
        .identity .value-right { width: 34%; }
        .identity .value-line {
            min-height: 13px;
            border-bottom: 1px dotted #444;
            line-height: 1.25;
            padding-bottom: 1px;
        }
        table.report { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .report th, .report td {
            border: 0.6px solid #222;
            padding: 2px 3px;
            vertical-align: middle;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }
        .report th { text-align: center; font-weight: 700; }
        .center { text-align: center; }
        .subject { text-align: left; }
        .muted { color: #555; }
        .blank { color: transparent; }
        .small { font-size: 8px; }
        /* =========================================================
           BAGIAN E - KETIDAKHADIRAN DAN KENAIKAN
           Struktur dibuat 1 tabel utama agar hasil DomPDF stabil
           dan mengikuti susunan pada template fisik.
        ========================================================== */
                /* =========================================================
           BAGIAN E - KETIDAKHADIRAN DAN KENAIKAN
           Struktur dibuat tanpa nested table agar garis pembatas
           Ganjil/Genap -> Hari tetap lurus saat dirender DomPDF.
        ========================================================== */
        .attendance-promotion-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 0;
            font-size: 7.2px;
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
            font-size: 7.4px;
            padding: 3px;
        }

        .e-semester {
            text-align: center;
            vertical-align: middle;
            font-size: 7.3px;
            padding: 2px 3px;
            height: 18px;
        }

        .e-attendance-label,
        .e-attendance-days {
            height: 20px;
            padding: 2px 3px;
            vertical-align: middle;
            white-space: nowrap;
            font-size: 7.1px;
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
            font-size: 7.2px;
            padding: 20px 3px 3px;
        }

        .signature-space {
            height: 34px;
        }

        .signature-line,
        .signature-nip {
            white-space: nowrap;
        }

        .signature-nip {
            margin-top: 2px;
        }

        .promotion-left {
            text-align: center;
            vertical-align: middle;
            font-size: 7.4px;
            padding: 3px;
            height: 54px;
        }

        .promotion-decision {
            padding: 5px 5px;
            vertical-align: top;
            font-size: 6.9px;
            line-height: 1.18;
            height: 54px;
        }

        .promotion-decision strong {
            font-size: 7.2px;
        }

        .promotion-result {
            padding: 0;
            vertical-align: middle;
            height: 54px;
        }

        .promotion-inner {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 6.9px;
            line-height: 1.12;
        }

        .promotion-inner td {
            border: 0;
            padding: 2px 3px;
            height: 18px;
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

        .print-only-hidden { display: none; }
    </style>
</head>
<body>
@php
    $schoolName = $schoolProfile['name'] ?? 'SDN 204 Palembang';
    $schoolAddress = $schoolProfile['address'] ?? '';
    $fase = $class->fase ?? $class->phase ?? '-';

    // Pilihan semester berasal dari halaman Rekap Rapot.
    $semesterMode = request('semester', 'ganjil_genap');

    $semesterLabel = match ($semesterMode) {
        'ganjil' => 'Ganjil',
        'genap' => 'Genap',
        default => 'Ganjil & Genap',
    };

    $gradeMapGanjil = $grades->get($semesterGanjil?->id, collect())->keyBy('subject_id');
    $gradeMapGenap = $grades->get($semesterGenap?->id, collect())->keyBy('subject_id');
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
            <td class="value-left"><div class="value-line">{{ $schoolAddress ?: ($student->address ?? '-') }}</div></td>
            <td class="label-right">Tahun Pelajaran</td>
            <td class="colon-right">:</td>
            <td class="value-right"><div class="value-line">{{ $academicYear->name ?? '-' }}</div></td>
        </tr>
    </table>

    @if(!$isFinalClass)
        <div class="section-title">B. INTRAKURIKULER</div>
        <table class="report">
            <colgroup>
                <col style="width:5%"><col style="width:22%"><col style="width:10%"><col style="width:27%"><col style="width:10%"><col style="width:26%">
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
    @else
        <div class="section-title">B. INTRAKURIKULER</div>
        <table class="report">
            <colgroup>
                <col style="width:5%"><col style="width:22%"><col style="width:11%"><col style="width:25%"><col style="width:11%"><col style="width:26%">
            </colgroup>
            <thead>
                <tr>
                    <th rowspan="2">NO.</th>
                    <th rowspan="2">MATA PELAJARAN</th>
                    <th colspan="2">NILAI RATA-RATA RAPORT</th>
                    <th colspan="2">NILAI UJIAN SEKOLAH</th>
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
                        $scores = collect([$g?->score, $e?->score])->filter(fn($v) => $v !== null && $v !== '');
                        $average = $scores->count() ? $scores->avg() : null;
                        $outcomes = collect([$g?->learning_outcome, $e?->learning_outcome])->filter()->values();
                    @endphp
                    <tr>
                        <td class="center">{{ $index + 1 }}</td>
                        <td class="subject">{{ $subject->name }}</td>
                        <td class="center">{{ $average !== null ? number_format($average, 2, ',', '.') : '' }}</td>
                        <td>{{ $outcomes->join(' / ') }}</td>
                        <td class="center"></td>
                        <td></td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="2">RATA - RATA</th>
                    <td class="center">
                        @php
                            $finalScores = $subjects->map(function($subject) use ($gradeMapGanjil, $gradeMapGenap) {
                                $vals = collect([
                                    $gradeMapGanjil->get($subject->id)?->score,
                                    $gradeMapGenap->get($subject->id)?->score,
                                ])->filter(fn($v) => $v !== null && $v !== '');
                                return $vals->count() ? $vals->avg() : null;
                            })->filter(fn($v) => $v !== null);
                        @endphp
                        {{ $finalScores->count() ? number_format($finalScores->avg(), 2, ',', '.') : '' }}
                    </td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>

        <div class="footer-note">Nilai Ujian Sekolah dan Nilai Sekolah belum ditampilkan karena sumber data tersebut belum tersedia pada struktur database BIDUK saat ini.</div>
    @endif

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
                    ................................
                </div>

                <div class="signature-nip">
                    NIP. ...........................
                </div>
            </td>

            <td class="e-signature" rowspan="5">
                Mengetahui<br>
                Kepala Sekolah

                <div class="signature-space"></div>

                <div class="signature-line">
                    ................................
                </div>

                <div class="signature-nip">
                    NIP. ...........................
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
                            Naik / Tidak Naik
                        </td>
                    </tr>
                    <tr>
                        <td class="field">
                            Ke Kelas: ........................
                        </td>
                    </tr>
                    <tr>
                        <td class="last">
                            Tanggal: ........................
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

    </table>

</div>
</body>
</html>
