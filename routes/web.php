<?php

use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing.index');
});

// Data Siswa — Resource Route
Route::resource('students', StudentController::class);

// Data Kelas — Resource Route
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
