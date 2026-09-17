@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Data Siswa</a></li>
    <li class="breadcrumb-item active">Edit — {{ $student->name }}</li>
@endsection

@section('content')

    <style>
        .biduk-accordion {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .biduk-accordion-item {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .04);
        }

        .biduk-accordion-header {
            width: 100%;
            border: 0;
            background: #fff;
            padding: 17px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: left;
            color: #1f2937;
            font-weight: 600;
        }

        .biduk-accordion-header:hover {
            background: #f8fafc;
        }

        .biduk-accordion-header .section-title {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .biduk-accordion-header .section-icon {
            width: 34px;
            height: 34px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #ecfdf5;
            color: #16a34a;
            flex-shrink: 0;
        }

        .biduk-accordion-header .arrow {
            color: #6b7280;
            transition: transform .25s ease;
        }

        .biduk-accordion-header:not(.collapsed) .arrow {
            transform: rotate(180deg);
        }

        .biduk-accordion-body {
            border-top: 1px solid #e5e7eb;
            padding: 24px;
        }

        .biduk-accordion-body>.biduk-card {
            border: 0 !important;
            box-shadow: none !important;
            margin-bottom: 0 !important;
        }

        .biduk-accordion-body>.biduk-card>.card-header {
            display: none;
        }

        .biduk-accordion-body>.biduk-card>.card-body {
            padding: 0;
        }

        .year-data-card {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
        }

        .year-data-header {
            padding: 12px 15px;
            background: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            color: #374151;
        }

        .biduk-period-table th,
        .biduk-health-table th {
            background: #f8fafc;
            font-size: .85rem;
            font-weight: 600;
        }

        .biduk-period-table td,
        .biduk-period-table th,
        .biduk-health-table td,
        .biduk-health-table th {
            vertical-align: middle;
        }

        .add-year-panel {
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            padding: 15px;
            background: #fafafa;
        }

        .health-hidden-record {
            display: none;
        }
    </style>
    <div class="fade-in-up">
        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">
                    <i class="bi bi-pencil-square text-warning me-2"></i>Edit Data Siswa
                </h4>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                    {{ $student->name }} — NIS: {{ $student->nis }}
                </p>
            </div>
            <a href="{{ route('students.index') }}" class="btn btn-biduk-outline">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        {{-- Validation Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Terdapat kesalahan:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('students.update', $student) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')


            {{-- =========================================================
                 HEADER BUKU INDUK
            ========================================================== --}}
            <div class="biduk-card mb-4">
                <div class="card-header">
                    <i class="bi bi-card-heading text-success me-2"></i>
                    Identitas Administratif Buku Induk
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="nis" class="form-label">Nomor Induk Siswa <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nis" id="nis"
                                class="form-control @error('nis') is-invalid @enderror"
                                value="{{ old('nis', $student->nis) }}" required>
                            @error('nis')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="nisn" class="form-label">Nomor Induk Siswa Nasional <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="nisn" id="nisn"
                                class="form-control @error('nisn') is-invalid @enderror"
                                value="{{ old('nisn', $student->nisn) }}" required>
                            @error('nisn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="school_code" class="form-label">Nomor Kode Sekolah</label>
                            <input type="text" name="school_code" id="school_code"
                                class="form-control @error('school_code') is-invalid @enderror"
                                value="{{ old('school_code', $student->school_code) }}">
                            @error('school_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="district_code" class="form-label">Nomor Kode Kecamatan</label>
                            <input type="text" name="district_code" id="district_code"
                                class="form-control @error('district_code') is-invalid @enderror"
                                value="{{ old('district_code', $student->district_code) }}">
                            @error('district_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="city_code" class="form-label">Nomor Kode Kab/Kota</label>
                            <input type="text" name="city_code" id="city_code"
                                class="form-control @error('city_code') is-invalid @enderror"
                                value="{{ old('city_code', $student->city_code) }}">
                            @error('city_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="province_code" class="form-label">Nomor Kode Provinsi</label>
                            <input type="text" name="province_code" id="province_code"
                                class="form-control @error('province_code') is-invalid @enderror"
                                value="{{ old('province_code', $student->province_code) }}">
                            @error('province_code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="student_number" class="form-label">Nomor Urut</label>
                            <input type="text" name="student_number" id="student_number"
                                class="form-control @error('student_number') is-invalid @enderror"
                                value="{{ old('student_number', $student->student_number) }}">
                            @error('student_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>


            <div class="biduk-accordion" id="dataSiswaAccordion">

                {{-- A. KETERANGAN SISWA --}}
                <div class="biduk-accordion-item">
                    <button type="button" class="biduk-accordion-header" data-bs-toggle="collapse"
                        data-bs-target="#sectionA" aria-expanded="true" aria-controls="sectionA">
                        <span class="section-title">
                            <span class="section-icon"><i class="bi bi-person-fill"></i></span>
                            <span>A. Keterangan Siswa</span>
                        </span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </button>

                    <div id="sectionA" class="collapse show" data-bs-parent="#dataSiswaAccordion">
                        <div class="biduk-accordion-body">
                            <div class="biduk-card mb-4">
                                <div class="card-header">
                                    <i class="bi bi-person-fill text-success me-2"></i>Keterangan Siswa
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="nis" class="form-label">Nomor Induk Siswa (NIS) <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="nis" id="nis"
                                                class="form-control @error('nis') is-invalid @enderror"
                                                value="{{ old('nis', $student->nis) }}" required>
                                            @error('nis')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="nisn" class="form-label">NISN <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="nisn" id="nisn"
                                                class="form-control @error('nisn') is-invalid @enderror"
                                                value="{{ old('nisn', $student->nisn) }}" required>
                                            @error('nisn')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="name" class="form-label">Nama Peserta Didik <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="name" id="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $student->name) }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="gender" class="form-label">Jenis Kelamin <span
                                                    class="text-danger">*</span></label>
                                            <select name="gender" id="gender"
                                                class="form-select @error('gender') is-invalid @enderror" required>
                                                <option value="">Pilih...</option>
                                                <option value="L"
                                                    {{ old('gender', $student->gender) == 'L' ? 'selected' : '' }}>
                                                    Laki-laki</option>
                                                <option value="P"
                                                    {{ old('gender', $student->gender) == 'P' ? 'selected' : '' }}>
                                                    Perempuan</option>
                                            </select>
                                            @error('gender')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="birth_place" class="form-label">Tempat Lahir</label>
                                            <input type="text" name="birth_place" id="birth_place"
                                                class="form-control @error('birth_place') is-invalid @enderror"
                                                value="{{ old('birth_place', $student->birth_place) }}">
                                            @error('birth_place')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="birth_date" class="form-label">Tanggal Lahir</label>
                                            <input type="date" name="birth_date" id="birth_date"
                                                class="form-control @error('birth_date') is-invalid @enderror"
                                                value="{{ old('birth_date', $student->birth_date?->format('Y-m-d')) }}">
                                            @error('birth_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="religion" class="form-label">Agama</label>
                                            <select name="religion" id="religion"
                                                class="form-select @error('religion') is-invalid @enderror">
                                                <option value="">Pilih...</option>
                                                @foreach (['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'] as $agama)
                                                    <option value="{{ $agama }}"
                                                        {{ old('religion', $student->religion) == $agama ? 'selected' : '' }}>
                                                        {{ $agama }}</option>
                                                @endforeach
                                            </select>
                                            @error('religion')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="citizenship" class="form-label">Kewarganegaraan</label>
                                            <input type="text" name="citizenship" id="citizenship"
                                                class="form-control @error('citizenship') is-invalid @enderror"
                                                value="{{ old('citizenship', $student->citizenship ?? 'WNI') }}">
                                            @error('citizenship')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-8">
                                            <label for="address" class="form-label">Alamat</label>
                                            <textarea name="address" id="address" class="form-control @error('address') is-invalid @enderror" rows="2">{{ old('address', $student->address) }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="rt_rw" class="form-label">RT/RW</label>
                                            <input type="text" name="rt_rw" id="rt_rw"
                                                class="form-control @error('rt_rw') is-invalid @enderror"
                                                value="{{ old('rt_rw', $student->rt_rw) }}">
                                            @error('rt_rw')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="village" class="form-label">Desa/Kelurahan</label>
                                            <input type="text" name="village" id="village"
                                                class="form-control @error('village') is-invalid @enderror"
                                                value="{{ old('village', $student->village) }}">
                                            @error('village')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="district" class="form-label">Kecamatan</label>
                                            <input type="text" name="district" id="district"
                                                class="form-control @error('district') is-invalid @enderror"
                                                value="{{ old('district', $student->district) }}">
                                            @error('district')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="city" class="form-label">Kabupaten/Kota</label>
                                            <input type="text" name="city" id="city"
                                                class="form-control @error('city') is-invalid @enderror"
                                                value="{{ old('city', $student->city) }}">
                                            @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="province" class="form-label">Provinsi</label>
                                            <input type="text" name="province" id="province"
                                                class="form-control @error('province') is-invalid @enderror"
                                                value="{{ old('province', $student->province) }}">
                                            @error('province')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="postal_code" class="form-label">Kode Pos</label>
                                            <input type="text" name="postal_code" id="postal_code"
                                                class="form-control @error('postal_code') is-invalid @enderror"
                                                value="{{ old('postal_code', $student->postal_code) }}">
                                            @error('postal_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="phone_number" class="form-label">Nomor Telepon</label>
                                            <input type="text" name="phone_number" id="phone_number"
                                                class="form-control @error('phone_number') is-invalid @enderror"
                                                value="{{ old('phone_number', $student->phone_number) }}">
                                            @error('phone_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="living_with" class="form-label">Bertempat Tinggal Pada</label>
                                            <input type="text" name="living_with" id="living_with"
                                                class="form-control @error('living_with') is-invalid @enderror"
                                                value="{{ old('living_with', $student->living_with) }}">
                                            @error('living_with')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="distance_to_school" class="form-label">Jarak ke Sekolah</label>
                                            <input type="text" name="distance_to_school" id="distance_to_school"
                                                class="form-control @error('distance_to_school') is-invalid @enderror"
                                                value="{{ old('distance_to_school', $student->distance_to_school) }}">
                                            @error('distance_to_school')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="child_position" class="form-label">Anak ke-</label>
                                            <input type="number" min="1" name="child_position"
                                                id="child_position"
                                                class="form-control @error('child_position') is-invalid @enderror"
                                                value="{{ old('child_position', $student->child_position) }}">
                                            @error('child_position')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="siblings_biological" class="form-label">Jumlah Saudara
                                                Kandung</label>
                                            <input type="number" min="0" name="siblings_biological"
                                                id="siblings_biological"
                                                class="form-control @error('siblings_biological') is-invalid @enderror"
                                                value="{{ old('siblings_biological', $student->siblings_biological) }}">
                                            @error('siblings_biological')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="siblings_step" class="form-label">Jumlah Saudara Tiri</label>
                                            <input type="number" min="0" name="siblings_step" id="siblings_step"
                                                class="form-control @error('siblings_step') is-invalid @enderror"
                                                value="{{ old('siblings_step', $student->siblings_step) }}">
                                            @error('siblings_step')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="siblings_adopted" class="form-label">Jumlah Saudara Angkat</label>
                                            <input type="number" min="0" name="siblings_adopted"
                                                id="siblings_adopted"
                                                class="form-control @error('siblings_adopted') is-invalid @enderror"
                                                value="{{ old('siblings_adopted', $student->siblings_adopted) }}">
                                            @error('siblings_adopted')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="daily_language" class="form-label">Bahasa Sehari-hari di
                                                Rumah</label>
                                            <input type="text" name="daily_language" id="daily_language"
                                                class="form-control @error('daily_language') is-invalid @enderror"
                                                value="{{ old('daily_language', $student->daily_language) }}">
                                            @error('daily_language')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="blood_type" class="form-label">Golongan Darah</label>
                                            <input type="text" name="blood_type" id="blood_type"
                                                class="form-control @error('blood_type') is-invalid @enderror"
                                                value="{{ old('blood_type', $student->blood_type) }}">
                                            @error('blood_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>


                {{-- B. KETERANGAN ORANG TUA/WALI PESERTA DIDIK --}}
                <div class="biduk-accordion-item">
                    <button type="button" class="biduk-accordion-header collapsed" data-bs-toggle="collapse"
                        data-bs-target="#sectionB" aria-expanded="false" aria-controls="sectionB">
                        <span class="section-title">
                            <span class="section-icon"><i class="bi bi-people-fill"></i></span>
                            <span>B. Keterangan Orang Tua/Wali Peserta Didik</span>
                        </span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </button>

                    <div id="sectionB" class="collapse" data-bs-parent="#dataSiswaAccordion">
                        <div class="biduk-accordion-body">
                            {{-- Data Orang Tua / Wali --}}
                            <div class="biduk-card mb-4">
                                <div class="card-header">
                                    <i class="bi bi-people-fill text-success me-2"></i>Data Orang Tua / Wali
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <h6 class="text-muted fw-semibold"
                                                style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                                <i class="bi bi-person me-1"></i> Data Ayah
                                            </h6>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="father_name" class="form-label">Nama Ayah</label>
                                            <input type="text" name="father_name" id="father_name"
                                                class="form-control @error('father_name') is-invalid @enderror"
                                                value="{{ old('father_name', $student->father_name) }}">
                                            @error('father_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="father_nik" class="form-label">NIK Ayah</label>
                                            <input type="text" name="father_nik" id="father_nik"
                                                class="form-control @error('father_nik') is-invalid @enderror"
                                                value="{{ old('father_nik', $student->father_nik) }}">
                                            @error('father_nik')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="father_education" class="form-label">Pendidikan Terakhir
                                                Ayah</label>
                                            <input type="text" name="father_education" id="father_education"
                                                class="form-control @error('father_education') is-invalid @enderror"
                                                value="{{ old('father_education', $student->father_education) }}">
                                            @error('father_education')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="father_job" class="form-label">Pekerjaan Ayah</label>
                                            <input type="text" name="father_job" id="father_job"
                                                class="form-control @error('father_job') is-invalid @enderror"
                                                value="{{ old('father_job', $student->father_job) }}">
                                            @error('father_job')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12 mt-3">
                                            <h6 class="text-muted fw-semibold"
                                                style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                                <i class="bi bi-person me-1"></i> Data Ibu
                                            </h6>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="mother_name" class="form-label">Nama Ibu</label>
                                            <input type="text" name="mother_name" id="mother_name"
                                                class="form-control @error('mother_name') is-invalid @enderror"
                                                value="{{ old('mother_name', $student->mother_name) }}">
                                            @error('mother_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="mother_nik" class="form-label">NIK Ibu</label>
                                            <input type="text" name="mother_nik" id="mother_nik"
                                                class="form-control @error('mother_nik') is-invalid @enderror"
                                                value="{{ old('mother_nik', $student->mother_nik) }}">
                                            @error('mother_nik')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="mother_education" class="form-label">Pendidikan Terakhir
                                                Ibu</label>
                                            <input type="text" name="mother_education" id="mother_education"
                                                class="form-control @error('mother_education') is-invalid @enderror"
                                                value="{{ old('mother_education', $student->mother_education) }}">
                                            @error('mother_education')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="mother_job" class="form-label">Pekerjaan Ibu</label>
                                            <input type="text" name="mother_job" id="mother_job"
                                                class="form-control @error('mother_job') is-invalid @enderror"
                                                value="{{ old('mother_job', $student->mother_job) }}">
                                            @error('mother_job')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-12 mt-3">
                                            <h6 class="text-muted fw-semibold"
                                                style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em;">
                                                <i class="bi bi-person-check me-1"></i> Data Wali
                                            </h6>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="guardian_name" class="form-label">Nama Wali</label>
                                            <input type="text" name="guardian_name" id="guardian_name"
                                                class="form-control @error('guardian_name') is-invalid @enderror"
                                                value="{{ old('guardian_name', $student->guardian_name) }}">
                                            @error('guardian_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="guardian_relation" class="form-label">Hubungan Keluarga</label>
                                            <input type="text" name="guardian_relation" id="guardian_relation"
                                                class="form-control @error('guardian_relation') is-invalid @enderror"
                                                value="{{ old('guardian_relation', $student->guardian_relation) }}">
                                            @error('guardian_relation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="guardian_education" class="form-label">Pendidikan Terakhir
                                                Wali</label>
                                            <input type="text" name="guardian_education" id="guardian_education"
                                                class="form-control @error('guardian_education') is-invalid @enderror"
                                                value="{{ old('guardian_education', $student->guardian_education) }}">
                                            @error('guardian_education')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-3">
                                            <label for="guardian_job" class="form-label">Pekerjaan Wali</label>
                                            <input type="text" name="guardian_job" id="guardian_job"
                                                class="form-control @error('guardian_job') is-invalid @enderror"
                                                value="{{ old('guardian_job', $student->guardian_job) }}">
                                            @error('guardian_job')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="guardian_phone" class="form-label">No. Telepon Wali</label>
                                            <input type="text" name="guardian_phone" id="guardian_phone"
                                                class="form-control @error('guardian_phone') is-invalid @enderror"
                                                value="{{ old('guardian_phone', $student->guardian_phone) }}">
                                            @error('guardian_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>


                {{-- C. PERKEMBANGAN PESERTA DIDIK --}}
                <div class="biduk-accordion-item">
                    <button type="button" class="biduk-accordion-header collapsed" data-bs-toggle="collapse"
                        data-bs-target="#sectionC" aria-expanded="false" aria-controls="sectionC">
                        <span class="section-title">
                            <span class="section-icon"><i class="bi bi-mortarboard-fill"></i></span>
                            <span>C. Perkembangan Peserta Didik</span>
                        </span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </button>

                    <div id="sectionC" class="collapse" data-bs-parent="#dataSiswaAccordion">
                        <div class="biduk-accordion-body">
                            {{-- Perkembangan Peserta Didik --}}
                            <div class="biduk-card mb-4">
                                <div class="card-header">
                                    <i class="bi bi-mortarboard text-success me-2"></i>Perkembangan Peserta Didik
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="previous_school_name" class="form-label">Asal Sekolah</label>
                                            <input type="text" name="previous_school_name" id="previous_school_name"
                                                class="form-control @error('previous_school_name') is-invalid @enderror"
                                                value="{{ old('previous_school_name', $student->previousEducation?->previous_school_name) }}">
                                            @error('previous_school_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="certificate_date_number" class="form-label">Tanggal dan Nomor
                                                Ijazah/STTB</label>
                                            <input type="text" name="certificate_date_number"
                                                id="certificate_date_number"
                                                class="form-control @error('certificate_date_number') is-invalid @enderror"
                                                value="{{ old('certificate_date_number', $student->previousEducation?->certificate_date_number) }}">
                                            @error('certificate_date_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="transfer_from_school" class="form-label">Nama Sekolah Asal</label>
                                            <input type="text" name="transfer_from_school" id="transfer_from_school"
                                                class="form-control @error('transfer_from_school') is-invalid @enderror"
                                                value="{{ old('transfer_from_school', $student->previousEducation?->transfer_from_school) }}">
                                            @error('transfer_from_school')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="transfer_from_grade" class="form-label">Dari Tingkat</label>
                                            <input type="text" name="transfer_from_grade" id="transfer_from_grade"
                                                class="form-control @error('transfer_from_grade') is-invalid @enderror"
                                                value="{{ old('transfer_from_grade', $student->previousEducation?->transfer_from_grade) }}">
                                            @error('transfer_from_grade')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="transfer_accepted_date" class="form-label">Diterima
                                                Tanggal</label>
                                            <input type="date" name="transfer_accepted_date"
                                                id="transfer_accepted_date"
                                                class="form-control @error('transfer_accepted_date') is-invalid @enderror"
                                                value="{{ old('transfer_accepted_date', $student->previousEducation?->transfer_accepted_date?->format('Y-m-d')) }}">
                                            @error('transfer_accepted_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-4">
                                            <label for="transfer_letter_number" class="form-label">Nomor Surat
                                                Keterangan</label>
                                            <input type="text" name="transfer_letter_number"
                                                id="transfer_letter_number"
                                                class="form-control @error('transfer_letter_number') is-invalid @enderror"
                                                value="{{ old('transfer_letter_number', $student->previousEducation?->transfer_letter_number) }}">
                                            @error('transfer_letter_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                </div>


                {{-- D. MENINGGALKAN SEKOLAH --}}
                <div class="biduk-accordion-item">
                    <button type="button" class="biduk-accordion-header collapsed" data-bs-toggle="collapse"
                        data-bs-target="#sectionD" aria-expanded="false" aria-controls="sectionD">
                        <span class="section-title">
                            <span class="section-icon"><i class="bi bi-box-arrow-right"></i></span>
                            <span>D. Meninggalkan Sekolah</span>
                        </span>
                        <i class="bi bi-chevron-down arrow"></i>
                    </button>

                    <div id="sectionD" class="collapse" data-bs-parent="#dataSiswaAccordion">
                        <div class="biduk-accordion-body">
                            <div class="biduk-card">
                                <div class="card-body">
                                    <div class="row g-3">

                                        <div class="col-12">
                                            <h6 class="fw-semibold mb-3">1. Tamat Belajar/Lulus</h6>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="graduation_year" class="form-label">a. Tahun</label>
                                            <input type="text" name="graduation_year" id="graduation_year"
                                                class="form-control"
                                                value="{{ old('graduation_year', $student->graduation_year ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="graduation_certificate_number" class="form-label">b. Nomor
                                                Ijazah/STTB</label>
                                            <input type="text" name="graduation_certificate_number"
                                                id="graduation_certificate_number" class="form-control"
                                                value="{{ old('graduation_certificate_number', $student->graduation_certificate_number ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="continued_school" class="form-label">c. Melanjutkan ke
                                                Sekolah</label>
                                            <input type="text" name="continued_school" id="continued_school"
                                                class="form-control"
                                                value="{{ old('continued_school', $student->continued_school ?? '') }}">
                                        </div>

                                        <div class="col-12 mt-3">
                                            <h6 class="fw-semibold mb-3">2. Pindah Sekolah</h6>
                                        </div>

                                        <div class="col-md-4">
                                            <label for="transfer_left_grade" class="form-label">a. Tingkat/Kelas yang
                                                ditinggalkan</label>
                                            <input type="text" name="transfer_left_grade" id="transfer_left_grade"
                                                class="form-control"
                                                value="{{ old('transfer_left_grade', $student->transfer_left_grade ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="transfer_to_school" class="form-label">b. Ke Sekolah</label>
                                            <input type="text" name="transfer_to_school" id="transfer_to_school"
                                                class="form-control"
                                                value="{{ old('transfer_to_school', $student->transfer_to_school ?? '') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label for="transfer_to_grade" class="form-label">c. Ke Tingkat</label>
                                            <input type="text" name="transfer_to_grade" id="transfer_to_grade"
                                                class="form-control"
                                                value="{{ old('transfer_to_grade', $student->transfer_to_grade ?? '') }}">
                                        </div>

                                        <div class="col-12 mt-3">
                                            <h6 class="fw-semibold mb-3">3. Keluar Sekolah</h6>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="leaving_reason" class="form-label">a. Alasan Keluar
                                                Sekolah</label>
                                            <textarea name="leaving_reason" id="leaving_reason" rows="2" class="form-control">{{ old('leaving_reason', $student->leaving_reason ?? '') }}</textarea>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="leaving_date" class="form-label">b. Hari dan Tanggal Keluar
                                                Sekolah</label>
                                            <input type="date" name="leaving_date" id="leaving_date"
                                                class="form-control"
                                                value="{{ old('leaving_date', isset($student->leaving_date) && $student->leaving_date ? \Illuminate\Support\Carbon::parse($student->leaving_date)->format('Y-m-d') : '') }}">
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- =========================================================
     E. LAIN-LAIN
========================================================= --}}
                <div class="biduk-accordion-item">

                    <button type="button" class="biduk-accordion-header collapsed" data-bs-toggle="collapse"
                        data-bs-target="#sectionE" aria-expanded="false" aria-controls="sectionE">

                        <span class="section-title">
                            <span class="section-icon">
                                <i class="bi bi-clipboard2-pulse-fill"></i>
                            </span>

                            <span>E. Lain-Lain</span>
                        </span>

                        <i class="bi bi-chevron-down arrow"></i>
                    </button>


                    <div id="sectionE" class="collapse" data-bs-parent="#dataSiswaAccordion">

                        <div class="biduk-accordion-body">

                            @php
                                /*
                |--------------------------------------------------------------------------
                | DATA E. LAIN-LAIN
                |--------------------------------------------------------------------------
                */

                                $existingPhysiquesByYear = $student->physiques->groupBy('academic_year_id');

                                $existingHealthsByYear = $student->healths->groupBy('academic_year_id');

                                /*
                | Buat data semester untuk JavaScript.
                | Setiap Tahun Pelajaran mempunyai Semester Ganjil dan Genap.
                */
                                $semesterData = $semesters
                                    ->map(function ($semester) {
                                        return [
                                            'id' => $semester->id,
                                            'academic_year_id' => $semester->academic_year_id,
                                            'name' => $semester->name,
                                        ];
                                    })
                                    ->values();
                            @endphp


                            {{-- =====================================================
                 E.1 TINGGI DAN BERAT BADAN PESERTA DIDIK
            ====================================================== --}}

                            <div class="biduk-card mb-4">

                                <div class="card-header d-flex justify-content-between align-items-center">

                                    <span>
                                        <i class="bi bi-rulers text-success me-2"></i>
                                        1. Tinggi dan Berat Badan Peserta Didik
                                    </span>

                                </div>


                                <div class="card-body">

                                    {{-- TEMPAT SEMUA TABEL TAHUN PELAJARAN --}}
                                    <div id="physique-years-container">

                                        @foreach ($academicYears as $academicYear)
                                            @php

                                                $yearPhysiques = $existingPhysiquesByYear->get(
                                                    $academicYear->id,
                                                    collect(),
                                                );

                                                $yearSemesters = $semesters->where(
                                                    'academic_year_id',
                                                    $academicYear->id,
                                                );

                                                $ganjilSemester = $yearSemesters->first(
                                                    fn($s) => strtolower(trim($s->name)) === 'ganjil',
                                                );

                                                $genapSemester = $yearSemesters->first(
                                                    fn($s) => strtolower(trim($s->name)) === 'genap',
                                                );

                                                $ganjilData = $ganjilSemester
                                                    ? $yearPhysiques->firstWhere('semester_id', $ganjilSemester->id)
                                                    : null;

                                                $genapData = $genapSemester
                                                    ? $yearPhysiques->firstWhere('semester_id', $genapSemester->id)
                                                    : null;

                                            @endphp


                                            @if ($yearPhysiques->count() > 0)
                                                <div class="year-data-card mb-4 physique-year-card"
                                                    data-year-id="{{ $academicYear->id }}">

                                                    {{-- HEADER TAHUN PELAJARAN --}}
                                                    <div
                                                        class="year-data-header d-flex justify-content-between align-items-center">

                                                        <strong>
                                                            Tahun Pelajaran {{ $academicYear->name }}
                                                        </strong>

                                                        <span class="badge bg-light text-secondary border">
                                                            Semester Ganjil & Genap
                                                        </span>

                                                    </div>


                                                    <div class="table-responsive">

                                                        <table
                                                            class="table table-bordered align-middle mb-0 biduk-period-table">

                                                            <thead>

                                                                <tr>

                                                                    <th rowspan="2" style="min-width: 180px;">
                                                                        Aspek yang dinilai
                                                                    </th>

                                                                    <th colspan="2" class="text-center">
                                                                        Tahun Pelajaran
                                                                        <br>
                                                                        {{ $academicYear->name }}
                                                                    </th>

                                                                </tr>

                                                                <tr>

                                                                    <th class="text-center" style="min-width: 180px;">
                                                                        Semester Ganjil
                                                                    </th>

                                                                    <th class="text-center" style="min-width: 180px;">
                                                                        Semester Genap
                                                                    </th>

                                                                </tr>

                                                            </thead>


                                                            <tbody>

                                                                {{-- TINGGI BADAN --}}
                                                                <tr>

                                                                    <td>
                                                                        <strong>1.</strong>
                                                                        Tinggi Badan
                                                                    </td>


                                                                    <td>

                                                                        @if ($ganjilSemester)
                                                                            @php
                                                                                $keyGanjil =
                                                                                    $academicYear->id .
                                                                                    '_' .
                                                                                    $ganjilSemester->id;
                                                                            @endphp

                                                                            <input type="hidden"
                                                                                name="physiques[{{ $keyGanjil }}][academic_year_id]"
                                                                                value="{{ $academicYear->id }}">

                                                                            <input type="hidden"
                                                                                name="physiques[{{ $keyGanjil }}][semester_id]"
                                                                                value="{{ $ganjilSemester->id }}">

                                                                            <div class="input-group">

                                                                                <input type="number" step="0.1"
                                                                                    min="0"
                                                                                    name="physiques[{{ $keyGanjil }}][height]"
                                                                                    class="form-control"
                                                                                    value="{{ old('physiques.' . $keyGanjil . '.height', $ganjilData?->height) }}"
                                                                                    placeholder="Tinggi">

                                                                                <span class="input-group-text">
                                                                                    Cm
                                                                                </span>

                                                                            </div>
                                                                        @endif

                                                                    </td>


                                                                    <td>

                                                                        @if ($genapSemester)
                                                                            @php
                                                                                $keyGenap =
                                                                                    $academicYear->id .
                                                                                    '_' .
                                                                                    $genapSemester->id;
                                                                            @endphp

                                                                            <input type="hidden"
                                                                                name="physiques[{{ $keyGenap }}][academic_year_id]"
                                                                                value="{{ $academicYear->id }}">

                                                                            <input type="hidden"
                                                                                name="physiques[{{ $keyGenap }}][semester_id]"
                                                                                value="{{ $genapSemester->id }}">

                                                                            <div class="input-group">

                                                                                <input type="number" step="0.1"
                                                                                    min="0"
                                                                                    name="physiques[{{ $keyGenap }}][height]"
                                                                                    class="form-control"
                                                                                    value="{{ old('physiques.' . $keyGenap . '.height', $genapData?->height) }}"
                                                                                    placeholder="Tinggi">

                                                                                <span class="input-group-text">
                                                                                    Cm
                                                                                </span>

                                                                            </div>
                                                                        @endif

                                                                    </td>

                                                                </tr>


                                                                {{-- BERAT BADAN --}}
                                                                <tr>

                                                                    <td>
                                                                        <strong>2.</strong>
                                                                        Berat Badan
                                                                    </td>


                                                                    <td>

                                                                        @if ($ganjilSemester)
                                                                            @php
                                                                                $keyGanjil =
                                                                                    $academicYear->id .
                                                                                    '_' .
                                                                                    $ganjilSemester->id;
                                                                            @endphp

                                                                            <div class="input-group">

                                                                                <input type="number" step="0.1"
                                                                                    min="0"
                                                                                    name="physiques[{{ $keyGanjil }}][weight]"
                                                                                    class="form-control"
                                                                                    value="{{ old('physiques.' . $keyGanjil . '.weight', $ganjilData?->weight) }}"
                                                                                    placeholder="Berat">

                                                                                <span class="input-group-text">
                                                                                    Kg
                                                                                </span>

                                                                            </div>
                                                                        @endif

                                                                    </td>


                                                                    <td>

                                                                        @if ($genapSemester)
                                                                            @php
                                                                                $keyGenap =
                                                                                    $academicYear->id .
                                                                                    '_' .
                                                                                    $genapSemester->id;
                                                                            @endphp

                                                                            <div class="input-group">

                                                                                <input type="number" step="0.1"
                                                                                    min="0"
                                                                                    name="physiques[{{ $keyGenap }}][weight]"
                                                                                    class="form-control"
                                                                                    value="{{ old('physiques.' . $keyGenap . '.weight', $genapData?->weight) }}"
                                                                                    placeholder="Berat">

                                                                                <span class="input-group-text">
                                                                                    Kg
                                                                                </span>

                                                                            </div>
                                                                        @endif

                                                                    </td>

                                                                </tr>

                                                            </tbody>

                                                        </table>

                                                    </div>

                                                </div>
                                            @endif
                                        @endforeach

                                    </div>


                                    {{-- TAMBAH TAHUN PELAJARAN --}}
                                    <div class="add-year-panel mt-3">

                                        <div class="row g-2 align-items-end">

                                            <div class="col-md-8">

                                                <label for="new_e_year" class="form-label mb-1">
                                                    Tambah Tahun Pelajaran
                                                </label>

                                                <select id="new_e_year" class="form-select">

                                                    <option value="">
                                                        Pilih Tahun Pelajaran...
                                                    </option>

                                                    @foreach ($academicYears as $academicYear)
                                                        <option value="{{ $academicYear->id }}">
                                                            {{ $academicYear->name }}
                                                        </option>
                                                    @endforeach

                                                </select>

                                            </div>


                                            <div class="col-md-4">

                                                <button type="button" class="btn btn-biduk-primary w-100"
                                                    id="addEYearBtn">

                                                    <i class="bi bi-plus-lg me-1"></i>
                                                    Tambah Tabel

                                                </button>

                                            </div>

                                        </div>


                                        <small class="text-muted d-block mt-2">

                                            Pilih tahun pelajaran untuk menambahkan
                                            tabel Semester Ganjil dan Genap.

                                        </small>

                                    </div>

                                </div>

                            </div>



                            {{-- =====================================================
                 E.2 KONDISI KESEHATAN PESERTA DIDIK
            ====================================================== --}}

                            <div class="biduk-card mb-4">

                                <div class="card-header">

                                    <i class="bi bi-heart-pulse text-success me-2"></i>

                                    2. Kondisi Kesehatan Peserta Didik

                                </div>


                                <div class="card-body">

                                    <div id="health-years-container">

                                        @foreach ($academicYears as $academicYear)
                                            @php

                                                $yearHealths = $existingHealthsByYear->get(
                                                    $academicYear->id,
                                                    collect(),
                                                );

                                                $healthRepresentative = $yearHealths->first();

                                            @endphp


                                            @if ($yearHealths->count() > 0)
                                                <div class="year-data-card mb-4 health-year-card"
                                                    data-year-id="{{ $academicYear->id }}">

                                                    <div
                                                        class="year-data-header d-flex justify-content-between align-items-center">

                                                        <strong>
                                                            Tahun Pelajaran {{ $academicYear->name }}
                                                        </strong>

                                                    </div>


                                                    <div class="table-responsive">

                                                        <table
                                                            class="table table-bordered align-middle mb-0 biduk-health-table">

                                                            <thead>

                                                                <tr>

                                                                    <th rowspan="2" style="min-width: 220px;">
                                                                        Aspek Kesehatan
                                                                    </th>

                                                                    <th class="text-center">
                                                                        Tahun Pelajaran
                                                                        <br>
                                                                        {{ $academicYear->name }}
                                                                    </th>

                                                                </tr>

                                                                <tr>

                                                                    <th class="text-center">
                                                                        Keterangan
                                                                    </th>

                                                                </tr>

                                                            </thead>


                                                            <tbody>

                                                                {{-- PENDENGARAN --}}
                                                                <tr>

                                                                    <td>
                                                                        <strong>1.</strong>
                                                                        Pendengaran
                                                                    </td>

                                                                    <td>

                                                                        <input type="text"
                                                                            class="form-control health-display"
                                                                            data-health-year="{{ $academicYear->id }}"
                                                                            data-health-field="hearing"
                                                                            value="{{ old('health_display.' . $academicYear->id . '.hearing', $healthRepresentative?->hearing) }}"
                                                                            placeholder="Keterangan">

                                                                    </td>

                                                                </tr>


                                                                {{-- PENGLIHATAN --}}
                                                                <tr>

                                                                    <td>
                                                                        <strong>2.</strong>
                                                                        Penglihatan
                                                                    </td>

                                                                    <td>

                                                                        <input type="text"
                                                                            class="form-control health-display"
                                                                            data-health-year="{{ $academicYear->id }}"
                                                                            data-health-field="vision"
                                                                            value="{{ old('health_display.' . $academicYear->id . '.vision', $healthRepresentative?->vision) }}"
                                                                            placeholder="Keterangan">

                                                                    </td>

                                                                </tr>


                                                                {{-- GIGI --}}
                                                                <tr>

                                                                    <td>
                                                                        <strong>3.</strong>
                                                                        Gigi
                                                                    </td>

                                                                    <td>

                                                                        <input type="text"
                                                                            class="form-control health-display"
                                                                            data-health-year="{{ $academicYear->id }}"
                                                                            data-health-field="teeth"
                                                                            value="{{ old('health_display.' . $academicYear->id . '.teeth', $healthRepresentative?->teeth) }}"
                                                                            placeholder="Keterangan">

                                                                    </td>

                                                                </tr>


                                                                {{-- LAIN-LAIN --}}
                                                                <tr>

                                                                    <td>
                                                                        <strong>4.</strong>
                                                                        Lain-lain
                                                                    </td>

                                                                    <td>

                                                                        <textarea class="form-control health-display" rows="2" data-health-year="{{ $academicYear->id }}"
                                                                            data-health-field="notes" placeholder="Keterangan lainnya">{{ old('health_display.' . $academicYear->id . '.notes', $healthRepresentative?->notes) }}</textarea>

                                                                    </td>

                                                                </tr>

                                                            </tbody>

                                                        </table>

                                                    </div>


                                                    {{-- HIDDEN HEALTH RECORD --}}
                                                    @foreach ($semesters->where('academic_year_id', $academicYear->id) as $semester)
                                                        @php
                                                            $healthKey = $academicYear->id . '_' . $semester->id;
                                                        @endphp

                                                        <div class="health-hidden-record"
                                                            data-health-year="{{ $academicYear->id }}">

                                                            <input type="hidden"
                                                                name="healths[{{ $healthKey }}][academic_year_id]"
                                                                value="{{ $academicYear->id }}">

                                                            <input type="hidden"
                                                                name="healths[{{ $healthKey }}][semester_id]"
                                                                value="{{ $semester->id }}">

                                                            <input type="hidden"
                                                                name="healths[{{ $healthKey }}][hearing]"
                                                                value="{{ $healthRepresentative?->hearing ?? '' }}">

                                                            <input type="hidden"
                                                                name="healths[{{ $healthKey }}][vision]"
                                                                value="{{ $healthRepresentative?->vision ?? '' }}">

                                                            <input type="hidden"
                                                                name="healths[{{ $healthKey }}][teeth]"
                                                                value="{{ $healthRepresentative?->teeth ?? '' }}">

                                                            <input type="hidden"
                                                                name="healths[{{ $healthKey }}][notes]"
                                                                value="{{ $healthRepresentative?->notes ?? '' }}">

                                                        </div>
                                                    @endforeach

                                                </div>
                                            @endif
                                        @endforeach

                                    </div>


                                    <div class="text-muted small mt-2">

                                        Keterangan kesehatan dicatat satu kali
                                        untuk setiap Tahun Pelajaran sesuai Buku Induk.

                                    </div>

                                </div>

                            </div>



                            {{-- =====================================================
                 JAVASCRIPT TAMBAH TABEL TAHUN PELAJARAN
            ====================================================== --}}

                            <script>
                                document.addEventListener('DOMContentLoaded', function() {

                                    const addButton =
                                        document.getElementById('addEYearBtn');

                                    const yearSelect =
                                        document.getElementById('new_e_year');

                                    const physiqueContainer =
                                        document.getElementById('physique-years-container');

                                    const healthContainer =
                                        document.getElementById('health-years-container');


                                    /*
                                    |--------------------------------------------------------------------------
                                    | DATA TAHUN DAN SEMESTER DARI LARAVEL
                                    |--------------------------------------------------------------------------
                                    */

                                    const semesterData =
                                        @json($semesterData);


                                    /*
                                    |--------------------------------------------------------------------------
                                    | CARI SEMESTER BERDASARKAN TAHUN
                                    |--------------------------------------------------------------------------
                                    */

                                    function getSemester(yearId, semesterName) {

                                        return semesterData.find(function(semester) {

                                            return String(semester.academic_year_id) === String(yearId) &&
                                                semester.name.toLowerCase().trim() === semesterName.toLowerCase();

                                        });

                                    }


                                    /*
                                    |--------------------------------------------------------------------------
                                    | CEK APAKAH TAHUN SUDAH ADA
                                    |--------------------------------------------------------------------------
                                    */

                                    function yearAlreadyExists(yearId) {

                                        const physiqueExists =
                                            document.querySelector(
                                                '.physique-year-card[data-year-id="' + yearId + '"]'
                                            );

                                        return !!physiqueExists;

                                    }


                                    /*
                                    |--------------------------------------------------------------------------
                                    | BUAT TABEL TINGGI DAN BERAT
                                    |--------------------------------------------------------------------------
                                    */

                                    function createPhysiqueTable(yearId, yearName) {

                                        const ganjil =
                                            getSemester(yearId, 'Ganjil');

                                        const genap =
                                            getSemester(yearId, 'Genap');


                                        if (!ganjil || !genap) {

                                            alert(
                                                'Semester Ganjil dan Genap untuk tahun pelajaran ini belum tersedia.'
                                            );

                                            return null;

                                        }


                                        const keyGanjil =
                                            yearId + '_' + ganjil.id;

                                        const keyGenap =
                                            yearId + '_' + genap.id;


                                        const card =
                                            document.createElement('div');

                                        card.className =
                                            'year-data-card mb-4 physique-year-card';

                                        card.dataset.yearId =
                                            yearId;


                                        card.innerHTML = `

                            <div class="year-data-header d-flex justify-content-between align-items-center">

                                <strong>
                                    Tahun Pelajaran ${yearName}
                                </strong>

                                <button type="button"
                                    class="btn btn-sm btn-outline-danger remove-e-year"
                                    data-year-id="${yearId}">

                                    <i class="bi bi-trash3 me-1"></i>
                                    Hapus

                                </button>

                            </div>


                            <div class="table-responsive">

                                <table class="table table-bordered align-middle mb-0 biduk-period-table">

                                    <thead>

                                        <tr>

                                            <th rowspan="2"
                                                style="min-width:180px;">
                                                Aspek yang dinilai
                                            </th>

                                            <th colspan="2"
                                                class="text-center">
                                                Tahun Pelajaran
                                                <br>
                                                ${yearName}
                                            </th>

                                        </tr>

                                        <tr>

                                            <th class="text-center">
                                                Semester Ganjil
                                            </th>

                                            <th class="text-center">
                                                Semester Genap
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <tr>

                                            <td>
                                                <strong>1.</strong>
                                                Tinggi Badan
                                            </td>


                                            <td>

                                                <input type="hidden"
                                                    name="physiques[${keyGanjil}][academic_year_id]"
                                                    value="${yearId}">

                                                <input type="hidden"
                                                    name="physiques[${keyGanjil}][semester_id]"
                                                    value="${ganjil.id}">


                                                <div class="input-group">

                                                    <input type="number"
                                                        step="0.1"
                                                        min="0"
                                                        name="physiques[${keyGanjil}][height]"
                                                        class="form-control"
                                                        placeholder="Tinggi">

                                                    <span class="input-group-text">
                                                        Cm
                                                    </span>

                                                </div>

                                            </td>


                                            <td>

                                                <input type="hidden"
                                                    name="physiques[${keyGenap}][academic_year_id]"
                                                    value="${yearId}">

                                                <input type="hidden"
                                                    name="physiques[${keyGenap}][semester_id]"
                                                    value="${genap.id}">


                                                <div class="input-group">

                                                    <input type="number"
                                                        step="0.1"
                                                        min="0"
                                                        name="physiques[${keyGenap}][height]"
                                                        class="form-control"
                                                        placeholder="Tinggi">

                                                    <span class="input-group-text">
                                                        Cm
                                                    </span>

                                                </div>

                                            </td>

                                        </tr>


                                        <tr>

                                            <td>
                                                <strong>2.</strong>
                                                Berat Badan
                                            </td>


                                            <td>

                                                <div class="input-group">

                                                    <input type="number"
                                                        step="0.1"
                                                        min="0"
                                                        name="physiques[${keyGanjil}][weight]"
                                                        class="form-control"
                                                        placeholder="Berat">

                                                    <span class="input-group-text">
                                                        Kg
                                                    </span>

                                                </div>

                                            </td>


                                            <td>

                                                <div class="input-group">

                                                    <input type="number"
                                                        step="0.1"
                                                        min="0"
                                                        name="physiques[${keyGenap}][weight]"
                                                        class="form-control"
                                                        placeholder="Berat">

                                                    <span class="input-group-text">
                                                        Kg
                                                    </span>

                                                </div>

                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>

                        `;


                                        return card;

                                    }


                                    /*
                                    |--------------------------------------------------------------------------
                                    | BUAT TABEL KONDISI KESEHATAN
                                    |--------------------------------------------------------------------------
                                    */

                                    function createHealthTable(yearId, yearName) {

                                        const ganjil =
                                            getSemester(yearId, 'Ganjil');

                                        const genap =
                                            getSemester(yearId, 'Genap');


                                        if (!ganjil || !genap) {

                                            return null;

                                        }


                                        const keyGanjil =
                                            yearId + '_' + ganjil.id;

                                        const keyGenap =
                                            yearId + '_' + genap.id;


                                        const card =
                                            document.createElement('div');

                                        card.className =
                                            'year-data-card mb-4 health-year-card';

                                        card.dataset.yearId =
                                            yearId;


                                        card.innerHTML = `

                            <div class="year-data-header d-flex justify-content-between align-items-center">

                                <strong>
                                    Tahun Pelajaran ${yearName}
                                </strong>

                                <button type="button"
                                    class="btn btn-sm btn-outline-danger remove-e-year"
                                    data-year-id="${yearId}">

                                    <i class="bi bi-trash3 me-1"></i>
                                    Hapus

                                </button>

                            </div>


                            <div class="table-responsive">

                                <table class="table table-bordered align-middle mb-0 biduk-health-table">

                                    <thead>

                                        <tr>

                                            <th rowspan="2"
                                                style="min-width:220px;">
                                                Aspek Kesehatan
                                            </th>

                                            <th class="text-center">
                                                Tahun Pelajaran
                                                <br>
                                                ${yearName}
                                            </th>

                                        </tr>

                                        <tr>

                                            <th class="text-center">
                                                Keterangan
                                            </th>

                                        </tr>

                                    </thead>


                                    <tbody>

                                        <tr>

                                            <td>
                                                <strong>1.</strong>
                                                Pendengaran
                                            </td>

                                            <td>

                                                <input type="text"
                                                    class="form-control health-display"
                                                    data-health-year="${yearId}"
                                                    data-health-field="hearing"
                                                    placeholder="Keterangan">

                                            </td>

                                        </tr>


                                        <tr>

                                            <td>
                                                <strong>2.</strong>
                                                Penglihatan
                                            </td>

                                            <td>

                                                <input type="text"
                                                    class="form-control health-display"
                                                    data-health-year="${yearId}"
                                                    data-health-field="vision"
                                                    placeholder="Keterangan">

                                            </td>

                                        </tr>


                                        <tr>

                                            <td>
                                                <strong>3.</strong>
                                                Gigi
                                            </td>

                                            <td>

                                                <input type="text"
                                                    class="form-control health-display"
                                                    data-health-year="${yearId}"
                                                    data-health-field="teeth"
                                                    placeholder="Keterangan">

                                            </td>

                                        </tr>


                                        <tr>

                                            <td>
                                                <strong>4.</strong>
                                                Lain-lain
                                            </td>

                                            <td>

                                                <textarea
                                                    class="form-control health-display"
                                                    rows="2"
                                                    data-health-year="${yearId}"
                                                    data-health-field="notes"
                                                    placeholder="Keterangan lainnya"></textarea>

                                            </td>

                                        </tr>

                                    </tbody>

                                </table>

                            </div>


                            <div class="health-hidden-record"
                                data-health-year="${yearId}">

                                <input type="hidden"
                                    name="healths[${keyGanjil}][academic_year_id]"
                                    value="${yearId}">

                                <input type="hidden"
                                    name="healths[${keyGanjil}][semester_id]"
                                    value="${ganjil.id}">

                                <input type="hidden"
                                    name="healths[${keyGanjil}][hearing]"
                                    data-health-hidden="${yearId}-hearing">

                                <input type="hidden"
                                    name="healths[${keyGanjil}][vision]"
                                    data-health-hidden="${yearId}-vision">

                                <input type="hidden"
                                    name="healths[${keyGanjil}][teeth]"
                                    data-health-hidden="${yearId}-teeth">

                                <input type="hidden"
                                    name="healths[${keyGanjil}][notes]"
                                    data-health-hidden="${yearId}-notes">


                                <input type="hidden"
                                    name="healths[${keyGenap}][academic_year_id]"
                                    value="${yearId}">

                                <input type="hidden"
                                    name="healths[${keyGenap}][semester_id]"
                                    value="${genap.id}">

                                <input type="hidden"
                                    name="healths[${keyGenap}][hearing]"
                                    data-health-hidden="${yearId}-hearing">

                                <input type="hidden"
                                    name="healths[${keyGenap}][vision]"
                                    data-health-hidden="${yearId}-vision">

                                <input type="hidden"
                                    name="healths[${keyGenap}][teeth]"
                                    data-health-hidden="${yearId}-teeth">

                                <input type="hidden"
                                    name="healths[${keyGenap}][notes]"
                                    data-health-hidden="${yearId}-notes">

                            </div>

                        `;


                                        return card;

                                    }


                                    /*
                                    |--------------------------------------------------------------------------
                                    | TAMBAH TAHUN PELAJARAN
                                    |--------------------------------------------------------------------------
                                    */

                                    addButton.addEventListener('click', function() {

                                        const yearId =
                                            yearSelect.value;

                                        const yearName =
                                            yearSelect.options[
                                                yearSelect.selectedIndex
                                            ]?.text;


                                        if (!yearId) {

                                            alert(
                                                'Silakan pilih Tahun Pelajaran terlebih dahulu.'
                                            );

                                            return;

                                        }


                                        /*
                                        | Jangan boleh tahun yang sama ditambahkan dua kali.
                                        */

                                        if (yearAlreadyExists(yearId)) {

                                            alert(
                                                'Tahun Pelajaran tersebut sudah ditambahkan.'
                                            );

                                            return;

                                        }


                                        const physiqueTable =
                                            createPhysiqueTable(
                                                yearId,
                                                yearName
                                            );

                                        const healthTable =
                                            createHealthTable(
                                                yearId,
                                                yearName
                                            );


                                        if (!physiqueTable || !healthTable) {

                                            alert(
                                                'Semester Ganjil dan Genap untuk tahun tersebut belum tersedia.'
                                            );

                                            return;

                                        }


                                        /*
                                        | Tambahkan tabel tinggi/berat.
                                        */

                                        physiqueContainer.appendChild(
                                            physiqueTable
                                        );


                                        /*
                                        | Tambahkan tabel kesehatan.
                                        */

                                        healthContainer.appendChild(
                                            healthTable
                                        );


                                        /*
                                        | Setelah ditambahkan, reset pilihan.
                                        */

                                        yearSelect.value = '';


                                        /*
                                        | Scroll ke tabel yang baru ditambahkan.
                                        */

                                        physiqueTable.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'center'
                                        });

                                    });


                                    /*
                                    |--------------------------------------------------------------------------
                                    | SINKRONISASI DATA KESEHATAN
                                    |--------------------------------------------------------------------------
                                    */

                                    document.addEventListener(
                                        'input',
                                        function(event) {

                                            const input =
                                                event.target.closest(
                                                    '.health-display'
                                                );


                                            if (!input) {

                                                return;

                                            }


                                            const yearId =
                                                input.dataset.healthYear;

                                            const field =
                                                input.dataset.healthField;

                                            const hiddenInputs =
                                                document.querySelectorAll(
                                                    '[data-health-hidden="' +
                                                    yearId +
                                                    '-' +
                                                    field +
                                                    '"]'
                                                );


                                            hiddenInputs.forEach(
                                                function(hidden) {

                                                    hidden.value =
                                                        input.value;

                                                }
                                            );

                                        }
                                    );


                                    /*
                                    |--------------------------------------------------------------------------
                                    | HAPUS TABEL TAHUN YANG BARU DITAMBAHKAN
                                    |--------------------------------------------------------------------------
                                    */

                                    document.addEventListener(
                                        'click',
                                        function(event) {

                                            const button =
                                                event.target.closest(
                                                    '.remove-e-year'
                                                );


                                            if (!button) {

                                                return;

                                            }


                                            const yearId =
                                                button.dataset.yearId;


                                            /*
                                            | Jangan hapus data lama yang sudah tersimpan.
                                            | Tombol Hapus hanya tersedia pada tabel
                                            | yang dibuat melalui tombol Tambah Tabel.
                                            */

                                            const physiqueCard =
                                                document.querySelector(
                                                    '.physique-year-card[data-year-id="' +
                                                    yearId +
                                                    '"]'
                                                );

                                            const healthCard =
                                                document.querySelector(
                                                    '.health-year-card[data-year-id="' +
                                                    yearId +
                                                    '"]'
                                                );


                                            /*
                                            | Kalau tabel tersebut adalah data lama,
                                            | jangan dihapus dari tampilan.
                                            */

                                            if (
                                                physiqueCard &&
                                                !physiqueCard.classList.contains(
                                                    'new-e-year'
                                                )
                                            ) {

                                                return;

                                            }


                                            if (physiqueCard) {

                                                physiqueCard.remove();

                                            }


                                            if (healthCard) {

                                                healthCard.remove();

                                            }

                                        }
                                    );

                                });
                            </script>

                        </div>

                    </div>

                </div>

                {{-- DATA TAMBAHAN APLIKASI --}}
                <div class="biduk-card mb-4">
                    <div class="card-header">
                        <i class="bi bi-database text-secondary me-2"></i>Data Administrasi Aplikasi
                    </div>
                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-4">
                                <label for="class_id" class="form-label">Kelas</label>
                                <select name="class_id" id="class_id"
                                    class="form-select @error('class_id') is-invalid @enderror">
                                    <option value="">Pilih Kelas...</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}"
                                            {{ old('class_id', $currentClassId) == $class->id ? 'selected' : '' }}>
                                            {{ $class->name }} ({{ $class->academicYear->name ?? '' }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('class_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="status" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <select name="status" id="status"
                                    class="form-select @error('status') is-invalid @enderror" required>
                                    @foreach (['Aktif', 'Pindah', 'Lulus', 'Alumni', 'Nonaktif'] as $s)
                                        <option value="{{ $s }}"
                                            {{ old('status', $student->status) == $s ? 'selected' : '' }}>
                                            {{ $s }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label for="photo" class="form-label">Foto Siswa</label>
                                <input type="file" name="photo" id="photo"
                                    class="form-control @error('photo') is-invalid @enderror"
                                    accept="image/jpeg,image/png,image/jpg">

                                @if ($student->photo)
                                    <div class="form-text text-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        Foto sudah ada. Upload ulang untuk mengganti.
                                    </div>
                                @else
                                    <div class="form-text">Maks 2MB, format JPG/PNG</div>
                                @endif

                                @error('photo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('students.index') }}" class="btn btn-outline-secondary px-4">
                        <i class="bi bi-x-lg me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-biduk-primary px-4">
                        <i class="bi bi-check-lg me-1"></i> Perbarui Data
                    </button>
                </div>
        </form>
    </div>
@endsection
