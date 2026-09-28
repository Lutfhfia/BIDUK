@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('breadcrumb')
    <li class="breadcrumb-item active">Log Aktivitas</li>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Log Aktivitas</h4>
        <p class="text-muted mb-0">
            Riwayat aktivitas pengguna yang tercatat di sistem BIDUK.
        </p>
    </div>
</div>

{{-- Filter --}}
<div class="biduk-card mb-4">
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
<div class="biduk-card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <span>
            <i class="bi bi-clock-history me-2"></i>
            Riwayat Aktivitas
        </span>

        <span class="badge bg-primary">
            {{ $logs->total() }} Aktivitas
        </span>

    </div>

    <div class="card-body p-0">

        @if($logs->count())

            <div class="table-responsive">

                <table class="table biduk-table mb-0">

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

                                    <span class="badge {{ $badgeClass }}">
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
                                       class="btn btn-sm btn-outline-secondary"
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

            <div class="empty-state">

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

    @endif

</div>

@endsection