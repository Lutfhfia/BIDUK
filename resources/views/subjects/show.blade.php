@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <div class="text-muted mb-1">
                Dashboard / Akademik / Mata Pelajaran / Detail
            </div>

            <h2 class="fw-bold mb-1">
                <i class="bi bi-journal-text me-2"></i>
                Detail Mata Pelajaran
            </h2>

            <p class="text-muted mb-0">
                Informasi lengkap mengenai mata pelajaran.
            </p>
        </div>

        <div class="d-flex gap-2">
            <a href="{{ route('subjects.index') }}"
               class="btn btn-outline-success">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>

            <a href="{{ route('subjects.edit', $subject) }}"
               class="btn btn-success">
                <i class="bi bi-pencil me-1"></i>
                Edit
            </a>
        </div>
    </div>


    {{-- Detail --}}
    <div class="card border-0 shadow-sm rounded-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-info-circle me-2 text-success"></i>
                Informasi Mata Pelajaran
            </h5>
        </div>

        <div class="card-body p-4">

            <div class="row g-4">

                {{-- Kode --}}
                <div class="col-md-6">
                    <label class="text-muted small mb-2">
                        Kode Mata Pelajaran
                    </label>

                    <div class="fw-semibold fs-5">
                        {{ $subject->code }}
                    </div>
                </div>


                {{-- Nama --}}
                <div class="col-md-6">
                    <label class="text-muted small mb-2">
                        Nama Mata Pelajaran
                    </label>

                    <div class="fw-semibold fs-5">
                        {{ $subject->name }}
                    </div>
                </div>


                {{-- Kategori --}}
                <div class="col-md-6">
                    <label class="text-muted small mb-2">
                        Kategori
                    </label>

                    <div>
                        @if($subject->category)
                            <span class="badge bg-success-subtle text-success px-3 py-2">
                                {{ $subject->category }}
                            </span>
                        @else
                            <span class="text-muted">
                                Tidak ada kategori
                            </span>
                        @endif
                    </div>
                </div>


                {{-- Status --}}
                <div class="col-md-6">
                    <label class="text-muted small mb-2">
                        Status
                    </label>

                    <div>
                        @if($subject->status === 'Aktif')
                            <span class="badge bg-success px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i>
                                Aktif
                            </span>
                        @else
                            <span class="badge bg-secondary px-3 py-2">
                                <i class="bi bi-x-circle me-1"></i>
                                Nonaktif
                            </span>
                        @endif
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>

@endsection