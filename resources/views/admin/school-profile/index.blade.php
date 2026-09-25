@extends('layouts.app')

@section('title', 'Profil Sekolah')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Profil Sekolah</li>
@endsection

@section('content')
<style>
    .sp-header {
        position: relative;
        overflow: hidden;
        padding: 1.5rem 1.75rem;
        margin-bottom: 1.5rem;
        border: none;
        border-radius: 14px;
        background: linear-gradient(135deg, #ffffff 0%, #f0faf3 100%);
        box-shadow: var(--biduk-card-shadow);
        border-left: 5px solid var(--biduk-primary);
    }

    .sp-header h1 {
        margin: 0 0 0.35rem;
        color: #1f2937;
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .sp-header p {
        margin: 0;
        color: #6b7280;
        font-size: 0.875rem;
    }

    .sp-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        padding: 0.5rem;
        margin-bottom: 1.5rem;
        border: none;
        border-radius: 12px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .sp-tabs .nav-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        border-radius: 8px;
        padding: 0.65rem 1.15rem;
        color: #4b5563;
        font-size: 0.85rem;
        font-weight: 600;
        transition: var(--biduk-transition);
        background: transparent;
    }

    .sp-tabs .nav-link:hover {
        color: var(--biduk-primary);
        background: var(--biduk-primary-light);
    }

    .sp-tabs .nav-link.active {
        color: #ffffff;
        background: var(--biduk-primary-gradient);
        box-shadow: 0 4px 10px rgba(26, 122, 76, 0.25);
    }

    .sp-tabs .nav-link i {
        font-size: 1rem;
    }

    .sp-panel {
        border: none;
        border-radius: 14px;
        padding: 1.5rem;
        background: #ffffff;
        box-shadow: var(--biduk-card-shadow);
    }

    .sp-section {
        margin-bottom: 1.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        overflow: hidden;
    }

    .sp-section:last-child {
        margin-bottom: 0;
    }

    .sp-section-head {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f3f4f6;
        background: #f9fafb;
    }

    .sp-section-head i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        color: var(--biduk-primary);
        background: var(--biduk-primary-light);
        font-size: 1.1rem;
    }

    .sp-section-head h5 {
        margin: 0;
        color: #111827;
        font-size: 0.95rem;
        font-weight: 700;
    }

    .sp-section-head small {
        display: block;
        color: #6b7280;
        font-size: 0.775rem;
    }

    .sp-section-body {
        padding: 1.25rem 1.5rem;
    }

    .sp-upload-box {
        padding: 1rem;
        border: 1.5px dashed #cbd5e1;
        border-radius: 10px;
        background: #f8fafc;
    }

    .sp-preview-img {
        display: block;
        max-width: 160px;
        max-height: 110px;
        margin-top: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 4px;
        background: #ffffff;
        object-fit: contain;
    }

    .sp-feature-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .sp-feature-head h4 {
        margin: 0;
        color: #111827;
        font-size: 1.15rem;
        font-weight: 700;
    }

    .sp-feature-head p {
        margin: 0.25rem 0 0;
        color: #6b7280;
        font-size: 0.8rem;
    }

    .sp-item {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        transition: var(--biduk-transition);
    }

    .sp-item:hover {
        border-color: #86efac;
        box-shadow: 0 4px 15px rgba(26, 122, 76, 0.08);
        transform: translateY(-2px);
    }

    .sp-item-body {
        padding: 1.15rem 1.25rem;
    }

    .sp-item-title {
        color: #111827;
        font-size: 0.95rem;
        font-weight: 700;
    }

    .sp-item-meta {
        color: #6b7280;
        font-size: 0.775rem;
        margin-top: 0.25rem;
    }

    .sp-item-description {
        color: #4b5563;
        font-size: 0.825rem;
        line-height: 1.6;
        margin: 0.5rem 0 0;
    }

    .sp-empty {
        padding: 3rem 1.5rem;
        border: 2px dashed #e5e7eb;
        border-radius: 12px;
        text-align: center;
        background: #f9fafb;
        color: #6b7280;
    }

    .sp-empty i {
        font-size: 2.5rem;
        color: #9ca3af;
        margin-bottom: 0.75rem;
        display: block;
    }

    .sp-gallery-thumb {
        width: 100%;
        height: 180px;
        object-fit: cover;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }

    @media (max-width: 768px) {
        .sp-header {
            padding: 1.25rem;
        }
        .sp-tabs {
            overflow-x: auto;
            flex-wrap: nowrap;
        }
        .sp-tabs .nav-link {
            white-space: nowrap;
        }
    }
</style>

<div class="fade-in-up">
    {{-- Header with Quick Link to Landing Page --}}
    <div class="sp-header d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h1>Profil Sekolah</h1>
            <p>Kelola identitas, visi misi, prestasi, ekstrakurikuler, berita, dan galeri sekolah yang terhubung ke Landing Page.</p>
        </div>
        <a href="{{ route('landing') }}" target="_blank" class="btn btn-biduk-outline btn-sm">
            <i class="bi bi-box-arrow-up-right me-1"></i> Buka Landing Page
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Terjadi kesalahan input:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @php
        $activeTab = session('active_tab', 'informasi');
    @endphp

    {{-- Tabs --}}
    <ul class="nav sp-tabs" role="tablist" id="schoolProfileTabs">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'informasi' ? 'active' : '' }}"
                    id="tab-btn-informasi"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-informasi"
                    type="button"
                    role="tab">
                <i class="bi bi-building"></i>
                <span>Informasi Sekolah</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'prestasi' ? 'active' : '' }}"
                    id="tab-btn-prestasi"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-prestasi"
                    type="button"
                    role="tab">
                <i class="bi bi-trophy"></i>
                <span>Prestasi ({{ $achievements->count() }})</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'ekskul' ? 'active' : '' }}"
                    id="tab-btn-ekskul"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-ekskul"
                    type="button"
                    role="tab">
                <i class="bi bi-people"></i>
                <span>Ekstrakurikuler ({{ $extracurriculars->count() }})</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'berita' ? 'active' : '' }}"
                    id="tab-btn-berita"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-berita"
                    type="button"
                    role="tab">
                <i class="bi bi-newspaper"></i>
                <span>Berita ({{ $newsItems->count() }})</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $activeTab === 'galeri' ? 'active' : '' }}"
                    id="tab-btn-galeri"
                    data-bs-toggle="tab"
                    data-bs-target="#tab-galeri"
                    type="button"
                    role="tab">
                <i class="bi bi-images"></i>
                <span>Galeri ({{ $galleries->count() }})</span>
            </button>
        </li>
    </ul>

    {{-- Tab Contents --}}
    <div class="tab-content" id="schoolProfileTabContent">

        {{-- ==================== TAB 1: INFORMASI SEKOLAH ==================== --}}
        <div class="tab-pane fade {{ $activeTab === 'informasi' ? 'show active' : '' }}" id="tab-informasi" role="tabpanel">
            <div class="sp-panel">
                <form action="{{ route('school-profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    {{-- Section: Identitas & Kontak --}}
                    <div class="sp-section">
                        <div class="sp-section-head">
                            <i class="bi bi-building"></i>
                            <div>
                                <h5>Identitas dan Kontak Utama</h5>
                                <small>Data profil resmi dan informasi kontak sekolah yang ditampilkan di landing page & footer.</small>
                            </div>
                        </div>
                        <div class="sp-section-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Sekolah <span class="text-danger">*</span></label>
                                <input class="form-control" type="text" name="name" value="{{ old('name', $school->name) }}" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">NPSN</label>
                                <input class="form-control" type="text" name="npsn" value="{{ old('npsn', $school->npsn) }}">
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">NSS</label>
                                <input class="form-control" type="text" name="nss" value="{{ old('nss', $school->nss) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Nomor Telepon</label>
                                <input class="form-control" type="text" name="phone" value="{{ old('phone', $school->phone) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Email Sekolah</label>
                                <input class="form-control" type="email" name="email" value="{{ old('email', $school->email) }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Website</label>
                                <input class="form-control" type="text" name="website" value="{{ old('website', $school->website) }}" placeholder="https://sdn204palembang.sch.id">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" name="address" rows="3">{{ old('address', $school->address) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Deskripsi / Profil Singkat Sekolah</label>
                                <textarea class="form-control" name="description" rows="4">{{ old('description', $school->description) }}</textarea>
                                <div class="form-text">Deskripsi yang menjelaskan sejarah, karakteristik, dan komitmen layanan pendidikan sekolah.</div>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Hero / Banner Landing Page --}}
                    <div class="sp-section">
                        <div class="sp-section-head">
                            <i class="bi bi-stars"></i>
                            <div>
                                <h5>Tampilan Beranda (Hero Section)</h5>
                                <small>Pengaturan teks pembuka dan foto utama yang tampil di bagian teratas Landing Page.</small>
                            </div>
                        </div>
                        <div class="sp-section-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Judul Utama Beranda (Hero Title)</label>
                                <input class="form-control" type="text" name="hero_title" value="{{ old('hero_title', $school->hero_title) }}" placeholder="Mengenal Lebih Dekat SDN 204">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Foto Utama Beranda (Hero Image)</label>
                                <div class="sp-upload-box">
                                    <input class="form-control" type="file" name="school_image" accept="image/*">
                                    <div class="form-text">Format: JPG, PNG, WEBP. Maks 5MB.</div>
                                    @if($school->school_image)
                                        <div class="mt-2">
                                            <span class="badge bg-light text-dark border">Foto Saat Ini:</span>
                                            <img class="sp-preview-img" src="{{ asset('storage/'.$school->school_image) }}" alt="Foto hero">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Deskripsi Pembuka (Hero Description)</label>
                                <textarea class="form-control" name="hero_description" rows="3" placeholder="Website informasi resmi SDN 204 Palembang...">{{ old('hero_description', $school->hero_description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Visi & Misi --}}
                    <div class="sp-section">
                        <div class="sp-section-head">
                            <i class="bi bi-bullseye"></i>
                            <div>
                                <h5>Visi dan Misi Sekolah</h5>
                                <small>Landasan dan arah pengembangan pendidikan SD Negeri 204.</small>
                            </div>
                        </div>
                        <div class="sp-section-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Visi Sekolah</label>
                                <textarea class="form-control" name="vision" rows="8" placeholder="Tuliskan visi sekolah...">{{ old('vision', $school->vision) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Misi Sekolah</label>
                                <textarea class="form-control" name="mission" rows="8" placeholder="Tuliskan butir-butir misi sekolah...">{{ old('mission', $school->mission) }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Section: Media Logo, Cover & Struktur Organisasi --}}
                    <div class="sp-section">
                        <div class="sp-section-head">
                            <i class="bi bi-diagram-3"></i>
                            <div>
                                <h5>Media & Struktur Organisasi</h5>
                                <small>Kelola logo instansi, cover foto profil, dan bagan struktur kepengurusan sekolah.</small>
                            </div>
                        </div>
                        <div class="sp-section-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Logo Sekolah</label>
                                <div class="sp-upload-box">
                                    <input class="form-control" type="file" name="logo" accept="image/*">
                                    <div class="form-text">Ditampilkan pada navbar dan header dokumen resmi.</div>
                                    @if($school->logo)
                                        <div class="mt-2">
                                            <span class="badge bg-light text-dark border">Logo Saat Ini:</span>
                                            <img class="sp-preview-img" src="{{ asset('storage/'.$school->logo) }}" alt="Logo sekolah">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Foto Gedung / Cover Sekolah</label>
                                <div class="sp-upload-box">
                                    <input class="form-control" type="file" name="cover_image" accept="image/*">
                                    <div class="form-text">Ditampilkan pada bagian Profil Informasi Sekolah.</div>
                                    @if($school->cover_image)
                                        <div class="mt-2">
                                            <span class="badge bg-light text-dark border">Cover Saat Ini:</span>
                                            <img class="sp-preview-img" src="{{ asset('storage/'.$school->cover_image) }}" alt="Cover sekolah">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Judul Struktur Organisasi</label>
                                <input class="form-control" type="text" name="organization_title" value="{{ old('organization_title', $school->organization_title) }}" placeholder="Struktur Organisasi SDN 204 Palembang">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Bagan Gambar Struktur Organisasi</label>
                                <div class="sp-upload-box">
                                    <input class="form-control" type="file" name="organization_image" accept="image/*">
                                    <div class="form-text">Upload bagan struktur organisasi (JPG/PNG/WEBP).</div>
                                    @if($school->organization_image)
                                        <div class="mt-2">
                                            <span class="badge bg-light text-dark border">Bagan Saat Ini:</span>
                                            <img class="sp-preview-img" src="{{ asset('storage/'.$school->organization_image) }}" alt="Struktur organisasi">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Keterangan / Deskripsi Struktur Organisasi</label>
                                <textarea class="form-control" name="organization_description" rows="3" placeholder="Penjelasan tata kelola dan manajemen SDN 204...">{{ old('organization_description', $school->organization_description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-biduk-primary px-4 py-2" type="submit">
                            <i class="bi bi-save2-fill me-2"></i>
                            Simpan Perubahan Informasi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ==================== TAB 2: PRESTASI ==================== --}}
        <div class="tab-pane fade {{ $activeTab === 'prestasi' ? 'show active' : '' }}" id="tab-prestasi" role="tabpanel">
            <div class="sp-panel">
                <div class="sp-feature-head">
                    <div>
                        <h4>Prestasi Sekolah & Siswa</h4>
                        <p>Kelola pencapaian, piala, dan penghargaan yang diraih oleh SDN 204.</p>
                    </div>
                    <button class="btn btn-biduk-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAchievement">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Prestasi
                    </button>
                </div>

                @forelse($achievements as $item)
                    <div class="sp-item mb-3">
                        <div class="sp-item-body d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div class="d-flex align-items-start gap-3 flex-grow-1">
                                @if($item->image)
                                    <img src="{{ asset('storage/'.$item->image) }}"
                                         alt="{{ $item->title }}"
                                         class="rounded-3 border flex-shrink-0"
                                         style="width: 100px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="rounded-3 border d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 100px; height: 80px; background: #fef3c7; color: #d97706;">
                                        <i class="bi bi-trophy-fill fs-3"></i>
                                    </div>
                                @endif

                                <div>
                                    <div class="sp-item-title">{{ $item->title }}</div>
                                    <div class="sp-item-meta">
                                        <span class="badge bg-light text-dark border me-1">
                                            <i class="bi bi-calendar3 me-1"></i>{{ $item->year ?: 'Tahun -' }}
                                        </span>
                                        @if($item->level)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                Tingkat {{ $item->level }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($item->description)
                                        <p class="sp-item-description">{{ $item->description }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#editAchievement{{ $item->id }}">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('school-achievements.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data prestasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="sp-empty">
                        <i class="bi bi-trophy"></i>
                        <h5>Belum Ada Data Prestasi</h5>
                        <p class="mb-3">Silakan tambahkan data prestasi atau pencapaian sekolah untuk ditampilkan di Landing Page.</p>
                        <button class="btn btn-biduk-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addAchievement">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Prestasi Sekarang
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ==================== TAB 3: EKSTRAKURIKULER ==================== --}}
        <div class="tab-pane fade {{ $activeTab === 'ekskul' ? 'show active' : '' }}" id="tab-ekskul" role="tabpanel">
            <div class="sp-panel">
                <div class="sp-feature-head">
                    <div>
                        <h4>Kegiatan Ekstrakurikuler</h4>
                        <p>Kelola program minat, bakat, dan kegiatan ekstrakurikuler siswa.</p>
                    </div>
                    <button class="btn btn-biduk-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEkskul">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Ekskul
                    </button>
                </div>

                @forelse($extracurriculars as $item)
                    <div class="sp-item mb-3">
                        <div class="sp-item-body d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div class="d-flex align-items-start gap-3 flex-grow-1">
                                @if($item->image)
                                    <img src="{{ asset('storage/' . $item->image) }}"
                                         alt="{{ $item->name }}"
                                         class="rounded-3 border flex-shrink-0"
                                         style="width: 100px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="rounded-3 border d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 100px; height: 80px; background: #e8f5ee; color: #1a7a4c;">
                                        <i class="bi bi-people-fill fs-3"></i>
                                    </div>
                                @endif

                                <div>
                                    <div class="sp-item-title">{{ $item->name }}</div>
                                    <div class="sp-item-meta">
                                        @if($item->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="bi bi-check-circle me-1"></i>Aktif di Landing Page
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border">
                                                <i class="bi bi-x-circle me-1"></i>Tidak Aktif
                                            </span>
                                        @endif
                                    </div>
                                    @if($item->description)
                                        <p class="sp-item-description">{{ $item->description }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#editEkskul{{ $item->id }}">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('extracurriculars.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ekstrakurikuler ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="sp-empty">
                        <i class="bi bi-people"></i>
                        <h5>Belum Ada Data Ekstrakurikuler</h5>
                        <p class="mb-3">Silakan tambahkan data ekstrakurikuler seperti Pramuka, Seni Tari, Olahraga, dll.</p>
                        <button class="btn btn-biduk-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEkskul">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Ekskul Sekarang
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ==================== TAB 4: BERITA ==================== --}}
        <div class="tab-pane fade {{ $activeTab === 'berita' ? 'show active' : '' }}" id="tab-berita" role="tabpanel">
            <div class="sp-panel">
                <div class="sp-feature-head">
                    <div>
                        <h4>Berita & Pengumuman Sekolah</h4>
                        <p>Kelola artikel berita dan dokumentasi kegiatan sekolah terkini.</p>
                    </div>
                    <button class="btn btn-biduk-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addNews">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Berita
                    </button>
                </div>

                @forelse($newsItems as $item)
                    <div class="sp-item mb-3">
                        <div class="sp-item-body d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div class="d-flex align-items-start gap-3 flex-grow-1">
                                @if($item->image)
                                    <img src="{{ asset('storage/'.$item->image) }}"
                                         alt="{{ $item->title }}"
                                         class="rounded-3 border flex-shrink-0"
                                         style="width: 110px; height: 85px; object-fit: cover;">
                                @else
                                    <div class="rounded-3 border d-flex align-items-center justify-content-center flex-shrink-0"
                                         style="width: 110px; height: 85px; background: #e0f2fe; color: #0284c7;">
                                        <i class="bi bi-newspaper fs-3"></i>
                                    </div>
                                @endif

                                <div>
                                    <div class="sp-item-title">{{ $item->title }}</div>
                                    <div class="sp-item-meta">
                                        <span class="badge bg-light text-dark border me-1">
                                            <i class="bi bi-calendar3 me-1"></i>{{ optional($item->published_at)->translatedFormat('d M Y') ?: 'Tanggal -' }}
                                        </span>
                                        @if($item->is_published)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                <i class="bi bi-broadcast me-1"></i>Dipublikasikan
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                <i class="bi bi-file-earmark me-1"></i>Draft
                                            </span>
                                        @endif
                                    </div>
                                    @if($item->summary)
                                        <p class="sp-item-description">{{ Str::limit($item->summary, 160) }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#editNews{{ $item->id }}">
                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('school-news.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus berita ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" type="submit">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="sp-empty">
                        <i class="bi bi-newspaper"></i>
                        <h5>Belum Ada Berita Sekolah</h5>
                        <p class="mb-3">Publikasikan berita atau informasi terkini mengenai kegiatan sekolah.</p>
                        <button class="btn btn-biduk-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addNews">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Berita Baru
                        </button>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- ==================== TAB 5: GALERI ==================== --}}
        <div class="tab-pane fade {{ $activeTab === 'galeri' ? 'show active' : '' }}" id="tab-galeri" role="tabpanel">
            <div class="sp-panel">
                <div class="sp-feature-head">
                    <div>
                        <h4>Galeri Dokumentasi Foto</h4>
                        <p>Kelola dokumentasi visual kegiatan, fasilitas, dan lingkungan sekolah.</p>
                    </div>
                    <button class="btn btn-biduk-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addGallery">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Foto Galeri
                    </button>
                </div>

                @if($galleries->count())
                    <div class="row g-3">
                        @foreach($galleries as $item)
                            <div class="col-md-6 col-lg-4">
                                <div class="sp-item overflow-hidden h-100 d-flex flex-column">
                                    @if($item->image)
                                        <img class="sp-gallery-thumb" src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}">
                                    @endif

                                    <div class="sp-item-body d-flex flex-column flex-grow-1 justify-content-between">
                                        <div>
                                            <div class="d-flex justify-content-between align-items-start gap-2">
                                                <div class="sp-item-title">{{ $item->title }}</div>
                                                @if($item->is_active)
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle flex-shrink-0">
                                                        Aktif
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary border flex-shrink-0">
                                                        Sembunyi
                                                    </span>
                                                @endif
                                            </div>
                                            @if($item->description)
                                                <p class="sp-item-description">{{ Str::limit($item->description, 100) }}</p>
                                            @endif
                                        </div>

                                        <div class="d-flex gap-2 mt-3 pt-2 border-top">
                                            <button class="btn btn-sm btn-outline-success flex-grow-1" data-bs-toggle="modal" data-bs-target="#editGallery{{ $item->id }}">
                                                <i class="bi bi-pencil-square me-1"></i> Edit
                                            </button>
                                            <form method="POST" action="{{ route('school-galleries.destroy', $item) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto galeri ini?')" class="flex-grow-1">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger w-100" type="submit">
                                                    <i class="bi bi-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="sp-empty">
                        <i class="bi bi-images"></i>
                        <h5>Belum Ada Foto Galeri</h5>
                        <p class="mb-3">Tambahkan dokumentasi foto kegiatan sekolah untuk ditampilkan pada Landing Page.</p>
                        <button class="btn btn-biduk-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addGallery">
                            <i class="bi bi-plus-circle me-1"></i> Tambah Galeri Sekarang
                        </button>
                    </div>
                @endif
            </div>
        </div>

    </div>
</div>

@include('admin.school-profile.modals')
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Support URL hash navigation e.g. #tab-berita
        const hash = window.location.hash;
        if (hash) {
            const tabBtn = document.querySelector('button[data-bs-target="' + hash + '"]');
            if (tabBtn) {
                const tab = new bootstrap.Tab(tabBtn);
                tab.show();
            }
        }

        // Update URL hash when clicking tab
        const tabButtons = document.querySelectorAll('#schoolProfileTabs button[data-bs-toggle="tab"]');
        tabButtons.forEach(btn => {
            btn.addEventListener('shown.bs.tab', function(e) {
                const target = e.target.getAttribute('data-bs-target');
                if (target && history.replaceState) {
                    history.replaceState(null, null, target);
                }
            });
        });
    });
</script>
@endpush
