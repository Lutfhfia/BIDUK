@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">

        <div class="mb-4">
            <div class="text-muted small mb-1">
                Akademik / Tahun Ajaran / Edit
            </div>

            <h3 class="fw-bold mb-1">
                Edit Tahun Ajaran
            </h3>

            <div class="text-muted">
                Perbarui informasi tahun ajaran.
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body">

                <form action="{{ route('academic-years.update', $academicYear) }}" method="POST">

                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Tahun Ajaran
                            </label>

                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $academicYear->name) }}" maxlength="20" required>

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Status
                            </label>

                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active"
                                    {{ old('status', $academicYear->status) === 'active' ? 'selected' : '' }}>
                                    Aktif
                                </option>

                                <option value="inactive"
                                    {{ old('status', $academicYear->status) === 'inactive' ? 'selected' : '' }}>
                                    Tidak Aktif
                                </option>
                            </select>

                            @error('status')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Semester Berjalan
                            </label>

                            <select name="current_semester"
                                class="form-select @error('current_semester') is-invalid @enderror" required>
                                <option value="Ganjil"
                                    {{ old('current_semester', $academicYear->current_semester) === 'Ganjil' ? 'selected' : '' }}>
                                    Ganjil
                                </option>

                                <option value="Genap"
                                    {{ old('current_semester', $academicYear->current_semester) === 'Genap' ? 'selected' : '' }}>
                                    Genap
                                </option>
                            </select>

                            @error('current_semester')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Tanggal Mulai
                            </label>

                            <input type="date" name="start_date"
                                class="form-control @error('start_date') is-invalid @enderror"
                                value="{{ old('start_date', $academicYear->start_date?->format('Y-m-d')) }}" required>

                            @error('start_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                Tanggal Selesai
                            </label>

                            <input type="date" name="end_date"
                                class="form-control @error('end_date') is-invalid @enderror"
                                value="{{ old('end_date', $academicYear->end_date?->format('Y-m-d')) }}" required>

                            @error('end_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                    <hr class="my-4">

                    <div class="d-flex justify-content-end gap-2">

                        <a href="{{ route('academic-years.index') }}" class="btn btn-light border">
                            Batal
                        </a>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-1"></i>
                            Simpan Perubahan
                        </button>

                    </div>

                </form>

            </div>
        </div>

    </div>
@endsection
