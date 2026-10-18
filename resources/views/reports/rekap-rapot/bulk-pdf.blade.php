<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Rapot</title>

    <style>
        @page {
            margin: 12mm 15mm;
            size: A4 portrait;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            font-size: 9px;
        }

        /*
         * =========================================================
         * PEMISAH ANTAR SISWA
         * =========================================================
         *
         * Setiap siswa dimulai dari halaman baru.
         */
        .student-report {
            display: block;
            width: 100%;
            margin: 0;
            padding: 0;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .student-report + .student-report {
            page-break-before: always !important;
            break-before: page !important;
        }

        .student-report .page {
            width: 100%;
            margin: 0;
            min-height: 0 !important;
            padding: 0 !important;
            position: relative;
            page-break-before: auto !important;
            page-break-after: auto !important;
            break-before: auto !important;
            break-after: auto !important;
        }

        .student-report.final-class-report .page {
            width: 100%;
            margin: 0;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
        }

        /*
         * =========================================================
         * TABEL
         * =========================================================
         */
        table {
            border-collapse: collapse;
        }

        /*
         * Mencegah perubahan layout akibat include template.
         */
        .student-report table {
            max-width: 100%;
        }

        /*
         * =========================================================
         * BAGIAN E
         * =========================================================
         */
        .attendance-promotion-table {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /*
         * =========================================================
         * SIGNATURE
         * =========================================================
         */
        .signature-block {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        @media screen {
            html,
            body {
                min-height: 100%;
                background: #e5e7eb;
            }

            body {
                padding: 24px 0;
            }

            .student-report {
                width: 210mm;
                min-height: 297mm;
                margin: 0 auto 24px;
                padding: 12mm 15mm;
                background: #fff;
                box-shadow: 0 2px 12px rgba(0, 0, 0, 0.2);
            }

            .student-report .page {
                width: 100%;
                min-height: 0;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>

<body>

@foreach($reports as $report)

<div class="student-report{{ !empty($report['isFinalClass']) ? ' final-class-report' : '' }}">

@if (!empty($report['isFinalClass']))
    @include('reports.rekap-rapot.kelas-6', array_merge($report, ['isBulkReport' => true]))
@else
    @include('reports.rekap-rapot.print', array_merge($report, ['isBulkReport' => true]))
@endif

</div>

@endforeach

</body>
</html>