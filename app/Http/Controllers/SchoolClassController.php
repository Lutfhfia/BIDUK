<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\Employee;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SchoolClassController extends Controller
{
    /**
     * Menampilkan daftar kelas.
     */
public function index(Request $request)
{
    $query = SchoolClass::with([
        'academicYear',
        'homeroomTeacher',
    ])->withCount([
        'students as active_students_count' => function ($query) {
            $query->where('class_student.status', 'Aktif');
        }
    ]);

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    if ($request->filled('academic_year_id')) {
        $query->where(
            'academic_year_id',
            $request->academic_year_id
        );
    }

    if ($request->filled('grade_level')) {
        $query->where(
            'grade_level',
            $request->grade_level
        );
    }

    if ($request->filled('status')) {
        $query->where(
            'status',
            $request->status
        );
    }

    $classes = $query
        ->orderBy('grade_level')
        ->orderBy('name')
        ->paginate(10)
        ->withQueryString();

    $academicYears = AcademicYear::orderByDesc('start_date')->get();

    return view('admin.classes.index', compact(
        'classes',
        'academicYears'
    ));
}

    /**
     * Form tambah kelas.
     */
    public function create()
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $teachers = Employee::active()
            ->teachers()
            ->orderBy('name')
            ->get();

        return view('admin.classes.create', compact(
            'academicYears',
            'teachers'
        ));
    }

    /**
     * Menyimpan kelas baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],

            'name' => [
                'required',
                'string',
                'max:20',
            ],

            'grade_level' => [
                'required',
                'integer',
                'between:1,6',
            ],

            'homeroom_teacher_id' => [
                'nullable',
                'exists:employees,id',
            ],

            'capacity' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'status' => [
                'required',
                Rule::in(['Aktif', 'Nonaktif']),
            ],
        ]);

        /*
         * Nama kelas tidak boleh sama
         * dalam tahun ajaran yang sama.
         */
        $classExists = SchoolClass::where(
            'academic_year_id',
            $validated['academic_year_id']
        )
            ->whereRaw(
                'LOWER(name) = ?',
                [strtolower($validated['name'])]
            )
            ->exists();

        if ($classExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'name' => 'Nama kelas tersebut sudah digunakan pada tahun ajaran ini.',
                ]);
        }

        /*
         * Satu guru hanya boleh menjadi wali kelas
         * untuk satu kelas pada tahun ajaran yang sama.
         */
        if (!empty($validated['homeroom_teacher_id'])) {
            $teacherAlreadyAssigned = SchoolClass::where(
                'academic_year_id',
                $validated['academic_year_id']
            )
                ->where(
                    'homeroom_teacher_id',
                    $validated['homeroom_teacher_id']
                )
                ->exists();

            if ($teacherAlreadyAssigned) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'homeroom_teacher_id' =>
                            'Guru tersebut sudah menjadi wali kelas pada tahun ajaran ini.',
                    ]);
            }
        }

        SchoolClass::create($validated);

        return redirect()
            ->route('classes.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail kelas.
     */
    public function show(SchoolClass $class)
    {
        $class->load([
            'academicYear',
            'homeroomTeacher',
        ]);

        $students = $class->students()
            ->wherePivot('status', 'Aktif')
            ->orderBy('name')
            ->paginate(10, ['*'], 'students_page')
            ->withQueryString();

        return view('admin.classes.show', compact(
            'class',
            'students'
        ));
    }

    /**
     * Form edit kelas.
     */
    public function edit(SchoolClass $class)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $teachers = Employee::active()
            ->teachers()
            ->orderBy('name')
            ->get();

        return view('admin.classes.edit', compact(
            'class',
            'academicYears',
            'teachers'
        ));
    }

    /**
     * Memperbarui data kelas.
     */
    public function update(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],

            'name' => [
                'required',
                'string',
                'max:20',
            ],

            'grade_level' => [
                'required',
                'integer',
                'between:1,6',
            ],

            'homeroom_teacher_id' => [
                'nullable',
                'exists:employees,id',
            ],

            'capacity' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'status' => [
                'required',
                Rule::in(['Aktif', 'Nonaktif']),
            ],
        ]);

        /*
         * Pastikan kapasitas tidak lebih kecil
         * dari jumlah siswa yang sudah ada.
         */
        $activeStudentCount = $class->students()
            ->wherePivot('status', 'Aktif')
            ->count();

        if ($validated['capacity'] < $activeStudentCount) {
            return back()
                ->withInput()
                ->withErrors([
                    'capacity' =>
                        "Kapasitas tidak boleh kurang dari jumlah siswa aktif saat ini ({$activeStudentCount} siswa).",
                ]);
        }

        /*
         * Cek nama kelas duplikat.
         */
        $classExists = SchoolClass::where(
            'academic_year_id',
            $validated['academic_year_id']
        )
            ->whereRaw(
                'LOWER(name) = ?',
                [strtolower($validated['name'])]
            )
            ->where('id', '!=', $class->id)
            ->exists();

        if ($classExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'name' =>
                        'Nama kelas tersebut sudah digunakan pada tahun ajaran ini.',
                ]);
        }

        /*
         * Cek wali kelas.
         */
        if (!empty($validated['homeroom_teacher_id'])) {
            $teacherAlreadyAssigned = SchoolClass::where(
                'academic_year_id',
                $validated['academic_year_id']
            )
                ->where(
                    'homeroom_teacher_id',
                    $validated['homeroom_teacher_id']
                )
                ->where('id', '!=', $class->id)
                ->exists();

            if ($teacherAlreadyAssigned) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'homeroom_teacher_id' =>
                            'Guru tersebut sudah menjadi wali kelas pada tahun ajaran ini.',
                    ]);
            }
        }

        $class->update($validated);

        return redirect()
            ->route('classes.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Menghapus kelas.
     */
    public function destroy(SchoolClass $class)
    {
        $activeStudentCount = $class->students()
            ->wherePivot('status', 'Aktif')
            ->count();

        if ($activeStudentCount > 0) {
            return back()->withErrors([
                'delete' =>
                    'Kelas tidak dapat dihapus karena masih memiliki siswa aktif.',
            ]);
        }

        $class->delete();

        return redirect()
            ->route('classes.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }

    /**
     * Menampilkan daftar siswa dalam suatu kelas.
     */
    public function students(SchoolClass $class)
    {
        $class->load([
            'academicYear',
            'homeroomTeacher',
        ]);

        $students = $class->students()
            ->wherePivot('status', 'Aktif')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.classes.students', compact(
            'class',
            'students'
        ));
    }

    /**
     * Form checklist siswa untuk dimasukkan ke kelas.
     */
    public function addStudents(SchoolClass $class)
{
    /*
     * Ambil ID siswa yang sudah memiliki kelas aktif
     * pada tahun ajaran yang sama.
     */
    $assignedStudentIds = ClassStudent::query()
        ->where('status', 'Aktif')
        ->whereHas('schoolClass', function ($query) use ($class) {
            $query->where(
                'academic_year_id',
                $class->academic_year_id
            );
        })
        ->pluck('student_id')
        ->toArray();

    /*
     * Tampilkan hanya siswa yang BELUM memiliki
     * kelas aktif pada tahun ajaran tersebut.
     */
    $students = Student::query()
        ->whereNotIn('id', $assignedStudentIds)
        ->orderBy('name')
        ->get();

    return view('admin.classes.add-students', compact(
        'class',
        'students'
    ));
}

    /**
     * Menyimpan siswa yang dipilih ke kelas.
     */
    public function storeStudents(Request $request, SchoolClass $class)
    {
        $validated = $request->validate([
            'student_ids' => [
                'required',
                'array',
                'min:1',
            ],

            'student_ids.*' => [
                'integer',
                'exists:students,id',
            ],
        ]);

        $studentIds = $validated['student_ids'];

        /*
         * Cek kapasitas kelas.
         */
        $currentCount = $class->students()
            ->wherePivot('status', 'Aktif')
            ->count();

        $newStudentCount = count(
            array_diff(
                $studentIds,
                $class->students()->pluck('students.id')->toArray()
            )
        );

        if (($currentCount + $newStudentCount) > $class->capacity) {
            $remaining = max(
                0,
                $class->capacity - $currentCount
            );

            return back()
                ->withInput()
                ->withErrors([
                    'student_ids' =>
                        "Kapasitas kelas tidak mencukupi. Hanya tersedia {$remaining} slot siswa.",
                ]);
        }

        /*
         * Pastikan siswa tidak sedang berada di kelas lain
         * pada tahun ajaran yang sama.
         */
        $alreadyAssigned = ClassStudent::query()
            ->whereIn('student_id', $studentIds)
            ->where('status', 'Aktif')
            ->whereHas('schoolClass', function ($query) use ($class) {
                $query->where(
                    'academic_year_id',
                    $class->academic_year_id
                );
            })
            ->where('class_id', '!=', $class->id)
            ->with('student:id,name')
            ->get();

        if ($alreadyAssigned->isNotEmpty()) {
            $names = $alreadyAssigned
                ->pluck('student.name')
                ->implode(', ');

            return back()
                ->withInput()
                ->withErrors([
                    'student_ids' =>
                        "Siswa berikut sudah memiliki kelas pada tahun ajaran ini: {$names}.",
                ]);
        }

        DB::transaction(function () use ($studentIds, $class) {
            foreach ($studentIds as $studentId) {
                ClassStudent::updateOrCreate(
                    [
                        'class_id' => $class->id,
                        'student_id' => $studentId,
                    ],
                    [
                        'status' => 'Aktif',
                        'entry_date' => now()->toDateString(),
                        'exit_date' => null,
                    ]
                );
            }
        });

        return redirect()
            ->route('classes.students', $class)
            ->with('success', 'Siswa berhasil dimasukkan ke kelas.');
    }

    /**
     * Mengeluarkan siswa dari kelas.
     */
    public function removeStudent(
        SchoolClass $class,
        Student $student
    ) {
        $assignment = ClassStudent::where('class_id', $class->id)
            ->where('student_id', $student->id)
            ->where('status', 'Aktif')
            ->first();

        if (!$assignment) {
            return back()->withErrors([
                'student' => 'Siswa tidak ditemukan dalam kelas ini.',
            ]);
        }

        $assignment->update([
            'status' => 'Pindah',
            'exit_date' => now()->toDateString(),
        ]);

        return back()->with(
            'success',
            'Siswa berhasil dikeluarkan dari kelas.'
        );
    }
}
