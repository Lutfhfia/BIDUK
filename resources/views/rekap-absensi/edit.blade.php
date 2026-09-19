@extends('layouts.app')

@section('title', 'Edit Rekap Absensi')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>

    <li class="breadcrumb-item">
        <span>Laporan</span>
    </li>

    <li class="breadcrumb-item">
        <a href="{{ route('rekap-absensi.index') }}">Rekap Absensi</a>
    </li>

    <li class="breadcrumb-item active" aria-current="page">
        Edit
    </li>
@endsection

@section('content')

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-pencil-square me-2"
                    style="color: var(--biduk-primary);"></i>
                Edit Rekap Absensi
            </h4>

            <p class="text-muted mb-0" style="font-size: 0.875rem;">
                Perbarui jumlah ketidakhadiran siswa.
            </p>
        </div>
    </div>


    {{-- Form --}}
    <div class="biduk-card fade-in-up">

        <div class="card-header">
            <i class="bi bi-clipboard2-check me-2"
                style="color: var(--biduk-primary);"></i>
            Form Edit Rekap Absensi
        </div>

        <div class="card-body">

            <form
                action="{{ route('rekap-absensi.update', $rekapAbsensi) }}"
                method="POST"
            >
                @csrf
                @method('PUT')

                <div class="row g-4">

                    {{-- Identitas Siswa --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Siswa
                        </label>

                        <div class="form-control bg-light">
                            {{ $rekapAbsensi->student->nisn ?? '-' }}
                            -
                            {{ $rekapAbsensi->student->name ?? '-' }}
                        </div>

                        <div class="form-text">
                            Data siswa tidak dapat diubah pada proses edit.
                        </div>
                    </div>


                    {{-- Kelas --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Kelas
                        </label>

                        <div class="form-control bg-light">
                            {{ $rekapAbsensi->schoolClass->name ?? '-' }}
                        </div>
                    </div>


                    {{-- Tahun Ajaran --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Tahun Ajaran
                        </label>

                        <div class="form-control bg-light">
                            {{ $rekapAbsensi->schoolClass->academicYear->name ?? '-' }}
                        </div>
                    </div>


                    {{-- Semester --}}
                    <div class="col-md-6">
                        <label class="form-label">
                            Semester
                        </label>

                        <div class="form-control bg-light">
                            {{ $rekapAbsensi->semester->name ?? '-' }}
                        </div>
                    </div>


                    {{-- Keterangan --}}
                    <div class="col-12">
                        <div class="alert alert-light border mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Perbarui jumlah hari ketidakhadiran siswa untuk semester tersebut.
                        </div>
                    </div>


                    {{-- Sakit --}}
                    <div class="col-md-4">
                        <label for="sakit" class="form-label">
                            Sakit <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <input
                                type="number"
                                name="sakit"
                                id="sakit"
                                class="form-control @error('sakit') is-invalid @enderror"
                                min="0"
                                value="{{ old('sakit', $rekapAbsensi->sakit) }}"
                                required
                            >
                            <span class="input-group-text">Hari</span>
                        </div>

                        @error('sakit')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>


                    {{-- Izin --}}
                    <div class="col-md-4">
                        <label for="izin" class="form-label">
                            Izin <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <input
                                type="number"
                                name="izin"
                                id="izin"
                                class="form-control @error('izin') is-invalid @enderror"
                                min="0"
                                value="{{ old('izin', $rekapAbsensi->izin) }}"
                                required
                            >
                            <span class="input-group-text">Hari</span>
                        </div>

                        @error('izin')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>


                    {{-- Tanpa Keterangan --}}
                    <div class="col-md-4">
                        <label for="tanpa_keterangan" class="form-label">
                            Tanpa Keterangan <span class="text-danger">*</span>
                        </label>

                        <div class="input-group">
                            <input
                                type="number"
                                name="tanpa_keterangan"
                                id="tanpa_keterangan"
                                class="form-control @error('tanpa_keterangan') is-invalid @enderror"
                                min="0"
                                value="{{ old('tanpa_keterangan', $rekapAbsensi->tanpa_keterangan) }}"
                                required
                            >
                            <span class="input-group-text">Hari</span>
                        </div>

                        @error('tanpa_keterangan')
                            <div class="text-danger small mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                </div>


                {{-- Tombol --}}
                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">

                    <a
                        href="{{ route('rekap-absensi.index') }}"
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