@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <div class="text-muted small mb-1">
                Akademik / Semester
            </div>

            <h3 class="fw-bold mb-1">
                Semester
            </h3>

            <div class="text-muted">
                Kelola semester Ganjil dan Genap berdasarkan tahun ajaran.
            </div>
        </div>

        <a href="{{ route('semesters.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Semester
        </a>

    </div>


    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">

            <i class="bi bi-check-circle-fill me-2"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>
    @endif


    {{-- Alert error --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            <ul class="mb-0">
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


    {{-- Card tabel --}}
    <div class="card border-0 shadow-sm">

        <div class="card-body">

            {{-- Header tabel --}}
            <div class="mb-3">

                <h5 class="fw-bold mb-1">
                    Daftar Semester
                </h5>

                <div class="text-muted small">
                    Semester yang tersedia berdasarkan tahun ajaran.
                </div>

            </div>


            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-light">

                        <tr>
                            <th width="60">No</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Status Tahun Ajaran</th>
                            <th width="180" class="text-center">
                                Aksi
                            </th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($semesters as $semester)

                            <tr>

                                {{-- No --}}
                                <td>
                                    {{ $semesters->firstItem() + $loop->index }}
                                </td>


                                {{-- Tahun Ajaran --}}
                                <td>
                                    <div class="fw-semibold">
                                        {{ $semester->academicYear->name }}
                                    </div>
                                </td>


                                {{-- Semester --}}
                                <td>

                                    @if($semester->name === 'Ganjil')

                                        <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                            Ganjil
                                        </span>

                                    @else

                                        <span class="badge bg-warning-subtle text-warning-emphasis px-3 py-2">
                                            Genap
                                        </span>

                                    @endif

                                </td>


                                {{-- Status Tahun Ajaran --}}
                                <td>

                                    @if($semester->academicYear->status === 'active')

                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            Aktif
                                        </span>

                                    @else

                                        <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                                            Tidak Aktif
                                        </span>

                                    @endif

                                </td>


                                {{-- Aksi --}}
                                <td>

                                    <div class="d-flex justify-content-center gap-1">

                                        {{-- Detail --}}
                                        <a href="{{ route('semesters.show', $semester) }}"
                                           class="btn btn-sm btn-outline-info"
                                           title="Detail">

                                            <i class="bi bi-eye"></i>

                                        </a>


                                        {{-- Edit --}}
                                        <a href="{{ route('semesters.edit', $semester) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit">

                                            <i class="bi bi-pencil"></i>

                                        </a>


                                        {{-- Hapus --}}
                                        <form action="{{ route('semesters.destroy', $semester) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus semester {{ $semester->name }} pada tahun ajaran {{ $semester->academicYear->name }}?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Hapus">

                                                <i class="bi bi-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="text-center py-5">

                                    <div class="text-muted">

                                        <i class="bi bi-calendar2-x fs-1 d-block mb-3"></i>

                                        <h6 class="fw-semibold">
                                            Belum ada semester
                                        </h6>

                                        <p class="small mb-3">
                                            Silakan tambahkan semester terlebih dahulu.
                                        </p>

                                        <a href="{{ route('semesters.create') }}"
                                           class="btn btn-primary btn-sm">

                                            <i class="bi bi-plus-lg me-1"></i>
                                            Tambah Semester

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if($semesters->hasPages())

                <div class="d-flex justify-content-end mt-3">

                    {{ $semesters->links() }}

                </div>

            @endif

        </div>

    </div>

</div>

@endsection
