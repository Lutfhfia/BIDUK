@extends('layouts.app')

@section('title', 'Rekap Absensi')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>

    <li class="breadcrumb-item">
        <span>Laporan</span>
    </li>

    <li class="breadcrumb-item active" aria-current="page">
        Rekap Absensi
    </li>
@endsection

@push('styles')
<style>
    .empty-state-action {
        padding: 0.25rem 0.65rem !important;
        font-size: 0.75rem !important;
        line-height: 1.25;
    }

    .empty-state-action i {
        margin-bottom: 0 !important;
        font-size: 0.8rem !important;
        opacity: 1 !important;
    }

    .attendance-summary-grid {
        --bs-gutter-x: 0.85rem;
        --bs-gutter-y: 0.85rem;
    }

    .attendance-summary-card {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        min-height: 88px;
        padding: 0.9rem 1rem;
        border: 1px solid #e5eee8;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 4px 14px rgba(20, 49, 35, 0.045);
    }

    .attendance-summary-icon {
        display: inline-flex;
        flex: 0 0 42px;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: var(--biduk-primary-light);
        color: var(--biduk-primary);
        font-size: 1.15rem;
    }

    .attendance-summary-grid > .col:nth-child(2) .attendance-summary-icon {
        background: #eef8f1;
        color: #267c50;
    }

    .attendance-summary-grid > .col:nth-child(3) .attendance-summary-icon {
        background: #e9f5ef;
        color: var(--biduk-primary-dark);
    }

    .attendance-summary-label {
        margin: 0 0 0.15rem;
        color: #64746b;
        font-size: 0.77rem;
        font-weight: 600;
    }

    .attendance-summary-value {
        color: #20352a;
        font-size: 1.35rem;
        font-weight: 750;
        line-height: 1.1;
    }

    .attendance-summary-unit {
        margin-left: 0.3rem;
        color: #7a8981;
        font-size: 0.72rem;
        font-weight: 500;
    }

    .attendance-action-dropdown .dropdown-menu {
        z-index: 1080;
    }

    @media (max-width: 575.98px) {
        .attendance-summary-card {
            min-height: 76px;
            padding: 0.75rem 0.85rem;
        }
    }
</style>
@endpush

@section('content')

    {{-- Header Halaman --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3 fade-in-up">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-clipboard2-check-fill text-success me-2"></i>Rekap Absensi
            </h4>

            <p class="text-muted mb-0" style="font-size: 0.85rem;">
                Kelola rekap ketidakhadiran siswa berdasarkan tahun ajaran dan kelas.
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('rekap-absensi.create') }}" class="btn btn-biduk-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i>Tambah Rekap Absensi
            </a>
        </div>
    </div>

    {{-- Rangkuman Ketidakhadiran --}}
    <div class="row row-cols-1 row-cols-sm-3 attendance-summary-grid mb-4 fade-in-up">
        <div class="col">
            <article class="attendance-summary-card h-100">
                <span class="attendance-summary-icon" aria-hidden="true">
                    <i class="bi bi-thermometer-half"></i>
                </span>
                <div>
                    <p class="attendance-summary-label">Sakit</p>
                    <div>
                        <strong class="attendance-summary-value">{{ $rekapAbsensiSummary->sakit }}</strong>
                        <span class="attendance-summary-unit">hari</span>
                    </div>
                </div>
            </article>
        </div>
        <div class="col">
            <article class="attendance-summary-card h-100">
                <span class="attendance-summary-icon" aria-hidden="true">
                    <i class="bi bi-envelope-check"></i>
                </span>
                <div>
                    <p class="attendance-summary-label">Izin</p>
                    <div>
                        <strong class="attendance-summary-value">{{ $rekapAbsensiSummary->izin }}</strong>
                        <span class="attendance-summary-unit">hari</span>
                    </div>
                </div>
            </article>
        </div>
        <div class="col">
            <article class="attendance-summary-card h-100">
                <span class="attendance-summary-icon" aria-hidden="true">
                    <i class="bi bi-question-circle"></i>
                </span>
                <div>
                    <p class="attendance-summary-label">Tanpa Keterangan</p>
                    <div>
                        <strong class="attendance-summary-value">{{ $rekapAbsensiSummary->tanpa_keterangan }}</strong>
                        <span class="attendance-summary-unit">hari</span>
                    </div>
                </div>
            </article>
        </div>
    </div>

    {{-- Card Filter --}}
    <div class="biduk-card mb-4 fade-in-up">
        <div class="card-body">

            <form action="{{ route('rekap-absensi.index') }}" method="GET">

                <div class="row g-3 align-items-end">

                    {{-- Tahun Ajaran --}}
                    <div class="col-md-3">
                        <label for="academic_year_id" class="form-label">
                            Tahun Ajaran
                        </label>

                        <select
                            name="academic_year_id"
                            id="academic_year_id"
                            class="form-select"
                        >
                            <option value="" {{ !$selectedAcademicYearId ? 'selected' : '' }}>Semua Tahun Ajaran</option>

                            @foreach ($academicYears as $academicYear)
                                <option
                                    value="{{ $academicYear->id }}"
                                    {{ $selectedAcademicYearId == $academicYear->id ? 'selected' : '' }}
                                >
                                    {{ $academicYear->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Kelas --}}
                    <div class="col-md-3">
                        <label for="class_id" class="form-label">
                            Kelas
                        </label>

                        <select
                            name="class_id"
                            id="class_id"
                            class="form-select"
                        >
                            <option value="">Semua Kelas</option>

                            @foreach ($classes as $class)
                                <option
                                    value="{{ $class->id }}"
                                    {{ request('class_id') == $class->id ? 'selected' : '' }}
                                >
                                    {{ $class->name }}{{ $class->academicYear ? ' (' . $class->academicYear->name . ')' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Cari Siswa --}}
                    <div class="col-md-3">
                        <label for="search" class="form-label">
                            Cari Siswa
                        </label>
                        <input
                            type="search"
                            name="search"
                            id="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Nama / NIS / NISN"
                        >
                    </div>

                    {{-- Tombol --}}
                    <div class="col-md-3">
                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="btn btn-biduk-primary flex-fill"
                            >
                                <i class="bi bi-search me-1"></i>
                                Cari
                            </button>

                            <a
                                href="{{ route('rekap-absensi.index') }}"
                                class="btn btn-biduk-outline"
                                title="Reset Filter"
                            >
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>

                        </div>
                    </div>

                </div>

            </form>

        </div>
    </div>


    {{-- Card Tabel --}}
    <div class="biduk-card fade-in-up">

        <div class="card-header d-flex justify-content-between align-items-center">

            <div>
                <i class="bi bi-list-ul me-2"
                    style="color: var(--biduk-primary);"></i>

                Daftar Rekap Absensi
            </div>

            <span class="badge rounded-pill bg-light text-dark">
                {{ $rekapAbsensi->total() }} Data
            </span>

        </div>


        <div class="card-body p-0">

            @if ($rekapAbsensi->count() > 0)

                <div class="table-responsive">

                    <table class="table biduk-table mb-0">

                        <thead>
                            <tr>

                                <th class="text-center align-middle" rowspan="2" style="width: 60px;">
                                    No
                                </th>

                                <th class="align-middle" rowspan="2" style="width: 140px;">
                                    NISN
                                </th>

                                <th class="align-middle" rowspan="2" style="min-width: 180px;">
                                    Nama Siswa
                                </th>

                                <th class="align-middle" rowspan="2" style="width: 130px;">
                                    Kelas
                                </th>

                                <th class="text-center" colspan="3">
                                    Semester Ganjil
                                </th>

                                <th class="text-center" colspan="3">
                                    Semester Genap
                                </th>

                                <th class="text-center align-middle" rowspan="2" style="width: 90px;">
                                    Aksi
                                </th>

                            </tr>
                            <tr>
                                @foreach (['Sakit', 'Izin', 'Tanpa Keterangan', 'Sakit', 'Izin', 'Tanpa Keterangan'] as $absenceLabel)
                                    <th class="text-center" style="min-width: 90px;">{{ $absenceLabel }}</th>
                                @endforeach
                            </tr>
                        </thead>


                        <tbody>

                            @foreach ($rekapAbsensi as $rekap)

                                <tr>

                                    {{-- Nomor --}}
                                    <td class="text-center">
                                        {{ $rekapAbsensi->firstItem() + $loop->index }}
                                    </td>


                                    {{-- NISN --}}
                                    <td>
                                        <span class="fw-semibold">
                                            {{ $rekap->student->nisn ?? '-' }}
                                        </span>
                                    </td>


                                    {{-- Nama --}}
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            {{ $rekap->student->name ?? '-' }}
                                        </div>
                                    </td>


                                    {{-- Kelas --}}
                                    <td>
                                        <span class="text-muted">
                                            {{ $rekap->schoolClass->name ?? '-' }}
                                        </span>
                                    </td>

                                    @foreach (['ganjil', 'genap'] as $semesterKey)
                                        <td class="text-center">{{ $rekap->{$semesterKey}?->sakit ?? '-' }}</td>
                                        <td class="text-center">{{ $rekap->{$semesterKey}?->izin ?? '-' }}</td>
                                        <td class="text-center">{{ $rekap->{$semesterKey}?->tanpa_keterangan ?? '-' }}</td>
                                    @endforeach


                                    {{-- Aksi --}}
                                    <td class="text-center">

                                        <div class="dropdown action-dropdown master-action-dropdown attendance-action-dropdown">

                                            <button
                                                class="btn btn-sm btn-light border-0 action-menu-toggle"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                data-bs-display="static"
                                                aria-expanded="false"
                                                title="Aksi"
                                                aria-label="Aksi rekap absensi {{ $rekap->student->name ?? '' }}"
                                            >
                                                <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                                            </button>


                                            <ul class="dropdown-menu dropdown-menu-end">

                                                {{-- Detail --}}
                                                <li>
                                                    <a
                                                        class="dropdown-item"
                                                        href="{{ route('rekap-absensi.show', $rekap->actionRecord) }}"
                                                    >
                                                        <i class="bi bi-eye text-success me-2" aria-hidden="true"></i>
                                                        Lihat Detail
                                                    </a>
                                                </li>


                                                @if (auth()->user()->isSuperAdmin() || auth()->user()->isGuru())
                                                    <li>
                                                        <a
                                                            class="dropdown-item"
                                                            href="{{ route('rekap-absensi.edit', $rekap->actionRecord) }}"
                                                        >
                                                            <i class="bi bi-pencil-square text-success me-2" aria-hidden="true"></i>
                                                            Edit
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>

                                                    <li>
                                                        <form
                                                            action="{{ route('rekap-absensi.destroy', $rekap->actionRecord) }}"
                                                            method="POST"
                                                            onsubmit="return confirm('Yakin ingin menghapus rekap absensi ini?');"
                                                        >
                                                            @csrf
                                                            @method('DELETE')

                                                            <button
                                                                type="submit"
                                                                class="dropdown-item text-danger"
                                                            >
                                                                <i class="bi bi-trash3 me-2" aria-hidden="true"></i>
                                                                Hapus
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif

                                            </ul>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}
                @if ($rekapAbsensi->hasPages())

                    <div class="d-flex justify-content-between align-items-center px-4 py-3">

                        <div class="text-muted" style="font-size: 0.8rem;">
                            Menampilkan
                            <strong>{{ $rekapAbsensi->firstItem() }}</strong>
                            sampai
                            <strong>{{ $rekapAbsensi->lastItem() }}</strong>
                            dari
                            <strong>{{ $rekapAbsensi->total() }}</strong>
                            data
                        </div>

                        <div>
                            {{ $rekapAbsensi->links() }}
                        </div>

                    </div>

                @endif

            @else

                {{-- Empty State --}}
                <div class="empty-state py-4">

                    <i class="bi bi-clipboard-x" style="font-size: 1.5rem; margin-bottom: 0.5rem;"></i>

                    <h6 class="mb-1">
                        Belum Ada Data Rekap Absensi
                    </h6>

                    <p class="small mb-2">
                        Belum ada data rekap absensi siswa yang tersedia.
                    </p>

                    <a
                        href="{{ route('rekap-absensi.create') }}"
                        class="btn btn-biduk-primary btn-sm empty-state-action"
                    >
                        <i class="bi bi-plus-lg me-1"></i>
                        Tambah Rekap Absensi
                    </a>

                </div>

            @endif

        </div>

    </div>

@endsection