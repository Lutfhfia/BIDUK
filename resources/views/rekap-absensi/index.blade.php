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

@section('content')

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-clipboard2-check-fill me-2"
                    style="color: var(--biduk-primary);"></i>
                Rekap Absensi
            </h4>

            <p class="text-muted mb-0" style="font-size: 0.875rem;">
                Kelola rekap ketidakhadiran siswa berdasarkan tahun ajaran dan semester.
            </p>
        </div>

        <a href="{{ route('rekap-absensi.create') }}" class="btn btn-biduk-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Rekap Absensi
        </a>
    </div>


    {{-- Card Filter --}}
    <div class="biduk-card mb-4 fade-in-up">
        <div class="card-body">

            <form action="{{ route('rekap-absensi.index') }}" method="GET">

                <div class="row g-3 align-items-end">

                    {{-- Tahun Ajaran --}}
                    <div class="col-md-4">
                        <label for="academic_year_id" class="form-label">
                            Tahun Ajaran
                        </label>

                        <select
                            name="academic_year_id"
                            id="academic_year_id"
                            class="form-select"
                        >
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
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Semester --}}
                    <div class="col-md-3">
                        <label for="semester_id" class="form-label">
                            Semester
                        </label>

                        <select
                            name="semester_id"
                            id="semester_id"
                            class="form-select"
                        >
                            <option value="">Semua Semester</option>

                            @foreach ($semesters as $semester)
                                <option
                                    value="{{ $semester->id }}"
                                    {{ request('semester_id') == $semester->id ? 'selected' : '' }}
                                >
                                    {{ $semester->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Tombol --}}
                    <div class="col-md-2">
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

                                <th class="text-center" style="width: 60px;">
                                    No
                                </th>

                                <th style="width: 140px;">
                                    NISN
                                </th>

                                <th style="min-width: 180px;">
                                    Nama Siswa
                                </th>

                                <th style="width: 130px;">
                                    Kelas
                                </th>

                                <th style="width: 140px;">
                                    Semester
                                </th>

                                <th class="text-center" style="width: 90px;">
                                    Sakit
                                </th>

                                <th class="text-center" style="width: 90px;">
                                    Izin
                                </th>

                                <th class="text-center" style="width: 150px;">
                                    Tanpa Keterangan
                                </th>

                                <th class="text-center" style="width: 90px;">
                                    Aksi
                                </th>

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


                                    {{-- Semester --}}
                                    <td>
                                        <span class="text-muted">
                                            {{ $rekap->semester->name ?? '-' }}
                                        </span>
                                    </td>


                                    {{-- Sakit --}}
                                    <td class="text-center">
                                        {{ $rekap->sakit }}
                                    </td>


                                    {{-- Izin --}}
                                    <td class="text-center">
                                        {{ $rekap->izin }}
                                    </td>


                                    {{-- Tanpa Keterangan --}}
                                    <td class="text-center">
                                        {{ $rekap->tanpa_keterangan }}
                                    </td>


                                    {{-- Aksi --}}
                                    <td class="text-center">

                                        <div class="dropdown action-dropdown">

                                            <button
                                                class="btn btn-sm btn-light"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                title="Aksi"
                                            >
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>


                                            <ul class="dropdown-menu dropdown-menu-end">

                                                {{-- Detail --}}
                                                <li>
                                                    <a
                                                        class="dropdown-item"
                                                        href="{{ route('rekap-absensi.show', $rekap) }}"
                                                    >
                                                        <i class="bi bi-eye"></i>
                                                        Lihat Detail
                                                    </a>
                                                </li>


                                                {{-- Edit --}}
                                                <li>
                                                    <a
                                                        class="dropdown-item"
                                                        href="{{ route('rekap-absensi.edit', $rekap) }}"
                                                    >
                                                        <i class="bi bi-pencil-square"></i>
                                                        Edit
                                                    </a>
                                                </li>


                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>


                                                {{-- Hapus --}}
                                                <li>
                                                    <form
                                                        action="{{ route('rekap-absensi.destroy', $rekap) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus rekap absensi ini?');"
                                                    >

                                                        @csrf
                                                        @method('DELETE')

                                                        <button
                                                            type="submit"
                                                            class="dropdown-item text-danger"
                                                        >
                                                            <i class="bi bi-trash"></i>
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
                <div class="empty-state">

                    <i class="bi bi-clipboard-x"></i>

                    <h5>
                        Belum Ada Data Rekap Absensi
                    </h5>

                    <p class="mb-3">
                        Belum ada data rekap absensi siswa yang tersedia.
                    </p>

                    <a
                        href="{{ route('rekap-absensi.create') }}"
                        class="btn btn-biduk-primary"
                    >
                        <i class="bi bi-plus-lg me-1"></i>
                        Tambah Rekap Absensi
                    </a>

                </div>

            @endif

        </div>

    </div>

@endsection