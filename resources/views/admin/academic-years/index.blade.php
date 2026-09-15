@extends('layouts.app')

@section('content')

<div class="container-fluid py-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <div class="text-muted small mb-1">
                Akademik / Tahun Ajaran
            </div>

            <h3 class="fw-bold mb-1">
                Tahun Ajaran
            </h3>

            <div class="text-muted">
                Kelola tahun ajaran yang digunakan dalam sistem BIDUK.
            </div>
        </div>

        <a href="{{ route('academic-years.create') }}"
           class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>
            Tambah Tahun Ajaran
        </a>

    </div>


    {{-- Alert sukses --}}
    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show"
             role="alert">

            <i class="bi bi-check-circle-fill me-2"></i>

            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>

        </div>

    @endif


    {{-- Alert error --}}
    @if($errors->has('delete'))

        <div class="alert alert-danger alert-dismissible fade show"
             role="alert">

            <i class="bi bi-exclamation-triangle-fill me-2"></i>

            {{ $errors->first('delete') }}

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
                    Daftar Tahun Ajaran
                </h5>

                <div class="text-muted small">
                    Daftar tahun ajaran yang tersedia dalam sistem.
                </div>

            </div>


            {{-- Tabel --}}
            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead class="table-light">

                        <tr>

                            <th width="60">
                                No
                            </th>

                            <th>
                                Tahun Ajaran
                            </th>

                            <th>
                                Semester Berjalan
                            </th>

                            <th>
                                Tanggal Mulai
                            </th>

                            <th>
                                Tanggal Selesai
                            </th>

                            <th>
                                Status
                            </th>

                            <th width="130"
                                class="text-center">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($academicYears as $academicYear)

                            <tr>

                                {{-- Nomor --}}
                                <td>
                                    {{ $academicYears->firstItem() + $loop->index }}
                                </td>


                                {{-- Tahun Ajaran --}}
                                <td>

                                    <div class="fw-semibold">
                                        {{ $academicYear->name }}
                                    </div>

                                    @if($academicYear->status === 'active')

                                        <div class="text-success small mt-1">

                                            <i class="bi bi-check-circle-fill me-1"></i>

                                            Tahun ajaran berjalan

                                        </div>

                                    @endif

                                </td>


                                {{-- Semester Berjalan --}}
                                <td>

                                    @if($academicYear->current_semester === 'Ganjil')

                                        <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                            Ganjil
                                        </span>

                                    @elseif($academicYear->current_semester === 'Genap')

                                        <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                            Genap
                                        </span>

                                    @else

                                        <span class="text-muted small">
                                            Belum ditentukan
                                        </span>

                                    @endif

                                </td>


                                {{-- Tanggal Mulai --}}
                                <td>
                                    {{ $academicYear->start_date?->format('d/m/Y') ?? '-' }}
                                </td>


                                {{-- Tanggal Selesai --}}
                                <td>
                                    {{ $academicYear->end_date?->format('d/m/Y') ?? '-' }}
                                </td>


                                {{-- Status --}}
                                <td>

                                    @if($academicYear->status === 'active')

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
                                        <a href="{{ route('academic-years.show', $academicYear) }}"
                                           class="btn btn-sm btn-outline-info"
                                           title="Detail">

                                            <i class="bi bi-eye"></i>

                                        </a>


                                        {{-- Edit --}}
                                        <a href="{{ route('academic-years.edit', $academicYear) }}"
                                           class="btn btn-sm btn-outline-warning"
                                           title="Edit">

                                            <i class="bi bi-pencil"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7"
                                    class="text-center py-5">

                                    <div class="text-muted">

                                        <i class="bi bi-calendar-x fs-1 d-block mb-3"></i>

                                        <h6 class="fw-semibold">
                                            Belum ada tahun ajaran
                                        </h6>

                                        <p class="small mb-3">
                                            Silakan tambahkan tahun ajaran terlebih dahulu.
                                        </p>

                                        <a href="{{ route('academic-years.create') }}"
                                           class="btn btn-primary btn-sm">

                                            <i class="bi bi-plus-lg me-1"></i>

                                            Tambah Tahun Ajaran

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if($academicYears->hasPages())

                <div class="d-flex justify-content-end mt-3">

                    {{ $academicYears->links() }}

                </div>

            @endif

        </div>

    </div>

</div>

@endsection
