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

@push('styles')
<style>
    .attendance-detail-header {
        gap: 1rem;
    }

    .attendance-detail-card .card-header {
        padding-bottom: 0.8rem;
    }

    .attendance-detail-grid {
        --bs-gutter-x: 1rem;
        --bs-gutter-y: 1rem;
    }

    .attendance-detail-field {
        min-width: 0;
    }

    .attendance-detail-label {
        margin-bottom: 0.2rem;
        color: #748179;
        font-size: 0.73rem;
        font-weight: 600;
    }

    .attendance-detail-value {
        overflow-wrap: anywhere;
        color: #263a30;
        font-size: 0.88rem;
        font-weight: 650;
    }

    .attendance-detail-summary {
        --bs-gutter-x: 0.85rem;
        --bs-gutter-y: 0.85rem;
    }

    .attendance-detail-stat {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        min-height: 78px;
        padding: 0.8rem 0.95rem;
        border: 1px solid #e5eee8;
        border-radius: 12px;
        background: #fff;
    }

    .attendance-detail-stat-icon {
        display: inline-flex;
        flex: 0 0 38px;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 11px;
        background: var(--biduk-primary-light);
        color: var(--biduk-primary);
        font-size: 1.05rem;
    }

    .attendance-detail-stat-label {
        margin: 0 0 0.15rem;
        color: #64746b;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .attendance-detail-stat-value {
        color: #20352a;
        font-size: 1.2rem;
        font-weight: 750;
        line-height: 1.1;
    }

    .attendance-detail-stat-unit {
        margin-left: 0.25rem;
        color: #7a8981;
        font-size: 0.7rem;
    }
</style>
@endpush

@section('content')

    {{-- Header --}}
    <div class="attendance-detail-header d-flex flex-column flex-sm-row justify-content-between align-items-sm-center mb-4 fade-in-up">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-clipboard2-check-fill text-success me-2"></i>
                Detail Rekap Absensi
            </h4>

            <p class="text-muted mb-0 small">
                {{ $rekapAbsensi->student->name ?? 'Siswa' }}
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
    <section class="biduk-card attendance-detail-card mb-4 fade-in-up">

        <div class="card-header">
            <i class="bi bi-person-vcard text-success me-2"></i>
            Informasi Siswa
        </div>

        <div class="card-body">

            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-4 attendance-detail-grid">
                <div class="col attendance-detail-field">
                    <div class="attendance-detail-label">NISN</div>
                    <div class="attendance-detail-value">
                        {{ $rekapAbsensi->student->nisn ?? '-' }}
                    </div>
                </div>

                <div class="col attendance-detail-field">
                    <div class="attendance-detail-label">Nama Siswa</div>
                    <div class="attendance-detail-value">
                        {{ $rekapAbsensi->student->name ?? '-' }}
                    </div>
                </div>

                <div class="col attendance-detail-field">
                    <div class="attendance-detail-label">Kelas</div>
                    <div class="attendance-detail-value">
                        {{ $rekapAbsensi->schoolClass->name ?? '-' }}
                    </div>
                </div>

                <div class="col attendance-detail-field">
                    <div class="attendance-detail-label">Tahun Ajaran</div>
                    <div class="attendance-detail-value">
                        {{ $rekapAbsensi->schoolClass->academicYear->name ?? '-' }}
                    </div>
                </div>

            </div>
        </div>
    </section>


    {{-- Rekap Ketidakhadiran --}}
    <section class="biduk-card attendance-detail-card fade-in-up">

        <div class="card-header">
            <i class="bi bi-calendar-x text-success me-2"></i>
            Rekap Ketidakhadiran
        </div>

        <div class="card-body">

            <div class="row g-3">
                @foreach (['ganjil' => 'Ganjil', 'genap' => 'Genap'] as $semesterKey => $semesterLabel)
                    <div class="col-lg-6">
                        <section class="border rounded-3 p-3 h-100">
                            <h6 class="fw-bold mb-3">Semester {{ $semesterLabel }}</h6>
                            @if ($attendanceBySemester[$semesterKey] === null)
                                <p class="text-muted mb-0">Data absensi semester ini masih kosong.</p>
                            @else
                                <div class="row row-cols-1 row-cols-sm-3 attendance-detail-summary">
                                    @foreach ([
                                        'sakit' => ['Sakit', 'bi-thermometer-half'],
                                        'izin' => ['Izin', 'bi-envelope-check'],
                                        'tanpa_keterangan' => ['Tanpa Keterangan', 'bi-question-circle'],
                                    ] as $field => [$label, $icon])
                                        <div class="col">
                                            <article class="attendance-detail-stat h-100">
                                                <span class="attendance-detail-stat-icon" aria-hidden="true">
                                                    <i class="bi {{ $icon }}"></i>
                                                </span>
                                                <div>
                                                    <p class="attendance-detail-stat-label">{{ $label }}</p>
                                                    <div>
                                                        <strong class="attendance-detail-stat-value">{{ $attendanceBySemester[$semesterKey][$field] }}</strong>
                                                        <span class="attendance-detail-stat-unit">hari</span>
                                                    </div>
                                                </div>
                                            </article>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </section>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

@endsection