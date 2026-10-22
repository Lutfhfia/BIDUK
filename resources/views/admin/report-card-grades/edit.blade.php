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

        <div class="d-flex gap-2">
            @if((int) $class->grade_level === 6)
                <a
                    href="{{ route('final-grades.edit', [
                        'class' => $class->id,
                        'student' => $student->id,
                        'return_url' => request()->fullUrl(),
                    ]) }}"
                    class="btn btn-outline-info"
                >
                    <i class="bi bi-award me-1"></i>
                    Nilai Akhir / Ijazah
                </a>
            @endif

            <a
                href="{{ $returnUrl ?? session('report_card_grades_index_url', route('report-card-grades.index', ['academic_year_id' => $class->academic_year_id, 'class_id' => $class->id])) }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>
                Kembali
            </a>
        </div>

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

        <input type="hidden" name="return_url" value="{{ $returnUrl ?? '' }}">


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

                    <div class="alert alert-warning mb-0 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <i class="bi bi-exclamation-triangle me-2"></i>

                            Kelas <strong>{{ $class->name }}</strong> belum memiliki mata pelajaran.
                            Nilai belum dapat diinput sampai mata pelajaran kelas disetel.
                        </div>

                        @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('classes.edit'))
                            <a href="{{ route('classes.edit', $class) }}" class="btn btn-sm btn-warning text-nowrap">
                                <i class="bi bi-gear me-1"></i>
                                Seting Kelas
                            </a>
                        @else
                            <span class="small text-muted">
                                Hubungi admin yang memiliki izin edit kelas untuk menyetel mata pelajaran.
                            </span>
                        @endif

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
                                                Pengetahuan & Keterampilan
                                            </th>

                                            <th style="min-width: 500px;">
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
                                                    <div class="small fw-semibold mb-1">Pengetahuan</div>
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
                                                    <select name="ganjil_predicates[{{ $subject->id }}]" class="form-select form-select-sm mt-1" aria-label="Predikat Pengetahuan">
                                                        <option value="">Predikat</option>
                                                        @foreach(['A', 'B', 'C', 'D'] as $predicate)
                                                            <option value="{{ $predicate }}" @selected(old('ganjil_predicates.' . $subject->id, $grade?->knowledge_predicate) === $predicate)>{{ $predicate }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="small fw-semibold mt-3 mb-1">Keterampilan</div>
                                                    <input
                                                        type="number"
                                                        name="ganjil_skill_scores[{{ $subject->id }}]"
                                                        class="form-control text-center"
                                                        value="{{ old('ganjil_skill_scores.' . $subject->id, $grade?->skill_score) }}"
                                                        min="0"
                                                        max="100"
                                                        step="0.01"
                                                        placeholder="0 - 100"
                                                    >
                                                    <select name="ganjil_skill_predicates[{{ $subject->id }}]" class="form-select form-select-sm mt-1" aria-label="Predikat Keterampilan">
                                                        <option value="">Predikat</option>
                                                        @foreach(['A', 'B', 'C', 'D'] as $predicate)
                                                            <option value="{{ $predicate }}" @selected(old('ganjil_skill_predicates.' . $subject->id, $grade?->skill_predicate) === $predicate)>{{ $predicate }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>


                                                {{-- Capaian Pembelajaran --}}
                                                <td>
                                                    <label class="small fw-semibold">Pengetahuan</label>
                                                    <textarea
                                                        name="ganjil_learning_outcomes[{{ $subject->id }}]"
                                                        class="form-control"
                                                        rows="2"
                                                        placeholder="Masukkan capaian pembelajaran..."
                                                    >{{ old(
                                                        'ganjil_learning_outcomes.' . $subject->id,
                                                        $grade?->learning_outcome
                                                    ) }}</textarea>
                                                    <label class="small fw-semibold mt-2">Keterampilan</label>
                                                    <textarea
                                                        name="ganjil_skill_learning_outcomes[{{ $subject->id }}]"
                                                        class="form-control"
                                                        rows="2"
                                                        placeholder="Capaian keterampilan..."
                                                    >{{ old('ganjil_skill_learning_outcomes.' . $subject->id, $grade?->skill_learning_outcome) }}</textarea>
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

                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">
                                    SEMESTER GENAP
                                </h6>

                                @if((int) $class->grade_level === 6)
                                    <a
                                        href="{{ route('final-grades.edit', ['class' => $class->id, 'student' => $student->id, 'return_url' => request()->fullUrl()]) }}"
                                        class="btn btn-sm btn-info text-white shadow-sm"
                                    >
                                        <i class="bi bi-award me-1"></i>
                                        Tabel Nilai Akhir / Ijazah Kelas 6
                                    </a>
                                @endif
                            </div>

                            @if((int) $class->grade_level === 6)
                                <div class="alert alert-info border-0 shadow-sm d-flex align-items-center justify-content-between mb-3 py-2 px-3">
                                    <div class="small">
                                        <i class="bi bi-info-circle-fill me-2 fs-6"></i>
                                        <strong>Catatan Kelas 6:</strong> Data rapor semester ini akan menjadi komponen perhitungan Nilai Sekolah (rata-rata 4 semester) pada tabel Nilai Akhir / Ijazah.
                                    </div>
                                    <a
                                        href="{{ route('final-grades.edit', ['class' => $class->id, 'student' => $student->id, 'return_url' => request()->fullUrl()]) }}"
                                        class="btn btn-sm btn-outline-info bg-white text-info text-nowrap ms-3"
                                    >
                                        Buka Nilai Akhir &rarr;
                                    </a>
                                </div>
                            @endif


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
                                                Pengetahuan & Keterampilan
                                            </th>

                                            <th style="min-width: 500px;">
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
                                                    <div class="small fw-semibold mb-1">Pengetahuan</div>
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
                                                    <select name="genap_predicates[{{ $subject->id }}]" class="form-select form-select-sm mt-1" aria-label="Predikat Pengetahuan">
                                                        <option value="">Predikat</option>
                                                        @foreach(['A', 'B', 'C', 'D'] as $predicate)
                                                            <option value="{{ $predicate }}" @selected(old('genap_predicates.' . $subject->id, $grade?->knowledge_predicate) === $predicate)>{{ $predicate }}</option>
                                                        @endforeach
                                                    </select>
                                                    <div class="small fw-semibold mt-3 mb-1">Keterampilan</div>
                                                    <input
                                                        type="number"
                                                        name="genap_skill_scores[{{ $subject->id }}]"
                                                        class="form-control text-center"
                                                        value="{{ old('genap_skill_scores.' . $subject->id, $grade?->skill_score) }}"
                                                        min="0"
                                                        max="100"
                                                        step="0.01"
                                                        placeholder="0 - 100"
                                                    >
                                                    <select name="genap_skill_predicates[{{ $subject->id }}]" class="form-select form-select-sm mt-1" aria-label="Predikat Keterampilan">
                                                        <option value="">Predikat</option>
                                                        @foreach(['A', 'B', 'C', 'D'] as $predicate)
                                                            <option value="{{ $predicate }}" @selected(old('genap_skill_predicates.' . $subject->id, $grade?->skill_predicate) === $predicate)>{{ $predicate }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>


                                                {{-- Capaian Pembelajaran --}}
                                                <td>
                                                    <label class="small fw-semibold">Pengetahuan</label>
                                                    <textarea
                                                        name="genap_learning_outcomes[{{ $subject->id }}]"
                                                        class="form-control"
                                                        rows="2"
                                                        placeholder="Masukkan capaian pembelajaran..."
                                                    >{{ old(
                                                        'genap_learning_outcomes.' . $subject->id,
                                                        $grade?->learning_outcome
                                                    ) }}</textarea>
                                                    <label class="small fw-semibold mt-2">Keterampilan</label>
                                                    <textarea
                                                        name="genap_skill_learning_outcomes[{{ $subject->id }}]"
                                                        class="form-control"
                                                        rows="2"
                                                        placeholder="Capaian keterampilan..."
                                                    >{{ old('genap_skill_learning_outcomes.' . $subject->id, $grade?->skill_learning_outcome) }}</textarea>
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
                    F. Ketidakhadiran dan {{ (int) $class->grade_level === 6 ? 'Status Kelulusan' : 'Kenaikan' }}
                </h5>

                <small class="text-muted">
                    Data ketidakhadiran diambil otomatis dari Rekap Absensi.
                    @if ((int) $class->grade_level === 6)
                        Status kelulusan diatur pada halaman Nilai Akhir / Ijazah.
                    @else
                        Keputusan kenaikan diisi pada bagian Kenaikan.
                    @endif
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


                @if ((int) $class->grade_level === 6)
                    <input
                        type="hidden"
                        name="promotion_status"
                        value="{{ old('promotion_status', $promotion?->promotion_status) }}"
                    >
                    <input
                        type="hidden"
                        name="promotion_class"
                        value="{{ old('promotion_class', $promotion?->promotion_class) }}"
                    >
                    <input
                        type="hidden"
                        name="promotion_date"
                        value="{{ old('promotion_date', $promotion?->promotion_date?->format('Y-m-d')) }}"
                    >

                    <div class="alert alert-info mb-0">
                        <div class="fw-semibold mb-1">
                            Status akhir kelas 6 adalah Lulus atau Tidak Lulus.
                        </div>
                        <div class="small mb-3">
                            Status ini akan digunakan pada cetak rapor kelas 6.
                        </div>
                        <a
                            href="{{ route('final-grades.edit', [
                                'class' => $class->id,
                                'student' => $student->id,
                                'return_url' => request()->fullUrl(),
                            ]) }}"
                            class="btn btn-sm btn-outline-info"
                        >
                            <i class="bi bi-award me-1"></i>
                            Atur Status Kelulusan
                        </a>
                    </div>
                @else
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

<option
    value="Naik"
    @selected(old('promotion_status', $promotion?->promotion_status) === 'Naik')
>
    Naik
</option>

<option
    value="Tidak Naik"
    @selected(old('promotion_status', $promotion?->promotion_status) === 'Tidak Naik')
>
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
                        value="{{ old('promotion_class', $promotion?->promotion_class) }}"
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
    value="{{ old(
        'promotion_date',
        $promotion?->promotion_date?->format('Y-m-d')
    ) }}"
>

                    </div>
                    @endif

                </div>

            </div>

        </div>


        {{-- =================================================
            BUTTON
        ================================================== --}}
        <div class="d-flex justify-content-end gap-2 mb-5">

            <a
                href="{{ $returnUrl ?? session('report_card_grades_index_url', route('report-card-grades.index', ['academic_year_id' => $class->academic_year_id, 'class_id' => $class->id])) }}"
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
