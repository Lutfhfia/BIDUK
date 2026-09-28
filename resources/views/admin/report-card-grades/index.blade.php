@extends('layouts.app')

@section('title', 'Nilai Rapot')

@section('content')

<div class="container-fluid py-4">

    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <div class="mb-4">

        <h4 class="fw-bold mb-1">
            <i class="bi bi-journal-check me-2"></i>
            Nilai Rapot
        </h4>

        <p class="text-muted mb-0">
            Kelola isi rapot siswa berdasarkan tahun ajaran dan kelas.
        </p>

    </div>


    {{-- =====================================================
        ALERT SUCCESS
    ====================================================== --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- =====================================================
        ALERT ERROR
    ====================================================== --}}
    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-circle me-2"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- =====================================================
        FILTER
    ====================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 py-3">

            <h5 class="fw-bold mb-1">
                <i class="bi bi-funnel me-2"></i>
                Pilih Data
            </h5>

            <small class="text-muted">
                Pilih tahun ajaran dan kelas untuk melihat daftar siswa.
            </small>

        </div>


        <div class="card-body">

            <form
                method="GET"
                action="{{ route('report-card-grades.index') }}"
            >

                <div class="row g-3">

                    {{-- Tahun Ajaran --}}
                    <div class="col-md-6">

                        <label
                            for="academic_year_id"
                            class="form-label fw-semibold"
                        >
                            Tahun Ajaran
                        </label>

                        <select
                            name="academic_year_id"
                            id="academic_year_id"
                            class="form-select"
                            onchange="this.form.submit()"
                        >

                            <option value="">
                                -- Pilih Tahun Ajaran --
                            </option>

                            @foreach($academicYears as $academicYear)

                                <option
                                    value="{{ $academicYear->id }}"
                                    {{ (string) $selectedAcademicYearId === (string) $academicYear->id ? 'selected' : '' }}
                                >
                                    {{ $academicYear->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Kelas --}}
                    <div class="col-md-6">

                        <label
                            for="class_id"
                            class="form-label fw-semibold"
                        >
                            Kelas
                        </label>

                        <select
                            name="class_id"
                            id="class_id"
                            class="form-select"
                            onchange="this.form.submit()"
                            {{ $selectedAcademicYearId ? '' : 'disabled' }}
                        >

                            <option value="">
                                -- Pilih Kelas --
                            </option>

                            @foreach($classes as $class)

                                <option
                                    value="{{ $class->id }}"
                                    {{ (string) $selectedClassId === (string) $class->id ? 'selected' : '' }}
                                >
                                    {{ $class->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =====================================================
        INFORMASI KELAS
    ====================================================== --}}
    @if($selectedClass)

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

                            {{ $selectedClass->subjects->count() }}

                            mata pelajaran

                        </div>

                    </div>

                </div>

            </div>

        </div>

    @endif


    {{-- =====================================================
        DAFTAR SISWA
    ====================================================== --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 py-3">

            <div class="d-flex justify-content-between align-items-center">

                <div>

                    <h5 class="fw-bold mb-1">
                        Daftar Siswa
                    </h5>

                    @if($selectedClass)

                        <small class="text-muted">
                            Kelola nilai rapot Semester Ganjil dan Genap.
                        </small>

                    @else

                        <small class="text-muted">
                            Pilih tahun ajaran dan kelas terlebih dahulu.
                        </small>

                    @endif

                </div>


                @if($selectedClass)

                    <span class="badge bg-success-subtle text-success">

                        {{ $students->count() }}

                        Siswa

                    </span>

                @endif

            </div>

        </div>


        <div class="card-body p-0">


            {{-- BELUM PILIH KELAS --}}
            @if(!$selectedClass)

                <div class="text-center py-5">

                    <i class="bi bi-people fs-1 text-muted"></i>

                    <h6 class="fw-semibold mt-3">
                        Belum memilih kelas
                    </h6>

                    <p class="text-muted mb-0">
                        Silakan pilih tahun ajaran dan kelas terlebih dahulu.
                    </p>

                </div>


            {{-- TIDAK ADA SISWA --}}
            @elseif($students->count() === 0)

                <div class="text-center py-5">

                    <i class="bi bi-person-x fs-1 text-muted"></i>

                    <h6 class="fw-semibold mt-3">
                        Belum ada siswa
                    </h6>

                    <p class="text-muted mb-0">
                        Belum ada siswa aktif di kelas ini.
                    </p>

                </div>


            {{-- TABEL --}}
            @else

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th
                                    class="text-center"
                                    style="width: 60px;"
                                >
                                    No
                                </th>

                                <th style="width: 130px;">
                                    NIS
                                </th>

                                <th style="width: 160px;">
                                    NISN
                                </th>

                                <th>
                                    Nama Siswa
                                </th>

                                <th
                                    class="text-center"
                                    style="width: 150px;"
                                >
                                    Ganjil
                                </th>

                                <th
                                    class="text-center"
                                    style="width: 150px;"
                                >
                                    Genap
                                </th>

                                <th
                                    class="text-center"
                                    style="width: 150px;"
                                >
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($students as $index => $student)

                                <tr>

                                    {{-- No --}}
                                    <td class="text-center">

                                        {{ $index + 1 }}

                                    </td>


                                    {{-- NIS --}}
                                    <td>

                                        {{ $student->nis ?? '-' }}

                                    </td>


                                    {{-- NISN --}}
                                    <td>

                                        {{ $student->nisn ?? '-' }}

                                    </td>


                                    {{-- Nama --}}
                                    <td>

                                        <div class="fw-semibold">
                                            {{ $student->name }}
                                        </div>

                                    </td>


                                    {{-- STATUS GANJIL --}}
                                    <td class="text-center">

                                        @if(!$semesterGanjil)

                                            <span class="badge bg-secondary-subtle text-secondary">
                                                Tidak tersedia
                                            </span>

                                        @elseif($student->ganjil_grades_count > 0)

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


                                    {{-- STATUS GENAP --}}
                                    <td class="text-center">

                                        @if(!$semesterGenap)

                                            <span class="badge bg-secondary-subtle text-secondary">
                                                Tidak tersedia
                                            </span>

                                        @elseif($student->genap_grades_count > 0)

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


                                    {{-- AKSI --}}
                                    <td class="text-center">

                                        <a
                                            href="{{ route(
                                                'report-card-grades.edit',
                                                [
                                                    'class' => $selectedClass->id,
                                                    'student' => $student->id
                                                ]
                                            ) }}"
                                            class="btn btn-sm btn-success"
                                        >

                                            <i class="bi bi-pencil-square me-1"></i>

                                            @if(
                                                $student->ganjil_grades_count > 0 ||
                                                $student->genap_grades_count > 0
                                            )

                                                Edit Rapot

                                            @else

                                                Input Rapot

                                            @endif

                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection
