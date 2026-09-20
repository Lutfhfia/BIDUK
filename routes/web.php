<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RolePermissionController;
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

    // ==============================
    // DASHBOARD
    // ==============================

    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');


    // ==============================
    // DATA SISWA
    // ==============================

    Route::get('students', [StudentController::class, 'index'])
        ->middleware('permission:students.view')
        ->name('students.index');

    Route::get('students/create', [StudentController::class, 'create'])
        ->middleware('permission:students.create')
        ->name('students.create');

    Route::post('students', [StudentController::class, 'store'])
        ->middleware('permission:students.create')
        ->name('students.store');

    Route::get('students/{student}', [StudentController::class, 'show'])
        ->middleware('permission:students.view')
        ->name('students.show');

    Route::get('students/{student}/edit', [StudentController::class, 'edit'])
        ->middleware('permission:students.edit')
        ->name('students.edit');

    Route::put('students/{student}', [StudentController::class, 'update'])
        ->middleware('permission:students.edit')
        ->name('students.update');

    Route::delete('students/{student}', [StudentController::class, 'destroy'])
        ->middleware('permission:students.delete')
        ->name('students.destroy');


    // ==============================
    // DATA KELAS
    // ==============================

    Route::resource('classes', SchoolClassController::class);

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


    // ==============================
    // MATA PELAJARAN
    // ==============================

    Route::resource('subjects', SubjectController::class);


    // ==============================
    // HAK AKSES ROLE
    // ==============================

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


    // ==============================
    // DATA PEGAWAI
    // ==============================

    // Daftar pegawai
    Route::get(
        'pegawai',
        [EmployeeController::class, 'index']
    )
        ->middleware('permission:employees.view')
        ->name('pegawai.index');

    // Form tambah pegawai
    Route::get(
        'pegawai/create',
        [EmployeeController::class, 'create']
    )
        ->middleware('permission:employees.create')
        ->name('pegawai.create');

    // Menyimpan pegawai baru
    Route::post(
        'pegawai',
        [EmployeeController::class, 'store']
    )
        ->middleware('permission:employees.create')
        ->name('pegawai.store');

    // Template import
    Route::get(
        'pegawai/import/template',
        [EmployeeController::class, 'downloadTemplate']
    )
        ->middleware('permission:employees.view')
        ->name('pegawai.import.template');

    // Import data pegawai
    Route::post(
        'pegawai/import',
        [EmployeeController::class, 'import']
    )
        ->middleware('permission:employees.create')
        ->name('pegawai.import');

    // Export Excel
    Route::get(
        'pegawai/export/excel',
        [EmployeeController::class, 'exportExcel']
    )
        ->middleware('permission:employees.view')
        ->name('pegawai.export.excel');

    // Export PDF
    Route::get(
        'pegawai/export/pdf',
        [EmployeeController::class, 'exportPdf']
    )
        ->middleware('permission:employees.view')
        ->name('pegawai.export.pdf');

    // Detail pegawai
    Route::get(
        'pegawai/{pegawai}',
        [EmployeeController::class, 'show']
    )
        ->middleware('permission:employees.view')
        ->name('pegawai.show');

    // Form edit pegawai
    Route::get(
        'pegawai/{pegawai}/edit',
        [EmployeeController::class, 'edit']
    )
        ->middleware('permission:employees.edit')
        ->name('pegawai.edit');

    // Update pegawai
    Route::put(
        'pegawai/{pegawai}',
        [EmployeeController::class, 'update']
    )
        ->middleware('permission:employees.edit')
        ->name('pegawai.update');

    // Hapus pegawai
    Route::delete(
        'pegawai/{pegawai}',
        [EmployeeController::class, 'destroy']
    )
        ->middleware('permission:employees.delete')
        ->name('pegawai.destroy');
});