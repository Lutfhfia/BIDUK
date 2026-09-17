<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\SchoolClass;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        // Tahun ajaran aktif
        $activeAcademicYear = AcademicYear::getActive();

        // Total siswa aktif
        $totalStudents = Student::active()->count();

        // Total pegawai aktif
        $totalEmployees = Employee::active()->count();

        // Kelas aktif pada tahun ajaran aktif
        $classes = SchoolClass::active()
            ->when($activeAcademicYear, function ($query) use ($activeAcademicYear) {
                $query->where('academic_year_id', $activeAcademicYear->id);
            })
            ->withCount([
                'students as active_students_count' => function ($query) {
                    $query->where('class_student.status', 'Aktif');
                }
            ])
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        // Total kelas
        $totalClasses = $classes->count();

        // Data untuk grafik
        $classLabels = $classes->pluck('name');
        $studentCounts = $classes->pluck('active_students_count');

        return view('dashboard', compact(
            'activeAcademicYear',
            'totalStudents',
            'totalEmployees',
            'totalClasses',
            'classes',
            'classLabels',
            'studentCounts'
        ));
    }
}