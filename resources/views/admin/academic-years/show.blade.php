@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">

        <div class="mb-4">
            <div class="text-muted small mb-1">
                Akademik / Tahun Ajaran / Detail
            </div>

            <h3 class="fw-bold mb-1">
                Detail Tahun Ajaran
            </h3>

            <div class="text-muted">
                Informasi lengkap tahun ajaran.
            </div>
        </div>

        <div class="card border-0 shadow-sm">

            <div class="card-body">

                <div class="row g-4">

                    <div class="col-md-6">
                        <div class="text-muted small">
                            Tahun Ajaran
                        </div>

                        <div class="fw-semibold fs-5">
                            {{ $academicYear->name }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">
                            Status
                        </div>

                        <div class="mt-1">
                            @if ($academicYear->status === 'active')
                                <span class="badge bg-success-subtle text-success px-3 py-2">
                                    Aktif
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                                    Tidak Aktif
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">
                            Tanggal Mulai
                        </div>

                        <div class="fw-semibold">
                            {{ $academicYear->start_date?->format('d/m/Y') ?? '-' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">
                            Tanggal Selesai
                        </div>

                        <div class="fw-semibold">
                            {{ $academicYear->end_date?->format('d/m/Y') ?? '-' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">
                            Semester Berjalan
                        </div>

                        <div class="mt-1">
                            @if ($academicYear->current_semester)
                                <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                    <i class="bi bi-bookmark-fill me-1"></i>
                                    {{ $academicYear->current_semester }}
                                </span>
                            @else
                                <span class="text-muted">
                                    Belum ditentukan
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">
                            Jumlah Kelas
                        </div>

                        <div class="fw-semibold">
                            {{ $academicYear->classes->count() }}
                        </div>
                    </div>

                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-end gap-2">

                    <a href="{{ route('academic-years.index') }}" class="btn btn-light border">
                        Kembali
                    </a>

                    <a href="{{ route('academic-years.edit', $academicYear) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-1"></i>
                        Edit
                    </a>

                </div>

            </div>

        </div>

    </div>
@endsection
