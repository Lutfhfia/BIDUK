@extends('layouts.app')

@section('title', 'Import Data Siswa')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Data Siswa</a></li>
    <li class="breadcrumb-item active">Import Excel</li>
@endsection

@section('content')
<div class="fade-in-up">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-0">
                <i class="bi bi-file-earmark-arrow-up text-success me-2"></i>Import Data Siswa
            </h4>
        </div>
        <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="fw-semibold mb-1"><i class="bi bi-exclamation-circle me-1"></i> Import gagal:</div>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="biduk-card">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-semibold text-dark mb-2">Pilih File Excel (.xlsx / .xls)</label>
                            <input type="file" name="file" class="form-control form-control-lg" accept=".xlsx,.xls" required>
                        </div>

                        <div class="d-flex gap-2 flex-wrap">
                            <button type="submit" class="btn btn-biduk-primary px-4 py-2">
                                <i class="bi bi-upload me-1"></i> Mulai Import
                            </button>
                            <a href="{{ route('students.export.excel') }}" class="btn btn-outline-primary px-3 py-2">
                                <i class="bi bi-download me-1"></i> Download Format Template
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
