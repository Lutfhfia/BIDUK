@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <div class="text-muted small mb-2">Laporan / Buku Induk</div>
                <h3 class="fw-bold mb-1">Buku Induk</h3>
                <p class="text-muted mb-0">Cetak buku induk siswa sesuai parameter yang dipilih.</p>
            </div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Kembali</a>
        </div>

        <form method="GET" action="{{ route('buku-induk.index') }}" class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Tahun Ajaran</label>
                        <select name="academic_year_id" class="form-select">
                            @foreach ($academicYears as $year)
                                <option value="{{ $year->id }}" @selected($selectedAcademicYear == $year->id)>{{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Semester</label>
                        <select name="semester_id" class="form-select">
                            <option value="">Semua Semester</option>
                            @foreach ($semesters as $semester)
                                <option value="{{ $semester->id }}" @selected(request('semester_id') == $semester->id)>{{ $semester->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Kelas</label>
                        <select name="class_id" class="form-select">
                            <option value="">Semua Kelas</option>
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}" @selected(request('class_id') == $class->id)>{{ $class->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="aktif" @selected(request('status', 'aktif') === 'aktif')>Aktif</option>
                            <option value="tidak_aktif" @selected(request('status') === 'tidak_aktif')>Tidak Aktif</option>
                            <option value="" @selected(request('status') === '')>Semua Status</option>
                        </select>
                    </div>
                    <div class="col-md-9">
                        <label class="form-label">Cari Siswa</label>
                        <input name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Nama siswa...">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Tampilkan</button>
                    </div>
                </div>
            </div>
        </form>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <strong>Daftar Siswa</strong>
                <span class="badge text-bg-primary">{{ $students->count() }} siswa</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">No.</th>
                            <th>Nama</th>
                            <th>NIS</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            @php $query = request()->only(['academic_year_id', 'semester_id', 'class_id', 'status']); @endphp
                            <tr>
                                <td class="ps-4">{{ $loop->iteration }}</td>
                                <td class="fw-semibold">{{ $student->name }}</td>
                                <td>{{ $student->nis }}</td>
                                <td class="text-end pe-4">
                                    <a class="btn btn-sm btn-primary" target="_blank"
                                        href="{{ route('buku-induk.print', [$student, ...$query]) }}">Cetak / Preview</a>
                                    <a class="btn btn-sm btn-outline-danger"
                                        href="{{ route('buku-induk.print', [$student, ...$query, 'pdf' => 1]) }}">PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Tidak ada siswa sesuai filter.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
