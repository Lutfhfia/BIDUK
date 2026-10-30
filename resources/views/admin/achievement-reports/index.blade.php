@extends('layouts.app')

@section('title', 'Rekap Prestasi')

@section('content')
<style>
    .achievement-report-page {
        --ar-green: #16834f;
        --ar-green-dark: #106b40;
        --ar-border: #e1ebe5;
        --ar-muted: #718078;
        color: #25372c;
    }

    .achievement-report-page .ar-hero {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        overflow: hidden;
        padding: 24px 27px;
        margin-bottom: 22px;
        border: 1px solid #dceee3;
        border-radius: 16px;
        background: linear-gradient(115deg, #e5f5eb 0%, #f8fcf9 48%, #def2e6 100%);
    }

    .achievement-report-page .ar-hero::after {
        position: absolute;
        top: 50%;
        right: 19%;
        width: 175px;
        height: 175px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(32, 185, 104, .09), rgba(32, 185, 104, 0));
        content: "";
        pointer-events: none;
        transform: translateY(-50%);
    }

    .achievement-report-page .ar-hero > * {
        position: relative;
        z-index: 1;
    }

    .achievement-report-page .ar-hero h4 {
        margin-bottom: 6px;
        color: #1f3026;
        font-size: clamp(1.35rem, 2vw, 1.75rem);
        font-weight: 750;
    }

    .achievement-report-page .ar-hero h4 i,
    .achievement-report-page .ar-card-heading i {
        color: var(--ar-green);
    }

    .achievement-report-page .ar-hero p {
        margin: 0;
        color: #66766c;
        font-size: .9rem;
        line-height: 1.6;
    }

    .achievement-report-page .ar-card {
        overflow: visible;
        border: 1px solid var(--ar-border) !important;
        border-radius: 15px !important;
        background: #fff;
        box-shadow: 0 5px 19px rgba(30, 70, 45, .045) !important;
    }

    .achievement-report-page .ar-card > .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 15px 20px;
        border-bottom: 1px solid var(--ar-border);
        border-radius: 15px 15px 0 0;
        background: linear-gradient(90deg, #fff, #f7fbf8);
        color: #293a2f;
        font-size: .96rem;
        font-weight: 700;
    }

    .achievement-report-page .ar-filter-card .card-body {
        padding: 20px;
    }

    .achievement-report-page .form-label {
        display: block;
        margin-bottom: 8px;
        color: #526257;
        font-size: .82rem;
        font-weight: 600;
    }

    .achievement-report-page .form-control,
    .achievement-report-page .form-select {
        min-height: 41px;
        border: 1px solid #dce6df;
        border-radius: 8px;
        background-color: #fff;
        color: #34463a;
        font-size: .84rem;
        box-shadow: none;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .achievement-report-page .form-control:focus,
    .achievement-report-page .form-select:focus {
        border-color: #70bd91;
        box-shadow: 0 0 0 3px rgba(22, 131, 79, .09);
    }

    .achievement-report-page .btn-ar-primary {
        display: inline-flex;
        min-height: 41px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 16px;
        border: 1px solid transparent;
        border-radius: 8px;
        background: linear-gradient(105deg, #16834f, #20b968);
        color: #fff;
        font-size: .84rem;
        font-weight: 600;
    }

    .achievement-report-page .btn-ar-primary:hover {
        background: linear-gradient(105deg, #116d42, #159653);
        color: #fff;
    }

    .achievement-report-page .btn-ar-reset {
        display: inline-flex;
        min-width: 41px;
        min-height: 41px;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 8px 13px;
        border: 1px solid #b8d9c4;
        border-radius: 8px;
        background: #fff;
        color: var(--ar-green);
        font-size: .84rem;
        font-weight: 600;
    }

    .achievement-report-page .btn-ar-reset:hover {
        background: #e5f5eb;
        color: var(--ar-green-dark);
    }

    .achievement-report-page .ar-filter-summary {
        margin: 0 0 18px;
        color: #718078;
        font-size: .86rem;
    }

    .achievement-report-page .ar-filter-summary strong {
        color: #435247;
        font-weight: 650;
    }

    .achievement-report-page .ar-count {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border: 1px solid #dceee3;
        border-radius: 8px;
        background: #f0f8f3;
        color: var(--ar-green-dark);
        font-size: .78rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .achievement-report-page .ar-table-card .card-body {
        padding: 0;
    }

    .achievement-report-page .ar-table-responsive {
        width: 100%;
        overflow-x: auto;
    }

    .achievement-report-page .ar-data-table {
        width: 100%;
        min-width: 900px;
        margin: 0;
        vertical-align: middle;
    }

    .achievement-report-page .ar-data-table thead th {
        padding: 13px 12px;
        border-top: 0;
        border-bottom: 1px solid #d8e9de;
        background: linear-gradient(90deg, #e5f4eb, #f0f8f3 50%, #e5f4eb);
        color: #3e5747;
        font-size: .78rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .achievement-report-page .ar-data-table tbody td {
        padding: 11px 12px;
        border-color: #e7eee9;
        background: #fff;
        color: #435247;
        font-size: .83rem;
        vertical-align: middle;
    }

    .achievement-report-page .ar-data-table tbody tr:hover td {
        background: #f7fbf8;
    }

    .achievement-report-page .ar-data-table .ar-student-name {
        color: #293a2f;
        font-weight: 650;
    }

    .achievement-report-page .ar-level-badge {
        display: inline-block;
        padding: 4px 8px;
        border: 1px solid #dceee3;
        border-radius: 6px;
        background: #f0f8f3;
        color: #176b43;
        font-size: .73rem;
        font-weight: 650;
        white-space: nowrap;
    }

    .achievement-report-page .ar-empty-state {
        padding: 48px 20px;
        color: var(--ar-muted);
        text-align: center;
    }

    .achievement-report-page .ar-empty-state i {
        color: #9aa9a0;
        font-size: 2.4rem;
    }

    .achievement-report-page .ar-empty-state h6 {
        margin: 12px 0 6px;
        color: #34463a;
        font-weight: 700;
    }

    .achievement-report-page .alert {
        border-radius: 10px;
    }

    .achievement-report-page .ar-print-title {
        display: none;
    }

    @media (max-width: 767.98px) {
        .achievement-report-page .ar-hero {
            align-items: flex-start;
            flex-direction: column;
            gap: 12px;
            padding: 20px;
        }

        .achievement-report-page .ar-hero::after {
            right: 2%;
        }

        .achievement-report-page .ar-filter-card .card-body {
            padding: 16px;
        }

        .achievement-report-page .ar-card > .card-header {
            padding: 15px;
        }

        .achievement-report-page .ar-filter-summary {
            line-height: 1.7;
        }
    }

    @page {
        size: A4 landscape;
        margin: 12mm 10mm;
    }

    @media print {
        html,
        body {
            width: 100% !important;
            min-width: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
            color: #000 !important;
        }

        .sidebar,
        .biduk-sidebar,
        .sidebar-overlay,
        nav,
        header,
        .navbar,
        .biduk-navbar,
        .navbar-user,
        .topbar,
        .app-header,
        .breadcrumb,
        .ar-screen-only {
            display: none !important;
        }

        .biduk-main,
        .biduk-content,
        .container-fluid,
        .achievement-report-page {
            width: 100% !important;
            max-width: 100% !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }

        .achievement-report-page {
            color: #000 !important;
        }

        .achievement-report-page .ar-table-card,
        .achievement-report-page .ar-table-card .card-body {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: #fff !important;
            box-shadow: none !important;
        }

        .achievement-report-page .ar-print-area {
            display: block !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }

        .achievement-report-page .ar-print-title {
            display: block !important;
            margin: 0 0 7mm !important;
            padding: 0 !important;
            color: #000 !important;
            font-family: "Times New Roman", serif !important;
            font-size: 14pt !important;
            font-weight: 700 !important;
            letter-spacing: .3px;
            text-align: center !important;
        }

        .achievement-report-page .ar-table-responsive {
            width: 100% !important;
            overflow: visible !important;
        }

        .achievement-report-page .ar-data-table {
            width: 100% !important;
            min-width: 0 !important;
            margin: 0 !important;
            border-collapse: collapse !important;
            table-layout: fixed !important;
            color: #000 !important;
            font-family: "Times New Roman", serif !important;
        }

        .achievement-report-page .ar-data-table thead {
            display: table-header-group !important;
        }

        .achievement-report-page .ar-data-table tr {
            break-inside: avoid !important;
            page-break-inside: avoid !important;
        }

        .achievement-report-page .ar-data-table thead th,
        .achievement-report-page .ar-data-table tbody td {
            border: 1px solid #000 !important;
            padding: 6px 5px !important;
            background: #fff !important;
            color: #000 !important;
            font-family: "Times New Roman", serif !important;
            font-size: 9pt !important;
            line-height: 1.25 !important;
            overflow-wrap: anywhere !important;
            vertical-align: top !important;
            box-shadow: none !important;
        }

        .achievement-report-page .ar-data-table thead th {
            font-weight: 700 !important;
            text-align: center !important;
        }

        .achievement-report-page .ar-data-table th:nth-child(1),
        .achievement-report-page .ar-data-table td:nth-child(1) {
            width: 5% !important;
            text-align: center !important;
        }

        .achievement-report-page .ar-data-table th:nth-child(2),
        .achievement-report-page .ar-data-table td:nth-child(2) {
            width: 18% !important;
        }

        .achievement-report-page .ar-data-table th:nth-child(3),
        .achievement-report-page .ar-data-table td:nth-child(3) {
            width: 9% !important;
            text-align: center !important;
        }

        .achievement-report-page .ar-data-table th:nth-child(4),
        .achievement-report-page .ar-data-table td:nth-child(4) {
            width: 11% !important;
            text-align: center !important;
        }

        .achievement-report-page .ar-data-table th:nth-child(5),
        .achievement-report-page .ar-data-table td:nth-child(5) {
            width: 8% !important;
            text-align: center !important;
        }

        .achievement-report-page .ar-data-table th:nth-child(6),
        .achievement-report-page .ar-data-table td:nth-child(6) {
            width: 20% !important;
        }

        .achievement-report-page .ar-data-table th:nth-child(7),
        .achievement-report-page .ar-data-table td:nth-child(7) {
            width: 10% !important;
            text-align: center !important;
        }

        .achievement-report-page .ar-data-table th:nth-child(8),
        .achievement-report-page .ar-data-table td:nth-child(8) {
            width: 19% !important;
        }

        .achievement-report-page .ar-level-badge,
        .achievement-report-page .badge {
            display: inline !important;
            padding: 0 !important;
            border: 0 !important;
            border-radius: 0 !important;
            background: transparent !important;
            color: #000 !important;
            font-family: "Times New Roman", serif !important;
            font-size: 9pt !important;
            font-weight: 400 !important;
        }

        .achievement-report-page .ar-data-table .ar-student-name {
            color: #000 !important;
            font-weight: 400 !important;
        }

        .achievement-report-page .ar-empty-row td {
            text-align: center !important;
        }

        * {
            -webkit-print-color-adjust: economy !important;
            print-color-adjust: economy !important;
        }
    }
</style>

<div class="container-fluid py-4 achievement-report-page">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show ar-screen-only" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    {{-- Header halaman --}}
    <div class="ar-hero ar-screen-only">
        <div>
            <h4>
                <i class="bi bi-trophy me-2"></i>Rekap Prestasi
            </h4>
            <p>Lihat rekap prestasi peserta didik berdasarkan tahun ajaran dan kelas.</p>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card ar-card ar-filter-card mb-4 ar-screen-only">
        <div class="card-header">
            <span><i class="bi bi-funnel me-2"></i>Filter Rekap Prestasi</span>
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('achievement-reports.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="achievementAcademicYear" class="form-label">Tahun Ajaran</label>
                        <select
                            name="academic_year_id"
                            id="achievementAcademicYear"
                            class="form-select"
                            onchange="this.form.submit()"
                        >
                            <option value="" @selected(!$selectedAcademicYearId)>-- Semua Tahun Ajaran --</option>
                            @foreach ($academicYears as $academicYear)
                                <option value="{{ $academicYear->id }}" @selected((string) $selectedAcademicYearId === (string) $academicYear->id)>
                                    {{ $academicYear->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="achievementClass" class="form-label">Kelas</label>
                        <select name="class_id" id="achievementClass" class="form-select">
                            <option value="">-- Semua Kelas --</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected((string) $selectedClassId === (string) $class->id)>
                                    {{ $class->grade_level }} {{ $class->name }}{{ $class->academicYear ? ' (' . $class->academicYear->name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="achievementSearch" class="form-label">Cari Siswa</label>
                        <input
                            type="text"
                            name="search"
                            id="achievementSearch"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Nama / NIS / NISN"
                        >
                    </div>
                </div>

                <div class="mt-3 d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-ar-primary">
                        <i class="bi bi-search"></i>Tampilkan
                    </button>
                    <a href="{{ route('achievement-reports.index') }}" class="btn btn-ar-reset">
                        <i class="bi bi-arrow-clockwise"></i>Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    @php
        $filteredYear = $academicYears->firstWhere('id', $selectedAcademicYearId);
        $filteredClass = $classes->firstWhere('id', $selectedClassId);
    @endphp

    {{-- Ringkasan filter di layar; tidak ikut dicetak. --}}
    <div class="ar-filter-summary ar-screen-only">
        Rentang filter: tahun ajaran
        <strong>{{ $filteredYear?->name ?? 'semua tahun ajaran' }}</strong>,
        kelas
        <strong>{{ $filteredClass ? $filteredClass->grade_level . ' ' . $filteredClass->name : 'semua kelas' }}</strong>,
        pencarian <strong>{{ request('search') ?: 'semua siswa' }}</strong>.
    </div>

    {{-- Daftar prestasi dan area khusus cetak --}}
    <div class="card ar-card ar-table-card">
        <div class="card-header ar-screen-only">
            <div>
                <div class="ar-card-heading"><i class="bi bi-trophy me-2"></i>Data Prestasi Peserta Didik</div>
                <small class="text-muted">Total data: <strong>{{ $achievements->count() }}</strong> prestasi</small>
            </div>
            <button type="button" onclick="window.print()" class="btn btn-ar-reset">
                <i class="bi bi-printer"></i>Cetak
            </button>
        </div>

        <div class="card-body">
            <div class="ar-print-area">
                <h1 class="ar-print-title">REKAP PRESTASI</h1>

                <div class="ar-table-responsive">
                    <table class="table table-hover align-middle ar-data-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;" class="text-center">No.</th>
                                <th>Nama Siswa</th>
                                <th>NIS</th>
                                <th>NISN</th>
                                <th>Kelas</th>
                                <th>Jenis Prestasi</th>
                                <th>Tingkat</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($achievements as $index => $achievement)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td><span class="ar-student-name">{{ $achievement->student->name ?? '-' }}</span></td>
                                    <td>{{ $achievement->student->nis ?? '-' }}</td>
                                    <td>{{ $achievement->student->nisn ?? '-' }}</td>
                                    <td>{{ $achievement->schoolClass->name ?? '-' }}</td>
                                    <td>{{ $achievement->type }}</td>
                                    <td class="text-center">
                                        @if ($achievement->level)
                                            <span class="ar-level-badge">{{ $achievement->level }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $achievement->description ?: '-' }}</td>
                                </tr>
                            @empty
                                <tr class="ar-empty-row">
                                    <td colspan="8" class="text-center py-5">
                                        <div class="ar-screen-only">
                                            <i class="bi bi-trophy"></i>
                                            <h6>Belum Ada Data Prestasi</h6>
                                            <p class="mb-0">Tidak ditemukan data prestasi berdasarkan filter yang dipilih.</p>
                                        </div>
                                        <span class="d-none d-print-inline">Tidak ada data prestasi.</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
