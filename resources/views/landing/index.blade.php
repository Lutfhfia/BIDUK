@extends('layouts.landing')

@section('title', 'Beranda')

@section('content')

{{-- =========================================================
    HERO / BERANDA
========================================================= --}}
<section class="hero-section" id="beranda">

    <div class="container">

        <div class="row align-items-center g-5">

            {{-- KIRI --}}
            <div class="col-lg-6">

                <span class="hero-badge">
                    <i class="bi bi-mortarboard-fill me-2"></i>
                    Selamat Datang di BIDUK
                </span>

                <h1 class="hero-title">
                    Mengenal Lebih Dekat
                    <span>SDN 204</span>
                </h1>

                <p class="hero-description">
                    Website informasi sekolah yang menyediakan berbagai
                    informasi mengenai profil, kegiatan, prestasi,
                    berita, dan galeri sekolah.
                </p>

                <div class="hero-buttons">

                    <a href="#informasi-sekolah"
                       class="btn btn-hero-primary">

                        Kenali Sekolah
                        <i class="bi bi-arrow-down ms-2"></i>

                    </a>

                    <a href="#berita"
                       class="btn btn-hero-secondary">

                        Lihat Berita

                    </a>

                </div>

            </div>


            {{-- KANAN / FOTO SEKOLAH --}}
            <div class="col-lg-6">

                <div class="hero-image-placeholder">

                    <div class="placeholder-content">

                        <div class="placeholder-icon">
                            <i class="bi bi-image"></i>
                        </div>

                        <h5>Foto Sekolah</h5>

                        <p>
                            Tempat foto utama sekolah
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    INFORMASI SEKOLAH
========================================================= --}}
<section
    id="informasi-sekolah"
    class="school-info-section"
>

    <div class="container">

        {{-- JUDUL --}}
        <div class="section-heading text-center">

            <span class="section-label">
                <i class="bi bi-building me-2"></i>
                Profil Sekolah
            </span>

            <h2>
                Informasi Sekolah
            </h2>

            <p>
                Mengenal lebih dekat identitas dan informasi
                SDN 204 melalui BIDUK.
            </p>

        </div>


        <div class="row align-items-center g-5">

            {{-- FOTO --}}
            <div class="col-lg-5">

                <div class="school-info-image">

                    <div class="placeholder-content">

                        <div class="placeholder-icon">
                            <i class="bi bi-building"></i>
                        </div>

                        <h5>
                            Foto Sekolah
                        </h5>

                        <p>
                            Tempat foto sekolah
                        </p>

                    </div>

                </div>

            </div>


            {{-- INFORMASI --}}
            <div class="col-lg-7">

                <div class="school-info-content">

                    <h3>
                        SDN 204
                    </h3>

                    <p class="school-description">

                        SDN 204 merupakan satuan pendidikan dasar
                        yang menyediakan layanan pendidikan bagi
                        peserta didik serta mendukung perkembangan
                        akademik dan karakter siswa.

                    </p>


                    <div class="school-details">

                        {{-- NAMA --}}
                        <div class="school-detail-item">

                            <div class="detail-icon">
                                <i class="bi bi-building"></i>
                            </div>

                            <div>
                                <small>
                                    Nama Sekolah
                                </small>

                                <strong>
                                    SDN 204
                                </strong>
                            </div>

                        </div>


                        {{-- NPSN --}}
                        <div class="school-detail-item">

                            <div class="detail-icon">
                                <i class="bi bi-card-text"></i>
                            </div>

                            <div>
                                <small>
                                    NPSN
                                </small>

                                <strong>
                                    XXXXXXXX
                                </strong>
                            </div>

                        </div>


                        {{-- ALAMAT --}}
                        <div class="school-detail-item">

                            <div class="detail-icon">
                                <i class="bi bi-geo-alt"></i>
                            </div>

                            <div>
                                <small>
                                    Alamat
                                </small>

                                <strong>
                                    Alamat sekolah
                                </strong>
                            </div>

                        </div>


                        {{-- TELEPON --}}
                        <div class="school-detail-item">

                            <div class="detail-icon">
                                <i class="bi bi-telephone"></i>
                            </div>

                            <div>
                                <small>
                                    Telepon
                                </small>

                                <strong>
                                    Nomor telepon sekolah
                                </strong>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    VISI & MISI
========================================================= --}}
<section
    id="visi-misi"
    class="profile-section"
>

    <div class="container">

        <div class="section-heading text-center">

            <span class="section-label">
                <i class="bi bi-bullseye me-2"></i>
                Profil Sekolah
            </span>

            <h2>
                Visi & Misi
            </h2>

            <p>
                Landasan dan arah pengembangan sekolah.
            </p>

        </div>


        <div class="row g-4">

            {{-- VISI --}}
            <div class="col-lg-6">

                <div class="profile-card">

                    <div class="profile-card-icon">
                        <i class="bi bi-eye"></i>
                    </div>

                    <h3>
                        Visi
                    </h3>

                    <p>
                        Tempat untuk menampilkan visi sekolah.
                        Informasi ini nantinya dapat dikelola
                        oleh Super Admin melalui Profil Sekolah.
                    </p>

                </div>

            </div>


            {{-- MISI --}}
            <div class="col-lg-6">

                <div class="profile-card">

                    <div class="profile-card-icon">
                        <i class="bi bi-list-check"></i>
                    </div>

                    <h3>
                        Misi
                    </h3>

                    <p>
                        Tempat untuk menampilkan misi sekolah.
                        Informasi ini nantinya dapat dikelola
                        oleh Super Admin melalui Profil Sekolah.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    STRUKTUR ORGANISASI
========================================================= --}}
<section
    id="struktur-organisasi"
    class="profile-section section-light"
>

    <div class="container">

        <div class="section-heading text-center">

            <span class="section-label">
                <i class="bi bi-diagram-3 me-2"></i>
                Profil Sekolah
            </span>

            <h2>
                Struktur Organisasi
            </h2>

            <p>
                Struktur organisasi SDN 204.
            </p>

        </div>


        <div class="organization-placeholder">

            <div class="placeholder-content">

                <div class="placeholder-icon">
                    <i class="bi bi-diagram-3"></i>
                </div>

                <h5>
                    Struktur Organisasi Sekolah
                </h5>

                <p>
                    Tempat untuk menampilkan gambar
                    struktur organisasi sekolah.
                </p>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    PRESTASI SEKOLAH
========================================================= --}}
<section
    id="prestasi"
    class="profile-section"
>

    <div class="container">

        <div class="section-heading text-center">

            <span class="section-label">
                <i class="bi bi-trophy me-2"></i>
                Profil Sekolah
            </span>

            <h2>
                Prestasi Sekolah
            </h2>

            <p>
                Berbagai pencapaian dan prestasi
                yang diraih oleh sekolah.
            </p>

        </div>


        <div class="row g-4">

            {{-- PRESTASI 1 --}}
            <div class="col-md-6 col-lg-4">

                <div class="achievement-card">

                    <div class="achievement-image">

                        <div class="placeholder-content">

                            <div class="placeholder-icon">
                                <i class="bi bi-image"></i>
                            </div>

                            <span>
                                Foto Prestasi
                            </span>

                        </div>

                    </div>

                    <div class="achievement-content">

                        <span class="achievement-year">
                            Tahun
                        </span>

                        <h5>
                            Nama Prestasi
                        </h5>

                        <p>
                            Deskripsi singkat prestasi sekolah.
                        </p>

                    </div>

                </div>

            </div>


            {{-- PRESTASI 2 --}}
            <div class="col-md-6 col-lg-4">

                <div class="achievement-card">

                    <div class="achievement-image">

                        <div class="placeholder-content">

                            <div class="placeholder-icon">
                                <i class="bi bi-image"></i>
                            </div>

                            <span>
                                Foto Prestasi
                            </span>

                        </div>

                    </div>

                    <div class="achievement-content">

                        <span class="achievement-year">
                            Tahun
                        </span>

                        <h5>
                            Nama Prestasi
                        </h5>

                        <p>
                            Deskripsi singkat prestasi sekolah.
                        </p>

                    </div>

                </div>

            </div>


            {{-- PRESTASI 3 --}}
            <div class="col-md-6 col-lg-4">

                <div class="achievement-card">

                    <div class="achievement-image">

                        <div class="placeholder-content">

                            <div class="placeholder-icon">
                                <i class="bi bi-image"></i>
                            </div>

                            <span>
                                Foto Prestasi
                            </span>

                        </div>

                    </div>

                    <div class="achievement-content">

                        <span class="achievement-year">
                            Tahun
                        </span>

                        <h5>
                            Nama Prestasi
                        </h5>

                        <p>
                            Deskripsi singkat prestasi sekolah.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    EKSTRAKURIKULER
========================================================= --}}
<section
    id="ekstrakurikuler"
    class="profile-section section-light"
>

    <div class="container">

        <div class="section-heading text-center">

            <span class="section-label">
                <i class="bi bi-people me-2"></i>
                Profil Sekolah
            </span>

            <h2>
                Ekstrakurikuler
            </h2>

            <p>
                Kegiatan ekstrakurikuler yang tersedia
                di SDN 204.
            </p>

        </div>


        <div class="row g-4">

            {{-- EKSKUL 1 --}}
            <div class="col-md-6 col-lg-4">

                <div class="extracurricular-card">

                    <div class="extracurricular-icon">
                        <i class="bi bi-star"></i>
                    </div>

                    <h5>
                        Nama Ekstrakurikuler
                    </h5>

                    <p>
                        Deskripsi singkat kegiatan
                        ekstrakurikuler.
                    </p>

                </div>

            </div>


            {{-- EKSKUL 2 --}}
            <div class="col-md-6 col-lg-4">

                <div class="extracurricular-card">

                    <div class="extracurricular-icon">
                        <i class="bi bi-music-note-beamed"></i>
                    </div>

                    <h5>
                        Nama Ekstrakurikuler
                    </h5>

                    <p>
                        Deskripsi singkat kegiatan
                        ekstrakurikuler.
                    </p>

                </div>

            </div>


            {{-- EKSKUL 3 --}}
            <div class="col-md-6 col-lg-4">

                <div class="extracurricular-card">

                    <div class="extracurricular-icon">
                        <i class="bi bi-trophy"></i>
                    </div>

                    <h5>
                        Nama Ekstrakurikuler
                    </h5>

                    <p>
                        Deskripsi singkat kegiatan
                        ekstrakurikuler.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>



{{-- =========================================================
    BERITA
========================================================= --}}
<section
    id="berita"
    class="profile-section"
>

    <div class="container">

        <div class="section-heading text-center">

            <span class="section-label">
                <i class="bi bi-newspaper me-2"></i>
                Informasi Terkini
            </span>

            <h2>
                Berita Sekolah
            </h2>

            <p>
                Informasi dan berita terbaru mengenai
                kegiatan SDN 204.
            </p>

        </div>


        <div class="row g-4">

            {{-- BERITA 1 --}}
            <div class="col-md-6 col-lg-4">

                <article class="news-card">

                    <div class="news-image">

                        <div class="placeholder-content">

                            <div class="placeholder-icon">
                                <i class="bi bi-image"></i>
                            </div>

                            <span>
                                Foto Berita
                            </span>

                        </div>

                    </div>

                    <div class="news-content">

                        <small class="news-date">
                            <i class="bi bi-calendar3 me-1"></i>
                            Tanggal berita
                        </small>

                        <h5>
                            Judul Berita Sekolah
                        </h5>

                        <p>
                            Ringkasan singkat berita sekolah
                            yang akan ditampilkan di halaman
                            utama.
                        </p>

                        <a href="#" class="news-link">
                            Baca Selengkapnya
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>

                    </div>

                </article>

            </div>


            {{-- BERITA 2 --}}
            <div class="col-md-6 col-lg-4">

                <article class="news-card">

                    <div class="news-image">

                        <div class="placeholder-content">

                            <div class="placeholder-icon">
                                <i class="bi bi-image"></i>
                            </div>

                            <span>
                                Foto Berita
                            </span>

                        </div>

                    </div>

                    <div class="news-content">

                        <small class="news-date">
                            <i class="bi bi-calendar3 me-1"></i>
                            Tanggal berita
                        </small>

                        <h5>
                            Judul Berita Sekolah
                        </h5>

                        <p>
                            Ringkasan singkat berita sekolah
                            yang akan ditampilkan di halaman
                            utama.
                        </p>

                        <a href="#" class="news-link">
                            Baca Selengkapnya
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>

                    </div>

                </article>

            </div>


            {{-- BERITA 3 --}}
            <div class="col-md-6 col-lg-4">

                <article class="news-card">

                    <div class="news-image">

                        <div class="placeholder-content">

                            <div class="placeholder-icon">
                                <i class="bi bi-image"></i>
                            </div>

                            <span>
                                Foto Berita
                            </span>

                        </div>

                    </div>

                    <div class="news-content">

                        <small class="news-date">
                            <i class="bi bi-calendar3 me-1"></i>
                            Tanggal berita
                        </small>

                        <h5>
                            Judul Berita Sekolah
                        </h5>

                        <p>
                            Ringkasan singkat berita sekolah
                            yang akan ditampilkan di halaman
                            utama.
                        </p>

                        <a href="#" class="news-link">
                            Baca Selengkapnya
                            <i class="bi bi-arrow-right ms-1"></i>
                        </a>

                    </div>

                </article>

            </div>

        </div>


        <div class="text-center mt-5">

            <a href="#berita"
               class="btn btn-hero-secondary">

                Lihat Semua Berita

                <i class="bi bi-arrow-right ms-2"></i>

            </a>

        </div>

    </div>

</section>



{{-- =========================================================
    GALERI
========================================================= --}}
<section
    id="galeri"
    class="profile-section section-light"
>

    <div class="container">

        <div class="section-heading text-center">

            <span class="section-label">
                <i class="bi bi-images me-2"></i>
                Dokumentasi
            </span>

            <h2>
                Galeri Sekolah
            </h2>

            <p>
                Dokumentasi kegiatan dan aktivitas
                SDN 204.
            </p>

        </div>


        <div class="row g-4">

            {{-- ALBUM 1 --}}
            <div class="col-md-6 col-lg-4">

                <div class="gallery-card">

                    <div class="gallery-image">

                        <div class="placeholder-content">

                            <div class="placeholder-icon">
                                <i class="bi bi-images"></i>
                            </div>

                            <span>
                                Foto Album
                            </span>

                        </div>

                    </div>

                    <div class="gallery-content">

                        <h5>
                            Nama Album
                        </h5>

                        <p>
                            Deskripsi singkat album foto.
                        </p>

                        <a href="#"
                           class="news-link">

                            Lihat Album
                            <i class="bi bi-arrow-right ms-1"></i>

                        </a>

                    </div>

                </div>

            </div>


            {{-- ALBUM 2 --}}
            <div class="col-md-6 col-lg-4">

                <div class="gallery-card">

                    <div class="gallery-image">

                        <div class="placeholder-content">

                            <div class="placeholder-icon">
                                <i class="bi bi-images"></i>
                            </div>

                            <span>
                                Foto Album
                            </span>

                        </div>

                    </div>

                    <div class="gallery-content">

                        <h5>
                            Nama Album
                        </h5>

                        <p>
                            Deskripsi singkat album foto.
                        </p>

                        <a href="#"
                           class="news-link">

                            Lihat Album
                            <i class="bi bi-arrow-right ms-1"></i>

                        </a>

                    </div>

                </div>

            </div>


            {{-- ALBUM 3 --}}
            <div class="col-md-6 col-lg-4">

                <div class="gallery-card">

                    <div class="gallery-image">

                        <div class="placeholder-content">

                            <div class="placeholder-icon">
                                <i class="bi bi-images"></i>
                            </div>

                            <span>
                                Foto Album
                            </span>

                        </div>

                    </div>

                    <div class="gallery-content">

                        <h5>
                            Nama Album
                        </h5>

                        <p>
                            Deskripsi singkat album foto.
                        </p>

                        <a href="#"
                           class="news-link">

                            Lihat Album
                            <i class="bi bi-arrow-right ms-1"></i>

                        </a>

                    </div>

                </div>

            </div>

        </div>


        <div class="text-center mt-5">

            <a href="#galeri"
               class="btn btn-hero-secondary">

                Lihat Semua Galeri

                <i class="bi bi-arrow-right ms-2"></i>

            </a>

        </div>

    </div>

</section>



@endsection