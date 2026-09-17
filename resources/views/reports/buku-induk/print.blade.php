<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">

    <title>
        Lembar Data Peserta Didik
    </title>

    <style>
        @page {
            size: 215mm 330mm;
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
            grid-template-columns: 1fr 1fr 70px;
            column-gap: 18px;
            margin-bottom: 4px;
        }

        .top-column {
            width: 100%;
        }

        .top-row {
            display: grid;
            grid-template-columns: 145px 10px minmax(0, 1fr);
            align-items: baseline;
            min-height: 15px;
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
        }

        .nomor-urut {
            border: 1px solid #000;
            text-align: center;
            height: 38px;
            font-size: 7.5pt;
        }

        .nomor-urut-title {
            padding: 5px 2px 3px;
            font-weight: bold;
        }

        .nomor-urut-value {
            height: 17px;
        }

        /* =====================================================
           JUDUL BAGIAN
        ===================================================== */

        .section-title {
            font-weight: bold;
            margin-top: 4px;
            margin-bottom: 3px;
        }

        /* =====================================================
           BARIS DATA TANPA TABEL
        ===================================================== */

        .data-row {
            display: grid;
            grid-template-columns: 19px 125px 10px minmax(0, 1fr);
            align-items: flex-start;
            min-height: 16px;
            line-height: 1.15;
        }

        .data-number {
            width: 19px;
            flex-shrink: 0;
        }

        .data-label {
            width: 125px;
            flex-shrink: 0;
        }

        .data-colon {
            width: 10px;
            flex-shrink: 0;
            text-align: center;
        }

        .data-value {
            flex: 1;
            min-height: 15px;
            border-bottom: 1px dotted #000;
            padding-left: 2px;
        }

        .data-value.no-line {
            border-bottom: none;
        }

        .sub-row {
            padding-left: 19px;
            grid-template-columns: 19px 106px 10px minmax(0, 1fr);
        }

        .sub-row .data-label {
            width: 106px;
        }

        /* =====================================================
           LAYOUT A + FOTO
        ===================================================== */

        .student-section {
            padding-right: 42mm;
        }

        .student-data {
            width: 100%;
        }

        .photo-column {
            position: absolute;
            top: 44mm;
            right: 0;
            width: 38mm;
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
            width: 34mm;
            margin: 2mm auto 7mm;
            text-align: center;
            font-size: 6pt;
            line-height: 1.15;
        }

        /* =====================================================
           BAGIAN B
        ===================================================== */

        .parent-section {
            margin-top: 3px;
            padding-right: 42mm;
        }

        .parent-row {
            display: grid;
            grid-template-columns: 19px 125px 10px minmax(0, 1fr);
            min-height: 16px;
            line-height: 1.15;
        }

        .parent-number {
            width: 19px;
            flex-shrink: 0;
        }

        .parent-label {
            width: 125px;
            flex-shrink: 0;
        }

        .parent-colon {
            width: 10px;
            flex-shrink: 0;
            text-align: center;
        }

        .parent-value {
            flex: 1;
            border-bottom: 1px dotted #000;
            min-height: 15px;
        }

        .parent-sub {
            padding-left: 19px;
            grid-template-columns: 19px 106px 10px minmax(0, 1fr);
        }

        .parent-sub .parent-label {
            width: 106px;
        }

        /* =====================================================
           BAGIAN C
        ===================================================== */

        .development {
            padding-right: 42mm;
        }

        .development-row {
            display: grid;
            grid-template-columns: 19px 125px 10px minmax(0, 1fr);
            min-height: 16px;
            line-height: 1.15;
        }

        .development-number {
            width: 19px;
            flex-shrink: 0;
        }

        .development-label {
            width: 125px;
            flex-shrink: 0;
        }

        .development-colon {
            width: 10px;
            flex-shrink: 0;
            text-align: center;
        }

        .development-value {
            flex: 1;
            border-bottom: 1px dotted #000;
            min-height: 15px;
        }

        .development-sub {
            padding-left: 19px;
            grid-template-columns: 19px 106px 10px minmax(0, 1fr);
        }

        .development-sub .development-label {
            width: 106px;
        }

        /* =====================================================
           HALAMAN 2
        ===================================================== */

        .page-two-section {
            margin-bottom: 7px;
        }

        .school-leaving {
            width: 100%;
        }

        .school-row {
            display: grid;
            grid-template-columns: 19px 185px 10px minmax(0, 1fr);
            min-height: 17px;
            line-height: 1.15;
        }

        .school-number {
            width: 19px;
            flex-shrink: 0;
        }

        .school-label {
            width: 185px;
            flex-shrink: 0;
        }

        .school-colon {
            width: 10px;
            flex-shrink: 0;
            text-align: center;
        }

        .school-value {
            flex: 1;
            border-bottom: 1px dotted #000;
            min-height: 15px;
        }

        .school-sub {
            padding-left: 19px;
            grid-template-columns: 19px 166px 10px minmax(0, 1fr);
        }

        .school-sub .school-label {
            width: 166px;
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
   Preview browser TIDAK DIUBAH
===================================================== */

        .pdf-export {
            background: #fff !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Margin dibuat pada halaman agar konsisten di Dompdf. */
        .pdf-export .page {
            width: 100% !important;
            min-height: 330mm !important;
            height: 330mm !important;

            margin: 0 !important;
            padding: 14mm !important;

            background: #fff !important;
            box-shadow: none !important;

            page-break-after: always;
            page-break-inside: avoid;
        }

        .pdf-export .page:last-child {
            page-break-after: auto;
        }


        /* =====================================================
   IDENTITAS ATAS
===================================================== */

        .pdf-export .top-information {
            display: table;
            width: 100%;
            table-layout: fixed;
            margin-bottom: 4px;
        }

        .pdf-export .top-column,
        .pdf-export .nomor-urut {
            display: table-cell;
            vertical-align: top;
        }

        .pdf-export .top-column:first-child {
            width: 43%;
            padding-right: 4mm;
        }

        .pdf-export .top-column:nth-child(2) {
            width: 43%;
            padding-right: 4mm;
        }

        .pdf-export .nomor-urut {
            width: 14%;
        }

        .pdf-export .top-row {
            display: table;
            width: 100%;
            table-layout: fixed;
            min-height: 15px;
        }

        .pdf-export .top-label,
        .pdf-export .top-colon,
        .pdf-export .top-value {
            display: table-cell;
            vertical-align: baseline;
        }

        .pdf-export .top-label {
            width: 145px;
            white-space: nowrap;
        }

        .pdf-export .top-colon {
            width: 10px;
            text-align: center;
        }

        .pdf-export .top-value {
            width: auto;
        }


        /* =====================================================
   BARIS DATA
===================================================== */

        .pdf-export .data-row,
        .pdf-export .parent-row,
        .pdf-export .development-row,
        .pdf-export .school-row {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .pdf-export .data-number,
        .pdf-export .data-label,
        .pdf-export .data-colon,
        .pdf-export .data-value,

        .pdf-export .parent-number,
        .pdf-export .parent-label,
        .pdf-export .parent-colon,
        .pdf-export .parent-value,

        .pdf-export .development-number,
        .pdf-export .development-label,
        .pdf-export .development-colon,
        .pdf-export .development-value,

        .pdf-export .school-number,
        .pdf-export .school-label,
        .pdf-export .school-colon,
        .pdf-export .school-value {
            display: table-cell;
            vertical-align: top;
        }

        .pdf-export .data-number,
        .pdf-export .parent-number,
        .pdf-export .development-number,
        .pdf-export .school-number {
            width: 19px;
        }

        .pdf-export .data-label,
        .pdf-export .parent-label,
        .pdf-export .development-label {
            width: 125px;
        }

        .pdf-export .school-label {
            width: 185px;
        }

        .pdf-export .data-colon,
        .pdf-export .parent-colon,
        .pdf-export .development-colon,
        .pdf-export .school-colon {
            width: 10px;
            text-align: center;
        }

        .pdf-export .data-value,
        .pdf-export .parent-value,
        .pdf-export .development-value,
        .pdf-export .school-value {
            width: auto;
        }


        /* =====================================================
   BARIS SUB
===================================================== */

        .pdf-export .sub-row,
        .pdf-export .parent-sub,
        .pdf-export .development-sub,
        .pdf-export .school-sub {
            padding-left: 19px;
        }

        .pdf-export .sub-row .data-label,
        .pdf-export .parent-sub .parent-label,
        .pdf-export .development-sub .development-label {
            width: 106px;
        }

        .pdf-export .school-sub .school-label {
            width: 166px;
        }


        /* =====================================================
   FOTO
===================================================== */

        .pdf-export .photo-column {
            position: absolute;
            top: 44mm;
            right: 0;
            width: 38mm;
        }

        .pdf-export .photo-box {
            width: 30mm;
            height: 40mm;

            border: 1px solid #000;

            margin: 0 auto;

            display: table;
            table-layout: fixed;

            text-align: center;
        }

        .pdf-export .photo-box img {
            width: 30mm;
            height: 40mm;
            object-fit: cover;
        }

        .pdf-export .photo-caption {
            width: 34mm;
            margin: 2mm auto 7mm;

            text-align: center;
            font-size: 6pt;
            line-height: 1.15;
        }


        /* =====================================================
   HALAMAN 2
===================================================== */

        .pdf-export .page-two-section {
            margin-bottom: 5mm;
        }

        .pdf-export .school-row {
            min-height: 5.2mm;
            line-height: 1.2;
        }


        /* =====================================================
   BAGIAN LAIN-LAIN
===================================================== */

        .pdf-export .other-title {
            margin-top: 5mm;
            margin-bottom: 5mm;
        }

        .pdf-export .other-subtitle {
            margin-bottom: 2.5mm;
        }


        /* =====================================================
   TABEL TINGGI / BERAT
===================================================== */

        .pdf-export .health-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 7pt;
        }

        .pdf-export .health-table th,
        .pdf-export .health-table td {
            border: 1px solid #000;
            padding: 0.7mm 0.35mm;

            text-align: center;
            vertical-align: middle;

            page-break-inside: avoid;
        }

        .pdf-export .health-table .no-column {
            width: 5%;
        }

        .pdf-export .health-table .aspect-column {
            width: 13%;
        }

        .pdf-export .health-table .year-column {
            width: 13.666%;
        }

        .pdf-export .health-table .semester-cell {
            width: 50%;
        }

        .pdf-export .year-title {
            height: 12mm;
            line-height: 1.1;
        }

        .pdf-export .semester-row {
            height: 10mm;
        }

        .pdf-export .body-row {
            height: 10.5mm;
        }

        .pdf-export .body-value {
            line-height: 1.1;
        }


        /* =====================================================
   TABEL KONDISI KESEHATAN
===================================================== */

        .pdf-export .condition-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 7pt;
        }

        .pdf-export .condition-table th,
        .pdf-export .condition-table td {
            border: 1px solid #000;
            padding: 0.7mm 0.35mm;

            text-align: center;
            vertical-align: middle;

            page-break-inside: avoid;
        }

        .pdf-export .condition-table .no-column {
            width: 5%;
        }

        .pdf-export .condition-table .aspect-column {
            width: 13%;
        }

        .pdf-export .condition-table .year-column {
            width: 13.666%;
        }

        .pdf-export .condition-year {
            height: 10mm;
            line-height: 1.1;
        }

        .pdf-export .condition-subheader {
            height: 6mm;
            line-height: 1;
        }

        .pdf-export .condition-body {
            height: 8mm;
        }


        /* =====================================================
   JARAK TABEL
===================================================== */

        .pdf-export .table-gap {
            height: 3mm;
        }


        /* =====================================================
   JANGAN PECAH BLOK
===================================================== */

        .pdf-export table {
            page-break-inside: avoid;
        }

        .pdf-export tr {
            page-break-inside: avoid;
        }

        .pdf-export .section-title,
        .pdf-export .other-title,
        .pdf-export .other-subtitle {
            page-break-after: avoid;
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
                            <img src="{{ asset('storage/' . $student->photo) }}" alt="Pas Photo">
                        @else
                            Pas Photo
                            <br>
                            3 Ã— 4
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


            <div class="development-row development-sub">
                <div class="development-number">1.</div>
                <div class="development-label">Asal Sekolah</div>
                <div class="development-colon">:</div>
                <div class="development-value">
                    {{ $student->previousEducation?->previous_school_name ?? '' }}
                </div>
            </div>


            <div class="development-row development-sub">
                <div class="development-number">2.</div>
                <div class="development-label">Nama Sekolah</div>
                <div class="development-colon">:</div>
                <div class="development-value">
                    {{ $student->previousEducation?->previous_school_name ?? '' }}
                </div>
            </div>


            <div class="development-row development-sub">
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


            <div class="development-row development-sub">
                <div class="development-number">1.</div>
                <div class="development-label">
                    Nama Sekolah asal
                </div>
                <div class="development-colon">:</div>
                <div class="development-value">
                    {{ $student->previousEducation?->transfer_from_school ?? '' }}
                </div>
            </div>


            <div class="development-row development-sub">
                <div class="development-number">2.</div>
                <div class="development-label">
                    Dari Tingkat
                </div>
                <div class="development-colon">:</div>
                <div class="development-value">
                    {{ $student->previousEducation?->transfer_from_grade ?? '' }}
                </div>
            </div>


            <div class="development-row development-sub">
                <div class="development-number">3.</div>
                <div class="development-label">
                    Diterima Tanggal
                </div>
                <div class="development-colon">:</div>
                <div class="development-value">
                    {{ $student->previousEducation?->transfer_accepted_date ?? '' }}
                </div>
            </div>


            <div class="development-row development-sub">
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
