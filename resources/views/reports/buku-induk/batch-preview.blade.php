
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Preview Buku Induk</title>

    <style>
        {!! $style !!}

        /* Tampilan kertas A4 utuh */
        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
        }

        body.pdf-export {
            width: 210mm !important;
            margin: 0 auto !important;
            padding: 10mm 0 !important;
            background: #e5e5e5 !important;
        }

        /* Setiap lembar memiliki margin kiri-kanan 11 mm */
        .pdf-export > .page {
            display: block !important;
            box-sizing: border-box !important;
            width: 210mm !important;
            max-width: 210mm !important;
            height: auto !important;
            min-height: 297mm !important;
            max-height: none !important;
            margin: 0 auto 10mm !important;
            padding: 10mm 11mm !important;
            background: #fff !important;
            box-shadow: 0 0 4px rgba(0, 0, 0, .25) !important;
            page-break-before: auto !important;
            page-break-after: always !important;
            page-break-inside: auto !important;
            overflow: visible !important;
        }

        /* Posisi foto */
        .pdf-export > .page .photo-column {
            right: 12mm !important;
        }

        .pdf-export > .page:last-child {
            page-break-after: auto !important;
            margin-bottom: 0 !important;
        }

        /* Pengaturan saat dicetak */
        @media print {
            @page {
                size: A4 portrait;
                margin: 0;
            }

            html,
            body {
                background: #fff !important;
            }

            body.pdf-export {
                width: 210mm !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .pdf-export > .page {
                width: 210mm !important;
                max-width: 210mm !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                margin: 0 !important;
                padding: 10mm 11mm !important;
                box-shadow: none !important;
                page-break-after: always !important;
                page-break-inside: auto !important;
                overflow: visible !important;
            }

            .pdf-export > .page:last-child {
                page-break-after: auto !important;
            }
        }
    </style>
</head>

<body class="pdf-export">
    @forelse ($studentPages as $studentPage)
        {!! $studentPage !!}
    @empty
        <div style="padding: 48px 24px; text-align: center; font-family: Arial, sans-serif;">
            <h2>Data siswa masih kosong</h2>
            <p>Tidak ada siswa pada kelas dan parameter yang dipilih.</p>
        </div>
    @endforelse
</body>
</html>
