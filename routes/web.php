<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\BukuIndukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing.index');
});

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

    // Halaman utama Buku Induk
    Route::get('/laporan/buku-induk', [BukuIndukController::class, 'index'])
        ->name('buku-induk.index');

    // Daftar Buku Induk per kelas atau seluruh kelas
    Route::get('/laporan/buku-induk/cetak-batch', [BukuIndukController::class, 'batch'])
        ->name('buku-induk.batch');

    // Halaman cetak Buku Induk per siswa
    Route::get('/laporan/buku-induk/cetak/{student}', [BukuIndukController::class, 'print'])
        ->name('buku-induk.print');

    Route::get('/laporan/buku-induk/unduh-semua', [BukuIndukController::class, 'downloadAll'])
        ->name('buku-induk.download-all');


    // =================================================
    // DATA SISWA
    // =================================================

    Route::resource('students', StudentController::class);


    // =================================================
    // DATA KELAS
    // =================================================

    Route::resource('classes', SchoolClassController::class);


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

    Route::get('classes/{class}/students', [SchoolClassController::class, 'students'])
        ->name('classes.students');

    Route::get('classes/{class}/students/add', [SchoolClassController::class, 'addStudents'])
        ->name('classes.students.add');

    Route::post('classes/{class}/students', [SchoolClassController::class, 'storeStudents'])
        ->name('classes.students.store');

    Route::delete('classes/{class}/students/{student}', [SchoolClassController::class, 'removeStudent'])
        ->name('classes.students.remove');
});
