@extends('layouts.app')

@section('title', 'Nilai Rapot')

@section('content')

    @php
        $currentUser = auth()->user();
        $canViewGrades = $currentUser && (
            $currentUser->isSuperAdmin() || $currentUser->hasPermission('grades.view')
        );
        $canEditGrades = $currentUser && (
            $currentUser->isSuperAdmin() || $currentUser->hasPermission('grades.edit')
        );
        $defaultImportClassId = $importOptions['classes']
            ->firstWhere('academic_year_id', $importOptions['default_academic_year_id'])
            ?->id;
    @endphp

    <div class="container-fluid py-4">

        {{-- =====================================================
            HEADER
        ====================================================== --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h4 class="fw-bold mb-1">

                    <i class="bi bi-journal-check me-2"></i>

                    Nilai Rapot

                </h4>

                <p class="text-muted mb-0">

                    Kelola dan input nilai rapot peserta didik semester Ganjil dan Genap serta riwayat akademik siswa.

                </p>

            </div>

        </div>


        {{-- =====================================================
            ALERT NOTIFIKASI
        ====================================================== --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($importOptions['is_teacher'] && $classes->isEmpty())
            <div class="alert alert-info" role="status">
                @if (! $currentUser?->employee_id)
                    Akun Guru ini belum terhubung dengan data pegawai. Minta Admin menghubungkannya dan menetapkan pegawai tersebut sebagai wali kelas agar kelas yang sesuai dapat ditampilkan.
                @else
                    Belum ada kelas yang tercatat sebagai kelas wali akun Guru ini. Minta Admin memeriksa penugasan wali kelas pada data kelas.
                @endif
            </div>
        @endif

        @if ($importOptions['can_import'])
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="fw-bold mb-1">Import Nilai Rapot</h6>
                        <p class="text-muted small mb-0">Pilih tahun ajaran, kelas, dan semester. Template hanya memuat mata pelajaran aktif yang ditetapkan pada kelas tersebut; satu file hanya untuk satu semester.</p>
                    </div>
                    @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('grades.edit'))
                        <form
                            id="report-card-import-form"
                            method="POST"
                            action="{{ route('report-card-grades.import.preview') }}"
                            enctype="multipart/form-data"
                        >
                            @csrf
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label for="import-academic-year" class="form-label">Tahun Ajaran</label>
                                    <select name="academic_year_id" id="import-academic-year" class="form-select" required>
                                        <option value="">Pilih tahun ajaran</option>
                                        @foreach ($importOptions['academic_years'] as $year)
                                            <option
                                                value="{{ $year->id }}"
                                                @selected((int) $year->id === (int) $importOptions['default_academic_year_id'])
                                            >
                                                {{ $year->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="import-class" class="form-label">Kelas</label>
                                    <select name="class_id" id="import-class" class="form-select" required>
                                        <option value="">Pilih kelas</option>
                                        @foreach ($importOptions['classes'] as $class)
                                            <option
                                                value="{{ $class->id }}"
                                                data-academic-year="{{ $class->academic_year_id }}"
                                                @selected((int) $class->id === (int) $defaultImportClassId)
                                            >
                                                Kelas {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="import-semester" class="form-label">Semester</label>
                                    <select name="semester_id" id="import-semester" class="form-select" required>
                                        <option value="">Pilih semester</option>
                                        @foreach ($importOptions['semesters'] as $semester)
                                            <option value="{{ $semester->id }}" data-academic-year="{{ $semester->academic_year_id }}">
                                                {{ $semester->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="import-file" class="form-label">File template (.xlsx)</label>
                                    <input type="file" name="file" id="import-file" class="form-control" accept=".xlsx" required>
                                </div>
                                <div class="col-12 d-flex flex-wrap justify-content-end gap-2">
                                    <a
                                        id="download-import-template"
                                        href="{{ route('report-card-grades.import.template') }}"
                                        class="btn btn-outline-success"
                                    >
                                        <i class="bi bi-download me-1"></i>Download Template Import
                                    </a>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-upload me-1"></i>Upload & Preview
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info mb-0">Akun ini tidak memiliki izin untuk mengimpor nilai rapor.</div>
                    @endif
                </div>
            </div>
        @endif

        @if ($importOptions['can_import'] && (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('grades.edit')))
            <script>
                (() => {
                    const year = document.getElementById('import-academic-year');
                    const classSelect = document.getElementById('import-class');
                    const semesterSelect = document.getElementById('import-semester');
                    const downloadLink = document.getElementById('download-import-template');

                    const updateImportOptions = () => {
                        const yearId = year.value;
                        [classSelect, semesterSelect].forEach((select) => {
                            Array.from(select.options).forEach((option) => {
                                if (!option.value) return;
                                const visible = option.dataset.academicYear === yearId;
                                option.hidden = !visible;
                                option.disabled = !visible;
                                if (!visible && option.selected) select.value = '';
                            });
                        });

                        const params = new URLSearchParams({
                            academic_year_id: yearId,
                            class_id: classSelect.value,
                            semester_id: semesterSelect.value,
                        });
                        downloadLink.href = `{{ route('report-card-grades.import.template') }}?${params.toString()}`;
                        downloadLink.classList.toggle(
                            'disabled',
                            !yearId || !classSelect.value || !semesterSelect.value
                        );
                    };

                    [year, classSelect, semesterSelect].forEach((select) => {
                        select.addEventListener('change', updateImportOptions);
                    });
                    updateImportOptions();
                })();
            </script>
        @endif

        {{-- =====================================================
            FILTER PENCARIAN
        ====================================================== --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h6 class="fw-bold mb-0">

                    <i class="bi bi-search me-2"></i>

                    Pencarian & Filter Siswa

                </h6>

            </div>

            <div class="card-body">

                <form id="report-card-grade-filter-form" method="GET" action="{{ route('report-card-grades.index') }}">

                    <div class="row g-3">

                        {{-- Cari Nama / NIS / NISN --}}
                        <div class="col-md-4">

                            <label class="form-label fw-semibold">

                                Cari Nama / NIS / NISN

                            </label>

                            <input
                                type="text"
                                name="search"
                                class="form-control"
                                value="{{ $search ?? '' }}"
                                placeholder="Ketik nama, NIS, atau NISN..."
                            >

                        </div>

                        {{-- Tahun Ajaran --}}
                        <div class="col-md-4">

                            <label class="form-label fw-semibold">

                                Tahun Ajaran

                            </label>

                            <select name="academic_year_id" id="filter_academic_year_id" class="form-select">

                                <option value="">
                                    Semua Tahun Ajaran
                                </option>

                                @foreach ($academicYears as $ay)
                                    <option
                                        value="{{ $ay->id }}"
                                        @selected((string) $selectedAcademicYearId === (string) $ay->id)
                                    >
                                        {{ $ay->name }}
                                        @if ($ay->status === 'active')
                                            (Aktif)
                                        @else
                                            (Tidak Aktif)
                                        @endif
                                    </option>
                                @endforeach

                            </select>

                        </div>

                        {{-- Kelas --}}
                        <div class="col-md-4">

                            <label class="form-label fw-semibold">

                                Kelas

                            </label>

                            <select name="class_id" id="filter_class_id" class="form-select">

                                <option value="">
                                    Semua Kelas
                                </option>

                                @foreach ($classes as $c)
                                    <option
                                        value="{{ $c->id }}"
                                        data-academic-year="{{ $c->academic_year_id }}"
                                        @selected((string) $selectedClassId === (string) $c->id)
                                    >
                                        {{ $c->name }}
                                        @if (!$selectedAcademicYearId && $c->academicYear)
                                            ({{ $c->academicYear->name }})
                                        @endif
                                    </option>
                                @endforeach

                            </select>

                        </div>

                    </div>

                    <div class="mt-3 d-flex gap-2">

                        <button type="submit" class="btn btn-success">

                            <i class="bi bi-search me-1"></i>

                            Cari

                        </button>

                        <a href="{{ route('report-card-grades.index') }}" class="btn btn-outline-secondary">

                            <i class="bi bi-arrow-clockwise me-1"></i>

                            Reset

                        </a>

                    </div>

                </form>

            </div>

        </div>


        {{-- =====================================================
            INFORMASI KELAS (JIKA MEMILIH KELAS SPESIFIK)
        ====================================================== --}}
        @if ($selectedClass)

            <div class="card border-0 shadow-sm mb-4">

                <div class="card-body">

                    <div class="row g-3">

                        <div class="col-md-4">

                            <div class="text-muted small">
                                Tahun Ajaran
                            </div>

                            <div class="fw-semibold">
                                {{ $selectedClass->academicYear->name ?? '-' }}
                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="text-muted small">
                                Kelas
                            </div>

                            <div class="fw-semibold">
                                {{ $selectedClass->name }}
                            </div>

                        </div>

                        <div class="col-md-4">

                            <div class="text-muted small">
                                Mata Pelajaran
                            </div>

                            <div class="fw-semibold">
                                {{ $selectedClass->subjects->count() }} mata pelajaran
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        @endif


        {{-- =====================================================
            DAFTAR SISWA / HASIL PENCARIAN
        ====================================================== --}}
        @if ($searched)

            <div class="card border-0 shadow-sm">

                <div class="card-header bg-white py-3">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="fw-bold mb-1">

                                Hasil Data Siswa

                            </h6>

                            <small class="text-muted">

                                @if ($students instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                    Total data:
                                    <strong>{{ $students->total() }}</strong>
                                    siswa
                                @else
                                    Total data:
                                    <strong>{{ $students->count() }}</strong>
                                    siswa
                                @endif

                            </small>

                        </div>

                    </div>

                </div>

                <div class="card-body">

                    @if ($students->count() > 0)

                        <div class="table-responsive">

                            <table class="table table-bordered table-hover align-middle mb-0">

                                <thead class="table-light">

                                    <tr>

                                        <th class="text-center" style="width: 50px;">
                                            No.
                                        </th>

                                        <th style="width: 110px;">
                                            NIS
                                        </th>

                                        <th style="width: 120px;">
                                            NISN
                                        </th>

                                        <th>
                                            Nama Siswa
                                        </th>

                                        <th>
                                            Kelas
                                        </th>

                                        <th>
                                            Tahun Ajaran
                                        </th>

                                        <th class="text-center" style="width: 130px;">
                                            Ganjil
                                        </th>

                                        <th class="text-center" style="width: 130px;">
                                            Genap
                                        </th>

                                        <th class="text-center" style="width: 260px; min-width: 260px;">
    Aksi
</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    @foreach ($students as $index => $student)

                                        @php
                                            $targetClass = $student->target_class ?? null;
                                        @endphp

                                        <tr>

                                            {{-- No --}}
                                            <td class="text-center">

                                                @if ($students instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                                    {{ $students->firstItem() + $index }}
                                                @else
                                                    {{ $index + 1 }}
                                                @endif

                                            </td>

                                            {{-- NIS --}}
                                            <td>
                                                {{ $student->nis ?? '-' }}
                                            </td>

                                            {{-- NISN --}}
                                            <td>
                                                {{ $student->nisn ?? '-' }}
                                            </td>

                                            {{-- Nama Siswa --}}
                                            <td>
                                                <div class="fw-semibold">
                                                    {{ $student->name }}
                                                </div>
                                            </td>

                                            {{-- Kelas --}}
                                            <td>
                                                @if ($targetClass)
                                                    Kelas {{ $targetClass->grade_level }} {{ $targetClass->name }}
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning">
                                                        <i class="bi bi-exclamation-circle me-1"></i>
                                                        Belum Masuk Kelas
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Tahun Ajaran --}}
                                            <td>
                                                @if ($targetClass?->academicYear)
                                                    {{ $targetClass->academicYear->name }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>

                                            {{-- Status Ganjil --}}
                                            <td class="text-center">

                                                @if (!$student->has_ganjil)
                                                    <span class="badge bg-secondary-subtle text-secondary">
                                                        Tidak tersedia
                                                    </span>
                                                @elseif ($student->ganjil_is_complete)
                                                    <span class="badge bg-success-subtle text-success">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        Sudah Diisi
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">
                                                        <i class="bi bi-circle me-1"></i>
                                                        Belum Diisi
                                                    </span>
                                                @endif

                                            </td>

                                            {{-- Status Genap --}}
                                            <td class="text-center">

                                                @if (!$student->has_genap)
                                                    <span class="badge bg-secondary-subtle text-secondary">
                                                        Tidak tersedia
                                                    </span>
                                                @elseif ($student->genap_is_complete)
                                                    <span class="badge bg-success-subtle text-success">
                                                        <i class="bi bi-check-circle me-1"></i>
                                                        Sudah Diisi
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">
                                                        <i class="bi bi-circle me-1"></i>
                                                        Belum Diisi
                                                    </span>
                                                @endif

                                            </td>

                                            {{-- Aksi --}}
<td class="text-center" style="min-width: 260px;">

<div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 6px; min-width: 248px;">

                                                    {{-- Input / Edit Rapot --}}
                                                    @if ($targetClass && $canEditGrades)
                                                        <a
                                                            href="{{ route('report-card-grades.edit', [
                                                                'class' => $targetClass->id,
                                                                'student' => $student->id,
                                                                'return_url' => request()->fullUrl(),
                                                            ]) }}"
                                                           class="btn btn-sm btn-success w-100 text-nowrap"
                                                            title="Input / Edit Rapot Kelas Ini"
                                                        >
                                                            <i class="bi bi-pencil-square me-1"></i>
                                                            {{ $student->ganjil_grades_count > 0 || $student->genap_grades_count > 0 ? 'Edit Rapot' : 'Input Rapot' }}
                                                        </a>

                                                        {{-- Tombol Nilai Akhir / Ijazah khusus Kelas 6 --}}
                                                        @if ((int) $targetClass->grade_level === 6)
                                                            <a
                                                                href="{{ route('final-grades.edit', [
                                                                    'class' => $targetClass->id,
                                                                    'student' => $student->id,
                                                                    'return_url' => request()->fullUrl(),
                                                                ]) }}"
                                                                class="btn btn-sm btn-outline-info"
                                                                title="Kelola Nilai Akhir / Ijazah"
                                                            >
                                                                <i class="bi bi-award me-1"></i>
                                                                Nilai Ijazah
                                                            </a>
                                                        @endif
                                                    @elseif (!$targetClass)
                                                        <button
                                                            type="button"
                                                            class="btn btn-sm btn-outline-secondary"
                                                            disabled
                                                            title="Siswa belum ditempatkan ke dalam kelas."
                                                        >
                                                            <i class="bi bi-slash-circle me-1"></i>
                                                            Belum Ada Kelas
                                                        </button>
                                                    @endif

                                                    {{-- Tombol Riwayat Nilai Rapot Siswa --}}
                                                            @if ($canViewGrades)
                                                            <a
                                                        href="{{ route('report-card-grades.history', [
                                                            'student' => $student->id,
                                                            'return_url' => request()->fullUrl(),
                                                        ]) }}"
                                                        class="btn btn-sm btn-outline-primary"
                                                        title="Lihat Riwayat Rapot Kelas 1 - 6"
                                                    >
                                                        <i class="bi bi-clock-history me-1"></i>
                                                        Riwayat
                                                    </a>
                                                    @endif

                                                </div>

                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                        {{-- Pagination --}}
                        @if ($students instanceof \Illuminate\Pagination\LengthAwarePaginator && $students->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $students->links() }}
                            </div>
                        @endif

                    @else

                        {{-- EMPTY STATE --}}
                        <div class="text-center py-5">

                            <div class="mb-3">
                                <i class="bi bi-search" style="font-size: 3rem; color: #ccc;"></i>
                            </div>

                            <h6 class="fw-bold">
                                Data Siswa Tidak Ditemukan
                            </h6>

                            <p class="text-muted mb-0">
                                Tidak ditemukan data siswa berdasarkan filter pencarian yang dimasukkan.
                            </p>

                        </div>

                    @endif

                </div>

            </div>

        @else

            {{-- INITIAL STATE --}}
            <div class="card border-0 shadow-sm">

                <div class="card-body">

                    <div class="text-center py-5">

                        <div class="mb-3">
                            <i class="bi bi-journal-check" style="font-size: 3rem; color: #1a7a4c;"></i>
                        </div>

                        <h6 class="fw-bold">
                            Kelola Nilai Rapot Siswa
                        </h6>

                        <p class="text-muted mb-0">
                            Gunakan filter di atas untuk mencari siswa berdasarkan Nama, NIS, NISN,
                            atau pilih Tahun Ajaran dan Kelas untuk menampilkan daftar siswa dan mengelola nilai rapot.
                        </p>

                    </div>

                </div>

            </div>

        @endif

    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const yearSelect = document.getElementById('filter_academic_year_id');
            const classSelect = document.getElementById('filter_class_id');

            if (!yearSelect || !classSelect) return;

            const allClassOptions = Array.from(classSelect.querySelectorAll('option')).filter(opt => opt.value !== '');

            function filterClasses() {
                const selectedYear = yearSelect.value;
                const currentSelectedClass = classSelect.value;
                const selectedOption = allClassOptions.find(opt => opt.value === currentSelectedClass);
                const keepSelectedClass = selectedOption
                    && (!selectedYear || selectedOption.dataset.academicYear === selectedYear);

                // Reset class options
                classSelect.innerHTML = '<option value="">Semua Kelas</option>';

                allClassOptions.forEach(opt => {
                    const optYear = opt.getAttribute('data-academic-year');
                    if (!selectedYear || optYear === selectedYear) {
                        const newOption = opt.cloneNode(true);
                        if (keepSelectedClass && newOption.value === currentSelectedClass) {
                            newOption.selected = true;
                        }
                        classSelect.appendChild(newOption);
                    }
                });
            }

            @if ($isTeacher)
                yearSelect.addEventListener('change', function () {
                    document.getElementById('report-card-grade-filter-form').requestSubmit();
                });
            @else
                yearSelect.addEventListener('change', filterClasses);
            @endif
        });
    </script>
    @endpush

@endsection
