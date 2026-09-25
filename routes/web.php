<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\AchievementReportController;
use App\Http\Controllers\BukuIndukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ReportCardGradeController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\SchoolProfileController;
use App\Http\Controllers\SchoolAchievementController;
use App\Http\Controllers\ExtracurricularController;
use App\Http\Controllers\SchoolNewsController;
use App\Http\Controllers\SchoolGalleryController;
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

    Route::get('/laporan/buku-induk', [BukuIndukController::class, 'index'])
        ->name('buku-induk.index');

    Route::get('/laporan/buku-induk/cetak-batch', [BukuIndukController::class, 'batch'])
        ->name('buku-induk.batch');

    Route::get('/laporan/buku-induk/cetak/{student}', [BukuIndukController::class, 'print'])
        ->name('buku-induk.print');

    Route::get('/laporan/buku-induk/unduh-semua', [BukuIndukController::class, 'downloadAll'])
        ->name('buku-induk.download-all');



        // rekap prestasi
        Route::get(
    '/achievement-reports',
    [AchievementReportController::class, 'index']
)->name('achievement-reports.index');


    // =================================================
    // DATA SISWA
    // =================================================

    Route::resource('students', StudentController::class);


    // =================================================
    // DATA KELAS
    // =================================================

    Route::resource('classes', SchoolClassController::class);


    // =================================================
    // MATA PELAJARAN
    // =================================================

    Route::resource('subjects', SubjectController::class);


    // =================================================
    // TAHUN AJARAN
    // =================================================

    Route::resource('academic-years', AcademicYearController::class);


    // =================================================
    // SEMESTER
    // =================================================

    Route::resource('semesters', SemesterController::class);


    // =================================================
    // PENGELOLAAN SISWA DALAM KELAS
    // =================================================

    Route::get(
        'classes/{class}/students',
        [SchoolClassController::class, 'students']
    )->name('classes.students');

    Route::get(
        'classes/{class}/students/add',
        [SchoolClassController::class, 'addStudents']
    )->name('classes.students.add');

    Route::post(
        'classes/{class}/students',
        [SchoolClassController::class, 'storeStudents']
    )->name('classes.students.store');

    Route::delete(
        'classes/{class}/students/{student}',
        [SchoolClassController::class, 'removeStudent']
    )->name('classes.students.remove');


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
    // DATA PEGAWAI
    // =================================================

    Route::get(
        'pegawai/import/template',
        [EmployeeController::class, 'downloadTemplate']
    )->name('pegawai.import.template');

    Route::post(
        'pegawai/import',
        [EmployeeController::class, 'import']
    )->name('pegawai.import');

    Route::get(
        'pegawai/export/excel',
        [EmployeeController::class, 'exportExcel']
    )->name('pegawai.export.excel');

    Route::get(
        'pegawai/export/pdf',
        [EmployeeController::class, 'exportPdf']
    )->name('pegawai.export.pdf');

    Route::resource('pegawai', EmployeeController::class);


    // =================================================
    // PROFIL SEKOLAH & PENGATURAN KONTEN
    // =================================================

    Route::get('/school-profile', [SchoolProfileController::class, 'index'])
        ->name('school-profile.index');

    Route::put('/school-profile', [SchoolProfileController::class, 'update'])
        ->name('school-profile.update');

    Route::resource('school-achievements', SchoolAchievementController::class)
        ->only(['store', 'update', 'destroy']);

    Route::resource('extracurriculars', ExtracurricularController::class)
        ->only(['store', 'update', 'destroy']);

    Route::resource('school-news', SchoolNewsController::class)
        ->only(['store', 'update', 'destroy']);

    Route::resource('school-galleries', SchoolGalleryController::class)
        ->only(['store', 'update', 'destroy']);

});
