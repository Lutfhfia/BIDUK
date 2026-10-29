@extends('layouts.app')

@section('title', 'Rekap Rapor')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Laporan</li>
    <li class="breadcrumb-item active" aria-current="page">Rekap Rapor</li>
@endsection

@section('content')
<style>
    .rr-input-icon { position: relative; }
    .rr-input-icon > i {
        position: absolute;
        top: 50%;
        left: 14px;
        z-index: 2;
        color: #7b9185;
        transform: translateY(-50%);
    }

    .rr-input-icon .form-control { padding-left: 40px; }

    .rr-student-search-results {
        display: none;
        max-height: 280px;
        overflow-y: auto;
        z-index: 1050;
    }
    .rr-student-list {
        max-height: 170px;
        overflow-y: auto;
        background: #fff;
    }
    .rr-student-list-item {
        display: flex;
        width: 100%;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 8px 12px;
        border: 0;
        border-bottom: 1px solid #f0f0f0;
        background: #fff;
        color: inherit;
        font: inherit;
        text-align: left;
        transition: background-color .15s ease;
    }
    .rr-student-list-item:last-child { border-bottom: 0; }
    .rr-student-list-item:hover,
    .rr-student-list-item.active {
        background: var(--biduk-primary-light, #e8f5ee);
    }
    .rr-student-list-item.active {
        border-left: 3px solid var(--biduk-primary, #1a7a4c);
        padding-left: 9px;
    }
    .rr-student-list-item .student-info { flex: 1; min-width: 0; }
    .rr-student-list-item .student-name {
        overflow: hidden;
        color: #212529;
        font-size: 13px;
        font-weight: 600;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .rr-student-list-item .student-meta { color: #6c757d; font-size: 11.5px; }
    .rr-student-list-empty { padding: 14px 12px; color: #6c757d; text-align: center; }
    .rr-student-list-more { padding: 8px; text-align: center; }
    .print-type-card:focus-visible {
        outline: 3px solid rgba(26, 122, 76, .25);
        outline-offset: 2px;
    }

</style>
@include('reports._shared-ui-styles')

@php
    $semesterSelection = request('semester', 'ganjil_genap');

    $commonParams = [
        'academic_year_id' => $selectedAcademicYearId,
        'semester' => $semesterSelection,
    ];

    $selectedPrintType = request('print_type', 'individual');

    $previewStudent = is_numeric(request('preview_student_id'))
        ? $students->firstWhere('id', (int) request('preview_student_id'))
        : null;
    $previewStudentClass = null;
    if ($previewStudent) {
        $previewClassId = is_numeric(request('preview_class_id'))
            ? (int) request('preview_class_id')
            : null;
        $previewStudentClassQuery = $previewStudent->classes()
            ->where('classes.academic_year_id', $selectedAcademicYearId)
            ->where('classes.status', 'Aktif');

        if ($previewClassId && $classes->contains('id', $previewClassId)) {
            $previewStudentClassQuery->where('classes.id', $previewClassId);
        } elseif (is_numeric($selectedClassId)) {
            $previewStudentClassQuery->where('classes.id', (int) $selectedClassId);
        }

        $previewStudentClass = $previewStudentClassQuery->first();
    }
@endphp

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h3 class="fw-bold mb-1 text-dark">
                <i class="bi bi-file-earmark-text text-success me-2"></i>Rekap Rapot
            </h3>
            <p class="text-muted mb-0">Pilih parameter laporan, atur jenis cetak, lalu pratinjau atau cetak rapor.</p>
        </div>
        <div class="d-flex flex-wrap justify-content-end gap-2">
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="row g-3 align-items-start">
        <div class="col-lg-5">
            <form method="GET" action="{{ route('rekap-rapot.index') }}" id="rekap-rapot-filter-form">
                <input type="hidden" name="print_type" id="rrPrintTypeInput" value="{{ $selectedPrintType }}">

                {{-- 1. PILIH PARAMETER LAPORAN --}}
                <div class="card border-0 shadow-sm mb-3 report-setup-card">
                    <div class="card-header bg-white py-2">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-sliders text-success me-2"></i>1. Pilih Parameter Laporan
                        </h5>
                    </div>
                    <div class="card-body py-2">
                        {{-- BARIS 1: TAHUN AJARAN, SEMESTER, KELAS --}}
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="rekap-rapot-academic-year">Tahun Ajaran</label>
                                <select name="academic_year_id" id="rekap-rapot-academic-year" class="form-select">
                                    @foreach($academicYears as $year)
                                        <option value="{{ $year->id }}" data-status="{{ $year->status }}" @selected((string) $selectedAcademicYearId === (string) $year->id)>
                                            {{ $year->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="rrSemester">Semester</label>
                                <select name="semester" id="rrSemester" class="form-select">
                                    <option value="ganjil_genap" @selected($semesterSelection === 'ganjil_genap' || $semesterSelection === 'both')>Ganjil &amp; Genap</option>
                                    <option value="ganjil" @selected($semesterSelection === 'ganjil')>Ganjil</option>
                                    <option value="genap" @selected($semesterSelection === 'genap')>Genap</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="rekap-rapot-class">Kelas</label>
                                <select name="class_id" id="rekap-rapot-class" class="form-select">
                                    <option value="">Semua Kelas</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" @selected((string) $selectedClassId === (string) $class->id)>
                                            {{ $class->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- BARIS 2: STATUS SISWA & PENCARIAN (kecil, sebelah status) --}}
                        <div class="row g-3 mt-1">
                            <div class="col-md-7">
                                <label class="form-label fw-semibold" for="rekap-rapot-student-status">Status Siswa</label>
                                <select name="status" id="rekap-rapot-student-status" class="form-select">
                                    <option value="Semua" @selected($selectedStatus === 'Semua')>Semua Status</option>
                                    <option value="Aktif" @selected($selectedStatus === 'Aktif')>Aktif</option>
                                    <option value="Tidak Aktif" @selected($selectedStatus === 'Tidak Aktif')>Tidak Aktif</option>
                                </select>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold" for="reportStudentSearch">
                                    Cari Siswa <span class="text-muted fw-normal">(Nama / NIS)</span>
                                </label>
                                <div class="position-relative">
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-search text-muted"></i>
                                        </span>
                                        <input
                                            type="search"
                                            id="reportStudentSearch"
                                            name="search"
                                            value="{{ $search }}"
                                            class="form-control border-start-0 ps-0"
                                            placeholder="Nama atau NIS..."
                                            autocomplete="off"
                                            role="combobox"
                                            aria-autocomplete="list"
                                            aria-controls="rrStudentSearchResults"
                                            aria-expanded="false"
                                        >
                                        <button type="button" class="btn btn-outline-secondary {{ $search !== '' ? '' : 'd-none' }}" id="clearStudentSearch1" title="Hapus pencarian">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                    <div
                                        id="rrStudentSearchResults"
                                        class="dropdown-menu shadow-sm w-100 p-0 border mt-1 rr-student-search-results"
                                        role="listbox"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        {{-- INFO PARAMETER --}}
                        <div class="alert alert-success bg-success-subtle text-success-emphasis border-0 mt-2 mb-0">
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


                {{-- 2. PILIH JENIS CETAK --}}
                <div class="card border-0 shadow-sm report-setup-card">
                    <div class="card-header bg-white py-2">
                        <h5 class="mb-0 fw-bold text-dark">
                            <i class="bi bi-printer text-success me-2"></i>2. Pilih Jenis Cetak
                        </h5>
                    </div>
                    <div class="card-body py-2">
                        {{-- KATEGORI CETAK --}}
                        <div class="row g-3">
                            {{-- PER INDIVIDU --}}
                            <div class="col-md-4">
                                <div class="print-type-card h-100 {{ $selectedPrintType === 'individual' ? 'active' : '' }}"
                                    data-print-type="individual" role="button" tabindex="0" aria-pressed="{{ $selectedPrintType === 'individual' ? 'true' : 'false' }}">
                                    <div class="radio-circle"><i class="bi bi-check"></i></div>
                                    <div class="print-icon"><i class="bi bi-person-fill"></i></div>
                                    <h6 class="fw-bold">Per Individu</h6>
                                    <small class="text-muted">
                                        Cetak rapor satu siswa.
                                        {{ match($semesterSelection) { 'ganjil' => 'Semester Ganjil.', 'genap' => 'Semester Genap.', default => 'Ganjil & Genap.' } }}
                                    </small>
                                </div>
                            </div>

                            {{-- PER KELAS --}}
                            <div class="col-md-4">
                                <div class="print-type-card h-100 {{ $selectedPrintType === 'class' ? 'active' : '' }}"
                                    data-print-type="class" role="button" tabindex="0" aria-pressed="{{ $selectedPrintType === 'class' ? 'true' : 'false' }}">
                                    <div class="radio-circle"><i class="bi bi-check"></i></div>
                                    <div class="print-icon"><i class="bi bi-people-fill"></i></div>
                                    <h6 class="fw-bold">Per Kelas</h6>
                                    <small class="text-muted">
                                        Cetak rapor seluruh siswa dalam satu kelas.
                                    </small>
                                </div>
                            </div>

                            {{-- SEMUA KELAS --}}
                            <div class="col-md-4">
                                <div class="print-type-card h-100 {{ $selectedPrintType === 'all' ? 'active' : '' }}"
                                    data-print-type="all" role="button" tabindex="0" aria-pressed="{{ $selectedPrintType === 'all' ? 'true' : 'false' }}">
                                    <div class="radio-circle"><i class="bi bi-check"></i></div>
                                    <div class="print-icon"><i class="bi bi-building"></i></div>
                                    <h6 class="fw-bold">Semua Kelas</h6>
                                    <small class="text-muted">Cetak rapor seluruh kelas.</small>
                                </div>
                            </div>
                        </div>


                        {{-- ACTION BUTTONS --}}
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('rekap-rapot.index') }}" class="btn btn-outline-secondary" id="rrResetButton">
                                <i class="bi bi-arrow-counterclockwise me-1"></i>
                                Reset
                            </a>

                            <button type="button" class="btn btn-biduk-primary" id="rrPreviewButton">
                                <i class="bi bi-search me-1"></i>
                                Preview Laporan
                            </button>
                        </div>

                        {{-- PILIH SISWA (MODE INDIVIDU) --}}
                        <div class="mt-3" id="rrStudentSelection" style="{{ $selectedPrintType === 'individual' ? '' : 'display:none' }}">
                            <label class="form-label fw-semibold">
                                Pilih Siswa <span class="text-danger">*</span>
                            </label>
                            <div class="rr-input-icon mb-2">
                                <i class="bi bi-search"></i>
                                <input
                                    type="search"
                                    id="rrStudentListFilter"
                                    class="form-control"
                                    placeholder="Filter daftar siswa..."
                                    autocomplete="off"
                                >
                            </div>

                            {{-- Hidden student inputs --}}
                            <input type="hidden" name="preview_student_id" id="rrStudentIdInput"
                                value="{{ $previewStudent?->id ?? request('preview_student_id') }}">
                            <input type="hidden" name="preview_class_id" id="rrClassIdInput"
                                value="{{ $previewStudentClass?->id ?? request('preview_class_id') }}">

                            <div id="rrStudentList" class="rr-student-list border rounded-3" role="listbox" aria-label="Daftar siswa">
                                <div class="p-3 text-center text-muted small">Memuat daftar siswa...</div>
                            </div>

                            <div id="rrSelectedStudent" class="small mt-2 text-success {{ $previewStudent ? '' : 'd-none' }}" aria-live="polite">
                                <i class="bi bi-person-check-fill me-1"></i>
                                Siswa dipilih: <strong id="rrSelectedStudentName">{{ $previewStudent?->name }}</strong>
                                <span class="text-muted" id="rrSelectedStudentMeta">
                                    @if($previewStudent)
                                        · NIS: {{ $previewStudent->nis ?: '-' }} · Kelas {{ $previewStudentClass?->name ?? '-' }}
                                    @endif
                                </span>
                            </div>

                            <div class="form-text mt-1 text-muted">
                                Pilih salah satu siswa dari daftar. Gunakan filter untuk mempersempit pencarian.
                            </div>
                        </div>


                        {{-- INFO JENIS CETAK --}}
                        <div class="alert alert-info mt-2 mb-0" id="rrPrintTypeInfo">
                            <i class="bi bi-info-circle me-2"></i>
                            <span>
                                @if($selectedPrintType === 'class')
                                    Mode <strong>Per Kelas</strong> dipilih.
                                    Silakan pilih kelas pada parameter laporan. Seluruh siswa pada kelas tersebut akan menjadi data cetak / export PDF.
                                @elseif($selectedPrintType === 'all')
                                    Mode <strong>Semua Kelas</strong> dipilih.
                                    Sistem akan menggunakan seluruh siswa sesuai parameter tahun ajaran dan status.
                                @else
                                    Mode <strong>Per Individu</strong> dipilih.
                                    Silakan cari dan pilih siswa yang akan dicetak.
                                @endif
                            </span>
                        </div>


                    </div>
                </div>
            </form>
        </div>

        {{-- RIGHT: PREVIEW --}}
        <div class="col-lg-7" id="rrPreviewPanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-file-earmark-text text-success me-2"></i>3. Preview Rapot
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-success bg-success-subtle text-success-emphasis border-0 d-flex align-items-start mb-3">
                        <i class="bi bi-info-circle-fill me-2 mt-1"></i>
                        <div class="small">
                            Preview mengikuti jenis cetak dan parameter yang dipilih. Pastikan data sudah sesuai sebelum mencetak atau menyimpan PDF.
                        </div>
                    </div>
                    <div class="preview-toolbar mb-3 {{ $previewStudentClass ? '' : 'd-none' }}" id="rrPreviewToolbar">
                        <div>
                            <span class="text-muted small">Laporan:</span>
                            <strong class="text-dark" id="rrPreviewStudentName">{{ $previewStudent?->name }}</strong>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge text-bg-light border text-dark" id="rrPreviewPageCount">Menghitung halaman...</span>
                            <a
                                class="btn btn-sm btn-danger"
                                id="rrExportPreviewPdf"
                                target="_blank"
                                rel="noopener"
                                @if($previewStudentClass)
                                    href="{{ route('rekap-rapot.print', array_merge($commonParams, ['student' => $previewStudent->id, 'class_id' => $previewStudentClass->id, 'pdf' => 1])) }}"
                                @endif
                            >
                                <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                            </a>
                        </div>
                    </div>
                    <div id="rrPreviewPlaceholder" class="preview-placeholder {{ $previewStudentClass ? 'd-none' : '' }}">
                        <div class="preview-placeholder-icon"><i class="bi bi-journal-text"></i></div>
                        <h5 class="fw-bold mt-3 text-dark">Preview Rapot</h5>
                        <p class="text-muted mb-0">Pilih jenis cetak dan parameternya, lalu klik <strong>Preview Laporan</strong> untuk menampilkan dokumen.</p>
                    </div>
                    <div class="preview-wrapper {{ $previewStudentClass ? '' : 'd-none' }}" id="rrPreviewWrapper">
                        <iframe
                            class="preview-frame"
                            id="rrPreviewFrame"
                            @if($previewStudentClass)
                                src="{{ route('rekap-rapot.print', array_merge($commonParams, ['student' => $previewStudent->id, 'class_id' => $previewStudentClass->id])) }}"
                            @endif
                            title="Preview rapor siswa"
                        ></iframe>
                    </div>
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
                            <h6 class="fw-bold mb-2 text-dark">Keterangan</h6>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        /* =========================================================
           ELEMENT REFERENCES
        ========================================================== */
        let selectedPrintType = @json($selectedPrintType);
        const printTypeInput = document.getElementById('rrPrintTypeInput');
        const printCards = document.querySelectorAll('[data-print-type]');
        const classSelect = document.getElementById('rekap-rapot-class');
        const printTypeInfo = document.getElementById('rrPrintTypeInfo');
        const previewButton = document.getElementById('rrPreviewButton');

        // Preview panel elements
        const previewFrame = document.getElementById('rrPreviewFrame');
        const previewWrapper = document.getElementById('rrPreviewWrapper');
        const previewPlaceholder = document.getElementById('rrPreviewPlaceholder');
        const previewToolbar = document.getElementById('rrPreviewToolbar');
        const previewStudentName = document.getElementById('rrPreviewStudentName');
        const exportPreviewPdf = document.getElementById('rrExportPreviewPdf');
        const previewPageCount = document.getElementById('rrPreviewPageCount');

        const form = document.getElementById('rekap-rapot-filter-form');
        const studentSelection = document.getElementById('rrStudentSelection');
        const studentListFilter = document.getElementById('rrStudentListFilter');
        const studentList = document.getElementById('rrStudentList');
        const selectedStudent = document.getElementById('rrSelectedStudent');
        const selectedStudentName = document.getElementById('rrSelectedStudentName');
        const selectedStudentMeta = document.getElementById('rrSelectedStudentMeta');
        const studentIdInput = document.getElementById('rrStudentIdInput');
        const classIdInput = document.getElementById('rrClassIdInput');

        const studentSearch = document.getElementById('reportStudentSearch');
        const studentSearchResults = document.getElementById('rrStudentSearchResults');
        const clearStudentSearch1 = document.getElementById('clearStudentSearch1');
        let lookupTimer;
        let lookupController;
        let listTimer;
        let listController;
        let listOffset = 0;
        let listHasMore = false;
        let currentListQuery = '';

        function updatePreviewPageCount() {
            if (!previewFrame || !previewPageCount || !previewFrame.contentDocument) return;

            const documentBody = previewFrame.contentDocument.body;
            const pageCount = documentBody.querySelectorAll('.page').length;
            const totalPages = pageCount || (documentBody.textContent.trim() !== '' ? 1 : 0);

            previewPageCount.textContent = totalPages > 0
                ? 'Total ' + totalPages + ' halaman'
                : 'Jumlah halaman tidak tersedia';
        }

        previewFrame?.addEventListener('load', updatePreviewPageCount);
        if (previewFrame?.contentDocument?.readyState === 'complete') {
            updatePreviewPageCount();
        }

        function studentLookupUrl(keyword, listMode, offset = 0) {
            const params = new URLSearchParams(new FormData(form));
            params.set('student_lookup', '1');
            params.set('search', keyword);
            if (listMode) {
                params.set('student_list', '1');
                params.set('offset', String(offset));
            }
            const url = new URL(form.action, window.location.origin);
            url.search = params.toString();
            return url;
        }

        function selectStudent(student, fromParameterSearch = false) {
            studentIdInput.value = student.id;
            classIdInput.value = student.class_id;
            selectedStudentName.textContent = student.name;
            selectedStudentMeta.textContent = ' · NIS: ' + (student.nis || '-') +
                ' · Kelas ' + (student.class_name || '-');
            selectedStudent.classList.remove('d-none');
            studentSearch.value = student.name;
            clearStudentSearch1.classList.toggle('d-none', !student.name);
            studentSearchResults.style.display = 'none';
            studentSearch.setAttribute('aria-expanded', 'false');

            if (selectedPrintType !== 'individual') {
                selectedPrintType = 'individual';
                printTypeInput.value = 'individual';
                updatePrintType();
            }

            if (fromParameterSearch && studentListFilter) {
                studentListFilter.value = student.name;
                loadStudentList(student.name);
            }
            renderStudentListSelection();
        }

        function renderStudentListSelection() {
            if (!studentList) return;
            studentList.querySelectorAll('[data-student-id]').forEach(function (item) {
                item.classList.toggle('active', studentIdInput.value !== '' &&
                    item.dataset.studentId === studentIdInput.value);
            });
        }

        function renderSearchResults(students) {
            studentSearchResults.replaceChildren();
            if (!students.length) {
                const empty = document.createElement('div');
                empty.className = 'p-3 text-center text-muted small';
                empty.textContent = 'Data siswa tidak ditemukan.';
                studentSearchResults.appendChild(empty);
                studentSearchResults.style.display = 'block';
                return;
            }

            const list = document.createElement('div');
            list.className = 'list-group list-group-flush';
            students.forEach(function (student) {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'list-group-item list-group-item-action py-2 px-3 student-search-item';
                item.setAttribute('role', 'option');

                const row = document.createElement('div');
                row.className = 'd-flex justify-content-between align-items-center gap-2';
                const identity = document.createElement('div');
                const name = document.createElement('div');
                name.className = 'fw-bold text-dark text-start';
                name.textContent = student.name || '';
                const identifiers = document.createElement('div');
                identifiers.className = 'small text-muted text-start';
                identifiers.textContent = 'NIS: ' + (student.nis || '-') + ' · NISN: ' + (student.nisn || '-');
                identity.append(name, identifiers);
                const classBadge = document.createElement('span');
                classBadge.className = 'badge bg-success bg-opacity-10 text-success border border-success-subtle';
                classBadge.textContent = 'Kelas ' + (student.class_name || '-');
                row.append(identity, classBadge);
                item.appendChild(row);
                item.addEventListener('click', function () {
                    selectStudent(student, true);
                    studentSearchResults.style.display = 'none';
                });
                list.appendChild(item);
            });
            studentSearchResults.appendChild(list);
            studentSearchResults.style.display = 'block';
        }

        function queueStudentLookup(keyword) {
            window.clearTimeout(lookupTimer);
            lookupController?.abort();
            const query = keyword.trim();
            if (!query) {
                studentSearchResults.style.display = 'none';
                studentSearch.setAttribute('aria-expanded', 'false');
                return;
            }

            studentSearchResults.innerHTML = '<div class="p-3 text-center text-muted small">Mencari siswa...</div>';
            studentSearchResults.style.display = 'block';
            studentSearch.setAttribute('aria-expanded', 'true');
            lookupTimer = window.setTimeout(async function () {
                const controller = new AbortController();
                lookupController = controller;
                try {
                    const response = await fetch(studentLookupUrl(query, false), {
                        headers: { Accept: 'application/json' },
                        signal: controller.signal,
                    });
                    if (!response.ok) throw new Error('Pencarian siswa gagal.');
                    const students = await response.json();
                    if (!controller.signal.aborted && studentSearch.value.trim() === query) {
                        renderSearchResults(students);
                    }
                } catch (error) {
                    if (controller.signal.aborted) return;
                    studentSearchResults.textContent = 'Pencarian gagal. Silakan coba lagi.';
                    studentSearchResults.style.display = 'block';
                }
            }, 250);
        }

        function renderStudentListItems(students, append) {
            if (!append) studentList.replaceChildren();
            if (!students.length && !append) {
                const empty = document.createElement('div');
                empty.className = 'rr-student-list-empty small';
                empty.textContent = 'Data siswa tidak ditemukan untuk parameter ini.';
                studentList.appendChild(empty);
                return;
            }

            students.forEach(function (student, index) {
                const item = document.createElement('button');
                item.type = 'button';
                item.className = 'rr-student-list-item' +
                    (String(student.id) === studentIdInput.value ? ' active' : '');
                item.dataset.studentId = student.id;
                item.setAttribute('role', 'option');
                const identity = document.createElement('span');
                identity.className = 'student-info';
                const name = document.createElement('span');
                name.className = 'student-name d-block';
                name.textContent = (listOffset + index + 1) + '. ' + student.name;
                const meta = document.createElement('span');
                meta.className = 'student-meta d-block';
                meta.textContent = 'NIS: ' + (student.nis || '-') + ' · Kelas ' + (student.class_name || '-');
                identity.append(name, meta);
                const badge = document.createElement('span');
                badge.className = 'badge bg-success bg-opacity-10 text-success border border-success-subtle';
                badge.textContent = student.class_name || 'Kelas';
                item.append(identity, badge);
                item.addEventListener('click', function () {
                    selectStudent(student);
                    renderStudentListSelection();
                });
                studentList.appendChild(item);
            });
            renderStudentListSelection();
        }

        async function loadStudentList(keyword = '', append = false) {
            if (!studentList || selectedPrintType !== 'individual') return;
            if (!append) {
                window.clearTimeout(listTimer);
                listController?.abort();
                listOffset = 0;
                currentListQuery = keyword.trim();
                listHasMore = false;
                studentList.innerHTML = '<div class="p-3 text-center text-muted small">Memuat daftar siswa...</div>';
            }

            const query = append ? currentListQuery : keyword.trim();
            listTimer = window.setTimeout(async function () {
                const controller = new AbortController();
                listController = controller;
                try {
                    const response = await fetch(studentLookupUrl(query, true, listOffset), {
                        headers: { Accept: 'application/json' },
                        signal: controller.signal,
                    });
                    if (!response.ok) throw new Error('Daftar siswa gagal dimuat.');
                    const result = await response.json();
                    if (controller.signal.aborted || query !== currentListQuery) return;
                    renderStudentListItems(result.data || [], append);
                    listHasMore = Boolean(result.has_more);
                    listOffset = Number(result.next_offset || 0);
                    studentList.querySelector('.rr-student-list-more')?.remove();
                    if (listHasMore) {
                        const more = document.createElement('div');
                        more.className = 'rr-student-list-more text-muted small';
                        more.textContent = 'Gulir ke bawah untuk memuat siswa lainnya.';
                        studentList.appendChild(more);
                    }
                } catch (error) {
                    if (controller.signal.aborted) return;
                    studentList.innerHTML = '<div class="rr-student-list-empty small">Daftar siswa gagal dimuat. Silakan coba lagi.</div>';
                }
            }, append ? 0 : 200);
        }

        if (studentSearch) {
            studentSearch.addEventListener('input', function () {
                const query = this.value.trim();
                clearStudentSearch1.classList.toggle('d-none', !query);
                if (studentIdInput.value) {
                    studentIdInput.value = '';
                    classIdInput.value = '';
                    selectedStudent.classList.add('d-none');
                    renderStudentListSelection();
                }
                queueStudentLookup(query);
            });
            studentSearch.addEventListener('focus', function () {
                if (this.value.trim()) queueStudentLookup(this.value);
            });
            studentSearch.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    studentSearchResults.style.display = 'none';
                    studentSearch.setAttribute('aria-expanded', 'false');
                }
                if (event.key === 'Enter') {
                    event.preventDefault();
                    studentSearchResults.querySelector('.student-search-item')?.click();
                }
            });
        }

        clearStudentSearch1?.addEventListener('click', function () {
            studentSearch.value = '';
            clearStudentSearch1.classList.add('d-none');
            studentSearchResults.style.display = 'none';
            studentSearch.setAttribute('aria-expanded', 'false');
            studentIdInput.value = '';
            classIdInput.value = '';
            selectedStudent.classList.add('d-none');
            renderStudentListSelection();
            studentSearch.focus();
        });

        studentListFilter?.addEventListener('input', function () {
            loadStudentList(this.value);
        });

        studentList?.addEventListener('scroll', function () {
            if (listHasMore && this.scrollTop + this.clientHeight >= this.scrollHeight - 24) {
                listHasMore = false;
                loadStudentList(currentListQuery, true);
            }
        });

        document.addEventListener('click', function (event) {
            if (studentSearchResults && studentSearch && !studentSearch.contains(event.target) &&
                !studentSearchResults.contains(event.target)) {
                studentSearchResults.style.display = 'none';
                studentSearch.setAttribute('aria-expanded', 'false');
            }
        });


        /* =========================================================
           PILIH JENIS CETAK
        ========================================================== */
        printCards.forEach(function (card) {
            card.addEventListener('click', function () {
                selectedPrintType = card.dataset.printType;
                if (printTypeInput) printTypeInput.value = selectedPrintType;

                if (selectedPrintType === 'all' && classSelect) {
                    classSelect.value = '';
                }

                updatePrintType();
            });
            card.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' || event.key === ' ') {
                    event.preventDefault();
                    card.click();
                }
            });
        });


        /* =========================================================
           UPDATE TAMPILAN BERDASARKAN JENIS CETAK
        ========================================================== */
        function updatePrintType() {
            if (!printTypeInfo) return;

            printCards.forEach(item => {
                item.classList.toggle('active', item.dataset.printType === selectedPrintType);
            });

            if (selectedPrintType === 'individual') {
                if (studentSelection) studentSelection.style.display = 'block';
                printTypeInfo.className = 'alert alert-info mt-2 mb-0';
                printTypeInfo.innerHTML =
                    '<i class="bi bi-info-circle me-2"></i>' +
                    'Mode <strong>Per Individu</strong> dipilih. Pilih siswa dari daftar, lalu klik Preview Laporan.';
                loadStudentList(studentListFilter?.value || '');
            } else if (selectedPrintType === 'class') {
                if (studentSelection) studentSelection.style.display = 'none';
                printTypeInfo.className = 'alert alert-info mt-2 mb-0';
                printTypeInfo.innerHTML =
                    '<i class="bi bi-people me-2"></i>' +
                    'Mode <strong>Per Kelas</strong> dipilih. Pilih kelas pada parameter laporan, lalu klik Preview Laporan.';
            } else if (selectedPrintType === 'all') {
                if (studentSelection) studentSelection.style.display = 'none';
                printTypeInfo.className = 'alert alert-info mt-2 mb-0';
                printTypeInfo.innerHTML =
                    '<i class="bi bi-building me-2"></i>' +
                    'Mode <strong>Semua Kelas</strong> dipilih. Klik Preview Laporan untuk melihat seluruh kelas sesuai parameter.';
            }

            printCards.forEach(function (card) {
                const isSelected = card.dataset.printType === selectedPrintType;
                card.classList.toggle('active', isSelected);
                card.setAttribute('aria-pressed', String(isSelected));
            });
        }


        /* =========================================================
           SHOW STUDENT PREVIEW IN IFRAME
        ========================================================== */
        function showStudentPreview(data) {
            if (!previewFrame || !previewWrapper || !previewPlaceholder || !previewToolbar || !previewStudentName || !exportPreviewPdf) {
                return;
            }

            const previewUrl = data.previewUrl || data.preview_url;
            const pdfUrl = data.pdfUrl || data.pdf_url;
            const studentNameVal = data.studentName || data.name;
            const formData = new FormData(form);

            previewPageCount.textContent = 'Menghitung halaman...';
            previewFrame.src = previewUrl;
            previewFrame.title = 'Preview rapor ' + studentNameVal;
            previewStudentName.textContent = studentNameVal;
            exportPreviewPdf.href = pdfUrl;
            previewWrapper.classList.remove('d-none');
            previewPlaceholder.classList.add('d-none');
            previewToolbar.classList.remove('d-none');

            const pageUrl = new URL(window.location.href);
            formData.forEach(function (value, key) {
                pageUrl.searchParams.set(key, value);
            });
            if (data.studentId) {
                pageUrl.searchParams.set('preview_student_id', data.studentId);
                pageUrl.searchParams.set('preview_class_id', data.classId);
            } else {
                pageUrl.searchParams.delete('preview_student_id');
                pageUrl.searchParams.delete('preview_class_id');
            }
            window.history.replaceState(null, '', pageUrl);

            if (window.matchMedia('(max-width: 991.98px)').matches) {
                document.getElementById('rrPreviewPanel')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }


        /* =========================================================
           PREVIEW LAPORAN BUTTON
        ========================================================== */
        if (previewButton) {
            previewButton.addEventListener('click', function () {
                const classId = classSelect?.value || '';
                const studentId = studentIdInput?.value || '';
                const classIdVal = classIdInput?.value || '';
                const params = new URLSearchParams();
                params.set('academic_year_id', document.getElementById('rekap-rapot-academic-year')?.value || '');
                params.set('semester', document.getElementById('rrSemester')?.value || 'ganjil_genap');
                params.set('status', document.getElementById('rekap-rapot-student-status')?.value || 'Semua');

                /* --- INDIVIDUAL MODE --- */
                if (selectedPrintType === 'individual') {
                    if (!studentId) {
                        alert('Silakan pilih siswa dari daftar terlebih dahulu.');
                        return;
                    }

                    if (classIdVal) params.set('class_id', classIdVal);
                    const previewUrl = new URL(
                        '{{ route('rekap-rapot.print', ['student' => '__STUDENT__']) }}'.replace('__STUDENT__', encodeURIComponent(studentId)),
                        window.location.origin
                    );
                    previewUrl.search = params.toString();
                    const pdfUrl = new URL(previewUrl);
                    pdfUrl.searchParams.set('pdf', '1');

                    showStudentPreview({
                        previewUrl: previewUrl.toString(),
                        pdfUrl: pdfUrl.toString(),
                        studentName: selectedStudentName?.textContent || '',
                        studentId: studentId,
                        classId: classIdVal,
                    });
                    return;
                }

                /* --- PER KELAS MODE --- */
                if (selectedPrintType === 'class' && !classId) {
                    alert('Silakan pilih kelas terlebih dahulu pada parameter laporan.');
                    return;
                }

                params.set('class_id', selectedPrintType === 'all' ? 'all' : classId);
                params.set('pdf', '0');
                const previewUrl = new URL('{{ route('rekap-rapot.batch') }}', window.location.origin);
                previewUrl.search = params.toString();
                const pdfUrl = new URL(previewUrl);
                pdfUrl.searchParams.set('pdf', '1');
                const previewLabel = selectedPrintType === 'all'
                    ? 'Seluruh Kelas'
                    : 'Kelas ' + classSelect.options[classSelect.selectedIndex].text;

                showStudentPreview({
                    previewUrl: previewUrl.toString(),
                    pdfUrl: pdfUrl.toString(),
                    studentName: previewLabel,
                });
            });
        }


        /* =========================================================
           PARAMETER CHANGES -> RELOAD
        ========================================================== */
        function reloadWithParameters(changedField) {
            const form = document.getElementById('rekap-rapot-filter-form');
            if (!form) return;
            const params = new URLSearchParams(new FormData(form));
            params.delete('preview_student_id');
            params.delete('preview_class_id');

            if (changedField === 'academicYear') {
                params.delete('semester');
                params.delete('class_id');
            }

            if (changedField === 'classSelect') {
                const classVal = classSelect?.value;
                if (classVal) {
                    params.set('print_type', 'class');
                } else {
                    params.set('print_type', selectedPrintType);
                }
            }

            window.location.href = '{{ route('rekap-rapot.index') }}?' + params.toString();
        }

        document.getElementById('rekap-rapot-academic-year')?.addEventListener('change', function () {
            reloadWithParameters('academicYear');
        });

        document.getElementById('rekap-rapot-student-status')?.addEventListener('change', function () {
            reloadWithParameters('statusSelect');
        });

        classSelect?.addEventListener('change', function () {
            if (this.value && selectedPrintType !== 'class') {
                selectedPrintType = 'class';
                printTypeInput.value = 'class';
                updatePrintType();
            }
            if (studentIdInput.value && this.value && this.value !== classIdInput.value) {
                studentIdInput.value = '';
                classIdInput.value = '';
                selectedStudent.classList.add('d-none');
            }
            if (selectedPrintType === 'individual') {
                loadStudentList(studentListFilter?.value || '');
            }
            if (studentSearch.value.trim()) {
                queueStudentLookup(studentSearch.value);
            }
        });

        // Initial state
        updatePrintType();
    });
</script>
@endsection
