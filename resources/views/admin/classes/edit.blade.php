@extends('layouts.app')

@section('title', 'Edit Data Kelas')

@section('content')

    <div class="container-fluid py-4">

        {{-- Header --}}
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">

            <div>

                <a href="{{ route('classes.show', $class) }}"
                    class="text-decoration-none text-muted small">

                    <i class="bi bi-arrow-left me-1"></i>
                    Kembali ke Detail Kelas

                </a>

                <h3 class="fw-bold mt-2 mb-1">
                    Edit Data Kelas
                </h3>

                <p class="text-muted mb-0">
                    Perbarui informasi kelas {{ $class->name }}.
                </p>

            </div>

        </div>


        {{-- Error Validasi --}}
        @if ($errors->any())

            <div class="alert alert-danger alert-dismissible fade show" role="alert">

                <div class="fw-semibold mb-1">

                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Periksa kembali data yang dimasukkan.

                </div>

                <ul class="mb-0">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

                <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
                </button>

            </div>

        @endif


        {{-- Form Edit --}}
        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white border-bottom py-3">

                <h5 class="fw-bold mb-0">

                    <i class="bi bi-pencil-square me-2"></i>
                    Informasi Kelas

                </h5>

            </div>


            <div class="card-body p-4">

                <form action="{{ route('classes.update', $class) }}"
                    method="POST">

                    @csrf
                    @method('PUT')


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

                                @foreach ($academicYears as $academicYear)

                                    <option
                                        value="{{ $academicYear->id }}"
                                        {{ old('academic_year_id', $class->academic_year_id) == $academicYear->id ? 'selected' : '' }}>

                                        {{ $academicYear->name }}

                                        @if ($academicYear->status === 'active')
                                            (Aktif)
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                            @error('academic_year_id')

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
                                value="{{ old('name', $class->name) }}"
                                class="form-control @error('name') is-invalid @enderror"
                                placeholder="Contoh: 1A"
                                maxlength="20"
                                required>

                            @error('name')

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

                                @for ($i = 1; $i <= 6; $i++)

                                    <option
                                        value="{{ $i }}"
                                        {{ old('grade_level', $class->grade_level) == $i ? 'selected' : '' }}>

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

                                @foreach ($teachers as $teacher)

                                    <option
                                        value="{{ $teacher->id }}"
                                        {{ old('homeroom_teacher_id', $class->homeroom_teacher_id) == $teacher->id ? 'selected' : '' }}>

                                        {{ $teacher->name }}

                                        @if ($teacher->nip)
                                            — NIP: {{ $teacher->nip }}
                                        @endif

                                    </option>

                                @endforeach

                            </select>

                            <div class="form-text">
                                Satu guru hanya dapat menjadi wali kelas untuk satu kelas
                                pada tahun ajaran yang sama.
                            </div>

                            @error('homeroom_teacher_id')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- Kapasitas --}}
                        <div class="col-md-6">

                            <label for="capacity"
                                class="form-label fw-semibold">

                                Kapasitas Kelas
                                <span class="text-danger">*</span>

                            </label>

                            <input
                                type="number"
                                name="capacity"
                                id="capacity"
                                value="{{ old('capacity', $class->capacity) }}"
                                class="form-control @error('capacity') is-invalid @enderror"
                                min="1"
                                max="100"
                                required>

                            <div class="form-text">
                                Jumlah maksimal siswa yang dapat ditempatkan
                                dalam kelas.
                            </div>

                            @error('capacity')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

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
                                    {{ old('status', $class->status) === 'Aktif' ? 'selected' : '' }}>

                                    Aktif

                                </option>

                                <option
                                    value="Nonaktif"
                                    {{ old('status', $class->status) === 'Nonaktif' ? 'selected' : '' }}>

                                    Nonaktif

                                </option>

                            </select>

                            @error('status')

                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>


                        {{-- ================================================= --}}
                        {{-- MATA PELAJARAN --}}
                        {{-- ================================================= --}}
                        <div class="col-12">

                            <div class="border rounded-3 p-3">

                                {{-- Header Mata Pelajaran --}}
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


                                    {{-- Pilih Semua --}}
                                    @if ($subjects->count() > 0)

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
                                @if ($subjects->count() > 0)

                                    <div class="row g-2">

                                        @foreach ($subjects as $subject)

                                            <div class="col-md-4">

                                                <div class="form-check border rounded-2 px-3 py-2">

                                                    <input
                                                        class="form-check-input subject-checkbox"
                                                        type="checkbox"
                                                        name="subject_ids[]"
                                                        value="{{ $subject->id }}"
                                                        id="subject_{{ $subject->id }}"

                                                        {{-- Jika sebelumnya sudah dipilih --}}
                                                        {{ in_array(
                                                            $subject->id,
                                                            old('subject_ids', $selectedSubjectIds ?? [])
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


                                {{-- Error Mata Pelajaran --}}
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


                    {{-- Informasi Siswa --}}
                    <div class="alert alert-info mt-4 mb-0">

                        <div class="d-flex">

                            <i class="bi bi-info-circle me-2 mt-1"></i>

                            <div>

                                <strong>Informasi kelas</strong>

                                <div class="small mt-1">

                                    Kelas ini saat ini memiliki
                                    <strong>{{ $class->student_count }}</strong>
                                    siswa aktif.

                                    Kapasitas kelas tidak dapat diatur
                                    lebih kecil dari jumlah siswa yang
                                    sudah terdaftar.

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Tombol --}}
                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <a
                            href="{{ route('classes.show', $class) }}"
                            class="btn btn-light border">

                            <i class="bi bi-x-lg me-1"></i>
                            Batal

                        </a>


                        <button
                            type="submit"
                            class="btn btn-primary">

                            <i class="bi bi-check-lg me-1"></i>
                            Simpan Perubahan

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
        document.addEventListener('DOMContentLoaded', function() {

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


            // Pilih semua
            checkAll.addEventListener('change', function() {

                subjectCheckboxes.forEach(function(checkbox) {

                    checkbox.checked = checkAll.checked;

                });

                updateCheckAllStatus();

            });


            // Perubahan checkbox individual
            subjectCheckboxes.forEach(function(checkbox) {

                checkbox.addEventListener('change', function() {

                    updateCheckAllStatus();

                });

            });


            // Cek status saat halaman pertama dibuka
            updateCheckAllStatus();

        });
    </script>

@endsection
