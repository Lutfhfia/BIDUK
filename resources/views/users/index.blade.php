@extends('layouts.app')

@section('title', 'Manajemen User')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>

    <li class="breadcrumb-item active" aria-current="page">
        Manajemen User
    </li>
@endsection

@section('content')
@if (session('generated_password'))
    <div class="alert alert-warning border mb-4">
        <div class="fw-semibold mb-2">
            <i class="bi bi-key-fill me-2"></i>
            Password Awal User
        </div>

        <div>
            User berhasil dibuat. Password awal:
            <code class="fs-6">{{ session('generated_password') }}</code>
        </div>

        <div class="small text-muted mt-2">
            Simpan password ini dan berikan kepada pengguna. Password sebaiknya segera diganti setelah login.
        </div>
    </div>
@endif

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-person-gear me-2"
                   style="color: var(--biduk-primary);"></i>
                Manajemen User
            </h4>

            <p class="text-muted mb-0">
                Kelola akun pengguna yang dapat mengakses sistem BIDUK.
            </p>
        </div>

        <a href="{{ route('users.create') }}"
           class="btn btn-biduk-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah User
        </a>
    </div>

    {{-- Pesan sukses --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter --}}
    <div class="biduk-card mb-4">
        <div class="card-header">
            <i class="bi bi-funnel me-2"></i>
            Filter User
        </div>

        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}">
                <div class="row g-3">

                    <div class="col-md-5">
                        <label class="form-label">
                            Cari User
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="Nama, username, atau email..."
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">
                            Role
                        </label>

                        <select name="role_id" class="form-select">
                            <option value="">Semua Role</option>

                            @foreach ($roles as $role)
                                <option
                                    value="{{ $role->id }}"
                                    {{ request('role_id') == $role->id ? 'selected' : '' }}
                                >
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">
                            Status
                        </label>

                        <select name="status" class="form-select">
                            <option value="">Semua</option>
                            <option value="Aktif"
                                {{ request('status') === 'Aktif' ? 'selected' : '' }}>
                                Aktif
                            </option>
                            <option value="Nonaktif"
                                {{ request('status') === 'Nonaktif' ? 'selected' : '' }}>
                                Nonaktif
                            </option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end gap-2">
                        <button type="submit"
                                class="btn btn-biduk-primary w-100">
                            <i class="bi bi-search me-1"></i>
                            Cari
                        </button>

                        <a href="{{ route('users.index') }}"
                           class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Daftar User --}}
    <div class="biduk-card">

        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-people me-2"></i>
                Daftar User
            </div>

            <span class="text-muted small">
                {{ $users->total() }} user
            </span>
        </div>

        <div class="card-body p-0">

            @if ($users->count())

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead>
                            <tr>
                                <th width="60">No</th>
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Pegawai</th>
                                <th>Status</th>
                                <th width="160">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach ($users as $user)
                                <tr>

                                    <td>
                                        {{ $users->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        <div class="fw-semibold">
                                            {{ $user->name }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $user->username ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $user->email ?? '-' }}
                                    </td>

                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $user->role->name ?? '-' }}
                                        </span>
                                    </td>

                                    <td>
                                        {{ $user->employee->name ?? '-' }}
                                    </td>

                                    <td>
                                        @if ($user->status === 'Aktif')
                                            <span class="badge bg-success-subtle text-success">
                                                Aktif
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">
                                                Nonaktif
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex gap-1">

                                            <a href="{{ route('users.show', $user) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Detail">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <a href="{{ route('users.edit', $user) }}"
                                               class="btn btn-sm btn-outline-warning"
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            @if ($user->id !== auth()->id() && !$user->isSuperAdmin())
    <form
        action="{{ route('users.destroy', $user) }}"
        method="POST"
        onsubmit="return confirm('Yakin ingin menghapus user ini?')"
    >
        @csrf
        @method('DELETE')

        <button
            type="submit"
            class="btn btn-sm btn-outline-danger"
            title="Hapus"
        >
            <i class="bi bi-trash"></i>
        </button>
    </form>
@endif
                                        </div>
                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>
                </div>

            @else

                <div class="text-center py-5">
                    <i class="bi bi-people fs-1 text-muted"></i>

                    <h5 class="mt-3">
                        Belum Ada Data User
                    </h5>

                    <p class="text-muted mb-0">
                        Belum ada akun pengguna yang tersedia.
                    </p>
                </div>

            @endif

        </div>

        @if ($users->hasPages())
            <div class="card-footer">
                {{ $users->links() }}
            </div>
        @endif

    </div>

@endsection