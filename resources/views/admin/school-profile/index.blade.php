@extends('layouts.app')

@section('title', 'Profil Sekolah')

@section('content')
<style>
    :root {
        --sp-green: #15803d;
        --sp-green-dark: #166534;
        --sp-green-soft: #edf8f0;
        --sp-ink: #17251e;
        --sp-muted: #728078;
        --sp-border: #e4ebe6;
        --sp-bg: #f6f9f7;
    }

    .school-profile-page {
        min-height: calc(100vh - 70px);
        padding: 30px;
        background:
            radial-gradient(circle at 100% 0, rgba(21,128,61,.07), transparent 25%),
            var(--sp-bg);
    }

    .sp-header {
        position: relative;
        overflow: hidden;
        padding: 28px 30px;
        margin-bottom: 22px;
        border: 1px solid var(--sp-border);
        border-radius: 20px;
        background: linear-gradient(135deg, #fff, #f0faf3);
        box-shadow: 0 10px 30px rgba(22,101,52,.06);
    }

    .sp-header::before {
        content: "";
        position: absolute;
        inset: 0 auto 0 0;
        width: 5px;
        background: linear-gradient(#22a862, #166534);
    }

    .sp-header h1 {
        margin: 0 0 7px;
        color: var(--sp-ink);
        font-size: 28px;
        font-weight: 800;
        letter-spacing: -.7px;
    }

    .sp-header p {
        margin: 0;
        color: var(--sp-muted);
        font-size: 13px;
    }

    .sp-tabs {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        padding: 7px;
        margin-bottom: 22px;
        border: 1px solid var(--sp-border);
        border-radius: 15px;
        background: rgba(255,255,255,.94);
        box-shadow: 0 8px 25px rgba(22,101,52,.05);
    }

    .sp-tabs .nav-link {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: 0;
        border-radius: 10px;
        padding: 11px 16px;
        color: #718078;
        font-size: 12px;
        font-weight: 700;
        transition: .25s ease;
    }

    .sp-tabs .nav-link:hover {
        color: var(--sp-green);
        background: var(--sp-green-soft);
        transform: translateY(-1px);
    }

    .sp-tabs .nav-link.active {
        color: #fff;
        background: linear-gradient(135deg, #199653, #166534);
        box-shadow: 0 5px 14px rgba(21,128,61,.18);
    }

    .sp-panel {
        border: 1px solid var(--sp-border);
        border-radius: 18px;
        padding: 25px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(22,101,52,.045);
        animation: spFade .3s ease;
    }

    .sp-section {
        margin-bottom: 22px;
        overflow: hidden;
        border: 1px solid var(--sp-border);
        border-radius: 15px;
        background: #fff;
    }

    .sp-section:last-child { margin-bottom: 0; }

    .sp-section-head {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 16px 20px;
        border-bottom: 1px solid var(--sp-border);
        background: linear-gradient(135deg, #fbfefc, #f1f9f4);
    }

    .sp-section-head i {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 31px;
        height: 31px;
        border-radius: 9px;
        color: var(--sp-green);
        background: #dff3e7;
    }

    .sp-section-head h5 {
        margin: 0;
        color: #244333;
        font-size: 14px;
        font-weight: 800;
    }

    .sp-section-head small {
        display: block;
        margin-top: 2px;
        color: var(--sp-muted);
        font-size: 11px;
    }

    .sp-section-body { padding: 22px; }

    .form-label {
        margin-bottom: 7px;
        color: #3c5144;
        font-size: 12px;
        font-weight: 700;
    }

    .form-control, .form-select {
        min-height: 42px;
        border: 1px solid #dce7df;
        border-radius: 9px;
        background: #fcfefd;
        color: #2b3c31;
        font-size: 13px;
        box-shadow: none !important;
        transition: .2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #65b889;
        background: #fff;
        box-shadow: 0 0 0 4px rgba(21,128,61,.08) !important;
    }

    textarea.form-control {
        min-height: 105px;
        line-height: 1.7;
    }

    .sp-upload {
        padding: 18px;
        border: 1px dashed #b9d9c5;
        border-radius: 13px;
        background: #f7fcf8;
    }

    .sp-preview {
        display: block;
        max-width: 170px;
        max-height: 115px;
        margin-top: 10px;
        border: 1px solid var(--sp-border);
        border-radius: 10px;
        padding: 4px;
        background: #fff;
        object-fit: contain;
    }

    .sp-action {
        border: 0;
        border-radius: 10px;
        padding: 11px 19px;
        color: #fff;
        background: linear-gradient(135deg, #199653, #166534);
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 6px 15px rgba(21,128,61,.17);
        transition: .2s ease;
    }

    .sp-action:hover {
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 9px 20px rgba(21,128,61,.23);
    }

    .sp-feature-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 20px;
    }

    .sp-feature-head h4 {
        margin: 0;
        color: var(--sp-ink);
        font-size: 21px;
        font-weight: 800;
    }

    .sp-feature-head p {
        margin: 5px 0 0;
        color: var(--sp-muted);
        font-size: 12px;
    }

    .sp-item {
        height: 100%;
        border: 1px solid var(--sp-border);
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(22,101,52,.035);
        transition: .25s ease;
    }

    .sp-item:hover {
        transform: translateY(-3px);
        border-color: #b9dfc8;
        box-shadow: 0 12px 25px rgba(22,101,52,.08);
    }

    .sp-item-body { padding: 19px; }

    .sp-item-title {
        color: #213b2b;
        font-size: 14px;
        font-weight: 800;
    }

    .sp-item-meta {
        margin-top: 5px;
        color: #819087;
        font-size: 11px;
    }

    .sp-item-description {
        margin: 10px 0 0;
        color: #607066;
        font-size: 12px;
        line-height: 1.7;
    }

    .sp-empty {
        padding: 42px 20px;
        border: 1px dashed #cfe2d5;
        border-radius: 14px;
        text-align: center;
        background: #fbfefc;
        color: var(--sp-muted);
        font-size: 13px;
    }

    .sp-empty i {
        display: block;
        margin-bottom: 10px;
        color: #9ac9aa;
        font-size: 30px;
    }

    .sp-gallery-image {
        width: 100%;
        height: 190px;
        object-fit: cover;
        border-radius: 14px 14px 0 0;
    }

    .modal-content {
        overflow: hidden;
        border: 1px solid var(--sp-border);
        border-radius: 17px;
        box-shadow: 0 20px 65px rgba(14,52,31,.18);
    }

    .modal-header {
        padding: 18px 22px;
        border-bottom: 1px solid var(--sp-border);
        background: linear-gradient(135deg, #f2faf5, #fff);
    }

    .modal-title, .modal-header h5 {
        color: var(--sp-ink);
        font-size: 16px;
        font-weight: 800;
    }

    .modal-body { padding: 22px; }
    .modal-footer { border-top: 1px solid var(--sp-border); background: #fbfdfb; }

    @keyframes spFade {
        from { opacity: 0; transform: translateY(7px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 768px) {
        .school-profile-page { padding: 18px 12px 35px; }
        .sp-header { padding: 23px 22px; }
        .sp-header h1 { font-size: 23px; }
        .sp-panel { padding: 16px; }
        .sp-section-body { padding: 17px; }
        .sp-feature-head { align-items: flex-start; flex-direction: column; }
        .sp-tabs { overflow-x: auto; flex-wrap: nowrap; }
        .sp-tabs .nav-link { white-space: nowrap; }
    }
</style>

<div class="school-profile-page">
    <div class="sp-header">
        <h1>Profil Sekolah</h1>
        <p>Kelola informasi dan seluruh konten yang ditampilkan pada landing page sekolah.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <ul class="nav sp-tabs" role="tablist">
        @foreach([
            'informasi' => ['label' => 'Informasi Sekolah', 'icon' => 'bi-building'],
            'prestasi' => ['label' => 'Prestasi', 'icon' => 'bi-trophy'],
            'ekskul' => ['label' => 'Ekstrakurikuler', 'icon' => 'bi-people'],
            'berita' => ['label' => 'Berita', 'icon' => 'bi-newspaper'],
            'galeri' => ['label' => 'Galeri', 'icon' => 'bi-images']
        ] as $id => $tab)
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                        data-bs-toggle="tab"
                        data-bs-target="#tab-{{ $id }}"
                        type="button"
                        role="tab">
                    <i class="bi {{ $tab['icon'] }}"></i>
                    {{ $tab['label'] }}
                </button>
            </li>
        @endforeach
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="tab-informasi" role="tabpanel">
            <div class="sp-panel">
                <form action="{{ route('school-profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="sp-section">
                        <div class="sp-section-head">
                            <i class="bi bi-building"></i>
                            <div>
                                <h5>Identitas dan Kontak</h5>
                                <small>Informasi utama sekolah dan kontak yang dapat ditampilkan kepada pengunjung.</small>
                            </div>
                        </div>
                        <div class="sp-section-body row g-3">
                            @foreach([
                                ['name', 'Nama Sekolah', 'text'],
                                ['npsn', 'NPSN', 'text'],
                                ['nss', 'NSS', 'text'],
                                ['phone', 'Telepon', 'text'],
                                ['email', 'Email', 'email'],
                                ['website', 'Website', 'text']
                            ] as $field)
                                <div class="col-md-6">
                                    <label class="form-label">{{ $field[1] }}</label>
                                    <input class="form-control"
                                           type="{{ $field[2] }}"
                                           name="{{ $field[0] }}"
                                           value="{{ old($field[0], $school->{$field[0]}) }}">
                                </div>
                            @endforeach

                            <div class="col-12">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="address" rows="3">{{ old('address', $school->address) }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Deskripsi Sekolah</label>
                                <textarea class="form-control" name="description" rows="4">{{ old('description', $school->description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="sp-section">
                        <div class="sp-section-head">
                            <i class="bi bi-stars"></i>
                            <div>
                                <h5>Hero / Beranda</h5>
                                <small>Konten utama yang muncul pada bagian pembuka landing page.</small>
                            </div>
                        </div>
                        <div class="sp-section-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Judul Hero</label>
                                <input class="form-control" name="hero_title" value="{{ old('hero_title', $school->hero_title) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Foto Hero</label>
                                <div class="sp-upload">
                                    <input class="form-control" type="file" name="school_image" accept="image/*">
                                    @if($school->school_image)
                                        <img class="sp-preview" src="{{ asset('storage/'.$school->school_image) }}" alt="Foto hero">
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Deskripsi Hero</label>
                                <textarea class="form-control" name="hero_description" rows="3">{{ old('hero_description', $school->hero_description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="sp-section">
                        <div class="sp-section-head">
                            <i class="bi bi-compass"></i>
                            <div>
                                <h5>Visi dan Misi</h5>
                                <small>Landasan dan arah pengembangan sekolah.</small>
                            </div>
                        </div>
                        <div class="sp-section-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Visi</label>
                                <textarea class="form-control" name="vision" rows="10">{{ old('vision', $school->vision) }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Misi</label>
                                <textarea class="form-control" name="mission" rows="10">{{ old('mission', $school->mission) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="sp-section">
                        <div class="sp-section-head">
                            <i class="bi bi-images"></i>
                            <div>
                                <h5>Media dan Struktur Organisasi</h5>
                                <small>Kelola logo, cover, dan informasi struktur organisasi sekolah.</small>
                            </div>
                        </div>
                        <div class="sp-section-body row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Logo Sekolah</label>
                                <div class="sp-upload">
                                    <input class="form-control" type="file" name="logo" accept="image/*">
                                    @if($school->logo)
                                        <img class="sp-preview" src="{{ asset('storage/'.$school->logo) }}" alt="Logo sekolah">
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Cover Sekolah</label>
                                <div class="sp-upload">
                                    <input class="form-control" type="file" name="cover_image" accept="image/*">
                                    @if($school->cover_image)
                                        <img class="sp-preview" src="{{ asset('storage/'.$school->cover_image) }}" alt="Cover sekolah">
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Judul Struktur</label>
                                <input class="form-control" name="organization_title" value="{{ old('organization_title', $school->organization_title) }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Gambar Struktur</label>
                                <div class="sp-upload">
                                    <input class="form-control" type="file" name="organization_image" accept="image/*">
                                    @if($school->organization_image)
                                        <img class="sp-preview" src="{{ asset('storage/'.$school->organization_image) }}" alt="Struktur organisasi">
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Deskripsi Struktur</label>
                                <textarea class="form-control" name="organization_description" rows="3">{{ old('organization_description', $school->organization_description) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button class="sp-action" type="submit">
                            <i class="bi bi-check2-circle me-2"></i>
                            Simpan Informasi Sekolah
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="tab-pane fade" id="tab-prestasi" role="tabpanel">
            <div class="sp-panel">
                <div class="sp-feature-head">
                    <div>
                        <h4>Prestasi Sekolah</h4>
                        <p>Kelola pencapaian dan prestasi yang akan ditampilkan pada landing page.</p>
                    </div>
                    <button class="sp-action" data-bs-toggle="modal" data-bs-target="#addAchievement">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Prestasi
                    </button>
                </div>

                @forelse($achievements as $item)
                    <div class="sp-item mb-3">
                        <div class="sp-item-body d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div class="flex-grow-1">
                                <div class="sp-item-title">{{ $item->title }}</div>
                                <div class="sp-item-meta">
                                    {{ $item->year ?: 'Tahun tidak diisi' }}
                                    @if($item->level) · {{ $item->level }} @endif
                                </div>
                                @if($item->description)
                                    <p class="sp-item-description">{{ $item->description }}</p>
                                @endif
                                @if($item->image)
                                    <img class="sp-preview" src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}">
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#editAchievement{{ $item->id }}">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('school-achievements.destroy', $item) }}" onsubmit="return confirm('Hapus prestasi ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="sp-empty">
                        <i class="bi bi-trophy"></i>
                        Belum ada data prestasi. Klik tombol Tambah Prestasi untuk menambahkan data.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="tab-pane fade" id="tab-ekskul" role="tabpanel">
            <div class="sp-panel">
                <div class="sp-feature-head">
                    <div>
                        <h4>Ekstrakurikuler</h4>
                        <p>Kelola kegiatan pengembangan minat dan bakat siswa.</p>
                    </div>
                    <button class="sp-action" data-bs-toggle="modal" data-bs-target="#addEkskul">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Ekskul
                    </button>
                </div>

                @forelse($extracurriculars as $item)
                    <div class="sp-item mb-3">
                        <div class="sp-item-body d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div class="d-flex align-items-start gap-3 flex-grow-1">
                                @if($item->image)
                                    <img
                                        src="{{ asset('storage/' . $item->image) }}"
                                        alt="{{ $item->name }}"
                                        class="rounded-3 border flex-shrink-0"
                                        style="width: 92px; height: 76px; object-fit: cover;"
                                    >
                                @else
                                    <div
                                        class="rounded-3 border d-flex align-items-center justify-content-center flex-shrink-0"
                                        style="width: 92px; height: 76px; background: #f4faf7; color: #198754;"
                                    >
                                        <i class="bi bi-image fs-4"></i>
                                    </div>
                                @endif

                                <div>
                                    <div class="sp-item-title">{{ $item->name }}</div>
                                    <div class="sp-item-meta">
                                        {{ $item->is_active ? 'Aktif' : 'Tidak aktif' }}
                                    </div>
                                    @if($item->description)
                                        <p class="sp-item-description">{{ $item->description }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#editEkskul{{ $item->id }}">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('extracurriculars.destroy', $item) }}" onsubmit="return confirm('Hapus ekstrakurikuler ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="sp-empty">
                        <i class="bi bi-people"></i>
                        Belum ada data ekstrakurikuler.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="tab-pane fade" id="tab-berita" role="tabpanel">
            <div class="sp-panel">
                <div class="sp-feature-head">
                    <div>
                        <h4>Berita Sekolah</h4>
                        <p>Kelola berita dan informasi terbaru sekolah.</p>
                    </div>
                    <button class="sp-action" data-bs-toggle="modal" data-bs-target="#addNews">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Berita
                    </button>
                </div>

                @forelse($newsItems as $item)
                    <div class="sp-item mb-3">
                        <div class="sp-item-body d-flex flex-wrap justify-content-between align-items-start gap-3">
                            <div class="flex-grow-1">
                                <div class="sp-item-title">{{ $item->title }}</div>
                                <div class="sp-item-meta">
                                    {{ optional($item->published_at)->format('d/m/Y') ?: 'Tanggal belum diisi' }}
                                    · {{ $item->is_published ? 'Dipublikasikan' : 'Draft' }}
                                </div>
                                @if($item->summary)
                                    <p class="sp-item-description">{{ $item->summary }}</p>
                                @endif
                                @if($item->image)
                                    <img class="sp-preview" src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}">
                                @endif
                            </div>

                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#editNews{{ $item->id }}">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </button>
                                <form method="POST" action="{{ route('school-news.destroy', $item) }}" onsubmit="return confirm('Hapus berita ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="sp-empty">
                        <i class="bi bi-newspaper"></i>
                        Belum ada berita sekolah.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="tab-pane fade" id="tab-galeri" role="tabpanel">
            <div class="sp-panel">
                <div class="sp-feature-head">
                    <div>
                        <h4>Galeri Sekolah</h4>
                        <p>Kelola dokumentasi foto kegiatan dan lingkungan sekolah.</p>
                    </div>
                    <button class="sp-action" data-bs-toggle="modal" data-bs-target="#addGallery">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Galeri
                    </button>
                </div>

                @if($galleries->count())
                    <div class="row g-4">
                        @foreach($galleries as $item)
                            <div class="col-md-6 col-xl-4">
                                <div class="sp-item overflow-hidden">
                                    @if($item->image)
                                        <img class="sp-gallery-image" src="{{ asset('storage/'.$item->image) }}" alt="{{ $item->title }}">
                                    @endif

                                    <div class="sp-item-body">
                                        <div class="sp-item-title">{{ $item->title }}</div>
                                        <p class="sp-item-description">{{ $item->description }}</p>

                                        <div class="d-flex gap-2 mt-3">
                                            <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#editGallery{{ $item->id }}">
                                                <i class="bi bi-pencil me-1"></i> Edit
                                            </button>
                                            <form method="POST" action="{{ route('school-galleries.destroy', $item) }}" onsubmit="return confirm('Hapus galeri ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger">
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
                        Belum ada foto galeri sekolah.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@include('admin.school-profile.modals')
@endsection
