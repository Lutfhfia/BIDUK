@extends('layouts.app')

@section('title', 'Edit Siswa')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Data Siswa</a></li>
    <li class="breadcrumb-item active">Edit — {{ $student->name }}</li>
@endsection

@section('content')
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

            {{-- Identitas Dasar --}}
            <div class="biduk-card mb-4">
                <div class="card-header">
                    <i class="bi bi-person-fill text-success me-2"></i>Identitas Peserta Didik
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
                            <label for="nisn" class="form-label">NISN <span class="text-danger">*</span></label>
                            <input type="text" name="nisn" id="nisn"
                                class="form-control @error('nisn') is-invalid @enderror"
                                value="{{ old('nisn', $student->nisn) }}" required>
                            @error('nisn')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="nik" class="form-label">NIK</label>
                            <input type="text" name="nik" id="nik"
                                class="form-control @error('nik') is-invalid @enderror"
                                value="{{ old('nik', $student->nik) }}">
                            @error('nik')
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
                            <label for="nickname" class="form-label">Nama Panggilan</label>
                            <input type="text" name="nickname" id="nickname"
                                class="form-control @error('nickname') is-invalid @enderror"
                                value="{{ old('nickname', $student->nickname) }}">
                            @error('nickname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="gender" class="form-label">Jenis Kelamin <span
                                    class="text-danger">*</span></label>
                            <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror"
                                required>
                                <option value="">Pilih...</option>
                                <option value="L" {{ old('gender', $student->gender) == 'L' ? 'selected' : '' }}>
                                    Laki-laki</option>
                                <option value="P" {{ old('gender', $student->gender) == 'P' ? 'selected' : '' }}>
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
                            <label for="city_code" class="form-label">Nomor Kode Kabupaten/Kota</label>
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
                        <div class="col-md-4">
                            <label for="citizenship" class="form-label">Kewarganegaraan</label>
                            <input type="text" name="citizenship" id="citizenship"
                                class="form-control @error('citizenship') is-invalid @enderror"
                                value="{{ old('citizenship', $student->citizenship ?? 'WNI') }}">
                            @error('citizenship')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="kk_number" class="form-label">No. Kartu Keluarga</label>
                            <input type="text" name="kk_number" id="kk_number"
                                class="form-control @error('kk_number') is-invalid @enderror"
                                value="{{ old('kk_number', $student->kk_number) }}">
                            @error('kk_number')
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
                            <input type="number" min="1" name="child_position" id="child_position"
                                class="form-control @error('child_position') is-invalid @enderror"
                                value="{{ old('child_position', $student->child_position) }}">
                            @error('child_position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label for="siblings_biological" class="form-label">Jumlah Saudara Kandung</label>
                            <input type="number" min="0" name="siblings_biological" id="siblings_biological"
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
                            <input type="number" min="0" name="siblings_adopted" id="siblings_adopted"
                                class="form-control @error('siblings_adopted') is-invalid @enderror"
                                value="{{ old('siblings_adopted', $student->siblings_adopted) }}">
                            @error('siblings_adopted')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="daily_language" class="form-label">Bahasa Sehari-hari di Rumah</label>
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
                            <label for="father_education" class="form-label">Pendidikan Terakhir Ayah</label>
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
                            <label for="mother_education" class="form-label">Pendidikan Terakhir Ibu</label>
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
                            <label for="guardian_education" class="form-label">Pendidikan Terakhir Wali</label>
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
                            <label for="certificate_date_number" class="form-label">Tanggal dan Nomor Ijazah/STTB</label>
                            <input type="text" name="certificate_date_number" id="certificate_date_number"
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
                            <label for="transfer_accepted_date" class="form-label">Diterima Tanggal</label>
                            <input type="date" name="transfer_accepted_date" id="transfer_accepted_date"
                                class="form-control @error('transfer_accepted_date') is-invalid @enderror"
                                value="{{ old('transfer_accepted_date', $student->previousEducation?->transfer_accepted_date?->format('Y-m-d')) }}">
                            @error('transfer_accepted_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="transfer_letter_number" class="form-label">Nomor Surat Keterangan</label>
                            <input type="text" name="transfer_letter_number" id="transfer_letter_number"
                                class="form-control @error('transfer_letter_number') is-invalid @enderror"
                                value="{{ old('transfer_letter_number', $student->previousEducation?->transfer_letter_number) }}">
                            @error('transfer_letter_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tinggi & Berat Badan --}}
            <div class="biduk-card mb-4">
                <div class="card-header">
                    <i class="bi bi-rulers text-success me-2"></i>Tinggi & Berat Badan
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $existingPhysiques = $student->physiques->sortByDesc('academic_year_id');
                        @endphp
                        @if ($existingPhysiques->count() > 0)
                            @foreach ($existingPhysiques as $physique)
                                <div class="col-md-12 border rounded p-3">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label">Tahun Pelajaran</label>
                                            <select name="physiques[{{ $physique->id }}][academic_year_id]"
                                                class="form-select">
                                                <option value="">Pilih tahun</option>
                                                @foreach ($academicYears as $academicYear)
                                                    <option value="{{ $academicYear->id }}"
                                                        {{ old('physiques.' . $physique->id . '.academic_year_id', $physique->academic_year_id) == $academicYear->id ? 'selected' : '' }}>
                                                        {{ $academicYear->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Semester</label>
                                            <select name="physiques[{{ $physique->id }}][semester_id]"
                                                class="form-select">
                                                <option value="">Pilih semester</option>
                                                @foreach ($semesters as $semester)
                                                    <option value="{{ $semester->id }}"
                                                        {{ old('physiques.' . $physique->id . '.semester_id', $physique->semester_id) == $semester->id ? 'selected' : '' }}>
                                                        {{ $semester->academicYear->name ?? '-' }} -
                                                        {{ $semester->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Tinggi Badan (cm)</label>
                                            <input type="number" step="0.1"
                                                name="physiques[{{ $physique->id }}][height]" class="form-control"
                                                value="{{ old('physiques.' . $physique->id . '.height', $physique->height) }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Berat Badan (kg)</label>
                                            <input type="number" step="0.1"
                                                name="physiques[{{ $physique->id }}][weight]" class="form-control"
                                                value="{{ old('physiques.' . $physique->id . '.weight', $physique->weight) }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        <div class="col-md-12 border rounded p-3">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Tahun Pelajaran Baru</label>
                                    <select name="physiques[new][academic_year_id]" class="form-select">
                                        <option value="">Pilih tahun</option>
                                        @foreach ($academicYears as $academicYear)
                                            <option value="{{ $academicYear->id }}"
                                                {{ old('physiques.new.academic_year_id') == $academicYear->id ? 'selected' : '' }}>
                                                {{ $academicYear->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Semester Baru</label>
                                    <select name="physiques[new][semester_id]" class="form-select">
                                        <option value="">Pilih semester</option>
                                        @foreach ($semesters as $semester)
                                            <option value="{{ $semester->id }}"
                                                {{ old('physiques.new.semester_id') == $semester->id ? 'selected' : '' }}>
                                                {{ $semester->academicYear->name ?? '-' }} - {{ $semester->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Tinggi Badan (cm)</label>
                                    <input type="number" step="0.1" name="physiques[new][height]"
                                        class="form-control" value="{{ old('physiques.new.height') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Berat Badan (kg)</label>
                                    <input type="number" step="0.1" name="physiques[new][weight]"
                                        class="form-control" value="{{ old('physiques.new.weight') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Kesehatan --}}
            <div class="biduk-card mb-4">
                <div class="card-header">
                    <i class="bi bi-heart-pulse text-success me-2"></i>Kondisi Kesehatan
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @php
                            $existingHealths = $student->healths->sortByDesc('academic_year_id');
                        @endphp
                        @if ($existingHealths->count() > 0)
                            @foreach ($existingHealths as $health)
                                <div class="col-md-12 border rounded p-3">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label">Tahun Pelajaran</label>
                                            <select name="healths[{{ $health->id }}][academic_year_id]"
                                                class="form-select">
                                                <option value="">Pilih tahun</option>
                                                @foreach ($academicYears as $academicYear)
                                                    <option value="{{ $academicYear->id }}"
                                                        {{ old('healths.' . $health->id . '.academic_year_id', $health->academic_year_id) == $academicYear->id ? 'selected' : '' }}>
                                                        {{ $academicYear->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Semester</label>
                                            <select name="healths[{{ $health->id }}][semester_id]"
                                                class="form-select">
                                                <option value="">Pilih semester</option>
                                                @foreach ($semesters as $semester)
                                                    <option value="{{ $semester->id }}"
                                                        {{ old('healths.' . $health->id . '.semester_id', $health->semester_id) == $semester->id ? 'selected' : '' }}>
                                                        {{ $semester->academicYear->name ?? '-' }} -
                                                        {{ $semester->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Pendengaran</label>
                                            <input type="text" name="healths[{{ $health->id }}][hearing]"
                                                class="form-control"
                                                value="{{ old('healths.' . $health->id . '.hearing', $health->hearing) }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Penglihatan</label>
                                            <input type="text" name="healths[{{ $health->id }}][vision]"
                                                class="form-control"
                                                value="{{ old('healths.' . $health->id . '.vision', $health->vision) }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Gigi</label>
                                            <input type="text" name="healths[{{ $health->id }}][teeth]"
                                                class="form-control"
                                                value="{{ old('healths.' . $health->id . '.teeth', $health->teeth) }}">
                                        </div>
                                        <div class="col-md-12">
                                            <label class="form-label">Keterangan</label>
                                            <textarea name="healths[{{ $health->id }}][notes]" class="form-control" rows="2">{{ old('healths.' . $health->id . '.notes', $health->notes) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        <div class="col-md-12 border rounded p-3">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Tahun Pelajaran Baru</label>
                                    <select name="healths[new][academic_year_id]" class="form-select">
                                        <option value="">Pilih tahun</option>
                                        @foreach ($academicYears as $academicYear)
                                            <option value="{{ $academicYear->id }}"
                                                {{ old('healths.new.academic_year_id') == $academicYear->id ? 'selected' : '' }}>
                                                {{ $academicYear->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Semester Baru</label>
                                    <select name="healths[new][semester_id]" class="form-select">
                                        <option value="">Pilih semester</option>
                                        @foreach ($semesters as $semester)
                                            <option value="{{ $semester->id }}"
                                                {{ old('healths.new.semester_id') == $semester->id ? 'selected' : '' }}>
                                                {{ $semester->academicYear->name ?? '-' }} - {{ $semester->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Pendengaran</label>
                                    <input type="text" name="healths[new][hearing]" class="form-control"
                                        value="{{ old('healths.new.hearing') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Penglihatan</label>
                                    <input type="text" name="healths[new][vision]" class="form-control"
                                        value="{{ old('healths.new.vision') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Gigi</label>
                                    <input type="text" name="healths[new][teeth]" class="form-control"
                                        value="{{ old('healths.new.teeth') }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Keterangan</label>
                                    <textarea name="healths[new][notes]" class="form-control" rows="2">{{ old('healths.new.notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Penempatan & Status --}}
            <div class="biduk-card mb-4">
                <div class="card-header">
                    <i class="bi bi-building text-success me-2"></i>Penempatan & Status
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
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
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
                                    <i class="bi bi-check-circle me-1"></i> Foto sudah ada. Upload ulang untuk mengganti.
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
