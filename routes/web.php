<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing.index');
});

// ==============================
// LOGIN
// ==============================

Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->name('password.forgot');


// ==============================
// ROUTE SETELAH LOGIN
// ==============================

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');


    // ==============================
    // DATA SISWA
    // ==============================

    Route::resource('students', StudentController::class);


    // ==============================
    // DATA PEGAWAI
    // ==============================

    Route::get('pegawai/import/template', [EmployeeController::class, 'downloadTemplate'])
        ->name('pegawai.import.template');

    Route::post('pegawai/import', [EmployeeController::class, 'import'])
        ->name('pegawai.import');

    Route::get('pegawai/export/excel', [EmployeeController::class, 'exportExcel'])
        ->name('pegawai.export.excel');

    Route::get('pegawai/export/pdf', [EmployeeController::class, 'exportPdf'])
        ->name('pegawai.export.pdf');

    Route::resource('pegawai', EmployeeController::class);


    // ==============================
    // DATA KELAS
    // ==============================

    Route::resource('classes', SchoolClassController::class);

    // Pengelolaan siswa dalam kelas
    Route::get('classes/{class}/students', [SchoolClassController::class, 'students'])
        ->name('classes.students');

    Route::get('classes/{class}/students/add', [SchoolClassController::class, 'addStudents'])
        ->name('classes.students.add');

    Route::post('classes/{class}/students', [SchoolClassController::class, 'storeStudents'])
        ->name('classes.students.store');

    Route::delete('classes/{class}/students/{student}', [SchoolClassController::class, 'removeStudent'])
        ->name('classes.students.remove');


    // ==============================
    // AKADEMIK
    // ==============================

    // Tahun Ajaran
    Route::resource('academic-years', AcademicYearController::class);

    // Semester
    Route::resource('semesters', SemesterController::class);

    // Mata Pelajaran
    Route::resource('subjects', SubjectController::class);


    // ==============================
    // LOG AKTIVITAS
    // ==============================

    Route::get('activity-logs', [ActivityLogController::class, 'index'])
        ->name('activity-logs.index');

    Route::get('activity-logs/{activityLog}', [ActivityLogController::class, 'show'])
        ->name('activity-logs.show');
});