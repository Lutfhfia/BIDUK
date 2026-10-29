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
                Perbarui jumlah ketidakhadiran untuk semester Ganjil dan Genap.
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


                    {{-- Keterangan --}}
                    <div class="col-12">
                        <div class="alert alert-light border mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Perbarui jumlah hari ketidakhadiran pada masing-masing semester.
                        </div>
                        @error('attendance')
                            <div class="alert alert-danger mt-2 mb-0">{{ $message }}</div>
                        @enderror
                    </div>

                    @foreach (['ganjil' => 'Ganjil', 'genap' => 'Genap'] as $semesterKey => $semesterLabel)
                        <div class="col-md-6">
                            <section class="border rounded-3 p-3 h-100">
                                <h6 class="fw-bold mb-3">Semester {{ $semesterLabel }}</h6>
                                <div class="row g-3">
                                    @foreach (['sakit' => 'Sakit', 'izin' => 'Izin', 'tanpa_keterangan' => 'Tanpa Keterangan'] as $field => $label)
                                        <div class="col-12">
                                            <label for="{{ $semesterKey }}_{{ $field }}" class="form-label">
                                                {{ $label }} <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-group">
                                                <input
                                                    type="number"
                                                    name="attendance[{{ $semesterKey }}][{{ $field }}]"
                                                    id="{{ $semesterKey }}_{{ $field }}"
                                                    class="form-control @error('attendance.' . $semesterKey . '.' . $field) is-invalid @enderror"
                                                    min="0"
                                                    value="{{ old('attendance.' . $semesterKey . '.' . $field, $attendanceBySemester[$semesterKey][$field] ?? '') }}"
                                                    required
                                                >
                                                <span class="input-group-text">Hari</span>
                                            </div>
                                            @error('attendance.' . $semesterKey . '.' . $field)
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </section>
                        </div>
                    @endforeach

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