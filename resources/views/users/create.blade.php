@extends('layouts.app')

@section('title', 'Tambah User')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>

    <li class="breadcrumb-item">
        <a href="{{ route('users.index') }}">Manajemen User</a>
    </li>

    <li class="breadcrumb-item active" aria-current="page">
        Tambah User
    </li>
@endsection

@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-person-plus me-2"
                   style="color: var(--biduk-primary);"></i>
                Tambah User
            </h4>

            <p class="text-muted mb-0">
                Buat akun pengguna baru untuk mengakses sistem BIDUK.
            </p>
        </div>
    </div>

    <div class="biduk-card">

        <div class="card-header">
            <i class="bi bi-person-plus me-2"
               style="color: var(--biduk-primary);"></i>
            Form Tambah User
        </div>

        <div class="card-body">

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="row g-4">

                    {{-- Nama --}}
                    <div class="col-md-6">
                        <label for="name" class="form-label">
                            Nama <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            required
                        >

                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Username --}}
                    <div class="col-md-6">
                        <label for="username" class="form-label">
                            Username <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="username"
                            id="username"
                            class="form-control @error('username') is-invalid @enderror"
                            value="{{ old('username') }}"
                            required
                        >

                        <div class="form-text">
                            Digunakan untuk login ke sistem.
                        </div>

                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <label for="email" class="form-label">
                            Email <span class="text-danger">*</span>
                        </label>

                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-control @error('email') is-invalid @enderror"
                            value="{{ old('email') }}"
                            required
                        >

                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div class="col-md-6">
                        <label for="role_id" class="form-label">
                            Role <span class="text-danger">*</span>
                        </label>

                        <select
                            name="role_id"
                            id="role_id"
                            class="form-select @error('role_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Pilih Role</option>

                            @foreach ($roles as $role)
                                <option
                                    value="{{ $role->id }}"
                                    {{ old('role_id') == $role->id ? 'selected' : '' }}
                                >
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Pegawai --}}
                    <div class="col-md-6">
                        <label for="employee_id" class="form-label">
                            Pegawai
                        </label>

                        <select
                            name="employee_id"
                            id="employee_id"
                            class="form-select @error('employee_id') is-invalid @enderror"
                        >
                            <option value="">
                                Tidak dikaitkan dengan pegawai
                            </option>

                            @foreach ($employees as $employee)
                                <option
                                    value="{{ $employee->id }}"
                                    {{ old('employee_id') == $employee->id ? 'selected' : '' }}
                                >
                                    {{ $employee->name }}
                                    @if ($employee->nip)
                                        - {{ $employee->nip }}
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        <div class="form-text">
                            Pilih pegawai apabila akun ini milik pegawai sekolah.
                        </div>

                        @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Status --}}
                    <div class="col-md-6">
                        <label for="status" class="form-label">
                            Status <span class="text-danger">*</span>
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-select @error('status') is-invalid @enderror"
                            required
                        >
                            <option value="Aktif"
                                {{ old('status', 'Aktif') === 'Aktif' ? 'selected' : '' }}>
                                Aktif
                            </option>

                            <option value="Nonaktif"
                                {{ old('status') === 'Nonaktif' ? 'selected' : '' }}>
                                Nonaktif
                            </option>
                        </select>

                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Informasi Password --}}
                    <div class="col-12">
                        <div class="alert alert-light border mb-0">
                            <i class="bi bi-shield-lock me-2"></i>
                            Password awal akan dibuat otomatis oleh sistem setelah akun berhasil dibuat.
                        </div>
                    </div>

                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">

                    <a
                        href="{{ route('users.index') }}"
                        class="btn btn-biduk-outline"
                    >
                        <i class="bi bi-arrow-left me-1"></i>
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn btn-biduk-primary"
                    >
                        <i class="bi bi-check-lg me-1"></i>
                        Simpan User
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection