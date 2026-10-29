@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('breadcrumb')
    <li class="breadcrumb-item active">Log Aktivitas</li>
@endsection

@section('content')

<style>
    /* ==========================================
       BIDUK - LOG AKTIVITAS
       Tema seragam dengan halaman lainnya
    ========================================== */

    .activity-logs-page {
        --al-green: #16834f;
        --al-green-dark: #106b40;
        --al-border: #e1ebe5;
        --al-muted: #718078;
        color: #25372c;
    }

    /* Header gradasi */
    .activity-logs-page .al-hero {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 23px 26px;
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

    .activity-logs-page .al-hero-content {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .activity-logs-page .al-hero-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 58px;
        height: 58px;
        border: 1px solid #d1eadd;
        border-radius: 15px;
        background: linear-gradient(
            145deg,
            #e4f6eb,
            #cfeedd
        );
        color: var(--al-green);
        font-size: 26px;
    }

    .activity-logs-page .al-hero h4 {
        margin-bottom: 6px;
        color: #1f3026;
        font-size: clamp(1.35rem, 2vw, 1.75rem);
        font-weight: 750;
    }

    .activity-logs-page .al-hero p {
        margin: 0;
        color: #66766c;
        font-size: .9rem;
        line-height: 1.6;
    }

    /* Kartu putih */
    .activity-logs-page .al-card {
        overflow: hidden;
        border: 1px solid var(--al-border) !important;
        border-radius: 15px !important;
        background: #fff;
        box-shadow: 0 5px 19px rgba(30, 70, 45, .045) !important;
    }

    /* Header kartu */
    .activity-logs-page .al-card > .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 15px 20px;
        border-bottom: 1px solid var(--al-border);
        background: linear-gradient(
            90deg,
            #fff,
            #f7fbf8
        );
        color: #293a2f;
        font-size: .96rem;
        font-weight: 700;
    }

    .activity-logs-page .al-card > .card-header > i,
    .activity-logs-page .al-card > .card-header span > i {
        color: var(--al-green);
    }

    /* Filter */
    .activity-logs-page .al-filter-card > .card-body {
        padding: 20px;
    }

    .activity-logs-page .al-filter-card .form-label {
        margin-bottom: 8px;
        color: #526257;
        font-size: .82rem;
        font-weight: 600;
    }

    .activity-logs-page .al-filter-card .form-control,
    .activity-logs-page .al-filter-card .form-select {
        min-height: 41px;
        border: 1px solid #dce6df;
        border-radius: 8px;
        background-color: #fff;
        color: #34463a;
        font-size: .84rem;
        box-shadow: none;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .activity-logs-page .al-filter-card .form-control:focus,
    .activity-logs-page .al-filter-card .form-select:focus {
        border-color: #70bd91;
        box-shadow: 0 0 0 3px rgba(22, 131, 79, .09);
    }

    /* Tombol filter */
    .activity-logs-page .btn-biduk-primary {
        border-color: transparent;
        border-radius: 8px;
        background: linear-gradient(
            105deg,
            #16834f,
            #20b968
        );
        color: #fff;
        font-weight: 600;
    }

    .activity-logs-page .btn-biduk-primary:hover {
        background: linear-gradient(
            105deg,
            #116d42,
            #159653
        );
        color: #fff;
    }

    .activity-logs-page .btn-biduk-outline {
        border: 1px solid #a9d8bc;
        border-radius: 8px;
        background: #fff;
        color: var(--al-green);
        font-weight: 600;
    }

    .activity-logs-page .btn-biduk-outline:hover {
        background: #e5f5eb;
        color: var(--al-green-dark);
    }

    /* Jumlah aktivitas */
    .activity-logs-page .al-count {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border: 1px solid #dceee3;
        border-radius: 8px;
        background: #f0f8f3;
        color: var(--al-green-dark);
        font-size: .77rem;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Tabel riwayat */
    .activity-logs-page .al-table-wrap {
        width: 100%;
    }

    .activity-logs-page .al-table {
        width: 100%;
        margin-bottom: 0;
        vertical-align: middle;
    }

    .activity-logs-page .al-table thead th {
        padding: 13px 12px;
        border-top: 0;
        border-bottom: 1px solid #d8e9de;
        background: linear-gradient(
            90deg,
            #e5f4eb,
            #f0f8f3 50%,
            #e5f4eb
        );
        color: #3e5747;
        font-size: .77rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .activity-logs-page .al-table tbody td {
        padding: 12px;
        border-color: #e7eee9;
        background: #fff;
        color: #435247;
        font-size: .83rem;
        vertical-align: middle;
    }

    .activity-logs-page .al-table tbody tr:hover td {
        background: #f7fbf8;
    }

    .activity-logs-page .al-table .fw-semibold {
        color: #293a2f;
    }

    .activity-logs-page .al-table small {
        font-size: .76rem;
    }

    /* Badge aktivitas: warna tetap membedakan jenis aktivitas */
    .activity-logs-page .al-action-badge {
        display: inline-block;
        padding: 5px 8px;
        border: 0;
        border-radius: 6px;
        font-size: .73rem;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
    }

    /* Tombol detail */
    .activity-logs-page .al-detail-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        padding: 0;
        border: 1px solid #b5dbc4;
        border-radius: 8px;
        background: #fff;
        color: var(--al-green);
        transition: background .18s ease, color .18s ease;
    }

    .activity-logs-page .al-detail-btn:hover {
        border-color: var(--al-green);
        background: var(--al-green);
        color: #fff;
    }

    /* Data kosong */
    .activity-logs-page .al-empty-state {
        padding: 45px 20px;
        text-align: center;
    }

    .activity-logs-page .al-empty-state > i {
        color: #8aab96;
        font-size: 2.8rem;
    }

    .activity-logs-page .al-empty-state h5 {
        margin-top: 13px;
        color: #304237;
        font-size: 1rem;
        font-weight: 700;
    }

    .activity-logs-page .al-empty-state p {
        color: var(--al-muted);
        font-size: .85rem;
    }

    /* Pagination */
    .activity-logs-page .al-card > .card-footer {
        padding: 14px 20px;
        border-top: 1px solid #edf1ee !important;
        background: #fff !important;
    }

    .activity-logs-page .pagination {
        gap: 5px;
        margin-bottom: 0;
    }

    .activity-logs-page .pagination .page-link {
        min-width: 34px;
        border: 1px solid #e0e9e3;
        border-radius: 7px;
        background: #fff;
        color: var(--al-green);
        font-size: .82rem;
        text-align: center;
    }

    .activity-logs-page .pagination .page-item.active .page-link {
        border-color: var(--al-green);
        background: var(--al-green);
        color: #fff;
    }

    .activity-logs-page .alert {
        border-radius: 10px;
    }

    /* Tampilan layar kecil */
    @media (max-width: 767.98px) {
        .activity-logs-page .al-hero {
            padding: 19px;
        }

        .activity-logs-page .al-hero-content {
            gap: 12px;
        }

        .activity-logs-page .al-hero-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            font-size: 22px;
        }

        .activity-logs-page .al-hero p {
            font-size: .82rem;
        }

        .activity-logs-page .al-filter-card > .card-body {
            padding: 16px;
        }

        .activity-logs-page .al-table {
            min-width: 1050px;
        }

        .activity-logs-page .al-table thead th,
        .activity-logs-page .al-table tbody td {
            padding: 11px;
        }

        .activity-logs-page .al-card > .card-header,
        .activity-logs-page .al-card > .card-footer {
            padding: 14px 16px;
        }
    }
</style>

<div class="activity-logs-page">



<div class="al-hero mb-4">
    <div class="al-hero-content">
        <div class="al-hero-icon" aria-hidden="true">
            <i class="bi bi-clock-history"></i>
        </div>

        <div>
            <h4>Log Aktivitas</h4>
            <p>
                Riwayat aktivitas pengguna yang tercatat di sistem BIDUK.
            </p>
        </div>
    </div>
</div>


{{-- Filter --}}
<div class="biduk-card al-card al-filter-card mb-4">
    <div class="card-header">
        <i class="bi bi-funnel me-2"></i>
        Filter Aktivitas
    </div>

    <div class="card-body">

        <form method="GET" action="{{ route('activity-logs.index') }}">

            <div class="row g-3">

                {{-- Pengguna --}}
                <div class="col-md-6 col-lg-3">
                    <label for="user_id" class="form-label">
                        Pengguna
                    </label>

                    <select name="user_id"
                            id="user_id"
                            class="form-select">

                        <option value="">Semua Pengguna</option>

                        @foreach($users as $user)
                            <option value="{{ $user->id }}"
                                @selected(request('user_id') == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- Modul --}}
                <div class="col-md-6 col-lg-3">
                    <label for="module" class="form-label">
                        Modul
                    </label>

                    <select name="module"
                            id="module"
                            class="form-select">

                        <option value="">Semua Modul</option>

                        @foreach($modules as $module)
                            <option value="{{ $module }}"
                                @selected(request('module') == $module)>
                                {{ ucwords(str_replace('_', ' ', $module)) }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- Aktivitas --}}
                <div class="col-md-6 col-lg-2">
                    <label for="action" class="form-label">
                        Aktivitas
                    </label>

                    <select name="action"
                            id="action"
                            class="form-select">

                        <option value="">Semua Aktivitas</option>

                        @foreach($actions as $action)
                            <option value="{{ $action }}"
                                @selected(request('action') == $action)>
                                {{ ucfirst($action) }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- Tanggal Mulai --}}
                <div class="col-md-6 col-lg-2">
                    <label for="start_date" class="form-label">
                        Dari
                    </label>

                    <input type="date"
                           name="start_date"
                           id="start_date"
                           class="form-control"
                           value="{{ request('start_date') }}">
                </div>

                {{-- Tanggal Akhir --}}
                <div class="col-md-6 col-lg-2">
                    <label for="end_date" class="form-label">
                        Sampai
                    </label>

                    <input type="date"
                           name="end_date"
                           id="end_date"
                           class="form-control"
                           value="{{ request('end_date') }}">
                </div>

            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">

                <a href="{{ route('activity-logs.index') }}"
                   class="btn btn-biduk-outline">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Reset
                </a>

                <button type="submit"
                        class="btn btn-biduk-primary">
                    <i class="bi bi-search me-1"></i>
                    Terapkan Filter
                </button>

            </div>

        </form>

    </div>
</div>

{{-- Data Log --}}
<div class="biduk-card al-card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <span>
            <i class="bi bi-clock-history me-2"></i>
            Riwayat Aktivitas
        </span>

        <span class="al-count">
    {{ $logs->total() }} Aktivitas
</span>

    </div>

    <div class="card-body p-0">

        @if($logs->count())

        <div class="table-responsive al-table-wrap">

        <table class="table biduk-table al-table mb-0">

                    <thead>
                        <tr>
                            <th width="60">No</th>
                            <th>Waktu</th>
                            <th>Pengguna</th>
                            <th>Aktivitas</th>
                            <th>Modul</th>
                            <th>Deskripsi</th>
                            <th width="80">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($logs as $log)

                            <tr>

                                <td>
                                    {{ $logs->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $log->created_at->format('d/m/Y') }}
                                    </div>

                                    <small class="text-muted">
                                        {{ $log->created_at->format('H:i:s') }}
                                    </small>
                                </td>

                                <td>
                                    @if($log->user)
                                        <div class="fw-semibold">
                                            {{ $log->user->name }}
                                        </div>

                                        <small class="text-muted">
                                            {{ $log->user->role?->name ?? '-' }}
                                        </small>
                                    @else
                                        <span class="text-muted">
                                            Sistem
                                        </span>
                                    @endif
                                </td>

                                <td>

                                    @php
                                        $badgeClass = match($log->action) {
                                            'created' => 'bg-success',
                                            'updated' => 'bg-warning text-dark',
                                            'deleted' => 'bg-danger',
                                            'login' => 'bg-primary',
                                            'logout' => 'bg-secondary',
                                            default => 'bg-dark',
                                        };
                                    @endphp

                                    <span class="badge {{ $badgeClass }} al-action-badge">
    {{ ucfirst($log->action) }}
</span>

                                </td>

                                <td>
                                    {{ ucwords(str_replace('_', ' ', $log->module)) }}
                                </td>

                                <td>
                                    {{ $log->description ?? '-' }}
                                </td>

                                <td>

                                    <a href="{{ route('activity-logs.show', $log) }}"
                                      class="al-detail-btn"
                                       title="Detail">

                                        <i class="bi bi-eye"></i>

                                    </a>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

        <div class="al-empty-state">

                <i class="bi bi-clock-history"></i>

                <h5>Belum ada aktivitas</h5>

                <p class="mb-0">
                    Belum ada aktivitas yang sesuai dengan filter.
                </p>

            </div>

        @endif

    </div>

    @if($logs->hasPages())

        <div class="card-footer bg-white border-0 py-3">

            {{ $logs->links() }}

        </div>
        </div>

    @endif

</div>

@endsection