<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Rapot</title>
    <style>
        @page { margin: 10mm 9mm 10mm 9mm; size: F4 portrait; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            font-size: 9px;
        }

        /* =========================================================
           PENTING UNTUK CETAK BATCH
           Pemisah siswa DITARUH DI LEVEL LOOP $reports, bukan
           mengandalkan .page + .page di dalam _page.blade.php.

           Alasan:
           1 siswa dapat memanjang menjadi 2 halaman fisik.
           DomPDF dapat mengabaikan page-break pada elemen yang
           sedang terfragmentasi. Dengan wrapper siswa di bawah,
           siswa berikutnya selalu dimulai dari halaman baru.
        ========================================================== */
        .student-report {
            display: block;
            width: 100%;
            margin: 0;
            padding: 0;
            page-break-inside: auto !important;
            break-inside: auto !important;
        }

        .student-report + .student-report {
            page-break-before: always !important;
            break-before: page !important;
        }

        /*
         * _page.blade.php memiliki class .page.
         * Pada mode batch kita TIDAK menggunakan page-break-after
         * dari .page karena satu siswa bisa menempati > 1 halaman.
         * Pemisah siswa ditangani oleh .student-report di atas.
         */
        .student-report .page {
            width: 100%;
            min-height: 310mm;
            position: relative;
            page-break-before: auto !important;
            page-break-after: auto !important;
            break-before: auto !important;
            break-after: auto !important;
        }

        h1,h2,h3,p { margin: 0; }

        .title {
            text-align: center;
            font-weight: 700;
            font-size: 11px;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .sub-title {
            text-align: center;
            font-weight: 700;
            font-size: 10px;
            margin-bottom: 8px;
        }

        .section-title {
            font-weight: 700;
            font-size: 9.5px;
            margin: 6px 0 3px;
        }

        .identity {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            table-layout: fixed;
        }

        .identity td {
            vertical-align: top;
            padding: 1.5px 2px;
        }

        .identity .label-left,
        .identity .label-right {
            white-space: nowrap;
        }

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

        table.report {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .report th,
        .report td {
            border: 0.6px solid #222;
            padding: 2px 3px;
            vertical-align: middle;
            overflow-wrap: break-word;
            word-wrap: break-word;
        }

        .report th {
            text-align: center;
            font-weight: 700;
        }

        .center { text-align: center; }
        .subject { text-align: left; }
        .muted { color: #555; }
        .blank { color: transparent; }
        .small { font-size: 8px; }

        /* =========================================================
           BAGIAN E - KETIDAKHADIRAN DAN KENAIKAN
        ========================================================== */
        .attendance-promotion-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 0;
            font-size: 7.2px;
            page-break-inside: avoid;
            break-inside: avoid;
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
            font-size: 7.1px;
            white-space: nowrap;
        }

        .e-attendance-label { text-align: left; }

        .e-attendance-days {
            text-align: center;
            border-left: 0.6px solid #222 !important;
        }

        .e-signature {
            text-align: center;
            vertical-align: top !important;
            font-size: 7.2px;
            padding: 0 3px;
        }

        .signature-block {
            padding-top: 20px;
            text-align: center;
        }

        .signature-space { height: 34px; }

        .signature-line,
        .signature-nip { white-space: nowrap; }

        .signature-nip { margin-top: 2px; }

        .promotion-left {
            text-align: center;
            vertical-align: middle;
            font-size: 7.4px;
            padding: 3px;
            height: 54px;
        }

        .promotion-decision {
            padding: 4px 4px;
            vertical-align: top;
            font-size: 6.9px;
            line-height: 1.15;
            height: 54px;
        }

        .promotion-decision strong { font-size: 7.2px; }

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
            line-height: 1.1;
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

        .promotion-inner .field { border-bottom: 0.6px solid #222; }
        .promotion-inner .last { border-bottom: 0; }
        .print-only-hidden { display: none; }
    </style>
</head>
<body>
@foreach($reports as $report)
    <div class="student-report">
        @include('reports.rekap-rapot._page', $report)
    </div>
@endforeach
</body>
</html>
