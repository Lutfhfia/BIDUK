@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid dashboard-page">

    {{-- Header dashboard --}}
    <div class="dashboard-heading d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <div class="dashboard-eyebrow"><span></span> BIDUK · SDN 204 PALEMBANG</div>
            <h1 class="dashboard-title mb-1">Dashboard</h1>
            <p class="text-muted mb-0">Selamat datang, {{ auth()->user()->name }}.</p>
        </div>
        <div class="dashboard-period">
            <i class="bi bi-calendar3 me-2"></i>
            Tahun ajaran aktif: <strong>{{ $activeAcademicYear?->name ?? 'Belum ada' }}</strong>
        </div>
    </div>

    {{-- Kartu statistik utama: statistik khusus Guru dan statistik umum role lain --}}
    <div class="row g-3 g-xl-4 mb-4">
        @if (auth()->user()->isGuru())
            <div class="col-12 col-md-6 col-xl-3">
                <div class="dashboard-stat-card stat-green-one h-100">
                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-content">
                        <p>Total Siswa</p>
                        <h2>{{ number_format($teacherTotalStudents) }}</h2>
                        <span>Siswa pada kelas yang diampu di tahun ajaran aktif</span>
                    </div>
                    <i class="bi bi-people stat-watermark" aria-hidden="true"></i>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="dashboard-stat-card stat-green-two h-100">
                    <div class="stat-icon"><i class="bi bi-gender-male"></i></div>
                    <div class="stat-content">
                        <p>Laki-laki</p>
                        <h2>{{ number_format($teacherMaleStudents) }}</h2>
                        <span>Jumlah siswa laki-laki</span>
                    </div>
                    <i class="bi bi-gender-male stat-watermark" aria-hidden="true"></i>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="dashboard-stat-card stat-green-three h-100">
                    <div class="stat-icon"><i class="bi bi-gender-female"></i></div>
                    <div class="stat-content">
                        <p>Perempuan</p>
                        <h2>{{ number_format($teacherFemaleStudents) }}</h2>
                        <span>Jumlah siswa perempuan</span>
                    </div>
                    <i class="bi bi-gender-female stat-watermark" aria-hidden="true"></i>
                </div>
            </div>
        @else
            <div class="col-12 col-md-6 col-xl-3">
                <div class="dashboard-stat-card stat-green-one h-100">
                    <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                    <div class="stat-content">
                        <p>Total Siswa Aktif</p>
                        <h2>{{ number_format($totalStudents) }}</h2>
                        <span>Siswa terdata pada sistem</span>
                    </div>
                    <i class="bi bi-people stat-watermark" aria-hidden="true"></i>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="dashboard-stat-card stat-green-two h-100">
                    <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
                    <div class="stat-content">
                        <p>Total Pegawai Aktif</p>
                        <h2>{{ number_format($totalEmployees) }}</h2>
                        <span>Pegawai terdata pada sistem</span>
                    </div>
                    <i class="bi bi-person-badge stat-watermark" aria-hidden="true"></i>
                </div>
            </div>

            <div class="col-12 col-md-6 col-xl-3">
                <div class="dashboard-stat-card stat-green-three h-100">
                    <div class="stat-icon"><i class="bi bi-building-fill"></i></div>
                    <div class="stat-content">
                        <p>Total Rombel</p>
                        <h2>{{ number_format($totalClasses) }}</h2>
                        <span>Rombongan belajar terdata</span>
                    </div>
                    <i class="bi bi-buildings stat-watermark" aria-hidden="true"></i>
                </div>
            </div>
        @endif

        {{-- Kartu tahun ajaran tetap sama untuk semua role. --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="dashboard-stat-card stat-green-four h-100">
                <div class="stat-icon"><i class="bi bi-calendar-event-fill"></i></div>
                <div class="stat-content">
                    <p>Tahun Ajaran Aktif</p>
                    <h2 class="stat-year">{{ $activeAcademicYear?->name ?? 'Belum ada' }}</h2>
                    <span>Periode akademik berjalan</span>
                </div>
                <i class="bi bi-calendar2-check stat-watermark" aria-hidden="true"></i>
            </div>
        </div>
    </div>
    @if (auth()->user()->isGuru())
        <section class="dashboard-panel teacher-history-panel mb-4">
            <div class="panel-heading">
                <div class="panel-heading-icon"><i class="bi bi-mortarboard-fill"></i></div>
                <div>
                    <h2>Riwayat Kelas Wali</h2>
                    <p>Pilih kelas untuk melihat tahun ajaran dan daftar siswanya.</p>
                </div>
            </div>

            <div class="teacher-history-list">
                @forelse ($teacherClassHistory as $historyClass)
                    <article class="teacher-history-item">
                        <button
                            class="teacher-history-toggle"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#teacherClassHistory{{ $historyClass->id }}"
                            aria-expanded="false"
                            aria-controls="teacherClassHistory{{ $historyClass->id }}"
                        >
                            <span class="teacher-history-class">
                                <span class="teacher-history-class-icon"><i class="bi bi-people-fill"></i></span>
                                <span>Kelas {{ $historyClass->name }}</span>
                            </span>
                            <span class="teacher-history-year">
                                {{ $historyClass->academicYear?->name ?? 'Tahun ajaran tidak tersedia' }}
                            </span>
                            <i class="bi bi-chevron-down teacher-history-chevron" aria-hidden="true"></i>
                        </button>

                        <div class="collapse" id="teacherClassHistory{{ $historyClass->id }}">
                            <div class="teacher-history-content">
                                <h3>Daftar Siswa <span>{{ $historyClass->students->count() }}</span></h3>

                                @if ($historyClass->students->isNotEmpty())
                                    <ul class="teacher-history-students">
                                        @foreach ($historyClass->students as $historyStudent)
                                            <li>{{ $historyStudent->name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="teacher-history-empty">
                                        Belum ada siswa yang tercatat pada kelas ini.
                                    </p>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="teacher-history-empty-state">
                        <i class="bi bi-journal-x" aria-hidden="true"></i>
                        <p>Belum ada riwayat kelas wali yang tercatat untuk akun ini.</p>
                    </div>
                @endforelse
            </div>
        </section>
    @endif

    {{-- Grafik dan progres nilai rapor --}}
    <div class="row g-3 g-xl-4 align-items-stretch">

        {{-- Grafik siswa per kelas --}}
        <div class="col-12 col-xl-7">
            <section class="dashboard-panel h-100">
                <div class="panel-heading">
                    <div class="panel-heading-icon"><i class="bi bi-bar-chart-line-fill"></i></div>
                    <div>
                        <h2>Jumlah Siswa per Kelas</h2>
                        <p>Sebaran siswa aktif berdasarkan kelas pada tahun ajaran aktif.</p>
                    </div>
                </div>

                <div class="chart-wrapper">
                    @if ($classes->count() > 0)
                        <canvas id="studentClassChart" aria-label="Grafik jumlah siswa per kelas"></canvas>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-bar-chart"></i>
                            <h3>Belum ada data kelas</h3>
                            <p>Data kelas belum tersedia pada tahun ajaran aktif.</p>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        {{-- Ringkasan progres nilai rapor --}}
        <div class="col-12 col-xl-5">
            <section class="dashboard-panel h-100">
                <div class="panel-heading">
                    <div class="panel-heading-icon"><i class="bi bi-clipboard2-check-fill"></i></div>
                    <div>
                        <h2>Progres Pengisian Nilai Rapor</h2>
                        <p>Kelengkapan nilai pada tahun ajaran {{ $activeAcademicYear?->name ?? 'aktif' }}.</p>
                    </div>
                </div>

                @if (isset($semesterProgress) && $semesterProgress->isNotEmpty())
                    <div class="semester-overview-list">
                        @foreach ($semesterProgress as $progress)
                            @php
                                $semesterKey = \Illuminate\Support\Str::slug($progress['name']);
                                $missingCount = count($progress['missing_items'] ?? []);
                            @endphp

                            <article class="semester-overview-card {{ $progress['available'] ? 'is-available' : 'is-unavailable' }}">
                                <div class="semester-overview-top">
                                    <div>
                                        <h3>Semester {{ $progress['name'] }}</h3>
                                        <p>{{ $progress['available'] ? 'Progres kelengkapan nilai siswa' : 'Semester belum tersedia' }}</p>
                                    </div>
                                    @if ($progress['available'])
                                        @if ($progress['percentage'] >= 100)
                                            <span class="status-pill status-complete"><i class="bi bi-check-circle-fill"></i> Lengkap</span>
                                        @else
                                            <span class="status-pill status-incomplete"><i class="bi bi-exclamation-circle-fill"></i> Perlu dilengkapi</span>
                                        @endif
                                    @else
                                        <span class="status-pill status-unavailable">Belum tersedia</span>
                                    @endif
                                </div>

                                @if ($progress['available'])
                                    <div class="semester-percentage-row">
                                        <strong>{{ $progress['percentage'] }}%</strong>
                                        <span>{{ number_format($progress['filled_grades']) }} dari {{ number_format($progress['expected_grades']) }} nilai terisi</span>
                                    </div>

                                    <div class="progress semester-progress-bar" role="progressbar"
                                         aria-label="Progres semester {{ $progress['name'] }}"
                                         aria-valuenow="{{ $progress['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $progress['percentage'] }}%"></div>
                                    </div>

                                    <div class="semester-overview-bottom">
                                        <span><i class="bi bi-list-check me-1"></i>{{ number_format($missingCount) }} item nilai belum diisi</span>
                                        @if ($progress['expected_grades'] > 0 && $missingCount > 0)
                                            <button class="btn btn-detail" type="button"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#missingGrades{{ $semesterKey }}"
                                                aria-expanded="false"
                                                aria-controls="missingGrades{{ $semesterKey }}">
                                                Lihat rincian <i class="bi bi-arrow-right ms-1"></i>
                                            </button>
                                        @elseif ($progress['expected_grades'] > 0)
                                            <span class="complete-note"><i class="bi bi-check-circle-fill me-1"></i>Semua nilai terisi</span>
                                        @endif
                                    </div>
                                @else
                                    <div class="semester-unavailable-message">
                                        <i class="bi bi-calendar-x"></i>
                                        <span>Semester {{ $progress['name'] }} belum tersedia pada tahun ajaran ini.</span>
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state empty-state-compact">
                        <i class="bi bi-clipboard2-x"></i>
                        <h3>Progres nilai belum tersedia</h3>
                        <p>Pastikan tahun ajaran aktif serta data semester sudah tersedia.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>

    {{-- Detail nilai yang belum diisi; dibuka dari tombol Lihat rincian --}}
    @if (isset($semesterProgress) && $semesterProgress->isNotEmpty())
        @foreach ($semesterProgress as $progress)
            @php
                $semesterKey = \Illuminate\Support\Str::slug($progress['name']);
                $missingItems = collect($progress['missing_items'] ?? []);
            @endphp

            @if ($progress['available'] && $progress['expected_grades'] > 0 && $missingItems->isNotEmpty())
                <section class="collapse detail-collapse" id="missingGrades{{ $semesterKey }}">
                    <div class="dashboard-panel detail-panel">
                        <div class="detail-heading">
                            <div class="detail-title-group">
                                <div class="panel-heading-icon"><i class="bi bi-list-check"></i></div>
                                <div>
                                    <div class="detail-breadcrumb"><span>Dashboard</span><i class="bi bi-chevron-right"></i><span>Progres Pengisian Nilai Rapor</span><i class="bi bi-chevron-right"></i><strong>Semester {{ $progress['name'] }}</strong></div>
                                    <h2>Detail Nilai yang Belum Diisi</h2>
                                    <p>Daftar siswa dan mata pelajaran yang belum memiliki nilai pada Semester {{ $progress['name'] }}.</p>
                                </div>
                            </div>
                            <button type="button" class="btn-close-detail" data-bs-toggle="collapse"
                                data-bs-target="#missingGrades{{ $semesterKey }}" aria-label="Tutup rincian">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        <div class="detail-summary-row">
                            <div class="detail-summary-icon"><i class="bi bi-journal-check"></i></div>
                            <div class="detail-summary-main">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                    <strong>Semester {{ $progress['name'] }}</strong>
                                    <span class="status-pill status-incomplete"><i class="bi bi-exclamation-circle-fill"></i> Perlu dilengkapi</span>
                                </div>
                                <div class="detail-summary-progress">
                                    <div class="progress semester-progress-bar" role="progressbar"
                                         aria-label="Progres semester {{ $progress['name'] }}"
                                         aria-valuenow="{{ $progress['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar" style="width: {{ $progress['percentage'] }}%"></div>
                                    </div>
                                    <strong>{{ $progress['percentage'] }}%</strong>
                                    <small>{{ number_format($progress['filled_grades']) }} dari {{ number_format($progress['expected_grades']) }} nilai terisi</small>
                                </div>
                            </div>
                        </div>

                        {{-- Filter daftar detail --}}
                        <div class="row g-3 detail-filters">
                            <div class="col-12 col-md-4">
                                <label for="filterClass{{ $semesterKey }}">Pilih kelas</label>
                                <select class="form-select detail-filter-class" id="filterClass{{ $semesterKey }}">
                                    <option value="">Semua kelas</option>
                                    @foreach ($missingItems->pluck('class_name')->filter()->unique()->sort() as $className)
                                        <option value="{{ mb_strtolower($className) }}">{{ $className }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="filterSubject{{ $semesterKey }}">Pilih mata pelajaran</label>
                                <select class="form-select detail-filter-subject" id="filterSubject{{ $semesterKey }}">
                                    <option value="">Semua mata pelajaran</option>
                                    @foreach ($missingItems->pluck('subject_name')->filter()->unique()->sort() as $subjectName)
                                        <option value="{{ mb_strtolower($subjectName) }}">{{ $subjectName }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-12 col-md-4">
                                <label for="filterStudent{{ $semesterKey }}">Cari nama siswa</label>
                                <div class="detail-search-wrap">
                                    <i class="bi bi-search"></i>
                                    <input type="search" class="form-control detail-filter-student"
                                        id="filterStudent{{ $semesterKey }}" placeholder="Ketik nama siswa...">
                                </div>
                            </div>
                        </div>

                        <div class="detail-table-heading">
                            <div>
                                <h3>Daftar nilai yang perlu dilengkapi</h3>
                                <p>Nama siswa dapat diklik untuk membuka halaman pengelolaan nilainya.</p>
                            </div>
                            <span class="detail-count"><span class="visible-row-count">{{ $missingItems->count() }}</span> item</span>
                        </div>

                        <div class="table-responsive detail-table-wrap">
                            <table class="table detail-table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th style="width: 65px;">No.</th>
                                        <th style="width: 115px;">Kelas</th>
                                        <th>Nama siswa</th>
                                        <th>Mata pelajaran</th>
                                        <th style="width: 150px;">Status nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($missingItems as $item)
                                        <tr class="missing-grade-row"
                                            data-class="{{ mb_strtolower($item['class_name'] ?? '') }}"
                                            data-subject="{{ mb_strtolower($item['subject_name'] ?? '') }}"
                                            data-student="{{ mb_strtolower($item['student_name'] ?? '') }}">
                                            <td class="row-number">{{ $loop->iteration }}</td>
                                            <td><span class="class-chip">{{ $item['class_name'] }}</span></td>
                                            <td>
                                                @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('grades.edit'))
                                                    <a href="{{ route('report-card-grades.edit', [
                                                        'class' => $item['class_id'],
                                                        'student' => $item['student_id'],
                                                    ]) }}" class="student-grade-link">
                                                        {{ $item['student_name'] }}
                                                        <i class="bi bi-box-arrow-up-right ms-1"></i>
                                                    </a>
                                                @else
                                                    <span class="student-grade-name">{{ $item['student_name'] }}</span>
                                                @endif
                                            </td>
                                            <td>{{ $item['subject_name'] }}</td>
                                            <td><span class="missing-status"><i class="bi bi-exclamation-circle-fill"></i> Belum diisi</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="filter-empty-state d-none">
                            <i class="bi bi-search"></i>
                            <span>Tidak ada data yang cocok dengan filter tersebut.</span>
                        </div>
                    </div>
                </section>
            @endif
        @endforeach
    @endif

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if ($classes->count() > 0)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const classLabels = @json($classLabels);
        const studentCounts = @json($studentCounts);
        const chartCanvas = document.getElementById('studentClassChart');

        if (chartCanvas) {
            new Chart(chartCanvas, {
                type: 'bar',
                data: {
                    labels: classLabels,
                    datasets: [{
                        label: 'Jumlah Siswa',
                        data: studentCounts,
                        backgroundColor: 'rgba(22, 143, 91, 0.82)',
                        hoverBackgroundColor: 'rgba(8, 104, 65, 0.95)',
                        borderColor: '#168f5b',
                        borderWidth: 1,
                        borderRadius: 8,
                        barThickness: 30,
                        maxBarThickness: 38
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: { duration: 550 },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#123c2c',
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function (context) {
                                    return ' ' + context.raw + ' siswa';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0, color: '#64748b' },
                            title: { display: true, text: 'Jumlah siswa', color: '#64748b' },
                            grid: { color: 'rgba(148, 163, 184, 0.18)', drawBorder: false }
                        },
                        x: {
                            ticks: { color: '#64748b' },
                            title: { display: true, text: 'Kelas', color: '#64748b' },
                            grid: { display: false }
                        }
                    }
                }
            });
        }
    });
</script>
@endif

<script>
    // Filter detail nilai berdasarkan kelas, mata pelajaran, dan nama siswa.
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.detail-collapse').forEach(function (detail) {
            const classFilter = detail.querySelector('.detail-filter-class');
            const subjectFilter = detail.querySelector('.detail-filter-subject');
            const studentFilter = detail.querySelector('.detail-filter-student');
            const rows = Array.from(detail.querySelectorAll('.missing-grade-row'));
            const count = detail.querySelector('.visible-row-count');
            const emptyState = detail.querySelector('.filter-empty-state');

            function applyFilters() {
                const selectedClass = classFilter ? classFilter.value : '';
                const selectedSubject = subjectFilter ? subjectFilter.value : '';
                const searchStudent = studentFilter ? studentFilter.value.trim().toLocaleLowerCase('id') : '';
                let visibleCount = 0;

                rows.forEach(function (row, index) {
                    const matchesClass = !selectedClass || row.dataset.class === selectedClass;
                    const matchesSubject = !selectedSubject || row.dataset.subject === selectedSubject;
                    const matchesStudent = !searchStudent || row.dataset.student.includes(searchStudent);
                    const visible = matchesClass && matchesSubject && matchesStudent;

                    row.classList.toggle('d-none', !visible);
                    if (visible) {
                        visibleCount++;
                        const numberCell = row.querySelector('.row-number');
                        if (numberCell) numberCell.textContent = visibleCount;
                    }
                });

                if (count) count.textContent = visibleCount;
                if (emptyState) emptyState.classList.toggle('d-none', visibleCount !== 0);
            }

            [classFilter, subjectFilter].forEach(function (control) {
                if (control) control.addEventListener('change', applyFilters);
            });
            if (studentFilter) studentFilter.addEventListener('input', applyFilters);
        });

        // Saat rincian dibuka, gulirkan area detail ke bagian yang terlihat.
        document.querySelectorAll('.detail-collapse').forEach(function (detail) {
            detail.addEventListener('shown.bs.collapse', function () {
                window.setTimeout(function () {
                    detail.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 80);
            });
        });
    });
</script>

<style>
    .dashboard-page {
        --dash-green: #087647;
        --dash-green-dark: #075b3c;
        --dash-green-bright: #27b875;
        --dash-text: #172b3d;
        --dash-muted: #66788a;
        --dash-border: #e3ebe8;
        padding-bottom: 1.5rem;
    }

    .dashboard-heading { padding-top: 0.25rem; }
    .dashboard-eyebrow {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
        color: var(--dash-green);
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.1em;
    }
    .dashboard-eyebrow span {
        width: 22px;
        height: 3px;
        border-radius: 10px;
        background: linear-gradient(90deg, #168653, #60d394);
    }
    .dashboard-title {
        color: var(--dash-text);
        font-size: clamp(1.55rem, 2vw, 1.9rem);
        font-weight: 800;
        letter-spacing: -0.035em;
    }
    .dashboard-heading p { font-size: 0.9rem; }
    .dashboard-period {
        padding: 10px 14px;
        border: 1px solid #dce9e1;
        border-radius: 12px;
        background: #fff;
        color: #607469;
        font-size: 0.8rem;
    }
    .dashboard-period i { color: var(--dash-green); }
    .dashboard-period strong { color: var(--dash-text); }

    .dashboard-stat-card {
        position: relative;
        isolation: isolate;
        display: flex;
        align-items: center;
        gap: 15px;
        min-height: 142px;
        padding: 22px 20px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 17px;
        color: #fff;
        box-shadow: 0 8px 22px rgba(16, 91, 59, 0.13);
        transition: transform 0.22s ease, box-shadow 0.22s ease;
    }
    .dashboard-stat-card::before,
    .dashboard-stat-card::after {
        position: absolute;
        z-index: -1;
        content: "";
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.07);
        pointer-events: none;
    }
    .dashboard-stat-card::before { width: 150px; height: 150px; top: -88px; right: -28px; }
    .dashboard-stat-card::after { width: 105px; height: 105px; bottom: -73px; right: 42px; }
    .dashboard-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 13px 28px rgba(16, 91, 59, 0.18);
    }
    .stat-green-one { background: linear-gradient(125deg, #075b43 0%, #087b51 48%, #20a86b 100%); }
    .stat-green-two { background: linear-gradient(125deg, #08704c 0%, #14965c 52%, #39bd7b 100%); }
    .stat-green-three { background: linear-gradient(125deg, #07654b 0%, #0c9366 55%, #2db989 100%); }
    .stat-green-four { background: linear-gradient(125deg, #0b6242 0%, #14895a 52%, #27ab72 100%); }
    .stat-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 54px;
        width: 54px;
        height: 54px;
        border: 1px solid rgba(255, 255, 255, 0.55);
        border-radius: 15px;
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        font-size: 1.55rem;
        backdrop-filter: blur(4px);
    }
    .stat-content { position: relative; z-index: 1; min-width: 0; }
    .stat-content p { margin: 0 0 5px; color: rgba(255,255,255,0.88); font-size: 0.83rem; font-weight: 600; }
    .stat-content h2 { margin: 0 0 5px; color: #fff; font-size: clamp(1.7rem, 2.4vw, 2.15rem); font-weight: 800; line-height: 1.1; letter-spacing: -0.04em; }
    .stat-content h2.stat-year { font-size: clamp(1.18rem, 1.7vw, 1.55rem); letter-spacing: -0.02em; }
    .stat-content span { display: block; color: rgba(255,255,255,0.78); font-size: 0.68rem; line-height: 1.45; }
    .stat-watermark { position: absolute; right: -6px; bottom: -17px; z-index: -1; color: rgba(255,255,255,0.11); font-size: 7.5rem; transform: rotate(-12deg); }

    .dashboard-panel {
        min-width: 0;
        padding: 23px;
        border: 1px solid #e6ece9;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(22, 45, 35, 0.045);
    }
    .panel-heading {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 22px;
    }
    .panel-heading-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 40px;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        background: #eaf7ef;
        color: var(--dash-green);
        font-size: 1.2rem;
    }
    .panel-heading h2 { margin: 1px 0 5px; color: var(--dash-text); font-size: 1.04rem; font-weight: 800; letter-spacing: -0.02em; }
    .panel-heading p { margin: 0; color: var(--dash-muted); font-size: 0.78rem; line-height: 1.55; }
    .teacher-history-panel { padding-bottom: 12px; }
    .teacher-history-list { display: grid; gap: 10px; }
    .teacher-history-item { overflow: hidden; border: 1px solid #e3ebe8; border-radius: 12px; background: #fff; transition: border-color 0.2s ease, box-shadow 0.2s ease; }
    .teacher-history-item:has(.teacher-history-toggle[aria-expanded="true"]) { border-color: #b9ddc9; box-shadow: 0 4px 14px rgba(16, 91, 59, 0.07); }
    .teacher-history-toggle { display: flex; align-items: center; gap: 12px; width: 100%; padding: 12px 14px; border: 0; background: #fff; color: var(--dash-text); text-align: left; font-size: 0.8rem; transition: background 0.2s ease; }
    .teacher-history-toggle:hover, .teacher-history-toggle[aria-expanded="true"] { background: #f7fbf8; }
    .teacher-history-toggle:focus-visible { outline: 3px solid rgba(8, 118, 71, 0.25); outline-offset: -3px; }
    .teacher-history-class { display: inline-flex; align-items: center; gap: 9px; color: var(--dash-green); font-weight: 750; }
    .teacher-history-class-icon { display: inline-flex; align-items: center; justify-content: center; width: 30px; height: 30px; border-radius: 9px; background: #eaf7ef; color: var(--dash-green); }
    .teacher-history-year { padding: 5px 9px; border: 1px solid #e3ebe8; border-radius: 999px; background: #f7faf8; color: var(--dash-muted); font-size: 0.7rem; font-weight: 600; }
    .teacher-history-chevron { margin-left: auto; color: var(--dash-green); font-size: 0.75rem; transition: transform 0.2s ease; }
    .teacher-history-toggle[aria-expanded="true"] .teacher-history-chevron { transform: rotate(180deg); }
    .teacher-history-content { margin: 0 14px; padding: 13px 0 14px; border-top: 1px solid #edf1ef; }
    .teacher-history-content h3 { display: flex; align-items: center; gap: 8px; margin: 0 0 10px; color: var(--dash-text); font-size: 0.78rem; font-weight: 750; }
    .teacher-history-content h3 span { display: inline-flex; align-items: center; justify-content: center; min-width: 22px; height: 22px; padding: 0 6px; border-radius: 999px; background: #eaf7ef; color: var(--dash-green); font-size: 0.67rem; }
    .teacher-history-students { display: grid; grid-template-columns: repeat(auto-fit, minmax(210px, 1fr)); gap: 7px 16px; margin: 0; padding: 0; list-style: none; }
    .teacher-history-students li { position: relative; padding-left: 15px; color: #405467; font-size: 0.76rem; line-height: 1.5; }
    .teacher-history-students li::before { position: absolute; top: 0.57em; left: 2px; width: 5px; height: 5px; border-radius: 50%; background: #27b875; content: ""; }
    .teacher-history-empty, .teacher-history-empty-state p { margin: 0; color: var(--dash-muted); font-size: 0.76rem; }
    .teacher-history-empty-state { display: flex; align-items: center; gap: 10px; padding: 16px; border: 1px dashed #dbe7e0; border-radius: 12px; background: #f8fbf9; }
    .teacher-history-empty-state i { color: var(--dash-green); font-size: 1.1rem; }
    .chart-wrapper { position: relative; width: 100%; height: 350px; }
    .empty-state { display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 260px; text-align: center; color: var(--dash-muted); }
    .empty-state > i { color: #7b9e8b; font-size: 2.3rem; margin-bottom: 12px; }
    .empty-state h3 { margin-bottom: 7px; color: var(--dash-text); font-size: 0.98rem; font-weight: 750; }
    .empty-state p { max-width: 330px; margin: 0; font-size: 0.82rem; line-height: 1.6; }
    .empty-state-compact { min-height: 300px; }

    .semester-overview-list { display: flex; flex-direction: column; gap: 14px; }
    .semester-overview-card {
        padding: 17px 17px 15px;
        border: 1px solid #e3eee7;
        border-radius: 15px;
        background: linear-gradient(125deg, #fbfefc 0%, #f1faf4 100%);
    }
    .semester-overview-card.is-unavailable { background: #f8fafc; border-color: #e7edf0; }
    .semester-overview-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; margin-bottom: 13px; }
    .semester-overview-top h3 { margin: 0 0 5px; color: var(--dash-text); font-size: 0.91rem; font-weight: 800; }
    .semester-overview-top p { margin: 0; color: var(--dash-muted); font-size: 0.73rem; }
    .status-pill { display: inline-flex; align-items: center; gap: 5px; flex: 0 0 auto; padding: 5px 9px; border-radius: 999px; font-size: 0.64rem; font-weight: 700; white-space: nowrap; }
    .status-complete { background: #dff5e8; color: #147746; }
    .status-incomplete { background: #fff2d7; color: #a75e00; }
    .status-unavailable { background: #eef2f6; color: #687789; }
    .semester-percentage-row { display: flex; flex-direction: column; gap: 2px; margin-bottom: 9px; }
    .semester-percentage-row strong { color: #14834e; font-size: 1.95rem; line-height: 1.15; font-weight: 800; letter-spacing: -0.05em; }
    .semester-percentage-row span { color: var(--dash-muted); font-size: 0.73rem; }
    .semester-progress-bar { height: 8px; overflow: hidden; border-radius: 99px; background: #e3eae7; }
    .semester-progress-bar .progress-bar { border-radius: 99px; background: linear-gradient(90deg, #13834e, #30b878); }
    .semester-overview-bottom { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 10px; margin-top: 15px; }
    .semester-overview-bottom > span { color: var(--dash-muted); font-size: 0.71rem; }
    .btn-detail { display: inline-flex; align-items: center; justify-content: center; padding: 8px 12px; border: 1px solid #70be91; border-radius: 10px; background: #fff; color: #117444; font-size: 0.75rem; font-weight: 750; white-space: nowrap; transition: color .18s ease, background .18s ease, border-color .18s ease, transform .18s ease; }
    .btn-detail:hover, .btn-detail[aria-expanded="true"] { border-color: var(--dash-green); background: var(--dash-green); color: #fff; transform: translateY(-1px); }
    .btn-detail:focus-visible { outline: 3px solid rgba(22, 143, 91, 0.22); outline-offset: 2px; }
    .complete-note { color: #147746 !important; font-weight: 700; }
    .semester-unavailable-message { display: flex; align-items: center; gap: 9px; padding: 13px; border-radius: 10px; background: #fff; color: var(--dash-muted); font-size: 0.77rem; }
    .semester-unavailable-message i { color: #7b8b9c; font-size: 1rem; }

    .detail-collapse { scroll-margin-top: 90px; margin-top: 22px; }
    .detail-panel { padding: 25px; }
    .detail-heading { display: flex; align-items: flex-start; justify-content: space-between; gap: 18px; margin-bottom: 22px; }
    .detail-title-group { display: flex; align-items: flex-start; gap: 12px; min-width: 0; }
    .detail-breadcrumb { display: flex; flex-wrap: wrap; align-items: center; gap: 7px; margin-bottom: 8px; color: #8a98a8; font-size: 0.66rem; }
    .detail-breadcrumb i { font-size: 0.55rem; }
    .detail-breadcrumb strong { color: #3b5b4a; font-weight: 700; }
    .detail-heading h2 { margin: 0 0 6px; color: var(--dash-text); font-size: 1.2rem; font-weight: 800; }
    .detail-heading p { margin: 0; color: var(--dash-muted); font-size: 0.8rem; line-height: 1.6; }
    .btn-close-detail { display: flex; align-items: center; justify-content: center; flex: 0 0 36px; width: 36px; height: 36px; border: 1px solid #e0e8e4; border-radius: 10px; background: #fff; color: #637486; transition: background .18s ease, color .18s ease; }
    .btn-close-detail:hover { background: #edf8f1; color: var(--dash-green); }
    .detail-summary-row { display: flex; align-items: center; gap: 15px; margin-bottom: 23px; padding: 15px; border: 1px solid #e1eee5; border-radius: 14px; background: linear-gradient(110deg, #f1faf4, #fbfefc); }
    .detail-summary-icon { display: flex; align-items: center; justify-content: center; flex: 0 0 44px; width: 44px; height: 44px; border-radius: 12px; background: #dff3e6; color: #13804b; font-size: 1.25rem; }
    .detail-summary-main { flex: 1; min-width: 0; }
    .detail-summary-main > div:first-child > strong { color: var(--dash-text); font-size: 0.83rem; }
    .detail-summary-progress { display: grid; grid-template-columns: minmax(100px, 1fr) auto; align-items: center; gap: 8px 12px; margin-top: 10px; }
    .detail-summary-progress .progress { grid-column: 1; }
    .detail-summary-progress > strong { color: #14834e; font-size: 1rem; }
    .detail-summary-progress > small { grid-column: 1 / -1; color: var(--dash-muted); font-size: 0.71rem; }
    .detail-filters { margin-bottom: 25px; }
    .detail-filters label { display: block; margin-bottom: 7px; color: #44586a; font-size: 0.74rem; font-weight: 700; }
    .detail-filters .form-select, .detail-search-wrap .form-control { min-height: 43px; border-color: #dce6e3; border-radius: 10px; background-color: #fff; color: #31485a; font-size: 0.77rem; box-shadow: none; }
    .detail-filters .form-select:focus, .detail-search-wrap .form-control:focus { border-color: #51b881; box-shadow: 0 0 0 3px rgba(22, 143, 91, 0.10); }
    .detail-search-wrap { position: relative; }
    .detail-search-wrap > i { position: absolute; top: 50%; left: 13px; z-index: 2; color: #82918e; transform: translateY(-50%); }
    .detail-search-wrap .form-control { padding-left: 36px; }
    .detail-table-heading { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; }
    .detail-table-heading h3 { margin: 0 0 4px; color: var(--dash-text); font-size: 0.9rem; font-weight: 800; }
    .detail-table-heading p { margin: 0; color: var(--dash-muted); font-size: 0.73rem; }
    .detail-count { flex-shrink: 0; padding: 6px 10px; border-radius: 999px; background: #eaf7ef; color: #147746; font-size: 0.72rem; font-weight: 800; }
    .detail-table-wrap { overflow: hidden; border: 1px solid #e2eae6; border-radius: 12px; }
    .detail-table { color: #405467; font-size: 0.78rem; }
    .detail-table thead th { padding: 12px 14px; border-bottom: 1px solid #dfe9e4; background: #f3f8f5; color: #506758; font-size: 0.69rem; font-weight: 800; white-space: nowrap; }
    .detail-table tbody td { padding: 12px 14px; border-color: #edf1ef; vertical-align: middle; }
    .detail-table tbody tr:hover td { background: #f8fcf9; }
    .row-number { color: #84938e; font-size: 0.72rem; }
    .class-chip { display: inline-block; padding: 5px 8px; border: 1px solid #dbece1; border-radius: 7px; background: #f1faf4; color: #176b43; font-size: 0.71rem; font-weight: 750; white-space: nowrap; }
    .student-grade-link { color: #087b4a; font-weight: 750; text-decoration: none; text-underline-offset: 3px; }
    .student-grade-link:hover { color: #075b3c; text-decoration: underline; }
    .student-grade-link i { font-size: 0.67rem; opacity: 0.65; }
    .student-grade-name { color: #374b5c; font-weight: 650; }
    .missing-status { display: inline-flex; align-items: center; gap: 5px; padding: 5px 8px; border-radius: 999px; background: #fff1f0; color: #b63832; font-size: 0.67rem; font-weight: 750; white-space: nowrap; }
    .filter-empty-state { display: flex; align-items: center; justify-content: center; gap: 9px; padding: 22px 12px; color: var(--dash-muted); font-size: 0.8rem; }
    .filter-empty-state i { color: #168653; }

    @media (max-width: 1199.98px) {
        .dashboard-stat-card { min-height: 132px; }
        .dashboard-panel { padding: 20px; }
        .semester-overview-card { padding: 15px; }
    }
    @media (max-width: 767.98px) {
        .dashboard-period { width: 100%; }
        .dashboard-stat-card { min-height: 120px; padding: 17px; }
        .stat-icon { flex-basis: 46px; width: 46px; height: 46px; font-size: 1.3rem; }
        .stat-content h2 { font-size: 1.75rem; }
        .stat-content h2.stat-year { font-size: 1.1rem; }
        .dashboard-panel, .detail-panel { padding: 17px; border-radius: 15px; }
        .panel-heading { gap: 10px; }
        .panel-heading-icon { flex-basis: 36px; width: 36px; height: 36px; }
        .teacher-history-panel { padding-bottom: 10px; }
        .teacher-history-toggle { flex-wrap: wrap; gap: 8px; padding: 11px; }
        .teacher-history-class { flex: 1 1 auto; }
        .teacher-history-year { margin-left: 39px; }
        .teacher-history-chevron { margin-left: auto; }
        .teacher-history-content { margin: 0 11px; }
        .teacher-history-students { grid-template-columns: 1fr; }
        .chart-wrapper { height: 290px; }
        .detail-heading { gap: 10px; }
        .detail-breadcrumb { font-size: 0.61rem; }
        .detail-summary-row { align-items: flex-start; padding: 12px; gap: 10px; }
        .detail-table { min-width: 680px; }
    }
    @media (prefers-reduced-motion: reduce) {
        .dashboard-stat-card, .btn-detail, .student-grade-link { transition: none !important; }
    }
</style>
@endsection
