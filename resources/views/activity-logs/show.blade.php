@extends('layouts.app')

@section('title', 'Detail Aktivitas')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('activity-logs.index') }}">Log Aktivitas</a>
    </li>
    <li class="breadcrumb-item active">Detail</li>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Detail Aktivitas</h4>
        <p class="text-muted mb-0">
            Informasi lengkap aktivitas yang tercatat dalam sistem.
        </p>
    </div>

    <a href="{{ route('activity-logs.index') }}"
       class="btn btn-biduk-outline">
        <i class="bi bi-arrow-left me-1"></i>
        Kembali
    </a>
</div>


{{-- Informasi Aktivitas --}}
<div class="biduk-card mb-4">

    <div class="card-header">
        <i class="bi bi-clock-history me-2"></i>
        Informasi Aktivitas
    </div>

    <div class="card-body">

        <div class="row g-4">

            {{-- Waktu --}}
            <div class="col-md-6">
                <label class="form-label text-muted">
                    Waktu
                </label>

                <div class="fw-semibold">
                    {{ $activityLog->created_at?->format('d F Y, H:i:s') ?? '-' }}
                </div>
            </div>

            {{-- Pengguna --}}
            <div class="col-md-6">
                <label class="form-label text-muted">
                    Pengguna
                </label>

                <div class="fw-semibold">
                    {{ $activityLog->user?->name ?? 'Sistem' }}
                </div>

                @if($activityLog->user?->role)
                    <small class="text-muted">
                        {{ $activityLog->user->role->name }}
                    </small>
                @endif
            </div>

            {{-- Aktivitas --}}
            <div class="col-md-6">
                <label class="form-label text-muted">
                    Aktivitas
                </label>

                @php
                    $badgeClass = match($activityLog->action) {
                        'created' => 'bg-success',
                        'updated' => 'bg-warning text-dark',
                        'deleted' => 'bg-danger',
                        'login' => 'bg-primary',
                        'logout' => 'bg-secondary',
                        default => 'bg-dark',
                    };
                @endphp

                <div>
                    <span class="badge {{ $badgeClass }}">
                        {{ ucfirst($activityLog->action) }}
                    </span>
                </div>
            </div>

            {{-- Modul --}}
            <div class="col-md-6">
                <label class="form-label text-muted">
                    Modul
                </label>

                <div class="fw-semibold">
                    {{ ucwords(str_replace('_', ' ', $activityLog->module ?? '-')) }}
                </div>
            </div>

            {{-- Deskripsi --}}
            <div class="col-12">
                <label class="form-label text-muted">
                    Deskripsi
                </label>

                <div class="p-3 bg-light rounded">
                    {{ $activityLog->description ?? '-' }}
                </div>
            </div>

        </div>

    </div>
</div>


{{-- Informasi Data --}}
@if($activityLog->subject_type || $activityLog->subject_id)

<div class="biduk-card mb-4">

    <div class="card-header">
        <i class="bi bi-database me-2"></i>
        Data yang Terpengaruh
    </div>

    <div class="card-body">

        <div class="row g-4">

            <div class="col-md-6">
                <label class="form-label text-muted">
                    Tipe Data
                </label>

                <div class="fw-semibold">
                    {{ class_basename($activityLog->subject_type ?? '-') }}
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted">
                    ID Data
                </label>

                <div class="fw-semibold">
                    {{ $activityLog->subject_id ?? '-' }}
                </div>
            </div>

        </div>

    </div>

</div>

@endif


{{-- Perubahan Data --}}
@if($activityLog->old_values || $activityLog->new_values)

<div class="biduk-card mb-4">

    <div class="card-header">
        <i class="bi bi-arrow-left-right me-2"></i>
        Perubahan Data
    </div>

    <div class="card-body">

        <div class="row g-4">

            {{-- Nilai Lama --}}
            <div class="col-md-6">

                <label class="form-label text-muted">
                    Data Sebelum
                </label>

                @if($activityLog->old_values)

                    <div class="bg-light rounded p-3">

                        @foreach($activityLog->old_values as $key => $value)

                            <div class="mb-2">

                                <div class="fw-semibold">
                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                </div>

                                <div class="text-muted">
                                    {{ is_array($value) ? json_encode($value) : ($value ?? '-') }}
                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="text-muted">
                        Tidak ada data sebelumnya.
                    </div>

                @endif

            </div>


            {{-- Nilai Baru --}}
            <div class="col-md-6">

                <label class="form-label text-muted">
                    Data Sesudah
                </label>

                @if($activityLog->new_values)

                    <div class="bg-light rounded p-3">

                        @foreach($activityLog->new_values as $key => $value)

                            <div class="mb-2">

                                <div class="fw-semibold">
                                    {{ ucwords(str_replace('_', ' ', $key)) }}
                                </div>

                                <div class="text-muted">
                                    {{ is_array($value) ? json_encode($value) : ($value ?? '-') }}
                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="text-muted">
                        Tidak ada data sesudahnya.
                    </div>

                @endif

            </div>

        </div>

    </div>

</div>

@endif


{{-- Informasi Akses --}}
<div class="biduk-card">

    <div class="card-header">
        <i class="bi bi-shield-lock me-2"></i>
        Informasi Akses
    </div>

    <div class="card-body">

        <div class="row g-4">

            <div class="col-md-6">
                <label class="form-label text-muted">
                    IP Address
                </label>

                <div class="fw-semibold">
                    {{ $activityLog->ip_address ?? '-' }}
                </div>
            </div>

            <div class="col-md-6">
                <label class="form-label text-muted">
                    User Agent
                </label>

                <div class="fw-semibold text-break">
                    {{ $activityLog->user_agent ?? '-' }}
                </div>
            </div>

        </div>

    </div>

</div>

@endsection