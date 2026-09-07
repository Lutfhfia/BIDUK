{{-- Sidebar BIDUK SDN 204 --}}
<aside class="biduk-sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="bi bi-book-half"></i>
        </div>

        <div>
            <h5>BIDUK</h5>
            <small>SDN 204</small>
        </div>
    </div>


    {{-- Navigation --}}
    <nav class="py-2">

        {{-- ==================== DASHBOARD ==================== --}}
        <ul class="sidebar-nav">

            <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>

                </a>
            </li>

        </ul>


        {{-- ==================== MASTER DATA ==================== --}}
        <div class="sidebar-section">Master Data</div>

        <ul class="sidebar-nav">

            {{-- Data Siswa --}}
            <li class="nav-item">

                <a href="{{ route('students.index') }}"
                    class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">

                    <i class="bi bi-people-fill"></i>
                    <span>Data Siswa</span>

                </a>

            </li>


            {{-- Data Pegawai --}}
            <li class="nav-item">

                <a href="#" class="nav-link">

                    <i class="bi bi-person-badge-fill"></i>
                    <span>Data Pegawai</span>

                </a>

            </li>


            {{-- Data Kelas --}}
            <li class="nav-item">

                <a href="{{ route('classes.index') }}"
                    class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">

                    <i class="bi bi-building"></i>
                    <span>Data Kelas</span>

                </a>

            </li>

        </ul>


        {{-- ==================== AKADEMIK ==================== --}}
        <div class="sidebar-section">Akademik</div>

        <ul class="sidebar-nav">

            {{-- Tahun Ajaran --}}
            <li class="nav-item">
                <a href="{{ route('academic-years.index') }}"
                    class="nav-link {{ request()->routeIs('academic-years.*') ? 'active' : '' }}">

                    <i class="bi bi-calendar3"></i>
                    <span>Tahun Ajaran</span>

                </a>
            </li>


            {{-- Mata Pelajaran --}}
            <li class="nav-item">

                <a href="#" class="nav-link">

                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span>Mata Pelajaran</span>

                </a>

            </li>


            {{-- Semester --}}
            <li class="nav-item">

                <a href="{{ route('semesters.index') }}"
                    class="nav-link {{ request()->routeIs('semesters.*') ? 'active' : '' }}">

                    <i class="bi bi-calendar-range"></i>
                    <span>Semester</span>

                </a>

            </li>


            {{-- Nilai Rapot --}}
            <li class="nav-item">

                <a href="#" class="nav-link">

                    <i class="bi bi-card-checklist"></i>
                    <span>Nilai Rapot</span>

                </a>

            </li>

        </ul>


        {{-- ==================== LAPORAN ==================== --}}
        <div class="sidebar-section">Laporan</div>

        <ul class="sidebar-nav">

            {{-- Buku Induk --}}
            <li class="nav-item">

                <a href="{{ route('buku-induk.index') }}"
                    class="nav-link {{ request()->routeIs('buku-induk.*') ? 'active' : '' }}">

                    <i class="bi bi-book-fill"></i>
                    <span>Buku Induk</span>

                </a>

            </li>


            {{-- Rekap Absensi --}}
            <li class="nav-item">

                <a href="#" class="nav-link">

                    <i class="bi bi-clipboard2-check-fill"></i>
                    <span>Rekap Absensi</span>

                </a>

            </li>


            {{-- Rekap Rapot --}}
            <li class="nav-item">

                <a href="#" class="nav-link">

                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    <span>Rekap Rapot</span>

                </a>

            </li>


            {{-- Rekap Prestasi --}}
            <li class="nav-item">

                <a href="#" class="nav-link">

                    <i class="bi bi-trophy-fill"></i>
                    <span>Rekap Prestasi</span>

                </a>

            </li>

        </ul>


        {{-- ==================== PENGATURAN ==================== --}}
        <div class="sidebar-section">Pengaturan</div>

        <ul class="sidebar-nav">

            {{-- Manajemen User --}}
            <li class="nav-item">

                <a href="#" class="nav-link">

                    <i class="bi bi-person-gear"></i>
                    <span>Manajemen User</span>

                </a>

            </li>


            {{-- Hak Akses Role --}}
            <li class="nav-item">

                <a href="#" class="nav-link">

                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Hak Akses Role</span>

                </a>

            </li>


            {{-- Profil Sekolah --}}
            <li class="nav-item">

                <a href="#" class="nav-link">

                    <i class="bi bi-info-circle-fill"></i>
                    <span>Profil Sekolah</span>

                </a>

            </li>


            {{-- Berita Sekolah --}}
            <li class="nav-item">

                <a href="#" class="nav-link">

                    <i class="bi bi-newspaper"></i>
                    <span>Berita Sekolah</span>

                </a>

            </li>


            {{-- Log Aktivitas --}}
            <li class="nav-item">

                <a href="#" class="nav-link">

                    <i class="bi bi-clock-history"></i>
                    <span>Log Aktivitas</span>

                </a>

            </li>

        </ul>

    </nav>

</aside>
