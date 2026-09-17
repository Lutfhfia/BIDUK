<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ReportCardGradeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing.index');
});

// Login
Route::get('/login', [AuthController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.process');

Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])
    ->name('password.forgot');

// Route yang hanya bisa diakses setelah login
Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');


    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    // Data Siswa
    Route::resource('students', StudentController::class);

    // Data Kelas
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
});

Route::delete('classes/{class}/students/{student}', [SchoolClassController::class, 'removeStudent'])
    ->name('classes.students.remove');

// Nilai Rapot

Route::get('report-card-grades', [ReportCardGradeController::class, 'index'])
    ->name('report-card-grades.index');

Route::get('report-card-grades/{class}/{student}/edit', [ReportCardGradeController::class, 'edit'])
    ->name('report-card-grades.edit');

Route::put('report-card-grades/{class}/{student}', [ReportCardGradeController::class, 'update'])
    ->name('report-card-grades.update');

Route::delete('report-card-grades/{class}/{student}', [ReportCardGradeController::class, 'destroy'])
    ->name('report-card-grades.destroy');

