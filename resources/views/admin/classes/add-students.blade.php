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
                Tambah Siswa ke Kelas {{ $class->name }}
            </h3>

            <div class="text-muted">
                Pilih siswa yang akan ditempatkan pada kelas ini.
            </div>

        </div>

        <a href="{{ route('classes.students', $class) }}"
           class="btn btn-light border">

            ← Kembali

        </a>

    </div>


    {{-- Info --}}
    <div class="alert alert-info">

        <strong>{{ $class->name }}</strong>

        — Kelas {{ $class->grade_level }}

        — Tahun Ajaran {{ $class->academicYear->name }}

        — Kapasitas:

        <strong>{{ $class->capacity }} siswa</strong>

    </div>


    @if($errors->any())

        <div class="alert alert-danger">

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- Pencarian Siswa --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-body">

            <form method="GET" action="{{ route('classes.students.add', $class) }}">

                <div class="row g-2 align-items-end">

                    <div class="col-md-6 col-lg-5">

                        <label class="form-label fw-semibold small text-muted mb-1">
                            Cari Siswa
                        </label>

                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            value="{{ $search ?? '' }}"
                            placeholder="Ketik Nama Siswa / NIS..."
                        >

                    </div>

                    <div class="col-md-4 d-flex gap-2">

                        <button type="submit" class="btn btn-success">

                            <i class="bi bi-search me-1"></i>

                            Cari

                        </button>

                        @if(!empty($search))

                            <a href="{{ route('classes.students.add', $class) }}" class="btn btn-outline-secondary">

                                <i class="bi bi-arrow-clockwise me-1"></i>

                                Reset

                            </a>

                        @endif

                    </div>

                </div>

            </form>

        </div>

    </div>


    <form
        action="{{ route('classes.students.store', $class) }}"
        method="POST">

        @csrf

        {{-- Tombol Simpan & Batal di BAGIAN ATAS --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-body py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">

                <div class="text-muted small">

                    Centang siswa yang ingin dimasukkan ke kelas <strong>{{ $class->name }}</strong>.

                </div>

                <div class="d-flex gap-2">

                    <button
                        type="submit"
                        class="btn btn-success">

                        <i class="bi bi-check-lg me-1"></i>

                        Simpan

                    </button>

                    <a
                        href="{{ route('classes.students', $class) }}"
                        class="btn btn-outline-secondary">

                        Batal

                    </a>

                </div>

            </div>

        </div>


        <div class="card border-0 shadow-sm">

            <div class="card-body">

                {{-- Header tabel --}}
                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>

                        <h5 class="fw-bold mb-1">
                            Pilih Siswa
                        </h5>

                        <div class="text-muted small">
                            Total tersedia: <strong>{{ $students->count() }}</strong> siswa
                        </div>

                    </div>

                    <div>

                        <button
                            type="button"
                            class="btn btn-sm btn-outline-primary"
                            id="checkAll">

                            Pilih Semua

                        </button>

                    </div>

                </div>


                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">

                            <tr>

                                <th width="60">

                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        id="selectAll">

                                </th>

                                <th width="60">
                                    No
                                </th>

                                <th>
                                    Nomor Induk
                                </th>

                                <th>
                                    NISN
                                </th>

                                <th>
                                    Nama Peserta Didik
                                </th>

                                <th>
                                    Jenis Kelamin
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($students as $student)

                                <tr>

                                    <td>

                                        <input
                                            type="checkbox"
                                            class="form-check-input student-checkbox"
                                            name="student_ids[]"
                                            value="{{ $student->id }}">

                                    </td>

                                    <td>
                                        {{ $loop->iteration }}
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

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6"
                                        class="text-center py-5 text-muted">

                                        <i class="bi bi-search fs-2 text-muted mb-2 d-block"></i>

                                        {{ !empty($search) ? 'Data siswa tidak ditemukan.' : 'Tidak ada siswa yang dapat ditambahkan.' }}

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- Footer --}}
                <div class="d-flex justify-content-between align-items-center mt-4">

                    <div class="text-muted small">

                        Siswa yang dipilih akan dimasukkan sebagai
                        <strong>Aktif</strong> pada kelas ini.

                    </div>


                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            class="btn btn-success">

                            <i class="bi bi-check-lg me-1"></i>

                            Simpan

                        </button>

                        <a
                            href="{{ route('classes.students', $class) }}"
                            class="btn btn-outline-secondary">

                            Batal

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </form>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const selectAll = document.getElementById('selectAll');
    const checkAll = document.getElementById('checkAll');

    const checkboxes = document.querySelectorAll(
        '.student-checkbox'
    );


    selectAll?.addEventListener('change', function () {

        checkboxes.forEach(function (checkbox) {

            checkbox.checked = selectAll.checked;

        });

    });


    checkAll?.addEventListener('click', function () {

        const allChecked = Array.from(checkboxes)
            .every(checkbox => checkbox.checked);

        checkboxes.forEach(function (checkbox) {

            checkbox.checked = !allChecked;

        });

        selectAll.checked = !allChecked;

    });

});

</script>

@endsection
