@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="text-muted mb-1">
                Dashboard / Akademik / Mata Pelajaran / Edit
            </div>

            <h2 class="fw-bold mb-1">
                <i class="bi bi-pencil-square me-2"></i>
                Edit Mata Pelajaran
            </h2>

            <p class="text-muted mb-0">
                Perbarui informasi mata pelajaran yang tersedia dalam sistem BIDUK.
            </p>
        </div>

        <a href="{{ route('subjects.index') }}"
           class="btn btn-outline-success">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>
    </div>


    {{-- Form --}}
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-journal-text me-2 text-success"></i>
                Form Edit Mata Pelajaran
            </h5>
        </div>

        <div class="card-body p-4">

            <form action="{{ route('subjects.update', $subject) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="row g-4">

                    {{-- Kode --}}
                    <div class="col-md-6">

                        <label for="code" class="form-label">
                            Kode Mata Pelajaran <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            class="form-control @error('code') is-invalid @enderror"
                            id="code"
                            name="code"
                            value="{{ old('code', $subject->code) }}"
                            placeholder="Contoh: PAI"
                            required
                        >

                        <div class="form-text">
                            Masukkan kode singkat mata pelajaran.
                        </div>

                        @error('code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- Nama --}}
                    <div class="col-md-6">

                        <label for="name" class="form-label">
                            Nama Mata Pelajaran <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            id="name"
                            name="name"
                            value="{{ old('name', $subject->name) }}"
                            placeholder="Contoh: Pendidikan Agama Islam"
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

                            <option value="">
                                Pilih Kategori
                            </option>

                            <option value="Intrakurikuler"
                                {{ old('category', $subject->category) == 'Intrakurikuler' ? 'selected' : '' }}>
                                Intrakurikuler
                            </option>

                            <option value="Muatan Lokal"
                                {{ old('category', $subject->category) == 'Muatan Lokal' ? 'selected' : '' }}>
                                Muatan Lokal
                            </option>

                        </select>

                        @error('category')
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
                                {{ old('status', $subject->status) == 'Aktif' ? 'selected' : '' }}>
                                Aktif
                            </option>

                            <option value="Nonaktif"
                                {{ old('status', $subject->status) == 'Nonaktif' ? 'selected' : '' }}>
                                Nonaktif
                            </option>

                        </select>

                        <div class="form-text">
                            Mata pelajaran aktif akan digunakan dalam sistem.
                        </div>

                        @error('status')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Tombol --}}
                <div class="d-flex justify-content-end gap-2 mt-4">

                    <a href="{{ route('subjects.index') }}"
                       class="btn btn-outline-success">
                        <i class="bi bi-x-lg me-1"></i>
                        Batal
                    </a>

                    <button type="submit"
                            class="btn btn-success">
                        <i class="bi bi-check-lg me-1"></i>
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection