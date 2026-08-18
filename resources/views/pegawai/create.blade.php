@extends('layouts.app')

@section('title', 'Tambah Pegawai')

@section('breadcrumb')

    <li class="breadcrumb-item">
        <a href="{{ route('dashboard') }}">
            Dashboard
        </a>
    </li>

    <li class="breadcrumb-item">
        <a href="{{ route('pegawai.index') }}">
            Data Pegawai
        </a>
    </li>

    <li class="breadcrumb-item active">
        Tambah Pegawai
    </li>

@endsection


@section('content')

<div class="fade-in-up">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1 fw-bold">
                <i class="bi bi-person-plus me-2 text-success"></i>
                Tambah Pegawai Baru
            </h4>

            <p class="text-muted mb-0">
                Lengkapi data pegawai untuk ditambahkan ke sistem BIDUK.
            </p>

        </div>


        <a
            href="{{ route('pegawai.index') }}"
            class="btn btn-biduk-outline"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>

    </div>


    <form
        action="{{ route('pegawai.store') }}"
        method="POST"
        enctype="multipart/form-data"
    >

        @include('pegawai._form')

    </form>

</div>

@endsection