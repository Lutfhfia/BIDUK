
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

<style>
    /* ==========================================
       BIDUK - HALAMAN MATA PELAJARAN
       Styling dibatasi khusus halaman ini
    ========================================== */

    .subjects-page {
        --sp-green: #16834f;
        --sp-green-dark: #106b40;
        --sp-green-bright: #20b968;
        --sp-green-light: #e5f5eb;
        --sp-border: #e1ebe5;
        --sp-text: #25372c;
        --sp-muted: #718078;

        color: var(--sp-text);
    }

    /* Header halaman */
    .subjects-page .sp-hero {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        overflow: hidden;
        padding: 24px 27px;
        margin-bottom: 22px;
        border: 1px solid #dceee3;
        border-radius: 16px;
        background: linear-gradient(
            115deg,
            #e5f5eb 0%,
            #f8fcf9 48%,
            #def2e6 100%
        );
    }

    .subjects-page .sp-hero-content,
    .subjects-page .sp-hero-action {
        position: relative;
        z-index: 1;
    }

    .subjects-page .sp-hero-content {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .subjects-page .sp-hero-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 60px;
        height: 60px;
        border: 1px solid rgba(22, 131, 79, .08);
        border-radius: 16px;
        background: linear-gradient(
            145deg,
            #e4f6eb,
            #cfeedd
        );
        color: var(--sp-green);
        font-size: 27px;
    }

    .subjects-page .sp-title {
        margin: 0 0 6px;
        color: #1f3026;
        font-size: clamp(1.35rem, 2vw, 1.75rem);
        font-weight: 750;
        letter-spacing: -.4px;
    }

    .subjects-page .sp-description {
        margin: 0;
        color: #66766c;
        font-size: .9rem;
        line-height: 1.6;
    }

    .subjects-page .sp-decoration {
        position: absolute;
        top: 50%;
        right: 24%;
        color: var(--sp-green);
        font-size: 108px;
        opacity: .065;
        pointer-events: none;
        transform: translateY(-50%) rotate(-8deg);
    }

    .subjects-page .sp-add-btn,
    .subjects-page .sp-search-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 41px;
        padding: 9px 16px;
        border: 1px solid transparent;
        border-radius: 9px;
        background: linear-gradient(
            105deg,
            #16834f 0%,
            #20b968 100%
        );
        box-shadow: 0 4px 11px rgba(22, 131, 79, .13);
        color: #fff;
        font-size: .86rem;
        font-weight: 600;
        text-decoration: none;
        white-space: nowrap;
        transition: background .18s ease, transform .18s ease;
    }

    .subjects-page .sp-add-btn:hover,
    .subjects-page .sp-search-btn:hover {
        background: linear-gradient(
            105deg,
            #116d42 0%,
            #159653 100%
        );
        color: #fff;
        transform: translateY(-1px);
    }

    /* Kartu filter dan tabel */
    .subjects-page .sp-card {
        overflow: visible;
        border: 1px solid var(--sp-border);
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 5px 19px rgba(30, 70, 45, .045);
    }

    .subjects-page .sp-filter-card {
        margin-bottom: 20px;
    }

    .subjects-page .sp-filter-card .card-body {
        padding: 20px;
    }

    /* Label dan input filter */
    .subjects-page .sp-filter-card .form-label {
        display: block;
        margin-bottom: 8px;
        color: #526257;
        font-size: .82rem;
        font-weight: 600;
    }

    .subjects-page .sp-filter-card .form-label i {
        margin-right: 5px;
        color: var(--sp-green);
    }

    .subjects-page .sp-filter-card .form-control,
    .subjects-page .sp-filter-card .form-select {
        min-height: 41px;
        border: 1px solid #dce6df;
        border-radius: 8px;
        background-color: #fff;
        color: #34463a;
        font-size: .84rem;
        box-shadow: none;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .subjects-page .sp-filter-card .form-control:focus,
    .subjects-page .sp-filter-card .form-select:focus {
        border-color: #70bd91;
        box-shadow: 0 0 0 3px rgba(22, 131, 79, .09);
    }

    .subjects-page .sp-filter-card .input-group-text {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        border: 1px solid #dce6df;
        border-right: 0;
        border-radius: 8px 0 0 8px;
        background: #f8fbf9;
        color: var(--sp-green);
    }

    .subjects-page .sp-filter-card .input-group .form-control {
        border-radius: 0 8px 8px 0;
    }

    .subjects-page .sp-reset-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 41px;
        min-height: 41px;
        padding: 8px 12px;
        border: 1px solid #a9d8bc;
        border-radius: 9px;
        background: linear-gradient(145deg, #fff, #f1f9f4);
        color: var(--sp-green);
        text-decoration: none;
        transition: background .18s ease, color .18s ease;
    }

    .subjects-page .sp-reset-btn:hover {
        background: var(--sp-green);
        color: #fff;
    }

    /* Header card daftar */
    .subjects-page .sp-table-card {
        overflow: hidden;
    }

    .subjects-page .sp-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        padding: 18px 22px;
        border-bottom: 1px solid #e7eee9;
        background: #fff;
    }

    .subjects-page .sp-table-heading {
        display: flex;
        align-items: center;
        gap: 11px;
        min-width: 0;
    }

    .subjects-page .sp-table-heading-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: linear-gradient(145deg, #e7f7ed, #d4efdf);
        color: var(--sp-green);
        font-size: 20px;
    }

    .subjects-page .sp-table-heading h5 {
        margin: 0 0 4px;
        color: #293a2f;
        font-size: 1rem;
        font-weight: 700;
    }

    .subjects-page .sp-table-heading p {
        margin: 0;
        color: var(--sp-muted);
        font-size: .8rem;
    }

    .subjects-page .sp-data-count {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 10px;
        border: 1px solid #dceee3;
        border-radius: 8px;
        background: #f0f8f3;
        color: var(--sp-green-dark);
        font-size: .77rem;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Tabel */
    .subjects-page .sp-table-wrap {
        width: 100%;
    }

    .subjects-page .sp-table {
        width: 100%;
        margin: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .subjects-page .sp-table thead th {
        padding: 13px 15px;
        border-top: 0;
        border-bottom: 1px solid #d9eae0;
        background: linear-gradient(
            90deg,
            #e5f4eb 0%,
            #f0f8f3 50%,
            #e5f4eb 100%
        );
        color: #3e5747;
        font-size: .78rem;
        font-weight: 700;
        text-transform: none;
        white-space: nowrap;
    }

    .subjects-page .sp-table tbody td {
        padding: 13px 15px;
        border-bottom: 1px solid #ebf0ec;
        background: #fff;
        color: #435247;
        font-size: .84rem;
        vertical-align: middle;
    }

    .subjects-page .sp-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .subjects-page .sp-table tbody tr {
        transition: background .16s ease;
    }

    .subjects-page .sp-table tbody tr:hover td {
        background: #f7fbf8;
    }

    .subjects-page .sp-row-number {
        color: #7b8980 !important;
        font-variant-numeric: tabular-nums;
    }

    .subjects-page .sp-subject-code {
        color: #304237;
        font-weight: 700;
        white-space: nowrap;
    }

    .subjects-page .sp-subject-name {
        color: #27382d;
        font-weight: 600;
    }

    .subjects-page .sp-category {
        color: #718078;
    }



/* Badge status: samakan dengan halaman Tahun Ajaran */
.subjects-page .sp-status {
    display: inline-block;
    padding: 4px 8px;
    border: none;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.2;
    white-space: nowrap;
}

.subjects-page .sp-status-active {
    background: #d4edda;
    color: #087443;
}

.subjects-page .sp-status-inactive {
    background: #e9ecef;
    color: #687781;
}


    /* Menu aksi tiga titik tetap dipertahankan */
    .subjects-page .sp-action-toggle {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        padding: 0;
        border: 1px solid #e7eee9;
        border-radius: 8px;
        background: #f7faf8;
        color: #52665a;
        font-size: 1rem;
        transition: background .18s ease, color .18s ease;
    }

    .subjects-page .sp-action-toggle:hover,
    .subjects-page .sp-action-toggle[aria-expanded="true"] {
        border-color: #c2e2cf;
        background: #e5f5eb;
        color: var(--sp-green);
    }

    .subjects-page .dropdown-menu {
        min-width: 165px;
        padding: 5px;
        border: 1px solid #e3ebe5;
        border-radius: 10px;
        box-shadow: 0 8px 24px rgba(27, 54, 37, .12);
    }

    .subjects-page .dropdown-item {
        display: flex;
        align-items: center;
        gap: 9px;
        padding: 9px 10px;
        border-radius: 6px;
        color: #46564b;
        font-size: .83rem;
    }

    .subjects-page .dropdown-item:hover {
        background: #eef8f2;
        color: var(--sp-green-dark);
    }

    .subjects-page .dropdown-item i {
        width: 16px;
        text-align: center;
    }

    /* Pagination dan informasi jumlah data */
    .subjects-page .sp-table-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding: 14px 22px;
        border-top: 1px solid #edf1ee;
        background: #fff;
    }

    .subjects-page .sp-pagination-info {
        color: var(--sp-muted);
        font-size: .8rem;
    }

    .subjects-page .sp-pagination-info strong {
        color: #3d5144;
    }

    .subjects-page .pagination {
        gap: 5px;
        margin: 0;
    }

    .subjects-page .pagination .page-link {
        min-width: 34px;
        border: 1px solid #e0e9e3;
        border-radius: 7px;
        background: #fff;
        color: var(--sp-green);
        font-size: .82rem;
        text-align: center;
    }

    .subjects-page .pagination .page-item.active .page-link {
        border-color: var(--sp-green);
        background: var(--sp-green);
        color: #fff;
    }

    .subjects-page .pagination .page-item.disabled .page-link {
        color: #a0aaa4;
    }

    /* Tampilan ketika belum ada data */
    .subjects-page .sp-empty-state {
        padding: 48px 20px;
        text-align: center;
    }

    .subjects-page .sp-empty-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 65px;
        height: 65px;
        margin-bottom: 15px;
        border-radius: 18px;
        background: linear-gradient(145deg, #e7f7ed, #d4efdf);
        color: var(--sp-green);
        font-size: 29px;
    }

    .subjects-page .sp-empty-state h5 {
        margin-bottom: 7px;
        color: #304237;
        font-size: 1rem;
        font-weight: 700;
    }

    .subjects-page .sp-empty-state p {
        margin-bottom: 18px;
        color: var(--sp-muted);
        font-size: .85rem;
    }

    /* Responsif */
    @media (max-width: 767.98px) {
        .subjects-page {
            padding-top: 16px !important;
        }

        .subjects-page .sp-hero {
            align-items: flex-start;
            flex-direction: column;
            gap: 16px;
            padding: 20px;
        }

        .subjects-page .sp-hero-content {
            gap: 12px;
        }

        .subjects-page .sp-hero-icon {
            width: 49px;
            height: 49px;
            border-radius: 13px;
            font-size: 23px;
        }

        .subjects-page .sp-description {
            font-size: .82rem;
        }

        .subjects-page .sp-hero-action,
        .subjects-page .sp-add-btn {
            width: 100%;
        }

        .subjects-page .sp-decoration {
            right: 4%;
            font-size: 80px;
        }

        .subjects-page .sp-filter-card .card-body {
            padding: 16px;
        }

        .subjects-page .sp-card-header {
            padding: 16px;
        }

        .subjects-page .sp-table {
            min-width: 760px;
        }

        .subjects-page .sp-table thead th,
        .subjects-page .sp-table tbody td {
            padding: 12px;
        }

        .subjects-page .sp-table-footer {
            padding: 14px 16px;
        }
    }
</style>

<div class="container-fluid py-4 subjects-page">

    {{-- Header halaman --}}
    <div class="sp-hero fade-in-up">

        <div class="sp-hero-content">
            <div class="sp-hero-icon" aria-hidden="true">
                <i class="bi bi-journal-bookmark-fill"></i>
            </div>

            <div>
                <h4 class="sp-title">Mata Pelajaran</h4>
                <p class="sp-description">
                    Kelola data mata pelajaran yang digunakan dalam sistem BIDUK.
                </p>
            </div>
        </div>

        <i class="bi bi-journal-bookmark sp-decoration"
           aria-hidden="true"></i>

        @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('subjects.create'))
            <div class="sp-hero-action">
                <a href="{{ route('subjects.create') }}"
                   class="btn sp-add-btn">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Mata Pelajaran
                </a>
            </div>
        @endif

    </div>

    {{-- Card filter dan pencarian --}}
    <div class="sp-card sp-filter-card fade-in-up">
        <div class="card-body">

            <form action="{{ route('subjects.index') }}" method="GET">

                <div class="row g-3 align-items-end">

                    {{-- Pencarian --}}
                    <div class="col-md-5">
                        <label for="search" class="form-label">
                            <i class="bi bi-search"></i>
                            Cari Mata Pelajaran
                        </label>

                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>

                            <input type="text"
                                   name="search"
                                   id="search"
                                   class="form-control"
                                   placeholder="Cari kode atau nama mata pelajaran..."
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    {{-- Filter kategori --}}
                    <div class="col-md-3">
                        <label for="category" class="form-label">
                            <i class="bi bi-grid"></i>
                            Kategori
                        </label>

                        <select name="category" id="category" class="form-select">
                            <option value="">Semua Kategori</option>

                            @foreach ($categories as $category)
                                <option value="{{ $category }}"
                                    {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter status --}}
                    <div class="col-md-2">
                        <label for="status" class="form-label">
                            <i class="bi bi-filter"></i>
                            Status
                        </label>

                        <select name="status" id="status" class="form-select">
                            <option value="">Semua Status</option>

                            <option value="Aktif"
                                {{ request('status') == 'Aktif' ? 'selected' : '' }}>
                                Aktif
                            </option>

                            <option value="Nonaktif"
                                {{ request('status') == 'Nonaktif' ? 'selected' : '' }}>
                                Nonaktif
                            </option>
                        </select>
                    </div>

                    {{-- Cari dan reset --}}
                    <div class="col-md-2">
                        <div class="d-flex gap-2">
                            <button type="submit"
                                    class="btn sp-search-btn flex-fill">
                                <i class="bi bi-search"></i>
                                Cari
                            </button>

                            <a href="{{ route('subjects.index') }}"
                               class="btn sp-reset-btn"
                               title="Reset Filter"
                               aria-label="Reset filter">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                        </div>
                    </div>

                </div>

            </form>

        </div>
    </div>

    {{-- Card daftar mata pelajaran --}}
    <div class="sp-card sp-table-card fade-in-up">

        {{-- Header daftar --}}
        <div class="sp-card-header">

            <div class="sp-table-heading">
                <div class="sp-table-heading-icon" aria-hidden="true">
                    <i class="bi bi-list-ul"></i>
                </div>

                <div>
                    <h5>Daftar Mata Pelajaran</h5>
                    <p>Daftar mata pelajaran yang tersedia dalam sistem.</p>
                </div>
            </div>

            <span class="sp-data-count">
                <i class="bi bi-collection"></i>
                {{ $subjects->total() }} Data
            </span>

        </div>

        <div class="card-body p-0">

            @if ($subjects->count() > 0)

                <div class="table-responsive sp-table-wrap">

                    <table class="table sp-table">

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
                                    <td class="text-center sp-row-number">
                                        {{ $subjects->firstItem() + $loop->index }}
                                    </td>

                                    {{-- Kode --}}
                                    <td>
                                        <span class="sp-subject-code">
                                            {{ $subject->code }}
                                        </span>
                                    </td>

                                    {{-- Nama --}}
                                    <td>
                                        <div class="sp-subject-name">
                                            {{ $subject->name }}
                                        </div>
                                    </td>

                                    {{-- Kategori --}}
                                    <td>
                                        @if ($subject->category)
                                            <span class="sp-category">
                                                {{ $subject->category }}
                                            </span>
                                        @else
                                            <span class="sp-category">-</span>
                                        @endif
                                    </td>


{{-- Status --}}
<td class="text-center">
    @if ($subject->status === 'Aktif')
        <span class="sp-status sp-status-active">
            Aktif
        </span>
    @else
        <span class="sp-status sp-status-inactive">
            Nonaktif
        </span>
    @endif
</td>


                                    {{-- Aksi: detail, edit, dan hapus --}}
                                    <td class="text-center">
                                        <div class="dropdown action-dropdown">

                                            <button class="sp-action-toggle"
                                                    type="button"
                                                    data-bs-toggle="dropdown"
                                                    aria-expanded="false"
                                                    title="Aksi"
                                                    aria-label="Aksi untuk {{ $subject->name }}">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end">

                                                {{-- Detail --}}
                                                <li>
                                                    <a class="dropdown-item"
                                                       href="{{ route('subjects.show', $subject) }}">
                                                        <i class="bi bi-eye"></i>
                                                        Lihat Detail
                                                    </a>
                                                </li>

                                                {{-- Edit --}}
                                                @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('subjects.edit'))
                                                    <li>
                                                        <a class="dropdown-item"
                                                           href="{{ route('subjects.edit', $subject) }}">
                                                            <i class="bi bi-pencil-square"></i>
                                                            Edit
                                                        </a>
                                                    </li>
                                                @endif

                                                {{-- Hapus --}}
                                                @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('subjects.delete'))
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('subjects.destroy', $subject) }}"
                                                              method="POST"
                                                              onsubmit="return confirm('Yakin ingin menghapus mata pelajaran ini?');">
                                                            @csrf
                                                            @method('DELETE')

                                                            <button type="submit"
                                                                    class="dropdown-item text-danger">
                                                                <i class="bi bi-trash"></i>
                                                                Hapus
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

                {{-- Pagination --}}
                @if ($subjects->hasPages())
                    <div class="sp-table-footer">

                        <div class="sp-pagination-info">
                            Menampilkan
                            <strong>{{ $subjects->firstItem() }}</strong>
                            sampai
                            <strong>{{ $subjects->lastItem() }}</strong>
                            dari
                            <strong>{{ $subjects->total() }}</strong>
                            data
                        </div>

                        <div>
                            {{ $subjects->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>

                    </div>
                @endif

            @else

                {{-- Data kosong --}}
                <div class="sp-empty-state">

                    <div class="sp-empty-icon">
                        <i class="bi bi-journal-x"></i>
                    </div>

                    <h5>Belum Ada Data Mata Pelajaran</h5>

                    <p>
                        Belum ada mata pelajaran yang tersedia.
                    </p>

                    @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('subjects.create'))
                        <a href="{{ route('subjects.create') }}"
                           class="btn sp-add-btn">
                            <i class="bi bi-plus-lg"></i>
                            Tambah Mata Pelajaran
                        </a>
                    @endif

                </div>

            @endif

        </div>

    </div>

</div>

@endsection
