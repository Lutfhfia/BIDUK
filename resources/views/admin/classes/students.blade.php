@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <div class="text-muted small mb-1">
                Master Data / Data Kelas / {{ $class->name }}
            </div>

            <h3 class="fw-bold mb-1">
                Daftar Siswa Kelas {{ $class->name }}
            </h3>

            <div class="text-muted">
                Tahun Ajaran {{ $class->academicYear->name }}
            </div>
        </div>

        <div>
            <a href="{{ route('classes.index') }}"
               class="btn btn-light border me-2">
                ← Kembali
            </a>

            <a href="{{ route('classes.students.add', $class) }}"
               class="btn btn-primary">
                + Tambah Siswa
            </a>
        </div>

    </div>


    {{-- Informasi Kelas --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">

            <div class="row">

                <div class="col-md-3 mb-3">
                    <div class="text-muted small">
                        Nama Kelas
                    </div>

                    <div class="fw-semibold">
                        {{ $class->name }}
                    </div>
                </div>


                <div class="col-md-3 mb-3">
                    <div class="text-muted small">
                        Tingkat
                    </div>

                    <div class="fw-semibold">
                        Kelas {{ $class->grade_level }}
                    </div>
                </div>


                <div class="col-md-3 mb-3">
                    <div class="text-muted small">
                        Wali Kelas
                    </div>

                    <div class="fw-semibold">
                        {{ $class->homeroomTeacher->name ?? 'Belum ditentukan' }}
                    </div>
                </div>


                <div class="col-md-3 mb-3">
                    <div class="text-muted small">
                        Kapasitas
                    </div>

                    <div class="fw-semibold">
                        {{ $class->student_count }} / {{ $class->capacity }}
                        siswa
                    </div>
                </div>

            </div>

        </div>
    </div>


    {{-- Pesan berhasil --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif


    {{-- Pesan error --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    {{-- Tabel Siswa --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <div>
                    <h5 class="fw-bold mb-1">
                        Siswa Kelas {{ $class->name }}
                    </h5>

                    <div class="text-muted small">
                        Daftar siswa aktif pada kelas ini
                    </div>
                </div>

                <span class="badge bg-success-subtle text-success">
                    {{ $students->total() }} Siswa
                </span>

            </div>


            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>
                            <th width="60">No</th>
                            <th>Nomor Induk</th>
                            <th>NISN</th>
                            <th>Nama Peserta Didik</th>
                            <th>Jenis Kelamin</th>
                            <th width="100">Aksi</th>
                        </tr>

                    </thead>


                    <tbody>

                        @forelse($students as $student)

                            <tr>

                                <td>
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

                                <td>
                                    @if($student->gender === 'L')
                                        Laki-laki
                                    @else
                                        Perempuan
                                    @endif
                                </td>

                                <td>

                                    <div class="dropdown">

                                        <button
                                            class="btn btn-sm btn-light"
                                            type="button"
                                            data-bs-toggle="dropdown">

                                            ⋮

                                        </button>

                                        <ul class="dropdown-menu dropdown-menu-end">

                                            <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="{{ route('students.show', $student) }}">

                                                    Lihat Data Siswa

                                                </a>
                                            </li>

                                            <li>
                                                <a
                                                    class="dropdown-item"
                                                    href="{{ route('students.edit', $student) }}">

                                                    Edit Data Siswa

                                                </a>
                                            </li>

                                            <li>
                                                <hr class="dropdown-divider">
                                            </li>

                                            <li>

                                                <form
                                                    action="{{ route('classes.students.remove', [$class, $student]) }}"
                                                    method="POST">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button
                                                        type="submit"
                                                        class="dropdown-item text-danger"
                                                        onclick="return confirm('Keluarkan siswa ini dari kelas?')">

                                                        Keluarkan dari Kelas

                                                    </button>

                                                </form>

                                            </li>

                                        </ul>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6"
                                    class="text-center py-5 text-muted">

                                    Belum ada siswa di kelas ini.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            <div class="mt-3">

                {{ $students->links() }}

            </div>

        </div>

    </div>

</div>

@endsection
