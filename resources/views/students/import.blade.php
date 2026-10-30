@extends('layouts.app')

@section('title', 'Import Data Siswa')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Data Siswa</a></li>
    <li class="breadcrumb-item active">Import Excel</li>
@endsection

@section('content')
<div class="fade-in-up">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-file-earmark-arrow-up text-success me-2"></i>Import Data Siswa
            </h4>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="fw-semibold mb-1"><i class="bi bi-exclamation-circle me-1"></i> Import gagal:</div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="biduk-card">
                <div class="card-body p-4">
                    <form id="student-import-form" method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="import-academic-year" class="form-label fw-semibold">Tahun Ajaran</label>
                                <select name="academic_year_id" id="import-academic-year" class="form-select" required>
                                    @foreach ($academicYears as $year)
                                        <option
                                            value="{{ $year->id }}"
                                            data-status="{{ $year->status }}"
                                            @selected((int) $selectedAcademicYearId === (int) $year->id)
                                        >
                                            {{ $year->name }} ({{ $year->status === 'active' ? 'Aktif' : 'Tidak Aktif' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="import-class" class="form-label fw-semibold">Kelas Tujuan</label>
                                <select name="class_id" id="import-class" class="form-select">
                                    <option value="" @selected($selectedClassId === null)>
                                        Semua Kelas (tanpa penempatan kelas)
                                    </option>
                                    @foreach ($classes as $class)
                                        <option
                                            value="{{ $class->id }}"
                                            data-academic-year="{{ $class->academic_year_id }}"
                                            @selected((int) $selectedClassId === (int) $class->id)
                                        >
                                            {{ $class->name }} — {{ $class->academicYear?->name ?? 'Tahun ajaran tidak tersedia' }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($classes->isEmpty())
                                    <div class="form-text text-danger">
                                        Belum ada kelas yang dapat dipilih pada tahun ajaran ini. Data tetap dapat diimpor tanpa penempatan kelas.
                                    </div>
                                @else
                                    <div id="import-class-empty" class="form-text text-danger d-none">
                                        Belum ada kelas yang dapat dipilih pada tahun ajaran ini. Data tetap dapat diimpor tanpa penempatan kelas.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-2">Pilih File Excel (.xlsx / .xls)</label>
                            <input type="file" name="file" class="form-control form-control-lg" accept=".xlsx,.xls" required>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button id="import-submit" type="submit" class="btn btn-biduk-primary px-4 py-2">
                                <i class="bi bi-upload me-1"></i> Mulai Import
                            </button>
                            <a href="{{ route('students.import.template') }}" class="btn btn-outline-primary px-3 py-2">
    <i class="bi bi-download me-1"></i> Download Format Template
</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const yearSelect = document.getElementById('import-academic-year');
            const classSelect = document.getElementById('import-class');
            const submitButton = document.getElementById('import-submit');
            const emptyMessage = document.getElementById('import-class-empty');
            if (!yearSelect || !classSelect) return;

            const noClassOption = classSelect.options[0].cloneNode(true);
            const allClassOptions = Array.from(classSelect.options)
                .filter(option => option.value !== '')
                .map(option => option.cloneNode(true));

            function filterClasses() {
                const selectedYear = yearSelect.value;
                const previousClassId = classSelect.value;
                classSelect.replaceChildren(noClassOption.cloneNode(true));

                let firstAvailableClass = null;
                allClassOptions.forEach(option => {
                    if (option.dataset.academicYear !== selectedYear) return;
                    const filteredOption = option.cloneNode(true);
                    if (!firstAvailableClass) firstAvailableClass = filteredOption.value;
                    classSelect.appendChild(filteredOption);
                });

                const previousClassIsAvailable = Array.from(classSelect.options)
                    .some(option => option.value === previousClassId);
                classSelect.value = previousClassIsAvailable
                    ? previousClassId
                    : (firstAvailableClass ?? '');

                const hasAvailableClass = Boolean(firstAvailableClass);
                if (submitButton) submitButton.disabled = !selectedYear;
                if (emptyMessage) emptyMessage.classList.toggle('d-none', hasAvailableClass);
            }

            yearSelect.addEventListener('change', filterClasses);
            filterClasses();
        });
    </script>
@endpush
