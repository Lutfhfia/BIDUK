@extends('layouts.app')

@section('title', 'Rekap Rapor')

@section('content')
<style>
    .rr-page {
        --rr-green: #168653;
        --rr-green-dark: #087246;
        --rr-green-bright: #24b86d;
        --rr-mint: #e9f7ef;
        --rr-border: #e1eae5;
        --rr-text: #17352b;
        color: var(--rr-text);
    }

    .rr-hero {
        position: relative;
        overflow: hidden;
        border: 1px solid #d8eee1;
        border-radius: 18px;
        padding: 19px 23px;
        margin-bottom: 18px;
        background: linear-gradient(115deg, #f0fbf4 0%, #ffffff 64%, #e1f7e9 100%);
    }

    .rr-hero-icon {
        width: 47px;
        height: 47px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 14px;
        color: #fff;
        font-size: 22px;
        background: linear-gradient(145deg, #087246, #26b86d);
        box-shadow: 0 8px 18px rgba(22, 134, 83, .18);
    }

    .rr-panel {
        overflow: hidden;
        margin-bottom: 17px;
        border: 1px solid var(--rr-border);
        border-radius: 17px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(22, 70, 44, .055);
    }

    .rr-panel-head {
        padding: 15px 19px;
        border-bottom: 1px solid #edf2ee;
        background: linear-gradient(90deg, #f7fcf8, #fff);
    }

    .rr-panel-body { padding: 18px; }

    .rr-heading {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0;
        font-size: 16px;
        font-weight: 750;
        color: #183a2b;
    }

    .rr-heading i { color: var(--rr-green); font-size: 18px; }

    .rr-input-icon { position: relative; }
    .rr-input-icon > i {
        position: absolute;
        top: 50%;
        left: 14px;
        z-index: 2;
        color: #7b9185;
        transform: translateY(-50%);
    }

    .rr-input-icon .form-control { padding-left: 40px; }
    .rr-page .form-control,
    .rr-page .form-select {
        min-height: 44px;
        border-color: #d9e5dd;
        border-radius: 10px;
        color: #243b31;
        font-size: 13px;
        box-shadow: none;
    }

    .rr-page .form-control:focus,
    .rr-page .form-select:focus {
        border-color: #34ae72;
        box-shadow: 0 0 0 3px rgba(36, 184, 109, .12);
    }

    .rr-label {
        display: block;
        margin-bottom: 7px;
        color: #52685b;
        font-size: 12px;
        font-weight: 700;
    }

    .rr-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 43px;
        padding: 9px 16px;
        border: 1px solid transparent;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
    }

    .rr-btn:hover { transform: translateY(-1px); }
    .rr-btn-primary {
        color: #fff;
        background: linear-gradient(110deg, #168653, #25b86d);
        box-shadow: 0 6px 14px rgba(22, 134, 83, .17);
    }
    .rr-btn-primary:hover { color: #fff; box-shadow: 0 8px 18px rgba(22, 134, 83, .23); }
    .rr-btn-outline {
        color: var(--rr-green-dark);
        border-color: #9bd5b4;
        background: #fff;
    }
    .rr-btn-outline:hover { color: var(--rr-green-dark); background: #eefaf2; }
    .rr-btn-muted { color: #5d7065; border-color: #dce7df; background: #fff; }
    .rr-btn-muted:hover { color: #244834; background: #f6faf7; }

    .rr-filter-toggle {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 15px 19px;
        border: 0;
        background: #fff;
        color: #183a2b;
        text-align: left;
    }
    .rr-filter-toggle:hover { background: #f9fcfa; }
    .rr-filter-sub { color: #7b8d82; font-size: 12px; font-weight: 400; }

    .rr-print-card {
        position: relative;
        height: 100%;
        min-height: 136px;
        padding: 16px;
        border: 1px solid #dfeae2;
        border-radius: 14px;
        background: #fff;
        transition: border-color .18s ease, box-shadow .18s ease, transform .18s ease;
    }
    .rr-print-card:hover { transform: translateY(-2px); border-color: #a5d9b8; box-shadow: 0 8px 18px rgba(22, 70, 44, .07); }
    .rr-print-card.rr-print-card-active { border-color: #75c99a; background: linear-gradient(140deg, #f0fbf4, #fff); }
    .rr-print-top { display: flex; align-items: flex-start; gap: 12px; }
    .rr-print-icon {
        width: 43px;
        height: 43px;
        flex: 0 0 43px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 13px;
        color: #087b49;
        background: #dff6e8;
        font-size: 19px;
    }
    .rr-print-icon.rr-blue { color: #176ac4; background: #e8f2ff; }
    .rr-print-icon.rr-teal { color: #0b8879; background: #e0f7f3; }
    .rr-print-title { margin: 1px 0 5px; font-size: 14px; font-weight: 800; }
    .rr-print-desc { margin: 0 0 14px; color: #748478; font-size: 12px; line-height: 1.5; }
    .rr-print-check {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 22px;
        height: 22px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #1aa260;
        color: #fff;
        font-size: 12px;
    }

    .rr-result-count {
        padding: 5px 10px;
        border-radius: 30px;
        background: #e6f7ed;
        color: #11814c;
        font-size: 11px;
        font-weight: 800;
        white-space: nowrap;
    }

    .rr-table { margin: 0; font-size: 13px; }
    .rr-table thead th {
        padding: 12px 14px;
        border-bottom: 1px solid #dcece2;
        background: #eaf7ef;
        color: #087047;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .25px;
        white-space: nowrap;
    }
    .rr-table tbody td { padding: 12px 14px; border-color: #edf2ee; vertical-align: middle; }
    .rr-table tbody tr:hover { background: #fbfefc; }
    .rr-avatar {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 34px;
        border-radius: 50%;
        background: #e4f3e9;
        color: #13834e;
        font-weight: 800;
    }
    .rr-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 9px;
        border-radius: 30px;
        background: #e4f6ea;
        color: #117445;
        font-size: 11px;
        font-weight: 750;
    }
    .rr-empty { padding: 36px 18px !important; text-align: center; color: #788a7e; }
    .rr-empty i { display: block; margin-bottom: 8px; color: #74b991; font-size: 27px; }

    .rr-class-list { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 9px; max-height: 330px; overflow-y: auto; padding: 2px; }
    .rr-class-option { display: flex; align-items: center; gap: 10px; padding: 11px 12px; border: 1px solid #dfe9e2; border-radius: 10px; cursor: pointer; }
    .rr-class-option:hover { border-color: #9fd5b3; background: #f3fbf5; }
    .rr-class-option:has(input:checked) { border-color: #50b67b; background: #eaf8ef; }
    .rr-class-option .form-check-input { margin: 0; }
    .rr-class-option .rr-class-name { font-size: 13px; font-weight: 750; }
    .rr-class-option .rr-class-level { margin-left: auto; color: #7a8d80; font-size: 11px; }

    @media (max-width: 768px) {
        .rr-hero { padding: 16px; }
        .rr-panel-body { padding: 14px; }
        .rr-class-list { grid-template-columns: 1fr; }
        .rr-table { min-width: 760px; }
    }
</style>

@php
    $semesterSelection = request('semester', 'ganjil_genap');

    $selectedClass = $selectedClass
        ?? (is_numeric($selectedClassId ?? null)
            ? $classes->firstWhere('id', (int) $selectedClassId)
            : null);

    $classIsSelected = $selectedClass !== null;
    $hasSearch = trim((string) $search) !== '';
@endphp

<div class="container-fluid py-3 rr-page">
    <div class="rr-hero">
        <div class="d-flex align-items-center gap-3">
            <div class="rr-hero-icon"><i class="bi bi-file-earmark-text"></i></div>
            <div>
                <h4 class="fw-bold mb-1">Rekap Rapor</h4>
                <div class="text-muted small">Cari dan cetak rapor siswa.</div>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-3">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="GET" action="{{ route('rekap-rapot.index') }}" id="rekap-rapot-filter-form">
        {{-- PENCARIAN UTAMA --}}
        <section class="rr-panel">
            <div class="rr-panel-body">
                <label for="reportStudentSearch" class="rr-heading mb-3">
                    <i class="bi bi-search"></i> Cari Siswa
                </label>
                <div class="row g-2 align-items-center">
                    <div class="col-lg">
                    <div class="col-lg">
    <input
        type="search"
        id="reportStudentSearch"
        name="search"
        value="{{ $search }}"
        class="form-control"
        placeholder="Ketik nama, NIS, atau NISN..."
        autocomplete="off"
    >
</div>
                    </div>
                    <div class="col-lg-auto d-grid">
                        <button type="submit" class="rr-btn rr-btn-primary px-4">
                            <i class="bi bi-search"></i> Cari Siswa
                        </button>
                    </div>
                </div>
            </div>
        </section>

        {{-- FILTER OPSIONAL --}}
        <section class="rr-panel">
            <button
                class="rr-filter-toggle"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#rrOptionalFilters"
                aria-expanded="true"
                aria-controls="rrOptionalFilters"
            >
                <span class="d-flex align-items-center gap-2">
                    <i class="bi bi-funnel text-success"></i>
                    <span class="fw-bold">Filter Opsional</span>
                    <span class="rr-filter-sub d-none d-md-inline">— gunakan bila diperlukan</span>
                </span>
                <i class="bi bi-chevron-up"></i>
            </button>
            <div class="collapse show" id="rrOptionalFilters">
                <div class="rr-panel-body pt-1">
                    <div class="row g-3 align-items-end">
                        <div class="col-6 col-xl-3">
                            <label class="rr-label" for="rrAcademicYear">Tahun Ajaran</label>
                            <select name="academic_year_id" id="rekap-rapot-academic-year" class="form-select">
                                @foreach($academicYears as $year)
                                    <option value="{{ $year->id }}"
                                    data-status="{{ $year->status }}"
                                     @selected((string) $selectedAcademicYearId === (string) $year->id)>
                                        {{ $year->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-xl-3">
                            <label class="rr-label" for="rrSemester">Semester Cetak</label>
                            <select name="semester" id="rrSemester" class="form-select">
                                <option value="ganjil_genap" @selected($semesterSelection === 'ganjil_genap' || $semesterSelection === 'both')>Ganjil &amp; Genap</option>
                                <option value="ganjil" @selected($semesterSelection === 'ganjil')>Ganjil</option>
                                <option value="genap" @selected($semesterSelection === 'genap')>Genap</option>
                            </select>
                        </div>
                        <div class="col-6 col-xl-3">
                            <label class="rr-label" for="rrClassFilter">Kelas</label>
                            <select name="class_id" id="rekap-rapot-class" class="form-select">
                                <option value="">Pilih kelas (opsional)</option>
                                <option value="all" @selected($selectedClassId === 'all')>Semua Kelas</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" @selected((string) $selectedClassId === (string) $class->id)>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-xl-3">
                            <label class="rr-label" for="rrStatus">Status Siswa</label>
                            <select name="status" id="rekap-rapot-student-status" class="form-select">
                                <option value="Semua" @selected($selectedStatus === 'Semua')>Semua Status</option>
                                <option value="Aktif" @selected($selectedStatus === 'Aktif')>Aktif</option>
                                <option value="Tidak Aktif" @selected($selectedStatus === 'Tidak Aktif')>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('rekap-rapot.index') }}" class="rr-btn rr-btn-muted">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </a>
                        <button type="submit" class="rr-btn rr-btn-primary">
                            <i class="bi bi-funnel"></i> Terapkan Filter
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </form>

    {{-- JENIS CETAK --}}
    <section class="rr-panel">
        <div class="rr-panel-head">
            <h5 class="rr-heading"><i class="bi bi-printer"></i> Jenis Cetak</h5>
        </div>
        <div class="rr-panel-body">
            @php
                $commonParams = [
                    'academic_year_id' => $selectedAcademicYearId,
                    'semester' => $semesterSelection,
                ];
            @endphp
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="rr-print-card rr-print-card-active">
                        <span class="rr-print-check"><i class="bi bi-check-lg"></i></span>
                        <div class="rr-print-top">
                            <div class="rr-print-icon"><i class="bi bi-person-fill"></i></div>
                            <div>
                                <h6 class="rr-print-title">Per Individu</h6>
                                <p class="rr-print-desc">Cetak rapor satu siswa.</p>
                            </div>
                        </div>
                        <span class="rr-pill">
                            {{ match($semesterSelection) { 'ganjil' => 'Semester Ganjil', 'genap' => 'Semester Genap', default => 'Ganjil & Genap' } }}
                        </span>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="rr-print-card">
                        <div class="rr-print-top mb-2">
                            <div class="rr-print-icon rr-blue"><i class="bi bi-people-fill"></i></div>
                            <div>
                                <h6 class="rr-print-title">Per Kelas</h6>
                                <p class="rr-print-desc">
                                    @if($classIsSelected)
                                        Cetak seluruh rapor kelas {{ $selectedClass->name }}.
                                    @else
                                        Cetak semua siswa dalam satu kelas.
                                    @endif
                                </p>
                            </div>
                        </div>
                        @if($classIsSelected)
                            <a
                                class="rr-btn rr-btn-primary w-100"
                                target="_blank"
                                rel="noopener"
                                href="{{ route('rekap-rapot.batch', array_merge($commonParams, ['class_id' => $selectedClass->id, 'pdf' => 1])) }}"
                            >
                                <i class="bi bi-printer"></i> Cetak Rapor Kelas {{ $selectedClass->name }}
                            </a>
                        @else
                            <button type="button" class="rr-btn rr-btn-outline w-100" data-bs-toggle="modal" data-bs-target="#rrClassPickerModal">
                                <i class="bi bi-list-ul"></i> Pilih Kelas
                            </button>
                        @endif
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="rr-print-card">
                        <div class="rr-print-top mb-2">
                            <div class="rr-print-icon rr-teal"><i class="bi bi-files"></i></div>
                            <div>
                                <h6 class="rr-print-title">Semua Kelas</h6>
                                <p class="rr-print-desc">Cetak rapor seluruh kelas.</p>
                            </div>
                        </div>
                        <a
                            class="rr-btn rr-btn-outline w-100"
                            target="_blank"
                            rel="noopener"
                            href="{{ route('rekap-rapot.download-all', array_merge($commonParams, ['pdf' => 1])) }}"
                        >
                            <i class="bi bi-printer"></i> Cetak Semua Kelas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- HASIL PENCARIAN --}}
    <section class="rr-panel">
        <div class="rr-panel-head d-flex align-items-center justify-content-between gap-3">
            <h5 class="rr-heading"><i class="bi bi-list-check"></i> Hasil Pencarian</h5>
            <span class="rr-result-count">{{ $students->count() }} siswa</span>
        </div>
        <div class="table-responsive">
            <table class="table rr-table table-hover align-middle">
                <thead>
                    <tr>
                        <th style="width:55px">NO</th>
                        <th>NIS</th>
                        <th>NISN</th>
                        <th>NAMA SISWA</th>
                        <th>KELAS</th>
                        <th>STATUS</th>
                        <th class="text-end">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $index => $student)
                        @php
                            $studentClass = $student->classes()
                                ->where('classes.academic_year_id', $selectedAcademicYearId)
                                ->where('classes.status', 'Aktif')
                                ->when(
                                    is_numeric($selectedClassId),
                                    fn ($query) => $query->where('classes.id', (int) $selectedClassId)
                                )
                                ->first();

                            $assignmentStatus = $studentClass
                                ? $student->classAssignments->first(fn ($assignment) => (int) $assignment->class_id === (int) $studentClass->id)?->status
                                : null;
                        @endphp
                        <tr>
                            <td class="text-muted">{{ $index + 1 }}</td>
                            <td>{{ $student->nis ?? '-' }}</td>
                            <td>{{ $student->nisn ?? '-' }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="rr-avatar">{{ strtoupper(mb_substr($student->name ?? 'S', 0, 1)) }}</span>
                                    <span class="fw-bold">{{ $student->name }}</span>
                                </div>
                            </td>
                            <td>{{ $studentClass?->name ?? '-' }}</td>
                            <td>
                                <span class="rr-pill">{{ $assignmentStatus ?? '—' }}</span>
                            </td>
                            <td class="text-end">
                                @if($studentClass)
                                    <div class="dropdown">
                                        <button class="rr-btn rr-btn-outline dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bi bi-printer"></i> Cetak
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li>
                                                <a class="dropdown-item py-2" target="_blank" rel="noopener" href="{{ route('rekap-rapot.print', array_merge($commonParams, ['student' => $student->id, 'class_id' => $studentClass->id])) }}">
                                                    <i class="bi bi-eye me-2 text-success"></i>Preview Rapor
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item py-2" target="_blank" rel="noopener" href="{{ route('rekap-rapot.print', array_merge($commonParams, ['student' => $student->id, 'class_id' => $studentClass->id, 'pdf' => 1])) }}">
                                                    <i class="bi bi-file-earmark-pdf me-2 text-success"></i>Unduh PDF
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                @else
                                    <span class="text-muted small">Kelas tidak tersedia</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="rr-empty">
                                @if($hasSearch)
                                    <i class="bi bi-search"></i>
                                    Tidak ada siswa yang cocok dengan pencarian.
                                @elseif($classIsSelected || $selectedClassId === 'all' || (request()->has('status') && $selectedStatus !== 'Semua'))
                                    <i class="bi bi-people"></i>
                                    Tidak ada siswa untuk filter ini.
                                @else
                                    <i class="bi bi-person-search"></i>
                                    Ketik nama, NIS, atau NISN untuk mencari siswa.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->isNotEmpty())
            <div class="px-3 py-3 border-top small text-muted">
                Menampilkan {{ $students->count() }} siswa
            </div>
        @endif
    </section>
</div>

{{-- MODAL PILIH KELAS --}}
<div class="modal fade" id="rrClassPickerModal" tabindex="-1" aria-labelledby="rrClassPickerTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <form method="GET" action="{{ route('rekap-rapot.index') }}">
                <div class="modal-header border-bottom-0 pb-2">
                    <h5 class="modal-title fw-bold" id="rrClassPickerTitle">
                        <i class="bi bi-building text-success me-2"></i>Pilih Kelas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
                <div class="modal-body pt-2">
                    <input type="hidden" name="academic_year_id" value="{{ $selectedAcademicYearId }}">
                    <input type="hidden" name="semester" value="{{ $semesterSelection }}">
                    <input type="hidden" name="status" value="{{ $selectedStatus }}">
                    @if($search !== '')
                        <input type="hidden" name="search" value="{{ $search }}">
                    @endif
                    <div class="rr-input-icon mb-3">
                        <i class="bi bi-search"></i>
                        <input type="search" id="rrClassSearch" class="form-control" placeholder="Cari kelas..." autocomplete="off">
                    </div>
                    @if($classes->isNotEmpty())
                        <div class="rr-class-list" id="rrClassList">
                            @foreach($classes as $class)
                                <label class="rr-class-option" data-class-name="{{ strtolower($class->name) }}">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="class_id"
                                        value="{{ $class->id }}"
                                        @checked((string) $selectedClassId === (string) $class->id)
                                        required
                                    >
                                    <span class="rr-class-name">{{ $class->name }}</span>
                                    <span class="rr-class-level">Kelas {{ $class->grade_level }}</span>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">Belum ada kelas aktif untuk tahun ajaran ini.</p>
                    @endif
                </div>
                <div class="modal-footer border-top-0 pt-1">
                    <button type="button" class="rr-btn rr-btn-muted" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="rr-btn rr-btn-primary" @disabled($classes->isEmpty())>
                        <i class="bi bi-check2"></i> Pilih Kelas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('rrClassSearch');
        const classOptions = document.querySelectorAll('[data-class-name]');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const keyword = this.value.trim().toLowerCase();
                classOptions.forEach(function (option) {
                    const name = option.getAttribute('data-class-name') || '';
                    option.hidden = !name.includes(keyword);
                });
            });
        }

        const filterCollapse = document.getElementById('rrOptionalFilters');
        const filterToggle = document.querySelector('[data-bs-target="#rrOptionalFilters"]');
        if (filterCollapse && filterToggle) {
            filterCollapse.addEventListener('shown.bs.collapse', function () {
                const icon = filterToggle.querySelector('.bi-chevron-down, .bi-chevron-up');
                if (icon) { icon.classList.remove('bi-chevron-down'); icon.classList.add('bi-chevron-up'); }
            });
            filterCollapse.addEventListener('hidden.bs.collapse', function () {
                const icon = filterToggle.querySelector('.bi-chevron-down, .bi-chevron-up');
                if (icon) { icon.classList.remove('bi-chevron-up'); icon.classList.add('bi-chevron-down'); }
            });
        }
    });
</script>
@endsection
