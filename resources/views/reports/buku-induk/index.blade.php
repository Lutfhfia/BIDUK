@extends('layouts.app')

@section('title', 'Buku Induk')

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

                <h3 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-book-fill text-success me-2"></i>Buku Induk
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

                <button type="button" class="btn btn-danger" id="headerPdfButton">
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
                    <input type="hidden" name="print_type" id="printTypeInput"
                        value="{{ request('print_type', 'individual') }}">

                    {{-- =================================================
                     1. PILIH PARAMETER LAPORAN
                ================================================== --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-sliders text-success me-2"></i>
                                1. Pilih Parameter Laporan
                            </h5>
                        </div>

                        <div class="card-body">
                            {{-- BARIS 1: TAHUN AJARAN, SEMESTER, KELAS --}}
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

                            {{-- BARIS 2: STATUS SISWA & PENCARIAN SISWA PARAMETER --}}
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

                                {{-- CARI NAMA / NIS PARAMETER --}}
                                <div class="col-md-7">
                                    <label class="form-label fw-semibold">
                                        Pencarian <span class="text-muted fw-normal">(Nama / NIS)</span>
                                    </label>
                                    <div class="position-relative">
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-end-0">
                                                <i class="bi bi-search text-muted"></i>
                                            </span>
                                            <input type="text" name="search" id="studentSearch"
                                                value="{{ $selectedStudent ? $selectedStudent->name . ' (' . ($selectedStudent->nis ?: '-') . ')' : request('search') }}"
                                                class="form-control border-start-0 ps-0"
                                                placeholder="Cari nama siswa atau NIS..." autocomplete="off">
                                            <button type="button" class="btn btn-outline-secondary d-none" id="clearStudentSearch1" title="Hapus pencarian">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>

                                        {{-- Dropdown Hasil Pencarian Card 1 --}}
                                        <div id="studentSearchResults1" class="dropdown-menu shadow-sm w-100 p-0 border mt-1"
                                            style="display: none; max-height: 280px; overflow-y: auto; z-index: 1050;">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- INFO PARAMETER --}}
                            <div class="alert alert-success bg-success-subtle text-success-emphasis border-0 mt-4 mb-0">
                                <div class="d-flex">
                                    <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                                    <div class="small">
                                        Pilih parameter laporan atau ketik nama/NIS pada pencarian.
                                        Hasil pencarian siswa akan muncul secara langsung.
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
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-printer text-success me-2"></i>
                                2. Pilih Jenis Cetak
                            </h5>
                        </div>

                        <div class="card-body">
                            {{-- PILIHAN JENIS CETAK --}}
                            <div class="row g-3">
                                {{-- PER INDIVIDU --}}
                                <div class="col-md-4">
                                    <div class="print-type-card {{ request('print_type', 'individual') === 'individual' ? 'active' : '' }}"
                                        data-print-type="individual">
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
                                    <div class="print-type-card {{ request('print_type') === 'class' ? 'active' : '' }}"
                                        data-print-type="class">
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
                                            Cetak buku induk seluruh siswa dalam satu kelas.
                                        </small>
                                    </div>
                                </div>

                                {{-- SEMUA KELAS --}}
                                <div class="col-md-4">
                                    <div class="print-type-card {{ request('print_type') === 'all' ? 'active' : '' }}"
                                        data-print-type="all">
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


                            {{-- PILIH SISWA (MODE INDIVIDU SEARCHABLE DROPDOWN) --}}
                            <div class="mt-4" id="studentSelection">
                                <label class="form-label fw-semibold">
                                    Pilih Siswa <span class="text-danger">*</span>
                                </label>

                                <div class="position-relative">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-search text-muted"></i>
                                        </span>
                                        <input type="text" id="studentSearchInput" class="form-control border-start-0 ps-0"
                                            placeholder="Cari nama siswa atau NIS..." autocomplete="off"
                                            value="{{ $selectedStudent ? $selectedStudent->name . ' (' . ($selectedStudent->nis ?: '-') . ')' : '' }}">
                                        <button type="button" class="btn btn-outline-secondary d-none" id="clearStudentSearchInput" title="Hapus pencarian">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>

                                    {{-- Hidden student_id input --}}
                                    <input type="hidden" name="student_id" id="studentIdInput"
                                        value="{{ $selectedStudent?->id ?? request('student_id') }}">

                                    {{-- Dropdown Hasil Pencarian Card 2 --}}
                                    <div id="studentSearchResults" class="dropdown-menu shadow-sm w-100 p-0 border mt-1"
                                        style="display: none; max-height: 280px; overflow-y: auto; z-index: 1050;">
                                    </div>
                                </div>

                                {{-- Card Informasi Siswa Terpilih --}}
                                <div id="selectedStudentCard"
                                    class="mt-2 p-2 px-3 bg-light rounded border d-flex justify-content-between align-items-center {{ $selectedStudent ? '' : 'd-none' }}">
                                    <div class="small">
                                        <i class="bi bi-person-check-fill text-success me-1"></i>
                                        <span class="fw-bold text-dark" id="selectedStudentName">{{ $selectedStudent?->name }}</span>
                                        <span class="text-muted ms-1">(NIS: <span id="selectedStudentNis">{{ $selectedStudent?->nis ?: '-' }}</span>)</span>
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle ms-2" id="selectedStudentClass">
                                            {{ $selectedStudent?->current_class_name ? 'Kelas ' . $selectedStudent->current_class_name : 'Siswa' }}
                                        </span>
                                    </div>
                                    <button type="button" class="btn btn-sm text-danger p-0 ms-2" id="deselectStudentBtn" title="Batal pilih">
                                        <i class="bi bi-x-circle-fill fs-6"></i>
                                    </button>
                                </div>

                                {{-- Fallback Hidden Native Select --}}
                                <select id="studentPrint" class="d-none">
                                    <option value="">Pilih Siswa</option>
                                    @foreach ($students as $student)
                                        @php
                                            $clsName = $student->classAssignments->firstWhere('status', 'Aktif')?->schoolClass?->name
                                                ?? $student->classes->first()?->name
                                                ?? '-';
                                        @endphp
                                        <option value="{{ $student->id }}"
                                            data-nis="{{ $student->nis }}"
                                            data-class-name="{{ $clsName }}"
                                            data-class-ids="{{ $student->classAssignments->pluck('class_id')->implode(',') }}"
                                            data-student-name="{{ strtolower($student->name) }}"
                                            {{ ($selectedStudent?->id ?? request('student_id')) == $student->id ? 'selected' : '' }}>
                                            {{ $student->name }} ({{ $student->nis }})
                                        </option>
                                    @endforeach
                                </select>

                                <div class="form-text mt-1 text-muted">
                                    Ketik nama atau NIS, lalu pilih salah satu siswa dari daftar dropdown pencarian.
                                </div>
                            </div>


                            {{-- INFO JENIS CETAK --}}
                            <div class="alert alert-info mt-3 mb-0" id="printTypeInfo">
                                <i class="bi bi-info-circle me-2"></i>
                                <span>
                                    Mode <strong>Per Individu</strong> dipilih.
                                    Silakan cari dan pilih siswa yang akan dicetak.
                                </span>
                            </div>


                            {{-- ACTION --}}
                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <button type="button" class="btn btn-outline-secondary" id="resetButton">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                                    Reset
                                </button>

                                <button type="button" class="btn btn-biduk-primary" id="previewButton">
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
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-file-earmark-text text-success me-2"></i>
                            3. Preview Buku Induk
                        </h5>
                    </div>

                    <div class="card-body">
                        {{-- INFO PREVIEW --}}
                        <div class="alert alert-success bg-success-subtle text-success-emphasis border-0 d-flex align-items-start mb-3">
                            <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                            <div class="small">
                                Berikut adalah preview Buku Induk siswa.
                                Pastikan data sudah sesuai sebelum mencetak
                                atau menyimpan dokumen sebagai PDF.
                            </div>
                        </div>

                        {{-- PREVIEW FRAME / PLACEHOLDER --}}
                        @if ($selectedStudent)
                            <div class="preview-toolbar mb-3">
                                <div>
                                    <span class="text-muted small">
                                        Siswa:
                                    </span>
                                    <strong class="text-dark">
                                        {{ $selectedStudent->name }}
                                    </strong>
                                </div>

                                <button type="button" class="btn btn-sm btn-danger" id="previewPdfButton">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>
                                    Export PDF
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

                                <h5 class="fw-bold mt-3 text-dark">
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

                {{-- KETERANGAN --}}
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-body">
                        <div class="d-flex align-items-start">
                            <div class="information-icon me-3">
                                <i class="bi bi-lightbulb"></i>
                            </div>

                            <div>
                                <h6 class="fw-bold mb-2 text-dark">
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
                                        menu <strong>Export PDF</strong> atau menu
                                        <strong>Print</strong> pada browser.
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Daftar Siswa untuk Mode Per Kelas / Semua Kelas --}}
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
                        Data siswa tidak ditemukan sesuai dengan parameter yang dipilih.
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <span class="small text-muted" id="bulkStudentCount"></span>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-success" id="downloadAllDocumentsButton">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Unduh 1 File PDF
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
            border: 1.5px solid #dce3ed;
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
            border-color: var(--biduk-primary, #1a7a4c);
            background: var(--biduk-primary-light, #e8f5ee);
            transform: translateY(-1px);
        }

        .print-type-card.active {
            border-color: var(--biduk-primary, #1a7a4c);
            background: var(--biduk-primary-light, #e8f5ee);
            box-shadow: 0 0 0 1px rgba(26, 122, 76, .15);
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
            border-color: var(--biduk-primary, #1a7a4c);
            background: var(--biduk-primary, #1a7a4c);
            color: #fff;
        }

        .print-icon {
            font-size: 35px;
            color: var(--biduk-primary, #1a7a4c);
            margin-bottom: 7px;
        }

        .print-type-card h6 {
            margin-bottom: 5px;
            color: #212529;
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
            background: var(--biduk-primary-light, #e8f5ee);
            color: var(--biduk-primary, #1a7a4c);
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
            border-color: var(--biduk-primary, #1a7a4c);
            box-shadow: 0 0 0 0.15rem rgba(26, 122, 76, 0.15);
        }

        .alert {
            border-radius: 8px;
        }

        /* Search Dropdown item hover */
        .student-search-item {
            cursor: pointer;
            transition: background-color 0.15s ease;
        }
        .student-search-item:hover, .student-search-item:focus {
            background-color: var(--biduk-primary-light, #e8f5ee) !important;
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

            let selectedPrintType = @json(request('print_type', 'individual'));
            const printTypeInput = document.getElementById('printTypeInput');

            const printCards = document.querySelectorAll('[data-print-type]');
            const studentSelection = document.getElementById('studentSelection');
            const studentSelect = document.getElementById('studentPrint');
            const classSelect = document.getElementById('classSelect');
            const printTypeInfo = document.getElementById('printTypeInfo');
            const previewButton = document.getElementById('previewButton');
            const resetButton = document.getElementById('resetButton');
            const previewPdfButton = document.getElementById('previewPdfButton');
            const headerPdfButton = document.getElementById('headerPdfButton');

            // Search Inputs in Card 1 & Card 2
            const studentSearch = document.getElementById('studentSearch'); // Card 1
            const studentSearchInput = document.getElementById('studentSearchInput'); // Card 2

            // Hidden Student ID Input
            const studentIdInput = document.getElementById('studentIdInput');

            // Search Dropdown Elements
            const studentSearchResults1 = document.getElementById('studentSearchResults1');
            const studentSearchResults = document.getElementById('studentSearchResults');

            // Selected Student Info Card
            const selectedStudentCard = document.getElementById('selectedStudentCard');
            const selectedStudentName = document.getElementById('selectedStudentName');
            const selectedStudentNis = document.getElementById('selectedStudentNis');
            const selectedStudentClass = document.getElementById('selectedStudentClass');

            // Clear Buttons
            const deselectStudentBtn = document.getElementById('deselectStudentBtn');
            const clearStudentSearch1 = document.getElementById('clearStudentSearch1');
            const clearStudentSearchInput = document.getElementById('clearStudentSearchInput');

            // Bulk modal elements
            const bulkStudentModalElement = document.getElementById('bulkStudentModal');
            const bulkStudentList = document.getElementById('bulkStudentList');
            const bulkStudentEmpty = document.getElementById('bulkStudentEmpty');
            const bulkStudentCount = document.getElementById('bulkStudentCount');
            const bulkStudentDescription = document.getElementById('bulkStudentModalDescription');
            const downloadAllDocumentsButton = document.getElementById('downloadAllDocumentsButton');

            let bulkStudents = [];

            // Build Client-Side Student Data array from native select
            function getStudentsData() {
                if (!studentSelect) return [];
                return Array.from(studentSelect.options)
                    .filter(opt => opt.value !== '')
                    .map(opt => ({
                        id: opt.value,
                        name: opt.getAttribute('data-student-name') || opt.textContent.trim(),
                        displayName: opt.textContent.split('(')[0].trim(),
                        nis: String(opt.getAttribute('data-nis') || ''),
                        className: opt.getAttribute('data-class-name') || '-',
                        classIds: (opt.getAttribute('data-class-ids') || '').split(',').filter(Boolean),
                        hidden: opt.hidden
                    }));
            }

            /* =========================================================
               SEARCHABLE STUDENT DROPDOWN RENDER
            ========================================================== */
            function renderSearchResults(keyword, targetContainer) {
                if (!targetContainer) return;

                const classId = classSelect ? classSelect.value : '';
                const query = (keyword || '').toLowerCase().trim();
                const allStudents = getStudentsData();

                // Filter matching students
                const matches = allStudents.filter(student => {
                    const matchClass = !classId || student.classIds.length === 0 || student.classIds.includes(String(classId));
                    const matchSearch = !query ||
                        student.name.toLowerCase().includes(query) ||
                        student.displayName.toLowerCase().includes(query) ||
                        student.nis.toLowerCase().includes(query);
                    return matchClass && matchSearch;
                });

                targetContainer.innerHTML = '';

                if (matches.length === 0) {
                    targetContainer.innerHTML = `
                        <div class="p-3 text-center text-muted small">
                            <i class="bi bi-search me-1"></i> Data siswa tidak ditemukan
                        </div>
                    `;
                } else {
                    const listContainer = document.createElement('div');
                    listContainer.className = 'list-group list-group-flush';

                    matches.forEach(student => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className = 'list-group-item list-group-item-action py-2 px-3 student-search-item';
                        btn.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold text-dark text-start">${student.displayName}</div>
                                    <div class="small text-muted text-start">NIS: ${student.nis || '-'}</div>
                                </div>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success-subtle ms-2">
                                    Kelas ${student.className}
                                </span>
                            </div>
                        `;
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            selectStudent(student);
                        });
                        listContainer.appendChild(btn);
                    });

                    targetContainer.appendChild(listContainer);
                }

                targetContainer.style.display = 'block';
            }

            function selectStudent(student) {
                if (studentIdInput) studentIdInput.value = student.id;
                if (studentSelect) studentSelect.value = student.id;

                const displayVal = `${student.displayName} (${student.nis || '-'})`;
                if (studentSearch) studentSearch.value = displayVal;
                if (studentSearchInput) studentSearchInput.value = displayVal;

                if (selectedStudentName) selectedStudentName.textContent = student.displayName;
                if (selectedStudentNis) selectedStudentNis.textContent = student.nis || '-';
                if (selectedStudentClass) selectedStudentClass.textContent = `Kelas ${student.className}`;

                if (selectedStudentCard) selectedStudentCard.classList.remove('d-none');
                if (clearStudentSearch1) clearStudentSearch1.classList.remove('d-none');
                if (clearStudentSearchInput) clearStudentSearchInput.classList.remove('d-none');

                if (studentSearchResults1) studentSearchResults1.style.display = 'none';
                if (studentSearchResults) studentSearchResults.style.display = 'none';

                // Automatically activate 'individual' print mode card
                if (selectedPrintType !== 'individual') {
                    selectedPrintType = 'individual';
                    if (printTypeInput) printTypeInput.value = 'individual';
                    updatePrintType();
                }
            }

            function clearSelection() {
                if (studentIdInput) studentIdInput.value = '';
                if (studentSelect) studentSelect.value = '';
                if (studentSearch) studentSearch.value = '';
                if (studentSearchInput) studentSearchInput.value = '';

                if (selectedStudentCard) selectedStudentCard.classList.add('d-none');
                if (clearStudentSearch1) clearStudentSearch1.classList.add('d-none');
                if (clearStudentSearchInput) clearStudentSearchInput.classList.add('d-none');

                if (studentSearchResults1) studentSearchResults1.style.display = 'none';
                if (studentSearchResults) studentSearchResults.style.display = 'none';
            }

            // Setup Search Input Helper
            function bindSearchInput(inputEl, dropdownEl, clearBtnEl) {
                if (!inputEl) return;
                let timer = null;

                inputEl.addEventListener('input', function() {
                    const val = this.value;
                    if (clearBtnEl) clearBtnEl.classList.toggle('d-none', !val);
                    if (studentIdInput && studentIdInput.value) {
                        studentIdInput.value = ''; // Reset selected ID if typing anew
                        if (selectedStudentCard) selectedStudentCard.classList.add('d-none');
                    }
                    clearTimeout(timer);
                    timer = setTimeout(function() {
                        renderSearchResults(val, dropdownEl);
                    }, 200);
                });

                inputEl.addEventListener('focus', function() {
                    renderSearchResults(this.value, dropdownEl);
                });

                inputEl.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const firstItem = dropdownEl.querySelector('.student-search-item');
                        if (firstItem) {
                            firstItem.click();
                        }
                    }
                });
            }

            bindSearchInput(studentSearch, studentSearchResults1, clearStudentSearch1);
            bindSearchInput(studentSearchInput, studentSearchResults, clearStudentSearchInput);

            if (deselectStudentBtn) deselectStudentBtn.addEventListener('click', clearSelection);

            if (clearStudentSearch1) {
                clearStudentSearch1.addEventListener('click', function() {
                    clearSelection();
                    if (studentSearch) studentSearch.focus();
                });
            }

            if (clearStudentSearchInput) {
                clearStudentSearchInput.addEventListener('click', function() {
                    clearSelection();
                    if (studentSearchInput) studentSearchInput.focus();
                });
            }

            // Hide search dropdown on click outside
            document.addEventListener('click', function(e) {
                if (studentSearchResults1 && studentSearch && !studentSearch.contains(e.target) && !studentSearchResults1.contains(e.target)) {
                    studentSearchResults1.style.display = 'none';
                }
                if (studentSearchResults && studentSearchInput && !studentSearchInput.contains(e.target) && !studentSearchResults.contains(e.target)) {
                    studentSearchResults.style.display = 'none';
                }
            });


            /* =========================================================
               PILIH JENIS CETAK
            ========================================================== */
            printCards.forEach(function(card) {
                card.addEventListener('click', function() {
                    printCards.forEach(item => item.classList.remove('active'));
                    card.classList.add('active');

                    selectedPrintType = card.dataset.printType;
                    if (printTypeInput) printTypeInput.value = selectedPrintType;

                    if (selectedPrintType === 'all' && classSelect) {
                        classSelect.value = '';
                        filterStudents();
                    }

                    updatePrintType();
                });
            });


            /* =========================================================
               UPDATE TAMPILAN BERDASARKAN JENIS CETAK
            ========================================================== */
            function updatePrintType() {
                if (!studentSelection || !printTypeInfo) return;

                printCards.forEach(item => {
                    item.classList.toggle('active', item.dataset.printType === selectedPrintType);
                });

                if (selectedPrintType === 'individual') {
                    studentSelection.style.display = 'block';
                    printTypeInfo.className = 'alert alert-info mt-3 mb-0';
                    printTypeInfo.innerHTML =
                        '<i class="bi bi-info-circle me-2"></i>' +
                        'Mode <strong>Per Individu</strong> dipilih. Silakan cari dan pilih siswa yang akan dicetak.';
                } else if (selectedPrintType === 'class') {
                    studentSelection.style.display = 'none';
                    printTypeInfo.className = 'alert alert-info mt-3 mb-0';
                    printTypeInfo.innerHTML =
                        '<i class="bi bi-people me-2"></i>' +
                        'Mode <strong>Per Kelas</strong> dipilih. Silakan pilih kelas pada parameter laporan. ' +
                        'Seluruh siswa pada kelas tersebut akan menjadi data cetak / export PDF.';
                } else if (selectedPrintType === 'all') {
                    studentSelection.style.display = 'none';
                    printTypeInfo.className = 'alert alert-info mt-3 mb-0';
                    printTypeInfo.innerHTML =
                        '<i class="bi bi-building me-2"></i>' +
                        'Mode <strong>Semua Kelas</strong> dipilih. Sistem akan menggunakan seluruh siswa sesuai parameter tahun ajaran dan status.';
                }
            }


            /* =========================================================
               FILTER SISWA BERDASARKAN KELAS
            ========================================================== */
            function filterStudents() {
                if (!studentSelect) return;

                const classId = classSelect ? classSelect.value : '';
                const keyword = studentSearch ? studentSearch.value.toLowerCase().trim() : '';
                const options = studentSelect.querySelectorAll('option');

                options.forEach(function(option, index) {
                    if (index === 0) {
                        option.hidden = false;
                        return;
                    }

                    const optionClassIds = (option.dataset.classIds || '').split(',').filter(Boolean);
                    const studentName = option.dataset.studentName || '';
                    const studentNis = (option.dataset.nis || '').toLowerCase();

                    const matchClass = !classId || optionClassIds.length === 0 || optionClassIds.includes(String(classId));
                    const matchSearch = !keyword || studentName.includes(keyword) || studentNis.includes(keyword);

                    option.hidden = !(matchClass && matchSearch);
                });

                if (studentSelect.value && studentSelect.selectedOptions[0] && studentSelect.selectedOptions[0].hidden) {
                    clearSelection();
                }
            }


            function showBulkStudentModal() {
                if (!studentSelect || !bulkStudentModalElement) return;

                bulkStudents = Array.from(studentSelect.options)
                    .filter((option, index) => index > 0 && !option.hidden)
                    .map(option => ({
                        id: option.value,
                        name: option.textContent.trim(),
                    }));

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
                    item.className = 'list-group-item list-group-item-action d-flex align-items-center justify-content-between';
                    item.innerHTML =
                        '<span><span class="text-muted me-3">' + (index + 1) +
                        '.</span><strong></strong></span>' +
                        '<span class="btn btn-sm btn-outline-success"><i class="bi bi-eye me-1"></i>Pratinjau</span>';
                    item.querySelector('strong').textContent = student.name;
                    item.addEventListener('click', function() {
                        const parameters = reportParameters();
                        parameters.set('print_type', 'individual');
                        parameters.set('student_id', student.id);
                        window.location.href = '{{ route('buku-induk.index') }}?' + parameters.toString();
                    });
                    bulkStudentList.appendChild(item);
                });

                bootstrap.Modal.getOrCreateInstance(bulkStudentModalElement).show();
            }

            downloadAllDocumentsButton?.addEventListener('click', function() {
                const parameters = reportParameters();
                parameters.set('pdf', '1');
                if (selectedPrintType === 'all') {
                    parameters.delete('class_id');
                }
                parameters.delete('student_id');
                window.location.href = '{{ route('buku-induk.batch-pdf') }}?' + parameters.toString();
            });

            if (classSelect) classSelect.addEventListener('change', filterStudents);


            /* =========================================================
               PREVIEW LAPORAN
            ========================================================== */
            if (previewButton) {
                previewButton.addEventListener('click', function() {
                    const classId = document.getElementById('classSelect')?.value || '';
                    const studentId = studentIdInput?.value || studentSelect?.value || '';

                    /* -----------------------------------------------
                       VALIDASI PER INDIVIDU
                    ------------------------------------------------ */
                    if (selectedPrintType === 'individual' && !studentId) {
                        alert('Silakan cari dan pilih siswa terlebih dahulu.');
                        return;
                    }

                    /* -----------------------------------------------
                       VALIDASI PER KELAS
                    ------------------------------------------------ */
                    if (selectedPrintType === 'class' && !classId) {
                        alert('Silakan pilih kelas terlebih dahulu pada parameter laporan.');
                        return;
                    }

                    /* -----------------------------------------------
                       MODE INDIVIDU -> SUBMIT FORM
                    ------------------------------------------------ */
                    if (selectedPrintType === 'individual') {
                        const form = document.getElementById('reportForm');
                        if (form) form.submit();
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
                    window.location.href = "{{ route('buku-induk.index') }}";
                });
            }


            /* =========================================================
               EXPORT PDF
            ========================================================== */
            function reportParameters() {
                const form = document.getElementById('reportForm');
                const parameters = new URLSearchParams(new FormData(form));
                parameters.set('print_type', selectedPrintType);
                if (studentIdInput && studentIdInput.value) {
                    parameters.set('student_id', studentIdInput.value);
                }
                return parameters;
            }

            function downloadCurrentDocument() {
                if (selectedPrintType === 'class') {
                    if (!classSelect?.value) {
                        alert('Silakan pilih kelas terlebih dahulu pada parameter laporan.');
                        return;
                    }
                    const parameters = reportParameters();
                    parameters.set('pdf', '1');
                    parameters.delete('student_id');
                    window.location.href = '{{ route('buku-induk.batch-pdf') }}?' + parameters.toString();
                    return;
                }

                if (selectedPrintType === 'all') {
                    const parameters = reportParameters();
                    parameters.set('pdf', '1');
                    parameters.delete('class_id');
                    parameters.delete('student_id');
                    window.location.href = '{{ route('buku-induk.batch-pdf') }}?' + parameters.toString();
                    return;
                }

                const studentId = studentIdInput?.value || studentSelect?.value || '';
                if (!studentId) {
                    alert('Silakan cari dan pilih siswa terlebih dahulu.');
                    return;
                }

                const parameters = reportParameters();
                parameters.set('pdf', '1');
                window.location.href = '{{ url('/laporan/buku-induk/cetak') }}/' + studentId + '?' + parameters.toString();
            }

            headerPdfButton?.addEventListener('click', downloadCurrentDocument);
            previewPdfButton?.addEventListener('click', downloadCurrentDocument);


            /* =========================================================
               PARAMETER LAPORAN -> HUBUNGKAN DENGAN JENIS CETAK
            ========================================================== */
            function reloadWithParameters(changedField) {
                const parameters = reportParameters();
                parameters.delete('student_id');

                if (changedField === 'academicYear') {
                    parameters.delete('semester_id');
                    parameters.delete('class_id');
                }

                if (changedField === 'classSelect') {
                    const classVal = document.getElementById('classSelect')?.value;
                    if (classVal) {
                        selectedPrintType = 'class';
                        parameters.set('print_type', 'class');
                    } else {
                        selectedPrintType = 'all';
                        parameters.set('print_type', 'all');
                    }
                }

                window.location.href = '{{ route('buku-induk.index') }}?' + parameters.toString();
            }

            document.getElementById('academicYear')?.addEventListener('change', function() {
                reloadWithParameters('academicYear');
            });

            document.getElementById('statusSelect')?.addEventListener('change', function() {
                reloadWithParameters('statusSelect');
            });

            // Initial view state setup
            updatePrintType();
            filterStudents();
        });
    </script>
@endsection
