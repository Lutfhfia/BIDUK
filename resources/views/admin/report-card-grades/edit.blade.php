@extends('layouts.app')

@section('title', 'Input Nilai Rapot')

@section('content')

<div class="container-fluid py-4">

    {{-- =====================================================
        HEADER
    ====================================================== --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="fw-bold mb-1">
                <i class="bi bi-journal-text me-2"></i>
                Input Nilai Rapot
            </h4>

            <p class="text-muted mb-0">
                Kelola isi rapot peserta didik.
            </p>
        </div>

        <a
            href="{{ route('report-card-grades.index', [
                'academic_year_id' => $class->academic_year_id,
                'class_id' => $class->id
            ]) }}"
            class="btn btn-outline-secondary"
        >
            <i class="bi bi-arrow-left me-1"></i>
            Kembali
        </a>

    </div>


    {{-- =====================================================
        ALERT
    ====================================================== --}}

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="bi bi-check-circle me-2"></i>

            {{ session('success') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="bi bi-exclamation-circle me-2"></i>

            {{ session('error') }}

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    @if($errors->any())

        <div class="alert alert-danger alert-dismissible fade show">

            <div class="fw-semibold mb-2">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Terdapat kesalahan:
            </div>

            <ul class="mb-0">

                @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>

        </div>

    @endif


    {{-- =====================================================
        FORM
    ====================================================== --}}
    <form
        method="POST"
        action="{{ route('report-card-grades.update', [
            'class' => $class->id,
            'student' => $student->id
        ]) }}"
    >

        @csrf

        @method('PUT')


        {{-- =================================================
            A. IDENTITAS PESERTA DIDIK
        ================================================== --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="fw-bold mb-0">
                    A. Identitas Peserta Didik
                </h5>

            </div>


            <div class="card-body">

                <div class="row g-3">

                    {{-- Nama --}}
                    <div class="col-md-6">

                        <label class="form-label small text-muted">
                            Nama Peserta Didik
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $student->name }}"
                            readonly
                        >

                    </div>


                    {{-- NISN --}}
                    <div class="col-md-3">

                        <label class="form-label small text-muted">
                            NISN
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $student->nisn ?? '-' }}"
                            readonly
                        >

                    </div>


                    {{-- NIS --}}
                    <div class="col-md-3">

                        <label class="form-label small text-muted">
                            NIS
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $student->nis ?? '-' }}"
                            readonly
                        >

                    </div>


                    {{-- Sekolah --}}
                    <div class="col-md-6">

                        <label class="form-label small text-muted">
                            Nama Sekolah
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="SDN 204 Palembang"
                            readonly
                        >

                    </div>


                    {{-- Kelas --}}
                    <div class="col-md-3">

                        <label class="form-label small text-muted">
                            Kelas
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $class->name }}"
                            readonly
                        >

                    </div>


                    {{-- Tahun Pelajaran --}}
                    <div class="col-md-3">

                        <label class="form-label small text-muted">
                            Tahun Pelajaran
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $class->academicYear->name ?? '-' }}"
                            readonly
                        >

                    </div>

                </div>

            </div>

        </div>


        {{-- =================================================
            B. INTRAKURIKULER
        ================================================== --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="fw-bold mb-1">
                    B. Intrakurikuler
                </h5>

                <small class="text-muted">
                    Nilai akhir dan capaian pembelajaran setiap mata pelajaran.
                </small>

            </div>


            <div class="card-body">

                @if($subjects->count() === 0)

                    <div class="alert alert-warning mb-0">

                        <i class="bi bi-exclamation-triangle me-2"></i>

                        Belum ada mata pelajaran untuk kelas
                        <strong>{{ $class->name }}</strong>.

                        Silakan atur mata pelajaran melalui Data Kelas.

                    </div>

                @else


                    {{-- =========================================
                        SEMESTER GANJIL
                    ========================================== --}}
                    @if($semesterGanjil)

                        <div class="mb-5">

                            <h6 class="fw-bold mb-3">
                                SEMESTER GANJIL
                            </h6>


                            <div class="table-responsive">

                                <table class="table table-bordered align-middle">

                                    <thead class="table-light">

                                        <tr>

                                            <th
                                                class="text-center"
                                                style="width: 60px;"
                                            >
                                                No.
                                            </th>

                                            <th style="min-width: 250px;">
                                                Mata Pelajaran
                                            </th>

                                            <th
                                                class="text-center"
                                                style="width: 160px;"
                                            >
                                                Nilai Akhir
                                            </th>

                                            <th style="min-width: 400px;">
                                                Capaian Pembelajaran
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        @foreach($subjects as $index => $subject)

                                            @php

                                                $grade = $grades->get(
                                                    $semesterGanjil->id . '_' . $subject->id
                                                );

                                            @endphp


                                            <tr>

                                                {{-- No --}}
                                                <td class="text-center">
                                                    {{ $index + 1 }}
                                                </td>


                                                {{-- Mata Pelajaran --}}
                                                <td>

                                                    <div class="fw-semibold">
                                                        {{ $subject->name }}
                                                    </div>

                                                    @if($subject->code)

                                                        <small class="text-muted">
                                                            {{ $subject->code }}
                                                        </small>

                                                    @endif

                                                </td>


                                                {{-- Nilai --}}
                                                <td>

                                                    <input
                                                        type="number"
                                                        name="ganjil_scores[{{ $subject->id }}]"
                                                        class="form-control text-center ganjil-score"
                                                        value="{{ old(
                                                            'ganjil_scores.' . $subject->id,
                                                            $grade?->score
                                                        ) }}"
                                                        min="0"
                                                        max="100"
                                                        step="0.01"
                                                        placeholder="0 - 100"
                                                    >

                                                </td>


                                                {{-- Capaian Pembelajaran --}}
                                                <td>

                                                    <textarea
                                                        name="ganjil_learning_outcomes[{{ $subject->id }}]"
                                                        class="form-control"
                                                        rows="2"
                                                        placeholder="Masukkan capaian pembelajaran..."
                                                    >{{ old(
                                                        'ganjil_learning_outcomes.' . $subject->id,
                                                        $grade?->learning_outcome
                                                    ) }}</textarea>

                                                </td>

                                            </tr>

                                        @endforeach

                                    </tbody>


                                    <tfoot>

                                        {{-- Jumlah --}}
                                        <tr>

                                            <th
                                                colspan="2"
                                                class="text-end"
                                            >
                                                Jumlah Nilai
                                            </th>

                                            <th>

                                                <input
                                                    type="text"
                                                    id="ganjil_total"
                                                    class="form-control text-center fw-semibold"
                                                    readonly
                                                >

                                            </th>

                                            <th></th>

                                        </tr>


                                        {{-- Rata-rata --}}
                                        <tr>

                                            <th
                                                colspan="2"
                                                class="text-end"
                                            >
                                                Rata-rata
                                            </th>

                                            <th>

                                                <input
                                                    type="text"
                                                    id="ganjil_average"
                                                    class="form-control text-center fw-semibold"
                                                    readonly
                                                >

                                            </th>

                                            <th></th>

                                        </tr>

                                    </tfoot>

                                </table>

                            </div>

                        </div>

                    @endif


                    {{-- =========================================
                        SEMESTER GENAP
                    ========================================== --}}
                    @if($semesterGenap)

                        <div>

                            <h6 class="fw-bold mb-3">
                                SEMESTER GENAP
                            </h6>


                            <div class="table-responsive">

                                <table class="table table-bordered align-middle">

                                    <thead class="table-light">

                                        <tr>

                                            <th
                                                class="text-center"
                                                style="width: 60px;"
                                            >
                                                No.
                                            </th>

                                            <th style="min-width: 250px;">
                                                Mata Pelajaran
                                            </th>

                                            <th
                                                class="text-center"
                                                style="width: 160px;"
                                            >
                                                Nilai Akhir
                                            </th>

                                            <th style="min-width: 400px;">
                                                Capaian Pembelajaran
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        @foreach($subjects as $index => $subject)

                                            @php

                                                $grade = $grades->get(
                                                    $semesterGenap->id . '_' . $subject->id
                                                );

                                            @endphp


                                            <tr>

                                                {{-- No --}}
                                                <td class="text-center">
                                                    {{ $index + 1 }}
                                                </td>


                                                {{-- Mata Pelajaran --}}
                                                <td>

                                                    <div class="fw-semibold">
                                                        {{ $subject->name }}
                                                    </div>

                                                    @if($subject->code)

                                                        <small class="text-muted">
                                                            {{ $subject->code }}
                                                        </small>

                                                    @endif

                                                </td>


                                                {{-- Nilai --}}
                                                <td>

                                                    <input
                                                        type="number"
                                                        name="genap_scores[{{ $subject->id }}]"
                                                        class="form-control text-center genap-score"
                                                        value="{{ old(
                                                            'genap_scores.' . $subject->id,
                                                            $grade?->score
                                                        ) }}"
                                                        min="0"
                                                        max="100"
                                                        step="0.01"
                                                        placeholder="0 - 100"
                                                    >

                                                </td>


                                                {{-- Capaian Pembelajaran --}}
                                                <td>

                                                    <textarea
                                                        name="genap_learning_outcomes[{{ $subject->id }}]"
                                                        class="form-control"
                                                        rows="2"
                                                        placeholder="Masukkan capaian pembelajaran..."
                                                    >{{ old(
                                                        'genap_learning_outcomes.' . $subject->id,
                                                        $grade?->learning_outcome
                                                    ) }}</textarea>

                                                </td>

                                            </tr>

                                        @endforeach

                                    </tbody>


                                    <tfoot>

                                        {{-- Jumlah --}}
                                        <tr>

                                            <th
                                                colspan="2"
                                                class="text-end"
                                            >
                                                Jumlah Nilai
                                            </th>

                                            <th>

                                                <input
                                                    type="text"
                                                    id="genap_total"
                                                    class="form-control text-center fw-semibold"
                                                    readonly
                                                >

                                            </th>

                                            <th></th>

                                        </tr>


                                        {{-- Rata-rata --}}
                                        <tr>

                                            <th
                                                colspan="2"
                                                class="text-end"
                                            >
                                                Rata-rata
                                            </th>

                                            <th>

                                                <input
                                                    type="text"
                                                    id="genap_average"
                                                    class="form-control text-center fw-semibold"
                                                    readonly
                                                >

                                            </th>

                                            <th></th>

                                        </tr>

                                    </tfoot>

                                </table>

                            </div>

                        </div>

                    @endif

                @endif

            </div>

        </div>


        {{-- =================================================
            C. P5
        ================================================== --}}

        {{--

            Bagian C tetap merupakan bagian C dalam struktur
            rapot, tetapi tidak dibuat sebagai fitur input
            sesuai kebutuhan BIDUK.

        --}}


        {{-- =================================================
            D. EKSTRAKURIKULER
        ================================================== --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="fw-bold mb-1">
                    D. Ekstrakurikuler
                </h5>

                <small class="text-muted">
                    Masukkan kegiatan ekstrakurikuler peserta didik.
                </small>

            </div>


            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered align-middle">

                        <thead class="table-light">

                            <tr>

                                <th
                                    class="text-center"
                                    style="width: 70px;"
                                >
                                    No.
                                </th>

                                <th>
                                    Kegiatan Ekstrakurikuler
                                </th>

                                <th>
                                    Keterangan
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @for($i = 1; $i <= 3; $i++)

                                <tr>

                                    <td class="text-center">
                                        {{ $i }}
                                    </td>

                                    <td>

                                        <input
                                            type="text"
                                            name="extracurricular[{{ $i }}][activity]"
                                            class="form-control"
                                            placeholder="Nama kegiatan"
                                        >

                                    </td>

                                    <td>

                                        <input
                                            type="text"
                                            name="extracurricular[{{ $i }}][description]"
                                            class="form-control"
                                            placeholder="Keterangan"
                                        >

                                    </td>

                                </tr>

                            @endfor

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


       {{-- =================================================
    E. PRESTASI
================================================== --}}
<div class="card border-0 shadow-sm mb-4">

    <div class="card-header bg-white py-3">

        <h5 class="fw-bold mb-1">
            E. Prestasi
        </h5>

        <small class="text-muted">
            Masukkan prestasi peserta didik.
        </small>

    </div>


    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-bordered align-middle">

                <thead class="table-light">

                    <tr>

                        <th
                            class="text-center"
                            style="width: 70px;"
                        >
                            No.
                        </th>

                        <th style="min-width: 250px;">
                            Jenis Prestasi
                        </th>

                        <th style="min-width: 180px;">
                            Tingkat
                        </th>

                        <th style="min-width: 300px;">
                            Keterangan
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @for($i = 1; $i <= 3; $i++)

                        @php
                            $achievement =
                                $achievementData[$i]
                                ?? [
                                    'type' => '',
                                    'level' => '',
                                    'description' => ''
                                ];
                        @endphp

                        <tr>

                            {{-- No --}}
                            <td class="text-center">
                                {{ $i }}
                            </td>


                            {{-- Jenis Prestasi --}}
                            <td>

                                <input
                                    type="text"
                                    name="achievements[{{ $i }}][type]"
                                    class="form-control"
                                    value="{{ old(
                                        'achievements.' . $i . '.type',
                                        $achievement['type']
                                    ) }}"
                                    placeholder="Contoh: Juara 1 Olimpiade Matematika"
                                >

                            </td>


                            {{-- Tingkat --}}
                            <td>

                                <select
                                    name="achievements[{{ $i }}][level]"
                                    class="form-select"
                                >

                                    <option value="">
                                        -- Pilih Tingkat --
                                    </option>

                                    <option
                                        value="Sekolah"
                                        @selected(
                                            old(
                                                'achievements.' . $i . '.level',
                                                $achievement['level']
                                            ) === 'Sekolah'
                                        )
                                    >
                                        Sekolah
                                    </option>

                                    <option
                                        value="Kecamatan"
                                        @selected(
                                            old(
                                                'achievements.' . $i . '.level',
                                                $achievement['level']
                                            ) === 'Kecamatan'
                                        )
                                    >
                                        Kecamatan
                                    </option>

                                    <option
                                        value="Kota"
                                        @selected(
                                            old(
                                                'achievements.' . $i . '.level',
                                                $achievement['level']
                                            ) === 'Kota'
                                        )
                                    >
                                        Kota
                                    </option>

                                    <option
                                        value="Provinsi"
                                        @selected(
                                            old(
                                                'achievements.' . $i . '.level',
                                                $achievement['level']
                                            ) === 'Provinsi'
                                        )
                                    >
                                        Provinsi
                                    </option>

                                    <option
                                        value="Nasional"
                                        @selected(
                                            old(
                                                'achievements.' . $i . '.level',
                                                $achievement['level']
                                            ) === 'Nasional'
                                        )
                                    >
                                        Nasional
                                    </option>

                                    <option
                                        value="Internasional"
                                        @selected(
                                            old(
                                                'achievements.' . $i . '.level',
                                                $achievement['level']
                                            ) === 'Internasional'
                                        )
                                    >
                                        Internasional
                                    </option>

                                </select>

                            </td>


                            {{-- Keterangan --}}
                            <td>

                                <input
                                    type="text"
                                    name="achievements[{{ $i }}][description]"
                                    class="form-control"
                                    value="{{ old(
                                        'achievements.' . $i . '.description',
                                        $achievement['description']
                                    ) }}"
                                    placeholder="Keterangan prestasi"
                                >

                            </td>

                        </tr>

                    @endfor

                </tbody>

            </table>

        </div>

        <div class="mt-2">

            <small class="text-muted">

                <i class="bi bi-info-circle me-1"></i>

                Kosongkan baris jika peserta didik tidak memiliki
                prestasi.

            </small>

        </div>

    </div>

</div>


        {{-- =================================================
            F. KETIDAKHADIRAN DAN KENAIKAN
        ================================================== --}}
        <div class="card border-0 shadow-sm mb-4">

            <div class="card-header bg-white py-3">

                <h5 class="fw-bold mb-1">
                    F. Ketidakhadiran dan Kenaikan
                </h5>

                <small class="text-muted">
                    Data ketidakhadiran diambil otomatis dari Rekap Absensi.
                    Keputusan kenaikan diisi pada bagian Kenaikan.
                </small>

            </div>


            <div class="card-body">

                {{-- =========================================
                    KETIDAKHADIRAN
                ========================================== --}}
                <h6 class="fw-bold mb-3">
                    Ketidakhadiran
                </h6>


                <div class="row g-4 mb-4">

                    {{-- Semester Ganjil --}}
                    <div class="col-md-6">

                        <div class="border rounded-3 p-3">

                            <div class="fw-semibold mb-3">
                                Semester Ganjil
                            </div>


                            {{-- Sakit --}}
                            <div class="mb-3">

                                <label class="form-label">
                                    Sakit
                                </label>

                                <div class="input-group">

                                    <input
                                        type="number"
                                        class="form-control"
                                        value="{{ $attendanceGanjil?->sakit ?? 0 }}"
                                        min="0"
                                        readonly
                                    >

                                    <span class="input-group-text">
                                        Hari
                                    </span>

                                </div>

                            </div>


                            {{-- Izin --}}
                            <div class="mb-3">

                                <label class="form-label">
                                    Izin
                                </label>

                                <div class="input-group">

                                    <input
                                        type="number"
                                        class="form-control"
                                        value="{{ $attendanceGanjil?->izin ?? 0 }}"
                                        min="0"
                                        readonly
                                    >

                                    <span class="input-group-text">
                                        Hari
                                    </span>

                                </div>

                            </div>


                            {{-- Tanpa Keterangan --}}
                            <div>

                                <label class="form-label">
                                    Tanpa Keterangan
                                </label>

                                <div class="input-group">

                                    <input
                                        type="number"
                                        class="form-control"
                                        value="{{ $attendanceGanjil?->tanpa_keterangan ?? 0 }}"
                                        min="0"
                                        readonly
                                    >

                                    <span class="input-group-text">
                                        Hari
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Semester Genap --}}
                    <div class="col-md-6">

                        <div class="border rounded-3 p-3">

                            <div class="fw-semibold mb-3">
                                Semester Genap
                            </div>


                            {{-- Sakit --}}
                            <div class="mb-3">

                                <label class="form-label">
                                    Sakit
                                </label>

                                <div class="input-group">

                                    <input
                                        type="number"
                                        class="form-control"
                                        value="{{ $attendanceGenap?->sakit ?? 0 }}"
                                        min="0"
                                        readonly
                                    >

                                    <span class="input-group-text">
                                        Hari
                                    </span>

                                </div>

                            </div>


                            {{-- Izin --}}
                            <div class="mb-3">

                                <label class="form-label">
                                    Izin
                                </label>

                                <div class="input-group">

                                    <input
                                        type="number"
                                        class="form-control"
                                        value="{{ $attendanceGenap?->izin ?? 0 }}"
                                        min="0"
                                        readonly
                                    >

                                    <span class="input-group-text">
                                        Hari
                                    </span>

                                </div>

                            </div>


                            {{-- Tanpa Keterangan --}}
                            <div>

                                <label class="form-label">
                                    Tanpa Keterangan
                                </label>

                                <div class="input-group">

                                    <input
                                        type="number"
                                        class="form-control"
                                        value="{{ $attendanceGenap?->tanpa_keterangan ?? 0 }}"
                                        min="0"
                                        readonly
                                    >

                                    <span class="input-group-text">
                                        Hari
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- =========================================
                    KENAIKAN
                ========================================== --}}
                <h6 class="fw-bold mb-3">
                    Kenaikan
                </h6>


                <div class="row g-3">

                    {{-- Keputusan --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Keputusan
                        </label>

                        <select
                            name="promotion_status"
                            class="form-select"
                        >

                            <option value="">
                                -- Pilih Keputusan --
                            </option>

                            <option value="Naik">
                                Naik
                            </option>

                            <option value="Tidak Naik">
                                Tidak Naik
                            </option>

                        </select>

                    </div>


                    {{-- Ke Kelas --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Ke Kelas
                        </label>

                        <input
                            type="text"
                            name="promotion_class"
                            class="form-control"
                            placeholder="Contoh: 5A"
                        >

                    </div>


                    {{-- Tanggal --}}
                    <div class="col-md-4">

                        <label class="form-label fw-semibold">
                            Tanggal
                        </label>

                        <input
                            type="date"
                            name="promotion_date"
                            class="form-control"
                        >

                    </div>

                </div>

            </div>

        </div>


        {{-- =================================================
            BUTTON
        ================================================== --}}
        <div class="d-flex justify-content-end gap-2 mb-5">

            <a
                href="{{ route('report-card-grades.index', [
                    'academic_year_id' => $class->academic_year_id,
                    'class_id' => $class->id
                ]) }}"
                class="btn btn-outline-secondary"
            >
                Batal
            </a>


            <button
                type="submit"
                class="btn btn-success"
            >

                <i class="bi bi-save me-1"></i>

                Simpan Data Rapot

            </button>

        </div>

    </form>

</div>

@endsection


{{-- =========================================================
    JAVASCRIPT
    JUMLAH NILAI + RATA-RATA OTOMATIS
========================================================== --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {


    /**
     * ======================================================
     * FUNGSI HITUNG NILAI
     * ======================================================
     */
    function calculateScores(
        inputSelector,
        totalSelector,
        averageSelector
    ) {

        const inputs = document.querySelectorAll(
            inputSelector
        );

        const totalInput = document.querySelector(
            totalSelector
        );

        const averageInput = document.querySelector(
            averageSelector
        );


        if (
            !totalInput ||
            !averageInput
        ) {
            return;
        }


        function calculate() {

            let total = 0;

            let count = 0;


            inputs.forEach(function (input) {

                const value = parseFloat(
                    input.value
                );


                /*
                 * Hanya nilai yang benar-benar
                 * diisi yang dihitung.
                 */
                if (!isNaN(value)) {

                    total += value;

                    count++;

                }

            });


            /*
             * JUMLAH NILAI
             */
            if (count > 0) {

                totalInput.value =
                    total.toFixed(2);

            } else {

                totalInput.value = '';

            }


            /*
             * RATA-RATA
             */
            if (count > 0) {

                averageInput.value =
                    (total / count).toFixed(2);

            } else {

                averageInput.value = '';

            }

        }


        /*
         * Hitung ketika halaman pertama dibuka.
         */
        calculate();


        /*
         * Hitung ulang setiap kali nilai berubah.
         */
        inputs.forEach(function (input) {

            input.addEventListener(
                'input',
                calculate
            );

            input.addEventListener(
                'change',
                calculate
            );

        });

    }


    /**
     * ======================================================
     * SEMESTER GANJIL
     * ======================================================
     */
    calculateScores(
        '.ganjil-score',
        '#ganjil_total',
        '#ganjil_average'
    );


    /**
     * ======================================================
     * SEMESTER GENAP
     * ======================================================
     */
    calculateScores(
        '.genap-score',
        '#genap_total',
        '#genap_average'
    );

});

</script>

@endpush
