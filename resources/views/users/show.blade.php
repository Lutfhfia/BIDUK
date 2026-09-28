@extends('layouts.app')

@section('title', 'Detail User')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>

    <li class="breadcrumb-item">
        <a href="{{ route('users.index') }}">Manajemen User</a>
    </li>

    <li class="breadcrumb-item active">
        Detail User
    </li>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <i class="bi bi-person-vcard me-2"
               style="color: var(--biduk-primary);"></i>
            Detail User
        </h4>

        <p class="text-muted mb-0">
            Informasi akun pengguna sistem BIDUK.
        </p>
    </div>

    <a href="{{ route('users.index') }}"
       class="btn btn-biduk-outline">
        <i class="bi bi-arrow-left me-1"></i>
        Kembali
    </a>
</div>

<div class="biduk-card">

    <div class="card-header">
        <i class="bi bi-person-circle me-2"></i>
        Informasi Akun
    </div>

    <div class="card-body">

        <div class="row g-4">

            <div class="col-md-6">
                <div class="text-muted small mb-1">
                    Nama
                </div>

                <div class="fw-semibold">
                    {{ $user->name }}
                </div>
            </div>

            <div class="col-md-6">
                <div class="text-muted small mb-1">
                    Username
                </div>

                <div class="fw-semibold">
                    {{ $user->username ?? '-' }}
                </div>
            </div>

            <div class="col-md-6">
                <div class="text-muted small mb-1">
                    Email
                </div>

                <div class="fw-semibold">
                    {{ $user->email ?? '-' }}
                </div>
            </div>

            <div class="col-md-6">
                <div class="text-muted small mb-1">
                    Role
                </div>

                <span class="badge bg-light text-dark border">
                    {{ $user->role->name ?? '-' }}
                </span>
            </div>

            <div class="col-md-6">
                <div class="text-muted small mb-1">
                    Pegawai
                </div>

                <div class="fw-semibold">
                    {{ $user->employee->name ?? '-' }}
                </div>
            </div>

            <div class="col-md-6">
                <div class="text-muted small mb-1">
                    Status
                </div>

                @if ($user->status === 'Aktif')
                    <span class="badge bg-success-subtle text-success">
                        Aktif
                    </span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary">
                        Nonaktif
                    </span>
                @endif
            </div>

            <div class="col-md-6">
                <div class="text-muted small mb-1">
                    Login Terakhir
                </div>

                <div class="fw-semibold">
                    {{ $user->last_login_at?->format('d/m/Y H:i') ?? 'Belum pernah login' }}
                </div>
            </div>

            <div class="col-md-6">
                <div class="text-muted small mb-1">
                    Akun Dibuat
                </div>

                <div class="fw-semibold">
                    {{ $user->created_at?->format('d/m/Y H:i') ?? '-' }}
                </div>
            </div>

        </div>

    </div>
</div>

@endsection