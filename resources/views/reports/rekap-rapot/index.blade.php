@extends('layouts.app')

@section('title', 'Rekap Rapot')

@section('content')
<div class="container-fluid py-4">

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-file-earmark-text me-2"></i>Rekap Rapot
            </h4>
            <p class="text-muted mb-0">
                Cetak rapot peserta didik sesuai format rapor sekolah.
            </p>
        </div>
    </div>

    {{-- =========================================================
        ERROR
    ========================================================== --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- =========================================================
        1. PARAMETER LAPORAN
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                1. Pilih Parameter Laporan
            </h5>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('rekap-rapot.index') }}">

                <div class="row g-3">

                    {{-- TAHUN AJARAN --}}
                    <div class="col-md-3">
                        <label class="form-label">
                            Tahun Ajaran
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="academic_year_id"
                            class="form-select"
                        >
                            @foreach($academicYears as $year)
                                <option
                                    value="{{ $year->id }}"
                                    @selected(
                                        (string) $selectedAcademicYearId ===
                                        (string) $year->id
                                    )
                                >
                                    {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- SEMESTER CETAK --}}
                    <div class="col-md-3">
                        <label class="form-label">
                            Semester Cetak
                        </label>

                        <select
                            name="semester"
                            class="form-select"
                        >
                            <option
                                value="ganjil_genap"
                                @selected(
                                    request('semester', 'ganjil_genap') ===
                                    'ganjil_genap'
                                )
                            >
                                Ganjil & Genap
                            </option>

                            <option
                                value="ganjil"
                                @selected(
                                    request('semester') === 'ganjil'
                                )
                            >
                                Ganjil
                            </option>

                            <option
                                value="genap"
                                @selected(
                                    request('semester') === 'genap'
                                )
                            >
                                Genap
                            </option>
                        </select>
                    </div>

                    {{-- KELAS --}}
                    <div class="col-md-3">
                        <label class="form-label">
                            Kelas
                        </label>

                        <select
                            name="class_id"
                            class="form-select"
                        >
                            <option value="">
                                Pilih Kelas
                            </option>

                            <option
                                value="all"
                                @selected($selectedClassId === 'all')
                            >
                                Semua Kelas
                            </option>

                            @foreach($classes as $class)
                                <option
                                    value="{{ $class->id }}"
                                    @selected(
                                        (string) $selectedClassId ===
                                        (string) $class->id
                                    )
                                >
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- STATUS SISWA --}}
                    <div class="col-md-3">
                        <label class="form-label">
                            Status Siswa
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >
                            <option
                                value="Aktif"
                                @selected($selectedStatus === 'Aktif')
                            >
                                Aktif
                            </option>

                            <option
                                value="Tidak Aktif"
                                @selected($selectedStatus === 'Tidak Aktif')
                            >
                                Tidak Aktif
                            </option>

                            <option
                                value="Semua"
                                @selected($selectedStatus === 'Semua')
                            >
                                Semua Status
                            </option>
                        </select>
                    </div>

                    {{-- PENCARIAN --}}
                    <div class="col-md-9">
                        <label class="form-label">
                            Cari Siswa
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="Cari nama, NIS, atau NISN..."
                        >
                    </div>

                    {{-- BUTTON --}}
                    <div class="col-md-3 d-flex align-items-end">
                        <button
                            class="btn btn-success w-100"
                            type="submit"
                        >
                            <i class="bi bi-funnel me-1"></i>
                            Tampilkan
                        </button>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- =========================================================
        2. JENIS CETAK
    ========================================================== --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="fw-bold mb-0">
                2. Pilih Jenis Cetak
            </h5>
        </div>

        <div class="card-body">

            @php
                $semesterMode = request('semester', 'ganjil_genap');

                $commonParams = [
                    'academic_year_id' => $selectedAcademicYearId,
                    'semester' => $semesterMode,
                ];
            @endphp

            <div class="row g-3">

                {{-- PER INDIVIDU --}}
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">

                        <h6 class="fw-bold">
                            Per Individu
                        </h6>

                        <p class="text-muted small mb-3">
                            Cetak satu rapor berdasarkan siswa
                            yang dipilih pada daftar di bawah.
                        </p>

                        <span class="badge bg-light text-dark border">
                            Semester:
                            {{ match($semesterMode) {
                                'ganjil' => 'Ganjil',
                                'genap' => 'Genap',
                                default => 'Ganjil & Genap'
                            } }}
                        </span>

                    </div>
                </div>

                {{-- PER KELAS --}}
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">

                        <h6 class="fw-bold">
                            Per Kelas
                        </h6>

                        <p class="text-muted small">
                            Cetak seluruh rapor pada satu kelas
                            sekaligus.
                        </p>

                        @if($selectedClassId && $selectedClassId !== 'all')

                            <a
                                class="btn btn-outline-success btn-sm"
                                target="_blank"
                                href="{{ route(
                                    'rekap-rapot.batch',
                                    array_filter([
                                        'academic_year_id' => $selectedAcademicYearId,
                                        'semester' => $semesterMode,
                                        'class_id' => $selectedClassId,
                                        'pdf' => 1,
                                    ], fn($v) =>
                                        $v !== null &&
                                        $v !== ''
                                    )
                                ) }}"
                            >
                                <i class="bi bi-file-earmark-pdf me-1"></i>
                                Cetak Kelas
                            </a>

                        @else

                            <button
                                class="btn btn-outline-secondary btn-sm"
                                disabled
                            >
                                Pilih kelas dulu
                            </button>

                        @endif

                    </div>
                </div>

                {{-- SEMUA KELAS --}}
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">

                        <h6 class="fw-bold">
                            Semua Kelas
                        </h6>

                        <p class="text-muted small">
                            Cetak seluruh rapor siswa dalam
                            tahun ajaran terpilih.
                        </p>

                        <a
                            class="btn btn-outline-success btn-sm"
                            target="_blank"
                            href="{{ route(
                                'rekap-rapot.download-all',
                                array_filter([
                                    'academic_year_id' => $selectedAcademicYearId,
                                    'semester' => $semesterMode,
                                    'pdf' => 1,
                                ], fn($v) =>
                                    $v !== null &&
                                    $v !== ''
                                )
                            ) }}"
                        >
                            <i class="bi bi-files me-1"></i>
                            Cetak Semua Kelas
                        </a>

                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- =========================================================
        3. DAFTAR SISWA
    ========================================================== --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">

            <h5 class="fw-bold mb-0">
                3. Daftar Siswa
            </h5>

            <span class="badge text-bg-light">
                {{ $students->count() }} siswa
            </span>

        </div>

        <div class="table-responsive">

            <table class="table table-hover align-middle mb-0">

                <thead class="table-light">
                    <tr>
                        <th style="width:70px">
                            No.
                        </th>

                        <th>
                            NIS
                        </th>

                        <th>
                            NISN
                        </th>

                        <th>
                            Nama Siswa
                        </th>

                        <th>
                            Kelas
                        </th>

                        <th class="text-center">
                            Aksi
                        </th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($students as $index => $student)

                        @php
                            $studentClass = $student->classes()
                                ->where(
                                    'classes.academic_year_id',
                                    $selectedAcademicYearId
                                )
                                ->where(
                                    'classes.status',
                                    'Aktif'
                                )
                                ->first();
                        @endphp

                        <tr>

                            <td>
                                {{ $index + 1 }}
                            </td>

                            <td>
                                {{ $student->nis ?? '-' }}
                            </td>

                            <td>
                                {{ $student->nisn ?? '-' }}
                            </td>

                            <td class="fw-semibold">
                                {{ $student->name }}
                            </td>

                            <td>
                                {{ $studentClass?->name ?? '-' }}
                            </td>

                            <td class="text-center">

                                @if($studentClass)

                                    {{-- PREVIEW --}}
                                    <a
                                        class="btn btn-success btn-sm"
                                        target="_blank"
                                        href="{{ route(
                                            'rekap-rapot.print',
                                            [
                                                'student' => $student->id,
                                                'academic_year_id' => $selectedAcademicYearId,
                                                'semester' => $semesterMode,
                                                'class_id' => $studentClass->id,
                                            ]
                                        ) }}"
                                    >
                                        <i class="bi bi-eye me-1"></i>
                                        Preview
                                    </a>

                                    {{-- PDF --}}
                                    <a
                                        class="btn btn-outline-danger btn-sm"
                                        target="_blank"
                                        href="{{ route(
                                            'rekap-rapot.print',
                                            [
                                                'student' => $student->id,
                                                'academic_year_id' => $selectedAcademicYearId,
                                                'semester' => $semesterMode,
                                                'class_id' => $studentClass->id,
                                                'pdf' => 1,
                                            ]
                                        ) }}"
                                    >
                                        <i class="bi bi-file-earmark-pdf me-1"></i>
                                        PDF
                                    </a>

                                @endif

                            </td>
                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="6"
                                class="text-center py-5 text-muted"
                            >
                                Pilih tahun ajaran dan kelas
                                untuk menampilkan siswa.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>
        </div>
    </div>

</div>
@endsection