@extends('layouts.app')

@section('title', 'Data Pegawai')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>

    <li class="breadcrumb-item">
        Master Data
    </li>

    <li class="breadcrumb-item active" aria-current="page">
        Data Pegawai
    </li>
@endsection

@section('content')

<div class="fade-in-up">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-person-badge me-2 text-success"></i>
                Data Pegawai
            </h4>

            <p class="text-muted mb-0">
                Kelola data guru, kepala sekolah, dan tata usaha SDN 204.
            </p>
        </div>

        <div class="d-flex gap-2">

            {{-- Import --}}
            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('employees.create'))
            <button
                type="button"
                class="btn btn-biduk-outline"
                data-bs-toggle="modal"
                data-bs-target="#importPegawaiModal"
            >
                <i class="bi bi-upload me-1"></i>
                Import Excel
            </button>
            @endif

            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('employees.view'))
            <div class="dropdown">
    <button
        type="button"
        class="btn btn-biduk-outline dropdown-toggle"
        data-bs-toggle="dropdown"
        aria-expanded="false"
    >
        <i class="bi bi-download me-1"></i>
        Export
    </button>

    <ul class="dropdown-menu dropdown-menu-end">
        <li>
            <a
                class="dropdown-item"
                href="{{ route('pegawai.export.excel') }}"
            >
                <i class="bi bi-file-earmark-excel text-success me-2"></i>
                Export Excel
            </a>
        </li>

        <li>
            <a
                class="dropdown-item"
                href="{{ route('pegawai.export.pdf') }}"
            >
                <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                Export PDF
            </a>
        </li>
    </ul>
</div>
            @endif

            {{-- Tambah --}}
            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('employees.create'))
            <a
                href="{{ route('pegawai.create') }}"
                class="btn btn-biduk-primary"
            >
                <i class="bi bi-plus-lg me-1"></i>
                Tambah Pegawai
            </a>
            @endif

        </div>

    </div>


    {{-- Alert berhasil --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
    @endif


    {{-- Alert error --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">

            <strong>
                <i class="bi bi-exclamation-triangle me-2"></i>
                Terjadi kesalahan
            </strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>
    @endif


    {{-- Filter --}}
    <div class="biduk-card mb-4">

        <div class="card-body">

            <form
                action="{{ route('pegawai.index') }}"
                method="GET"
            >

                <div class="row g-3 align-items-end">

                    {{-- Pencarian --}}
                    <div class="col-md-5">

                        <label class="form-label">
                            <i class="bi bi-search me-1"></i>
                            Pencarian
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ request('search') }}"
                            placeholder="Cari Nama, NIP, atau NUPTK..."
                        >

                    </div>


                    {{-- Jabatan --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            <i class="bi bi-briefcase me-1"></i>
                            Jabatan
                        </label>

                        <select
                            name="position"
                            class="form-select"
                        >

                            <option value="">
                                Semua Jabatan
                            </option>

                            <option
                                value="Guru"
                                @selected(request('position') === 'Guru')
                            >
                                Guru
                            </option>

                            <option
                                value="Kepala Sekolah"
                                @selected(request('position') === 'Kepala Sekolah')
                            >
                                Kepala Sekolah
                            </option>

                            <option
                                value="Tata Usaha"
                                @selected(request('position') === 'Tata Usaha')
                            >
                                Tata Usaha
                            </option>

                        </select>

                    </div>


                    {{-- Status --}}
                    <div class="col-md-2">

                        <label class="form-label">
                            <i class="bi bi-toggle-on me-1"></i>
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-select"
                        >

                            <option value="">
                                Semua Status
                            </option>

                            <option
                                value="Aktif"
                                @selected(request('status') === 'Aktif')
                            >
                                Aktif
                            </option>

                            <option
                                value="Nonaktif"
                                @selected(request('status') === 'Nonaktif')
                            >
                                Nonaktif
                            </option>

                        </select>

                    </div>


                    {{-- Filter --}}
                    <div class="col-md-2">

                        <button
                            type="submit"
                            class="btn btn-biduk-primary w-100"
                        >
                            <i class="bi bi-search me-1"></i>
                            Filter
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Tabel --}}
    <div class="biduk-card">

        <div class="card-header">

            <div class="d-flex justify-content-between align-items-center">

                <span>
                    <i class="bi bi-people me-2"></i>
                    Daftar Pegawai
                </span>

                <span class="text-muted small">
                    Total: {{ $employees->total() }} pegawai
                </span>

            </div>

        </div>


        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table biduk-table mb-0">

                    <thead>

                        <tr>
                            <th>NO</th>
                            <th>NIP</th>
                            <th>NAMA PEGAWAI</th>
                            <th>JK</th>
                            <th>JABATAN</th>
                            <th>WALI KELAS</th>
                            <th>STATUS</th>
                            <th class="text-center">AKSI</th>
                        </tr>

                    </thead>


                    <tbody>

                        @forelse($employees as $employee)

                            <tr>

                                {{-- No --}}
                                <td>
                                    {{ $employees->firstItem() + $loop->index }}
                                </td>


                                {{-- NIP --}}
                                <td>
                                    {{ $employee->nip ?? '-' }}
                                </td>


                                {{-- Nama --}}
                                <td>

                                    <div class="d-flex align-items-center gap-2">

                                        <div
                                            class="rounded-circle d-flex align-items-center justify-content-center"
                                            style="
                                                width:40px;
                                                height:40px;
                                                background:#e8f7ef;
                                                color:#198754;
                                                font-weight:700;
                                                overflow:hidden;
                                            "
                                        >
                                            @if ($employee->photo)
                                                <img
                                                    src="{{ asset('storage/' . $employee->photo) }}"
                                                    alt="Foto {{ $employee->name }}"
                                                    style="width:100%;height:100%;object-fit:cover;"
                                                >
                                            @else
                                                {{ strtoupper(substr($employee->name, 0, 1)) }}
                                            @endif
                                        </div>

                                        <div>

                                            <div class="fw-semibold">
                                                {{ $employee->name }}
                                            </div>

                                            @if($employee->nuptk)
                                                <small class="text-muted">
                                                    NUPTK: {{ $employee->nuptk }}
                                                </small>
                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- JK --}}
                                <td>
                                    {{ $employee->gender }}
                                </td>


                                {{-- Jabatan --}}
                                <td>
                                    {{ $employee->position ?? '-' }}
                                </td>


                                {{-- Wali Kelas --}}
                                <td>

                                    @php
                                        $activeClass = $employee->homeroomClasses
                                            ->first(
                                                fn ($class) =>
                                                    $class->academicYear?->status === 'active'
                                            );
                                    @endphp

                                    @if($activeClass)

                                        <span class="badge bg-light text-success border">
                                            {{ $activeClass->name }}
                                        </span>

                                    @else

                                        <span class="text-muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                {{-- Status --}}
                                <td>

                                    @if($employee->status === 'Aktif')

                                        <span class="badge badge-aktif">
                                            Aktif
                                        </span>

                                    @else

                                        <span class="badge badge-nonaktif">
                                            Nonaktif
                                        </span>

                                    @endif

                                </td>


                                {{-- Aksi --}}
                                <td class="text-center">

                                    <div class="d-inline-flex align-items-center justify-content-center gap-1 flex-nowrap">
                                        <a
                                            class="btn btn-sm btn-light text-primary"
                                            href="{{ route('pegawai.show', $employee) }}"
                                            title="Lihat Detail"
                                            aria-label="Lihat detail {{ $employee->name }}"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('employees.edit'))
                                            <a
                                                class="btn btn-sm btn-light text-warning"
                                                href="{{ route('pegawai.edit', $employee) }}"
                                                title="Edit"
                                                aria-label="Edit {{ $employee->name }}"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif

                                        @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('employees.delete'))
                                            <form
                                                action="{{ route('pegawai.destroy', $employee) }}"
                                                method="POST"
                                                onsubmit="return confirm('Yakin ingin menghapus pegawai ini? Akun login BIDUK yang terhubung juga akan dihapus.')"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-light text-danger"
                                                    title="Hapus"
                                                    aria-label="Hapus {{ $employee->name }}"
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="8"
                                    class="text-center py-5"
                                >

                                    <div class="empty-state">

                                        <i class="bi bi-people"></i>

                                        <h5>
                                            Belum Ada Data Pegawai
                                        </h5>

                                        <p class="mb-0">
                                            Data pegawai belum tersedia.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


       {{-- Pagination --}}
    <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">

<div class="text-muted">
    @if ($employees->total() > 0)
        Menampilkan
        {{ $employees->firstItem() }}
        –
        {{ $employees->lastItem() }}
        dari
        {{ $employees->total() }}
        data
    @else
        Menampilkan 0 data
    @endif
</div>

@if ($employees->hasPages())
    <div>
        {{ $employees->links() }}
    </div>
@endif

    </div>

</div>


{{-- ===================================================== --}}
{{-- MODAL IMPORT EXCEL --}}
{{-- ===================================================== --}}

<div
    class="modal fade"
    id="importPegawaiModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title fw-bold">

                    <i class="bi bi-file-earmark-spreadsheet text-success me-2"></i>

                    Import Data Pegawai

                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <p class="text-muted">
                    Gunakan template Excel BIDUK agar format data sesuai
                    dengan sistem.
                </p>


                <div class="alert alert-light border">

                    <div class="fw-semibold mb-2">
                        <i class="bi bi-info-circle me-1"></i>
                        Langkah import
                    </div>

                    <ol class="mb-0 ps-3">

                        <li>
                            Download template Excel.
                        </li>

                        <li>
                            Isi data pegawai sesuai kolom.
                        </li>

                        <li>
                            Simpan file dalam format
                            <strong>.xlsx</strong> atau <strong>.xls</strong>.
                        </li>

                        <li>
                            Upload file tersebut di sini.
                        </li>

                    </ol>

                </div>


                <div class="d-grid mb-3">

                    <a
                        href="{{ route('pegawai.import.template') }}"
                        class="btn btn-outline-success"
                    >

                        <i class="bi bi-download me-2"></i>

                        Download Template Excel

                    </a>

                </div>


                <form
                    action="{{ route('pegawai.import') }}"
                    method="POST"
                    enctype="multipart/form-data"
                >

                    @csrf


                    <label
                        for="file"
                        class="form-label fw-semibold"
                    >
                        File Excel
                    </label>

                    <input
                        type="file"
                        name="file"
                        id="file"
                        class="form-control"
                        accept=".xlsx,.xls"
                        required
                    >


                    <div class="form-text">
                        Maksimal ukuran file 5 MB.
                    </div>


                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <button
                            type="button"
                            class="btn btn-biduk-outline"
                            data-bs-dismiss="modal"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="btn btn-biduk-primary"
                        >
                            <i class="bi bi-upload me-1"></i>
                            Import Data
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</div>

@endsection