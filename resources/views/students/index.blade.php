@extends('layouts.app')

@section('title', 'Data Siswa')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Master Data</li>
    <li class="breadcrumb-item active">Data Siswa</li>
@endsection

@push('styles')
<style>
    .student-page-header {
        gap: 1rem;
    }

    .student-stat-card {
        position: relative;
        isolation: isolate;
        min-height: 116px;
        height: 100%;
        overflow: hidden;
        border: 0;
        border-radius: 17px;
        color: #fff;
        box-shadow: 0 7px 20px rgba(16, 92, 60, .12);
        transition: transform .22s ease, box-shadow .22s ease;
    }

    .student-stat-card::after {
        content: "";
        position: absolute;
        z-index: -1;
        width: 150px;
        height: 150px;
        top: -65px;
        right: -45px;
        border: 1px solid rgba(255,255,255,.16);
        border-radius: 50%;
        box-shadow: 0 0 0 20px rgba(255,255,255,.045),
                    0 0 0 42px rgba(255,255,255,.035);
    }

    .student-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 26px rgba(16, 92, 60, .18);
    }

    .student-stat-card.stat-total {
        background: linear-gradient(135deg, #086546 0%, #169b65 58%, #31bb79 100%);
    }

    .student-stat-card.stat-active {
        background: linear-gradient(135deg, #087b58 0%, #19a86e 60%, #53c88a 100%);
    }

    .student-stat-card.stat-male {
        background: linear-gradient(135deg, #086b62 0%, #0f987d 58%, #43b99a 100%);
    }

    .student-stat-card.stat-female {
        background: linear-gradient(135deg, #08704f 0%, #159b69 58%, #48bd83 100%);
    }

    .student-stat-card .stat-icon {
        width: 52px;
        height: 52px;
        flex: 0 0 52px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255,255,255,.45);
        border-radius: 14px;
        background: rgba(255,255,255,.16);
        color: #fff;
        font-size: 1.55rem;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.13);
    }

    .student-stat-card .stat-label {
        margin-bottom: .25rem;
        color: rgba(255,255,255,.88);
        font-size: .82rem;
        font-weight: 600;
    }

    .student-stat-card .stat-number {
        margin: 0;
        color: #fff;
        font-size: clamp(1.4rem, 2vw, 1.85rem);
        font-weight: 800;
        line-height: 1.15;
        letter-spacing: -.03em;
    }

    .student-stat-card .stat-note {
        margin-top: .32rem;
        color: rgba(255,255,255,.78);
        font-size: .69rem;
        line-height: 1.3;
    }

    .student-actions .btn,
    .student-actions .dropdown-toggle {
        min-height: 40px;
        border-radius: 10px;
        font-weight: 600;
    }

    .student-export-toggle {
        color: #fff !important;
        border: 0 !important;
        background: linear-gradient(120deg, #168653, #22b573) !important;
        box-shadow: 0 4px 12px rgba(22, 134, 83, .16);
    }

    .student-export-toggle:hover,
    .student-export-toggle:focus {
        filter: brightness(.96);
        box-shadow: 0 6px 16px rgba(22, 134, 83, .22);
    }

    .student-export-menu {
        min-width: 185px;
        padding: .45rem;
        border: 1px solid #e0ebe4;
        border-radius: 12px;
        box-shadow: 0 12px 28px rgba(15, 50, 32, .12);
    }

    .student-export-menu .dropdown-item {
        display: flex;
        align-items: center;
        gap: .65rem;
        padding: .65rem .75rem;
        border-radius: 8px;
        font-size: .875rem;
    }

    .student-export-menu .dropdown-item:hover {
        color: #087447;
        background: #edf9f1;
    }

    .student-filter-card,
    .student-table-card {
        border: 1px solid #e7eeea;
        border-radius: 16px;
        box-shadow: 0 5px 18px rgba(20, 49, 35, .045);
        background: #fff;
    }

    .student-filter-card .form-label {
        margin-bottom: .5rem;
        color: #3d5364;
        font-size: .84rem;
        font-weight: 600;
    }

    .student-filter-card .form-control,
    .student-filter-card .form-select {
        min-height: 44px;
        border-color: #dce5e2;
        border-radius: 9px;
        font-size: .88rem;
    }

    .student-filter-card .form-control:focus,
    .student-filter-card .form-select:focus {
        border-color: #55b981;
        box-shadow: 0 0 0 3px rgba(34, 181, 115, .12);
    }

    .student-table-card {
        overflow: hidden;
    }

    .student-table-card .table {
        margin-bottom: 0;
    }

    .student-table-card .table thead th {
        padding: .95rem .85rem;
        border-bottom: 1px solid #dbece2;
        background: #eaf7ef;
        color: #126c4b;
        font-size: .75rem;
        font-weight: 800;
        letter-spacing: .035em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .student-table-card .table tbody td {
        padding: .78rem .85rem;
        border-color: #edf1ef;
        vertical-align: middle;
        font-size: .86rem;
    }

    .student-table-card .table tbody tr {
        transition: background-color .16s ease;
    }

    .student-table-card .table tbody tr:hover {
        background: #f7fcf8;
    }

    .student-avatar {
        display: inline-flex;
        flex: 0 0 34px;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        overflow: hidden;
        border-radius: 50%;
        font-size: .78rem;
        font-weight: 800;
    }

    .student-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .gender-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 27px;
        padding: .28rem .45rem;
        border-radius: 7px;
        font-size: .75rem;
        font-weight: 800;
    }

    .gender-badge.gender-l {
        background: #e4f1ff;
        color: #1763a5;
    }

    .gender-badge.gender-p {
        background: #fde8ef;
        color: #b42358;
    }

    .gender-badge.gender-empty {
        background: #eef2f5;
        color: #687989;
    }

    .student-pagination {
        gap: .75rem;
    }

    .student-empty-state {
        padding: 3.5rem 1.5rem;
        text-align: center;
    }

    .student-empty-state > i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 68px;
        height: 68px;
        border-radius: 20px;
        background: #edf8f1;
        color: #168653;
        font-size: 2rem;
    }

    @media (max-width: 767.98px) {
        .student-stat-card {
            min-height: 106px;
        }

        .student-stat-card .stat-icon {
            width: 43px;
            height: 43px;
            flex-basis: 43px;
            font-size: 1.25rem;
        }

        .student-page-header,
        .student-actions {
            width: 100%;
        }

        .student-actions > * {
            flex: 1 1 auto;
        }

        .student-actions .btn,
        .student-actions .dropdown-toggle {
            width: 100%;
        }

        .student-pagination {
            overflow-x: auto;
            max-width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="fade-in-up">

    {{-- Header halaman --}}
    <div class="student-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-people-fill text-success me-2"></i>Data Siswa
            </h4>
            <p class="text-muted mb-0" style="font-size: .9rem;">
                Kelola data peserta didik SDN 204 Palembang
            </p>
        </div>

        <div class="student-actions d-flex flex-wrap align-items-center gap-2 mt-3 mt-md-0">
            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('students.import') || auth()->user()?->hasPermission('students.create'))
                <a href="{{ route('students.import.form') }}" class="btn btn-outline-success">
                    <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Excel
                </a>
            @endif

            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('students.export'))
                <div class="dropdown">
                    <button
                        class="btn dropdown-toggle student-export-toggle"
                        type="button"
                        id="studentExportDropdown"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <i class="bi bi-download me-1"></i> Export Data
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end student-export-menu" aria-labelledby="studentExportDropdown">
                        <li>
                            <a class="dropdown-item" href="{{ route('students.export.excel') }}">
                                <i class="bi bi-file-earmark-excel text-success"></i>
                                Export Excel
                            </a>
                        </li>
                        <li>
                        <a class="dropdown-item" href="{{ route('students.export.pdf', request()->only([
    'search',
    'academic_year_id',
    'class_id',
    'status',
])) }}">
    <i class="bi bi-file-earmark-pdf text-danger"></i>
    Export PDF
</a>
                        </li>
                    </ul>
                </div>
            @endif

            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('students.create'))
                <a href="{{ route('students.create') }}" class="btn btn-biduk-primary">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Siswa
                </a>
            @endif
        </div>
    </div>

    {{-- Catatan hasil import --}}
    @if (session('import_errors') && count(session('import_errors')) > 0)
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <div class="fw-semibold mb-2">
                <i class="bi bi-exclamation-triangle me-1"></i>Catatan Import Siswa:
            </div>
            <ul class="mb-0 small">
                @foreach (session('import_errors') as $err)
                    <li>Baris {{ $err['row'] ?? '?' }} ({{ $err['name'] ?? '-' }}): {{ $err['message'] }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif

    {{-- Kartu ringkasan --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-xl-3">
            <div class="student-stat-card stat-total">
                <div class="card-body d-flex align-items-center gap-3 p-3 p-lg-4">
                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="min-w-0">
                        <div class="stat-label">Total Siswa</div>
                        <p class="stat-number">{{ number_format($stats['total'] ?? 0) }}</p>
                        <div class="stat-note">Seluruh siswa terdaftar</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="student-stat-card stat-active">
                <div class="card-body d-flex align-items-center gap-3 p-3 p-lg-4">
                    <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
                    <div class="min-w-0">
                        <div class="stat-label">Siswa Aktif</div>
                        <p class="stat-number">{{ number_format($stats['active'] ?? 0) }}</p>
                        <div class="stat-note">Siswa dengan status aktif</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="student-stat-card stat-male">
                <div class="card-body d-flex align-items-center gap-3 p-3 p-lg-4">
                    <div class="stat-icon"><i class="bi bi-gender-male"></i></div>
                    <div class="min-w-0">
                        <div class="stat-label">Laki-laki</div>
                        <p class="stat-number">{{ number_format($stats['male'] ?? 0) }}</p>
                        <div class="stat-note">Data jenis kelamin L</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3">
            <div class="student-stat-card stat-female">
                <div class="card-body d-flex align-items-center gap-3 p-3 p-lg-4">
                    <div class="stat-icon"><i class="bi bi-gender-female"></i></div>
                    <div class="min-w-0">
                        <div class="stat-label">Perempuan</div>
                        <p class="stat-number">{{ number_format($stats['female'] ?? 0) }}</p>
                        <div class="stat-note">Data jenis kelamin P</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Pencarian dan filter --}}
    <div class="student-filter-card mb-4">
        <div class="card-body p-3 p-lg-4">
        <form id="student-filters" method="GET" action="{{ route('students.index') }}">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-lg-4">
                        <label for="student-search" class="form-label">
                            <i class="bi bi-search me-1"></i>Pencarian
                        </label>
                        <input
                            id="student-search"
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Cari nama, NIS, atau NISN..."
                            value="{{ $search }}"
                        >
                    </div>
                    <div class="col-6 col-lg-2">
    <label for="student-academic-year" class="form-label">
        <i class="bi bi-calendar3 me-1"></i> Tahun Ajaran
    </label>

    <select
        id="student-academic-year"
        name="academic_year_id"
        class="form-select"
    >
        @unless ($isTeacher)
            <option value="" @selected(!$academicYearId)>Semua Tahun Ajaran</option>
        @endunless
        @foreach ($academicYears as $academicYear)
            <option
                value="{{ $academicYear->id }}"
                @selected((string) $academicYearId === (string) $academicYear->id)
            >
                {{ $academicYear->name }}
            </option>
        @endforeach
    </select>
</div>
                    <div class="col-6 col-lg-2">
                        <label for="student-class-filter" class="form-label">
                            <i class="bi bi-funnel me-1"></i>Kelas
                        </label>
                        <select id="student-class-filter" name="class_id" class="form-select">
                            <option value="">Semua Kelas</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" {{ (string) $classId === (string) $class->id ? 'selected' : '' }}>
                                    {{ $class->name }}{{ !$academicYearId && $class->academicYear ? ' — ' . $class->academicYear->name : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-6 col-lg-2">
                        <label for="student-status-filter" class="form-label">
                            <i class="bi bi-filter me-1"></i>Status
                        </label>
                        <select id="student-status-filter" name="status" class="form-select">
                            <option value="">Semua Status</option>
                            @foreach ($statuses as $s)
                                <option value="{{ $s }}" {{ $status == $s ? 'selected' : '' }}>
                                    {{ $s }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 col-lg-2 d-flex gap-2">
                        <button type="submit" class="btn btn-biduk-primary w-100">
                            <i class="bi bi-search me-1"></i>Filter
                        </button>
                        @if ($search || $classId || $status || $academicYearId)
                            <a href="{{ route('students.index') }}" class="btn btn-outline-secondary" title="Reset filter" aria-label="Reset filter">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel siswa --}}
    <div class="student-table-card">
        @if ($students->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th style="width: 58px;">No</th>
                            <th>NIS</th>
                            <th>NISN</th>
                            <th>Nama Peserta Didik</th>
                            <th>JK</th>
                            <th>Kelas</th>
                            <th>Status</th>
                            <th style="width: 80px;" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($students as $index => $student)
                            <tr>
                                <td class="text-muted">{{ $students->firstItem() + $index }}</td>
                                <td><span class="fw-semibold">{{ $student->nis ?? '—' }}</span></td>
                                <td>{{ $student->nisn ?? '—' }}</td>

                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($student->photo)
                                            <span class="student-avatar">
                                                <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto {{ $student->name }}">
                                            </span>
                                        @else
                                            <span
                                                class="student-avatar"
                                                style="
                                                    background: {{ $student->gender === 'L' ? '#e4f1ff' : ($student->gender === 'P' ? '#fde8ef' : '#eef2f5') }};
                                                    color: {{ $student->gender === 'L' ? '#1763a5' : ($student->gender === 'P' ? '#b42358' : '#687989') }};
                                                "
                                                aria-hidden="true"
                                            >
                                                {{ strtoupper(substr($student->name ?? '?', 0, 1)) }}
                                            </span>
                                        @endif

                                        <span class="fw-semibold text-dark">{{ $student->name }}</span>
                                    </div>
                                </td>

                                <td>
                                    @if ($student->gender === 'L')
                                        <span class="gender-badge gender-l">L</span>
                                    @elseif ($student->gender === 'P')
                                        <span class="gender-badge gender-p">P</span>
                                    @else
                                        <span class="gender-badge gender-empty" title="Jenis kelamin belum diisi">—</span>
                                    @endif
                                </td>

                                <td>
                                    @php $currentClass = $student->classes->first(); @endphp
                                    @if ($currentClass)
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-2">
                                            {{ $currentClass->name }}
                                        </span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                <td>
                                    @php
                                        $badgeClass = match ($student->status) {
                                            'Aktif' => 'badge-aktif',
                                            'Pindah' => 'badge-pindah',
                                            'Lulus' => 'badge-lulus',
                                            'Alumni' => 'badge-alumni',
                                            'Nonaktif' => 'badge-nonaktif',
                                            default => 'bg-secondary',
                                        };
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $student->status ?? '—' }}</span>
                                </td>

                                <td class="text-center student-actions-cell">
                                    <div class="action-dropdown master-action-dropdown dropdown">
                                        <button
                                            class="btn btn-sm btn-light border-0 action-menu-toggle"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            data-bs-display="static"
                                            aria-expanded="false"
                                            aria-label="Aksi siswa {{ $student->name }}"
                                        >
                                            <i class="bi bi-three-dots-vertical" aria-hidden="true"></i>
                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="{{ route('students.show', array_merge(
                                                        ['student' => $student->id],
                                                        request()->only(['search', 'class_id', 'status', 'academic_year_id'])
                                                    )) }}"
                                                >
                                                    <i class="bi bi-eye text-success me-2" aria-hidden="true"></i>
                                                    Detail Siswa
                                                </a>
                                            </li>

                                            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('students.edit'))
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a
                                                        class="dropdown-item"
                                                        href="{{ route('students.edit', $student->id) }}"
                                                    >
                                                        <i class="bi bi-pencil-square text-success me-2" aria-hidden="true"></i>
                                                        Edit Siswa
                                                    </a>
                                                </li>
                                            @endif

                                            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('students.delete'))
                                                <li>
                                                    <form
                                                        action="{{ route('students.destroy', $student->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')"
                                                    >
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash3 me-2" aria-hidden="true"></i>
                                                            Hapus Siswa
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

            <div class="student-pagination d-flex flex-column flex-md-row justify-content-between align-items-md-center px-3 px-lg-4 py-3 border-top">
                <div class="text-muted small mb-2 mb-md-0">
                    Menampilkan {{ $students->firstItem() }}–{{ $students->lastItem() }}
                    dari {{ $students->total() }} data
                </div>
                <div>
                    {{ $students->links('pagination::bootstrap-5') }}
                </div>
            </div>
        @else
            <div class="student-empty-state">
                <i class="bi bi-people"></i>
                <h5 class="fw-bold mt-3">Belum Ada Data Siswa</h5>
                <p class="text-muted mb-3">
                    @if ($search || $classId || $status)
                        Tidak ada data yang cocok dengan filter pencarian.
                    @else
                        Mulai dengan menambahkan data siswa baru.
                    @endif
                </p>

                @if (!$search && !$classId && !$status && (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('students.create')))
                    <a href="{{ route('students.create') }}" class="btn btn-biduk-primary">
                        <i class="bi bi-plus-lg me-1"></i>Tambah Siswa Pertama
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const yearSelect = document.getElementById('student-academic-year');
        const classSelect = document.querySelector(
            '#student-filters select[name="class_id"]'
        );
        const filterForm = document.getElementById('student-filters');

        if (!yearSelect || !classSelect || !filterForm) {
            return;
        }

        yearSelect.addEventListener('change', function () {
            classSelect.value = '';
            filterForm.requestSubmit();
        });
    });
</script>
@endpush