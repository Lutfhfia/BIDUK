<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Rapot</title>

    <style>
        @page {
            margin: 10mm 9mm 10mm 9mm;
            size: F4 portrait;
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
            page-break-inside: auto !important;
            break-inside: auto !important;
        }

        .student-report + .student-report {
            page-break-before: always !important;
            break-before: page !important;
        }

        /*
         * Template print.blade.php memiliki .page.
         */
        .student-report .page {
            width: 100%;
            min-height: 310mm;
            position: relative;

            /*
             * Jangan paksa page-break di sini.
             * Pemisah siswa ditangani oleh .student-report.
             */
            page-break-before: auto !important;
            page-break-after: auto !important;
            break-before: auto !important;
            break-after: auto !important;
        }

        /*
         * Siswa kedua dan seterusnya selalu mulai halaman baru.
         */
        .student-report + .student-report .page {
            page-break-before: always !important;
            break-before: page !important;
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
    </style>
</head>

<body>

@foreach($reports as $report)

    <div class="student-report">

        {{-- =====================================================
             GUNAKAN TEMPLATE YANG SAMA DENGAN CETAK INDIVIDU
             ===================================================== --}}
        @include('reports.rekap-rapot.print', $report)

    </div>

@endforeach

</body>
</html>