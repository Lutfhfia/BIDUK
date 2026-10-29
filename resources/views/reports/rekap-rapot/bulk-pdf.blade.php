<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Rapot</title>

    <style>
        /*
         * Margin cetak dikelola melalui padding .student-report.
         * Cara ini menyamakan area cetak kelas 1-5 dan kelas 6,
         * termasuk saat DomPDF memproses template kelas 6 yang di-include.
         */
        @page {
            margin: 0;
            size: A4 portrait;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #111;
            font-size: 9px;
        }

        /*
         * Area cetak A4: 210 mm - margin kiri 15 mm - margin kanan 15 mm
         * = 180 mm. Gunakan lebar eksplisit agar DomPDF tidak menambahkan
         * padding horizontal ke width: 100% lalu memotong sisi kanan.
         */
        .student-report {
            display: block;
            width: 180mm;
            margin: 0 15mm;
            padding: 12mm 0;
            page-break-inside: auto !important;
            break-inside: auto !important;
        }

        .student-report + .student-report {
            page-break-before: always !important;
            break-before: page !important;
        }

        /*
         * Reset ukuran/margin template yang di-include agar tidak
         * menimpa margin cetak bersama, terutama untuk template kelas 6.
         */
        .student-report .page,
        .student-report.final-class-report .page {
            width: 100% !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            box-sizing: border-box !important;
            page-break-before: auto !important;
            page-break-after: auto !important;
            break-before: auto !important;
            break-after: auto !important;
            page-break-inside: auto !important;
            break-inside: auto !important;
        }

        h1,
        h2,
        h3,
        p {
            margin: 0;
        }

        table {
            border-collapse: collapse;
        }

        .student-report table {
            max-width: 100%;
        }

        .attendance-promotion-table,
        .signature-block {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* Margin fisik konsisten untuk PDF cetak semua kelas maupun per kelas. */
        @media print {
            html,
            body {
                width: 100%;
                margin: 0 !important;
                padding: 0 !important;
            }

            .student-report {
                /* Lebar konten + kedua margin tepat memenuhi A4 (210 mm). */
                width: 180mm !important;
                min-height: 0 !important;
                margin: 0 15mm !important;
                padding: 12mm 0 !important;
                box-sizing: content-box !important;
                page-break-inside: auto !important;
                break-inside: auto !important;
            }

            .student-report + .student-report {
                page-break-before: always !important;
                break-before: page !important;
            }
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

            .student-report .page,
            .student-report.final-class-report .page {
                width: 100%;
                min-height: 0;
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>

@forelse($reports as $report)
    <div class="student-report{{ !empty($report['isFinalClass']) ? ' final-class-report' : '' }}">
        @if (!empty($report['isFinalClass']))
            @include('reports.rekap-rapot.kelas-6', array_merge($report, ['isBulkReport' => true]))
        @else
            @include('reports.rekap-rapot.print', array_merge($report, ['isBulkReport' => true]))
        @endif
    </div>
@empty
    <div class="student-report">
        <h2>Data siswa masih kosong</h2>
        <p>Tidak ada siswa atau nilai siswa pada kelas dan parameter yang dipilih.</p>
    </div>
@endforelse

</body>
</html>
