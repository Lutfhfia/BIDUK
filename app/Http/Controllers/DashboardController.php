<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\ReportCardGrade;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Services\TeacherClassAccess;

class DashboardController extends Controller
{
    public function index()
    {
        // Tahun ajaran aktif.
        $activeAcademicYear = AcademicYear::getActive();

        // Total siswa dan pegawai aktif.
        $totalStudents = Student::active()->count();
        $totalEmployees = Employee::active()->count();

        // Kelas aktif pada tahun ajaran aktif,
        // beserta siswa dan mata pelajaran aktif.
        // Pengguna yang sedang login.
        $user = auth()->user();
        $teacherClassIds = app(TeacherClassAccess::class)->classIds($user);
        $teacherClassHistory = collect();

        if ($user->isGuru()) {
            $teacherClassHistory = SchoolClass::query()
                ->whereIn('id', $teacherClassIds)
                ->with([
                    'academicYear',
                    'students' => fn ($query) => $query->orderBy('name'),
                ])
                ->get()
                ->sortByDesc(fn (SchoolClass $class) => $class->academicYear?->start_date?->getTimestamp() ?? 0)
                ->values();
        }

        // Query kelas aktif pada tahun ajaran aktif.
        $classesQuery = SchoolClass::active()
            ->when($activeAcademicYear, function ($query) use ($activeAcademicYear) {
                $query->where(
                    'academic_year_id',
                    $activeAcademicYear->id
                );
            });

        // Guru hanya boleh melihat kelas yang menjadi wali kelasnya.
        if ($user->isGuru()) {
            $classesQuery->whereIn('id', $teacherClassIds);
        } elseif (
            !$user->isSuperAdmin()
            && !$user->isAdmin()
            && !$user->isKepalaSekolah()
        ) {
            // Role lain tidak mendapat data kelas dari dashboard ini.
            $classesQuery->whereRaw('1 = 0');
        }

        // Ambil kelas beserta siswa dan mata pelajaran aktif.
        $classes = $classesQuery
            ->withCount([
                'students as active_students_count' => function ($query) {
                    $query->where('class_student.status', 'Aktif');
                },
                'subjects as active_subjects_count' => function ($query) {
                    $query->where('subjects.status', 'Aktif');
                },
            ])
            ->with([
                'students' => function ($query) {
                    $query->wherePivot('status', 'Aktif')
                        ->select('students.id', 'students.name');
                },
                'subjects' => function ($query) {
                    $query->where('subjects.status', 'Aktif')
                        ->select('subjects.id', 'subjects.name');
                },
            ])
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();


        // Total kelas aktif.
        $totalClasses = $classes->count();

        // Data grafik jumlah siswa per kelas yang sudah ada.
        $classLabels = $classes->pluck('name');
        $studentCounts = $classes->pluck('active_students_count');

        // Ambil semester pada tahun ajaran aktif.
        $semesters = $activeAcademicYear
            ? Semester::where(
                'academic_year_id',
                $activeAcademicYear->id
            )->get()
            : collect();

        $semesterGanjil = $semesters->first(
            fn ($semester) => strtolower($semester->name) === 'ganjil'
        );

        $semesterGenap = $semesters->first(
            fn ($semester) => strtolower($semester->name) === 'genap'
        );

        $semesterOptions = [
            'Ganjil' => $semesterGanjil,
            'Genap' => $semesterGenap,
        ];

        // Ambil nilai yang sudah memiliki angka untuk kelas
        // dan semester yang sedang dipantau.
        $semesterIds = collect($semesterOptions)
            ->filter()
            ->pluck('id')
            ->values();

        $progressGrades = collect();

        if ($classes->isNotEmpty() && $semesterIds->isNotEmpty()) {
            $progressGrades = ReportCardGrade::query()
                ->whereIn('class_id', $classes->pluck('id'))
                ->whereIn('semester_id', $semesterIds)
                ->whereNotNull('score')
                ->get([
                    'student_id',
                    'class_id',
                    'subject_id',
                    'semester_id',
                ]);
        }

        // Hitung progres pengisian untuk masing-masing semester.
        $semesterProgress = collect();

        foreach ($semesterOptions as $semesterName => $semester) {
            if (!$semester) {
                $semesterProgress->put($semesterName, [
                    'available' => false,
                    'name' => $semesterName,
                    'completed_classes' => 0,
                    'incomplete_classes' => 0,
                    'total_classes' => $totalClasses,
                    'filled_grades' => 0,
                    'expected_grades' => 0,
                    'percentage' => 0,
                    'classes' => [],
                ]);

                continue;
            }

            $completedClasses = 0;
            $incompleteClasses = 0;
            $totalFilledGrades = 0;
            $totalExpectedGrades = 0;
            $classDetails = [];
            $missingItems = [];

            foreach ($classes as $class) {
                $activeStudentIds = $class->students->pluck('id');
                $activeSubjectIds = $class->subjects->pluck('id');

                $studentCount = $activeStudentIds->count();
                $subjectCount = $activeSubjectIds->count();

                // Jumlah kombinasi nilai yang wajib diisi.
                $expectedGrades = $studentCount * $subjectCount;

                $classGrades = $progressGrades
                    ->where('class_id', $class->id)
                    ->where('semester_id', $semester->id);

                // Hanya hitung nilai untuk siswa aktif dan
                // mata pelajaran aktif di kelas tersebut.
                // Pasangan siswa dan mata pelajaran yang nilainya terisi.
$filledPairs = $classGrades
->filter(function ($grade) use (
    $activeStudentIds,
    $activeSubjectIds
) {
    return $activeStudentIds->contains(
        $grade->student_id
    ) && $activeSubjectIds->contains(
        $grade->subject_id
    );
})
->mapWithKeys(function ($grade) {
    return [
        $grade->student_id . '-' . $grade->subject_id => true,
    ];
});

$filledGrades = $filledPairs->count();

// Cari kombinasi siswa dan mata pelajaran yang
// belum memiliki nilai terisi pada semester ini.
foreach ($class->students as $student) {
foreach ($class->subjects as $subject) {
    $pairKey = $student->id . '-' . $subject->id;

    if (!$filledPairs->has($pairKey)) {
        $missingItems[] = [
            'class_id' => $class->id,
            'class_name' => $class->name,
            'student_id' => $student->id,
            'student_name' => $student->name,
            'subject_name' => $subject->name,
        ];
    }
}
}

                $percentage = $expectedGrades > 0
                    ? (int) round(
                        ($filledGrades / $expectedGrades) * 100
                    )
                    : 0;

                $isComplete = $expectedGrades > 0
                    && $filledGrades >= $expectedGrades;

                if ($isComplete) {
                    $completedClasses++;
                } else {
                    $incompleteClasses++;
                }

                $totalFilledGrades += $filledGrades;
                $totalExpectedGrades += $expectedGrades;

                $classDetails[] = [
                    'id' => $class->id,
                    'name' => $class->name,
                    'students_count' => $studentCount,
                    'subjects_count' => $subjectCount,
                    'filled_grades' => $filledGrades,
                    'expected_grades' => $expectedGrades,
                    'percentage' => $percentage,
                    'is_complete' => $isComplete,
                ];
            }

            $overallPercentage = $totalExpectedGrades > 0
                ? (int) round(
                    ($totalFilledGrades / $totalExpectedGrades) * 100
                )
                : 0;

            $semesterProgress->put($semesterName, [
                'available' => true,
                'name' => $semesterName,
                'completed_classes' => $completedClasses,
                'incomplete_classes' => $incompleteClasses,
                'total_classes' => $totalClasses,
                'filled_grades' => $totalFilledGrades,
                'expected_grades' => $totalExpectedGrades,
                'percentage' => $overallPercentage,
                'classes' => $classDetails,
                'missing_items' => $missingItems,
            ]);
        }

        return view('dashboard', compact(
            'activeAcademicYear',
            'totalStudents',
            'totalEmployees',
            'totalClasses',
            'classes',
            'teacherClassHistory',
            'classLabels',
            'studentCounts',
            'semesterProgress'
        ));
    }
}
