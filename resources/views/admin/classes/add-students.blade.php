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


    <form
        action="{{ route('classes.students.store', $class) }}"
        method="POST">

        @csrf


        <div class="card border-0 shadow-sm">

            <div class="card-body">


                {{-- Header tabel --}}
                <div class="d-flex justify-content-between align-items-center mb-3">

                    <div>

                        <h5 class="fw-bold mb-1">
                            Pilih Siswa
                        </h5>

                        <div class="text-muted small">
                            Centang siswa yang ingin dimasukkan ke kelas
                            {{ $class->name }}.
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

                                        Tidak ada siswa yang dapat ditambahkan.

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


                    <div>

                        <a
                            href="{{ route('classes.students', $class) }}"
                            class="btn btn-light border me-2">

                            Batal

                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary">

                            Simpan Siswa

                        </button>

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
