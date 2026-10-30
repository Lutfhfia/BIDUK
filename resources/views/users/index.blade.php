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

<style>
    /* Tema BIDUK untuk Manajemen User */
    .users-management-page {
        --um-green: #16834f;
        --um-green-dark: #106b40;
        --um-green-bright: #20b968;
        --um-border: #e1ebe5;
        --um-muted: #718078;
        color: #25372c;
    }

    /* Header gradasi hijau */
    .users-management-page .um-hero {
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

    .users-management-page .um-hero::after {
        position: absolute;
        top: 50%;
        right: 19%;
        width: 175px;
        height: 175px;
        border-radius: 50%;
        background: radial-gradient(
            circle,
            rgba(32, 185, 104, .09),
            rgba(32, 185, 104, 0)
        );
        content: "";
        pointer-events: none;
        transform: translateY(-50%);
    }

    .users-management-page .um-hero > * {
        position: relative;
        z-index: 1;
    }

    .users-management-page .um-hero h4 {
        margin-bottom: 6px;
        color: #1f3026;
        font-size: clamp(1.35rem, 2vw, 1.75rem);
        font-weight: 750;
    }

    .users-management-page .um-hero h4 i {
        color: var(--um-green) !important;
    }

    .users-management-page .um-hero p {
        margin: 0;
        color: #66766c;
        font-size: .9rem;
        line-height: 1.6;
    }

    .users-management-page .um-hero .btn-biduk-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        min-height: 41px;
        padding: 9px 16px;
        border: 1px solid transparent;
        border-radius: 9px;
        background: linear-gradient(
            105deg,
            #16834f,
            #20b968
        );
        box-shadow: 0 4px 11px rgba(22, 131, 79, .13);
        color: #fff;
        font-size: .86rem;
        font-weight: 600;
        white-space: nowrap;
        transition: .18s ease;
    }

    .users-management-page .um-hero .btn-biduk-primary:hover {
        background: linear-gradient(
            105deg,
            #116d42,
            #159653
        );
        color: #fff;
        transform: translateY(-1px);
    }

    /* Kartu filter dan tabel */
    .users-management-page .um-card {
        overflow: visible;
        border: 1px solid var(--um-border) !important;
        border-radius: 15px !important;
        background: #fff;
        box-shadow: 0 5px 19px rgba(30, 70, 45, .045) !important;
    }

    .users-management-page .um-filter-card .card-header,
    .users-management-page .um-table-card > .card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        padding: 15px 20px;
        border-bottom: 1px solid var(--um-border);
        border-radius: 15px 15px 0 0;
        background: linear-gradient(
            90deg,
            #fff,
            #f7fbf8
        );
        color: #293a2f;
        font-size: .96rem;
        font-weight: 700;
    }

    .users-management-page .um-filter-card .card-header i,
    .users-management-page .um-table-card .card-header i {
        color: var(--um-green);
    }

    .users-management-page .um-filter-card .card-body {
        padding: 20px;
    }

    /* Label filter */
    .users-management-page .um-filter-card .form-label {
        display: block;
        margin-bottom: 8px;
        color: #526257;
        font-size: .82rem;
        font-weight: 600;
    }

    /* Input pencarian dan dropdown */
    .users-management-page .um-filter-card .form-control,
    .users-management-page .um-filter-card .form-select {
        min-height: 41px;
        border: 1px solid #dce6df;
        border-radius: 8px;
        background-color: #fff;
        color: #34463a;
        font-size: .84rem;
        box-shadow: none;
        transition: border-color .18s ease, box-shadow .18s ease;
    }

    .users-management-page .um-filter-card .form-control:focus,
    .users-management-page .um-filter-card .form-select:focus {
        border-color: #70bd91;
        box-shadow: 0 0 0 3px rgba(22, 131, 79, .09);
    }

    .users-management-page .um-filter-card .btn-biduk-primary {
        border: 1px solid transparent;
        border-radius: 8px;
        background: linear-gradient(
            105deg,
            #16834f,
            #20b968
        );
        color: #fff;
        font-weight: 600;
    }

    .users-management-page .um-filter-card .btn-biduk-primary:hover {
        background: linear-gradient(
            105deg,
            #116d42,
            #159653
        );
        color: #fff;
    }

    .users-management-page .um-filter-card .btn-outline-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 41px;
        min-height: 41px;
        border-color: #b8d9c4;
        border-radius: 8px;
        background: #fff;
        color: var(--um-green);
    }

    .users-management-page .um-filter-card .btn-outline-secondary:hover {
        background: #e5f5eb;
        color: var(--um-green-dark);
    }

    /* Header daftar user */
    .users-management-page .um-table-card > .card-header {
        padding: 17px 21px;
    }

    .users-management-page .um-user-count {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border: 1px solid #dceee3;
        border-radius: 8px;
        background: #f0f8f3;
        color: var(--um-green-dark);
        font-size: .78rem;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Tabel */
    .users-management-page .um-table-card .table-responsive {
        width: 100%;
    }

    .users-management-page .um-table-card table {
        width: 100%;
        margin: 0;
        vertical-align: middle;
    }

    .users-management-page .um-table-card table thead th {
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
        font-size: .78rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .users-management-page .um-table-card table tbody td {
        padding: 11px 12px;
        border-color: #e7eee9;
        background: #fff;
        color: #435247;
        font-size: .83rem;
        vertical-align: middle;
    }

    .users-management-page .um-table-card table tbody tr:hover td {
        background: #f7fbf8;
    }

    .users-management-page .um-table-card table tbody td .fw-semibold {
        color: #293a2f;
    }

    /* Badge role */
    .users-management-page .um-role-badge {
        display: inline-block;
        padding: 5px 8px;
        border: 1px solid #e1eae4;
        border-radius: 6px;
        background: #f5f8f6;
        color: #526257;
        font-size: .73rem;
        font-weight: 600;
        white-space: nowrap;
    }

    /* Badge status kecil tanpa ikon */
    .users-management-page .um-status {
        display: inline-block;
        padding: 4px 8px;
        border: 0;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.2;
        white-space: nowrap;
    }

    .users-management-page .um-status-active {
        background: #d4edda;
        color: #087443;
    }

    .users-management-page .um-status-inactive {
        background: #e9ecef;
        color: #687781;
    }

    /* Tombol aksi tetap mempertahankan warna dan fungsi */
    .users-management-page .um-table-card td .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        min-height: 32px;
        padding: 5px 8px;
        border-radius: 7px;
        font-size: .82rem;
    }

    /* Footer dan pagination */
    .users-management-page .um-table-card .card-footer {
        padding: 14px 20px;
        border-top: 1px solid #edf1ee;
        border-radius: 0 0 15px 15px;
        background: #fff;
    }

    .users-management-page .um-table-card .pagination {
        gap: 5px;
        margin: 0;
    }

    .users-management-page .um-table-card .pagination .page-link {
        min-width: 34px;
        border: 1px solid #e0e9e3;
        border-radius: 7px;
        background: #fff;
        color: var(--um-green);
        font-size: .82rem;
        text-align: center;
    }

    .users-management-page .um-table-card .pagination .page-item.active .page-link {
        border-color: var(--um-green);
        background: var(--um-green);
        color: #fff;
    }

    /* Alert password dan notifikasi */
    .users-management-page .alert {
        border-radius: 10px;
    }

    /* Responsif */
    @media (max-width: 767.98px) {
        .users-management-page .um-hero {
            align-items: flex-start;
            flex-direction: column;
            gap: 15px;
            padding: 20px;
        }

        .users-management-page .um-hero::after {
            right: 2%;
        }

        .users-management-page .um-hero > div:last-child,
        .users-management-page .um-hero .btn-biduk-primary {
            width: 100%;
        }

        .users-management-page .um-filter-card .card-body {
            padding: 16px;
        }

        .users-management-page .um-table-card table {
            min-width: 1050px;
        }

        .users-management-page .um-table-card > .card-header {
            padding: 15px;
        }

        .users-management-page .um-table-card .card-footer {
            padding: 14px 15px;
        }
    }

/* Dropdown aksi Manajemen User */
.users-management-page .um-action-toggle {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 35px;
    height: 35px;
    padding: 0;
    border: 1px solid #e1ebe5;
    border-radius: 8px;
    background: #f7faf8;
    color: #52665a;
    font-size: 1rem;
    transition: background .18s ease, color .18s ease;
}

.users-management-page .um-action-toggle:hover,
.users-management-page .um-action-toggle[aria-expanded="true"] {
    border-color: #c2e2cf;
    background: #e5f5eb;
    color: #16834f;
}

.users-management-page .dropdown-menu {
    min-width: 185px;
    padding: 5px;
    border: 1px solid #e1ebe5;
    border-radius: 10px;
    box-shadow: 0 8px 24px rgba(27, 54, 37, .12);
}

.users-management-page .dropdown-item {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 9px 10px;
    border-radius: 6px;
    color: #46564b;
    font-size: .83rem;
}

.users-management-page .dropdown-item:hover {
    background: #eef8f2;
    color: #106b40;
}

.users-management-page .dropdown-item i {
    width: 16px;
    text-align: center;
}

.users-management-page .dropdown-item.text-danger:hover {
    background: #fff0f0;
    color: #dc3545;
}

</style>

<div class="users-management-page">

@if (session('reset_password'))
    <div class="alert alert-warning border mb-4">
        <div class="fw-semibold mb-2">
            <i class="bi bi-key-fill me-2"></i>
            Password Baru
        </div>

        <div class="mb-2">
            Password akun
            <strong>{{ session('reset_password_user') }}</strong>
            berhasil direset.
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <code id="resetPasswordValue" class="fs-6">
                {{ session('reset_password') }}
            </code>

            <button
                type="button"
                class="btn btn-sm btn-outline-dark"
                onclick="copyPassword(this, 'resetPasswordValue')"
            >
                <i class="bi bi-copy me-1"></i>
                Salin Password
            </button>
        </div>

        <div class="small text-muted mt-2">
            Berikan password sementara ini kepada pengguna dan sarankan untuk
            segera menggantinya setelah berhasil login.
        </div>
    </div>
@endif
@if (session('generated_password'))
    <div class="alert alert-warning border mb-4">
        <div class="fw-semibold mb-2">
            <i class="bi bi-key-fill me-2"></i>
            Password Awal User
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            User berhasil dibuat. Password awal:
            <code id="generatedPasswordValue" class="fs-6">{{ session('generated_password') }}</code>

            <button
                type="button"
                class="btn btn-sm btn-outline-dark"
                onclick="copyPassword(this, 'generatedPasswordValue')"
            >
                <i class="bi bi-copy me-1"></i>
                Salin Password
            </button>
        </div>

        <div class="small text-muted mt-2">
            Simpan password ini dan berikan kepada pengguna. Password sebaiknya segera diganti setelah login.
        </div>
    </div>
@endif

    {{-- Header --}}
    <div class="um-hero mb-4">
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
    <div class="biduk-card um-card um-filter-card mb-4">
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
    <div class="biduk-card um-card um-table-card">

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
                                    <span class="um-role-badge">
    {{ $user->role->name ?? '-' }}
</span>
                                    </td>

                                    <td>
                                        {{ $user->employee->name ?? '-' }}
                                    </td>


{{-- Status --}}
<td>
    @if ($user->status === 'Aktif')
        <span class="um-status um-status-active">
            Aktif
        </span>
    @else
        <span class="um-status um-status-inactive">
            Nonaktif
        </span>
    @endif
</td>



{{-- Aksi --}}
<td class="text-center">
    <div class="dropdown">

        <button
            type="button"
            class="um-action-toggle"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            title="Aksi"
            aria-label="Aksi untuk {{ $user->name }}"
        >
            <i class="bi bi-three-dots-vertical"></i>
        </button>

        <ul class="dropdown-menu dropdown-menu-end">

            {{-- Detail --}}
            <li>
                <a class="dropdown-item"
                   href="{{ route('users.show', $user) }}">
                    <i class="bi bi-eye"></i>
                    Lihat Detail
                </a>
            </li>

            {{-- Edit --}}
            <li>
                <a class="dropdown-item"
                   href="{{ route('users.edit', $user) }}">
                    <i class="bi bi-pencil-square"></i>
                    Edit
                </a>
            </li>

            {{-- Reset Password --}}
            @if ($user->id !== auth()->id() && !$user->isSuperAdmin())
                <li>
                    <form
                        action="{{ route('users.reset-password', $user) }}"
                        method="POST"
                        class="m-0"
                        onsubmit="return confirm('Yakin ingin mereset password {{ $user->name }}? Password lama akan langsung tidak berlaku.')"
                    >
                        @csrf

                        <button type="submit" class="dropdown-item">
                            <i class="bi bi-key-fill"></i>
                            Reset Password
                        </button>
                    </form>
                </li>
            @endif

            {{-- Hapus --}}
            @if ($user->id !== auth()->id() && !$user->isSuperAdmin())
                <li>
                    <hr class="dropdown-divider">
                </li>
                <li>
                    <form
                        action="{{ route('users.destroy', $user) }}"
                        method="POST"
                        class="m-0"
                        onsubmit="return confirm('Yakin ingin menghapus user ini?')"
                    >
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
    <script>
    function copyPassword(button, passwordId) {
        const passwordElement = document.getElementById(passwordId);

        if (!passwordElement) {
            return;
        }

        const password = passwordElement.textContent.trim();

        navigator.clipboard.writeText(password)
            .then(() => {
                const originalHtml = button.innerHTML;

                button.innerHTML = '<i class="bi bi-check-lg me-1"></i>Tersalin';

                setTimeout(() => {
                    button.innerHTML = originalHtml;
                }, 2000);
            })
            .catch(() => {
                alert('Password gagal disalin. Silakan salin secara manual.');
            });
    }
</script>
</div>
@endsection