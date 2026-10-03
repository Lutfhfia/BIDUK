<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>
        Lembar Data Peserta Didik
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
            background: #e5e5e5;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            font-size: 8.5pt;
        }

        /* =====================================================
           HALAMAN
        ===================================================== */

        .page {
            width: 210mm;
            min-height: 297mm;
            margin: 20px auto;
            padding: 10mm 11mm;
            background: #fff;
            position: relative;
            page-break-after: always;
        }

        .page:last-child {
            page-break-after: auto;
        }

        /* =====================================================
           JUDUL HALAMAN 1
        ===================================================== */

        .main-title {
            text-align: center;
            font-size: 10pt;
            font-weight: bold;
            margin: 0 0 5px 0;
        }

        /* =====================================================
           IDENTITAS ATAS
        ===================================================== */

        .top-information {
            display: grid;
            grid-template-columns: 1fr 1fr 65px;
            column-gap: 12px;
            margin-bottom: 4px;
        }

        .top-column {
            width: 100%;
        }

        .top-row {
            display: grid;
            grid-template-columns: 140px 8px minmax(0, 1fr);
            align-items: baseline;
            min-height: 15px;
            line-height: 1.2;
        }

        .top-column:nth-child(2) .top-row {
            grid-template-columns: 125px 8px minmax(0, 1fr);
        }

        .top-label {
            white-space: nowrap;
        }

        .top-colon {
            text-align: center;
        }

        .top-value {
            flex: 1;
            border-bottom: 1px dotted #000;
            min-height: 13px;
            padding-left: 4px;
        }

        .nomor-urut {
            border: 1px solid #000;
            text-align: center;
            height: 36px;
            font-size: 7.5pt;
        }

        .nomor-urut-title {
            padding: 4px 2px 2px;
            font-weight: bold;
        }

        .nomor-urut-value {
            height: 16px;
        }

        /* =====================================================
           JUDUL BAGIAN
        ===================================================== */

        .section-title {
            font-weight: bold;
            margin-top: 4px;
            margin-bottom: 2px;
        }

        /* =====================================================
           BARIS DATA TANPA TABEL (BAGIAN A, B, C)
        ===================================================== */

        .data-row,
        .parent-row,
        .development-row {
            display: grid;
            grid-template-columns: 22px 195px 8px minmax(0, 1fr);
            align-items: flex-start;
            min-height: 15px;
            line-height: 1.25;
            margin-bottom: 1px;
        }

        .data-number,
        .parent-number,
        .development-number {
            width: 22px;
            flex-shrink: 0;
            text-align: left;
            white-space: nowrap;
        }

        .data-label,
        .parent-label,
        .development-label {
            width: 195px;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .data-colon,
        .parent-colon,
        .development-colon {
            width: 8px;
            flex-shrink: 0;
            text-align: center;
        }

        .data-value,
        .parent-value,
        .development-value {
            flex: 1;
            min-height: 14px;
            border-bottom: 1px dotted #000;
            padding-left: 4px;
            word-wrap: break-word;
        }

        .data-value.no-line,
        .parent-value.no-line,
        .development-value.no-line {
            border-bottom: none;
        }

        /* Sub-item a, b, c (Level 2) indent 12px, sub-label 191px -> Colons remain locked at 217px */
        .sub-row,
        .parent-sub,
        .development-sub {
            grid-template-columns: 26px 191px 8px minmax(0, 1fr);
        }

        .sub-row .data-number,
        .parent-sub .parent-number,
        .development-sub .development-number {
            width: 26px;
            padding-left: 12px;
            box-sizing: border-box;
        }

        .sub-row .data-label,
        .parent-sub .parent-label,
        .development-sub .development-label {
            width: 191px;
        }

        /* Sub-sub-item 1, 2, 3 under a, b (Level 3) indent 24px, sub-label 179px -> Colons remain locked at 217px */
        .sub-sub-row,
        .development-sub-sub {
            grid-template-columns: 38px 179px 8px minmax(0, 1fr);
        }

        .sub-sub-row .data-number,
        .development-sub-sub .development-number {
            width: 38px;
            padding-left: 24px;
            box-sizing: border-box;
        }

        .sub-sub-row .data-label,
        .development-sub-sub .development-label {
            width: 179px;
        }

        /* =====================================================
           LAYOUT A + FOTO
        ===================================================== */

        .student-section {
            position: relative;
            padding-right: 36mm;
        }

        .student-data {
            width: 100%;
        }

        .photo-column {
            position: absolute;
            top: 2px;
            right: 0;
            width: 30mm;
        }

        .photo-box {
            width: 30mm;
            height: 40mm;
            border: 1px solid #000;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 7pt;
            line-height: 1.2;
        }

        .photo-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-caption {
            width: 30mm;
            margin: 2mm auto 7mm;
            text-align: center;
            font-size: 6pt;
            line-height: 1.15;
        }

        /* =====================================================
           BAGIAN B & C
        ===================================================== */

        .parent-section {
            margin-top: 2px;
            padding-right: 36mm;
        }

        .development {
            padding-right: 36mm;
        }

        /* =====================================================
           HALAMAN 2 (BAGIAN D)
        ===================================================== */

        .page-two-section {
            margin-bottom: 5px;
        }

        .school-leaving {
            width: 100%;
        }

        .school-row {
            display: grid;
            grid-template-columns: 22px 190px 8px minmax(0, 1fr);
            min-height: 15px;
            line-height: 1.25;
            margin-bottom: 1px;
        }

        .school-number {
            width: 22px;
            flex-shrink: 0;
            text-align: left;
            white-space: nowrap;
        }

        .school-label {
            width: 190px;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .school-colon {
            width: 8px;
            flex-shrink: 0;
            text-align: center;
        }

        .school-value {
            flex: 1;
            border-bottom: 1px dotted #000;
            min-height: 14px;
            padding-left: 4px;
            word-wrap: break-word;
        }

        .school-value.no-line {
            border-bottom: none;
        }

        /* Sub-item a, b, c indent 12px, sub-label 186px -> Colons remain locked at 212px */
        .school-sub {
            grid-template-columns: 26px 186px 8px minmax(0, 1fr);
        }

        .school-sub .school-number {
            width: 26px;
            padding-left: 12px;
            box-sizing: border-box;
        }

        .school-sub .school-label {
            width: 186px;
        }

        /* =====================================================
           BAGIAN E
        ===================================================== */

        .other-title {
            font-weight: bold;
            margin-top: 8px;
            margin-bottom: 10px;
        }

        .other-subtitle {
            font-weight: bold;
            margin-bottom: 5px;
        }

        /* =====================================================
           TABEL TINGGI BERAT
        ===================================================== */

        .health-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 6.7pt;
        }

        .health-table th,
        .health-table td {
            border: 1px solid #000;
            padding: 2px 1px;
            text-align: center;
            vertical-align: middle;
        }

        .health-table th {
            font-weight: normal;
        }

        .health-table .no-column {
            width: 28px;
        }

        .health-table .aspect-column {
            width: 72px;
        }

        .health-table .year-column {
            width: calc((100% - 100px) / 6);
        }

        .year-title {
            height: 29px;
            line-height: 1.1;
        }

        .semester-row {
            height: 27px;
        }

        .semester-cell {
            width: 50%;
            line-height: 1.05;
        }

        .body-row {
            height: 34px;
        }

        .body-value {
            line-height: 1.1;
        }

        /* =====================================================
           TABEL KONDISI KESEHATAN
        ===================================================== */

        .condition-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 6.8pt;
        }

        .condition-table th,
        .condition-table td {
            border: 1px solid #000;
            padding: 2px 1px;
            text-align: center;
            vertical-align: middle;
        }

        .condition-table .no-column {
            width: 28px;
        }

        .condition-table .aspect-column {
            width: 72px;
        }

        .condition-table .year-column {
            width: calc((100% - 100px) / 6);
        }

        .condition-year {
            height: 28px;
            line-height: 1.1;
        }

        .condition-subheader {
            height: 17px;
            line-height: 1;
            font-weight: normal;
        }

        .condition-body {
            height: 27px;
        }

        /* =====================================================
           JARAK ANTAR BLOK
        ===================================================== */

        .table-gap {
            height: 9px;
        }

        /* =====================================================
           PRINT
        ===================================================== */

        @media print {

            html,
            body {
                background: #fff;
            }

            .page {
                width: 100%;
                min-height: auto;
                margin: 0;
                padding: 14mm;
                background: #fff;
                box-shadow: none;
            }
        }

        @media screen {

            .page {
                box-shadow: 0 0 4px rgba(0, 0, 0, 0.25);
            }
        }


        /* =====================================================
           PDF EXPORT - DOMPDF
           A4 PORTRAIT
        ===================================================== */

        @page {
            size: A4 portrait;
            margin: 0;
        }

        .pdf-export {
            width: 210mm !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }

        /*
         * SATU .page = SATU halaman A4.
         * Lebar 190mm dengan margin: 0 auto memberikan margin kertas 10mm di kiri dan kanan
         * sehingga tidak ada elemen yang terpotong di tepi kanan.
         */
        .pdf-export .page {
            box-sizing: border-box !important;

            width: 190mm !important;
            max-width: 190mm !important;
            height: auto !important;
            min-height: auto !important;
            max-height: 297mm !important;

            margin: 0 auto !important;
            padding: 6mm 0 !important;

            background: #fff !important;
            box-shadow: none !important;

            position: relative !important;

            page-break-before: auto !important;
            page-break-after: always !important;
            page-break-inside: avoid !important;

            overflow: hidden !important;
        }

        .pdf-export .page:last-child {
            page-break-after: auto !important;
        }

        /* =====================================================
           BATAS KONTEN
        ===================================================== */

        .pdf-export .page > * {
            max-width: 100% !important;
        }

        .pdf-export table {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            table-layout: fixed !important;
        }

        .pdf-export th,
        .pdf-export td {
            box-sizing: border-box !important;
            max-width: 100% !important;
            overflow: hidden !important;
            word-wrap: break-word !important;
        }

        /* =====================================================
           IDENTITAS ATAS
        ===================================================== */

        .pdf-export .top-information {
            width: 100% !important;
            max-width: 100% !important;
            display: table !important;
            table-layout: fixed !important;
            margin-bottom: 3mm !important;
        }

        .pdf-export .top-column,
        .pdf-export .nomor-urut {
            display: table-cell !important;
            vertical-align: top !important;
            box-sizing: border-box !important;
        }

        .pdf-export .top-column:first-child {
            width: 44% !important;
            padding-right: 2mm !important;
        }

        .pdf-export .top-column:nth-child(2) {
            width: 42% !important;
            padding-right: 2mm !important;
        }

        .pdf-export .nomor-urut {
            width: 14% !important;
            max-width: 14% !important;
            border: 1px solid #000 !important;
            text-align: center !important;
            height: 36px !important;
            font-size: 7.5pt !important;
            overflow: hidden !important;
        }

        .pdf-export .top-row {
            display: block !important;
            clear: both !important;
            width: 100% !important;
            min-height: 14px !important;
            line-height: 1.2 !important;
            margin-bottom: 1px !important;
        }

        .pdf-export .top-label {
            display: inline-block !important;
            float: left !important;
            width: 140px !important;
            white-space: nowrap !important;
            box-sizing: border-box !important;
        }

        .pdf-export .top-column:nth-child(2) .top-label {
            width: 125px !important;
        }

        .pdf-export .top-colon {
            display: inline-block !important;
            float: left !important;
            width: 8px !important;
            text-align: center !important;
            box-sizing: border-box !important;
        }

        .pdf-export .top-value {
            display: block !important;
            margin-left: 148px !important;
            border-bottom: 1px dotted #000 !important;
            min-height: 13px !important;
            padding-left: 4px !important;
            box-sizing: border-box !important;
            overflow: hidden !important;
        }

        .pdf-export .top-column:nth-child(2) .top-value {
            margin-left: 133px !important;
        }

        /* =====================================================
           DATA SISWA (HALAMAN 1: BAGIAN A, B, C)
           Photo column menggunakan position:absolute relatif
           ke .page sehingga KELUAR dari document flow.
           Data dibatasi 154mm agar tidak masuk area foto.
        ===================================================== */

        /*
         * .student-section = position:static → photo di dalamnya
         *   akan di-resolve relatif ke .page (position:relative).
         * .student-data = display:block, width 154mm → data
         *   dan garis titik-titik BERHENTI di 154mm.
         * .photo-column = position:absolute, top:26mm, right:0
         *   → sejajar NOMOR URUT, TIDAK mempengaruhi tinggi.
         */
        .pdf-export .student-section {
            display: block !important;
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
            padding-right: 0 !important;
            margin-bottom: 1mm !important;
            position: static !important;
        }

        .pdf-export .student-data {
            display: block !important;
            width: 154mm !important;
            max-width: 154mm !important;
            min-width: 0 !important;
            box-sizing: border-box !important;
        }

        /* Bagian B dan C: lebar sama dengan area data Bagian A
           agar konsisten. Margin rapat supaya muat di halaman 1. */
        .pdf-export .parent-section,
        .pdf-export .development {
            width: 154mm !important;
            max-width: 154mm !important;
            box-sizing: border-box !important;
            padding-right: 0 !important;
            margin-bottom: 1mm !important;
        }

        .pdf-export .data-row,
        .pdf-export .parent-row,
        .pdf-export .development-row {
            display: block !important;
            clear: both !important;
            width: 100% !important;
            max-width: 100% !important;
            min-height: 14px !important;
            line-height: 1.25 !important;
            margin-bottom: 0.5mm !important;
            box-sizing: border-box !important;
        }

        /* Kolom nomor rapat */
        .pdf-export .data-number,
        .pdf-export .parent-number,
        .pdf-export .development-number {
            display: inline-block !important;
            float: left !important;
            width: 22px !important;
            max-width: 22px !important;
            text-align: left !important;
            white-space: nowrap !important;
            box-sizing: border-box !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Kolom label pas untuk label terpanjang */
        .pdf-export .data-label,
        .pdf-export .parent-label,
        .pdf-export .development-label {
            display: inline-block !important;
            float: left !important;
            width: 195px !important;
            max-width: 195px !important;
            white-space: nowrap !important;
            box-sizing: border-box !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Kolom titik dua sejajar */
        .pdf-export .data-colon,
        .pdf-export .parent-colon,
        .pdf-export .development-colon {
            display: inline-block !important;
            float: left !important;
            width: 8px !important;
            max-width: 8px !important;
            text-align: center !important;
            box-sizing: border-box !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Kolom data/nilai — mengikuti lebar kolom induk */
        .pdf-export .data-value,
        .pdf-export .parent-value,
        .pdf-export .development-value {
            display: block !important;
            margin-left: 225px !important;
            border-bottom: 1px dotted #000 !important;
            min-height: 13px !important;
            padding-left: 4px !important;
            box-sizing: border-box !important;
            word-wrap: break-word !important;
        }

        .pdf-export .data-value.no-line,
        .pdf-export .parent-value.no-line,
        .pdf-export .development-value.no-line {
            border-bottom: none !important;
        }

        /* Sub-nomor a, b, c (Level 2) indent 12px, label 191px -> Titik dua tetap terkunci di 225px */
        .pdf-export .sub-row .data-number,
        .pdf-export .parent-sub .parent-number,
        .pdf-export .development-sub .development-number {
            width: 26px !important;
            max-width: 26px !important;
            padding-left: 12px !important;
        }

        .pdf-export .sub-row .data-label,
        .pdf-export .parent-sub .parent-label,
        .pdf-export .development-sub .development-label {
            width: 191px !important;
            max-width: 191px !important;
        }

        /* Sub-sub-nomor 1, 2, 3 under a, b (Level 3) indent 24px, label 179px -> Titik dua tetap terkunci di 225px */
        .pdf-export .sub-sub-row .data-number,
        .pdf-export .development-sub-sub .development-number {
            width: 38px !important;
            max-width: 38px !important;
            padding-left: 24px !important;
        }

        .pdf-export .sub-sub-row .data-label,
        .pdf-export .development-sub-sub .development-label {
            width: 179px !important;
            max-width: 179px !important;
        }

        /* =====================================================
           FOTO — position:absolute relative to .page
           Berada di kanan atas, sejajar NOMOR URUT.
           TIDAK mempengaruhi tinggi konten halaman.
        ===================================================== */

        .pdf-export .photo-column {
            position: absolute !important;
            top: 26mm !important;
            right: 2mm !important;

            width: 30mm !important;
            max-width: 30mm !important;

            box-sizing: border-box !important;
        }

        .pdf-export .photo-box {
            width: 30mm !important;
            height: 40mm !important;
            max-width: 30mm !important;

            box-sizing: border-box !important;
            border: 1px solid #000 !important;
            margin: 0 auto !important;
            text-align: center !important;
            font-size: 7pt !important;
            line-height: 1.2 !important;
        }

        .pdf-export .photo-box img {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover !important;
        }

        .pdf-export .photo-caption {
            width: 30mm !important;
            max-width: 30mm !important;
            margin: 2mm auto 7mm !important;

            text-align: center !important;
            font-size: 6pt !important;
            line-height: 1.15 !important;
        }

        /* =====================================================
           HALAMAN 2 (BAGIAN D & E)
        ===================================================== */

        .pdf-export .page-two-section {
            width: 100% !important;
            max-width: 100% !important;
            margin-bottom: 4mm !important;
        }

        .pdf-export .school-leaving {
            width: 100% !important;
            max-width: 100% !important;
        }

        .pdf-export .school-row {
            display: block !important;
            clear: both !important;
            width: 100% !important;
            max-width: 100% !important;
            min-height: 14px !important;
            line-height: 1.25 !important;
            margin-bottom: 0.5mm !important;
            box-sizing: border-box !important;
        }

        .pdf-export .school-number {
            display: inline-block !important;
            float: left !important;
            width: 22px !important;
            max-width: 22px !important;
            text-align: left !important;
            white-space: nowrap !important;
            box-sizing: border-box !important;
        }

        .pdf-export .school-label {
            display: inline-block !important;
            float: left !important;
            width: 190px !important;
            max-width: 190px !important;
            white-space: nowrap !important;
            box-sizing: border-box !important;
        }

        .pdf-export .school-colon {
            display: inline-block !important;
            float: left !important;
            width: 8px !important;
            max-width: 8px !important;
            text-align: center !important;
            box-sizing: border-box !important;
        }

        .pdf-export .school-value {
            display: block !important;
            margin-left: 220px !important;
            border-bottom: 1px dotted #000 !important;
            min-height: 13px !important;
            padding-left: 4px !important;
            box-sizing: border-box !important;
            word-wrap: break-word !important;
        }

        .pdf-export .school-value.no-line {
            border-bottom: none !important;
        }

        .pdf-export .school-sub .school-number {
            width: 26px !important;
            max-width: 26px !important;
            padding-left: 12px !important;
        }

        .pdf-export .school-sub .school-label {
            width: 186px !important;
            max-width: 186px !important;
        }

        /* =====================================================
           BAGIAN LAIN-LAIN
        ===================================================== */

        .pdf-export .other-title {
            margin-top: 5mm !important;
            margin-bottom: 5mm !important;
        }

        .pdf-export .other-subtitle {
            margin-bottom: 2.5mm !important;
        }

        /* =====================================================
           TABEL TINGGI / BERAT
        ===================================================== */

        .pdf-export .health-table {
            width: 100% !important;
            max-width: 100% !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            font-size: 7pt !important;
        }

        .pdf-export .health-table th,
        .pdf-export .health-table td {
            box-sizing: border-box !important;
            max-width: 100% !important;
            border: 1px solid #000 !important;
            padding: 0.7mm 0.35mm !important;
            text-align: center !important;
            vertical-align: middle !important;
            page-break-inside: avoid !important;
        }

        .pdf-export .health-table .no-column {
            width: 5% !important;
        }

        .pdf-export .health-table .aspect-column {
            width: 13% !important;
        }

        .pdf-export .health-table .year-column {
            width: 13.666% !important;
        }

        .pdf-export .health-table .semester-cell {
            width: 50% !important;
        }

        .pdf-export .year-title {
            height: 12mm !important;
            line-height: 1.1 !important;
        }

        .pdf-export .semester-row {
            height: 10mm !important;
        }

        .pdf-export .body-row {
            height: 10.5mm !important;
        }

        /* =====================================================
           TABEL KONDISI KESEHATAN
        ===================================================== */

        .pdf-export .condition-table {
            width: 100% !important;
            max-width: 100% !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            font-size: 7pt !important;
        }

        .pdf-export .condition-table th,
        .pdf-export .condition-table td {
            box-sizing: border-box !important;
            max-width: 100% !important;
            border: 1px solid #000 !important;
            padding: 0.7mm 0.35mm !important;
            text-align: center !important;
            vertical-align: middle !important;
            page-break-inside: avoid !important;
        }

        .pdf-export .condition-table .no-column {
            width: 5% !important;
        }

        .pdf-export .condition-table .aspect-column {
            width: 13% !important;
        }

        .pdf-export .condition-table .year-column {
            width: 13.666% !important;
        }

        .pdf-export .condition-year {
            height: 10mm !important;
            line-height: 1.1 !important;
        }

        .pdf-export .condition-subheader {
            height: 6mm !important;
            line-height: 1 !important;
        }

        .pdf-export .condition-body {
            height: 8mm !important;
        }

        .pdf-export .table-gap {
            height: 3mm !important;
        }

        /* =====================================================
           JANGAN PECAH BLOK
        ===================================================== */

        .pdf-export table {
            page-break-inside: avoid !important;
        }

        .pdf-export tr {
            page-break-inside: avoid !important;
        }

        .pdf-export .section-title,
        .pdf-export .other-title,
        .pdf-export .other-subtitle {
            page-break-after: avoid !important;
        }



    </style>
</head>

<body class="{{ request()->boolean('pdf') ? 'pdf-export' : '' }}">

    {{-- =====================================================
         HALAMAN 1
    ====================================================== --}}

    <div class="page">

        <div class="main-title">
            III. LEMBAR DATA PESERTA DIDIK KURIKULUM MERDEKA SEKOLAH DASAR (SD)
        </div>


        {{-- DATA IDENTITAS ATAS --}}

        <div class="top-information">

            <div class="top-column">

                <div class="top-row">
                    <span class="top-label">Nomor Induk Siswa</span>
                    <span class="top-colon">:</span>
                    <span class="top-value">{{ $student->nis ?? '' }}</span>
                </div>

                <div class="top-row">
                    <span class="top-label">Nomor Induk Siswa Nasional</span>
                    <span class="top-colon">:</span>
                    <span class="top-value">{{ $student->nisn ?? '' }}</span>
                </div>

                <div class="top-row">
                    <span class="top-label">Nomor Kode Sekolah</span>
                    <span class="top-colon">:</span>
                    <span class="top-value">{{ $student->school_code ?? '' }}</span>
                </div>

            </div>


            <div class="top-column">

                <div class="top-row">
                    <span class="top-label">Nomor Kode Kecamatan</span>
                    <span class="top-colon">:</span>
                    <span class="top-value">{{ $student->district_code ?? '' }}</span>
                </div>

                <div class="top-row">
                    <span class="top-label">Nomor Kode Kab / Kota</span>
                    <span class="top-colon">:</span>
                    <span class="top-value">{{ $student->city_code ?? '' }}</span>
                </div>

                <div class="top-row">
                    <span class="top-label">Nomor Kode Provinsi</span>
                    <span class="top-colon">:</span>
                    <span class="top-value">{{ $student->province_code ?? '' }}</span>
                </div>

            </div>


            <div class="nomor-urut">

                <div class="nomor-urut-title">
                    NOMOR URUT
                </div>

                <div class="nomor-urut-value">
                    {{ $student->order_number ?? '' }}
                </div>

            </div>

        </div>


        {{-- =================================================
             A. KETERANGAN SISWA
        ================================================== --}}

        <div class="section-title">
            A. KETERANGAN SISWA
        </div>


        <div class="student-section">

            <div class="student-data">

                <div class="data-row">
                    <div class="data-number">1.</div>
                    <div class="data-label">Nama peserta didik</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->name ?? '' }}
                    </div>
                </div>


                <div class="data-row">
                    <div class="data-number">2.</div>
                    <div class="data-label">Jenis kelamin</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        @if (($student->gender ?? '') === 'L')
                            Laki-laki
                        @elseif (($student->gender ?? '') === 'P')
                            Perempuan
                        @endif
                    </div>
                </div>


                <div class="data-row">
                    <div class="data-number">3.</div>
                    <div class="data-label">Tempat dan tanggal lahir</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->birth_place ?? '' }}
                        @if ($student->birth_date)
                            , {{ \Carbon\Carbon::parse($student->birth_date)->translatedFormat('d F Y') }}
                        @endif
                    </div>
                </div>


                <div class="data-row">
                    <div class="data-number">4.</div>
                    <div class="data-label">Agama</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->religion ?? '' }}
                    </div>
                </div>


                <div class="data-row">
                    <div class="data-number">5.</div>
                    <div class="data-label">Kewarganegaraan</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->citizenship ?? '' }}
                    </div>
                </div>


                <div class="data-row">
                    <div class="data-number">6.</div>
                    <div class="data-label">Anak ke berapa</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->child_position ?? '' }}
                    </div>
                </div>


                <div class="data-row">
                    <div class="data-number">7.</div>
                    <div class="data-label">Jumlah saudara</div>
                    <div class="data-colon">:</div>
                    <div class="data-value"></div>
                </div>


                <div class="data-row sub-row">
                    <div class="data-number">a.</div>
                    <div class="data-label">Kandung</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->siblings_biological ?? '' }}
                    </div>
                </div>


                <div class="data-row sub-row">
                    <div class="data-number">b.</div>
                    <div class="data-label">Tiri</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->siblings_step ?? '' }}
                    </div>
                </div>


                <div class="data-row sub-row">
                    <div class="data-number">c.</div>
                    <div class="data-label">Angkat</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->siblings_adopted ?? '' }}
                    </div>
                </div>


                <div class="data-row">
                    <div class="data-number">8.</div>
                    <div class="data-label">Bahasa sehari-hari di rumah</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->daily_language ?? '' }}
                    </div>
                </div>


                <div class="data-row">
                    <div class="data-number">9.</div>
                    <div class="data-label">Golongan darah</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->blood_type ?? '' }}
                    </div>
                </div>


                <div class="data-row">
                    <div class="data-number">10.</div>
                    <div class="data-label">Alamat saat diterima</div>
                    <div class="data-colon">:</div>
                    <div class="data-value"></div>
                </div>


                <div class="data-row sub-row">
                    <div class="data-number">a.</div>
                    <div class="data-label">RT / RW</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->rt_rw ?? '' }}
                    </div>
                </div>


                <div class="data-row sub-row">
                    <div class="data-number">b.</div>
                    <div class="data-label">Desa / Kelurahan</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->village ?? '' }}
                    </div>
                </div>


                <div class="data-row sub-row">
                    <div class="data-number">c.</div>
                    <div class="data-label">Kecamatan</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->district ?? '' }}
                    </div>
                </div>


                <div class="data-row sub-row">
                    <div class="data-number">d.</div>
                    <div class="data-label">Kabupaten / Kota</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->city ?? '' }}
                    </div>
                </div>


                <div class="data-row sub-row">
                    <div class="data-number">e.</div>
                    <div class="data-label">Provinsi</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->province ?? '' }}
                    </div>
                </div>


                <div class="data-row">
                    <div class="data-number">11.</div>
                    <div class="data-label">Kode Pos dan Nomor Telepon</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->postal_code ?? '' }}
                        @if (!empty($student->phone))
                            / {{ $student->phone }}
                        @endif
                    </div>
                </div>


                <div class="data-row">
                    <div class="data-number">12.</div>
                    <div class="data-label">Bertempat tinggal pada</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->living_with ?? '' }}
                    </div>
                </div>


                <div class="data-row">
                    <div class="data-number">13.</div>
                    <div class="data-label">Jarak ke sekolah</div>
                    <div class="data-colon">:</div>
                    <div class="data-value">
                        {{ $student->distance_to_school ?? '' }}
                    </div>
                </div>

            </div>


            {{-- FOTO --}}

            <div class="photo-column">

                @for ($i = 1; $i <= 4; $i++)
                    <div class="photo-box">

                        @if ($student->photo)
                            @php
                                $photoDisk = public_path('storage/' . $student->photo);
                            @endphp
                            @if (request()->boolean('pdf') && file_exists($photoDisk))
                                <img src="{{ $photoDisk }}" alt="Pas Photo">
                            @else
                                <img src="{{ asset('storage/' . $student->photo) }}" alt="Pas Photo">
                            @endif
                        @else
                            Pas Photo
                            <br>
                            3 x 4
                        @endif

                    </div>

                    <div class="photo-caption">
                        Cap tiga jari tengah tangan kiri
                        di atas pas photo
                        bagian bawah
                    </div>
                @endfor

            </div>

        </div>


        {{-- =================================================
             B. KETERANGAN ORANG TUA / WALI
        ================================================== --}}

        <div class="section-title">
            B. KETERANGAN ORANG TUA / WALI PESERTA DIDIK
        </div>


        <div class="parent-section">

            <div class="parent-row">
                <div class="parent-number">1.</div>
                <div class="parent-label">Nama Orang Tua Kandung</div>
                <div class="parent-colon">:</div>
                <div class="parent-value"></div>
            </div>

            <div class="parent-row parent-sub">
                <div class="parent-number">a.</div>
                <div class="parent-label">Ayah</div>
                <div class="parent-colon">:</div>
                <div class="parent-value">
                    {{ $student->father_name ?? '' }}
                </div>
            </div>

            <div class="parent-row parent-sub">
                <div class="parent-number">b.</div>
                <div class="parent-label">Ibu</div>
                <div class="parent-colon">:</div>
                <div class="parent-value">
                    {{ $student->mother_name ?? '' }}
                </div>
            </div>


            <div class="parent-row">
                <div class="parent-number">2.</div>
                <div class="parent-label">Pendidikan Terakhir</div>
                <div class="parent-colon">:</div>
                <div class="parent-value"></div>
            </div>

            <div class="parent-row parent-sub">
                <div class="parent-number">a.</div>
                <div class="parent-label">Ayah</div>
                <div class="parent-colon">:</div>
                <div class="parent-value">
                    {{ $student->father_education ?? '' }}
                </div>
            </div>

            <div class="parent-row parent-sub">
                <div class="parent-number">b.</div>
                <div class="parent-label">Ibu</div>
                <div class="parent-colon">:</div>
                <div class="parent-value">
                    {{ $student->mother_education ?? '' }}
                </div>
            </div>


            <div class="parent-row">
                <div class="parent-number">3.</div>
                <div class="parent-label">Pekerjaan Orang Tua</div>
                <div class="parent-colon">:</div>
                <div class="parent-value"></div>
            </div>

            <div class="parent-row parent-sub">
                <div class="parent-number">a.</div>
                <div class="parent-label">Ayah</div>
                <div class="parent-colon">:</div>
                <div class="parent-value">
                    {{ $student->father_job ?? '' }}
                </div>
            </div>

            <div class="parent-row parent-sub">
                <div class="parent-number">b.</div>
                <div class="parent-label">Ibu</div>
                <div class="parent-colon">:</div>
                <div class="parent-value">
                    {{ $student->mother_job ?? '' }}
                </div>
            </div>


            <div class="parent-row">
                <div class="parent-number">4.</div>
                <div class="parent-label">Wali Murid</div>
                <div class="parent-colon">:</div>
                <div class="parent-value"></div>
            </div>

            <div class="parent-row parent-sub">
                <div class="parent-number">a.</div>
                <div class="parent-label">Nama</div>
                <div class="parent-colon">:</div>
                <div class="parent-value">
                    {{ $student->guardian_name ?? '' }}
                </div>
            </div>

            <div class="parent-row parent-sub">
                <div class="parent-number">b.</div>
                <div class="parent-label">Hubungan Keluarga</div>
                <div class="parent-colon">:</div>
                <div class="parent-value">
                    {{ $student->guardian_relationship ?? '' }}
                </div>
            </div>

            <div class="parent-row parent-sub">
                <div class="parent-number">c.</div>
                <div class="parent-label">Pendidikan Terakhir</div>
                <div class="parent-colon">:</div>
                <div class="parent-value">
                    {{ $student->guardian_education ?? '' }}
                </div>
            </div>

            <div class="parent-row parent-sub">
                <div class="parent-number">d.</div>
                <div class="parent-label">Pekerjaan</div>
                <div class="parent-colon">:</div>
                <div class="parent-value">
                    {{ $student->guardian_job ?? '' }}
                </div>
            </div>

        </div>


        {{-- =================================================
             C. PERKEMBANGAN PESERTA DIDIK
        ================================================== --}}

        <div class="section-title">
            C. PERKEMBANGAN PESERTA DIDIK
        </div>


        <div class="development">

            <div class="development-row">
                <div class="development-number">1.</div>
                <div class="development-label">Pendidikan sebelumnya</div>
                <div class="development-colon">:</div>
                <div class="development-value"></div>
            </div>


            <div class="development-row development-sub">
                <div class="development-number">a.</div>
                <div class="development-label">
                    Masuk menjadi peserta didik baru
                </div>
                <div class="development-colon">:</div>
                <div class="development-value"></div>
            </div>


            <div class="development-row development-sub-sub">
                <div class="development-number">1.</div>
                <div class="development-label">Asal Sekolah</div>
                <div class="development-colon">:</div>
                <div class="development-value">
                    {{ $student->previousEducation?->previous_school_name ?? '' }}
                </div>
            </div>


            <div class="development-row development-sub-sub">
                <div class="development-number">2.</div>
                <div class="development-label">Nama Sekolah</div>
                <div class="development-colon">:</div>
                <div class="development-value">
                    {{ $student->previousEducation?->previous_school_name ?? '' }}
                </div>
            </div>


            <div class="development-row development-sub-sub">
                <div class="development-number">3.</div>
                <div class="development-label">
                    Tanggal dan Nomor Ijazah / STTB
                </div>
                <div class="development-colon">:</div>
                <div class="development-value">
                    {{ $student->previousEducation?->certificate_date_number ?? '' }}
                </div>
            </div>


            <div class="development-row development-sub">
                <div class="development-number">b.</div>
                <div class="development-label">
                    Pindah dari sekolah lain
                </div>
                <div class="development-colon">:</div>
                <div class="development-value"></div>
            </div>


            <div class="development-row development-sub-sub">
                <div class="development-number">1.</div>
                <div class="development-label">
                    Nama Sekolah asal
                </div>
                <div class="development-colon">:</div>
                <div class="development-value">
                    {{ $student->previousEducation?->transfer_from_school ?? '' }}
                </div>
            </div>


            <div class="development-row development-sub-sub">
                <div class="development-number">2.</div>
                <div class="development-label">
                    Dari Tingkat
                </div>
                <div class="development-colon">:</div>
                <div class="development-value">
                    {{ $student->previousEducation?->transfer_from_grade ?? '' }}
                </div>
            </div>


            <div class="development-row development-sub-sub">
                <div class="development-number">3.</div>
                <div class="development-label">
                    Diterima Tanggal
                </div>
                <div class="development-colon">:</div>
                <div class="development-value">
                    {{ $student->previousEducation?->transfer_accepted_date ?? '' }}
                </div>
            </div>


            <div class="development-row development-sub-sub">
                <div class="development-number">4.</div>
                <div class="development-label">
                    No. Surat Keterangan
                </div>
                <div class="development-colon">:</div>
                <div class="development-value">
                    {{ $student->previousEducation?->transfer_letter_number ?? '' }}
                </div>
            </div>

        </div>

    </div>


    {{-- =====================================================
         HALAMAN 2
    ====================================================== --}}

    <div class="page">

        {{-- =================================================
             D. MENINGGALKAN SEKOLAH
        ================================================== --}}

        <div class="page-two-section">

            <div class="section-title">
                D. MENINGGALKAN SEKOLAH
            </div>


            <div class="school-leaving">

                <div class="school-row">
                    <div class="school-number">1.</div>
                    <div class="school-label">Tamat Belajar / Lulus</div>
                    <div class="school-colon">:</div>
                    <div class="school-value"></div>
                </div>

                <div class="school-row school-sub">
                    <div class="school-number">a.</div>
                    <div class="school-label">Tahun</div>
                    <div class="school-colon">:</div>
                    <div class="school-value"></div>
                </div>

                <div class="school-row school-sub">
                    <div class="school-number">b.</div>
                    <div class="school-label">Nomor Ijazah / STTB</div>
                    <div class="school-colon">:</div>
                    <div class="school-value"></div>
                </div>

                <div class="school-row school-sub">
                    <div class="school-number">c.</div>
                    <div class="school-label">Melanjutkan Ke Sekolah</div>
                    <div class="school-colon">:</div>
                    <div class="school-value"></div>
                </div>


                <div class="school-row">
                    <div class="school-number">2.</div>
                    <div class="school-label">Pindah Sekolah</div>
                    <div class="school-colon">:</div>
                    <div class="school-value"></div>
                </div>

                <div class="school-row school-sub">
                    <div class="school-number">a.</div>
                    <div class="school-label">
                        Tingkat / Kelas yang ditinggalkan
                    </div>
                    <div class="school-colon">:</div>
                    <div class="school-value"></div>
                </div>

                <div class="school-row school-sub">
                    <div class="school-number">b.</div>
                    <div class="school-label">Ke Sekolah</div>
                    <div class="school-colon">:</div>
                    <div class="school-value"></div>
                </div>

                <div class="school-row school-sub">
                    <div class="school-number">c.</div>
                    <div class="school-label">Ke Tingkat</div>
                    <div class="school-colon">:</div>
                    <div class="school-value"></div>
                </div>


                <div class="school-row">
                    <div class="school-number">3.</div>
                    <div class="school-label">Keluar Sekolah</div>
                    <div class="school-colon">:</div>
                    <div class="school-value"></div>
                </div>

                <div class="school-row school-sub">
                    <div class="school-number">a.</div>
                    <div class="school-label">Alasan Keluar Sekolah</div>
                    <div class="school-colon">:</div>
                    <div class="school-value"></div>
                </div>

                <div class="school-row school-sub">
                    <div class="school-number">b.</div>
                    <div class="school-label">
                        Hari dan Tanggal Keluar Sekolah
                    </div>
                    <div class="school-colon">:</div>
                    <div class="school-value"></div>
                </div>

            </div>

        </div>


        {{-- =================================================
             E. LAIN-LAIN
        ================================================== --}}

        <div class="other-title">
            E. LAIN - LAIN
        </div>


        {{-- =================================================
             1. TINGGI DAN BERAT BADAN
        ================================================== --}}

        <div class="other-subtitle">
            1. TINGGI DAN BERAT BADAN PESERTA DIDIK
        </div>


        {{-- TABEL 1 --}}

        <table class="health-table">

            <thead>

                <tr>

                    <th rowspan="2" class="no-column">
                        No.
                    </th>

                    <th rowspan="2" class="aspect-column">
                        Aspek yang<br>
                        dinilai
                    </th>

                    @for ($i = 1; $i <= 6; $i++)
                        <th colspan="2" class="year-column">
                            <div class="year-title">
                                Tahun<br>
                                Pelajaran<br>
                                ........ / ........
                            </div>
                        </th>
                    @endfor

                </tr>


                <tr class="semester-row">

                    @for ($i = 1; $i <= 6; $i++)
                        <th class="semester-cell">
                            Semester<br>
                            Ganjil
                        </th>

                        <th class="semester-cell">
                            Semester<br>
                            Genap
                        </th>
                    @endfor

                </tr>

            </thead>


            <tbody>

                <tr class="body-row">

                    <td>1.</td>

                    <td>
                        Tinggi<br>
                        Badan
                    </td>

                    @for ($i = 1; $i <= 12; $i++)
                        <td class="body-value">
                            ........ Cm
                        </td>
                    @endfor

                </tr>


                <tr class="body-row">

                    <td>2.</td>

                    <td>
                        Berat<br>
                        Badan
                    </td>

                    @for ($i = 1; $i <= 12; $i++)
                        <td class="body-value">
                            ........ Kg
                        </td>
                    @endfor

                </tr>

            </tbody>

        </table>


        <div class="table-gap"></div>


        {{-- TABEL 2 --}}

        <table class="health-table">

            <thead>

                <tr>

                    <th rowspan="2" class="no-column">
                        No.
                    </th>

                    <th rowspan="2" class="aspect-column">
                        Aspek yang<br>
                        dinilai
                    </th>

                    @for ($i = 1; $i <= 6; $i++)
                        <th colspan="2" class="year-column">
                            <div class="year-title">
                                Tahun<br>
                                Pelajaran<br>
                                ........ / ........
                            </div>
                        </th>
                    @endfor

                </tr>


                <tr class="semester-row">

                    @for ($i = 1; $i <= 6; $i++)
                        <th class="semester-cell">
                            Semester<br>
                            Ganjil
                        </th>

                        <th class="semester-cell">
                            Semester<br>
                            Genap
                        </th>
                    @endfor

                </tr>

            </thead>


            <tbody>

                <tr class="body-row">

                    <td>1.</td>

                    <td>
                        Tinggi<br>
                        Badan
                    </td>

                    @for ($i = 1; $i <= 12; $i++)
                        <td class="body-value">
                            ........ Cm
                        </td>
                    @endfor

                </tr>


                <tr class="body-row">

                    <td>2.</td>

                    <td>
                        Berat<br>
                        Badan
                    </td>

                    @for ($i = 1; $i <= 12; $i++)
                        <td class="body-value">
                            ........ Kg
                        </td>
                    @endfor

                </tr>

            </tbody>

        </table>


        <div class="table-gap"></div>


        {{-- =================================================
             2. KONDISI KESEHATAN
        ================================================== --}}

        <div class="other-subtitle">
            2. KONDISI KESEHATAN PESERTA DIDIK
        </div>


        {{-- TABEL KESEHATAN 1 --}}

        <table class="condition-table">

            <thead>

                <tr>

                    <th rowspan="2" class="no-column">
                        No.
                    </th>

                    <th rowspan="2" class="aspect-column">
                        Aspek<br>
                        Kesehatan
                    </th>

                    @for ($i = 1; $i <= 6; $i++)
                        <th class="year-column">
                            <div class="condition-year">
                                Tahun<br>
                                Pelajaran<br>
                                ........ / ........
                            </div>
                        </th>
                    @endfor

                </tr>

                <tr>
                    @for ($i = 1; $i <= 6; $i++)
                        <th class="condition-subheader">Keterangan</th>
                    @endfor
                </tr>

            </thead>


            <tbody>

                <tr class="condition-body">

                    <td>1.</td>

                    <td>Pendengaran</td>

                    @for ($i = 1; $i <= 6; $i++)
                        <td></td>
                    @endfor

                </tr>


                <tr class="condition-body">

                    <td>2.</td>

                    <td>Penglihatan</td>

                    @for ($i = 1; $i <= 6; $i++)
                        <td></td>
                    @endfor

                </tr>


                <tr class="condition-body">

                    <td>3.</td>

                    <td>Gigi</td>

                    @for ($i = 1; $i <= 6; $i++)
                        <td></td>
                    @endfor

                </tr>


                <tr class="condition-body">

                    <td>4.</td>

                    <td>................</td>

                    @for ($i = 1; $i <= 6; $i++)
                        <td></td>
                    @endfor

                </tr>

            </tbody>

        </table>


        <div class="table-gap"></div>


        {{-- TABEL KESEHATAN 2 --}}

        <table class="condition-table">

            <thead>

                <tr>

                    <th rowspan="2" class="no-column">
                        No.
                    </th>

                    <th rowspan="2" class="aspect-column">
                        Aspek<br>
                        Kesehatan
                    </th>

                    @for ($i = 1; $i <= 6; $i++)
                        <th class="year-column">

                            <div class="condition-year">
                                Tahun<br>
                                Pelajaran<br>
                                ........ / ........
                            </div>

                        </th>
                    @endfor

                </tr>

                <tr>
                    @for ($i = 1; $i <= 6; $i++)
                        <th class="condition-subheader">Keterangan</th>
                    @endfor
                </tr>

            </thead>


            <tbody>

                <tr class="condition-body">

                    <td>1.</td>

                    <td>Pendengaran</td>

                    @for ($i = 1; $i <= 6; $i++)
                        <td></td>
                    @endfor

                </tr>


                <tr class="condition-body">

                    <td>2.</td>

                    <td>Penglihatan</td>

                    @for ($i = 1; $i <= 6; $i++)
                        <td></td>
                    @endfor

                </tr>


                <tr class="condition-body">

                    <td>3.</td>

                    <td>Gigi</td>

                    @for ($i = 1; $i <= 6; $i++)
                        <td></td>
                    @endfor

                </tr>


                <tr class="condition-body">

                    <td>4.</td>

                    <td>................</td>

                    @for ($i = 1; $i <= 6; $i++)
                        <td></td>
                    @endfor

                </tr>

            </tbody>

        </table>

    </div>


</body>

</html>
