<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('students.index');
});

// Data Siswa — Resource Route
Route::resource('students', StudentController::class);
