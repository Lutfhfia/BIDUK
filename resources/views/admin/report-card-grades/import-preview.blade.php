@extends('layouts.app')

@section('title', 'Preview Import Nilai Rapot')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
                <h4 class="fw-bold mb-1">Preview Import Nilai Rapot</h4>
                <p class="text-muted mb-0">Periksa metadata dan seluruh kolom nilai sebelum konfirmasi. Kolom kosong akan mengosongkan data tersimpan.</p>
            </div>
            <a href="{{ route('report-card-grades.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Kembali
            </a>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4"><strong>Tahun Ajaran:</strong> {{ $preview['metadata']['academic_year'] ?: '-' }}</div>
                    <div class="col-md-4"><strong>Kelas:</strong> {{ $preview['metadata']['class'] ?: '-' }}</div>
                    <div class="col-md-4"><strong>Semester:</strong> {{ $preview['metadata']['semester'] ?: '-' }}</div>
                </div>
            </div>
        </div>

        @if ($preview['global_errors'])
            <div class="alert alert-danger">
                <strong>File perlu diperbaiki:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($preview['global_errors'] as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-3 mb-4">
            @foreach ([
                'Jumlah baris dibaca' => 'read_rows',
                'Siswa ditemukan' => 'students_found',
                'Siswa valid' => 'valid_students',
                'NISN tidak ditemukan' => 'students_not_found',
                'Nilai valid' => 'valid_values',
                'Nilai invalid' => 'invalid_values',
                'Data baru' => 'new_records',
                'Data diperbarui' => 'updated_records',
            ] as $label => $key)
                <div class="col-6 col-lg-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body">
                            <div class="small text-muted">{{ $label }}</div>
                            <div class="fs-4 fw-bold">{{ $preview['summary'][$key] }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($preview['rows'])
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white fw-bold">Hasil Validasi per Baris</div>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Baris</th>
                                <th>NISN</th>
                                <th>Nama pada file</th>
                                <th>Nama BIDUK / Kelas</th>
                                <th>Nilai yang akan diimpor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($preview['rows'] as $row)
                                <tr>
                                    <td>{{ $row['row'] }}</td>
                                    <td>{{ $row['nisn'] ?: '-' }}</td>
                                    <td>{{ $row['name'] ?: '-' }}</td>
                                    <td>
                                        {{ $row['matched_name'] ?: '-' }}
                                        @if ($row['class_name'])
                                            <div class="small text-muted">Kelas {{ $row['class_name'] }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $gradeFieldLabels = [
                                                'score' => 'Nilai Akhir',
                                                'knowledge_predicate' => 'Predikat',
                                                'learning_outcome' => 'Capaian Pembelajaran',
                                            ];
                                        @endphp
                                        @foreach ($row['grades'] as $subjectName => $gradeValues)
                                            <div class="mb-2">
                                                <strong>{{ $subjectName }}:</strong>
                                                @foreach ($gradeFieldLabels as $field => $label)
                                                    <div class="small">
                                                        {{ $label }}:
                                                        @if (isset($gradeValues[$field]) && $gradeValues[$field] !== '')
                                                            {{ $field === 'score' ? number_format((float) $gradeValues[$field], 2) : $gradeValues[$field] }}
                                                        @else
                                                            <span class="text-warning-emphasis">Kosong — akan mengosongkan data tersimpan</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @foreach ($row['warnings'] as $warning)
                                            <div class="text-warning-emphasis mb-1"><i class="bi bi-exclamation-triangle me-1"></i>{{ $warning }}</div>
                                        @endforeach
                                        @foreach ($row['errors'] as $error)
                                            <div class="text-danger mb-1"><i class="bi bi-x-circle me-1"></i>{{ $error }}</div>
                                        @endforeach
                                        @if (! $row['warnings'] && ! $row['errors'])
                                            <span class="text-success"><i class="bi bi-check-circle me-1"></i>Siap diimpor</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="d-flex flex-wrap gap-2 justify-content-end">
            <form method="POST" action="{{ route('report-card-grades.import.cancel') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <button type="submit" class="btn btn-outline-secondary">Batal</button>
            </form>
            @if ($preview['can_import'])
                <form method="POST" action="{{ route('report-card-grades.import.confirm') }}">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check2-circle me-1"></i>Konfirmasi Import
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection
