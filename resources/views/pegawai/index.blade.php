@extends('layouts.app')

@section('title', 'Data Pegawai')

@push('styles')
<style>
    /* Tampilan Data Pegawai diselaraskan dengan halaman Data Siswa. */
    .employee-page .employee-page-header h4 {
        color: #172f42;
        letter-spacing: -0.25px;
    }

    .employee-page .employee-page-header p {
        font-size: .875rem;
        color: #64748b;
    }

    .employee-page .employee-actions {
        flex-wrap: wrap;
        align-items: center;
    }

    .employee-page .employee-action-btn {
        min-height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        padding: .55rem .9rem;
        border-radius: 9px;
        font-size: .875rem;
        font-weight: 600;
        white-space: nowrap;
        transition: background-color .18s ease, border-color .18s ease,
                    color .18s ease, box-shadow .18s ease, transform .18s ease;
    }

    .employee-page .btn-biduk-primary.employee-action-btn,
    .employee-page .employee-export-toggle {
        border: 0;
        color: #fff;
        background: linear-gradient(115deg, #168653 0%, #22bd70 100%);
        box-shadow: 0 3px 9px rgba(22, 134, 83, .12);
    }

    .employee-page .btn-biduk-primary.employee-action-btn:hover,
    .employee-page .employee-export-toggle:hover,
    .employee-page .btn-biduk-primary.employee-action-btn:focus,
    .employee-page .employee-export-toggle:focus {
        color: #fff;
        background: linear-gradient(115deg, #117548 0%, #18a961 100%);
        box-shadow: 0 5px 13px rgba(22, 134, 83, .2);
        transform: translateY(-1px);
    }

    .employee-page .btn-biduk-outline.employee-action-btn,
    .employee-page .btn-outline-success.employee-action-btn {
        border: 1px solid #198754;
        color: #147847;
        background: #fff;
    }

    .employee-page .btn-biduk-outline.employee-action-btn:hover,
    .employee-page .btn-outline-success.employee-action-btn:hover {
        color: #116a40;
        background: #effaf3;
        border-color: #147847;
    }

    .employee-page .employee-filter-card,
    .employee-page .employee-table-card {
        overflow: hidden;
        border: 1px solid #e7eeea;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(15, 45, 32, .055);
    }

    .employee-page .employee-filter-card .card-body {
        padding: 1.15rem;
    }

    .employee-page .employee-filter-card .form-label {
        margin-bottom: .5rem;
        color: #334155;
        font-size: .84rem;
        font-weight: 600;
    }

    .employee-page .employee-filter-card .form-control,
    .employee-page .employee-filter-card .form-select {
        min-height: 46px;
        border: 1px solid #d8e1e5;
        border-radius: 8px;
        color: #23374a;
        background-color: #fff;
        font-size: .875rem;
        box-shadow: none;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .employee-page .employee-filter-card .form-control:focus,
    .employee-page .employee-filter-card .form-select:focus {
        border-color: #38a66f;
        box-shadow: 0 0 0 3px rgba(34, 181, 115, .12);
    }

    .employee-page .employee-filter-card .btn-filter {
        min-height: 46px;
        border-radius: 8px;
        font-size: .875rem;
        font-weight: 700;
    }

    .employee-page .employee-table-card .card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #edf2ef;
        background: #fff;
        color: #24394a;
        font-size: .95rem;
        font-weight: 700;
    }

    .employee-page .employee-table-card .table-responsive {
        border-radius: 0 0 14px 14px;
    }

    .employee-page .employee-table {
        margin-bottom: 0;
        color: #263747;
        font-size: .875rem;
        vertical-align: middle;
    }

    .employee-page .employee-table thead th {
        padding: .9rem .8rem;
        border-top: 0;
        border-bottom: 1px solid #dbece2;
        background: #e8f5ed;
        color: #087348;
        font-size: .75rem;
        font-weight: 800;
        letter-spacing: .035em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .employee-page .employee-table tbody td {
        padding: .82rem .8rem;
        border-color: #edf1ef;
        vertical-align: middle;
    }

    .employee-page .employee-table tbody tr {
        transition: background-color .15s ease;
    }

    .employee-page .employee-table tbody tr:hover > td {
        background: #f7fcf8;
    }

    .employee-page .employee-avatar {
        width: 36px;
        height: 36px;
        flex: 0 0 36px;
        overflow: hidden;
        border-radius: 50%;
        background: #e6f5ec;
        color: #168653;
        font-size: .85rem;
        font-weight: 800;
    }

    .employee-page .employee-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .employee-page .employee-name {
        color: #203448;
        font-weight: 700;
        line-height: 1.35;
    }

    .employee-page .employee-gender {
        min-width: 27px;
        padding: .35rem .48rem;
        font-size: .75rem;
        font-weight: 800;
    }

    .employee-page .employee-gender-male {
        background: #e1efff;
        color: #1764b0;
    }

    .employee-page .employee-gender-female {
        background: #fde7ef;
        color: #b42355;
    }

    .employee-page .employee-gender-empty {
        background: #f1f5f9;
        color: #64748b;
    }

    .employee-page .employee-position-badge {
        border: 1px solid #dcefe3;
        background: #eef8f1;
        color: #187548;
        font-size: .78rem;
        font-weight: 600;
    }

    .employee-page .employee-action-menu {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #e2e8e5;
        border-radius: 8px;
        background: #fff;
        color: #506274;
    }

    .employee-page .employee-action-menu:hover,
    .employee-page .employee-action-menu[aria-expanded="true"] {
        border-color: #a9d9bc;
        background: #effaf3;
        color: #147847;
    }

    .employee-page .dropdown-menu {
        padding: .4rem;
        border: 1px solid #e7eeea;
        border-radius: 10px;
        box-shadow: 0 10px 28px rgba(15, 45, 32, .12);
    }

    .employee-page .dropdown-item {
        padding: .55rem .7rem;
        border-radius: 6px;
        font-size: .85rem;
    }

    .employee-page .employee-pagination {
        gap: 1rem;
        flex-wrap: wrap;
        padding: 1rem 1.25rem;
        border-top: 1px solid #edf1ef;
        background: #fff;
        font-size: .85rem;
    }

    .employee-page .pagination {
        margin-bottom: 0;
        gap: .2rem;
    }

    .employee-page .pagination .page-link {
        border-radius: 7px;
        color: #168653;
        border-color: #e0e9e4;
    }

    .employee-page .pagination .active .page-link {
        border-color: #168653;
        background: linear-gradient(115deg, #168653, #22bd70);
        color: #fff;
    }

    .employee-page .modal-content {
        overflow: hidden;
        border: 1px solid #e5eee8;
        border-radius: 16px;
        box-shadow: 0 18px 50px rgba(15, 45, 32, .18);
    }

    .employee-page .modal-header {
        border-bottom-color: #e9f0ec;
        background: #fbfefc;
    }

    @media (max-width: 767.98px) {
        .employee-page .employee-page-header {
            align-items: flex-start !important;
        }

        .employee-page .employee-actions {
            width: 100%;
            gap: .5rem !important;
        }

        .employee-page .employee-actions > .dropdown {
            flex: 1 1 auto;
        }

        .employee-page .employee-actions > .dropdown .employee-export-toggle {
            width: 100%;
        }

        .employee-page .employee-action-btn {
            flex: 1 1 auto;
        }

        .employee-page .employee-filter-card .card-body {
            padding: .9rem;
        }

        .employee-page .employee-pagination {
            align-items: flex-start !important;
            flex-direction: column;
        }
    }
</style>
@endpush

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

<div class="fade-in-up employee-page">

    {{-- Header --}}
    <div class="employee-page-header d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">

        <div>
            <h4 class="mb-1 fw-bold">
                <i class="bi bi-person-badge me-2 text-success"></i>
                Data Pegawai
            </h4>

            <p class="text-muted mb-0">
                Kelola data guru, kepala sekolah, dan tenaga kependidikan SDN 204.
            </p>
        </div>

        <div class="employee-actions d-flex gap-2">

            {{-- Import --}}
            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('employees.create'))
            <button
                type="button"
                class="btn btn-biduk-outline employee-action-btn"
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
        class="btn btn-biduk-primary employee-action-btn employee-export-toggle dropdown-toggle"
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
                class="btn btn-biduk-primary employee-action-btn"
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
    <div class="biduk-card employee-filter-card mb-4">

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
                            class="form-control employee-filter-control"
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
                            class="form-select employee-filter-control"
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
                            <option
                                value="Tenaga Administrasi"
                                @selected(request('position') === 'Tenaga Administrasi')
                            >
                                Tenaga Administrasi
                            </option>
                            <option value="OPS" @selected(request('position') === 'OPS')>
                                OPS
                            </option>
                            <option value="Perpstk" @selected(request('position') === 'Perpstk')>
                                Perpstk
                            </option>
                            <option value="Keamanan" @selected(request('position') === 'Keamanan')>
                                Keamanan
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
                            class="form-select employee-filter-control"
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
                            class="btn btn-biduk-primary btn-filter w-100"
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
    <div class="biduk-card employee-table-card">

        <div class="card-header">

            <div class="d-flex justify-content-between align-items-center gap-3">

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

                <table class="table biduk-table table-hover mb-0 employee-table">

                    <thead>

                        <tr>
                            <th>No</th>
                            <th>NIP</th>
                            <th>Nama Pegawai</th>
                            <th>JK</th>
                            <th>Jabatan</th>
                            <th>Wali Kelas</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
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

                                        <div class="employee-avatar d-flex align-items-center justify-content-center">
                                            @if ($employee->photo)
                                                <img
                                                    src="{{ asset('storage/' . $employee->photo) }}"
                                                    alt="Foto {{ $employee->name }}"
                                                >
                                            @else
                                                {{ strtoupper(substr($employee->name, 0, 1)) }}
                                            @endif
                                        </div>

                                        <div>

                                            <div class="employee-name">
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
                                    @if (in_array($employee->gender, ['L', 'P'], true))
                                        <span class="badge employee-gender {{ $employee->gender === 'L' ? 'employee-gender-male' : 'employee-gender-female' }}">
                                            {{ $employee->gender }}
                                        </span>
                                    @else
                                        <span class="badge employee-gender employee-gender-empty">—</span>
                                    @endif
                                </td>


                                {{-- Jabatan --}}
                                <td>
                                    <span class="badge employee-position-badge">
                                        {{ $employee->position ?? '—' }}
                                    </span>
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
        aria-label="Lihat detail Pegawai"
    >
        <i class="bi bi-eye"></i>
    </a>

    @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('employees.edit'))
        <a
            class="btn btn-sm btn-light text-warning"
            href="{{ route('pegawai.edit', $employee) }}"
            title="Edit"
            aria-label="Edit Pegawai"
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
                aria-label="Hapus Pegawai"
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
    <div class="employee-pagination d-flex justify-content-between align-items-center">

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
        {{ $employees->links('pagination::bootstrap-5') }}
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
                        class="btn btn-outline-success employee-action-btn"
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
                            class="btn btn-biduk-outline employee-action-btn"
                            data-bs-dismiss="modal"
                        >
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="btn btn-biduk-primary employee-action-btn"
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