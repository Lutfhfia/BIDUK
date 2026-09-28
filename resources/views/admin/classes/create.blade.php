@extends('layouts.app')

@section('title', 'Tambah Kelas')

@section('content')

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">

        <div>
            <div class="text-muted small mb-1">
                Master Data / Data Kelas / Tambah Kelas
            </div>

            <h3 class="fw-bold mb-1">
                Tambah Kelas
            </h3>

            <p class="text-muted mb-0">
                Tambahkan data kelas baru untuk tahun ajaran tertentu.
            </p>
        </div>

        <a href="{{ route('classes.index') }}"
           class="btn btn-light border">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>

    </div>


    {{-- Error --}}
    @if($errors->any())

        <div class="alert alert-danger border-0 shadow-sm">

            <div class="fw-semibold mb-1">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                Periksa kembali data yang dimasukkan.
            </div>

            <ul class="mb-0 ps-4">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    {{-- Form --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom py-3">

            <div class="d-flex align-items-center">

                <div class="rounded-3 bg-primary-subtle text-primary
                            d-flex align-items-center justify-content-center me-3"
                     style="width: 44px; height: 44px;">

                    <i class="bi bi-building-add fs-5"></i>

                </div>

                <div>

                    <h5 class="fw-bold mb-1">
                        Informasi Kelas
                    </h5>

                    <small class="text-muted">
                        Lengkapi informasi kelas berikut.
                    </small>

                </div>

            </div>

        </div>


        <div class="card-body p-4">

            <form action="{{ route('classes.store') }}"
                  method="POST">

                @csrf


                <div class="row g-4">


                    {{-- Tahun Ajaran --}}
                    <div class="col-md-6">

                        <label for="academic_year_id"
                               class="form-label fw-semibold">

                            Tahun Ajaran
                            <span class="text-danger">*</span>

                        </label>

                        <select
                            name="academic_year_id"
                            id="academic_year_id"
                            class="form-select @error('academic_year_id') is-invalid @enderror"
                            required>

                            <option value="">
                                -- Pilih Tahun Ajaran --
                            </option>

                            @foreach($academicYears as $academicYear)

                                <option
                                    value="{{ $academicYear->id }}"
                                    {{ old('academic_year_id') == $academicYear->id ? 'selected' : '' }}>

                                    {{ $academicYear->name }}

                                </option>

                            @endforeach

                        </select>

                        @error('academic_year_id')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- Tingkat --}}
                    <div class="col-md-6">

                        <label for="grade_level"
                               class="form-label fw-semibold">

                            Tingkat
                            <span class="text-danger">*</span>

                        </label>

                        <select
                            name="grade_level"
                            id="grade_level"
                            class="form-select @error('grade_level') is-invalid @enderror"
                            required>

                            <option value="">
                                -- Pilih Tingkat --
                            </option>

                            @for($i = 1; $i <= 6; $i++)

                                <option
                                    value="{{ $i }}"
                                    {{ old('grade_level') == $i ? 'selected' : '' }}>

                                    Kelas {{ $i }}

                                </option>

                            @endfor

                        </select>

                        @error('grade_level')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- Nama Kelas --}}
                    <div class="col-md-6">

                        <label for="name"
                               class="form-label fw-semibold">

                            Nama Kelas
                            <span class="text-danger">*</span>

                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name') }}"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Contoh: 1A"
                            maxlength="20"
                            required>

                        @error('name')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                        <div class="form-text">
                            Contoh penamaan: 1A, 1B, 2A, 2B, dan seterusnya.
                        </div>

                    </div>


                    {{-- Wali Kelas --}}
                    <div class="col-md-6">

                        <label for="homeroom_teacher_id"
                               class="form-label fw-semibold">

                            Wali Kelas

                        </label>

                        <select
                            name="homeroom_teacher_id"
                            id="homeroom_teacher_id"
                            class="form-select @error('homeroom_teacher_id') is-invalid @enderror">

                            <option value="">
                                -- Belum Ditentukan --
                            </option>

                            @foreach($teachers as $teacher)

                                <option
                                    value="{{ $teacher->id }}"
                                    {{ old('homeroom_teacher_id') == $teacher->id ? 'selected' : '' }}>

                                    {{ $teacher->name }}

                                    @if($teacher->nip)
                                        — NIP {{ $teacher->nip }}
                                    @endif

                                </option>

                            @endforeach

                        </select>

                        @error('homeroom_teacher_id')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                        <div class="form-text">
                            Satu guru hanya dapat menjadi wali kelas satu kelas
                            pada tahun ajaran yang sama.
                        </div>

                    </div>


                    {{-- Kapasitas --}}
                    <div class="col-md-6">

                        <label for="capacity"
                               class="form-label fw-semibold">

                            Kapasitas Kelas
                            <span class="text-danger">*</span>

                        </label>

                        <div class="input-group">

                            <input
                                type="number"
                                name="capacity"
                                id="capacity"
                                value="{{ old('capacity', 32) }}"
                                class="form-control @error('capacity') is-invalid @enderror"
                                min="1"
                                max="100"
                                required>

                            <span class="input-group-text">
                                siswa
                            </span>

                        </div>

                        @error('capacity')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                        <div class="form-text">
                            Jumlah maksimal siswa yang dapat ditempatkan
                            pada kelas ini.
                        </div>

                    </div>


                    {{-- Status --}}
                    <div class="col-md-6">

                        <label for="status"
                               class="form-label fw-semibold">

                            Status
                            <span class="text-danger">*</span>

                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-select @error('status') is-invalid @enderror"
                            required>

                            <option
                                value="Aktif"
                                {{ old('status', 'Aktif') === 'Aktif' ? 'selected' : '' }}>
                                Aktif
                            </option>

                            <option
                                value="Nonaktif"
                                {{ old('status') === 'Nonaktif' ? 'selected' : '' }}>
                                Nonaktif
                            </option>

                        </select>

                        @error('status')

                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>

                        @enderror

                    </div>


                    {{-- ================================================ --}}
                    {{-- MATA PELAJARAN --}}
                    {{-- ================================================ --}}
                    <div class="col-12">

                        <div class="border rounded-3 p-3">

                            {{-- Header --}}
                            <div class="d-flex justify-content-between align-items-center mb-3">

                                <div>
                                    <label class="form-label fw-semibold mb-0">
                                        <i class="bi bi-book me-1"></i>
                                        Mata Pelajaran
                                        <span class="text-danger">*</span>
                                    </label>

                                    <div class="text-muted small">
                                        Pilih mata pelajaran untuk kelas ini.
                                    </div>
                                </div>

                                @if($subjects->count() > 0)

                                    <div class="form-check mb-0">

                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            id="checkAllSubjects">

                                        <label
                                            class="form-check-label small fw-semibold"
                                            for="checkAllSubjects">

                                            Pilih Semua

                                        </label>

                                    </div>

                                @endif

                            </div>


                            {{-- Daftar Mata Pelajaran --}}
                            @if($subjects->count() > 0)

                                <div class="row g-2">

                                    @foreach($subjects as $subject)

                                        <div class="col-md-4">

                                            <div class="form-check border rounded-2 px-3 py-2">

                                                <input
                                                    class="form-check-input subject-checkbox"
                                                    type="checkbox"
                                                    name="subject_ids[]"
                                                    value="{{ $subject->id }}"
                                                    id="subject_{{ $subject->id }}"
                                                    {{ in_array(
                                                        $subject->id,
                                                        old('subject_ids', [])
                                                    ) ? 'checked' : '' }}>

                                                <label
                                                    class="form-check-label small"
                                                    for="subject_{{ $subject->id }}">

                                                    {{ $subject->name }}

                                                </label>

                                            </div>

                                        </div>

                                    @endforeach

                                </div>

                            @else

                                <div class="alert alert-warning mb-0 py-2">

                                    <i class="bi bi-info-circle me-1"></i>

                                    Belum ada mata pelajaran aktif.

                                </div>

                            @endif


                            {{-- Validation Error --}}
                            @error('subject_ids')

                                <div class="text-danger small mt-2">
                                    {{ $message }}
                                </div>

                            @enderror

                            @error('subject_ids.*')

                                <div class="text-danger small mt-2">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                    </div>


                </div>


                {{-- Tombol --}}
                <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">

                    <a href="{{ route('classes.index') }}"
                       class="btn btn-light border">

                        <i class="bi bi-x-lg me-1"></i>
                        Batal

                    </a>


                    <button type="submit"
                            class="btn btn-primary">

                        <i class="bi bi-check-lg me-1"></i>
                        Simpan Kelas

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- ================================================= --}}
{{-- SCRIPT PILIH SEMUA MATA PELAJARAN --}}
{{-- ================================================= --}}
<script>
document.addEventListener('DOMContentLoaded', function () {

    const checkAll = document.getElementById('checkAllSubjects');

    const subjectCheckboxes = document.querySelectorAll(
        '.subject-checkbox'
    );

    if (!checkAll) {
        return;
    }

    function updateCheckAllStatus() {

        const total = subjectCheckboxes.length;

        const checked = document.querySelectorAll(
            '.subject-checkbox:checked'
        ).length;

        checkAll.checked =
            total > 0 && checked === total;

        checkAll.indeterminate =
            checked > 0 && checked < total;
    }

    checkAll.addEventListener('change', function () {

        subjectCheckboxes.forEach(function (checkbox) {

            checkbox.checked = checkAll.checked;

        });

        updateCheckAllStatus();
    });

    subjectCheckboxes.forEach(function (checkbox) {

        checkbox.addEventListener('change', function () {

            updateCheckAllStatus();

        });

    });

    updateCheckAllStatus();

});
</script>

@endsection
