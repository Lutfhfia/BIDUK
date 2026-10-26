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

                {{-- Pegawai --}}
                <div class="col-md-6">
                    <label for="employee_id" class="form-label">
                        Pilih Pegawai <span class="text-danger">*</span>
                    </label>

                    <select
                        name="employee_id"
                        id="employee_id"
                        class="form-select @error('employee_id') is-invalid @enderror"
                        required
                    >
                        <option value="">Pilih Pegawai</option>
                        @foreach ($employees as $employee)
                            <option
                                value="{{ $employee->id }}"
                                data-name="{{ $employee->name }}"
                                data-nip="{{ $employee->nip }}"
                                data-nuptk="{{ $employee->nuptk }}"
                                data-email="{{ $employee->email }}"
                                {{ old('employee_id', $user->employee_id) == $employee->id ? 'selected' : '' }}
                            >
                                {{ $employee->name }}
                                @if ($employee->nip)
                                    — NIP {{ $employee->nip }}
                                @elseif ($employee->nuptk)
                                    — NUPTK {{ $employee->nuptk }}
                                @endif
                            </option>
                        @endforeach
                    </select>

                    <div class="form-text">
                        Akun ditautkan ke pegawai yang sudah ada. Data identitas akun mengikuti Data Pegawai.
                    </div>

                    @error('employee_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label for="employee_name" class="form-label">Nama</label>
                    <input id="employee_name" class="form-control" readonly>
                </div>

                <div class="col-md-6">
                    <label for="employee_nip" class="form-label">NIP</label>
                    <input id="employee_nip" class="form-control" readonly>
                </div>

                <div class="col-md-6">
                    <label for="employee_nuptk" class="form-label">NUPTK</label>
                    <input id="employee_nuptk" class="form-control" readonly>
                </div>

                <div class="col-md-6">
                    <label for="employee_email" class="form-label">Email</label>
                    <input id="employee_email" type="email" class="form-control" readonly>
                </div>

                <div class="col-md-6">
                    <label for="username_preview" class="form-label">Username</label>
                    <input id="username_preview" class="form-control" readonly>
                    <div class="form-text">Username menggunakan NIP, atau NUPTK jika NIP kosong.</div>
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

@push('scripts')
<script>
    const employeeSelect = document.getElementById('employee_id');
    const employeeName = document.getElementById('employee_name');
    const employeeNip = document.getElementById('employee_nip');
    const employeeNuptk = document.getElementById('employee_nuptk');
    const employeeEmail = document.getElementById('employee_email');
    const usernamePreview = document.getElementById('username_preview');

    function updateEmployeeDetails() {
        const option = employeeSelect.selectedOptions[0];
        const nip = option?.dataset.nip?.trim() ?? '';
        const nuptk = option?.dataset.nuptk?.trim() ?? '';

        employeeName.value = option?.dataset.name ?? '';
        employeeNip.value = nip;
        employeeNuptk.value = nuptk;
        employeeEmail.value = option?.dataset.email ?? '';
        usernamePreview.value = nip || nuptk;
    }

    employeeSelect.addEventListener('change', updateEmployeeDetails);
    updateEmployeeDetails();
</script>
@endpush