@extends('layouts.app')

@section('title', 'Data Siswa')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item">Master Data</li>
    <li class="breadcrumb-item active">Data Siswa</li>
@endsection

@section('content')
    <div class="fade-in-up">
        {{-- Page Header --}}
        <div
            class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold text-dark mb-1">
                    <i class="bi bi-people-fill text-success me-2"></i>Data Siswa
                </h4>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                    Kelola data peserta didik SDN 204
                </p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('students.import.form') }}" class="btn btn-outline-success">
                    <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Excel
                </a>
                <a href="{{ route('students.export.excel') }}" class="btn btn-outline-primary">
                    <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                </a>
                <a href="{{ route('students.create') }}" class="btn btn-biduk-primary">
                    <i class="bi bi-plus-lg me-1"></i> Tambah Siswa
                </a>
            </div>
        </div>

        {{-- Import Error Details (if any) --}}
        @if (session('import_errors') && count(session('import_errors')) > 0)
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <div class="fw-semibold mb-2">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    Catatan Import Siswa:
                </div>
                <ul class="mb-0 small">
                    @foreach (session('import_errors') as $err)
                        <li>Baris {{ $err['row'] ?? '?' }} ({{ $err['name'] ?? '-' }}): {{ $err['message'] }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Stat Cards --}}
        <div class="row g-3 mb-4">
            <div class="col-6 col-lg-3">
                <div class="biduk-card p-3 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-success-subtle text-success"
                        style="width: 48px; height: 48px; font-size: 1.4rem;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Total Siswa</div>
                        <h5 class="fw-bold mb-0 text-dark">{{ number_format($stats['total'] ?? 0) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="biduk-card p-3 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary"
                        style="width: 48px; height: 48px; font-size: 1.4rem;">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Siswa Aktif</div>
                        <h5 class="fw-bold mb-0 text-dark">{{ number_format($stats['active'] ?? 0) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="biduk-card p-3 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-info-subtle text-info"
                        style="width: 48px; height: 48px; font-size: 1.4rem;">
                        <i class="bi bi-gender-male"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Laki-laki</div>
                        <h5 class="fw-bold mb-0 text-dark">{{ number_format($stats['male'] ?? 0) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="biduk-card p-3 d-flex align-items-center gap-3">
                    <div class="d-flex align-items-center justify-content-center rounded-3 bg-danger-subtle text-danger"
                        style="width: 48px; height: 48px; font-size: 1.4rem;">
                        <i class="bi bi-gender-female"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-medium">Perempuan</div>
                        <h5 class="fw-bold mb-0 text-dark">{{ number_format($stats['female'] ?? 0) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search & Filter --}}
        <div class="biduk-card mb-4">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('students.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-12 col-md-4">
                            <label class="form-label">
                                <i class="bi bi-search me-1"></i> Pencarian
                            </label>
                            <input type="text" name="search" class="form-control"
                                placeholder="Cari Nama, NIS, atau NISN..." value="{{ $search }}">
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">
                                <i class="bi bi-funnel me-1"></i> Kelas
                            </label>
                            <select name="class_id" class="form-select">
                                <option value="">Semua Kelas</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 col-md-3">
                            <label class="form-label">
                                <i class="bi bi-filter me-1"></i> Status
                            </label>
                            <select name="status" class="form-select">
                                <option value="">Semua Status</option>
                                @foreach ($statuses as $s)
                                    <option value="{{ $s }}" {{ $status == $s ? 'selected' : '' }}>
                                        {{ $s }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-2 d-flex gap-2">
                            <button type="submit" class="btn btn-biduk-primary w-100">
                                <i class="bi bi-search"></i> Filter
                            </button>
                            @if ($search || $classId || $status)
                                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary" title="Reset">
                                    <i class="bi bi-x-lg"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Student Table --}}
        <div class="biduk-card">
            <div class="card-body p-0">
                @if ($students->count() > 0)
                    <div class="table-responsive">
                        <table class="table biduk-table mb-0">
                            <thead>
                                <tr>
                                    <th style="width: 50px;">No</th>
                                    <th>NIS</th>
                                    <th>NISN</th>
                                    <th>Nama Peserta Didik</th>
                                    <th>JK</th>
                                    <th>Kelas</th>
                                    <th>Status</th>
                                    <th style="width: 140px;" class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($students as $index => $student)
                                    <tr>
                                        <td class="text-muted">
                                            {{ $students->firstItem() + $index }}
                                        </td>
                                        <td>
                                            <span class="fw-medium">{{ $student->nis }}</span>
                                        </td>
                                        <td>{{ $student->nisn }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if ($student->photo)
                                                    <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto"
                                                        class="rounded-circle"
                                                        style="width: 32px; height: 32px; object-fit: cover;">
                                                @else
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 32px; height: 32px; background: {{ $student->gender === 'L' ? '#e3f2fd' : '#fce4ec' }};
                                                            color: {{ $student->gender === 'L' ? '#1565c0' : '#c62828' }}; font-size: 0.8rem; font-weight: 600;">
                                                        {{ strtoupper(substr($student->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <a href="{{ route('students.show', $student->id) }}"
                                                    class="fw-medium text-dark text-decoration-none hover-primary">
                                                    {{ $student->name }}
                                                </a>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge {{ $student->gender === 'L' ? 'badge-l' : 'badge-p' }}"
                                                style="font-size: 0.75rem;">
                                                {{ $student->gender === 'L' ? 'L' : 'P' }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $currentClass = $student->classes->first();
                                            @endphp
                                            @if ($currentClass)
                                                <span class="badge bg-success bg-opacity-10 text-success"
                                                    style="font-size: 0.75rem;">
                                                    {{ $currentClass->name }}
                                                </span>
                                            @else
                                                <span class="text-muted" style="font-size: 0.8rem;">—</span>
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
                                            <span class="badge {{ $badgeClass }}" style="font-size: 0.75rem;">
                                                {{ $student->status }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center align-items-center gap-1">
                                                <a href="{{ route('students.show', $student->id) }}"
                                                    class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('students.edit', $student->id) }}"
                                                    class="btn btn-sm btn-outline-warning" title="Edit Data">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('students.destroy', $student->id) }}"
                                                    method="POST" class="d-inline"
                                                    onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                                        title="Hapus Data">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    <div
                        class="d-flex flex-column flex-md-row justify-content-between align-items-center px-3 py-3 border-top">
                        <div class="text-muted mb-2 mb-md-0" style="font-size: 0.85rem;">
                            Menampilkan {{ $students->firstItem() }}–{{ $students->lastItem() }}
                            dari {{ $students->total() }} data
                        </div>
                        {{ $students->links('pagination::bootstrap-5') }}
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-people"></i>
                        <h5>Belum Ada Data Siswa</h5>
                        <p class="text-muted">
                            @if ($search || $classId || $status)
                                Tidak ada data yang cocok dengan filter pencarian.
                            @else
                                Mulai dengan menambahkan data siswa baru.
                            @endif
                        </p>
                        @if (!$search && !$classId && !$status)
                            <a href="{{ route('students.create') }}" class="btn btn-biduk-primary">
                                <i class="bi bi-plus-lg me-1"></i> Tambah Siswa Pertama
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
