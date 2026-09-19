@extends('layouts.app')

@section('title', 'Tambah Rekap Absensi')

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
        Tambah Rekap Absensi
    </li>
@endsection

@section('content')

    {{-- Header Halaman --}}
    <div class="d-flex justify-content-between align-items-center mb-4 fade-in-up">
        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-clipboard2-plus-fill me-2"
                    style="color: var(--biduk-primary);"></i>
                Tambah Rekap Absensi
            </h4>

            <p class="text-muted mb-0" style="font-size: 0.875rem;">
                Tambahkan jumlah ketidakhadiran siswa berdasarkan semester.
            </p>
        </div>
    </div>


    {{-- Form --}}
    <div class="biduk-card fade-in-up">

        <div class="card-header">
            <i class="bi bi-clipboard2-check me-2"
                style="color: var(--biduk-primary);"></i>
            Form Rekap Absensi
        </div>

        <div class="card-body">

            <form action="{{ route('rekap-absensi.store') }}" method="POST">
                @csrf

                <div class="row g-4">

                    {{-- Tahun Ajaran --}}
                    <div class="col-md-6">
                        <label for="academic_year_id" class="form-label">
                            Tahun Ajaran <span class="text-danger">*</span>
                        </label>

                        <select
                            name="academic_year_id"
                            id="academic_year_id"
                            class="form-select @error('academic_year_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Pilih Tahun Ajaran</option>

                            @foreach ($academicYears as $academicYear)
                                <option
                                    value="{{ $academicYear->id }}"
                                    {{ old('academic_year_id') == $academicYear->id ? 'selected' : '' }}
                                >
                                    {{ $academicYear->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('academic_year_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    {{-- Semester --}}
                    <div class="col-md-6">
                        <label for="semester_id" class="form-label">
                            Semester <span class="text-danger">*</span>
                        </label>

                        <select
                            name="semester_id"
                            id="semester_id"
                            class="form-select @error('semester_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Pilih Semester</option>

                            @foreach ($semesters as $semester)
                                <option
                                    value="{{ $semester->id }}"
                                    data-academic-year="{{ $semester->academic_year_id }}"
                                    {{ old('semester_id') == $semester->id ? 'selected' : '' }}
                                >
                                    {{ $semester->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('semester_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    {{-- Kelas --}}
                    <div class="col-md-6">
                        <label for="class_id" class="form-label">
                            Kelas <span class="text-danger">*</span>
                        </label>

                        <select
                            name="class_id"
                            id="class_id"
                            class="form-select @error('class_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Pilih Kelas</option>

                            @foreach ($classes as $class)
                                <option
                                    value="{{ $class->id }}"
                                    data-academic-year="{{ $class->academic_year_id }}"
                                    {{ old('class_id') == $class->id ? 'selected' : '' }}
                                >
                                    {{ $class->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('class_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    {{-- Siswa --}}
                    <div class="col-md-6">
                        <label for="student_id" class="form-label">
                            Siswa <span class="text-danger">*</span>
                        </label>

                        <select
                            name="student_id"
                            id="student_id"
                            class="form-select @error('student_id') is-invalid @enderror"
                            required
                            disabled
                        >
                            <option value="">Pilih kelas terlebih dahulu</option>
                        </select>

                        @error('student_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>


                    {{-- Keterangan --}}
                    <div class="col-12">
                        <div class="alert alert-light border mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Masukkan jumlah hari ketidakhadiran siswa untuk semester yang dipilih.
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
                                value="{{ old('sakit', 0) }}"
                                required
                            >
                            <span class="input-group-text">Hari</span>
                        </div>

                        @error('sakit')
                            <div class="text-danger small mt-1">{{ $message }}</div>
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
                                value="{{ old('izin', 0) }}"
                                required
                            >
                            <span class="input-group-text">Hari</span>
                        </div>

                        @error('izin')
                            <div class="text-danger small mt-1">{{ $message }}</div>
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
                                value="{{ old('tanpa_keterangan', 0) }}"
                                required
                            >
                            <span class="input-group-text">Hari</span>
                        </div>

                        @error('tanpa_keterangan')
                            <div class="text-danger small mt-1">{{ $message }}</div>
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
                        Simpan Rekap Absensi
                    </button>

                </div>

            </form>

        </div>

    </div>

@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const classSelect = document.getElementById('class_id');
    const studentSelect = document.getElementById('student_id');
    const academicYearSelect = document.getElementById('academic_year_id');
    const semesterSelect = document.getElementById('semester_id');

    function filterSemesterAndClass() {
        const academicYearId = academicYearSelect.value;

        Array.from(semesterSelect.options).forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden = option.dataset.academicYear !== academicYearId;
        });

        Array.from(classSelect.options).forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden = option.dataset.academicYear !== academicYearId;
        });

        semesterSelect.value = '';
        classSelect.value = '';

        studentSelect.innerHTML =
            '<option value="">Pilih kelas terlebih dahulu</option>';
        studentSelect.disabled = true;
    }

    academicYearSelect.addEventListener('change', filterSemesterAndClass);

    classSelect.addEventListener('change', function () {
        const classId = this.value;

        studentSelect.innerHTML =
            '<option value="">Memuat data siswa...</option>';

        studentSelect.disabled = true;

        if (!classId) {
            studentSelect.innerHTML =
                '<option value="">Pilih kelas terlebih dahulu</option>';
            return;
        }

        fetch('{{ url('rekap-absensi/students') }}/' + classId)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mengambil data siswa.');
                }

                return response.json();
            })
            .then(students => {
                studentSelect.innerHTML =
                    '<option value="">Pilih Siswa</option>';

                if (students.length === 0) {
                    studentSelect.innerHTML =
                        '<option value="">Tidak ada siswa aktif</option>';
                    return;
                }

                students.forEach(student => {
                    const option = document.createElement('option');

                    option.value = student.id;
                    option.textContent =
                        student.nisn + ' - ' + student.name;

                    studentSelect.appendChild(option);
                });

                studentSelect.disabled = false;
            })
            .catch(error => {
                console.error(error);

                studentSelect.innerHTML =
                    '<option value="">Gagal memuat siswa</option>';
            });
    });
});
</script>
@endpush
