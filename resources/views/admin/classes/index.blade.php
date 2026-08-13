@extends('layouts.app')

@section('title', 'Data Kelas')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ url('/') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">Master Data</li>
    <li class="breadcrumb-item active">Data Kelas</li>
@endsection

@section('content')
<div class="fade-in-up">

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-building text-success me-2"></i>
                Data Kelas
            </h4>

            <p class="text-muted mb-0" style="font-size: 0.85rem;">
                Kelola data kelas dan wali kelas SDN 204
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('classes.create') }}" class="btn btn-biduk-primary">
                <i class="bi bi-plus-lg me-1"></i>
                Tambah Kelas
            </a>
        </div>
    </div>


    {{-- Search & Filter --}}
    <div class="biduk-card mb-4">
        <div class="card-body p-3">

            <form method="GET" action="{{ route('classes.index') }}">

                <div class="row g-3 align-items-end">

                    {{-- Pencarian --}}
                    <div class="col-12 col-md-4">
                        <label class="form-label">
                            <i class="bi bi-search me-1"></i>
                            Pencarian
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Cari nama kelas..."
                            value="{{ request('search') }}"
                        >
                    </div>


                    {{-- Tahun Ajaran --}}
                    <div class="col-6 col-md-3">
                        <label class="form-label">
                            <i class="bi bi-calendar3 me-1"></i>
                            Tahun Ajaran
                        </label>

                        <select name="academic_year_id" class="form-select">
                            <option value="">Semua Tahun Ajaran</option>

                            @foreach ($academicYears as $academicYear)
                                <option
                                    value="{{ $academicYear->id }}"
                                    {{ request('academic_year_id') == $academicYear->id ? 'selected' : '' }}
                                >
                                    {{ $academicYear->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Tingkat --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label">
                            <i class="bi bi-layers me-1"></i>
                            Tingkat
                        </label>

                        <select name="grade_level" class="form-select">
                            <option value="">Semua</option>

                            @for ($i = 1; $i <= 6; $i++)
                                <option
                                    value="{{ $i }}"
                                    {{ request('grade_level') == $i ? 'selected' : '' }}
                                >
                                    Kelas {{ $i }}
                                </option>
                            @endfor
                        </select>
                    </div>


                    {{-- Status --}}
                    <div class="col-6 col-md-2">
                        <label class="form-label">
                            <i class="bi bi-filter me-1"></i>
                            Status
                        </label>

                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>

                            <option
                                value="Aktif"
                                {{ request('status') === 'Aktif' ? 'selected' : '' }}
                            >
                                Aktif
                            </option>

                            <option
                                value="Nonaktif"
                                {{ request('status') === 'Nonaktif' ? 'selected' : '' }}
                            >
                                Nonaktif
                            </option>
                        </select>
                    </div>


                    {{-- Tombol --}}
                    <div class="col-6 col-md-1 d-flex gap-2">

                        <button
                            type="submit"
                            class="btn btn-biduk-primary w-100"
                            title="Filter"
                        >
                            <i class="bi bi-search"></i>
                        </button>

                        @if(
                            request('search') ||
                            request('academic_year_id') ||
                            request('grade_level') ||
                            request('status')
                        )
                            <a
                                href="{{ route('classes.index') }}"
                                class="btn btn-outline-secondary"
                                title="Reset"
                            >
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif

                    </div>

                </div>

            </form>

        </div>
    </div>


    {{-- Table --}}
    <div class="biduk-card">

        <div class="card-body p-0">

            @if ($classes->count() > 0)

                <div class="table-responsive">

                    <table class="table biduk-table mb-0">

                        <thead>
                            <tr>

                                <th style="width: 50px;">
                                    No
                                </th>

                                <th>
                                    Kelas
                                </th>

                                <th>
                                    Tingkat
                                </th>

                                <th>
                                    Tahun Ajaran
                                </th>

                                <th>
                                    Wali Kelas
                                </th>

                                <th>
                                    Siswa
                                </th>

                                <th>
                                    Status
                                </th>

                                <th style="width: 80px;">
                                    Aksi
                                </th>

                            </tr>
                        </thead>


                        <tbody>

                            @foreach ($classes as $index => $class)

                                <tr>

                                    {{-- No --}}
                                    <td class="text-muted">
                                        {{ $classes->firstItem() + $index }}
                                    </td>


                                    {{-- Nama Kelas --}}
                                    <td>
                                        <span class="fw-semibold">
                                            {{ $class->name }}
                                        </span>
                                    </td>


                                    {{-- Tingkat --}}
                                    <td>
                                        <span
                                            class="badge bg-success bg-opacity-10 text-success"
                                            style="font-size: 0.75rem;"
                                        >
                                            Kelas {{ $class->grade_level }}
                                        </span>
                                    </td>


                                    {{-- Tahun Ajaran --}}
                                    <td>
                                        {{ $class->academicYear?->name ?? '—' }}
                                    </td>


                                    {{-- Wali Kelas --}}
                                    <td>

                                        @if ($class->homeroomTeacher)

                                            <div class="d-flex align-items-center gap-2">

                                                <div
                                                    class="rounded-circle d-flex align-items-center justify-content-center"
                                                    style="
                                                        width: 32px;
                                                        height: 32px;
                                                        background: #e8f5e9;
                                                        color: #2e7d32;
                                                        font-size: 0.8rem;
                                                        font-weight: 600;
                                                    "
                                                >
                                                    {{ strtoupper(substr($class->homeroomTeacher->name, 0, 1)) }}
                                                </div>

                                                <span>
                                                    {{ $class->homeroomTeacher->name }}
                                                </span>

                                            </div>

                                        @else

                                            <span
                                                class="text-muted"
                                                style="font-size: 0.8rem;"
                                            >
                                                Belum ditentukan
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Jumlah Siswa --}}
                                    <td>

                                        <span class="fw-medium">
                                            {{ $class->active_students_count }}
                                        </span>

                                        <span class="text-muted">
                                            / {{ $class->capacity }}
                                        </span>

                                    </td>


                                    {{-- Status --}}
                                    <td>

                                        @if ($class->status === 'Aktif')

                                            <span
                                                class="badge badge-aktif"
                                                style="font-size: 0.75rem;"
                                            >
                                                Aktif
                                            </span>

                                        @else

                                            <span
                                                class="badge badge-nonaktif"
                                                style="font-size: 0.75rem;"
                                            >
                                                Nonaktif
                                            </span>

                                        @endif

                                    </td>


                                    {{-- Aksi --}}
                                    <td>

                                        <div class="action-dropdown dropdown">

                                            <button
                                                class="btn btn-sm btn-light border-0"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                            >
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>


                                            <ul class="dropdown-menu dropdown-menu-end">

                                                {{-- Detail --}}
                                                <li>
                                                    <a
                                                        class="dropdown-item"
                                                        href="{{ route('classes.show', $class) }}"
                                                    >
                                                        <i class="bi bi-eye text-primary me-2"></i>
                                                        Lihat Detail
                                                    </a>
                                                </li>


                                                {{-- Kelola Siswa --}}
                                                <li>
                                                    <a
                                                        class="dropdown-item"
                                                        href="{{ route('classes.students', $class) }}"
                                                    >
                                                        <i class="bi bi-people text-success me-2"></i>
                                                        Kelola Siswa
                                                    </a>
                                                </li>


                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>


                                                {{-- Edit --}}
                                                <li>
                                                    <a
                                                        class="dropdown-item"
                                                        href="{{ route('classes.edit', $class) }}"
                                                    >
                                                        <i class="bi bi-pencil-square text-warning me-2"></i>
                                                        Edit
                                                    </a>
                                                </li>


                                                {{-- Hapus --}}
                                                <li>

                                                    <form
                                                        action="{{ route('classes.destroy', $class) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus kelas ini?')"
                                                    >

                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="dropdown-item text-danger"
                                                        >
                                                            <i class="bi bi-trash3 me-2"></i>
                                                            Hapus
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
                <div
                    class="d-flex flex-column flex-md-row justify-content-between align-items-center px-3 py-3 border-top"
                >

                    <div
                        class="text-muted mb-2 mb-md-0"
                        style="font-size: 0.85rem;"
                    >
                        Menampilkan
                        {{ $classes->firstItem() }}–{{ $classes->lastItem() }}
                        dari {{ $classes->total() }} data
                    </div>

                    {{ $classes->links('pagination::bootstrap-5') }}

                </div>

            @else

                {{-- Empty State --}}
                <div class="empty-state">

                    <i class="bi bi-building"></i>

                    <h5>
                        Belum Ada Data Kelas
                    </h5>

                    <p class="text-muted">

                        @if(
                            request('search') ||
                            request('academic_year_id') ||
                            request('grade_level') ||
                            request('status')
                        )

                            Tidak ada data yang cocok dengan filter pencarian.

                        @else

                            Mulai dengan menambahkan data kelas baru.

                        @endif

                    </p>


                    @if(
                        !request('search') &&
                        !request('academic_year_id') &&
                        !request('grade_level') &&
                        !request('status')
                    )

                        <a
                            href="{{ route('classes.create') }}"
                            class="btn btn-biduk-primary"
                        >
                            <i class="bi bi-plus-lg me-1"></i>
                            Tambah Kelas Pertama
                        </a>

                    @endif

                </div>

            @endif

        </div>

    </div>

</div>
@endsection
