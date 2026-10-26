
@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">

    {{-- Header Dashboard --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-1">
            <i class="bi bi-speedometer2 text-success me-2"></i>Dashboard
        </h4>
        <p class="text-muted mb-0">
            Selamat datang, {{ auth()->user()->name }}
        </p>
    </div>

    {{-- Card Statistik --}}
    <div class="row g-4 mb-4">

        {{-- Total Siswa --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-3 p-3 bg-success bg-opacity-10 me-3 stat-icon">
                        <i class="bi bi-people-fill text-success fs-3"></i>
                    </div>

                    <div>
                        <p class="text-muted mb-1">Total Siswa Aktif</p>
                        <h3 class="fw-bold mb-0">{{ $totalStudents }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Pegawai --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10 me-3 stat-icon">
                        <i class="bi bi-person-badge-fill text-primary fs-3"></i>
                    </div>

                    <div>
                        <p class="text-muted mb-1">Total Pegawai Aktif</p>
                        <h3 class="fw-bold mb-0">{{ $totalEmployees }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Kelas --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10 me-3 stat-icon">
                        <i class="bi bi-building-fill text-warning fs-3"></i>
                    </div>

                    <div>
                        <p class="text-muted mb-1">Total Roumble</p>
                        <h3 class="fw-bold mb-0">{{ $totalClasses }}</h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tahun Ajaran --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 dashboard-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-3 p-3 bg-info bg-opacity-10 me-3 stat-icon">
                        <i class="bi bi-calendar-event-fill text-info fs-3"></i>
                    </div>

                    <div>
                        <p class="text-muted mb-1">Tahun Ajaran Aktif</p>
                        <h5 class="fw-bold mb-0">
                            {{ $activeAcademicYear?->name ?? 'Belum Ada' }}
                        </h5>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @if (auth()->user()->isGuru())
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <h5 class="fw-bold mb-1">Riwayat Kelas Wali</h5>
                <p class="text-muted mb-0">Pilih kelas untuk melihat tahun ajaran dan daftar siswanya.</p>
            </div>
            <div class="card-body p-4">
                @forelse ($teacherClassHistory as $historyClass)
                    <div class="border rounded-3 mb-3">
                        <button
                            class="btn btn-link text-decoration-none text-start w-100 p-3"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#teacherClassHistory{{ $historyClass->id }}"
                            aria-expanded="false"
                            aria-controls="teacherClassHistory{{ $historyClass->id }}"
                        >
                            <span class="fw-semibold">Kelas {{ $historyClass->name }}</span>
                            <span class="text-muted ms-2">{{ $historyClass->academicYear?->name ?? 'Tahun ajaran tidak tersedia' }}</span>
                            <i class="bi bi-chevron-down float-end"></i>
                        </button>
                        <div class="collapse" id="teacherClassHistory{{ $historyClass->id }}">
                            <div class="px-3 pb-3">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Tahun Ajaran</small>
                                        <span class="fw-semibold">{{ $historyClass->academicYear?->name ?? '-' }}</span>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Kelas</small>
                                        <span class="fw-semibold">{{ $historyClass->name }}</span>
                                    </div>
                                </div>
                                <h6 class="fw-semibold">Daftar Siswa ({{ $historyClass->students->count() }})</h6>
                                @if ($historyClass->students->isNotEmpty())
                                    <ul class="mb-0">
                                        @foreach ($historyClass->students as $historyStudent)
                                            <li>{{ $historyStudent->name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-muted mb-0">Belum ada siswa yang tercatat pada kelas ini.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Belum ada riwayat kelas wali yang tercatat untuk akun ini.</p>
                @endforelse
            </div>
        </div>
    @endif

    {{-- Grafik Jumlah Siswa per Kelas --}}
    <div class="card border-0 shadow-sm mb-4">

        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold mb-1">Jumlah Siswa per Kelas</h5>
            <p class="text-muted mb-0">
                Jumlah siswa aktif berdasarkan kelas pada tahun ajaran aktif
            </p>
        </div>

        <div class="card-body px-4 pb-4">

            @if ($classes->count() > 0)

                <div class="chart-wrapper">
                    <canvas id="studentClassChart"></canvas>
                </div>

            @else

                <div class="text-center py-5">
                    <i class="bi bi-bar-chart fs-1 text-muted"></i>

                    <h6 class="fw-bold mt-3">Belum Ada Data Kelas</h6>

                    <p class="text-muted mb-0">
                        Data kelas belum tersedia pada tahun ajaran aktif.
                    </p>
                </div>

            @endif

        </div>
    </div>

    {{-- =====================================================
         PROGRES PENGISIAN NILAI RAPOT
    ====================================================== --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold mb-1">
                <i class="bi bi-clipboard-data text-success me-2"></i>
                Progres Pengisian Nilai Rapot
            </h5>

            <p class="text-muted mb-0">
                Pantau kelengkapan nilai setiap kelas pada tahun ajaran
                {{ $activeAcademicYear?->name ?? 'Belum Ada' }}.
            </p>
        </div>

        <div class="card-body p-4">

            @if (isset($semesterProgress) && $semesterProgress->isNotEmpty())

                <div class="row g-4">

                    @foreach ($semesterProgress as $progress)

                        <div class="col-lg-6">

                            <div class="semester-progress-card border rounded-3 p-3 h-100">

                                {{-- Header Semester --}}
                                <div class="d-flex justify-content-between align-items-start gap-2 mb-4">
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            Semester {{ $progress['name'] }}
                                        </h6>

                                        <small class="text-muted">
                                            Progres kelengkapan nilai siswa
                                        </small>
                                    </div>

                                    @if ($progress['available'])
                                        <span class="badge rounded-pill bg-success bg-opacity-10 text-success">
                                            Tersedia
                                        </span>
                                    @else
                                        <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary">
                                            Belum tersedia
                                        </span>
                                    @endif
                                </div>

                                @if ($progress['available'])

                                    {{-- Persentase Keseluruhan --}}
                                    <div class="d-flex justify-content-between align-items-end mb-2">
                                        <div>
                                            <h2 class="fw-bold text-success mb-1">
                                                {{ $progress['percentage'] }}%
                                            </h2>

                                            <small class="text-muted">
                                                {{ number_format($progress['filled_grades']) }}
                                                dari
                                                {{ number_format($progress['expected_grades']) }}
                                                nilai terisi
                                            </small>
                                        </div>

                                        <i class="bi bi-journal-check text-success fs-2"></i>
                                    </div>

                                    {{-- Progress Bar Keseluruhan --}}
                                    <div
                                        class="progress mb-4"
                                        role="progressbar"
                                        aria-label="Progres semester {{ $progress['name'] }}"
                                        aria-valuenow="{{ $progress['percentage'] }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                        style="height: 10px;"
                                    >
                                        <div
                                            class="progress-bar bg-success"
                                            style="width: {{ $progress['percentage'] }}%;"
                                        ></div>
                                    </div>

                                    {{-- Jumlah Kelas --}}
                                    <div class="row g-3 mb-4">

                                        <div class="col-6">
                                            <div class="rounded-3 bg-success bg-opacity-10 p-3 h-100">
                                                <small class="text-muted d-block mb-2">
                                                    Kelas Lengkap
                                                </small>

                                                <h4 class="fw-bold text-success mb-0">
                                                    {{ $progress['completed_classes'] }}
                                                </h4>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="rounded-3 bg-warning bg-opacity-10 p-3 h-100">
                                                <small class="text-muted d-block mb-2">
                                                    Belum Lengkap
                                                </small>

                                                <h4 class="fw-bold text-warning-emphasis mb-0">
                                                    {{ $progress['incomplete_classes'] }}
                                                </h4>
                                            </div>
                                        </div>

                                    </div>

                                    {{-- Rincian per Kelas --}}
                                    <h6 class="fw-bold mb-3">Rincian Progres per Kelas</h6>

                                    @forelse ($progress['classes'] as $classProgress)

                                        <div class="class-progress-item mb-3">

                                            <div class="d-flex justify-content-between align-items-center gap-2 mb-2">
                                                <span class="fw-semibold">
                                                    {{ $classProgress['name'] }}
                                                </span>

                                                @if ($classProgress['expected_grades'] <= 0)
                                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                                        Belum ada data
                                                    </span>
                                                @else
                                                    <span class="small fw-semibold {{ $classProgress['is_complete'] ? 'text-success' : 'text-warning-emphasis' }}">
                                                        {{ $classProgress['percentage'] }}%
                                                    </span>
                                                @endif
                                            </div>

                                            @if ($classProgress['expected_grades'] > 0)

                                                <div
                                                    class="progress mb-1"
                                                    role="progressbar"
                                                    aria-label="Progres kelas {{ $classProgress['name'] }}"
                                                    aria-valuenow="{{ $classProgress['percentage'] }}"
                                                    aria-valuemin="0"
                                                    aria-valuemax="100"
                                                    style="height: 7px;"
                                                >
                                                    <div
                                                        class="progress-bar {{ $classProgress['is_complete'] ? 'bg-success' : 'bg-warning' }}"
                                                        style="width: {{ $classProgress['percentage'] }}%;"
                                                    ></div>
                                                </div>

                                                <small class="text-muted">
                                                    {{ number_format($classProgress['filled_grades']) }}
                                                    /
                                                    {{ number_format($classProgress['expected_grades']) }}
                                                    nilai terisi
                                                </small>

                                            @else

                                                <small class="text-muted">
                                                    Progres belum dapat dihitung karena siswa atau mata pelajaran aktif belum tersedia.
                                                </small>

                                            @endif

                                        </div>

                                    @empty

                                        <p class="text-muted small mb-0">
                                            Belum ada kelas pada tahun ajaran ini.
                                        </p>

                                    @endforelse
{{-- Rincian nilai yang belum diisi --}}
<div class="mt-3">

@if ($progress['expected_grades'] > 0)

    @if (!empty($progress['missing_items']))

        <button
            class="btn btn-outline-success btn-sm"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#missingGrades{{ strtolower($progress['name']) }}"
            aria-expanded="false"
            aria-controls="missingGrades{{ strtolower($progress['name']) }}"
        >
            <i class="bi bi-list-check me-1"></i>
            Lihat Nilai yang Belum Diisi
            ({{ count($progress['missing_items']) }})
        </button>

        <div
            class="collapse mt-3"
            id="missingGrades{{ strtolower($progress['name']) }}"
        >
            <div class="table-responsive">
                <table class="table table-sm table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Kelas</th>
                            <th>Nama Siswa</th>
                            <th>Mata Pelajaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($progress['missing_items'] as $item)
                            <tr>
                                <td>{{ $item['class_name'] }}</td>
                                <td>
                                    @if (auth()->user()?->isSuperAdmin() || auth()->user()?->hasPermission('grades.edit'))
                                        <a
                                            href="{{ route('report-card-grades.edit', [
                                                'class' => $item['class_id'],
                                                'student' => $item['student_id'],
                                            ]) }}"
                                            class="text-decoration-none"
                                        >
                                            {{ $item['student_name'] }}
                                        </a>
                                    @else
                                        {{ $item['student_name'] }}
                                    @endif
                                </td>
                                <td>{{ $item['subject_name'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @else

        <div class="alert alert-success py-2 mb-0 small">
            Semua nilai yang diharapkan sudah terisi.
        </div>

    @endif

@else

    <p class="text-muted small mb-0">
        Belum ada data siswa dan mata pelajaran aktif
        yang dapat diperiksa.
    </p>

@endif

</div>


                                @else

                                    <div class="text-center py-4">
                                        <i class="bi bi-calendar-x fs-2 text-muted"></i>

                                        <p class="text-muted mt-2 mb-0">
                                            Semester {{ $progress['name'] }} belum tersedia
                                            pada tahun ajaran ini.
                                        </p>
                                    </div>

                                @endif

                            </div>
                        </div>

                    @endforeach

                </div>

            @else

                <div class="text-center py-5">
                    <i class="bi bi-journal-x fs-1 text-muted"></i>

                    <h6 class="fw-bold mt-3">
                        Progres Nilai Belum Tersedia
                    </h6>

                    <p class="text-muted mb-0">
                        Pastikan tahun ajaran aktif serta data semester sudah tersedia.
                    </p>
                </div>

            @endif

        </div>
    </div>

</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if ($classes->count() > 0)
<script>
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
                    backgroundColor: 'rgba(25, 135, 84, 0.75)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1,
                    borderRadius: 8,
                    barThickness: 32
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                animation: {
                    duration: 800
                },

                plugins: {
                    legend: {
                        display: false
                    },

                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ' ' + context.raw + ' siswa';
                            }
                        }
                    }
                },

                scales: {
                    y: {
                        beginAtZero: true,

                        ticks: {
                            precision: 0
                        },

                        title: {
                            display: true,
                            text: 'Jumlah Siswa'
                        },

                        grid: {
                            drawBorder: false
                        }
                    },

                    x: {
                        title: {
                            display: true,
                            text: 'Kelas'
                        },

                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
</script>
@endif

<style>
    /* Efek hover pada kartu statistik */
    .dashboard-stat-card {
        transition: all 0.25s ease;
    }

    .dashboard-stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 22px rgba(0, 0, 0, 0.10) !important;
    }

    .dashboard-stat-card .stat-icon {
        transition: transform 0.25s ease;
    }

    .dashboard-stat-card:hover .stat-icon {
        transform: scale(1.08);
    }

    /* Area grafik */
    .chart-wrapper {
        position: relative;
        width: 100%;
        height: 350px;
    }

    /* Panel progres */
    .semester-progress-card {
        background-color: #fff;
        transition: box-shadow 0.2s ease;
    }

    .semester-progress-card:hover {
        box-shadow: 0 5px 16px rgba(0, 0, 0, 0.05);
    }

    .class-progress-item:last-child {
        margin-bottom: 0 !important;
    }
</style>
@endsection
