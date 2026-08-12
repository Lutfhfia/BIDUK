<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('landing.index');
});

// Data Siswa — Resource Route
Route::resource('students', StudentController::class);