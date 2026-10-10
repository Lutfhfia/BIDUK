
@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="fw-bold mb-1">Profil Saya</h4>
            <p class="text-muted mb-0">Informasi akun pengguna BIDUK.</p>
        </div>

        <a href="{{ route('profile.edit') }}" class="btn btn-success">
            <i class="bi bi-pencil-square me-1"></i>
            Edit Profil
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"
                aria-label="Tutup"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="rounded-circle bg-success bg-opacity-10 text-success
                    d-flex align-items-center justify-content-center"
                    style="width: 64px; height: 64px; font-size: 28px;">
                    @if ($user->profile_photo_path)
    <img
        src="{{ asset('storage/' . $user->profile_photo_path) }}"
        alt="Foto Profil"
        style="width: 100%; height: 100%; object-fit: cover;"
    >
@else
    <i class="bi bi-person-fill"></i>
@endif
                </div>

                <div>
                    <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                    <span class="text-muted">
                        {{ $user->role?->name ?? 'Role belum ditentukan' }}
                    </span>
                </div>
            </div>

            <hr>

            <h6 class="fw-bold mb-3">Informasi Akun</h6>

            <div class="row g-4">
                <div class="col-md-6">
                    <small class="text-muted d-block mb-1">Nama Lengkap</small>
                    <div class="fw-semibold">{{ $user->name }}</div>
                </div>

                <div class="col-md-6">
                    <small class="text-muted d-block mb-1">Email</small>
                    <div class="fw-semibold">{{ $user->email ?: '-' }}</div>
                </div>

                <div class="col-md-6">
                    <small class="text-muted d-block mb-1">Username</small>
                    <div class="fw-semibold">{{ $user->username ?: '-' }}</div>
                </div>

                <div class="col-md-6">
                    <small class="text-muted d-block mb-1">Role</small>
                    <div class="fw-semibold">
                        {{ $user->role?->name ?? '-' }}
                    </div>
                </div>

                <div class="col-md-6">
                    <small class="text-muted d-block mb-1">Status Akun</small>
                    <div class="fw-semibold">{{ $user->status ?: '-' }}</div>
                </div>

                @if ($user->employee)
                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">NIP</small>
                        <div class="fw-semibold">
                            {{ $user->employee->nip ?: '-' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Jabatan</small>
                        <div class="fw-semibold">
                            {{ $user->employee->position ?: '-' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-muted d-block mb-1">Nomor Telepon</small>
                        <div class="fw-semibold">
                            {{ $user->employee->phone ?: '-' }}
                        </div>
                    </div>
                @endif
            </div>

            <hr class="my-4">

            <div class="small text-muted">
                <i class="bi bi-info-circle me-1"></i>
                Username, role, dan status akun tidak dapat diubah melalui halaman ini.
                Untuk mengganti atau mereset kata sandi, hubungi Admin atau Super Admin.
            </div>
        </div>
    </div>
</div>
@endsection
