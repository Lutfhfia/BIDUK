<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RolePermissionController;
use App\Http\Controllers\UserController;
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

// Melihat daftar kelas
Route::get(
    'classes',
    [SchoolClassController::class, 'index']
)
    ->middleware('permission:classes.view')
    ->name('classes.index');

// Form tambah kelas
Route::get(
    'classes/create',
    [SchoolClassController::class, 'create']
)
    ->middleware('permission:classes.create')
    ->name('classes.create');

// Menyimpan kelas baru
Route::post(
    'classes',
    [SchoolClassController::class, 'store']
)
    ->middleware('permission:classes.create')
    ->name('classes.store');

// Melihat detail kelas
Route::get(
    'classes/{class}',
    [SchoolClassController::class, 'show']
)
    ->middleware('permission:classes.view')
    ->name('classes.show');

// Form edit kelas
Route::get(
    'classes/{class}/edit',
    [SchoolClassController::class, 'edit']
)
    ->middleware('permission:classes.edit')
    ->name('classes.edit');

// Memperbarui kelas
Route::put(
    'classes/{class}',
    [SchoolClassController::class, 'update']
)
    ->middleware('permission:classes.edit')
    ->name('classes.update');

// Menghapus kelas
Route::delete(
    'classes/{class}',
    [SchoolClassController::class, 'destroy']
)
    ->middleware('permission:classes.delete')
    ->name('classes.destroy');


// ==============================
// PENGELOLAAN SISWA DALAM KELAS
// ==============================

// Melihat siswa dalam kelas
Route::get(
    'classes/{class}/students',
    [SchoolClassController::class, 'students']
)
    ->middleware('permission:classes.view')
    ->name('classes.students');

// Form menambahkan siswa ke kelas
Route::get(
    'classes/{class}/students/add',
    [SchoolClassController::class, 'addStudents']
)
    ->middleware('permission:classes.edit')
    ->name('classes.students.add');

// Menambahkan siswa ke kelas
Route::post(
    'classes/{class}/students',
    [SchoolClassController::class, 'storeStudents']
)
    ->middleware('permission:classes.edit')
    ->name('classes.students.store');

// Mengeluarkan siswa dari kelas
Route::delete(
    'classes/{class}/students/{student}',
    [SchoolClassController::class, 'removeStudent']
)
    ->middleware('permission:classes.edit')
    ->name('classes.students.remove');

    // ==============================
// MATA PELAJARAN
// ==============================

// Melihat daftar mata pelajaran
Route::get(
    'subjects',
    [SubjectController::class, 'index']
)
    ->middleware('permission:subjects.view')
    ->name('subjects.index');

// Form tambah mata pelajaran
Route::get(
    'subjects/create',
    [SubjectController::class, 'create']
)
    ->middleware('permission:subjects.create')
    ->name('subjects.create');

// Menyimpan mata pelajaran baru
Route::post(
    'subjects',
    [SubjectController::class, 'store']
)
    ->middleware('permission:subjects.create')
    ->name('subjects.store');

// Melihat detail mata pelajaran
Route::get(
    'subjects/{subject}',
    [SubjectController::class, 'show']
)
    ->middleware('permission:subjects.view')
    ->name('subjects.show');

// Form edit mata pelajaran
Route::get(
    'subjects/{subject}/edit',
    [SubjectController::class, 'edit']
)
    ->middleware('permission:subjects.edit')
    ->name('subjects.edit');

// Memperbarui mata pelajaran
Route::put(
    'subjects/{subject}',
    [SubjectController::class, 'update']
)
    ->middleware('permission:subjects.edit')
    ->name('subjects.update');

// Menghapus mata pelajaran
Route::delete(
    'subjects/{subject}',
    [SubjectController::class, 'destroy']
)
    ->middleware('permission:subjects.delete')
    ->name('subjects.destroy');


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
    // MANAJEMEN USER
    // ==============================

    Route::resource('users', UserController::class);


    // ==============================
    // DATA PEGAWAI
    // ==============================

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
});