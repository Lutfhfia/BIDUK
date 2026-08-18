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
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-people-fill text-success me-2"></i>Data Siswa
            </h4>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">
                Kelola data peserta didik SDN 204
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('students.create') }}" class="btn btn-biduk-primary">
                <i class="bi bi-plus-lg me-1"></i> Tambah Siswa
            </a>
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
                               placeholder="Cari Nama, NIS, atau NISN..."
                               value="{{ $search }}">
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
                                <th style="width: 80px;">Aksi</th>
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
                                                <img src="{{ asset('storage/' . $student->photo) }}"
                                                     alt="Foto" class="rounded-circle"
                                                     style="width: 32px; height: 32px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                                     style="width: 32px; height: 32px; background: {{ $student->gender === 'L' ? '#e3f2fd' : '#fce4ec' }};
                                                            color: {{ $student->gender === 'L' ? '#1565c0' : '#c62828' }}; font-size: 0.8rem; font-weight: 600;">
                                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <span class="fw-medium">{{ $student->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $student->gender === 'L' ? 'badge-l' : 'badge-p' }}" style="font-size: 0.75rem;">
                                            {{ $student->gender === 'L' ? 'L' : 'P' }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $currentClass = $student->classes->first();
                                        @endphp
                                        @if ($currentClass)
                                            <span class="badge bg-success bg-opacity-10 text-success" style="font-size: 0.75rem;">
                                                {{ $currentClass->name }}
                                            </span>
                                        @else
                                            <span class="text-muted" style="font-size: 0.8rem;">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($student->status) {
                                                'Aktif'    => 'badge-aktif',
                                                'Pindah'   => 'badge-pindah',
                                                'Lulus'    => 'badge-lulus',
                                                'Alumni'   => 'badge-alumni',
                                                'Nonaktif' => 'badge-nonaktif',
                                                default    => 'bg-secondary',
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}" style="font-size: 0.75rem;">
                                            {{ $student->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-dropdown dropdown">
                                            <button class="btn btn-sm btn-light border-0" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('students.show', $student) }}">
                                                        <i class="bi bi-eye text-primary"></i> Lihat Detail
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('students.edit', $student) }}">
                                                        <i class="bi bi-pencil-square text-warning"></i> Edit
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('students.destroy', $student) }}" method="POST"
                                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data siswa ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash3"></i> Hapus
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-3 py-3 border-top">
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
