@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">

        <div>
            <a href="{{ route('classes.index') }}"
               class="text-decoration-none text-muted small">
                <i class="bi bi-arrow-left me-1"></i>
                Kembali ke Data Kelas
            </a>

            <h3 class="fw-bold mt-2 mb-1">
                Detail Kelas {{ $class->name }}
            </h3>

            <p class="text-muted mb-0">
                Informasi kelas dan daftar siswa yang terdaftar.
            </p>
        </div>

        <div class="d-flex gap-2">

            <a href="{{ route('classes.edit', $class) }}"
               class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i>
                Edit Kelas
            </a>

            <a href="{{ route('classes.students', $class) }}"
               class="btn btn-primary">
                <i class="bi bi-people me-1"></i>
                Kelola Siswa
            </a>

        </div>

    </div>


    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif


    {{-- Error --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>

            <strong>Terjadi kesalahan:</strong>

            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif


 {{-- Informasi Kelas --}}
<div class="card border-0 shadow-sm mb-4">

    {{-- Header --}}
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex align-items-center">
            <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center me-3"
                 style="width: 42px; height: 42px;">
                <i class="bi bi-building fs-5"></i>
            </div>

            <div>
                <h5 class="fw-bold mb-1">
                    Informasi Kelas
                </h5>
                <small class="text-muted">
                    Informasi umum mengenai kelas yang dipilih.
                </small>
            </div>
        </div>
    </div>

    <div class="card-body p-4">

        <div class="row g-4">

            {{-- Nama Kelas --}}
            <div class="col-md-6 col-lg-4">
                <div class="class-info-item">
                    <div class="class-info-label">
                        <i class="bi bi-building me-2"></i>
                        Nama Kelas
                    </div>

                    <div class="class-info-value fs-5">
                        {{ $class->name }}
                    </div>
                </div>
            </div>

            {{-- Tingkat --}}
            <div class="col-md-6 col-lg-4">
                <div class="class-info-item">
                    <div class="class-info-label">
                        <i class="bi bi-layers me-2"></i>
                        Tingkat
                    </div>

                    <div class="class-info-value">
                        Kelas {{ $class->grade_level }}
                    </div>
                </div>
            </div>

            {{-- Tahun Ajaran --}}
            <div class="col-md-6 col-lg-4">
                <div class="class-info-item">
                    <div class="class-info-label">
                        <i class="bi bi-calendar3 me-2"></i>
                        Tahun Ajaran
                    </div>

                    <div class="class-info-value">
                        {{ $class->academicYear?->name ?? '-' }}
                    </div>
                </div>
            </div>

            {{-- Wali Kelas --}}
            <div class="col-md-6 col-lg-4">
                <div class="class-info-item">
                    <div class="class-info-label">
                        <i class="bi bi-person-badge me-2"></i>
                        Wali Kelas
                    </div>

                    <div class="class-info-value">

                        @if($class->homeroomTeacher)

                            {{ $class->homeroomTeacher->name }}

                        @else

                            <span class="text-muted">
                                Belum ditentukan
                            </span>

                        @endif

                    </div>
                </div>
            </div>

            {{-- Kapasitas --}}
            <div class="col-md-6 col-lg-4">
                <div class="class-info-item">
                    <div class="class-info-label">
                        <i class="bi bi-people me-2"></i>
                        Kapasitas Kelas
                    </div>

                    <div class="class-info-value">
                        {{ $class->capacity ?? 0 }}
                        <span class="text-muted fw-normal">siswa</span>
                    </div>
                </div>
            </div>

            {{-- Jumlah Siswa --}}
            <div class="col-md-6 col-lg-4">
                <div class="class-info-item">
                    <div class="class-info-label">
                        <i class="bi bi-person-check me-2"></i>
                        Jumlah Siswa Aktif
                    </div>

                    <div class="class-info-value">
                        {{ $students->total() }}
                        <span class="text-muted fw-normal">siswa</span>
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div class="col-12">
                <div class="class-info-item">

                    <div class="class-info-label">
                        <i class="bi bi-toggle-on me-2"></i>
                        Status Kelas
                    </div>

                    <div class="mt-2">

                        @if($class->status === 'Aktif')

                            <span class="badge bg-success-subtle text-success px-3 py-2">
                                <i class="bi bi-check-circle me-1"></i>
                                Aktif
                            </span>

                        @else

                            <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                                <i class="bi bi-dash-circle me-1"></i>
                                Nonaktif
                            </span>

                        @endif

                    </div>



                {{-- Wali Kelas --}}
                <div class="col-md-6 col-lg-4">

                    <div class="text-muted small mb-1">
                        Wali Kelas
                    </div>

                    <div class="fw-semibold">

                        @if($class->homeroomTeacher)

                            <i class="bi bi-person-badge me-1"></i>
                            {{ $class->homeroomTeacher->name }}

                        @else

                            <span class="text-muted">
                                Belum ditentukan
                            </span>

                        @endif

                    </div>

                </div>


                {{-- Kapasitas --}}
                <div class="col-md-6 col-lg-4">

                    <div class="text-muted small mb-1">
                        Kapasitas Kelas
                    </div>

                    <div class="fw-semibold">

                        {{ $class->capacity ?? 0 }} siswa

                    </div>

                </div>


                {{-- Jumlah Siswa --}}
                <div class="col-md-6 col-lg-4">

                    <div class="text-muted small mb-1">
                        Jumlah Siswa Aktif
                    </div>

                    <div class="fw-semibold">

                        {{ $students->total() }} siswa

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- Daftar Siswa --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-bottom py-3">

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                <div>

                    <h5 class="fw-bold mb-1">
                        <i class="bi bi-people me-2"></i>
                        Daftar Siswa
                    </h5>

                    <small class="text-muted">
                        Siswa yang terdaftar aktif di kelas {{ $class->name }}.
                    </small>

                </div>

                <a href="{{ route('classes.students.add', $class) }}"
                   class="btn btn-primary">

                    <i class="bi bi-person-plus me-1"></i>
                    Tambah Siswa

                </a>

            </div>

        </div>


        <div class="card-body p-0">

            @if($students->count() > 0)

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th class="text-center" style="width: 70px;">
                                    No
                                </th>

                                <th>
                                    NIS
                                </th>

                                <th>
                                    NISN
                                </th>

                                <th>
                                    Nama Peserta Didik
                                </th>

                                <th class="text-center">
                                    Jenis Kelamin
                                </th>

                                <th class="text-center" style="width: 120px;">
                                    Aksi
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($students as $student)

                                <tr>

                                    <td class="text-center">
                                        {{ $students->firstItem() + $loop->index }}
                                    </td>

                                    <td>
                                        {{ $student->nis ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $student->nisn ?? '-' }}
                                    </td>

                                    <td>

                                        <div class="fw-semibold">
                                            {{ $student->name }}
                                        </div>

                                    </td>

                                    <td class="text-center">

                                        @if($student->gender === 'L')

                                            Laki-laki

                                        @elseif($student->gender === 'P')

                                            Perempuan

                                        @else

                                            -

                                        @endif

                                    </td>

                                    <td class="text-center">

                                        <div class="d-flex justify-content-center gap-1">

                                            {{-- Detail siswa --}}
                                            <a href="{{ route('students.show', $student) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Lihat Data Siswa">

                                                <i class="bi bi-eye"></i>

                                            </a>


                                            {{-- Keluarkan dari kelas --}}
                                            <form action="{{ route('classes.students.remove', [$class, $student]) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Yakin ingin mengeluarkan siswa ini dari kelas {{ $class->name }}?')">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Keluarkan dari Kelas">

                                                    <i class="bi bi-person-dash"></i>

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center px-3 py-3 border-top">

                    <div class="text-muted small mb-2 mb-md-0">

                        Menampilkan
                        {{ $students->firstItem() }}
                        –
                        {{ $students->lastItem() }}
                        dari
                        {{ $students->total() }}
                        siswa

                    </div>

                    {{ $students->links('pagination::bootstrap-5') }}

                </div>

            @else

                {{-- Empty state --}}
                <div class="text-center py-5 px-3">

                    <div class="mb-3">

                        <i class="bi bi-people"
                           style="font-size: 3rem; opacity: .35;">
                        </i>

                    </div>

                    <h5 class="fw-semibold">
                        Belum Ada Siswa
                    </h5>

                    <p class="text-muted mb-4">

                        Belum ada siswa yang ditempatkan
                        pada kelas {{ $class->name }}.

                    </p>

                    <a href="{{ route('classes.students.add', $class) }}"
                       class="btn btn-primary">

                        <i class="bi bi-person-plus me-1"></i>
                        Tambah Siswa

                    </a>

                </div>

            @endif

        </div>

    </div>

</div>

<style>
    .class-info-item {
        height: 100%;
        padding: 1rem 1.1rem;
        background: #f8f9fa;
        border: 1px solid #edf0f2;
        border-radius: 10px;
    }

    .class-info-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .class-info-value {
        font-size: 0.95rem;
        font-weight: 600;
        color: #212529;
    }

    .class-info-label i {
        font-size: 0.9rem;
    }
</style>
@endsection
