@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">

        {{-- =========================================================
         HEADER
    ========================================================== --}}
        <div class="d-flex justify-content-between align-items-start mb-4">

            <div>
                <div class="text-muted small mb-2">
                    Laporan
                    <span class="mx-2">›</span>
                    Buku Induk
                </div>

                <h3 class="fw-bold mb-1">
                    Buku Induk
                </h3>

                <p class="text-muted mb-0">
                    Cetak buku induk siswa sesuai format yang ditetapkan.
                </p>
            </div>

            <div class="d-flex flex-wrap justify-content-end gap-2 report-actions">

                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali
                </a>

                <button type="button" class="btn btn-outline-primary" id="headerPrintButton">
                    <i class="bi bi-printer me-1"></i> Print
                </button>

                <button type="button" class="btn btn-outline-danger" id="headerPdfButton">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
                </button>


            </div>

        </div>


        {{-- =========================================================
         MAIN CONTENT
    ========================================================== --}}
        <div class="row g-4 align-items-start">

            {{-- =====================================================
             LEFT : PARAMETER + JENIS CETAK
        ====================================================== --}}
            <div class="col-lg-5">

                <form method="GET" action="{{ route('buku-induk.index') }}" id="reportForm">

                    {{-- =================================================
                     1. PILIH PARAMETER LAPORAN
                ================================================== --}}
                    <div class="card border-0 shadow-sm mb-4">

                        <div class="card-header bg-white py-3">

                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-sliders text-primary me-2"></i>
                                1. Pilih Parameter Laporan
                            </h5>

                        </div>


                        <div class="card-body">

                            {{-- BARIS 1 --}}
                            <div class="row g-3">

                                {{-- TAHUN AJARAN --}}
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        Tahun Ajaran
                                    </label>

                                    <select name="academic_year_id" id="academicYear" class="form-select">

                                        @foreach ($academicYears as $year)
                                            <option value="{{ $year->id }}"
                                                {{ $selectedAcademicYear == $year->id ? 'selected' : '' }}>

                                                {{ $year->name }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>


                                {{-- SEMESTER --}}
                                <div class="col-md-4">

                                    <label class="form-label fw-semibold">
                                        Semester
                                    </label>

                                    <select name="semester_id" id="semester" class="form-select">

                                        <option value="">
                                            Semua Semester
                                        </option>

                                        @foreach ($semesters as $semester)
                                            <option value="{{ $semester->id }}"
                                                {{ request('semester_id') == $semester->id ? 'selected' : '' }}>

                                                {{ $semester->name }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                {{-- KELAS --}}
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Kelas</label>
                                    <select name="class_id" id="classSelect" class="form-select">
                                        <option value="">Semua Kelas</option>
                                        @foreach ($classes ?? collect() as $class)
                                            <option value="{{ $class->id }}"
                                                {{ request('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>


                            {{-- BARIS 2 --}}
                            <div class="row g-3 mt-1">

                                {{-- STATUS SISWA --}}
                                <div class="col-md-5">

                                    <label class="form-label fw-semibold">
                                        Status Siswa
                                    </label>

                                    <select name="status" id="statusSelect" class="form-select">

                                        <option value="aktif"
                                            {{ request('status', 'aktif') == 'aktif' ? 'selected' : '' }}>
                                            Aktif
                                        </option>

                                        <option value="tidak_aktif"
                                            {{ request('status') == 'tidak_aktif' ? 'selected' : '' }}>
                                            Tidak Aktif
                                        </option>

                                        <option value="" {{ request('status') === '' ? 'selected' : '' }}>
                                            Semua Status
                                        </option>

                                    </select>

                                </div>

                                {{-- CARI SISWA --}}
                                <div class="col-md-7">

                                    <label class="form-label fw-semibold">
                                        Cari Siswa
                                        <span class="text-muted fw-normal">
                                            (Opsional)
                                        </span>
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text bg-white">
                                            <i class="bi bi-search text-muted"></i>
                                        </span>

                                        <input type="text" name="search" id="studentSearch"
                                            value="{{ request('search') }}" class="form-control"
                                            placeholder="Ketik nama siswa...">

                                    </div>

                                </div>

                            </div>

                            {{-- INFO --}}
                            <div class="alert alert-primary mt-4 mb-0">

                                <div class="d-flex">

                                    <i class="bi bi-info-circle-fill me-2 mt-1"></i>

                                    <div class="small">

                                        Pilih parameter laporan terlebih dahulu.
                                        Data siswa akan menyesuaikan tahun ajaran,
                                        kelas, dan status yang dipilih.

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                     2. PILIH JENIS CETAK
                ================================================== --}}
                    <div class="card border-0 shadow-sm">

                        <div class="card-header bg-white py-3">

                            <h5 class="mb-0 fw-bold">
                                <i class="bi bi-printer text-primary me-2"></i>
                                2. Pilih Jenis Cetak
                            </h5>

                        </div>


                        <div class="card-body">

                            {{-- PILIHAN JENIS CETAK --}}
                            <div class="row g-3">

                                {{-- PER INDIVIDU --}}
                                <div class="col-md-4">

                                    <div class="print-type-card active" data-print-type="individual">

                                        <div class="radio-circle">
                                            <i class="bi bi-check"></i>
                                        </div>

                                        <div class="print-icon">
                                            <i class="bi bi-person-fill"></i>
                                        </div>

                                        <h6 class="fw-bold">
                                            Per Individu
                                        </h6>

                                        <small class="text-muted">
                                            Cetak buku induk satu siswa.
                                        </small>

                                    </div>

                                </div>


                                {{-- PER KELAS --}}
                                <div class="col-md-4">

                                    <div class="print-type-card" data-print-type="class">

                                        <div class="radio-circle">
                                            <i class="bi bi-check"></i>
                                        </div>

                                        <div class="print-icon">
                                            <i class="bi bi-people-fill"></i>
                                        </div>

                                        <h6 class="fw-bold">
                                            Per Kelas
                                        </h6>

                                        <small class="text-muted">
                                            Cetak buku induk seluruh siswa
                                            dalam satu kelas.
                                        </small>

                                    </div>

                                </div>


                                {{-- SEMUA KELAS --}}
                                <div class="col-md-4">

                                    <div class="print-type-card" data-print-type="all">

                                        <div class="radio-circle">
                                            <i class="bi bi-check"></i>
                                        </div>

                                        <div class="print-icon">
                                            <i class="bi bi-building"></i>
                                        </div>

                                        <h6 class="fw-bold">
                                            Semua Kelas
                                        </h6>

                                        <small class="text-muted">
                                            Cetak buku induk seluruh siswa.
                                        </small>

                                    </div>

                                </div>

                            </div>


                            {{-- PILIH SISWA --}}
                            <div class="mt-4" id="studentSelection">

                                <label class="form-label fw-semibold">

                                    Pilih Siswa
                                    <span class="text-danger">*</span>

                                </label>


                                <select name="student_id" id="studentPrint" class="form-select">

                                    <option value="">
                                        Pilih Siswa
                                    </option>

                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}"
                                            data-class-ids="{{ $student->classAssignments->pluck('class_id')->implode(',') }}"
                                            data-student-name="{{ strtolower($student->name) }}"
                                            {{ request('student_id') == $student->id ? 'selected' : '' }}>

                                            {{ $student->name }}
                                            ({{ $student->nis }})
                                        </option>
                                    @endforeach

                                </select>


                                <div class="form-text">
                                    Pilih satu siswa untuk mencetak Buku Induk
                                    secara individu.
                                </div>

                            </div>


                            {{-- INFO JENIS CETAK --}}
                            <div class="alert alert-info mt-3 mb-0" id="printTypeInfo">

                                <i class="bi bi-info-circle me-2"></i>

                                <span>
                                    Mode <strong>Per Individu</strong> dipilih.
                                    Silakan pilih siswa yang akan dicetak.
                                </span>

                            </div>


                            {{-- ACTION --}}
                            <div class="d-flex justify-content-end gap-2 mt-4">

                                <button type="button" class="btn btn-outline-secondary" id="resetButton">

                                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                                    Reset

                                </button>


                                <button type="button" class="btn btn-primary" id="previewButton">

                                    <i class="bi bi-search me-1"></i>
                                    Preview Laporan

                                </button>

                            </div>

                        </div>

                    </div>

                </form>

            </div>


            {{-- =====================================================
             RIGHT : PREVIEW
        ====================================================== --}}
            <div class="col-lg-7">

                <div class="card border-0 shadow-sm">

                    <div class="card-header bg-white py-3">

                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-file-earmark-text text-primary me-2"></i>
                            3. Preview Buku Induk
                        </h5>

                    </div>


                    <div class="card-body">

                        {{-- INFO PREVIEW --}}
                        <div class="alert alert-primary d-flex align-items-start mb-3">

                            <i class="bi bi-info-circle-fill me-2 mt-1"></i>

                            <div class="small">

                                Berikut adalah preview Buku Induk siswa.
                                Pastikan data sudah sesuai sebelum mencetak
                                atau menyimpan dokumen sebagai PDF.

                            </div>

                        </div>


                        {{-- PREVIEW --}}
                        @if ($selectedStudent)
                            <div class="preview-toolbar mb-3">

                                <div>
                                    <span class="text-muted small">
                                        Siswa:
                                    </span>

                                    <strong>
                                        {{ $selectedStudent->name }}
                                    </strong>
                                </div>


                                <button type="button" class="btn btn-sm btn-primary" id="printPreviewButton">

                                    <i class="bi bi-printer me-1"></i>
                                    Print

                                </button>

                            </div>


                            <div class="preview-wrapper">

                                <iframe id="previewFrame" class="preview-frame"
                                    src="{{ route('buku-induk.print', $selectedStudent) . '?' . http_build_query(request()->only(['academic_year_id', 'semester_id', 'class_id', 'status'])) }}"
                                    title="Preview Buku Induk {{ $selectedStudent->name }}">
                                </iframe>

                            </div>
                        @else
                            <div class="preview-placeholder">

                                <div class="preview-placeholder-icon">

                                    <i class="bi bi-journal-text"></i>

                                </div>

                                <h5 class="fw-bold mt-3">
                                    Preview Buku Induk
                                </h5>

                                <p class="text-muted mb-0">

                                    Pilih parameter dan siswa terlebih dahulu,
                                    kemudian klik <strong>Preview Laporan</strong>
                                    untuk menampilkan dokumen.

                                </p>

                            </div>
                        @endif

                    </div>

                </div>


                {{-- =================================================
                 KETERANGAN
            ================================================== --}}
                <div class="card border-0 shadow-sm mt-4">

                    <div class="card-body">

                        <div class="d-flex align-items-start">

                            <div class="information-icon me-3">
                                <i class="bi bi-lightbulb"></i>
                            </div>

                            <div>

                                <h6 class="fw-bold mb-2">
                                    Keterangan
                                </h6>

                                <ul class="mb-0 text-muted small">

                                    <li class="mb-1">
                                        Pilih jenis cetak sesuai kebutuhan:
                                        <strong>Per Individu</strong>,
                                        <strong>Per Kelas</strong>, atau
                                        <strong>Semua Kelas</strong>.
                                    </li>

                                    <li class="mb-1">
                                        Preview ditampilkan terlebih dahulu
                                        sebelum dokumen dicetak.
                                    </li>

                                    <li>
                                        Untuk menyimpan sebagai PDF, gunakan
                                        menu <strong>Print</strong> kemudian
                                        pilih <strong>Save as PDF</strong>
                                        pada dialog pencetakan browser.
                                    </li>

                                </ul>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- Daftar siswa untuk mode Per Kelas dan Semua Kelas --}}
    <div class="modal fade" id="bulkStudentModal" tabindex="-1" aria-labelledby="bulkStudentModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title fw-bold" id="bulkStudentModalLabel">Pilih Siswa untuk Pratinjau</h5>
                        <p class="text-muted small mb-0" id="bulkStudentModalDescription"></p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body">
                    <div class="list-group" id="bulkStudentList"></div>
                    <div class="text-center text-muted py-4 d-none" id="bulkStudentEmpty">
                        Tidak ada siswa yang sesuai dengan parameter yang dipilih.
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <span class="small text-muted" id="bulkStudentCount"></span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-success" id="downloadAllDocumentsButton">
                            <i class="bi bi-download me-1"></i> Unduh Semua File
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- =============================================================
     STYLE
============================================================= --}}
    <style>
        .print-type-card {
            position: relative;
            border: 1px solid #dce3ed;
            border-radius: 10px;
            padding: 18px 12px;
            min-height: 145px;
            text-align: center;
            cursor: pointer;
            background: #fff;
            transition: all .2s ease;
            overflow: hidden;
        }


        .print-type-card:hover {
            border-color: #0d6efd;
            background: #f8fbff;
            transform: translateY(-1px);
        }


        .print-type-card.active {
            border-color: #0d6efd;
            background: #f5f9ff;
            box-shadow: 0 0 0 1px rgba(13, 110, 253, .08);
        }


        .radio-circle {
            position: absolute;
            top: 10px;
            left: 10px;

            width: 18px;
            height: 18px;

            border: 1.5px solid #cbd5e1;
            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            color: transparent;
            font-size: 11px;
        }


        .print-type-card.active .radio-circle {
            border-color: #0d6efd;
            background: #0d6efd;
            color: #fff;
        }


        .print-icon {
            font-size: 35px;
            color: #0d6efd;
            margin-bottom: 7px;
        }


        .print-type-card h6 {
            margin-bottom: 5px;
        }


        .print-type-card small {
            display: block;
            line-height: 1.45;
        }


        .preview-toolbar {
            min-height: 42px;
            border: 1px solid #e3e8ef;
            border-radius: 8px;
            padding: 8px 12px;

            display: flex;
            align-items: center;
            justify-content: space-between;

            background: #fff;
        }


        .preview-wrapper {
            width: 100%;
            overflow: hidden;
            border-radius: 8px;
            background: #343a40;
            border: 1px solid #dce3ed;
        }


        .preview-frame {
            display: block;
            width: 100%;
            height: 620px;
            border: 0;
            background: #fff;
        }


        .preview-placeholder {
            min-height: 620px;

            border: 2px dashed #dce3ed;
            border-radius: 10px;

            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            text-align: center;

            padding: 40px;
            color: #6c757d;
        }


        .preview-placeholder-icon {
            width: 75px;
            height: 75px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #f0f6ff;
            color: #0d6efd;

            font-size: 38px;
        }


        .information-icon {
            width: 40px;
            height: 40px;

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #fff8e8;
            color: #f59e0b;

            font-size: 20px;
        }


        .form-select,
        .form-control {
            min-height: 42px;
            border-color: #dce3ed;
        }


        .form-select:focus,
        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .15rem rgba(13, 110, 253, .12);
        }


        .alert {
            border-radius: 8px;
        }


        @media (max-width: 991.98px) {

            .preview-frame,
            .preview-placeholder {
                min-height: 500px;
                height: 500px;
            }

        }
    </style>


    {{-- =============================================================
     JAVASCRIPT
============================================================= --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let selectedPrintType = 'individual';


            const printCards =
                document.querySelectorAll('[data-print-type]');

            const studentSelection =
                document.getElementById('studentSelection');

            const studentSelect =
                document.getElementById('studentPrint');

            const classSelect =
                document.getElementById('classSelect');

            const searchInput =
                document.getElementById('studentSearch');

            const printTypeInfo =
                document.getElementById('printTypeInfo');

            const previewButton =
                document.getElementById('previewButton');

            const resetButton =
                document.getElementById('resetButton');

            const printPreviewButton =
                document.getElementById('printPreviewButton');

            const headerPrintButton =
                document.getElementById('headerPrintButton');

            const headerPdfButton =
                document.getElementById('headerPdfButton');

            const bulkStudentModalElement =
                document.getElementById('bulkStudentModal');

            const bulkStudentList =
                document.getElementById('bulkStudentList');

            const bulkStudentEmpty =
                document.getElementById('bulkStudentEmpty');

            const bulkStudentCount =
                document.getElementById('bulkStudentCount');

            const bulkStudentDescription =
                document.getElementById('bulkStudentModalDescription');

            const downloadAllDocumentsButton =
                document.getElementById('downloadAllDocumentsButton');

            let bulkStudents = [];


            /* =========================================================
               PILIH JENIS CETAK
            ========================================================== */

            printCards.forEach(function(card) {

                card.addEventListener('click', function() {

                    printCards.forEach(function(item) {
                        item.classList.remove('active');
                    });

                    card.classList.add('active');

                    selectedPrintType =
                        card.dataset.printType;

                    updatePrintType();

                });

            });


            /* =========================================================
               UPDATE TAMPILAN BERDASARKAN JENIS CETAK
            ========================================================== */

            function updatePrintType() {

                if (!studentSelection || !printTypeInfo) {
                    return;
                }


                if (selectedPrintType === 'individual') {

                    studentSelection.style.display = 'block';

                    printTypeInfo.className =
                        'alert alert-info mt-3 mb-0';

                    printTypeInfo.innerHTML =
                        '<i class="bi bi-info-circle me-2"></i>' +
                        'Mode <strong>Per Individu</strong> dipilih. ' +
                        'Silakan pilih siswa yang akan dicetak.';

                } else if (selectedPrintType === 'class') {

                    studentSelection.style.display = 'none';

                    printTypeInfo.className =
                        'alert alert-info mt-3 mb-0';

                    printTypeInfo.innerHTML =
                        '<i class="bi bi-people me-2"></i>' +
                        'Mode <strong>Per Kelas</strong> dipilih. ' +
                        'Silakan pilih kelas pada parameter laporan. ' +
                        'Semua siswa pada kelas tersebut akan menjadi ' +
                        'data cetak.';

                } else if (selectedPrintType === 'all') {

                    studentSelection.style.display = 'none';

                    printTypeInfo.className =
                        'alert alert-info mt-3 mb-0';

                    printTypeInfo.innerHTML =
                        '<i class="bi bi-building me-2"></i>' +
                        'Mode <strong>Semua Kelas</strong> dipilih. ' +
                        'Sistem akan menggunakan seluruh siswa sesuai ' +
                        'parameter tahun ajaran dan status.';

                }

            }


            /* =========================================================
               FILTER SISWA BERDASARKAN KELAS
            ========================================================== */

            function filterStudents() {

                if (!studentSelect) {
                    return;
                }


                const classId =
                    classSelect ? classSelect.value : '';

                const keyword =
                    searchInput ?
                    searchInput.value.toLowerCase().trim() :
                    '';


                const options =
                    studentSelect.querySelectorAll('option');


                options.forEach(function(option, index) {

                    if (index === 0) {
                        option.hidden = false;
                        return;
                    }


                    const optionClassIds =
                        (option.dataset.classIds || '').split(',').filter(Boolean);

                    const studentName =
                        option.dataset.studentName || '';


                    const matchClass = !classId ||
                        optionClassIds.includes(classId);


                    const matchSearch = !keyword ||
                        studentName.includes(keyword);


                    option.hidden = !(matchClass && matchSearch);

                });


                /*
                 * Jika siswa yang sedang dipilih ternyata tidak sesuai
                 * filter, kosongkan pilihan.
                 */

                if (
                    studentSelect.value &&
                    studentSelect.selectedOptions[0] &&
                    studentSelect.selectedOptions[0].hidden
                ) {

                    studentSelect.value = '';

                }

            }


            function showBulkStudentModal() {
                if (!studentSelect || !bulkStudentModalElement) {
                    return;
                }

                bulkStudents = Array.from(studentSelect.options)
                    .filter(function(option, index) {
                        return index > 0 && !option.hidden;
                    })
                    .map(function(option) {
                        return {
                            id: option.value,
                            name: option.textContent.trim(),
                        };
                    });

                const modeLabel = selectedPrintType === 'class' ? 'kelas yang dipilih' : 'semua kelas';
                bulkStudentDescription.textContent =
                    'Pilih nama siswa untuk melihat pratinjau Buku Induk satu per satu (' + modeLabel + ').';
                bulkStudentCount.textContent = bulkStudents.length + ' siswa ditemukan';
                bulkStudentList.innerHTML = '';
                bulkStudentEmpty.classList.toggle('d-none', bulkStudents.length > 0);
                downloadAllDocumentsButton.disabled = bulkStudents.length === 0;

                bulkStudents.forEach(function(student, index) {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className =
                        'list-group-item list-group-item-action d-flex align-items-center justify-content-between';
                    item.innerHTML =
                        '<span><span class="text-muted me-3">' + (index + 1) +
                        '.</span><strong></strong></span>' +
                        '<span class="btn btn-sm btn-outline-primary"><i class="bi bi-eye me-1"></i>Pratinjau</span>';
                    item.querySelector('strong').textContent = student.name;
                    item.addEventListener('click', function() {
                        const parameters = reportParameters();
                        parameters.set('print_type', 'individual');
                        parameters.set('student_id', student.id);
                        window.location.href = '{{ route('buku-induk.index') }}?' + parameters
                            .toString();
                    });
                    bulkStudentList.appendChild(item);
                });

                bootstrap.Modal.getOrCreateInstance(bulkStudentModalElement).show();
            }

            downloadAllDocumentsButton?.addEventListener('click', function() {
                if (!bulkStudents.length) {
                    return;
                }

                const parameters = reportParameters();
                parameters.set('pdf', '1');
                bulkStudents.forEach(function(student) {
                    parameters.append('student_ids[]', student.id);
                });

                const downloadLink = document.createElement('a');
                downloadLink.href = '{{ route('buku-induk.download-all') }}?' + parameters.toString();
                document.body.appendChild(downloadLink);
                downloadLink.click();
                downloadLink.remove();
            });


            if (classSelect) {

                classSelect.addEventListener(
                    'change',
                    filterStudents
                );

            }


            if (searchInput) {

                searchInput.addEventListener(
                    'input',
                    filterStudents
                );

            }


            /* =========================================================
               PREVIEW LAPORAN
            ========================================================== */

            if (previewButton) {

                previewButton.addEventListener('click', function() {

                    const academicYear =
                        document.getElementById('academicYear')?.value || '';

                    const semester =
                        document.getElementById('semester')?.value || '';

                    const classId =
                        document.getElementById('classSelect')?.value || '';

                    const status =
                        document.getElementById('statusSelect')?.value || '';

                    const studentId =
                        studentSelect?.value || '';


                    /* -----------------------------------------------
                       VALIDASI PER INDIVIDU
                    ------------------------------------------------ */

                    if (
                        selectedPrintType === 'individual' &&
                        !studentId
                    ) {

                        alert(
                            'Silakan pilih siswa terlebih dahulu.'
                        );

                        return;

                    }


                    /* -----------------------------------------------
                       VALIDASI PER KELAS
                    ------------------------------------------------ */

                    if (
                        selectedPrintType === 'class' &&
                        !classId
                    ) {

                        alert(
                            'Silakan pilih kelas terlebih dahulu.'
                        );

                        return;

                    }


                    /* -----------------------------------------------
                       SEMUA KELAS
                    ------------------------------------------------ */

                    if (
                        selectedPrintType === 'all'
                    ) {

                        /*
                         * Tidak membutuhkan class_id.
                         * Backend nantinya menentukan seluruh siswa
                         * berdasarkan parameter laporan.
                         */

                    }


                    /*
                     * Untuk mode individu, kita submit form agar
                     * controller menghasilkan selectedStudent dan
                     * iframe preview menggunakan parameter terbaru.
                     */

                    if (selectedPrintType === 'individual') {

                        const form =
                            document.getElementById('reportForm');

                        if (form) {

                            form.submit();

                        }

                        return;

                    }


                    showBulkStudentModal();

                });

            }


            /* =========================================================
               RESET
            ========================================================== */

            if (resetButton) {

                resetButton.addEventListener('click', function() {

                    window.location.href =
                        "{{ route('buku-induk.index') }}";

                });

            }


            /* =========================================================
               PRINT DARI PREVIEW
            ========================================================== */

            if (printPreviewButton) {

                printPreviewButton.addEventListener(
                    'click',
                    function() {

                        const frame =
                            document.getElementById('previewFrame');


                        if (!frame) {

                            alert(
                                'Preview Buku Induk belum tersedia.'
                            );

                            return;

                        }


                        try {

                            frame.contentWindow.focus();
                            frame.contentWindow.print();

                        } catch (error) {

                            window.open(
                                frame.src,
                                '_blank'
                            );

                        }

                    }
                );

            }


            /* =========================================================
               AKSI CEPAT DI HEADER
            ========================================================== */

            function reportParameters() {
                const form = document.getElementById('reportForm');
                const parameters = new URLSearchParams(new FormData(form));

                parameters.set('print_type', selectedPrintType);

                return parameters;
            }

            function printCurrentReport() {
                const frame = document.getElementById('previewFrame');

                if (selectedPrintType === 'individual') {
                    if (!frame) {
                        alert('Tampilkan preview laporan terlebih dahulu sebelum mencetak.');
                        return;
                    }

                    try {
                        frame.contentWindow.focus();
                        frame.contentWindow.print();
                    } catch (error) {
                        window.open(frame.src, '_blank');
                    }

                    return;
                }

                if (selectedPrintType === 'class' && !classSelect?.value) {
                    alert('Silakan pilih kelas terlebih dahulu.');
                    return;
                }

                showBulkStudentModal();
            }

            function downloadCurrentDocument() {
                if (selectedPrintType === 'class' && !classSelect?.value) {
                    alert('Silakan pilih kelas terlebih dahulu.');
                    return;
                }

                if (selectedPrintType !== 'individual') {
                    showBulkStudentModal();
                    return;
                }

                if (!studentSelect?.value) {
                    alert('Silakan pilih siswa terlebih dahulu.');
                    return;
                }

                printCurrentReport();
            }

            headerPrintButton?.addEventListener('click', printCurrentReport);

            headerPdfButton?.addEventListener('click', downloadCurrentDocument);


            /* =========================================================
               INITIAL FILTER
            ========================================================== */

            filterStudents();

            updatePrintType();

        });
    </script>
@endsection
