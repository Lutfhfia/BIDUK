@extends('layouts.app')

@section('title', 'Detail Siswa')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ url('/') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Data Siswa</a></li>
    <li class="breadcrumb-item active">Detail — {{ $student->name }}</li>
@endsection

@section('content')
    <div class="fade-in-up">
        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold text-dark mb-1">
                    <i class="bi bi-person-lines-fill text-primary me-2"></i>Detail Data Siswa
                </h4>
                <p class="text-muted mb-0" style="font-size: 0.85rem;">
                    Informasi lengkap peserta didik
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('students.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ route('students.edit', $student) }}" class="btn btn-biduk-primary">
                    <i class="bi bi-pencil-square me-1"></i> Edit Data
                </a>
            </div>
        </div>

        <div class="row g-4">
            {{-- Left Column: Profile Card --}}
            <div class="col-12 col-lg-4">
                <div class="biduk-card text-center overflow-hidden">
                    <div style="height: 100px; background: var(--biduk-primary-gradient);"></div>
                    <div class="card-body position-relative pt-0 pb-4 px-4">
                        <div class="mx-auto bg-white p-1 rounded-circle mb-3"
                            style="width: 110px; height: 110px; margin-top: -55px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                            @if ($student->photo)
                                <img src="{{ asset('storage/' . $student->photo) }}" alt="Foto"
                                    class="rounded-circle w-100 h-100 object-fit-cover">
                            @else
                                <div class="rounded-circle w-100 h-100 d-flex align-items-center justify-content-center"
                                    style="background: {{ $student->gender === 'L' ? '#e3f2fd' : '#fce4ec' }};
                                        color: {{ $student->gender === 'L' ? '#1565c0' : '#c62828' }}; font-size: 2.5rem; font-weight: 600;">
                                    {{ strtoupper(substr($student->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <h5 class="fw-bold mb-1">{{ $student->name }}</h5>
                        <p class="text-muted mb-3" style="font-size: 0.9rem;">NIS: {{ $student->nis }}</p>

                        @php
                            $badgeClass = match ($student->status) {
                                'Aktif' => 'badge-aktif',
                                'Pindah' => 'badge-pindah',
                                'Lulus' => 'badge-lulus',
                                'Alumni' => 'badge-alumni',
                                'Nonaktif' => 'badge-nonaktif',
                                default => 'bg-secondary',
                            };
                        @endphp
                        <div class="d-flex justify-content-center gap-2 mb-4">
                            <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill">{{ $student->status }}</span>
                            @if ($student->currentClassName)
                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                    Kelas {{ $student->currentClassName }}
                                </span>
                            @endif
                        </div>

                        <hr class="text-muted opacity-25">

                        <div class="d-flex justify-content-between text-start mb-2" style="font-size: 0.85rem;">
                            <span class="text-muted">NISN</span>
                            <span class="fw-medium">{{ $student->nisn }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-start mb-2" style="font-size: 0.85rem;">
                            <span class="text-muted">Kelas</span>
                            <span class="fw-medium">{{ $student->currentClassName ?? '-' }}</span>
                        </div>
                        <div class="d-flex justify-content-between text-start" style="font-size: 0.85rem;">
                            <span class="text-muted">Agama</span>
                            <span class="fw-medium">{{ $student->religion ?? '-' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column: Details Tabs --}}
            <div class="col-12 col-lg-8">
                <div class="biduk-card h-100">
                    <div class="card-header bg-transparent border-bottom-0 pt-3 px-4 pb-0">
                        <ul class="nav nav-tabs border-bottom-0 gap-3 flex-wrap" id="studentTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button
                                    class="nav-link active fw-medium px-1 py-2 text-dark border-bottom border-3 border-success bg-transparent"
                                    id="keterangan-tab" data-bs-toggle="tab" data-bs-target="#keterangan" type="button"
                                    role="tab" style="border-radius: 0;">
                                    Keterangan Siswa
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-medium px-1 py-2 text-muted border-0 bg-transparent"
                                    id="ortu-tab" data-bs-toggle="tab" data-bs-target="#ortu" type="button" role="tab"
                                    style="border-radius: 0;">
                                    Orang Tua / Wali
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-medium px-1 py-2 text-muted border-0 bg-transparent"
                                    id="perkembangan-tab" data-bs-toggle="tab" data-bs-target="#perkembangan" type="button"
                                    role="tab" style="border-radius: 0;">
                                    Perkembangan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-medium px-1 py-2 text-muted border-0 bg-transparent"
                                    id="physique-tab" data-bs-toggle="tab" data-bs-target="#physique" type="button"
                                    role="tab" style="border-radius: 0;">
                                    Tinggi & Berat Badan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-medium px-1 py-2 text-muted border-0 bg-transparent"
                                    id="health-tab" data-bs-toggle="tab" data-bs-target="#health" type="button"
                                    role="tab" style="border-radius: 0;">
                                    Kesehatan
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-medium px-1 py-2 text-muted border-0 bg-transparent"
                                    id="lainnya-tab" data-bs-toggle="tab" data-bs-target="#lainnya" type="button"
                                    role="tab" style="border-radius: 0;">
                                    Bagian Lainnya
                                </button>
                            </li>
                        </ul>
                        <hr class="m-0 text-muted opacity-25">
                    </div>

                    <div class="card-body p-4">
                        <div class="tab-content" id="studentTabContent">
                            {{-- Tab 1: Keterangan Siswa --}}
                            <div class="tab-pane fade show active" id="keterangan" role="tabpanel">
                                <div class="mb-4">
                                    <h6 class="text-success mb-3 fw-bold"><i class="bi bi-person-badge me-2"></i>Identitas
                                        Peserta Didik</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">NIS</div>
                                            <div class="fw-medium">{{ $student->nis ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">NISN</div>
                                            <div class="fw-medium">{{ $student->nisn ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Nomor Kode Sekolah
                                            </div>
                                            <div class="fw-medium">{{ $student->school_code ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Nomor Kode Kecamatan
                                            </div>
                                            <div class="fw-medium">{{ $student->district_code ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Nomor Kode
                                                Kabupaten/Kota</div>
                                            <div class="fw-medium">{{ $student->city_code ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Nomor Kode Provinsi
                                            </div>
                                            <div class="fw-medium">{{ $student->province_code ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Nomor Urut</div>
                                            <div class="fw-medium">{{ $student->student_number ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Kewarganegaraan
                                            </div>
                                            <div class="fw-medium">{{ $student->citizenship ?? 'WNI' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Nama Peserta Didik
                                            </div>
                                            <div class="fw-medium">{{ $student->name ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Jenis Kelamin</div>
                                            <div class="fw-medium">
                                                {{ $student->gender == 'L' ? 'Laki-laki' : 'Perempuan' }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Tempat Lahir</div>
                                            <div class="fw-medium">{{ $student->birth_place ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Tanggal Lahir</div>
                                            <div class="fw-medium">
                                                {{ $student->birth_date ? $student->birth_date->translatedFormat('d F Y') : '-' }}
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Agama</div>
                                            <div class="fw-medium">{{ $student->religion ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Nilai / Pas Foto
                                            </div>
                                            <div class="fw-medium">
                                                {{ $student->photo ? 'Foto tersedia' : 'Foto belum diunggah' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="text-success mb-3 fw-bold"><i class="bi bi-people me-2"></i>Data Saudara
                                    </h6>
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Anak ke-</div>
                                            <div class="fw-medium">{{ $student->child_position ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Jumlah Saudara
                                                Kandung</div>
                                            <div class="fw-medium">{{ $student->siblings_biological ?? 0 }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Jumlah Saudara Tiri
                                            </div>
                                            <div class="fw-medium">{{ $student->siblings_step ?? 0 }}</div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Jumlah Saudara
                                                Angkat</div>
                                            <div class="fw-medium">{{ $student->siblings_adopted ?? 0 }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="text-success mb-3 fw-bold"><i class="bi bi-card-list me-2"></i>Data
                                        Tambahan</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Bahasa Sehari-hari
                                                di Rumah</div>
                                            <div class="fw-medium">{{ $student->daily_language ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Golongan Darah</div>
                                            <div class="fw-medium">{{ $student->blood_type ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h6 class="text-success mb-3 fw-bold"><i class="bi bi-geo-alt me-2"></i>Alamat</h6>
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Alamat</div>
                                            <div class="fw-medium">{{ $student->address ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">RT/RW</div>
                                            <div class="fw-medium">{{ $student->rt_rw ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Desa/Kelurahan</div>
                                            <div class="fw-medium">{{ $student->village ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Kecamatan</div>
                                            <div class="fw-medium">{{ $student->district ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Kabupaten/Kota</div>
                                            <div class="fw-medium">{{ $student->city ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Provinsi</div>
                                            <div class="fw-medium">{{ $student->province ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Kode Pos</div>
                                            <div class="fw-medium">{{ $student->postal_code ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Nomor Telepon</div>
                                            <div class="fw-medium">{{ $student->phone_number ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Bertempat Tinggal
                                                Pada</div>
                                            <div class="fw-medium">{{ $student->living_with ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Jarak ke Sekolah
                                            </div>
                                            <div class="fw-medium">{{ $student->distance_to_school ?? '-' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h6 class="text-success mb-3 fw-bold"><i class="bi bi-image me-2"></i>Pas Foto</h6>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="text-muted">Pas foto tetap tersedia pada detail siswa.</div>
                                        @if ($student->photo)
                                            <span class="badge bg-success bg-opacity-10 text-success">Foto tersimpan</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary">Belum ada
                                                foto</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 2: Orang Tua / Wali --}}
                            <div class="tab-pane fade" id="ortu" role="tabpanel">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <h6 class="text-success mb-3 fw-bold"><i class="bi bi-person me-2"></i>Data Ayah
                                        </h6>
                                        <div class="mb-3">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Nama Ayah</div>
                                            <div class="fw-medium">{{ $student->father_name ?? '-' }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Pendidikan Terakhir
                                                Ayah</div>
                                            <div class="fw-medium">{{ $student->father_education ?? '-' }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Pekerjaan Ayah</div>
                                            <div class="fw-medium">{{ $student->father_job ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-success mb-3 fw-bold"><i class="bi bi-person me-2"></i>Data Ibu
                                        </h6>
                                        <div class="mb-3">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Nama Ibu</div>
                                            <div class="fw-medium">{{ $student->mother_name ?? '-' }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Pendidikan Terakhir
                                                Ibu</div>
                                            <div class="fw-medium">{{ $student->mother_education ?? '-' }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <div class="text-muted"
                                                style="font-size: 0.75rem; text-transform: uppercase;">Pekerjaan Ibu</div>
                                            <div class="fw-medium">{{ $student->mother_job ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12 mt-2 pt-3 border-top">
                                        <h6 class="text-success mb-3 fw-bold"><i class="bi bi-person-check me-2"></i>Data
                                            Wali</h6>
                                        <div class="row">
                                            <div class="col-md-3 mb-3">
                                                <div class="text-muted"
                                                    style="font-size: 0.75rem; text-transform: uppercase;">Nama Wali</div>
                                                <div class="fw-medium">{{ $student->guardian_name ?? '-' }}</div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="text-muted"
                                                    style="font-size: 0.75rem; text-transform: uppercase;">Hubungan
                                                    Keluarga</div>
                                                <div class="fw-medium">{{ $student->guardian_relation ?? '-' }}</div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="text-muted"
                                                    style="font-size: 0.75rem; text-transform: uppercase;">Pendidikan
                                                    Terakhir</div>
                                                <div class="fw-medium">{{ $student->guardian_education ?? '-' }}</div>
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <div class="text-muted"
                                                    style="font-size: 0.75rem; text-transform: uppercase;">Pekerjaan Wali
                                                </div>
                                                <div class="fw-medium">{{ $student->guardian_job ?? '-' }}</div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="text-muted"
                                                    style="font-size: 0.75rem; text-transform: uppercase;">No. Telepon Wali
                                                </div>
                                                <div class="fw-medium">{{ $student->guardian_phone ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 3: Perkembangan Peserta Didik --}}
                            <div class="tab-pane fade" id="perkembangan" role="tabpanel">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <h6 class="text-success mb-3 fw-bold"><i
                                                class="bi bi-mortarboard me-2"></i>Pendidikan Sebelumnya</h6>
                                        @if ($student->previousEducation)
                                            <div class="row g-3">
                                                <div class="col-md-4">
                                                    <div class="text-muted"
                                                        style="font-size: 0.75rem; text-transform: uppercase;">Asal Sekolah
                                                    </div>
                                                    <div class="fw-medium">
                                                        {{ $student->previousEducation->previous_school_name ?? '-' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="text-muted"
                                                        style="font-size: 0.75rem; text-transform: uppercase;">Nama Sekolah
                                                    </div>
                                                    <div class="fw-medium">
                                                        {{ $student->previousEducation->previous_school_name ?? '-' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="text-muted"
                                                        style="font-size: 0.75rem; text-transform: uppercase;">Tanggal dan
                                                        Nomor Ijazah/STTB</div>
                                                    <div class="fw-medium">
                                                        {{ $student->previousEducation->certificate_date_number ?? '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-muted">Belum ada data pendidikan sebelumnya.</div>
                                        @endif
                                    </div>
                                    <div class="col-12 mt-3 pt-3 border-top">
                                        <h6 class="text-success mb-3 fw-bold"><i
                                                class="bi bi-arrow-repeat me-2"></i>Pindah dari Sekolah Lain</h6>
                                        @if ($student->previousEducation)
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <div class="text-muted"
                                                        style="font-size: 0.75rem; text-transform: uppercase;">Nama Sekolah
                                                        Asal</div>
                                                    <div class="fw-medium">
                                                        {{ $student->previousEducation->transfer_from_school ?? '-' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-muted"
                                                        style="font-size: 0.75rem; text-transform: uppercase;">Dari Tingkat
                                                    </div>
                                                    <div class="fw-medium">
                                                        {{ $student->previousEducation->transfer_from_grade ?? '-' }}</div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-muted"
                                                        style="font-size: 0.75rem; text-transform: uppercase;">Diterima
                                                        Tanggal</div>
                                                    <div class="fw-medium">
                                                        {{ $student->previousEducation->transfer_accepted_date ? $student->previousEducation->transfer_accepted_date->translatedFormat('d F Y') : '-' }}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-muted"
                                                        style="font-size: 0.75rem; text-transform: uppercase;">Nomor Surat
                                                        Keterangan</div>
                                                    <div class="fw-medium">
                                                        {{ $student->previousEducation->transfer_letter_number ?? '-' }}
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-muted">Belum ada data pindah sekolah.</div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 4: Tinggi & Berat Badan --}}
                            <div class="tab-pane fade" id="physique" role="tabpanel">
                                <h6 class="text-success mb-3 fw-bold"><i class="bi bi-rulers me-2"></i>Tinggi & Berat
                                    Badan</h6>
                                @if ($student->physiques && $student->physiques->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Tahun Pelajaran</th>
                                                    <th>Semester</th>
                                                    <th>Tinggi Badan</th>
                                                    <th>Berat Badan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($student->physiques->sortByDesc('academic_year_id') as $physique)
                                                    <tr>
                                                        <td>{{ $physique->academicYear->name ?? '-' }}</td>
                                                        <td>{{ $physique->semester->name ?? '-' }}</td>
                                                        <td>{{ $physique->height ?? '-' }} cm</td>
                                                        <td>{{ $physique->weight ?? '-' }} kg</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-muted">Belum ada histori tinggi dan berat badan.</div>
                                @endif
                            </div>

                            {{-- Tab 5: Kesehatan --}}
                            <div class="tab-pane fade" id="health" role="tabpanel">
                                <h6 class="text-success mb-3 fw-bold"><i class="bi bi-heart-pulse me-2"></i>Kondisi
                                    Kesehatan</h6>
                                @if ($student->healths && $student->healths->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Tahun Pelajaran</th>
                                                    <th>Semester</th>
                                                    <th>Pendengaran</th>
                                                    <th>Penglihatan</th>
                                                    <th>Gigi</th>
                                                    <th>Keterangan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($student->healths->sortByDesc('academic_year_id') as $health)
                                                    <tr>
                                                        <td>{{ $health->academicYear->name ?? '-' }}</td>
                                                        <td>{{ $health->semester->name ?? '-' }}</td>
                                                        <td>{{ $health->hearing ?? '-' }}</td>
                                                        <td>{{ $health->vision ?? '-' }}</td>
                                                        <td>{{ $health->teeth ?? '-' }}</td>
                                                        <td>{{ $health->notes ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-muted">Belum ada data kondisi kesehatan.</div>
                                @endif
                            </div>

                            {{-- Tab 6: Bagian Lainnya --}}
                            <div class="tab-pane fade" id="lainnya" role="tabpanel">
                                <h6 class="text-success mb-3 fw-bold"><i
                                        class="bi bi-list-columns-reverse me-2"></i>Bagian Lainnya</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase;">
                                            Status Siswa</div>
                                        <div class="fw-medium">{{ $student->status ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase;">
                                            Kelas Aktif</div>
                                        <div class="fw-medium">{{ $student->currentClassName ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase;">
                                            Nomor Kartu Keluarga</div>
                                        <div class="fw-medium">{{ $student->kk_number ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="text-muted" style="font-size: 0.75rem; text-transform: uppercase;">
                                            Histori Penempatan Kelas</div>
                                        <div class="fw-medium">
                                            @if ($student->classAssignments->count() > 0)
                                                @foreach ($student->classAssignments->sortByDesc('entry_date') as $assignment)
                                                    <span class="badge bg-light text-dark border me-1 mb-1">
                                                        {{ $assignment->schoolClass->name ?? '-' }}
                                                        {{ $assignment->schoolClass->academicYear->name ?? '' }}
                                                    </span>
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
