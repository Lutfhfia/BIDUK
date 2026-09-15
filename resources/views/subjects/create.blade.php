@extends('layouts.app')

@section('title', 'Tambah Mata Pelajaran')

@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">Dashboard</a>
    </li>

    <li class="breadcrumb-item">
        <span>Akademik</span>
    </li>

    <li class="breadcrumb-item">
        <a href="{{ route('subjects.index') }}">Mata Pelajaran</a>
    </li>

    <li class="breadcrumb-item active" aria-current="page">
        Tambah
    </li>
@endsection

@section('content')

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-journal-plus me-2"
                    style="color: var(--biduk-primary);"></i>
                Tambah Mata Pelajaran
            </h4>

            <p class="text-muted mb-0" style="font-size: 0.875rem;">
                Tambahkan data mata pelajaran yang digunakan dalam sistem BIDUK.
            </p>
        </div>

        <a href="{{ route('subjects.index') }}" class="btn btn-biduk-outline">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>


    {{-- Card Form --}}
    <div class="biduk-card fade-in-up">

        <div class="card-header">
            <div>
                <i class="bi bi-pencil-square me-2"
                    style="color: var(--biduk-primary);"></i>
                Form Mata Pelajaran
            </div>
        </div>

        <div class="card-body">

            <form action="{{ route('subjects.store') }}" method="POST">
                @csrf

                <div class="row g-4">

                    {{-- Kode Mata Pelajaran --}}
                    <div class="col-md-6">
                        <label for="code" class="form-label">
                            Kode Mata Pelajaran
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="code"
                            id="code"
                            class="form-control @error('code') is-invalid @enderror"
                            value="{{ old('code') }}"
                            placeholder="Contoh: PAI"
                            maxlength="20"
                            required
                        >

                        @error('code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="text-muted">
                            Masukkan kode singkat mata pelajaran.
                        </small>
                    </div>


                    {{-- Nama Mata Pelajaran --}}
                    <div class="col-md-6">
                        <label for="name" class="form-label">
                            Nama Mata Pelajaran
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}"
                            placeholder="Contoh: Pendidikan Agama dan Budi Pekerti"
                            maxlength="100"
                            required
                        >

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>


                    {{-- Kategori --}}
                    <div class="col-md-6">
                        <label for="category" class="form-label">
                            Kategori
                        </label>

                        <select
                            name="category"
                            id="category"
                            class="form-select @error('category') is-invalid @enderror"
                        >
                            <option value="">Pilih Kategori</option>

                            <option
                                value="Intrakurikuler"
                                {{ old('category') == 'Intrakurikuler' ? 'selected' : '' }}
                            >
                                Intrakurikuler
                            </option>

                            <option
                                value="Muatan Lokal"
                                {{ old('category') == 'Muatan Lokal' ? 'selected' : '' }}
                            >
                                Muatan Lokal
                            </option>
                        </select>

                        @error('category')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="text-muted">
                            Tentukan kategori mata pelajaran.
                        </small>
                    </div>


                    {{-- Status --}}
                    <div class="col-md-6">
                        <label for="status" class="form-label">
                            Status
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-select @error('status') is-invalid @enderror"
                            required
                        >
                            <option value="">Pilih Status</option>

                            <option
                                value="Aktif"
                                {{ old('status', 'Aktif') == 'Aktif' ? 'selected' : '' }}
                            >
                                Aktif
                            </option>

                            <option
                                value="Nonaktif"
                                {{ old('status') == 'Nonaktif' ? 'selected' : '' }}
                            >
                                Nonaktif
                            </option>
                        </select>

                        @error('status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                        <small class="text-muted">
                            Mata pelajaran aktif akan digunakan dalam sistem.
                        </small>
                    </div>

                </div>


                {{-- Informasi --}}
                <div class="alert alert-light border mt-4 mb-0">
                    <div class="d-flex">
                        <i class="bi bi-info-circle me-2"
                            style="color: var(--biduk-primary);"></i>

                        <div>
                            <strong>Informasi</strong>
                            <div class="text-muted mt-1" style="font-size: 0.875rem;">
                                Pastikan kode dan nama mata pelajaran sudah benar sebelum
                                menyimpan data.
                            </div>
                        </div>
                    </div>
                </div>


                {{-- Tombol --}}
                <div class="d-flex justify-content-end gap-2 mt-4">

                    <a
                        href="{{ route('subjects.index') }}"
                        class="btn btn-biduk-outline"
                    >
                        <i class="bi bi-x-lg me-1"></i>
                        Batal
                    </a>

                    <button
                        type="submit"
                        class="btn btn-biduk-primary"
                    >
                        <i class="bi bi-check-lg me-1"></i>
                        Simpan Mata Pelajaran
                    </button>

                </div>

            </form>

        </div>
    </div>

@endsection