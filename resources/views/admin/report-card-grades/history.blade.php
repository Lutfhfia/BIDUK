@extends('layouts.app')

@section('title', 'Riwayat Nilai Rapot — ' . $student->name)

@section('content')

    <div class="container-fluid py-4">

        {{-- =====================================================
            HEADER
        ====================================================== --}}
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <div class="text-muted small mb-1">
                    Laporan / Nilai Rapot / Riwayat Nilai
                </div>

                <h4 class="fw-bold mb-1">

                    <i class="bi bi-clock-history me-2"></i>

                    Riwayat Nilai Rapot Siswa

                </h4>

                <p class="text-muted mb-0">

                    Histori nilai rapot peserta didik dari Kelas 1 sampai Kelas 6.

                </p>

            </div>

            <div>

                <a href="{{ route('report-card-grades.index') }}" class="btn btn-light border">

                    <i class="bi bi-arrow-left me-1"></i>

                    Kembali ke Nilai Rapot

                </a>

            </div>

        </div>


        {{-- =====================================================
            INFORMASI SISWA
        ====================================================== --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body">

                <div class="row align-items-center g-3">

                    <div class="col-auto">

                        @if ($student->photo)
                            <img
                                src="{{ asset('storage/' . $student->photo) }}"
                                alt="{{ $student->name }}"
                                class="rounded-circle"
                                style="width: 64px; height: 64px; object-fit: cover;"
                            >
                        @else
                            <div
                                class="rounded-circle d-flex align-items-center justify-content-center text-white fw-bold"
                                style="width: 64px; height: 64px; background-color: #1a7a4c; font-size: 1.5rem;"
                            >
                                {{ strtoupper(substr($student->name, 0, 1)) }}
                            </div>
                        @endif

                    </div>

                    <div class="col">

                        <h5 class="fw-bold mb-1">
                            {{ $student->name }}
                        </h5>

                        <div class="text-muted small d-flex flex-wrap gap-3">

                            <div>
                                <strong>NIS:</strong> {{ $student->nis ?? '-' }}
                            </div>

                            <div>
                                <strong>NISN:</strong> {{ $student->nisn ?? '-' }}
                            </div>

                            <div>
                                <strong>Jenis Kelamin:</strong>
                                {{ $student->gender === 'L' ? 'Laki-laki' : 'Perempuan' }}
                            </div>

                            <div>
                                <strong>Status:</strong>
                                <span class="badge bg-success-subtle text-success">
                                    {{ $student->status }}
                                </span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
            RIWAYAT RAPOT KELAS 1 - 6
        ====================================================== --}}
        <h5 class="fw-bold mb-3">

            <i class="bi bi-journal-bookmark me-2"></i>

            Riwayat Rapot per Tingkatan Kelas

        </h5>

        <div class="row g-3">

            @for ($level = 1; $level <= 6; $level++)

                @php
                    $data = $gradeLevelsData[$level] ?? null;
                    $hasClass = $data['has_class'] ?? false;
                    $class = $data['class'] ?? null;
                    $ay = $data['academic_year'] ?? null;
                    $hasData = $data['has_data'] ?? false;
                    $ganjilCount = $data['ganjil_count'] ?? 0;
                    $genapCount = $data['genap_count'] ?? 0;
                @endphp

                <div class="col-md-6 col-lg-4">

                    <div class="card border-0 shadow-sm h-100 {{ $hasData ? 'border-start border-success border-4' : '' }}">

                        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">

                            <h6 class="fw-bold mb-0">

                                <i class="bi bi-bookmark-star me-2 text-success"></i>

                                Rapot Kelas {{ $level }}

                            </h6>

                            @if (!$hasClass)
                                <span class="badge bg-light text-muted">
                                    Belum Ada
                                </span>
                            @elseif ($hasData)
                                <span class="badge bg-success-subtle text-success">
                                    <i class="bi bi-check-circle me-1"></i> Ada Data
                                </span>
                            @else
                                <span class="badge bg-warning-subtle text-warning">
                                    <i class="bi bi-clock me-1"></i> Belum Diisi
                                </span>
                            @endif

                        </div>

                        <div class="card-body">

                            @if ($hasClass)

                                <div class="mb-3">

                                    <div class="text-muted small">
                                        Rombel / Kelas
                                    </div>

                                    <div class="fw-semibold">
                                        Kelas {{ $class->grade_level }} {{ $class->name }}
                                    </div>

                                </div>

                                <div class="mb-3">

                                    <div class="text-muted small">
                                        Tahun Ajaran
                                    </div>

                                    <div class="fw-semibold">
                                        {{ $ay->name ?? '-' }}
                                    </div>

                                </div>

                                <div class="row g-2 mb-3">

                                    {{-- Ganjil --}}
                                    <div class="col-6">

                                        <div class="p-2 border rounded text-center bg-light">

                                            <div class="small fw-semibold text-muted mb-1">
                                                Ganjil
                                            </div>

                                            @if ($ganjilCount > 0)
                                                <span class="badge bg-success-subtle text-success">
                                                    <i class="bi bi-check me-1"></i> Sudah Diisi
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    Belum Diisi
                                                </span>
                                            @endif

                                        </div>

                                    </div>

                                    {{-- Genap --}}
                                    <div class="col-6">

                                        <div class="p-2 border rounded text-center bg-light">

                                            <div class="small fw-semibold text-muted mb-1">
                                                Genap
                                            </div>

                                            @if ($genapCount > 0)
                                                <span class="badge bg-success-subtle text-success">
                                                    <i class="bi bi-check me-1"></i> Sudah Diisi
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    Belum Diisi
                                                </span>
                                            @endif

                                        </div>

                                    </div>

                                </div>

                                <div class="mt-auto">

                                    <a
                                        href="{{ route('report-card-grades.edit', [
                                            'class' => $class->id,
                                            'student' => $student->id,
                                        ]) }}"
                                        class="btn btn-sm btn-success w-100 mb-2"
                                    >
                                        <i class="bi bi-pencil-square me-1"></i>
                                        {{ $hasData ? 'Lihat & Edit Rapot' : 'Input Rapot' }}
                                    </a>

                                    @if ($hasData)
                                        <div class="d-flex gap-2">
                                            @if ($ganjilCount > 0)
                                                <a
                                                    href="{{ route('report-card-grades.print', [
                                                        'class' => $class->id,
                                                        'student' => $student->id,
                                                        'semester' => 'ganjil',
                                                    ]) }}"
                                                    class="btn btn-sm btn-outline-danger flex-fill"
                                                    target="_blank"
                                                >
                                                    <i class="bi bi-file-earmark-pdf me-1"></i>
                                                    Cetak Ganjil
                                                </a>
                                            @endif

                                            @if ($genapCount > 0)
                                                <a
                                                    href="{{ route('report-card-grades.print', [
                                                        'class' => $class->id,
                                                        'student' => $student->id,
                                                        'semester' => 'genap',
                                                    ]) }}"
                                                    class="btn btn-sm btn-outline-danger flex-fill"
                                                    target="_blank"
                                                >
                                                    <i class="bi bi-file-earmark-pdf me-1"></i>
                                                    Cetak Genap
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                </div>

                            @else

                                <div class="text-center py-4 text-muted">

                                    <i class="bi bi-folder-x fs-2 text-muted mb-2 d-block"></i>

                                    <div class="small fw-medium">
                                        Data Rapot belum tersedia.
                                    </div>

                                    <div class="small text-muted">
                                        Siswa belum pernah ditempatkan di Kelas {{ $level }}.
                                    </div>

                                </div>

                            @endif

                        </div>

                    </div>

                </div>

            @endfor

        </div>

    </div>

@endsection
