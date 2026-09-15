@extends('layouts.app')

@section('title', 'Mata Pelajaran')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <span>Akademik</span>
    </li>
    <li class="breadcrumb-item active" aria-current="page">
        Mata Pelajaran
    </li>
@endsection

@section('content')

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-journal-bookmark-fill me-2"
                    style="color: var(--biduk-primary);"></i>
                Mata Pelajaran
            </h4>

            <p class="text-muted mb-0" style="font-size: 0.875rem;">
                Kelola data mata pelajaran yang digunakan dalam sistem BIDUK.
            </p>
        </div>

        <a href="{{ route('subjects.create') }}" class="btn btn-biduk-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Mata Pelajaran
        </a>
    </div>


    {{-- Card Filter & Pencarian --}}
    <div class="biduk-card mb-4 fade-in-up">
        <div class="card-body">

            <form action="{{ route('subjects.index') }}" method="GET">

                <div class="row g-3 align-items-end">

                    {{-- Pencarian --}}
                    <div class="col-md-5">
                        <label for="search" class="form-label">
                            Cari Mata Pelajaran
                        </label>

                        <div class="input-group">
                            <span class="input-group-text bg-white">
                                <i class="bi bi-search text-muted"></i>
                            </span>

                            <input
                                type="text"
                                name="search"
                                id="search"
                                class="form-control"
                                placeholder="Cari kode atau nama mata pelajaran..."
                                value="{{ request('search') }}"
                            >
                        </div>
                    </div>


                    {{-- Filter Kategori --}}
                    <div class="col-md-3">
                        <label for="category" class="form-label">
                            Kategori
                        </label>

                        <select name="category" id="category" class="form-select">
                            <option value="">Semua Kategori</option>

                            @foreach ($categories as $category)
                                <option
                                    value="{{ $category }}"
                                    {{ request('category') == $category ? 'selected' : '' }}
                                >
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    {{-- Filter Status --}}
                    <div class="col-md-2">
                        <label for="status" class="form-label">
                            Status
                        </label>

                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>

                            <option
                                value="Aktif"
                                {{ request('status') == 'Aktif' ? 'selected' : '' }}
                            >
                                Aktif
                            </option>

                            <option
                                value="Nonaktif"
                                {{ request('status') == 'Nonaktif' ? 'selected' : '' }}
                            >
                                Nonaktif
                            </option>
                        </select>
                    </div>


                    {{-- Tombol --}}
                    <div class="col-md-2">
                        <div class="d-flex gap-2">

                            <button type="submit" class="btn btn-biduk-primary flex-fill">
                                <i class="bi bi-search me-1"></i>
                                Cari
                            </button>

                            <a
                                href="{{ route('subjects.index') }}"
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

                Daftar Mata Pelajaran
            </div>

            <span class="badge rounded-pill bg-light text-dark">
                {{ $subjects->total() }} Data
            </span>

        </div>


        <div class="card-body p-0">

            @if ($subjects->count() > 0)

                <div class="table-responsive">

                    <table class="table biduk-table mb-0">

                        <thead>
                            <tr>
                                <th class="text-center" style="width: 70px;">
                                    No
                                </th>

                                <th style="width: 150px;">
                                    Kode
                                </th>

                                <th>
                                    Nama Mata Pelajaran
                                </th>

                                <th style="width: 180px;">
                                    Kategori
                                </th>

                                <th class="text-center" style="width: 120px;">
                                    Status
                                </th>

                                <th class="text-center" style="width: 130px;">
                                    Aksi
                                </th>
                            </tr>
                        </thead>


                        <tbody>

                            @foreach ($subjects as $subject)

                                <tr>

                                    {{-- Nomor --}}
                                    <td class="text-center">
                                        {{ $subjects->firstItem() + $loop->index }}
                                    </td>


                                    {{-- Kode --}}
                                    <td>
                                        <span class="fw-semibold">
                                            {{ $subject->code }}
                                        </span>
                                    </td>


                                    {{-- Nama --}}
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            {{ $subject->name }}
                                        </div>
                                    </td>


                                    {{-- Kategori --}}
                                    <td>
                                        @if ($subject->category)
                                            <span class="text-muted">
                                                {{ $subject->category }}
                                            </span>
                                        @else
                                            <span class="text-muted">
                                                -
                                            </span>
                                        @endif
                                    </td>


                                    {{-- Status --}}
                                    <td class="text-center">

                                        @if ($subject->status === 'Aktif')

                                            <span class="badge badge-aktif rounded-pill px-3 py-2">
                                                <i class="bi bi-check-circle-fill me-1"></i>
                                                Aktif
                                            </span>

                                        @else

                                            <span class="badge badge-nonaktif rounded-pill px-3 py-2">
                                                <i class="bi bi-x-circle-fill me-1"></i>
                                                Nonaktif
                                            </span>

                                        @endif

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
                                                        href="{{ route('subjects.show', $subject) }}"
                                                    >
                                                        <i class="bi bi-eye"></i>
                                                        Lihat Detail
                                                    </a>
                                                </li>


                                                {{-- Edit --}}
                                                <li>
                                                    <a
                                                        class="dropdown-item"
                                                        href="{{ route('subjects.edit', $subject) }}"
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
                                                        action="{{ route('subjects.destroy', $subject) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Yakin ingin menghapus mata pelajaran ini?');"
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
                @if ($subjects->hasPages())

                    <div class="d-flex justify-content-between align-items-center px-4 py-3">

                        <div class="text-muted" style="font-size: 0.8rem;">
                            Menampilkan
                            <strong>{{ $subjects->firstItem() }}</strong>
                            sampai
                            <strong>{{ $subjects->lastItem() }}</strong>
                            dari
                            <strong>{{ $subjects->total() }}</strong>
                            data
                        </div>

                        <div>
                            {{ $subjects->links() }}
                        </div>

                    </div>

                @endif

            @else

                {{-- Empty State --}}
                <div class="empty-state">

                    <i class="bi bi-journal-x"></i>

                    <h5>
                        Belum Ada Data Mata Pelajaran
                    </h5>

                    <p class="mb-3">
                        Belum ada mata pelajaran yang tersedia.
                    </p>

                    <a
                        href="{{ route('subjects.create') }}"
                        class="btn btn-biduk-primary"
                    >
                        <i class="bi bi-plus-lg me-1"></i>
                        Tambah Mata Pelajaran
                    </a>

                </div>

            @endif

        </div>

    </div>

@endsection