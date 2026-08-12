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
        {{-- Dashboard --}}
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ url('/') }}" class="nav-link {{ request()->is('/') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
        </ul>

        {{-- Master Data --}}
        <div class="sidebar-section">Master Data</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                    <i class="bi bi-people-fill"></i>
                    <span>Data Siswa</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-person-badge-fill"></i>
                    <span>Data Pegawai</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-building"></i>
                    <span>Data Kelas</span>
                </a>
            </li>
        </ul>

        {{-- Akademik --}}
        <div class="sidebar-section">Akademik</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-calendar3"></i>
                    <span>Tahun Ajaran</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span>Mata Pelajaran</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-calendar-range"></i>
                    <span>Semester</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-card-checklist"></i>
                    <span>Nilai Rapot</span>
                </a>
            </li>
        </ul>

        {{-- Laporan --}}
        <div class="sidebar-section">Laporan</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-book-fill"></i>
                    <span>Buku Induk</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-clipboard2-check-fill"></i>
                    <span>Rekap Absensi</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-file-earmark-bar-graph-fill"></i>
                    <span>Rekap Rapot</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-trophy-fill"></i>
                    <span>Rekap Prestasi</span>
                </a>
            </li>
        </ul>

        {{-- Pengaturan --}}
        <div class="sidebar-section">Pengaturan</div>
        <ul class="sidebar-nav">
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-person-gear"></i>
                    <span>Manajemen User</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-shield-lock-fill"></i>
                    <span>Hak Akses Role</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-info-circle-fill"></i>
                    <span>Profil Sekolah</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-newspaper"></i>
                    <span>Berita Sekolah</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="bi bi-clock-history"></i>
                    <span>Log Aktivitas</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
