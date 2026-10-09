@extends('layouts.app')

@section('title', 'Nilai Rapot')

@section('content')

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

                <form method="GET" action="{{ route('report-card-grades.index') }}">

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

                                        <th class="text-center" style="width: 220px;">
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
                                                @elseif ($student->ganjil_grades_count > 0)
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
                                                @elseif ($student->genap_grades_count > 0)
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
                                            <td class="text-center">

                                                <div class="d-flex justify-content-center gap-1 flex-wrap">

                                                    {{-- Input / Edit Rapot --}}
                                                    @if ($targetClass)
                                                        <a
                                                            href="{{ route('report-card-grades.edit', [
                                                                'class' => $targetClass->id,
                                                                'student' => $student->id,
                                                            ]) }}"
                                                            class="btn btn-sm btn-success"
                                                            title="Input / Edit Rapot Kelas Ini"
                                                        >
                                                            <i class="bi bi-pencil-square me-1"></i>
                                                            {{ $student->ganjil_grades_count > 0 || $student->genap_grades_count > 0 ? 'Edit Rapot' : 'Input Rapot' }}
                                                        </a>
                                                    @else
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
                                                    <a
                                                        href="{{ route('report-card-grades.history', $student->id) }}"
                                                        class="btn btn-sm btn-outline-primary"
                                                        title="Lihat Riwayat Rapot Kelas 1 - 6"
                                                    >
                                                        <i class="bi bi-clock-history me-1"></i>
                                                        Riwayat
                                                    </a>

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

                // Reset class options
                classSelect.innerHTML = '<option value="">Semua Kelas</option>';

                allClassOptions.forEach(opt => {
                    const optYear = opt.getAttribute('data-academic-year');
                    if (!selectedYear || optYear === selectedYear) {
                        const newOption = opt.cloneNode(true);
                        if (newOption.value === currentSelectedClass) {
                            newOption.selected = true;
                        }
                        classSelect.appendChild(newOption);
                    }
                });
            }

            yearSelect.addEventListener('change', filterClasses);
        });
    </script>
    @endpush

@endsection
