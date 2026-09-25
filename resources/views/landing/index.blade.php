@extends('layouts.landing')

@section('title', 'Beranda')

@section('content')

{{-- =========================================================
    HERO / BERANDA SECTION
========================================================= --}}
<section class="landing-section" id="beranda" style="padding-top: 60px; padding-bottom: 80px; background: linear-gradient(135deg, #ffffff 0%, #f0faf3 100%);">
    <div class="container">
        <div class="row align-items-center g-5">
            {{-- Left Content --}}
            <div class="col-lg-6">
                <span class="section-badge">
                    <i class="bi bi-mortarboard-fill"></i>
                    Selamat Datang di BIDUK
                </span>

                <h1 class="hero-title" style="font-size: clamp(2.3rem, 4vw, 3.4rem); font-weight: 800; line-height: 1.15; color: #111827; margin: 1rem 0 1.25rem;">
                    {{ $school->hero_title ?? ('Mengenal Lebih Dekat ' . ($school->name ?? 'SDN 204 Palembang')) }}
                </h1>

                <p class="hero-description" style="font-size: 1.1rem; color: #4b5563; line-height: 1.8; margin-bottom: 2rem;">
                    {{ $school->hero_description ?? ($school->description ? Str::limit($school->description, 230) : 'Website informasi sekolah resmi yang menyediakan berbagai data mengenai profil sekolah, kegiatan akademik, ekstrakurikuler, prestasi, berita terkini, dan dokumentasi galeri.') }}
                </p>

                <div class="d-flex align-items-center flex-wrap gap-3">
                    <a href="#informasi-sekolah" class="btn btn-biduk-primary btn-lg px-4 py-3">
                        <i class="bi bi-info-circle"></i>
                        Kenali Sekolah
                    </a>
                    <a href="#berita" class="btn btn-biduk-outline btn-lg px-4 py-3">
                        <i class="bi bi-newspaper"></i>
                        Lihat Berita
                    </a>
                </div>
            </div>

            {{-- Right Photo / Hero Media --}}
            <div class="col-lg-6">
                @php
                    $heroImage = $school->school_image ?? $school->cover_image ?? null;
                @endphp

                @if($heroImage && file_exists(public_path('storage/' . $heroImage)))
                    <div class="position-relative">
                        <div class="p-2 bg-white rounded-4 shadow-lg border" style="transform: rotate(-1deg); transition: transform 0.3s ease;">
                            <img src="{{ asset('storage/' . $heroImage) }}"
                                 alt="{{ $school->name ?? 'Foto Sekolah' }}"
                                 class="img-fluid rounded-4 w-100"
                                 style="max-height: 440px; object-fit: cover;">
                        </div>
                        <div class="position-absolute bottom-0 end-0 p-3 bg-white rounded-3 shadow border m-3 d-none d-sm-flex align-items-center gap-2">
                            <div class="rounded-circle p-2" style="background: var(--biduk-primary-light); color: var(--biduk-primary);">
                                <i class="bi bi-shield-check fs-5"></i>
                            </div>
                            <div>
                                <strong class="d-block text-dark small">{{ $school->name ?? 'SDN 204' }}</strong>
                                <small class="text-muted" style="font-size: 0.75rem;">NPSN: {{ $school->npsn ?? '10604338' }}</small>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Decorative Fallback Hero Card --}}
                    <div class="p-4 p-md-5 rounded-4 shadow-sm border text-center text-md-start position-relative overflow-hidden"
                         style="background: linear-gradient(135deg, #ffffff 0%, #e8f5ee 100%); border-color: #d1fae5 !important; min-height: 380px; display: flex; flex-direction: column; justify-content: center;">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-4 mb-4"
                             style="width: 70px; height: 70px; background: var(--biduk-primary); color: #fff; font-size: 2rem;">
                            <i class="bi bi-building"></i>
                        </div>
                        <h3 class="fw-bold text-dark mb-2">{{ $school->name ?? 'SD Negeri 204 Palembang' }}</h3>
                        <p class="text-muted mb-4" style="font-size: 0.95rem;">
                            {{ $school->address ?? 'Kota Palembang, Sumatera Selatan' }}
                        </p>
                        <div class="row g-2 text-center">
                            <div class="col-4">
                                <div class="bg-white p-2.5 rounded-3 border">
                                    <div class="fw-bold text-success fs-5">{{ $achievements->count() }}</div>
                                    <small class="text-muted" style="font-size: 0.725rem;">Prestasi</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-white p-2.5 rounded-3 border">
                                    <div class="fw-bold text-success fs-5">{{ $extracurriculars->count() }}</div>
                                    <small class="text-muted" style="font-size: 0.725rem;">Ekskul</small>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-white p-2.5 rounded-3 border">
                                    <div class="fw-bold text-success fs-5">{{ $newsItems->count() }}</div>
                                    <small class="text-muted" style="font-size: 0.725rem;">Berita</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>


{{-- =========================================================
    INFORMASI SEKOLAH SECTION
========================================================= --}}
<section class="landing-section" id="informasi-sekolah">
    <div class="container">
        {{-- Section Header --}}
        <div class="section-header">
            <span class="section-badge">
                <i class="bi bi-building"></i> Profil Sekolah
            </span>
            <h2 class="section-title">Informasi & Identitas Sekolah</h2>
            <p class="section-description">
                Mengenal lebih dekat identitas, profil, dan kontak resmi {{ $school->name ?? 'SDN 204' }} melalui BIDUK.
            </p>
        </div>

        <div class="row align-items-center g-5">
            {{-- Foto Profil / Cover --}}
            <div class="col-lg-5">
                @php
                    $infoImage = $school->cover_image ?? $school->school_image ?? null;
                @endphp

                @if($infoImage && file_exists(public_path('storage/' . $infoImage)))
                    <div class="position-relative">
                        <img src="{{ asset('storage/' . $infoImage) }}"
                             alt="Gedung {{ $school->name }}"
                             class="img-fluid rounded-4 shadow-sm border w-100"
                             style="max-height: 420px; object-fit: cover;">
                        <div class="position-absolute bottom-0 start-0 m-3 p-2 px-3 rounded-3 bg-dark bg-opacity-75 text-white small">
                            <i class="bi bi-geo-alt-fill text-success me-1"></i> {{ $school->name }}
                        </div>
                    </div>
                @else
                    <div class="p-5 rounded-4 border text-center" style="background: var(--biduk-primary-light); min-height: 350px; display: flex; flex-direction: column; align-items: center; justify-content: center;">
                        <i class="bi bi-building text-success" style="font-size: 4rem;"></i>
                        <h5 class="fw-bold text-dark mt-3 mb-1">{{ $school->name ?? 'SDN 204 Palembang' }}</h5>
                        <p class="text-muted small mb-0">{{ $school->address ?? 'Alamat Sekolah' }}</p>
                    </div>
                @endif
            </div>

            {{-- Detail Profil --}}
            <div class="col-lg-7">
                <h3 class="fw-bold text-dark mb-3" style="font-size: 1.85rem;">
                    {{ $school->name ?? 'SD Negeri 204 Palembang' }}
                </h3>

                <p class="text-muted mb-4" style="line-height: 1.8; font-size: 1rem;">
                    {{ $school->description ?? 'SD Negeri 204 Palembang merupakan satuan pendidikan dasar yang berkomitmen menyediakan layanan pendidikan berkualitas bagi peserta didik serta mendukung perkembangan akademik dan karakter siswa yang unggul, berakhlak mulia, dan berprestasi.' }}
                </p>

                <div class="row g-3">
                    {{-- Nama Sekolah --}}
                    <div class="col-md-6">
                        <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100">
                            <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 44px; height: 44px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                                <i class="bi bi-building fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Nama Sekolah</small>
                                <strong class="text-dark d-block text-truncate">{{ $school->name ?? 'SDN 204 Palembang' }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- NPSN --}}
                    <div class="col-md-6">
                        <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100">
                            <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 44px; height: 44px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                                <i class="bi bi-card-text fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">NPSN</small>
                                <strong class="text-dark d-block">{{ $school->npsn ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- NSS --}}
                    @if($school->nss)
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100">
                                <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width: 44px; height: 44px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
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
                    <div class="col-md-6">
                        <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100">
                            <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 44px; height: 44px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                                <i class="bi bi-telephone fs-5"></i>
                            </div>
                            <div class="overflow-hidden">
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Telepon</small>
                                <strong class="text-dark d-block">{{ $school->phone ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div class="col-md-6">
                        <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100">
                            <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                                 style="width: 44px; height: 44px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
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
                        <div class="col-md-6">
                            <div class="p-3 bg-white rounded-3 border d-flex align-items-center gap-3 h-100">
                                <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center flex-shrink-0"
                                     style="width: 44px; height: 44px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
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
                                 style="width: 44px; height: 44px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                                <i class="bi bi-geo-alt fs-5"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">Alamat Sekolah</small>
                                <strong class="text-dark d-block" style="line-height: 1.5;">{{ $school->address ?? 'Alamat sekolah belum diisi.' }}</strong>
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
        <div class="section-header">
            <span class="section-badge">
                <i class="bi bi-bullseye"></i> Landasan Sekolah
            </span>
            <h2 class="section-title">Visi & Misi</h2>
            <p class="section-description">
                Landasan, komitmen, dan arah tujuan pengembangan pendidikan bagi seluruh warga sekolah.
            </p>
        </div>

        <div class="row g-4">
            {{-- Visi --}}
            <div class="col-lg-6">
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

                    <div class="text-secondary" style="line-height: 1.8; font-size: 0.95rem; white-space: pre-line;">
                        @if(!empty(trim($school->vision)))
                            {{ $school->vision }}
                        @else
                            Terwujudnya peserta didik yang beriman dan bertakwa kepada Tuhan Yang Maha Esa, berakhlak mulia, cerdas, terampil, mandiri, dan berwawasan lingkungan.
                        @endif
                    </div>
                </div>
            </div>

            {{-- Misi --}}
            <div class="col-lg-6">
                <div class="biduk-feature-card h-100 position-relative overflow-hidden" style="border-top: 5px solid var(--biduk-secondary);">
                    <div class="d-flex align-items-center gap-3 mb-4">
                        <div class="rounded-3 p-2.5 d-flex align-items-center justify-content-center"
                             style="width: 52px; height: 52px; background: #e0f2fe; color: #0284c7; font-size: 1.5rem;">
                            <i class="bi bi-list-check"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold text-dark mb-0">Misi Sekolah</h4>
                            <small class="text-muted">Langkah Strategis & Implementasi</small>
                        </div>
                    </div>

                    <div class="text-secondary" style="line-height: 1.8; font-size: 0.95rem; white-space: pre-line;">
                        @if(!empty(trim($school->mission)))
                            {{ $school->mission }}
                        @else
                            1. Menyelenggarakan proses pembelajaran yang aktif, inovatif, kreatif, efektif, dan menyenangkan.
                            2. Menumbuhkan penghayatan terhadap ajaran agama serta budaya bangsa.
                            3. Meningkatkan prestasi akademik dan non-akademik siswa.
                            4. Menciptakan lingkungan sekolah yang bersih, asri, aman, dan tertib.
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
        <div class="section-header">
            <span class="section-badge">
                <i class="bi bi-diagram-3"></i> Tata Kelola
            </span>
            <h2 class="section-title">{{ $school->organization_title ?? 'Struktur Organisasi Sekolah' }}</h2>
            <p class="section-description">
                {{ $school->organization_description ?? ('Bagan kepengurusan, komite, dan manajemen dalam mendukung tata kelola pendidikan di ' . ($school->name ?? 'SDN 204 Palembang') . '.') }}
            </p>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                @if($school->organization_image && file_exists(public_path('storage/' . $school->organization_image)))
                    <div class="p-3 bg-white rounded-4 shadow-sm border text-center position-relative">
                        <img src="{{ asset('storage/' . $school->organization_image) }}"
                             alt="Struktur Organisasi {{ $school->name }}"
                             class="img-fluid rounded-3 w-100"
                             style="max-height: 600px; object-fit: contain;">

                        <div class="mt-3 d-flex justify-content-center gap-2">
                            <button class="btn btn-biduk-outline btn-sm" data-bs-toggle="modal" data-bs-target="#modalOrgImage">
                                <i class="bi bi-arrows-fullscreen me-1"></i> Perbesar Gambar Struktur
                            </button>
                            <a href="{{ asset('storage/' . $school->organization_image) }}" target="_blank" download class="btn btn-biduk-primary btn-sm">
                                <i class="bi bi-download me-1"></i> Unduh Bagan
                            </a>
                        </div>
                    </div>

                    {{-- Modal Lightbox Struktur Organisasi --}}
                    <div class="modal fade" id="modalOrgImage" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold text-dark">{{ $school->organization_title ?? 'Struktur Organisasi' }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body text-center p-3">
                                    <img src="{{ asset('storage/' . $school->organization_image) }}" alt="Struktur Organisasi" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-5 bg-white rounded-4 border text-center shadow-sm">
                        <div class="rounded-circle p-3 d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 70px; height: 70px; background: var(--biduk-primary-light); color: var(--biduk-primary); font-size: 2rem;">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Bagan Struktur Organisasi</h5>
                        <p class="text-muted mx-auto mb-0" style="max-width: 500px;">
                            Bagan struktur organisasi SD Negeri 204 Palembang dapat diperbarui oleh administrator melalui menu Profil Sekolah.
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
        <div class="section-header">
            <span class="section-badge">
                <i class="bi bi-trophy"></i> Prestasi & Pencapaian
            </span>
            <h2 class="section-title">Prestasi Sekolah & Siswa</h2>
            <p class="section-description">
                Pencapaian membanggakan yang telah diraih oleh siswa-siswi dan institusi {{ $school->name ?? 'SDN 204' }}.
            </p>
        </div>

        @if($achievements->count())
            <div class="row g-4">
                @foreach($achievements as $item)
                    <div class="col-md-6 col-lg-4">
                        <div class="biduk-feature-card d-flex flex-column h-100 p-0 overflow-hidden">
                            @if($item->image && file_exists(public_path('storage/' . $item->image)))
                                <img src="{{ asset('storage/' . $item->image) }}"
                                     alt="{{ $item->title }}"
                                     class="w-100"
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="w-100 d-flex align-items-center justify-content-center"
                                     style="height: 180px; background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); color: #d97706;">
                                    <i class="bi bi-trophy-fill" style="font-size: 3.5rem;"></i>
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
                                    <h5 class="fw-bold text-dark mb-2">{{ $item->title }}</h5>
                                    @if($item->description)
                                        <p class="text-secondary small mb-0" style="line-height: 1.6;">
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
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm p-4">
                <i class="bi bi-trophy text-muted opacity-50" style="font-size: 3.5rem;"></i>
                <h5 class="fw-bold text-muted mt-3 mb-1">Belum Ada Data Prestasi</h5>
                <p class="text-muted small mb-0">Data prestasi sekolah akan ditampilkan di sini setelah ditambahkan melalui admin profil sekolah.</p>
            </div>
        @endif
    </div>
</section>


{{-- =========================================================
    EKSTRAKURIKULER SECTION
========================================================= --}}
<section class="landing-section" id="ekstrakurikuler">
    <div class="container">
        <div class="section-header">
            <span class="section-badge">
                <i class="bi bi-people"></i> Pengembangan Diri
            </span>
            <h2 class="section-title">Kegiatan Ekstrakurikuler</h2>
            <p class="section-description">
                Wadah pembinaan minat, bakat, kreativitas, dan kepemimpinan peserta didik di luar jam pelajaran utama.
            </p>
        </div>

        @if($extracurriculars->count())
            <div class="row g-4">
                @foreach($extracurriculars as $item)
                    <div class="col-md-6 col-lg-4">
                        <div class="biduk-feature-card d-flex flex-column h-100 p-0 overflow-hidden">
                            @if($item->image && file_exists(public_path('storage/' . $item->image)))
                                <img src="{{ asset('storage/' . $item->image) }}"
                                     alt="{{ $item->name }}"
                                     class="w-100"
                                     style="height: 190px; object-fit: cover;">
                            @else
                                <div class="w-100 d-flex align-items-center justify-content-center"
                                     style="height: 180px; background: var(--biduk-primary-light); color: var(--biduk-primary);">
                                    <i class="bi bi-people-fill" style="font-size: 3.5rem;"></i>
                                </div>
                            @endif

                            <div class="p-4 d-flex flex-column flex-grow-1 justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h5 class="fw-bold text-dark mb-0">{{ $item->name }}</h5>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                                    </div>
                                    <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                        {{ $item->description ?? 'Kegiatan pembinaan minat dan bakat siswa di sekolah.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm p-4">
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
        <div class="section-header">
            <span class="section-badge">
                <i class="bi bi-newspaper"></i> Informasi Terkini
            </span>
            <h2 class="section-title">Berita & Artikel Sekolah</h2>
            <p class="section-description">
                Kabar terbaru seputar kegiatan, agenda akademik, dan pengumuman resmi {{ $school->name ?? 'SDN 204' }}.
            </p>
        </div>

        @if($newsItems->count())
            <div class="row g-4">
                @foreach($newsItems as $item)
                    <div class="col-md-6 col-lg-4">
                        <article class="biduk-feature-card d-flex flex-column h-100 p-0 overflow-hidden">
                            @if($item->image && file_exists(public_path('storage/' . $item->image)))
                                <img src="{{ asset('storage/' . $item->image) }}"
                                     alt="{{ $item->title }}"
                                     class="w-100"
                                     style="height: 200px; object-fit: cover;">
                            @else
                                <div class="w-100 d-flex align-items-center justify-content-center"
                                     style="height: 190px; background: #e0f2fe; color: #0284c7;">
                                    <i class="bi bi-newspaper" style="font-size: 3.5rem;"></i>
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
                                    <p class="text-secondary small mb-3" style="line-height: 1.6;">
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

                                    <div class="text-dark" style="line-height: 1.8; font-size: 0.95rem; white-space: pre-line;">
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
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm p-4">
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
        <div class="section-header">
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
                @foreach($galleries as $item)
                    <div class="col-md-6 col-lg-4">
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
                                    <i class="bi bi-images" style="font-size: 3.5rem;"></i>
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
            <div class="text-center py-5 bg-white rounded-4 border shadow-sm p-4">
                <i class="bi bi-images text-muted opacity-50" style="font-size: 3.5rem;"></i>
                <h5 class="fw-bold text-muted mt-3 mb-1">Belum Ada Foto Galeri</h5>
                <p class="text-muted small mb-0">Dokumentasi foto kegiatan sekolah akan ditampilkan di sini.</p>
            </div>
        @endif
    </div>
</section>

@endsection