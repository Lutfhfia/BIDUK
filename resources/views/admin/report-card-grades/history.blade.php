@extends('layouts.app')

@section('title', 'Riwayat Nilai Rapot — ' . $student->name)

@section('content')
    @php
        $canManageClassHistory = !auth()->user()?->isGuru()
            && (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('students.edit'));
    @endphp

    <div class="container-fluid py-4">

        {{-- =====================================================
            HEADER
        ====================================================== --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h4 class="fw-bold mb-1">

                    <i class="bi bi-clock-history me-2"></i>

                    Riwayat Nilai Rapot Siswa

                </h4>

                <p class="text-muted mb-0">

                    Histori nilai rapot peserta didik dari Kelas 1 sampai Kelas 6.

                </p>

            </div>

            <div>

                <a href="{{ $returnUrl ?? session('report_card_grades_index_url', route('report-card-grades.index')) }}" class="btn btn-light border">

                    <i class="bi bi-arrow-left me-1"></i>

                    Kembali ke Nilai Rapot

                </a>

            </div>

        </div>

        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Riwayat kelas belum dapat disimpan.</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif


        {{-- =====================================================
            INFORMASI SISWA
        ====================================================== --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <div class="row align-items-center g-3">

                    <div class="col-auto">

                        @if ($student->photo)
                            <img
                                src="{{ asset('storage/' . $student->photo) }}"
                                alt="{{ $student->name }}"
                                class="rounded-circle"
                                style="width: 64px; height: 64px; object-fit: cover;"
                            >
                        @else
                            <div
                                class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                style="width: 64px; height: 64px; background-color: #1a7a4c; font-size: 1.5rem;"
                            >
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>
                        @endif

                    </div>

                    <div class="col">

                        <h5 class="fw-bold mb-1">
                            {{ $student->name }}
                        </h5>

                        <div class="text-muted small d-flex flex-wrap gap-3">

                            <div>
                                <strong>NIS:</strong> {{ $student->nis ?? '-' }}
                            </div>

                            <div>
                                <strong>NISN:</strong> {{ $student->nisn ?? '-' }}
                            </div>

                            <div>
                                <strong>Jenis Kelamin:</strong>
                                {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </div>

                            <div>
                                <strong>Status:</strong>
                                <span class="badge bg-success-subtle text-success">
                                    {{ $student->status }}
                                </span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            RIWAYAT RAPOT KELAS 1 - 6
        ====================================================== --}}
        <h5 class="fw-bold mb-3">

            <i class="bi bi-journal-bookmark me-2"></i>

            Riwayat Rapot per Tingkatan Kelas

        </h5>

        <div class="row g-3">

            @for ($level = 1; $level <= 6; $level++)

                @php
                    $data = $gradeLevelsData[$level] ?? null;
                    $hasClass = $data['has_class'] ?? false;
                    $class = $data['class'] ?? null;
                    $ay = $data['academic_year'] ?? null;
                    $historyEntry = $data['history_entry'] ?? null;
                    $manualHistory = $data['manual_history'] ?? null;
                    $hasData = $data['has_data'] ?? false;
                    $ganjilCount = $data['ganjil_count'] ?? 0;
                    $genapCount = $data['genap_count'] ?? 0;
                    $historyClasses = $data['history_classes'] ?? collect();
                    $historyAcademicYears = $academicYears;
                    $manualAcademicYear = $manualHistory
                        ? $academicYears->firstWhere('name', trim($manualHistory->academic_year))
                        : null;
                    $selectedHistoryClass = $historyClasses->first(function ($historyClass) use ($manualHistory, $class) {
                        if ($manualHistory) {
                            return strcasecmp(trim($manualHistory->rombel), trim($historyClass->name)) === 0
                                && trim($manualHistory->academic_year) === trim($historyClass->academicYear->name);
                        }

                        return $class && $class->id === $historyClass->id;
                    });
                    $selectedAcademicYearId = old('grade_level') == $level
                        ? old('academic_year_id')
                        : ($selectedHistoryClass?->academic_year_id ?? $manualAcademicYear?->id);
                    $selectedClassId = old('grade_level') == $level
                        ? old('class_id')
                        : $selectedHistoryClass?->id;
                    $masterMatchesHistory = !$manualHistory
                        || ($class
                            && $ay
                            && strcasecmp(trim($manualHistory->rombel), trim($class->name)) === 0
                            && trim($manualHistory->academic_year) === trim($ay->name));
                @endphp

                <div class="col-md-6 col-lg-4">

                    <div class="card border-0 shadow-sm h-100 {{ $hasData ? 'border-start border-success border-4' : '' }}">

                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">

                            <h6 class="fw-bold mb-0">

                                <i class="bi bi-bookmark-star me-2 text-success"></i>

                                Rapot Kelas {{ $level }}

                            </h6>

                            @if ($manualHistory && !$class)
                                <span class="badge bg-warning-subtle text-warning">
                                    Riwayat Perlu Dicocokkan
                                </span>
                            @elseif ($hasData)
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bi bi-check-circle me-1"></i> Nilai Tersedia
                                </span>
                            @elseif ($manualHistory)
                                <span class="badge bg-primary-subtle text-primary">
                                    Nilai Belum Diisi
                                </span>
                            @elseif (!$hasClass)
                                <span class="badge bg-light text-muted">
                                    Belum Ada
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning">
                                    <i class="bi bi-clock me-1"></i> Belum Diisi
                                </span>
                            @endif

                        </div>

                        <div class="card-body">

                            @if ($hasClass)

                                <div class="mb-3">

                                    <div class="text-muted small">
                                        Rombel
                                    </div>

                                    <div class="fw-semibold">
                                        {{ $manualHistory?->rombel ?? $class?->name ?? '-' }}
                                    </div>

                                </div>

                                <div class="mb-3">

                                    <div class="text-muted small">
                                        Tahun Ajaran
                                    </div>

                                    <div class="fw-semibold">
                                        {{ $manualHistory?->academic_year ?? $ay?->name ?? 'Riwayat belum dilengkapi' }}
                                    </div>

                                </div>

                                @if ($manualHistory || $historyEntry)
                                    <div class="mb-3">
                                        <div class="text-muted small">
                                            Status Riwayat
                                        </div>
                                        <div class="fw-semibold small">
                                            {{ $manualHistory?->final_status ?? $historyEntry?->final_status ?? '-' }}
                                        </div>
                                    </div>
                                @endif

                                <div class="row g-2 mb-3">

                                    {{-- Ganjil --}}
                                    <div class="col-6">

                                        <div class="p-2 border rounded text-center bg-light">

                                            <div class="small fw-semibold text-muted mb-1">
                                                Ganjil
                                            </div>

                                            @if (!$class)
                                                <span class="badge bg-secondary-subtle text-secondary">Tidak terhubung</span>
                                            @elseif ($data['ganjil_is_complete'] ?? false)
                                                <span class="badge bg-success-subtle text-success">
                                                    <i class="bi bi-check me-1"></i> Sudah Diisi
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    Belum Diisi
                                                </span>
                                            @endif

                                        </div>

                                    </div>

                                    {{-- Genap --}}
                                    <div class="col-6">

                                        <div class="p-2 border rounded text-center bg-light">

                                            <div class="small fw-semibold text-muted mb-1">
                                                Genap
                                            </div>

                                            @if (!$class)
                                                <span class="badge bg-secondary-subtle text-secondary">Tidak terhubung</span>
                                            @elseif ($data['genap_is_complete'] ?? false)
                                                <span class="badge bg-success-subtle text-success">
                                                    <i class="bi bi-check me-1"></i> Sudah Diisi
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    Belum Diisi
                                                </span>
                                            @endif

                                        </div>

                                    </div>

                                </div>
                                @if (!$class)
                                    <p class="small text-muted mb-3">
                                        Riwayat historis tersimpan terpisah dan tidak otomatis membuat atau mengisi nilai rapor.
                                    </p>
                                @endif

                                <div class="mt-auto">

                                    @if ($class && $masterMatchesHistory)
                                        @if ($data['has_subjects'] ?? false)
                                            <div class="d-flex gap-2 mb-2">

                                                @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('grades.edit'))
                                                <a
                                                    href="{{ route('report-card-grades.edit', [
                                                        'class' => $class->id,
                                                        'student' => $student->id,
                                                        'return_url' => request()->fullUrl(),
                                                    ]) }}"
                                                    class="btn btn-sm btn-success flex-fill"
                                                >
                                                    <i class="bi bi-pencil-square me-1"></i>
                                                    {{ $hasData ? 'Lihat & Edit' : 'Input Rapot' }}
                                                </a>
                                                @endif

                                                @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('grades.view'))
                                                <a
                                                    href="{{ route('report-card-grades.print', [
                                                        'class' => $class->id,
                                                        'student' => $student->id,
                                                        'pdf' => true,
                                                    ]) }}"
                                                    class="btn btn-sm btn-outline-danger flex-fill"
                                                    target="_blank"
                                                >
                                                    <i class="bi bi-printer me-1"></i>
                                                    Cetak Rapot
                                                </a>
                                                @endif

                                            </div>
                                        @else
                                            <div class="alert alert-warning small py-2">
                                                Kelas ini sudah tersedia, tetapi mata pelajarannya belum disetel.
                                            </div>
                                            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('classes.edit'))
                                                <a href="{{ route('classes.edit', $class) }}" class="btn btn-sm btn-warning w-100 mb-2">
                                                    <i class="bi bi-gear me-1"></i>
                                                    Seting Mata Pelajaran Kelas
                                                </a>
                                            @else
                                                <div class="small text-muted text-center mb-2">
                                                    Hubungi admin yang memiliki izin edit kelas untuk menyetel mata pelajaran.
                                                </div>
                                            @endif
                                        @endif
                                    @elseif ($manualHistory && !$class)
                                        <div class="small text-muted text-center mb-2">
                                            Riwayat tersimpan belum cocok dengan kelas master. Pilih data master yang benar atau hubungi admin sekolah.
                                        </div>
                                    @endif

                                    @if (!$historyEntry || $historyEntry->status !== 'Aktif')
                                        @if ($canManageClassHistory)
                                            @if ($historyAcademicYears->isNotEmpty() || auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('academic_years.create'))
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary w-100"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#history-modal-{{ $level }}">
                                                    <i class="bi bi-journal-text me-1"></i>
                                                    {{ $manualHistory || $historyEntry ? 'Lihat & Edit Riwayat' : 'Lengkapi Riwayat' }}
                                                </button>
                                            @else
                                                <div class="small text-muted text-center">
                                                    Kelas master untuk tingkatan ini belum tersedia. Hubungi admin sekolah.
                                                </div>
                                            @endif
                                        @else
                                            <div class="small text-muted text-center">
                                                Riwayat kelas dikelola oleh Admin sekolah.
                                            </div>
                                        @endif
                                    @else
                                        <div class="small text-muted text-center">
                                            Penempatan kelas aktif dikelola melalui data kelas siswa.
                                        </div>
                                    @endif

                                </div>

                                @if ($canManageClassHistory)
                                <div class="modal fade" id="history-modal-{{ $level }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('report-card-grades.history.store', ['student' => $student->id]) }}">
                                                @csrf
                                                <input type="hidden" name="grade_level" value="{{ $level }}">
                                                @if ($manualHistory)
                                                    <input type="hidden" name="history_id" value="{{ $manualHistory->id }}">
                                                @endif

                                                <div class="modal-header">
                                                    <h5 class="modal-title">
                                                        {{ $manualHistory || $historyEntry ? 'Edit Riwayat Kelas' : 'Lengkapi Riwayat Kelas' }} {{ $level }}
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Tahun Ajaran</label>
                                                            <select
                                                                id="history-academic-year-{{ $level }}"
                                                                name="academic_year_id"
                                                                class="form-select"
                                                                required
                                                            >
                                                                <option value="">Pilih tahun ajaran</option>
                                                                @foreach ($historyAcademicYears as $historyAcademicYear)
                                                                    <option
                                                                        value="{{ $historyAcademicYear->id }}"
                                                                        @selected((string) $selectedAcademicYearId === (string) $historyAcademicYear->id)
                                                                    >
                                                                        {{ $historyAcademicYear->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('academic_year_id')
                                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                                            @enderror
                                                            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('academic_years.create'))
                                                                <a
                                                                    href="{{ route('academic-years.create', ['history_student_id' => $student->id]) }}"
                                                                    class="btn btn-sm btn-outline-secondary mt-2"
                                                                >
                                                                    <i class="bi bi-plus-circle me-1"></i>
                                                                    Tambah Tahun Ajaran
                                                                </a>
                                                            @endif
                                                            @if ($currentGradeLevel && $level < $currentGradeLevel)
                                                                <div class="form-text text-warning mt-2">
                                                                    <i class="bi bi-info-circle me-1"></i>
                                                                    Isi tahun ajaran sesuai dokumen sekolah dan harus lebih lama dari tahun ajaran kelas aktif.
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">Rombel</label>
                                                            <select
                                                                id="history-class-{{ $level }}"
                                                                name="class_id"
                                                                class="form-select"
                                                                data-academic-year-select="history-academic-year-{{ $level }}"
                                                                required
                                                            >
                                                                <option value="">Pilih rombel</option>
                                                                @foreach ($historyClasses as $historyClass)
                                                                    <option
                                                                        value="{{ $historyClass->id }}"
                                                                        data-academic-year-id="{{ $historyClass->academic_year_id }}"
                                                                        @selected((string) $selectedClassId === (string) $historyClass->id)
                                                                    >
                                                                        {{ $historyClass->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('class_id')
                                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                                            @enderror
                                                            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('classes.create'))
                                                                <a
                                                                    href="{{ route('classes.create', ['grade_level' => $level, 'academic_year_id' => $selectedAcademicYearId, 'history_student_id' => $student->id]) }}"
                                                                    class="btn btn-sm btn-outline-secondary mt-2"
                                                                    data-history-class-create
                                                                    data-year-select="history-academic-year-{{ $level }}"
                                                                >
                                                                    <i class="bi bi-plus-circle me-1"></i>
                                                                    Tambah Rombel Kelas {{ $level }}
                                                                </a>
                                                            @endif
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">Status akhir tahun</label>
                                                            <select name="final_status" class="form-select" required>
                                                                <option value="">Pilih status</option>
                                                                <option value="Naik Kelas" @selected((old('grade_level') == $level ? old('final_status') : ($manualHistory?->final_status ?? $historyEntry?->final_status ?? '')) === 'Naik Kelas')>Naik Kelas</option>
                                                                <option value="Tinggal Kelas" @selected((old('grade_level') == $level ? old('final_status') : ($manualHistory?->final_status ?? $historyEntry?->final_status ?? '')) === 'Tinggal Kelas')>Tinggal Kelas</option>
                                                                <option value="Belum Diketahui" @selected((old('grade_level') == $level ? old('final_status') : ($manualHistory?->final_status ?? $historyEntry?->final_status ?? '')) === 'Belum Diketahui')>Belum Diketahui</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">Tingkatan kelas</label>
                                                            <input type="text" class="form-control" value="Kelas {{ $level }}" readonly>
                                                        </div>

                                                        <div class="col-12">
                                                            <label class="form-label">Catatan</label>
                                                            <textarea name="notes" rows="3" class="form-control" placeholder="Opsional: catatan verifikasi sekolah, pindahan, atau keterangannya">{{ old('grade_level') == $level ? old('notes', $manualHistory?->notes) : ($manualHistory?->notes ?? $historyEntry?->notes ?? '') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Riwayat</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif

                            @else

                                <div class="text-center py-4 text-muted mb-3">

                                    <i class="bi bi-folder-x fs-2 text-muted mb-2 d-block"></i>

                                    <div class="small fw-medium">
                                        Riwayat kelas belum dilengkapi.
                                    </div>

                                    <div class="small text-muted">
                                        Data penempatan untuk Kelas {{ $level }} belum tersedia atau belum diverifikasi.
                                    </div>

                                </div>

                                @if ($canManageClassHistory)
                                    @if ($historyAcademicYears->isNotEmpty() || auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('academic_years.create'))
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary w-100"
                                                data-bs-toggle="modal"
                                                data-bs-target="#history-modal-{{ $level }}">
                                            <i class="bi bi-plus-circle me-1"></i>
                                            Lengkapi Riwayat
                                        </button>
                                    @else
                                        <div class="small text-muted text-center">
                                            Kelas master untuk tingkatan ini belum tersedia. Hubungi admin sekolah.
                                        </div>
                                    @endif
                                @else
                                    <div class="small text-muted text-center">
                                        Riwayat kelas dikelola oleh Admin sekolah.
                                    </div>
                                @endif

                                @if ($canManageClassHistory)
                                <div class="modal fade" id="history-modal-{{ $level }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('report-card-grades.history.store', ['student' => $student->id]) }}">
                                                @csrf
                                                <input type="hidden" name="grade_level" value="{{ $level }}">

                                                <div class="modal-header">
                                                    <h5 class="modal-title">Lengkapi Riwayat Kelas {{ $level }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>

                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Tahun Ajaran</label>
                                                            <select
                                                                id="history-academic-year-{{ $level }}"
                                                                name="academic_year_id"
                                                                class="form-select"
                                                                required
                                                            >
                                                                <option value="">Pilih tahun ajaran</option>
                                                                @foreach ($historyAcademicYears as $historyAcademicYear)
                                                                    <option
                                                                        value="{{ $historyAcademicYear->id }}"
                                                                        @selected((string) $selectedAcademicYearId === (string) $historyAcademicYear->id)
                                                                    >
                                                                        {{ $historyAcademicYear->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('academic_year_id')
                                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                                            @enderror
                                                            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('academic_years.create'))
                                                                <a
                                                                    href="{{ route('academic-years.create', ['history_student_id' => $student->id]) }}"
                                                                    class="btn btn-sm btn-outline-secondary mt-2"
                                                                >
                                                                    <i class="bi bi-plus-circle me-1"></i>
                                                                    Tambah Tahun Ajaran
                                                                </a>
                                                            @endif
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">Rombel</label>
                                                            <select
                                                                id="history-class-{{ $level }}"
                                                                name="class_id"
                                                                class="form-select"
                                                                data-academic-year-select="history-academic-year-{{ $level }}"
                                                                required
                                                            >
                                                                <option value="">Pilih rombel</option>
                                                                @foreach ($historyClasses as $historyClass)
                                                                    <option
                                                                        value="{{ $historyClass->id }}"
                                                                        data-academic-year-id="{{ $historyClass->academic_year_id }}"
                                                                        @selected((string) $selectedClassId === (string) $historyClass->id)
                                                                    >
                                                                        {{ $historyClass->name }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('class_id')
                                                                <div class="text-danger small mt-2">{{ $message }}</div>
                                                            @enderror
                                                            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('classes.create'))
                                                                <a
                                                                    href="{{ route('classes.create', ['grade_level' => $level, 'academic_year_id' => $selectedAcademicYearId, 'history_student_id' => $student->id]) }}"
                                                                    class="btn btn-sm btn-outline-secondary mt-2"
                                                                    data-history-class-create
                                                                    data-year-select="history-academic-year-{{ $level }}"
                                                                >
                                                                    <i class="bi bi-plus-circle me-1"></i>
                                                                    Tambah Rombel Kelas {{ $level }}
                                                                </a>
                                                            @endif
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">Status akhir tahun</label>
                                                            <select name="final_status" class="form-select" required>
                                                                <option value="">Pilih status</option>
                                                                <option value="Naik Kelas" @selected(old('grade_level') == $level && old('final_status') === 'Naik Kelas')>Naik Kelas</option>
                                                                <option value="Tinggal Kelas" @selected(old('grade_level') == $level && old('final_status') === 'Tinggal Kelas')>Tinggal Kelas</option>
                                                                <option value="Belum Diketahui" @selected(old('grade_level') == $level && old('final_status') === 'Belum Diketahui')>Belum Diketahui</option>
                                                            </select>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">Tingkatan kelas</label>
                                                            <input type="text" class="form-control" value="Kelas {{ $level }}" readonly>
                                                        </div>

                                                        <div class="col-12">
                                                            <label class="form-label">Catatan</label>
                                                            <textarea name="notes" rows="3" class="form-control" placeholder="Opsional: catatan verifikasi sekolah atau keterangan tambahan">{{ old('grade_level') == $level ? old('notes') : '' }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Riwayat</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif

                            @endif

                        </div>

                    </div>

                </div>

            @endfor

        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('[data-academic-year-select]').forEach(function (classSelect) {
                const yearSelect = document.getElementById(classSelect.dataset.academicYearSelect);
                if (!yearSelect) {
                    return;
                }

                function filterClasses() {
                    Array.from(classSelect.options).slice(1).forEach(function (option) {
                        const matchesYear = option.dataset.academicYearId === yearSelect.value;
                        option.hidden = !matchesYear;
                        option.disabled = !matchesYear;
                    });

                    const selectedOption = classSelect.selectedOptions[0];
                    if (selectedOption && selectedOption.value
                        && selectedOption.dataset.academicYearId !== yearSelect.value) {
                        classSelect.value = '';
                    }
                }

                yearSelect.addEventListener('change', filterClasses);
                filterClasses();
            });

            document.querySelectorAll('[data-history-class-create]').forEach(function (createLink) {
                const yearSelect = document.getElementById(createLink.dataset.yearSelect);
                if (!yearSelect) {
                    return;
                }

                function updateCreateLink() {
                    const createUrl = new URL(createLink.href);
                    if (yearSelect.value) {
                        createUrl.searchParams.set('academic_year_id', yearSelect.value);
                    } else {
                        createUrl.searchParams.delete('academic_year_id');
                    }

                    createLink.href = createUrl.toString();
                }

                yearSelect.addEventListener('change', updateCreateLink);
                updateCreateLink();
            });

            const failedLevel = @json(old('grade_level'));
            const failedModal = failedLevel
                ? document.getElementById('history-modal-' + failedLevel)
                : null;

            if (failedModal && window.bootstrap && window.bootstrap.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(failedModal).show();
            }

        });
    </script>

@endsection
