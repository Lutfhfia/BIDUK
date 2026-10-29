
@extends('layouts.app')

@section('content')

<style>
    /* Semua styling dibatasi pada halaman Tahun Ajaran */
    .academic-years-page {
        --ay-green: #16834f;
        --ay-green-dark: #106b40;
        --ay-green-light: #eef8f2;
        --ay-border: #e4ece7;
        --ay-text: #24352b;
        --ay-muted: #718078;
        color: var(--ay-text);
    }

    /* Header halaman */
    .academic-years-page .ay-hero {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        overflow: hidden;
        padding: 25px 28px;
        margin-bottom: 22px;
        border: 1px solid #e0eee5;
        border-radius: 17px;
        background: linear-gradient(
            115deg,
            #edf8f1 0%,
            #f8fcf9 58%,
            #e7f5ec 100%
        );
    }

    .academic-years-page .ay-hero-content,
    .academic-years-page .ay-hero-action {
        position: relative;
        z-index: 1;
    }

    .academic-years-page .ay-hero-content {
        display: flex;
        align-items: center;
        gap: 17px;
        min-width: 0;
    }

    .academic-years-page .ay-title-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 62px;
        height: 62px;
        border: 1px solid rgba(22, 131, 79, .08);
        border-radius: 17px;
        background: #dff2e7;
        color: var(--ay-green);
        font-size: 28px;
    }

    .academic-years-page .ay-page-title {
        margin: 0 0 6px;
        color: #1c3025;
        font-size: clamp(1.35rem, 2vw, 1.8rem);
        font-weight: 750;
        letter-spacing: -.5px;
    }

    .academic-years-page .ay-page-description {
        margin: 0;
        color: #65766b;
        font-size: .94rem;
        line-height: 1.6;
    }

    .academic-years-page .ay-hero-decoration {
        position: absolute;
        top: 50%;
        right: 24%;
        color: #218453;
        font-size: 112px;
        opacity: .065;
        pointer-events: none;
        transform: translateY(-50%) rotate(-9deg);
    }

    .academic-years-page .ay-add-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 43px;
        padding: 10px 17px;
        border: 1px solid var(--ay-green);
        border-radius: 10px;
        background: var(--ay-green);
        box-shadow: 0 4px 10px rgba(22, 131, 79, .12);
        color: #fff;
        font-size: .875rem;
        font-weight: 600;
        white-space: nowrap;
        transition: background .18s ease, transform .18s ease;
    }

    .academic-years-page .ay-add-btn:hover {
        border-color: var(--ay-green-dark);
        background: var(--ay-green-dark);
        color: #fff;
        transform: translateY(-1px);
    }

    /* Alert */
    .academic-years-page .alert {
        border-radius: 11px;
    }

    /* Kartu daftar tahun ajaran */
    .academic-years-page .ay-card {
        overflow: hidden;
        border: 1px solid var(--ay-border);
        border-radius: 16px;
        background: #fff;
        box-shadow: 0 5px 20px rgba(30, 70, 45, .045);
    }

    .academic-years-page .ay-card-header {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 22px 24px 18px;
    }

    .academic-years-page .ay-card-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: var(--ay-green-light);
        color: var(--ay-green);
        font-size: 21px;
    }

    .academic-years-page .ay-card-title {
        margin: 0 0 4px;
        color: #24352b;
        font-size: 1.08rem;
        font-weight: 700;
    }

    .academic-years-page .ay-card-description {
        margin: 0;
        color: var(--ay-muted);
        font-size: .86rem;
        line-height: 1.5;
    }

    /* Tabel: kolom dipertahankan persis seperti sebelumnya */
    .academic-years-page .ay-table-wrap {
        padding: 0 22px;
    }

    .academic-years-page .ay-table {
        width: 100%;
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
    }

    .academic-years-page .ay-table thead th {
        padding: 14px 15px;
        border-top: 0;
        border-bottom: 1px solid #e1ebe5;
        background: #f1f7f3;
        color: #425247;
        font-size: .81rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .academic-years-page .ay-table thead th:first-child {
        border-top-left-radius: 9px;
    }

    .academic-years-page .ay-table thead th:last-child {
        border-top-right-radius: 9px;
    }

    .academic-years-page .ay-table tbody td {
        padding: 15px;
        border-bottom: 1px solid #e9efeb;
        background: #fff;
        color: #46564b;
        font-size: .875rem;
        vertical-align: middle;
    }

    .academic-years-page .ay-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .academic-years-page .ay-table tbody tr {
        transition: background .15s ease;
    }

    .academic-years-page .ay-table tbody tr:hover td {
        background: #f8fbf9;
    }

    .academic-years-page .ay-table tbody tr.ay-current-row td {
        background: #f3faf6;
    }

    .academic-years-page .ay-table tbody tr.ay-current-row:hover td {
        background: #eaf6ef;
    }

    .academic-years-page .ay-number {
        color: #7a8980 !important;
        font-variant-numeric: tabular-nums;
    }

    .academic-years-page .ay-year-name {
        color: #25382c;
        font-size: .91rem;
        font-weight: 700;
    }

    .academic-years-page .ay-current-label {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-top: 5px;
        color: var(--ay-green);
        font-size: .76rem;
        font-weight: 500;
    }

    /* Badge status */
    .academic-years-page .ay-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        padding: 7px 11px;
        border-radius: 8px;
        font-size: .77rem;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
    }

    .academic-years-page .ay-status::before {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        content: "";
    }

    .academic-years-page .ay-status-active {
        background: #dcf4e5;
        color: #147343;
    }

    .academic-years-page .ay-status-active::before {
        background: #159653;
    }

    .academic-years-page .ay-status-inactive {
        background: #edf0f2;
        color: #687781;
    }

    .academic-years-page .ay-status-inactive::before {
        background: #89969e;
    }

    /* Dua tombol aksi: detail dan edit */
    .academic-years-page .ay-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
    }

    .academic-years-page .ay-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 36px;
        padding: 0;
        border: 1px solid #b5dbc4;
        border-radius: 9px;
        background: #fff;
        color: var(--ay-green);
        font-size: .94rem;
        text-decoration: none;
        transition:
            color .18s ease,
            background .18s ease,
            border-color .18s ease;
    }

    .academic-years-page .ay-action-btn:hover {
        border-color: var(--ay-green);
        background: var(--ay-green);
        color: #fff;
    }

    .academic-years-page .ay-action-btn:focus-visible {
        outline: 3px solid rgba(22, 131, 79, .2);
        outline-offset: 2px;
    }

    /* Data kosong */
    .academic-years-page .ay-empty {
        padding: 45px 20px !important;
        text-align: center;
    }

    .academic-years-page .ay-empty-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 64px;
        height: 64px;
        margin-bottom: 14px;
        border-radius: 18px;
        background: var(--ay-green-light);
        color: var(--ay-green);
        font-size: 29px;
    }

    /* Bagian bawah dan pagination */
    .academic-years-page .ay-card-footer {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding: 14px 22px 18px;
        border-top: 1px solid #f0f3f1;
    }

    .academic-years-page .ay-card-footer:empty {
        display: none;
    }

    .academic-years-page .pagination {
        gap: 5px;
        margin-bottom: 0;
    }

    .academic-years-page .pagination .page-link {
        min-width: 35px;
        border: 1px solid #e1e9e4;
        border-radius: 8px;
        color: var(--ay-green);
        text-align: center;
    }

    .academic-years-page .pagination .page-item.active .page-link {
        border-color: var(--ay-green);
        background: var(--ay-green);
        color: #fff;
    }

    .academic-years-page .pagination .page-item.disabled .page-link {
        color: #a0aaa4;
    }

    /* Tampilan tablet dan ponsel */
    @media (max-width: 767.98px) {
        .academic-years-page {
            padding-top: 16px !important;
        }

        .academic-years-page .ay-hero {
            align-items: flex-start;
            flex-direction: column;
            gap: 17px;
            padding: 20px;
        }

        .academic-years-page .ay-hero-content {
            gap: 12px;
        }

        .academic-years-page .ay-title-icon {
            width: 49px;
            height: 49px;
            border-radius: 13px;
            font-size: 23px;
        }

        .academic-years-page .ay-page-description {
            font-size: .84rem;
        }

        .academic-years-page .ay-hero-action,
        .academic-years-page .ay-add-btn {
            width: 100%;
        }

        .academic-years-page .ay-hero-decoration {
            right: 4%;
            font-size: 85px;
        }

        .academic-years-page .ay-card-header {
            padding: 18px 17px;
        }

        .academic-years-page .ay-table-wrap {
            padding: 0 12px;
        }

        .academic-years-page .ay-table {
            min-width: 700px;
        }

        .academic-years-page .ay-table thead th,
        .academic-years-page .ay-table tbody td {
            padding: 12px;
        }

        .academic-years-page .ay-card-footer {
            padding: 13px 16px;
        }
    }

/* ===== Penyelarasan gradasi dengan halaman Data Kelas ===== */

/* Header halaman: gradasi hijau lembut */
.academic-years-page .ay-hero {
    background: linear-gradient(
        115deg,
        #e4f5eb 0%,
        #f7fcf9 48%,
        #dff2e7 100%
    );
    border-color: #d6ebdf;
}

/* Ikon judul dengan gradasi */
.academic-years-page .ay-title-icon {
    background: linear-gradient(
        145deg,
        #e1f5e9 0%,
        #ccebd9 100%
    );
    border-color: #d1eadd;
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, .8);
}

/* Tombol tambah dengan gradasi hijau seperti Data Kelas */
.academic-years-page .ay-add-btn {
    background: linear-gradient(
        105deg,
        #16834f 0%,
        #20b968 100%
    );
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(22, 131, 79, .16);
    transition:
        background .2s ease,
        box-shadow .2s ease,
        transform .2s ease;
}

.academic-years-page .ay-add-btn:hover {
    background: linear-gradient(
        105deg,
        #116d42 0%,
        #159653 100%
    );
    border-color: transparent;
    color: #fff;
    box-shadow: 0 6px 15px rgba(22, 131, 79, .22);
    transform: translateY(-1px);
}

/* Header tabel: gradasi hijau sangat lembut */
.academic-years-page .ay-table thead th {
    background: linear-gradient(
        90deg,
        #e7f5ec 0%,
        #f2f9f5 50%,
        #e6f4eb 100%
    );
    border-bottom-color: #d6e9dd;
}

/* Ikon pada kartu daftar */
.academic-years-page .ay-card-icon {
    background: linear-gradient(
        145deg,
        #e4f6eb 0%,
        #d4efdf 100%
    );
}

/* Status aktif */
.academic-years-page .ay-status-active {
    background: linear-gradient(
        110deg,
        #dcf5e5 0%,
        #c8ecd7 100%
    );
    color: #147343;
}

/* Baris tahun ajaran berjalan */
.academic-years-page .ay-table tbody tr.ay-current-row td {
    background: linear-gradient(
        90deg,
        #f0faf4 0%,
        #f8fcf9 100%
    );
}

/* Tombol detail dan edit tetap hanya dua */
.academic-years-page .ay-action-btn {
    background: linear-gradient(
        145deg,
        #ffffff 0%,
        #f0f9f3 100%
    );
    border-color: #b5dbc4;
}

.academic-years-page .ay-action-btn:hover {
    background: linear-gradient(
        110deg,
        #16834f 0%,
        #20b968 100%
    );
    border-color: #16834f;
    color: #fff;
}

/* Samakan badge status dengan fitur Data Kelas */
.academic-years-page .ay-status {
    display: inline-block;
    padding: 4px 8px;
    border: none;
    border-radius: 5px;
    font-size: 12px;
    font-weight: 600;
    line-height: 1.2;
    white-space: nowrap;
}

/* Hilangkan titik di samping label status */
.academic-years-page .ay-status::before {
    display: none;
}

/* Status Aktif */
.academic-years-page .ay-status-active {
    background: #d4edda;
    color: #087443;
}

/* Status Tidak Aktif */
.academic-years-page .ay-status-inactive {
    background: #e9ecef;
    color: #687781;
}

/* Posisikan status di tengah kolom */
.academic-years-page .ay-table thead th:nth-child(5),
.academic-years-page .ay-table tbody td:nth-child(5) {
    text-align: center;
}

</style>


<div class="container-fluid py-4 academic-years-page">

    {{-- Header halaman --}}
    <div class="ay-hero">

        <div class="ay-hero-content">
            <div class="ay-title-icon" aria-hidden="true">
                <i class="bi bi-calendar3"></i>
            </div>

            <div>
                <h3 class="ay-page-title">Tahun Ajaran</h3>
                <p class="ay-page-description">
                    Kelola tahun ajaran yang digunakan dalam sistem BIDUK.
                </p>
            </div>
        </div>

        <i class="bi bi-calendar2-week ay-hero-decoration"
           aria-hidden="true"></i>

        @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('academic_years.create'))
            <div class="ay-hero-action">
                <a href="{{ route('academic-years.create') }}"
                   class="btn ay-add-btn">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Tahun Ajaran
                </a>
            </div>
        @endif

    </div>

    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"
             role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Tutup">
            </button>
        </div>
    @endif

    {{-- Alert error --}}
    @if($errors->has('delete'))
        <div class="alert alert-danger alert-dismissible fade show"
             role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ $errors->first('delete') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Tutup">
            </button>
        </div>
    @endif

    {{-- Kartu daftar tahun ajaran --}}
    <div class="ay-card">

        {{-- Judul tabel --}}
        <div class="ay-card-header">
            <div class="ay-card-icon" aria-hidden="true">
                <i class="bi bi-calendar3"></i>
            </div>

            <div>
                <h5 class="ay-card-title">Daftar Tahun Ajaran</h5>
                <p class="ay-card-description">
                    Daftar tahun ajaran yang tersedia dalam sistem.
                </p>
            </div>
        </div>

        {{-- Tabel: struktur kolom tidak diubah --}}
        <div class="table-responsive ay-table-wrap">
            <table class="table ay-table">

                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Tahun Ajaran</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Status</th>
                        <th width="130" class="text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($academicYears as $academicYear)

                        <tr class="{{ $academicYear->status === 'active' ? 'ay-current-row' : '' }}">

                            {{-- Nomor --}}
                            <td class="ay-number">
                                {{ $academicYears->firstItem() + $loop->index }}
                            </td>

                            {{-- Tahun Ajaran --}}
                            <td>
                                <div class="ay-year-name">
                                    {{ $academicYear->name }}
                                </div>

                                @if($academicYear->status === 'active')
                                    <div class="ay-current-label">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Tahun ajaran berjalan
                                    </div>
                                @endif
                            </td>

                            {{-- Tanggal Mulai --}}
                            <td class="text-nowrap">
                                {{ $academicYear->start_date?->format('d/m/Y') ?? '-' }}
                            </td>

                            {{-- Tanggal Selesai --}}
                            <td class="text-nowrap">
                                {{ $academicYear->end_date?->format('d/m/Y') ?? '-' }}
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($academicYear->status === 'active')
                                    <span class="ay-status ay-status-active">
                                        Aktif
                                    </span>
                                @else
                                    <span class="ay-status ay-status-inactive">
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>

                            {{-- Aksi: tetap Detail dan Edit --}}
                            <td>
                                <div class="ay-actions">

                                    <a href="{{ route('academic-years.show', $academicYear) }}"
                                       class="ay-action-btn"
                                       title="Lihat detail"
                                       aria-label="Lihat detail tahun ajaran {{ $academicYear->name }}">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('academic_years.edit'))
                                        <a href="{{ route('academic-years.edit', $academicYear) }}"
                                           class="ay-action-btn"
                                           title="Edit"
                                           aria-label="Edit tahun ajaran {{ $academicYear->name }}">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endif

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="ay-empty">
                                <div class="ay-empty-icon">
                                    <i class="bi bi-calendar-x"></i>
                                </div>

                                <h6 class="fw-bold mb-2">
                                    Belum ada tahun ajaran
                                </h6>

                                <p class="text-muted small mb-3">
                                    Silakan tambahkan tahun ajaran terlebih dahulu.
                                </p>

                                @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('academic_years.create'))
                                    <a href="{{ route('academic-years.create') }}"
                                       class="btn ay-add-btn">
                                        <i class="bi bi-plus-lg"></i>
                                        Tambah Tahun Ajaran
                                    </a>
                                @endif
                            </td>
                        </tr>

                    @endforelse
                </tbody>

            </table>
        </div>

        {{-- Pagination --}}
        @if($academicYears->hasPages())
            <div class="ay-card-footer">
                {{ $academicYears->links() }}
            </div>
        @endif

    </div>

</div>

@endsection
