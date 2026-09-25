
<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AchievementReportController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BukuIndukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ExtracurricularController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\RekapAbsensiController;
use App\Http\Controllers\ReportCardGradeController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\SchoolAchievementController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SchoolGalleryController;
use App\Http\Controllers\SchoolNewsController;
use App\Http\Controllers\SchoolProfileController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;


// =====================================================
// LANDING PAGE
// =====================================================

Route::get('/', [LandingController::class, 'index'])
    ->name('landing');


// =====================================================
// LOGIN
// =====================================================

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->name('password.forgot');


// =====================================================
// ROUTE SETELAH LOGIN
// =====================================================

Route::middleware('auth')->group(function () {

    // =================================================
    // DASHBOARD
    // =================================================

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');


    // =================================================
    // LOGOUT
    // =================================================

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');


    // =================================================
    // LAPORAN - BUKU INDUK
    // =================================================

    Route::get(
        '/laporan/buku-induk',
        [BukuIndukController::class, 'index']
    )->name('buku-induk.index');

    Route::get(
        '/laporan/buku-induk/cetak-batch',
        [BukuIndukController::class, 'batch']
    )->name('buku-induk.batch');

    Route::get(
        '/laporan/buku-induk/cetak/{student}',
        [BukuIndukController::class, 'print']
    )->name('buku-induk.print');

    Route::get(
        '/laporan/buku-induk/unduh-semua',
        [BukuIndukController::class, 'downloadAll']
    )->name('buku-induk.download-all');


    // =================================================
    // REKAP PRESTASI
    // =================================================

    Route::get(
        '/achievement-reports',
        [AchievementReportController::class, 'index']
    )->name('achievement-reports.index');


    // =================================================
    // DATA SISWA
    // =================================================

    Route::get(
        'students',
        [StudentController::class, 'index']
    )
        ->middleware('permission:students.view')
        ->name('students.index');

    Route::get(
        'students/create',
        [StudentController::class, 'create']
    )
        ->middleware('permission:students.create')
        ->name('students.create');

    Route::post(
        'students',
        [StudentController::class, 'store']
    )
        ->middleware('permission:students.create')
        ->name('students.store');

    Route::get(
        'students/{student}',
        [StudentController::class, 'show']
    )
        ->middleware('permission:students.view')
        ->name('students.show');

    Route::get(
        'students/{student}/edit',
        [StudentController::class, 'edit']
    )
        ->middleware('permission:students.edit')
        ->name('students.edit');

    Route::put(
        'students/{student}',
        [StudentController::class, 'update']
    )
        ->middleware('permission:students.edit')
        ->name('students.update');

    Route::delete(
        'students/{student}',
        [StudentController::class, 'destroy']
    )
        ->middleware('permission:students.delete')
        ->name('students.destroy');


    // =================================================
    // DATA KELAS
    // =================================================

    Route::get(
        'classes',
        [SchoolClassController::class, 'index']
    )
        ->middleware('permission:classes.view')
        ->name('classes.index');

    Route::get(
        'classes/create',
        [SchoolClassController::class, 'create']
    )
        ->middleware('permission:classes.create')
        ->name('classes.create');

    Route::post(
        'classes',
        [SchoolClassController::class, 'store']
    )
        ->middleware('permission:classes.create')
        ->name('classes.store');

    Route::get(
        'classes/{class}',
        [SchoolClassController::class, 'show']
    )
        ->middleware('permission:classes.view')
        ->name('classes.show');

    Route::get(
        'classes/{class}/edit',
        [SchoolClassController::class, 'edit']
    )
        ->middleware('permission:classes.edit')
        ->name('classes.edit');

    Route::put(
        'classes/{class}',
        [SchoolClassController::class, 'update']
    )
        ->middleware('permission:classes.edit')
        ->name('classes.update');

    Route::delete(
        'classes/{class}',
        [SchoolClassController::class, 'destroy']
    )
        ->middleware('permission:classes.delete')
        ->name('classes.destroy');


    // =================================================
    // PENGELOLAAN SISWA DALAM KELAS
    // =================================================

    Route::get(
        'classes/{class}/students',
        [SchoolClassController::class, 'students']
    )
        ->middleware('permission:classes.view')
        ->name('classes.students');

    Route::get(
        'classes/{class}/students/add',
        [SchoolClassController::class, 'addStudents']
    )
        ->middleware('permission:classes.edit')
        ->name('classes.students.add');

    Route::post(
        'classes/{class}/students',
        [SchoolClassController::class, 'storeStudents']
    )
        ->middleware('permission:classes.edit')
        ->name('classes.students.store');

    Route::delete(
        'classes/{class}/students/{student}',
        [SchoolClassController::class, 'removeStudent']
    )
        ->middleware('permission:classes.edit')
        ->name('classes.students.remove');


    // =================================================
    // MATA PELAJARAN
    // =================================================

    Route::get(
        'subjects',
        [SubjectController::class, 'index']
    )
        ->middleware('permission:subjects.view')
        ->name('subjects.index');

    Route::get(
        'subjects/create',
        [SubjectController::class, 'create']
    )
        ->middleware('permission:subjects.create')
        ->name('subjects.create');

    Route::post(
        'subjects',
        [SubjectController::class, 'store']
    )
        ->middleware('permission:subjects.create')
        ->name('subjects.store');

    Route::get(
        'subjects/{subject}',
        [SubjectController::class, 'show']
    )
        ->middleware('permission:subjects.view')
        ->name('subjects.show');

    Route::get(
        'subjects/{subject}/edit',
        [SubjectController::class, 'edit']
    )
        ->middleware('permission:subjects.edit')
        ->name('subjects.edit');

    Route::put(
        'subjects/{subject}',
        [SubjectController::class, 'update']
    )
        ->middleware('permission:subjects.edit')
        ->name('subjects.update');

    Route::delete(
        'subjects/{subject}',
        [SubjectController::class, 'destroy']
    )
        ->middleware('permission:subjects.delete')
        ->name('subjects.destroy');


    // =================================================
    // REKAP ABSENSI
    // =================================================

    Route::get(
        'rekap-absensi/students/{class}',
        [RekapAbsensiController::class, 'studentsByClass']
    )->name('rekap-absensi.students');

    Route::resource(
        'rekap-absensi',
        RekapAbsensiController::class
    );


    // =================================================
    // TAHUN AJARAN
    // =================================================

    Route::resource(
        'academic-years',
        AcademicYearController::class
    );


    // =================================================
    // SEMESTER
    // =================================================

    Route::resource(
        'semesters',
        SemesterController::class
    );


    // =================================================
    // NILAI RAPOT
    // =================================================

    Route::get(
        'report-card-grades',
        [ReportCardGradeController::class, 'index']
    )->name('report-card-grades.index');

    Route::get(
        'report-card-grades/{class}/{student}/edit',
        [ReportCardGradeController::class, 'edit']
    )->name('report-card-grades.edit');

    Route::put(
        'report-card-grades/{class}/{student}',
        [ReportCardGradeController::class, 'update']
    )->name('report-card-grades.update');

    Route::delete(
        'report-card-grades/{class}/{student}',
        [ReportCardGradeController::class, 'destroy']
    )->name('report-card-grades.destroy');


    // =================================================
    // HAK AKSES ROLE
    // =================================================

    Route::get(
        'role-permissions',
        [RolePermissionController::class, 'index']
    )
        ->middleware('permission:roles.manage')
        ->name('role-permissions.index');

    Route::put(
        'role-permissions/{role}',
        [RolePermissionController::class, 'update']
    )
        ->middleware('permission:roles.manage')
        ->name('role-permissions.update');


    // =================================================
    // MANAJEMEN USER
    // =================================================

    Route::resource(
        'users',
        UserController::class
    );


    // =================================================
    // DATA PEGAWAI
    // =================================================

    Route::get(
        'pegawai',
        [EmployeeController::class, 'index']
    )
        ->middleware('permission:employees.view')
        ->name('pegawai.index');

    Route::get(
        'pegawai/create',
        [EmployeeController::class, 'create']
    )
        ->middleware('permission:employees.create')
        ->name('pegawai.create');

    Route::post(
        'pegawai',
        [EmployeeController::class, 'store']
    )
        ->middleware('permission:employees.create')
        ->name('pegawai.store');

    Route::get(
        'pegawai/import/template',
        [EmployeeController::class, 'downloadTemplate']
    )
        ->middleware('permission:employees.view')
        ->name('pegawai.import.template');

    Route::post(
        'pegawai/import',
        [EmployeeController::class, 'import']
    )
        ->middleware('permission:employees.create')
        ->name('pegawai.import');

    Route::get(
        'pegawai/export/excel',
        [EmployeeController::class, 'exportExcel']
    )
        ->middleware('permission:employees.view')
        ->name('pegawai.export.excel');

    Route::get(
        'pegawai/export/pdf',
        [EmployeeController::class, 'exportPdf']
    )
        ->middleware('permission:employees.view')
        ->name('pegawai.export.pdf');

    Route::get(
        'pegawai/{pegawai}',
        [EmployeeController::class, 'show']
    )
        ->middleware('permission:employees.view')
        ->name('pegawai.show');

    Route::get(
        'pegawai/{pegawai}/edit',
        [EmployeeController::class, 'edit']
    )
        ->middleware('permission:employees.edit')
        ->name('pegawai.edit');

    Route::put(
        'pegawai/{pegawai}',
        [EmployeeController::class, 'update']
    )
        ->middleware('permission:employees.edit')
        ->name('pegawai.update');

    Route::delete(
        'pegawai/{pegawai}',
        [EmployeeController::class, 'destroy']
    )
        ->middleware('permission:employees.delete')
        ->name('pegawai.destroy');


    // =================================================
    // PROFIL SEKOLAH & PENGATURAN KONTEN
    // =================================================

    Route::get(
        '/school-profile',
        [SchoolProfileController::class, 'index']
    )->name('school-profile.index');

    Route::put(
        '/school-profile',
        [SchoolProfileController::class, 'update']
    )->name('school-profile.update');


    // =================================================
    // PRESTASI SEKOLAH
    // =================================================

    Route::resource(
        'school-achievements',
        SchoolAchievementController::class
    )->only([
        'store',
        'update',
        'destroy'
    ]);


    // =================================================
    // EKSTRAKURIKULER
    // =================================================

    Route::resource(
        'extracurriculars',
        ExtracurricularController::class
    )->only([
        'store',
        'update',
        'destroy'
    ]);


    // =================================================
    // BERITA SEKOLAH
    // =================================================

    Route::resource(
        'school-news',
        SchoolNewsController::class
    )->only([
        'store',
        'update',
        'destroy'
    ]);


    // =================================================
    // GALERI SEKOLAH
    // =================================================

    Route::resource(
        'school-galleries',
        SchoolGalleryController::class
    )->only([
        'store',
        'update',
        'destroy'
    ]);


    // =================================================
    // LOG AKTIVITAS
    // =================================================

    Route::get(
        'activity-logs',
        [ActivityLogController::class, 'index']
    )->name('activity-logs.index');

    Route::get(
        'activity-logs/{activityLog}',
        [ActivityLogController::class, 'show']
    )->name('activity-logs.show');

});