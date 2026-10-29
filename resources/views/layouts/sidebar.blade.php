{{-- Sidebar BIDUK SDN 204 --}}

@php
    $schoolProfile = \App\Models\SchoolProfile::first();
@endphp

<aside class="biduk-sidebar" id="sidebar">

    {{-- Brand --}}
    <div class="sidebar-brand">

        <div class="sidebar-brand-icon">
            @if($schoolProfile && $schoolProfile->logo)
                <img
                    src="{{ asset('storage/' . $schoolProfile->logo) }}"
                    alt="Logo Sekolah"
                    class="school-logo"
                >
            @else
                <i class="bi bi-book-half"></i>
            @endif
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
                <a href="{{ route('dashboard') }}"
                   class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">

                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>

                </a>
            </li>

        </ul>


        {{-- ==================== MASTER DATA ==================== --}}
        <div class="sidebar-section">Master Data</div>

        <ul class="sidebar-nav">

            {{-- Data Siswa --}}
            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('students.view'))
            <li class="nav-item">

                <a href="{{ route('students.index') }}"
                   class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">

                    <i class="bi bi-people-fill"></i>
                    <span>Data Siswa</span>

                </a>

            </li>
            @endif


            {{-- Data Pegawai --}}
            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('employees.view'))
            <li class="nav-item">

                <a href="{{ route('pegawai.index') }}"
                   class="nav-link {{ request()->routeIs('pegawai.*') ? 'active' : '' }}">

                    <i class="bi bi-person-badge-fill"></i>
                    <span>Data Pegawai</span>

                </a>

            </li>
            @endif


            {{-- Data Kelas --}}
            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('classes.view') || auth()->user()?->hasPermission('classes.edit'))
            <li class="nav-item">

                <a href="{{ route('classes.index') }}"
                   class="nav-link {{ request()->routeIs('classes.*') ? 'active' : '' }}">

                    <i class="bi bi-building"></i>
                    <span>Data Kelas</span>

                </a>

            </li>
            @endif

        </ul>


        {{-- ==================== AKADEMIK ==================== --}}
        <div class="sidebar-section">Akademik</div>

        <ul class="sidebar-nav">

            {{-- Tahun Ajaran --}}
            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('academic_years.view'))
            <li class="nav-item">

                <a href="{{ route('academic-years.index') }}"
                   class="nav-link {{ request()->routeIs('academic-years.*') ? 'active' : '' }}">

                    <i class="bi bi-calendar3"></i>
                    <span>Tahun Ajaran</span>

                </a>

            </li>
            @endif


            {{-- Mata Pelajaran --}}
            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('subjects.view'))
            <li class="nav-item">

                <a href="{{ route('subjects.index') }}"
                   class="nav-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}">

                    <i class="bi bi-journal-bookmark-fill"></i>
                    <span>Mata Pelajaran</span>

                </a>

            </li>
            @endif


            {{-- Nilai Rapot --}}
            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('grades.view'))
            <li class="nav-item">

                <a href="{{ route('report-card-grades.index') }}"
                   class="nav-link {{ request()->routeIs('report-card-grades.*', 'final-grades.*') ? 'active' : '' }}">

                    <i class="bi bi-card-checklist"></i>
                    <span>Nilai Rapot</span>

                </a>

            </li>
            @endif

        </ul>


        {{-- ==================== LAPORAN ==================== --}}
        <div class="sidebar-section">Laporan</div>

        <ul class="sidebar-nav">

            {{-- Buku Induk --}}
            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('student_records.view'))
            <li class="nav-item">

                <a href="{{ route('buku-induk.index') }}"
                   class="nav-link {{ request()->routeIs('buku-induk.*') ? 'active' : '' }}">

                    <i class="bi bi-book-fill"></i>
                    <span>Buku Induk</span>

                </a>

            </li>
            @endif


         {{-- Rekap Absensi --}}
@if (auth()->user()?->isSuperAdmin() || auth()->user()?->isAdmin() || auth()->user()?->isGuru() || auth()->user()?->isKepalaSekolah())
<li class="nav-item">
    <a href="{{ route('rekap-absensi.index') }}"
       class="nav-link {{ request()->routeIs('rekap-absensi.*') ? 'active' : '' }}">
        <i class="bi bi-clipboard2-check-fill"></i>
        <span>Rekap Absensi</span>
    </a>
</li>
@endif


           {{-- Rekap Rapot --}}
@if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('report_cards.view'))
<li class="nav-item">

<a href="{{ route('rekap-rapot.index') }}"
   class="nav-link {{ request()->routeIs('rekap-rapot.*') ? 'active' : '' }}">

    <i class="bi bi-file-earmark-bar-graph-fill"></i>
    <span>Rekap Rapot</span>

</a>

</li>
@endif

            {{-- Rekap Prestasi --}}
            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('achievements.view'))
            <li class="nav-item">

                <a href="{{ route('achievement-reports.index') }}"
                   class="nav-link {{ request()->routeIs('achievement-reports.*') ? 'active' : '' }}">

                    <i class="bi bi-trophy me-2"></i>
                    <span>Rekap Prestasi</span>

                </a>

            </li>
            @endif

        </ul>



       {{-- ==================== PENGATURAN ==================== --}}
@if (
    auth()->user()?->isSuperAdmin()
    || auth()->user()?->hasPermission('school_profile.manage')
    || auth()->user()?->isKepalaSekolah()
)
    <div class="sidebar-section">Pengaturan</div>

        <ul class="sidebar-nav">

           {{-- Manajemen User --}}
@if (auth()->user()?->isSuperAdmin())
<li class="nav-item">
    <a href="{{ route('users.index') }}"
       class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
        <i class="bi bi-person-gear"></i>
        <span>Manajemen User</span>
    </a>
</li>
@endif

          {{-- Hak Akses Role --}}
@if (auth()->user()?->isSuperAdmin())
<li class="nav-item">
    <a href="{{ route('role-permissions.index') }}"
       class="nav-link {{ request()->routeIs('role-permissions.*') ? 'active' : '' }}">
        <i class="bi bi-shield-lock-fill"></i>
        <span>Hak Akses Role</span>
    </a>
</li>
@endif

            {{-- Profil Sekolah --}}
            @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('school_profile.manage'))
            <li class="nav-item">

                <a href="{{ route('school-profile.index') }}"
                   class="nav-link {{ request()->routeIs('school-profile.*')
                        || request()->routeIs('school-achievements.*')
                        || request()->routeIs('extracurriculars.*')
                        || request()->routeIs('school-news.*')
                        || request()->routeIs('school-galleries.*')
                        ? 'active' : '' }}">

                    <i class="bi bi-info-circle-fill"></i>
                    <span>Profil Sekolah</span>

                </a>

            </li>
            @endif


            {{-- Log Aktivitas --}}
            @if(auth()->user()?->isSuperAdmin() || auth()->user()?->isKepalaSekolah())

                <li class="nav-item">

                    <a href="{{ route('activity-logs.index') }}"
                       class="nav-link {{ request()->routeIs('activity-logs.*') ? 'active' : '' }}">

                        <i class="bi bi-clock-history"></i>
                        <span>Log Aktivitas</span>

                    </a>

                </li>
                @endif

</ul>

@endif

</nav>

</aside>