@extends('layouts.app')

@section('title', 'Detail Rekap Absensi')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>

    <li class="breadcrumb-item">
        <span>Laporan</span>
    </li>

    <li class="breadcrumb-item">
        <a href="{{ route('rekap-absensi.index') }}">Rekap Absensi</a>
    </li>

    <li class="breadcrumb-item active" aria-current="page">
        Detail
    </li>
@endsection

@section('content')

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-clipboard2-check-fill me-2"
                    style="color: var(--biduk-primary);"></i>
                Detail Rekap Absensi
            </h4>

            <p class="text-muted mb-0" style="font-size: 0.875rem;">
                Detail rekap ketidakhadiran siswa.
            </p>
        </div>

        <a
            href="{{ route('rekap-absensi.index') }}"
            class="btn btn-biduk-outline"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>


    {{-- Identitas Siswa --}}
    <div class="biduk-card mb-4 fade-in-up">

        <div class="card-header">
            <i class="bi bi-person-vcard me-2"
                style="color: var(--biduk-primary);"></i>
            Identitas Siswa
        </div>

        <div class="card-body">

            <div class="row g-4">

                <div class="col-md-6">
                    <div class="text-muted small mb-1">NISN</div>
                    <div class="fw-semibold">
                        {{ $rekapAbsensi->student->nisn ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small mb-1">Nama Siswa</div>
                    <div class="fw-semibold">
                        {{ $rekapAbsensi->student->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small mb-1">Kelas</div>
                    <div class="fw-semibold">
                        {{ $rekapAbsensi->schoolClass->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small mb-1">Tahun Ajaran</div>
                    <div class="fw-semibold">
                        {{ $rekapAbsensi->schoolClass->academicYear->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="text-muted small mb-1">Semester</div>
                    <div class="fw-semibold">
                        {{ $rekapAbsensi->semester->name ?? '-' }}
                    </div>
                </div>

            </div>

        </div>

    </div>


    {{-- Rekap Ketidakhadiran --}}
    <div class="biduk-card fade-in-up">

        <div class="card-header">
            <i class="bi bi-calendar-x me-2"
                style="color: var(--biduk-primary);"></i>
            Rekap Ketidakhadiran
        </div>

        <div class="card-body">

            <div class="row g-4">

                {{-- Sakit --}}
                <div class="col-md-4">
                    <div class="border rounded-3 p-4 h-100">
                        <div class="text-muted mb-2">
                            <i class="bi bi-thermometer-half me-1"></i>
                            Sakit
                        </div>

                        <div class="display-6 fw-bold">
                            {{ $rekapAbsensi->sakit }}
                        </div>

                        <div class="text-muted small">
                            Hari
                        </div>
                    </div>
                </div>


                {{-- Izin --}}
                <div class="col-md-4">
                    <div class="border rounded-3 p-4 h-100">
                        <div class="text-muted mb-2">
                            <i class="bi bi-envelope-check me-1"></i>
                            Izin
                        </div>

                        <div class="display-6 fw-bold">
                            {{ $rekapAbsensi->izin }}
                        </div>

                        <div class="text-muted small">
                            Hari
                        </div>
                    </div>
                </div>


                {{-- Tanpa Keterangan --}}
                <div class="col-md-4">
                    <div class="border rounded-3 p-4 h-100">
                        <div class="text-muted mb-2">
                            <i class="bi bi-question-circle me-1"></i>
                            Tanpa Keterangan
                        </div>

                        <div class="display-6 fw-bold">
                            {{ $rekapAbsensi->tanpa_keterangan }}
                        </div>

                        <div class="text-muted small">
                            Hari
                        </div>
                    </div>
                </div>

            </div>

        </div>

    </div>

@endsection