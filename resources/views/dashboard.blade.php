@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

<div class="container-fluid">

    {{-- Header Dashboard --}}
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Dashboard</h4>
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
                <div class="rounded-3 p-3 bg-success bg-opacity-10 me-3 stat-icon">                        <i class="bi bi-people-fill text-success fs-3"></i>
                    </div>

                    <div>
                        <p class="text-muted mb-1">Total Siswa Aktif</p>
                        <h3 class="fw-bold mb-0">
                            {{ $totalStudents }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Pegawai --}}
        <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 dashboard-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-3 p-3 bg-primary bg-opacity-10 me-3">
                        <i class="bi bi-person-badge-fill text-primary fs-3"></i>
                    </div>

                    <div>
                        <p class="text-muted mb-1">Total Pegawai Aktif</p>
                        <h3 class="fw-bold mb-0">
                            {{ $totalEmployees }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Total Kelas --}}
        <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 dashboard-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-3 p-3 bg-warning bg-opacity-10 me-3">
                        <i class="bi bi-building-fill text-warning fs-3"></i>
                    </div>

                    <div>
                        <p class="text-muted mb-1">Total Kelas</p>
                        <h3 class="fw-bold mb-0">
                            {{ $totalClasses }}
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tahun Ajaran --}}
        <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 dashboard-stat-card">
                <div class="card-body d-flex align-items-center">
                    <div class="rounded-3 p-3 bg-info bg-opacity-10 me-3">
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


    {{-- Grafik Jumlah Siswa per Kelas --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white border-0 pt-4 px-4">
            <h5 class="fw-bold mb-1">
                Jumlah Siswa per Kelas
            </h5>

            <p class="text-muted mb-0">
                Jumlah siswa aktif berdasarkan kelas pada tahun ajaran aktif
            </p>
        </div>

        <div class="card-body px-4 pb-4">

            @if($classes->count() > 0)

                <div style="height: 350px;">
                    <canvas id="studentClassChart"></canvas>
                </div>

            @else

                <div class="text-center py-5">
                    <i class="bi bi-bar-chart fs-1 text-muted"></i>

                    <h6 class="fw-bold mt-3">
                        Belum Ada Data Kelas
                    </h6>

                    <p class="text-muted mb-0">
                        Data kelas belum tersedia pada tahun ajaran aktif.
                    </p>
                </div>

            @endif

        </div>

    </div>

</div>


{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if($classes->count() > 0)

<script>
    const classLabels = @json($classLabels);
    const studentCounts = @json($studentCounts);

    const ctx = document.getElementById('studentClassChart');

    new Chart(ctx, {
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
</script>

@endif

@endsection
<style>
  /* Efek hover pada card dashboard */
  .dashboard-stat-card {
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .dashboard-stat-card:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.12) !important;
    }

    .dashboard-stat-card .stat-icon {
        transition: transform 0.25s ease;
    }

    .dashboard-stat-card:hover .stat-icon {
        transform: scale(1.1);
    }

    /* Area grafik */
    .chart-wrapper {
        position: relative;
        width: 100%;
        height: 350px;
    }
</style>