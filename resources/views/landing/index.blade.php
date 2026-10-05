@extends('layouts.landing')

@section('title', 'Beranda')

@section('content')

{{-- =========================================================
    HERO / BERANDA SECTION
========================================================= --}}
<section class="landing-section" id="beranda" style="padding-top: 70px; padding-bottom: 85px; background: radial-gradient(circle at 10% 20%, rgba(240, 253, 244, 0.85) 0%, rgba(255, 255, 255, 1) 90%);">
    <div class="container">
        <div class="row align-items-center g-5">
            {{-- Left Content --}}
            <div class="col-lg-6 reveal-left">
                <span class="section-badge">
                    <i class="bi bi-mortarboard-fill"></i>
                    Portal Resmi BIDUK
                </span>

                <h1 class="hero-title" style="font-size: clamp(2.2rem, 3.8vw, 3.3rem); font-weight: 800; line-height: 1.18; color: #0f172a; margin: 1rem 0 1.25rem;">
                    {{ $school->hero_title ?? ('Selamat Datang di ' . ($school->name ?? 'SDN 204 Palembang')) }}
                </h1>

                <p class="hero-description" style="font-size: 1.075rem; color: #475569; line-height: 1.8; margin-bottom: 2rem;">
                    {{ $school->hero_description ?? ($school->description ? Str::limit($school->description, 210) : 'Sistem Informasi Buku Induk dan Pusat Informasi Resmi SD Negeri 204 Palembang dalam mendukung tata kelola pendidikan yang unggul, transparan, dan terpercaya.') }}
                </p>

                <div class="d-flex align-items-center flex-wrap gap-3 mb-4">
                    <a href="#informasi-sekolah" class="btn btn-biduk-primary btn-lg px-4 py-3">
                        <i class="bi bi-info-circle"></i>
                        Kenali Sekolah
                    </a>
                    <a href="#berita" class="btn btn-biduk-outline btn-lg px-4 py-3">
                        <i class="bi bi-newspaper"></i>
                        Berita Terkini
                    </a>
                </div>

                {{-- Fast Facts Pill Row --}}
                <div class="pt-3 border-top d-flex flex-wrap align-items-center gap-3 text-secondary" style="font-size: 0.875rem;">
                    <div class="d-flex align-items-center gap-1.5">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>NPSN: <strong>{{ $school->npsn ?? '10604338' }}</strong></span>
                    </div>
                    <span class="text-muted">•</span>
                    <div class="d-flex align-items-center gap-1.5">
                        <i class="bi bi-patch-check-fill text-success"></i>
                        <span>Status <strong>Negeri</strong></span>
                    </div>
                    <span class="text-muted">•</span>
                    <div class="d-flex align-items-center gap-1.5">
                        <i class="bi bi-geo-alt-fill text-success"></i>
                        <span>Kota Palembang</span>
                    </div>
                </div>
            </div>

            {{-- Right Photo / Hero Media --}}
            <div class="col-lg-6 reveal-right">
                @php
                    $heroImage = $school->school_image_url ?? $school->cover_image_url ?? null;
                @endphp

                @if($heroImage)
                    <div class="position-relative">
                        <div class="p-2 bg-white rounded-4 shadow border overflow-hidden" style="transition: transform 0.4s ease, box-shadow 0.4s ease;">
                            <img src="{{ $heroImage }}"
                                 alt="{{ $school->name ?? 'Foto Sekolah' }}"
                                 class="img-fluid rounded-3 w-100"
                                 style="max-height: 420px; object-fit: cover; display: block;">
                        </div>
                    </div>
                @else
                    {{-- Decorative Fallback Hero Card --}}
                    <div class="p-4 p-md-5 rounded-4 shadow-sm border text-center text-md-start position-relative overflow-hidden"
                         style="background: linear-gradient(135deg, #ffffff 0%, #f0fdf4 100%); border-color: #bbf7d0 !important; min-height: 380px; display: flex; flex-direction: column; justify-content: center;">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-3 mb-4"
                             style="width: 64px; height: 64px; background: var(--biduk-primary); color: #fff; font-size: 1.85rem;">
                            <i class="bi bi-building"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-2">{{ $school->name ?? 'SD Negeri 204 Palembang' }}</h3>
                        <p class="text-muted mb-4" style="font-size: 0.95rem; line-height: 1.6;">
                            {{ $school->address ?? 'Kota Palembang, Sumatera Selatan' }}
                        </p>
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="bg-white p-3 rounded-3 border shadow-sm">
                                    <div class="fw-bold text-success fs-5">{{ $achievements->count() }}</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Prestasi</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-white p-3 rounded-3 border shadow-sm">
                                    <div class="fw-bold text-success fs-5">{{ $extracurriculars->count() }}</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Ekskul</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-white p-3 rounded-3 border shadow-sm">
                                    <div class="fw-bold text-success fs-5">{{ $newsItems->count() }}</div>
                                    <small class="text-muted" style="font-size: 0.75rem;">Berita</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Quick Highlights Bar --}}
        <div class="row g-3 mt-4 pt-3">
            <div class="col-6 col-md-3 reveal delay-100">
                <div class="p-3 bg-white rounded-3 border shadow-sm d-flex align-items-center gap-3 h-100">
                    <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 44px; height: 44px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                        <i class="bi bi-mortarboard fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Jenjang</small>
                        <strong class="text-dark d-block">Sekolah Dasar</strong>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3 reveal delay-200">
                <div class="p-3 bg-white rounded-3 border shadow-sm d-flex align-items-center gap-3 h-100">
                    <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 44px; height: 44px; background: #fef3c7; color: #d97706;">
                        <i class="bi bi-trophy fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Prestasi Terdata</small>
                        <strong class="text-dark d-block">{{ $achievements->count() }} Pencapaian</strong>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3 reveal delay-300">
                <div class="p-3 bg-white rounded-3 border shadow-sm d-flex align-items-center gap-3 h-100">
                    <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 44px; height: 44px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                        <i class="bi bi-people fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Ekstrakurikuler</small>
                        <strong class="text-dark d-block">{{ $extracurriculars->count() }} Pilihan</strong>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3 reveal delay-400">
                <div class="p-3 bg-white rounded-3 border shadow-sm d-flex align-items-center gap-3 h-100">
                    <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                         style="width: 44px; height: 44px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                        <i class="bi bi-images fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block" style="font-size: 0.75rem;">Dokumentasi</small>
                        <strong class="text-dark d-block">{{ $galleries->count() }} Foto Kegiatan</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- =========================================================
    INFORMASI & IDENTITAS SEKOLAH SECTION
========================================================= --}}
<section class="landing-section" id="informasi-sekolah">
    <div class="container">
        {{-- Section Header --}}
        <div class="section-header reveal">
            <span class="section-badge">
                <i class="bi bi-building"></i> Identitas Sekolah
            </span>
            <h2 class="section-title">Profil & Informasi Resmi</h2>
            <p class="section-description">
                Mengenal lebih dekat profil lembaga, legalitas, serta saluran komunikasi resmi sekolah.
            </p>
        </div>

        <div class="row align-items-center g-5">
            {{-- Foto Gedung / Profil Cover --}}
            <div class="col-lg-5 reveal-left">
                @php
                    $infoImage = $school->cover_image_url ?? $school->school_image_url ?? null;
                @endphp

                @if($infoImage)
                    <div class="position-relative overflow-hidden rounded-4 border shadow-sm">
                        <img src="{{ $infoImage }}"
                             alt="{{ $school->name ?? 'Gedung Sekolah' }}"
                             class="img-fluid w-100"
                             style="max-height: 420px; object-fit: cover;">
                    </div>
                @else
                    <div class="p-5 rounded-4 border text-center" style="background: var(--biduk-primary-light); min-height: 340px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <i class="bi bi-building text-success" style="font-size: 3.5rem;"></i>
                        <h5 class="fw-bold text-dark mt-3 mb-1">{{ $school->name ?? 'SDN 204 Palembang' }}</h5>
                        <p class="text-muted small mb-0">{{ $school->address ?? 'Kota Palembang' }}</p>
                    </div>
                @endif
            </div>

            {{-- Detail Profil & Data Kontak --}}
            <div class="col-lg-7 reveal-right">
                <div class="d-inline-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5">
                        <i class="bi bi-check-circle-fill me-1"></i> Terakreditasi
                    </span>
                    <span class="badge bg-light text-dark border px-2.5 py-1.5">
                        Jenjang SD
                    </span>
                </div>

                <h3 class="fw-bold text-dark mb-3" style="font-size: 1.85rem;">
                    {{ $school->name ?? 'SD Negeri 204 Palembang' }}
                </h3>

                <p class="text-secondary mb-4" style="line-height: 1.8; font-size: 1rem;">
                    {{ $school->description ?? 'SD Negeri 204 Palembang merupakan satuan pendidikan dasar yang berkomitmen menyediakan layanan pendidikan berkualitas bagi peserta didik serta mendukung perkembangan akademik dan karakter siswa yang unggul, berakhlak mulia, dan berprestasi.' }}
                </p>

                <div class="row g-3">
                    {{-- NPSN --}}
                    <div class="col-sm-6">
                        <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100">
                            <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 42px; height: 42px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                                <i class="bi bi-card-text fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">NPSN</small>
                                <strong class="text-dark d-block">{{ $school->npsn ?? '10604338' }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- NSS --}}
                    @if($school->nss)
                        <div class="col-sm-6">
                            <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100">
                                <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width: 42px; height: 42px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                                    <i class="bi bi-upc-scan fs-5"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">NSS</small>
                                    <strong class="text-dark d-block">{{ $school->nss }}</strong>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Telepon --}}
                    <div class="col-sm-6">
                        <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100">
                            <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 42px; height: 42px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                                <i class="bi bi-telephone fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Telepon</small>
                                <strong class="text-dark d-block">{{ $school->phone ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="col-sm-6">
                        <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100">
                            <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 42px; height: 42px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                                <i class="bi bi-envelope fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Email Resmi</small>
                                <strong class="text-dark d-block text-truncate">{{ $school->email ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Website --}}
                    @if($school->website)
                        <div class="col-sm-6">
                            <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100">
                                <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width: 42px; height: 42px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                                    <i class="bi bi-globe fs-5"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Website</small>
                                    <strong class="text-dark d-block text-truncate">{{ $school->website }}</strong>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Alamat --}}
                    <div class="col-12">
                        <div class="p-3 bg-white rounded-3 border d-flex align-items-start gap-3">
                            <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0 mt-1"
                                 style="width: 42px; height: 42px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                                <i class="bi bi-geo-alt fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Alamat Sekolah</small>
                                <strong class="text-dark d-block" style="line-height: 1.55;">{{ $school->address ?? 'Kota Palembang, Sumatera Selatan' }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- =========================================================
    VISI & MISI SECTION
========================================================= --}}
<section class="landing-section section-light" id="visi-misi">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">
                <i class="bi bi-bullseye"></i> Landasan Sekolah
            </span>
            <h2 class="section-title">Visi & Misi</h2>
            <p class="section-description">
                Arah dan komitmen bersama dalam mewujudkan lingkungan belajar yang unggul, berakhlak, dan berprestasi.
            </p>
        </div>

        <div class="row g-4">
            {{-- Visi --}}
            <div class="col-lg-6 reveal-left">
                <div class="biduk-feature-card h-100 position-relative overflow-hidden" style="border-top: 5px solid var(--biduk-primary);">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center"
                             style="width: 52px; height: 52px; background: var(--biduk-primary-light); color: var(--biduk-primary); font-size: 1.5rem;">
                            <i class="bi bi-eye-fill"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0">Visi Sekolah</h4>
                            <small class="text-muted">Tujuan & Cita-Cita Utama</small>
                        </div>
                    </div>

                    <div class="text-secondary" style="line-height: 1.85; font-size: 1rem; white-space: pre-line;">
                        @if(!empty(trim($school->vision)))
                            {{ $school->vision }}
                        @else
                            "Terwujudnya peserta didik yang beriman dan bertakwa kepada Tuhan Yang Maha Esa, berakhlak mulia, cerdas, terampil, mandiri, dan berwawasan lingkungan."
                        @endif
                    </div>
                </div>
            </div>

            {{-- Misi --}}
            <div class="col-lg-6 reveal-right">
                <div class="biduk-feature-card h-100 position-relative overflow-hidden" style="border-top: 5px solid var(--biduk-secondary);">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center"
                             style="width: 52px; height: 52px; background: var(--biduk-primary-light); color: var(--biduk-primary); font-size: 1.5rem;">
                            <i class="bi bi-list-check"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0">Misi Sekolah</h4>
                            <small class="text-muted">Langkah Strategis & Implementasi</small>
                        </div>
                    </div>

                    <div class="text-secondary" style="line-height: 1.85; font-size: 0.95rem; white-space: pre-line;">
                        @if(!empty(trim($school->mission)))
                            {{ $school->mission }}
                        @else
                            1. Menyelenggarakan proses pembelajaran yang aktif, inovatif, kreatif, efektif, dan menyenangkan.
                            2. Menumbuhkan penghayatan terhadap ajaran agama serta budaya bangsa.
                            3. Meningkatkan prestasi akademik dan non-akademik siswa secara berkelanjutan.
                            4. Menciptakan lingkungan sekolah yang bersih, asri, aman, dan berkarakter.
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


{{-- =========================================================
    STRUKTUR ORGANISASI SECTION
========================================================= --}}
<section class="landing-section" id="struktur-organisasi">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">
                <i class="bi bi-diagram-3"></i> Tata Kelola
            </span>
            <h2 class="section-title">{{ $school->organization_title ?? 'Struktur Organisasi Sekolah' }}</h2>
            <p class="section-description">
                {{ $school->organization_description ?? ('Bagan kepengurusan dan manajemen dalam mendukung tata kelola pendidikan di ' . ($school->name ?? 'SDN 204 Palembang') . '.') }}
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10 reveal-scale">
                @if($school->organization_image_url)
                    <div class="p-3 bg-white rounded-4 shadow-sm border text-center position-relative">
                        <img src="{{ $school->organization_image_url }}"
                             alt="Struktur Organisasi {{ $school->name ?? 'Sekolah' }}"
                             class="img-fluid rounded-3 w-100"
                             style="max-height: 560px; object-fit: contain;">

                        <div class="mt-3 d-flex justify-content-center gap-2">
                            <button class="btn btn-biduk-outline btn-sm" data-bs-toggle="modal" data-bs-target="#modalOrgImage">
                                <i class="bi bi-arrows-fullscreen me-1"></i> Perbesar Gambar
                            </button>
                            <a href="{{ $school->organization_image_url }}" target="_blank" download class="btn btn-biduk-primary btn-sm">
                                <i class="bi bi-download me-1"></i> Unduh Bagan
                            </a>
                        </div>
                    </div>

                    {{-- Modal Lightbox Struktur Organisasi --}}
                    <div class="modal fade" id="modalOrgImage" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-dark">{{ $school->organization_title ?? 'Struktur Organisasi' }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center p-3">
                                    <img src="{{ $school->organization_image_url }}" alt="Struktur Organisasi" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-5 bg-white rounded-4 border text-center shadow-sm">
                        <div class="rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 64px; height: 64px; background: var(--biduk-primary-light); color: var(--biduk-primary); font-size: 1.85rem;">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Bagan Struktur Organisasi</h5>
                        <p class="text-muted mx-auto mb-0" style="max-width: 520px; font-size: 0.95rem; line-height: 1.6;">
                            Bagan struktur kepengurusan dan manajemen SD Negeri 204 Palembang dapat diperbarui oleh administrator melalui menu Profil Sekolah.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>


{{-- =========================================================
    PRESTASI SEKOLAH SECTION
========================================================= --}}
<section class="landing-section section-light" id="prestasi">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">
                <i class="bi bi-trophy"></i> Prestasi & Pencapaian
            </span>
            <h2 class="section-title">Prestasi Siswa & Sekolah</h2>
            <p class="section-description">
                Berbagai pencapaian membanggakan yang diraih peserta didik dan institusi dalam bidang akademik maupun non-akademik.
            </p>
        </div>

        @if($achievements->count())
            <div class="row g-4">
                @foreach($achievements as $index => $item)
                    <div class="col-md-6 col-lg-4 reveal delay-{{ (($index % 3) + 1) * 100 }}">
                        <div class="biduk-feature-card d-flex flex-column h-100 p-0 overflow-hidden">
                            @if($item->image && file_exists(public_path('storage/' . $item->image)))
                                <img src="{{ asset('storage/' . $item->image) }}"
                                     alt="{{ $item->title }}"
                                     class="w-100"
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="w-100 d-flex align-items-center justify-content-center"
                                     style="height: 180px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706;">
                                    <i class="bi bi-trophy-fill" style="font-size: 3.25rem;"></i>
                                </div>
                            @endif

                            <div class="p-4 d-flex flex-column flex-grow-1 justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-calendar3 me-1"></i>{{ $item->year ?: 'Tahun -' }}
                                        </span>
                                        @if($item->level)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">
                                                Tingkat {{ $item->level }}
                                            </span>
                                        @endif
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2" style="line-height: 1.4;">{{ $item->title }}</h5>
                                    @if($item->description)
                                        <p class="text-secondary small mb-0" style="line-height: 1.65;">
                                            {{ $item->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm p-4 reveal">
                <i class="bi bi-trophy text-muted opacity-50" style="font-size: 3.5rem;"></i>
                <h5 class="fw-bold text-muted mt-3 mb-1">Belum Ada Data Prestasi</h5>
                <p class="text-muted small mb-0">Daftar prestasi sekolah dan siswa akan ditampilkan di sini.</p>
            </div>
        @endif
    </div>
</section>


{{-- =========================================================
    EKSTRAKURIKULER SECTION
========================================================= --}}
<section class="landing-section" id="ekstrakurikuler">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">
                <i class="bi bi-people"></i> Pengembangan Minat & Bakat
            </span>
            <h2 class="section-title">Kegiatan Ekstrakurikuler</h2>
            <p class="section-description">
                Program pembinaan potensi minat, kreativitas, seni, olahraga, dan kepemimpinan di luar jam belajar formal.
            </p>
        </div>

        @if($extracurriculars->count())
            <div class="row g-4">
                @foreach($extracurriculars as $index => $item)
                    <div class="col-md-6 col-lg-4 reveal delay-{{ (($index % 3) + 1) * 100 }}">
                        <div class="biduk-feature-card d-flex flex-column h-100 p-0 overflow-hidden">
                            @if($item->image && file_exists(public_path('storage/' . $item->image)))
                                <img src="{{ asset('storage/' . $item->image) }}"
                                     alt="{{ $item->name }}"
                                     class="w-100"
                                     style="height: 190px; object-fit: cover;">
                            @else
                                <div class="w-100 d-flex align-items-center justify-content-center"
                                     style="height: 180px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                                    <i class="bi bi-people-fill" style="font-size: 3.25rem;"></i>
                                </div>
                            @endif

                            <div class="p-4 d-flex flex-column flex-grow-1 justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h5 class="fw-bold text-dark mb-0">{{ $item->name }}</h5>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                                    </div>
                                    <p class="text-secondary small mb-0" style="line-height: 1.65;">
                                        {{ $item->description ?? 'Kegiatan pembinaan minat dan bakat siswa di sekolah.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm p-4 reveal">
                <i class="bi bi-people text-muted opacity-50" style="font-size: 3.5rem;"></i>
                <h5 class="fw-bold text-muted mt-3 mb-1">Belum Ada Data Ekstrakurikuler</h5>
                <p class="text-muted small mb-0">Daftar kegiatan ekstrakurikuler akan ditampilkan di sini.</p>
            </div>
        @endif
    </div>
</section>


{{-- =========================================================
    BERITA SEKOLAH SECTION
========================================================= --}}
<section class="landing-section section-light" id="berita">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">
                <i class="bi bi-newspaper"></i> Informasi Terkini
            </span>
            <h2 class="section-title">Berita & Artikel Sekolah</h2>
            <p class="section-description">
                Informasi dan kabar kegiatan terbaru seputar agenda pembelajaran, upacara, dan pengumuman resmi.
            </p>
        </div>

        @if($newsItems->count())
            <div class="row g-4">
                @foreach($newsItems as $index => $item)
                    <div class="col-md-6 col-lg-4 reveal delay-{{ (($index % 3) + 1) * 100 }}">
                        <article class="biduk-feature-card d-flex flex-column h-100 p-0 overflow-hidden">
                            @if($item->image && file_exists(public_path('storage/' . $item->image)))
                                <img src="{{ asset('storage/' . $item->image) }}"
                                     alt="{{ $item->title }}"
                                     class="w-100"
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="w-100 d-flex align-items-center justify-content-center"
                                     style="height: 190px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                                    <i class="bi bi-newspaper" style="font-size: 3.25rem;"></i>
                                </div>
                            @endif

                            <div class="p-4 d-flex flex-column flex-grow-1 justify-content-between">
                                <div>
                                    <small class="text-muted d-block mb-2">
                                        <i class="bi bi-calendar3 text-success me-1"></i>
                                        {{ optional($item->published_at)->translatedFormat('d F Y') ?: 'Tanggal tidak tercantum' }}
                                    </small>
                                    <h5 class="fw-bold text-dark mb-2" style="line-height: 1.4;">
                                        {{ $item->title }}
                                    </h5>
                                    <p class="text-secondary small mb-3" style="line-height: 1.65;">
                                        {{ $item->summary ? Str::limit($item->summary, 120) : ($item->content ? Str::limit(strip_tags($item->content), 120) : 'Informasi kegiatan sekolah...') }}
                                    </p>
                                </div>

                                <div>
                                    <button class="btn btn-link text-success p-0 fw-semibold text-decoration-none d-inline-flex align-items-center gap-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#newsModal{{ $item->id }}">
                                        <span>Baca Selengkapnya</span>
                                        <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>

                    {{-- Modal Detail Berita --}}
                    <div class="modal fade" id="newsModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-bottom">
                                    <div>
                                        <small class="text-muted d-block">
                                            <i class="bi bi-calendar3 text-success me-1"></i>
                                            {{ optional($item->published_at)->translatedFormat('d F Y') }}
                                        </small>
                                        <h5 class="modal-title fw-bold text-dark mt-1">{{ $item->title }}</h5>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body p-4">
                                    @if($item->image && file_exists(public_path('storage/' . $item->image)))
                                        <img src="{{ asset('storage/' . $item->image) }}"
                                             alt="{{ $item->title }}"
                                             class="img-fluid rounded-3 mb-4 w-100"
                                             style="max-height: 380px; object-fit: cover;">
                                    @endif

                                    @if($item->summary)
                                        <div class="lead text-secondary mb-3 fs-6 fw-semibold">
                                            {{ $item->summary }}
                                        </div>
                                    @endif

                                    <div class="text-dark" style="line-height: 1.85; font-size: 0.95rem; white-space: pre-line;">
                                        {{ $item->content ?? $item->summary }}
                                    </div>
                                </div>
                                <div class="modal-footer border-top bg-light rounded-bottom-4">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm p-4 reveal">
                <i class="bi bi-newspaper text-muted opacity-50" style="font-size: 3.5rem;"></i>
                <h5 class="fw-bold text-muted mt-3 mb-1">Belum Ada Berita Dipublikasikan</h5>
                <p class="text-muted small mb-0">Kabar berita dan agenda sekolah akan diinformasikan di sini.</p>
            </div>
        @endif
    </div>
</section>


{{-- =========================================================
    GALERI SEKOLAH SECTION
========================================================= --}}
<section class="landing-section" id="galeri">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-badge">
                <i class="bi bi-images"></i> Dokumentasi
            </span>
            <h2 class="section-title">Galeri Foto & Aktivitas</h2>
            <p class="section-description">
                Dokumentasi momen kegiatan belajar, upacara, perlombaan, dan fasilitas lingkungan sekolah.
            </p>
        </div>

        @if($galleries->count())
            <div class="row g-4">
                @foreach($galleries as $index => $item)
                    <div class="col-md-6 col-lg-4 reveal delay-{{ (($index % 3) + 1) * 100 }}">
                        <div class="biduk-feature-card d-flex flex-column h-100 p-0 overflow-hidden position-relative">
                            @if($item->image && file_exists(public_path('storage/' . $item->image)))
                                <div class="position-relative overflow-hidden" style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#galleryModal{{ $item->id }}">
                                    <img src="{{ asset('storage/' . $item->image) }}"
                                         alt="{{ $item->title }}"
                                         class="w-100"
                                         style="height: 230px; object-fit: cover; transition: transform 0.4s ease;">
                                    <div class="position-absolute top-0 end-0 m-3">
                                        <span class="badge bg-dark bg-opacity-75 p-2 rounded-circle">
                                            <i class="bi bi-zoom-in text-white"></i>
                                        </span>
                                    </div>
                                </div>
                            @else
                                <div class="w-100 d-flex align-items-center justify-content-center"
                                     style="height: 220px; background: #f1f5f9; color: #64748b;">
                                    <i class="bi bi-images" style="font-size: 3.25rem;"></i>
                                </div>
                            @endif

                            <div class="p-3 bg-white">
                                <h6 class="fw-bold text-dark mb-1">{{ $item->title }}</h6>
                                @if($item->description)
                                    <small class="text-secondary d-block">{{ Str::limit($item->description, 90) }}</small>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Modal Preview Galeri --}}
                    @if($item->image)
                        <div class="modal fade" id="galleryModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                                    <div class="modal-header border-0 pb-0">
                                        <h6 class="modal-title fw-bold text-dark">{{ $item->title }}</h6>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body p-3 text-center">
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="img-fluid rounded-3 mb-2 w-100" style="max-height: 520px; object-fit: contain;">
                                        @if($item->description)
                                            <p class="text-secondary small mb-0 text-start px-2">{{ $item->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm p-4 reveal">
                <i class="bi bi-images text-muted opacity-50" style="font-size: 3.5rem;"></i>
                <h5 class="fw-bold text-muted mt-3 mb-1">Belum Ada Foto Galeri</h5>
                <p class="text-muted small mb-0">Dokumentasi foto kegiatan sekolah akan ditampilkan di sini.</p>
            </div>
        @endif
    </div>
</section>

@endsection