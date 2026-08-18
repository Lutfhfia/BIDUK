@extends('layouts.app')

@section('title', 'Detail Pegawai')

@section('breadcrumb')

    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">
            Dashboard
        </a>
    </li>

    <li class="breadcrumb-item">
        <a href="{{ route('pegawai.index') }}">
            Data Pegawai
        </a>
    </li>

    <li class="breadcrumb-item active">
        Detail Pegawai
    </li>

@endsection


@section('content')

<div class="fade-in-up">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1 fw-bold">
                <i class="bi bi-person-vcard me-2 text-success"></i>
                Detail Pegawai
            </h4>

            <p class="text-muted mb-0">
                Informasi lengkap pegawai dan akun akses BIDUK.
            </p>

        </div>


        <div class="d-flex gap-2">

            <a
                href="{{ route('pegawai.edit', $employee) }}"
                class="btn btn-biduk-outline"
            >
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>

            <a
                href="{{ route('pegawai.index') }}"
                class="btn btn-biduk-primary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>

        </div>

    </div>


    <div class="row g-4">

        {{-- IDENTITAS --}}
        <div class="col-lg-8">

            <div class="biduk-card h-100">

                <div class="card-header">
                    <i class="bi bi-person me-2"></i>
                    Identitas Pegawai
                </div>


                <div class="card-body">

                    <div class="row g-4">

                        <div class="col-md-4 text-center">

                            @if($employee->photo)

                                <img
                                    src="{{ asset('storage/' . $employee->photo) }}"
                                    alt="{{ $employee->name }}"
                                    class="rounded-circle"
                                    style="
                                        width:130px;
                                        height:130px;
                                        object-fit:cover;
                                    "
                                >

                            @else

                                <div
                                    class="rounded-circle mx-auto d-flex align-items-center justify-content-center"
                                    style="
                                        width:130px;
                                        height:130px;
                                        background:#e8f7ef;
                                        color:#198754;
                                        font-size:45px;
                                        font-weight:700;
                                    "
                                >
                                    {{ strtoupper(substr($employee->name, 0, 1)) }}
                                </div>

                            @endif


                            <h5 class="fw-bold mt-3 mb-1">
                                {{ $employee->name }}
                            </h5>

                            <span class="badge badge-aktif">
                                {{ $employee->status }}
                            </span>

                        </div>


                        <div class="col-md-8">

                            <div class="row g-3">

                                <div class="col-sm-6">
                                    <small class="text-muted">
                                        NIP
                                    </small>

                                    <div class="fw-semibold">
                                        {{ $employee->nip ?? '-' }}
                                    </div>
                                </div>


                                <div class="col-sm-6">
                                    <small class="text-muted">
                                        NUPTK
                                    </small>

                                    <div class="fw-semibold">
                                        {{ $employee->nuptk ?? '-' }}
                                    </div>
                                </div>


                                <div class="col-sm-6">
                                    <small class="text-muted">
                                        Jenis Kelamin
                                    </small>

                                    <div class="fw-semibold">
                                        {{ $employee->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                                    </div>
                                </div>


                                <div class="col-sm-6">
                                    <small class="text-muted">
                                        Tempat, Tanggal Lahir
                                    </small>

                                    <div class="fw-semibold">
                                        {{ $employee->birth_place ?? '-' }},
                                        {{ $employee->birth_date?->format('d/m/Y') ?? '-' }}
                                    </div>
                                </div>


                                <div class="col-sm-6">
                                    <small class="text-muted">
                                        Jabatan
                                    </small>

                                    <div class="fw-semibold">
                                        {{ $employee->position ?? '-' }}
                                    </div>
                                </div>


                                <div class="col-sm-6">
                                    <small class="text-muted">
                                        Status Kepegawaian
                                    </small>

                                    <div class="fw-semibold">
                                        @if(in_array($employee->employment_status, ['Honorer', 'Kontrak']))
                                            Non-ASN
                                        @else
                                            {{ $employee->employment_status ?? '-' }}
                                        @endif
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- AKUN --}}
        <div class="col-lg-4">

            <div class="biduk-card h-100">

                <div class="card-header">
                    <i class="bi bi-shield-lock me-2"></i>
                    Akun Akses BIDUK
                </div>


                <div class="card-body">

                    @if($employee->user)

                        <div class="mb-3">

                            <small class="text-muted">
                                Username
                            </small>

                            <div class="fw-semibold">
                                {{ $employee->user->username }}
                            </div>

                        </div>


                        <div class="mb-3">

                            <small class="text-muted">
                                Role
                            </small>

                            <div>

                                <span class="badge bg-success">
                                    {{ $employee->user->role?->name ?? '-' }}
                                </span>

                            </div>

                        </div>


                        <div class="mb-3">

                            <small class="text-muted">
                                Status Akun
                            </small>

                            <div>

                                @if($employee->user->status === 'Aktif')

                                    <span class="badge badge-aktif">
                                        Aktif
                                    </span>

                                @else

                                    <span class="badge badge-nonaktif">
                                        Nonaktif
                                    </span>

                                @endif

                            </div>

                        </div>


                        <div>

                            <small class="text-muted">
                                Terakhir Login
                            </small>

                            <div class="fw-semibold">
                                {{ $employee->user->last_login_at?->format('d/m/Y H:i') ?? 'Belum pernah login' }}
                            </div>

                        </div>

                    @else

                        <div class="text-muted">
                            Akun BIDUK belum tersedia.
                        </div>

                    @endif

                </div>

            </div>

        </div>


        {{-- PENUGASAN --}}
        <div class="col-12">

            <div class="biduk-card">

                <div class="card-header">
                    <i class="bi bi-building me-2"></i>
                    Penugasan Wali Kelas
                </div>


                <div class="card-body">

                    @php
                        $activeClass = $employee->homeroomClasses
                            ->first(
                                fn ($class) =>
                                    $class->academicYear?->status === 'active'
                            );
                    @endphp


                    @if($activeClass)

                        <div class="row g-3">

                            <div class="col-md-4">

                                <small class="text-muted">
                                    Status
                                </small>

                                <div class="fw-semibold text-success">
                                    Wali Kelas
                                </div>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Kelas
                                </small>

                                <div class="fw-semibold">
                                    {{ $activeClass->name }}
                                </div>

                            </div>


                            <div class="col-md-4">

                                <small class="text-muted">
                                    Tahun Ajaran
                                </small>

                                <div class="fw-semibold">
                                    {{ $activeClass->academicYear?->name ?? '-' }}
                                </div>

                            </div>

                        </div>

                    @else

                        <span class="text-muted">
                            Pegawai ini belum ditetapkan sebagai wali kelas
                            pada tahun ajaran aktif.
                        </span>

                    @endif

                </div>

            </div>

        </div>


        {{-- KONTAK --}}
        <div class="col-12">

            <div class="biduk-card">

                <div class="card-header">
                    <i class="bi bi-telephone me-2"></i>
                    Informasi Kontak
                </div>


                <div class="card-body">

                    <div class="row g-4">

                        <div class="col-md-4">

                            <small class="text-muted">
                                Nomor HP
                            </small>

                            <div class="fw-semibold">
                                {{ $employee->phone ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Email
                            </small>

                            <div class="fw-semibold">
                                {{ $employee->email ?? '-' }}
                            </div>

                        </div>


                        <div class="col-md-4">

                            <small class="text-muted">
                                Alamat
                            </small>

                            <div class="fw-semibold">
                                {{ $employee->address ?? '-' }}
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection