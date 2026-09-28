@extends('layouts.app')

@section('title', 'Rekap Prestasi')

@section('content')

    <div class="container-fluid py-4">

        {{-- =====================================================
        HEADER
    ====================================================== --}}

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h4 class="fw-bold mb-1">

                    <i class="bi bi-trophy me-2"></i>

                    Rekap Prestasi

                </h4>

                <p class="text-muted mb-0">

                    Rekap prestasi peserta didik berdasarkan
                    tahun ajaran dan kelas.

                </p>

            </div>

        </div>


        {{-- =====================================================
        ALERT
    ====================================================== --}}

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">

                <i class="bi bi-check-circle me-2"></i>

                {{ session('success') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

            </div>
        @endif


        {{-- =====================================================
        FILTER
    ====================================================== --}}

        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h6 class="fw-bold mb-0">

                    <i class="bi bi-funnel me-2"></i>

                    Filter Rekap Prestasi

                </h6>

            </div>


            <div class="card-body">

                <form method="GET" action="{{ route('achievement-reports.index') }}">

                    <div class="row g-3">

                        {{-- Tahun Ajaran --}}
                        <div class="col-md-4">

                            <label class="form-label fw-semibold">

                                Tahun Ajaran

                            </label>

                            <select name="academic_year_id" class="form-select" onchange="this.form.submit()">

                                <option value="">
                                    -- Pilih Tahun Ajaran --
                                </option>

                                @foreach ($academicYears as $academicYear)
                                    <option value="{{ $academicYear->id }}" @selected($selectedAcademicYearId == $academicYear->id)>
                                        {{ $academicYear->name }}
                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- Kelas --}}
                        <div class="col-md-4">

                            <label class="form-label fw-semibold">

                                Kelas

                            </label>

                            <select name="class_id" class="form-select">

                                <option value="">
                                    -- Semua Kelas --
                                </option>

                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" @selected($selectedClassId == $class->id)>

                                        {{ $class->grade_level }}
                                        {{ $class->name }}

                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- Search --}}
                        <div class="col-md-4">

                            <label class="form-label fw-semibold">

                                Cari Siswa

                            </label>

                            <input type="text" name="search" class="form-control" value="{{ request('search') }}"
                                placeholder="Nama / NIS / NISN">

                        </div>

                    </div>


                    <div class="mt-3 d-flex gap-2">

                        <button type="submit" class="btn btn-success">

                            <i class="bi bi-search me-1"></i>

                            Tampilkan

                        </button>


                        <a href="{{ route('achievement-reports.index') }}" class="btn btn-outline-secondary">

                            <i class="bi bi-arrow-clockwise me-1"></i>

                            Reset

                        </a>

                    </div>

                </form>

            </div>

        </div>


        {{-- =====================================================
        INFO
    ====================================================== --}}

        @if ($selectedAcademicYearId)
            <div class="alert alert-light border mb-4">

                <div class="d-flex align-items-center">

                    <i class="bi bi-info-circle me-2"></i>

                    <span>

                        Menampilkan data prestasi peserta didik
                        yang memiliki prestasi pada tahun ajaran
                        yang dipilih.

                    </span>

                </div>

            </div>
        @endif


        {{-- =====================================================
        DATA REKAP
    ====================================================== --}}

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white py-3">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h6 class="fw-bold mb-1">

                            Data Prestasi Peserta Didik

                        </h6>

                        <small class="text-muted">

                            Total data:
                            <strong>
                                {{ $achievements->count() }}
                            </strong>
                            prestasi

                        </small>

                    </div>

                    {{-- Tombol cetak sementara --}}
                    <button type="button" onclick="window.print()" class="btn btn-outline-success">

                        <i class="bi bi-printer me-1"></i>

                        Cetak

                    </button>

                </div>

            </div>


            <div class="card-body">

                @if ($achievements->count() > 0)

                    <div class="table-responsive">

                        <table class="table table-bordered table-hover align-middle">

                            <thead class="table-light">

                                <tr>

                                    <th class="text-center" style="width: 60px;">
                                        No.
                                    </th>

                                    <th>
                                        Nama Siswa
                                    </th>

                                    <th>
                                        NIS
                                    </th>

                                    <th>
                                        NISN
                                    </th>

                                    <th>
                                        Kelas
                                    </th>

                                    <th>
                                        Jenis Prestasi
                                    </th>

                                    <th>
                                        Tingkat
                                    </th>

                                    <th>
                                        Keterangan
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach ($achievements as $index => $achievement)
                                    <tr>

                                        {{-- No --}}
                                        <td class="text-center">

                                            {{ $index + 1 }}

                                        </td>


                                        {{-- Nama --}}
                                        <td>

                                            <div class="fw-semibold">

                                                {{ $achievement->student->name ?? '-' }}

                                            </div>

                                        </td>


                                        {{-- NIS --}}
                                        <td>

                                            {{ $achievement->student->nis ?? '-' }}

                                        </td>


                                        {{-- NISN --}}
                                        <td>

                                            {{ $achievement->student->nisn ?? '-' }}

                                        </td>


                                        {{-- Kelas --}}
                                        
                                        <td>
                                            {{ $achievement->schoolClass->name ?? '-' }}
                                        </td>


                                        {{-- Jenis Prestasi --}}
                                        <td>

                                            {{ $achievement->type }}

                                        </td>


                                        {{-- Tingkat --}}
                                        <td>

                                            @if ($achievement->level)
                                                <span class="badge bg-success">

                                                    {{ $achievement->level }}

                                                </span>
                                            @else
                                                -
                                            @endif

                                        </td>


                                        {{-- Keterangan --}}
                                        <td>

                                            {{ $achievement->description ?: '-' }}

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>
                @else
                    {{-- EMPTY STATE --}}

                    <div class="text-center py-5">

                        <div class="mb-3">

                            <i class="bi bi-trophy" style="font-size: 3rem;"></i>

                        </div>

                        <h6 class="fw-bold">

                            Belum Ada Data Prestasi

                        </h6>

                        <p class="text-muted mb-0">

                            Tidak ditemukan data prestasi
                            berdasarkan filter yang dipilih.

                        </p>

                    </div>

                @endif

            </div>

        </div>

    </div>

@endsection


{{-- =========================================================
    PRINT STYLE
========================================================== --}}

@push('styles')
    <style>
        @media print {

            body {
                background: white !important;
            }

            .sidebar,
            nav,
            .navbar,
            .btn,
            form,
            .card-header {
                display: none !important;
            }

            .container-fluid {
                width: 100% !important;
                max-width: 100% !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            table {
                width: 100% !important;
            }

            table th,
            table td {
                font-size: 11px;
            }

        }
    </style>
@endpush
