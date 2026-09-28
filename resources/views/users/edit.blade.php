@extends('layouts.app')

@section('title', 'Edit User')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>

    <li class="breadcrumb-item">
        <a href="{{ route('users.index') }}">Manajemen User</a>
    </li>

    <li class="breadcrumb-item active">
        Edit User
    </li>
@endsection

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <i class="bi bi-pencil-square me-2"
               style="color: var(--biduk-primary);"></i>
            Edit User
        </h4>

        <p class="text-muted mb-0">
            Perbarui data akun pengguna sistem BIDUK.
        </p>
    </div>
</div>

<div class="biduk-card">

    <div class="card-header">
        <i class="bi bi-person-gear me-2"
           style="color: var(--biduk-primary);"></i>
        Form Edit User
    </div>

    <div class="card-body">

        <form
            action="{{ route('users.update', $user) }}"
            method="POST"
        >
            @csrf
            @method('PUT')

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
                        value="{{ old('name', $user->name) }}"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
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
                        value="{{ old('username', $user->username) }}"
                        required
                    >

                    @error('username')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
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
                        value="{{ old('email', $user->email) }}"
                        required
                    >

                    @error('email')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
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
                                {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}
                            >
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('role_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
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
                                {{ old('employee_id', $user->employee_id) == $employee->id ? 'selected' : '' }}
                            >
                                {{ $employee->name }}
                                @if ($employee->nip)
                                    - {{ $employee->nip }}
                                @endif
                            </option>
                        @endforeach
                    </select>

                    @error('employee_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
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
                            {{ old('status', $user->status) === 'Aktif' ? 'selected' : '' }}>
                            Aktif
                        </option>

                        <option value="Nonaktif"
                            {{ old('status', $user->status) === 'Nonaktif' ? 'selected' : '' }}>
                            Nonaktif
                        </option>
                    </select>

                    @error('status')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="col-12">
                    <div class="alert alert-light border mb-0">
                        <i class="bi bi-shield-lock me-2"></i>
                        Password tidak diubah pada form ini. Gunakan fitur reset password
                        untuk mengganti password akun.
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
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>
</div>

@endsection