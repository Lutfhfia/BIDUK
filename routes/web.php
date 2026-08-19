<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\SemesterController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
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

// Tahun Ajaran
Route::resource('academic-years', AcademicYearController::class);

// Semester
Route::resource('semesters', SemesterController::class);

// Pengelolaan siswa dalam kelas
Route::get('classes/{class}/students', [SchoolClassController::class, 'students'])
    ->name('classes.students');

Route::get('classes/{class}/students/add', [SchoolClassController::class, 'addStudents'])
    ->name('classes.students.add');

Route::post('classes/{class}/students', [SchoolClassController::class,'storeStudents'])
    ->name('classes.students.store');

Route::delete('classes/{class}/students/{student}', [SchoolClassController::class, 'removeStudent'])
    ->name('classes.students.remove');
});
