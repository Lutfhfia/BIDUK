<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\TeacherClassAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

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
            'homeroomTeacherUser.employee',
        ])->withCount([
            'students as active_students_count' => function ($query) {
                $query->where(
                    'class_student.status',
                    'Aktif'
                );
            },
            'subjects',
        ]);
        $teacherClassIds = app(TeacherClassAccess::class)->classIds($request->user());
        if ($teacherClassIds !== null) {
            $query->whereIn('classes.id', $teacherClassIds);
        }

        if ($request->filled('search')) {
            $query->where(
                'name',
                'like',
                '%' . $request->search . '%'
            );
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

        $academicYears = AcademicYear::orderByDesc(
            'start_date'
        )->get();

        return view(
            'admin.classes.index',
            compact(
                'classes',
                'academicYears'
            )
        );
    }

    /**
     * Form tambah kelas.
     */
    public function create(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc(
            'start_date'
        )->get();

        $teachers = User::query()
            ->where('status', 'Aktif')
            ->whereHas('role', fn ($query) => $query->where('name', 'Guru'))
            ->when($request->user()->isGuru(), fn ($query) => $query->whereKey($request->user()->id))
            ->with('employee')
            ->orderBy('name')
            ->get();

        // Ambil hanya mata pelajaran aktif.
        $subjects = Subject::active()
            ->orderBy('code')
            ->orderBy('id')
            ->get();

        $selectedAcademicYearId = $request->integer('academic_year_id') ?: null;
        $selectedGradeLevel = $request->integer('grade_level') ?: null;
        $prefilledClassName = trim((string) $request->input('name'));

        return view(
            'admin.classes.create',
            compact(
                'academicYears',
                'teachers',
                'subjects',
                'selectedAcademicYearId',
                'selectedGradeLevel',
                'prefilledClassName'
            )
        );
    }

    /**
     * Menyimpan kelas baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'history_student_id' => [
                'nullable',
                'integer',
                'exists:students,id',
            ],

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

            'homeroom_teacher_user_id' => ['nullable', 'string'],

            'capacity' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'status' => [
                'required',
                Rule::in([
                    'Aktif',
                    'Nonaktif',
                ]),
            ],

            'subject_ids' => [
                'nullable',
                'array',
            ],

            'subject_ids.*' => [
                'integer',
                Rule::exists(
                    'subjects',
                    'id'
                )->where(function ($query) {
                    $query->where(
                        'status',
                        'Aktif'
                    );
                }),
            ],
        ]);

        $teacherUser = $this->resolveActiveTeacherUser($validated['homeroom_teacher_user_id'] ?? null);
        if ($request->user()->isGuru()) {
            abort_unless(
                $teacherUser && (int) $teacherUser->id === (int) $request->user()->id,
                403,
                'Guru hanya dapat membuat kelas dengan dirinya sendiri sebagai wali kelas.'
            );
        }

        $historyStudentId = $validated['history_student_id'] ?? null;

        /*
         * Simpan subject_ids secara terpisah.
         * Karena subject_ids bukan kolom di tabel classes.
         */
        $subjectIds = $validated['subject_ids'] ?? [];
        $isTeacher = $request->user()->isGuru();

        unset($validated['subject_ids'], $validated['history_student_id'], $validated['homeroom_teacher_user_id']);
        $validated['homeroom_teacher_user_id'] = $teacherUser?->id;
        $validated['homeroom_teacher_id'] = $teacherUser?->employee_id;

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
                    'name' =>
                        'Nama kelas tersebut sudah digunakan pada tahun ajaran ini.',
                ]);
        }

        /*
         * Satu guru hanya boleh menjadi wali kelas
         * untuk satu kelas pada tahun ajaran yang sama.
         */
        if ($teacherUser) {
            $teacherAlreadyAssigned = SchoolClass::where(
                'academic_year_id',
                $validated['academic_year_id']
            )->where(function ($query) use ($teacherUser) {
                $query->where('homeroom_teacher_user_id', $teacherUser->id);
                if ($teacherUser->employee_id) {
                    $query->orWhere('homeroom_teacher_id', $teacherUser->employee_id);
                }
            })
                ->exists();

            if ($teacherAlreadyAssigned) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'homeroom_teacher_user_id' =>
                            'Guru tersebut sudah menjadi wali kelas pada tahun ajaran ini.',
                    ]);
            }
        }

        /*
         * Simpan kelas + mata pelajaran
         * dalam satu transaksi.
         */
        DB::transaction(function () use (
            $validated,
            $subjectIds
        ) {
            $class = SchoolClass::create(
                $validated
            );

            // Simpan mata pelajaran ke pivot class_subject.
            $class->subjects()->sync(
                $subjectIds
            );
        });

        // Ambil kelas yang baru dibuat.
        $class = SchoolClass::latest('id')->first();

        // Catat aktivitas penambahan kelas.
        app(ActivityLogger::class)->log(
            'created',
            'classes',
            'Menambahkan data kelas: ' . $class->name,
            $class->fresh(),
            null,
            $class->fresh()->getAttributes()
        );

        if ($historyStudentId) {
            return redirect()
                ->route('report-card-grades.history', ['student' => $historyStudentId])
                ->with(
                    'success',
                    'Kelas dan mata pelajaran berhasil disiapkan. Riwayat siswa sudah terhubung dengan kelas master.'
                );
        }

        return redirect()
            ->route('classes.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail kelas.
     */
    public function show(Request $request, SchoolClass $class)
    {
        $this->assertTeacherCanAccessClass($request, $class);
        $class->load([
            'academicYear',
            'homeroomTeacher',
            'homeroomTeacherUser.employee',
            'subjects',
        ]);

        $students = $class->students()
            ->wherePivot('status', 'Aktif')
            ->orderBy('name')
            ->paginate(
                10,
                ['*'],
                'students_page'
            )
            ->withQueryString();

        return view(
            'admin.classes.show',
            compact(
                'class',
                'students'
            )
        );
    }

    /**
     * Form edit kelas.
     */
    public function edit(Request $request, SchoolClass $class)
    {
        $this->assertTeacherCanAccessClass($request, $class);
        $academicYears = AcademicYear::orderByDesc(
            'start_date'
        )->get();

        $teachers = User::query()
            ->whereHas('role', fn ($query) => $query->where('name', 'Guru'))
            ->when($request->user()->isGuru(), fn ($query) => $query->whereKey($request->user()->id))
            ->where(function ($query) use ($class) {
                $query->where('status', 'Aktif')
                    ->orWhereKey($class->homeroom_teacher_user_id);
            })
            ->with('employee')
            ->orderBy('name')
            ->get();

        // Ambil hanya mata pelajaran aktif.
        $subjects = Subject::active()
            ->orderBy('code')
            ->orderBy('id')
            ->get();

        // Ambil mata pelajaran yang sudah dimiliki kelas.
        $class->load(['subjects', 'homeroomTeacher']);

        // ID mata pelajaran yang sudah dipilih.
        $selectedSubjectIds = $class->subjects
            ->pluck('id')
            ->toArray();

        return view(
            'admin.classes.edit',
            compact(
                'class',
                'academicYears',
                'teachers',
                'subjects',
                'selectedSubjectIds'
            )
        );
    }

    /**
     * Memperbarui data kelas.
     */
    public function update(
        Request $request,
        SchoolClass $class
    ) {
        $this->assertTeacherCanAccessClass($request, $class);
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

            'homeroom_teacher_user_id' => ['nullable', 'string'],

            'capacity' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'status' => [
                'required',
                Rule::in([
                    'Aktif',
                    'Nonaktif',
                ]),
            ],

            'subject_ids' => [
                'nullable',
                'array',
            ],

            'subject_ids.*' => [
                'integer',
                Rule::exists(
                    'subjects',
                    'id'
                )->where(function ($query) {
                    $query->where(
                        'status',
                        'Aktif'
                    );
                }),
            ],
        ]);

        $teacherSelection = $validated['homeroom_teacher_user_id'] ?? null;
        if ($request->user()->isGuru()) {
            $isLegacyAssignment = !$class->homeroom_teacher_user_id
                && $request->user()->employee_id
                && (int) $class->homeroom_teacher_id === (int) $request->user()->employee_id;
            abort_unless(
                (string) $teacherSelection === (string) $request->user()->id
                    || ($teacherSelection === 'legacy' && $isLegacyAssignment),
                403,
                'Guru tidak dapat memindahkan kelas ke wali kelas lain.'
            );
            $validated['academic_year_id'] = $class->academic_year_id;
        }
        $keepLegacyEmployeeAssignment = $teacherSelection === 'legacy';
        $teacherUser = $keepLegacyEmployeeAssignment
            ? null
            : $this->resolveActiveTeacherUser($teacherSelection, $class);

        /*
         * Simpan subject_ids secara terpisah.
         */
        $subjectIds = $validated['subject_ids'] ?? [];

        unset($validated['subject_ids'], $validated['homeroom_teacher_user_id']);

        /*
         * Simpan kondisi kelas sebelum perubahan.
         */
        $oldClassValues = $class->getAttributes();

        /*
         * Pastikan kapasitas tidak lebih kecil
         * dari jumlah siswa yang sudah ada.
         */
        $activeStudentCount = $class->students()
            ->wherePivot('status', 'Aktif')
            ->count();

        if (
            $validated['capacity']
            < $activeStudentCount
        ) {
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
            ->where(
                'id',
                '!=',
                $class->id
            )
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
        if ($teacherUser) {
            $teacherAlreadyAssigned = SchoolClass::where(
                'academic_year_id',
                $validated['academic_year_id']
            )->where(function ($query) use ($teacherUser) {
                $query->where('homeroom_teacher_user_id', $teacherUser->id);
                if ($teacherUser->employee_id) {
                    $query->orWhere('homeroom_teacher_id', $teacherUser->employee_id);
                }
            })->where(
                    'id',
                    '!=',
                    $class->id
                )
                ->exists();

            if ($teacherAlreadyAssigned) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'homeroom_teacher_user_id' =>
                            'Guru tersebut sudah menjadi wali kelas pada tahun ajaran ini.',
                    ]);
            }
        }

        /*
         * Update kelas + mata pelajaran
         * dalam satu transaksi.
         */
        DB::transaction(function () use (
            $validated,
            $subjectIds,
            $class,
            $teacherUser,
            $keepLegacyEmployeeAssignment,
            $isTeacher
        ) {
            if ($isTeacher) {
                $validated['homeroom_teacher_user_id'] = $class->homeroom_teacher_user_id;
                $validated['homeroom_teacher_id'] = $class->homeroom_teacher_id;
            } elseif (! $keepLegacyEmployeeAssignment) {
                $validated['homeroom_teacher_user_id'] = $teacherUser?->id;
                $validated['homeroom_teacher_id'] = $teacherUser?->employee_id;
            }

            $class->update(
                $validated
            );

            /*
             * sync() akan:
             * - menambahkan mapel baru
             * - mempertahankan mapel yang masih dipilih
             * - menghapus mapel yang tidak lagi dipilih
             */
            $class->subjects()->sync(
                $subjectIds
            );
        });

        /*
         * Ambil kondisi setelah perubahan.
         */
        $newClassValues = $class
            ->fresh()
            ->getAttributes();

        unset(
            $oldClassValues['created_at'],
            $oldClassValues['updated_at']
        );

        unset(
            $newClassValues['created_at'],
            $newClassValues['updated_at']
        );

        /*
         * Cari field yang benar-benar berubah.
         */
        $changedOldValues = [];
        $changedNewValues = [];

        foreach (
            $newClassValues as $key => $newValue
        ) {
            $oldValue =
                $oldClassValues[$key] ?? null;

            if (
                (string) $oldValue
                !== (string) $newValue
            ) {
                $changedOldValues[$key] =
                    $oldValue;

                $changedNewValues[$key] =
                    $newValue;
            }
        }

        // Catat aktivitas jika ada perubahan.
        if (!empty($changedNewValues)) {
            app(ActivityLogger::class)->log(
                'updated',
                'classes',
                'Mengubah data kelas: ' . $class->name,
                $class->fresh(),
                $changedOldValues,
                $changedNewValues
            );
        }

        return redirect()
            ->route('classes.index')
            ->with(
                'success',
                'Data kelas berhasil diperbarui.'
            );
    }

    private function resolveActiveTeacherUser(
        mixed $teacherUserId,
        ?SchoolClass $class = null
    ): ?User {
        if ($teacherUserId === null || $teacherUserId === '') {
            return null;
        }

        if (
            (! is_string($teacherUserId) && ! is_int($teacherUserId))
            || ! ctype_digit((string) $teacherUserId)
        ) {
            throw ValidationException::withMessages([
                'homeroom_teacher_user_id' => 'Wali kelas harus dipilih dari akun Guru yang tersedia.',
            ]);
        }

        $teacher = User::with('role')->find((int) $teacherUserId);
        $isExistingAssignment = $class
            && (int) $class->homeroom_teacher_user_id === (int) $teacherUserId;

        if (
            ! $teacher
            || ! $teacher->isGuru()
            || ($teacher->status !== 'Aktif' && ! $isExistingAssignment)
        ) {
            throw ValidationException::withMessages([
                'homeroom_teacher_user_id' => 'Wali kelas harus merupakan akun Guru yang aktif.',
            ]);
        }

        return $teacher;
    }

    private function assertTeacherCanAccessClass(Request $request, SchoolClass $class): void
    {
        if ($request->user()->isGuru()) {
            app(TeacherClassAccess::class)->assertCanAccessClass($request->user(), $class);
        }
    }

    /**
     * Menghapus kelas.
     */
    public function destroy(Request $request, SchoolClass $class)
    {
        $this->assertTeacherCanAccessClass($request, $class);
        $activeStudentCount = $class->students()
            ->wherePivot('status', 'Aktif')
            ->count();

        if ($activeStudentCount > 0) {
            return back()->withErrors([
                'delete' =>
                    'Kelas tidak dapat dihapus karena masih memiliki siswa aktif.',
            ]);
        }

        // Simpan data sebelum dihapus.
        $oldClassValues = $class->getAttributes();
        $className = $class->name;

        $class->delete();

        // Catat aktivitas penghapusan.
        app(ActivityLogger::class)->log(
            'deleted',
            'classes',
            'Menghapus data kelas: ' . $className,
            $class,
            $oldClassValues,
            null
        );

        return redirect()
            ->route('classes.index')
            ->with(
                'success',
                'Data kelas berhasil dihapus.'
            );
    }

    /**
     * Menampilkan daftar siswa dalam suatu kelas.
     */
    public function students(Request $request, SchoolClass $class)
    {
        $this->assertTeacherCanAccessClass($request, $class);
        $class->load([
            'academicYear',
            'homeroomTeacher',
        ]);

        $students = $class->students()
            ->wherePivot('status', 'Aktif')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.classes.students',
            compact(
                'class',
                'students'
            )
        );
    }

    /**
     * Form checklist siswa untuk dimasukkan ke kelas.
     */
    public function addStudents(Request $request, SchoolClass $class)
    {
        $this->assertTeacherCanAccessClass($request, $class);
        /*
         * Ambil ID siswa yang sudah memiliki kelas aktif
         * pada tahun ajaran yang sama.
         */
        $assignedStudentIds = ClassStudent::query()
            ->where(
                'status',
                'Aktif'
            )
            ->whereHas(
                'schoolClass',
                function ($query) use ($class) {
                    $query->where(
                        'academic_year_id',
                        $class->academic_year_id
                    );
                }
            )
            ->pluck('student_id')
            ->toArray();

        /*
         * Parameter pencarian siswa.
         */
        $search = $request->input('search');

        /*
         * Query siswa yang BELUM memiliki kelas aktif
         * pada tahun ajaran tersebut dan status Aktif.
         */
        $studentsQuery = Student::query()
            ->whereNotIn(
                'id',
                $assignedStudentIds
            );

        if (!empty($search)) {
            $studentsQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%');
            });
        }

        $students = $studentsQuery
            ->orderBy('name')
            ->get();

        return view(
            'admin.classes.add-students',
            compact(
                'class',
                'students',
                'search'
            )
        );
    }

    /**
     * Menyimpan siswa yang dipilih ke kelas.
     */
    public function storeStudents(
        Request $request,
        SchoolClass $class
    ) {
        $this->assertTeacherCanAccessClass($request, $class);
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

        $studentIds =
            $validated['student_ids'];

        /*
         * Cek kapasitas kelas.
         */
        $currentCount = $class->students()
            ->wherePivot('status', 'Aktif')
            ->count();

        $newStudentCount = count(
            array_diff(
                $studentIds,
                $class->students()
                    ->pluck('students.id')
                    ->toArray()
            )
        );

        if (
            ($currentCount + $newStudentCount)
            > $class->capacity
        ) {
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
            ->whereIn(
                'student_id',
                $studentIds
            )
            ->where(
                'status',
                'Aktif'
            )
            ->whereHas(
                'schoolClass',
                function ($query) use ($class) {
                    $query->where(
                        'academic_year_id',
                        $class->academic_year_id
                    );
                }
            )
            ->where(
                'class_id',
                '!=',
                $class->id
            )
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

        DB::transaction(function () use (
            $studentIds,
            $class
        ) {
            foreach (
                $studentIds as $studentId
            ) {
                $previousAssignments = ClassStudent::where('student_id', $studentId)
                    ->where('status', 'Aktif')
                    ->where('class_id', '!=', $class->id)
                    ->get();

                foreach ($previousAssignments as $previousAssignment) {
                    $previousAssignment->update([
                        'status' => 'Naik Kelas',
                        'exit_date' => now()->toDateString(),
                    ]);
                }

                ClassStudent::updateOrCreate(
                    [
                        'class_id' =>
                            $class->id,
                        'student_id' =>
                            $studentId,
                    ],
                    [
                        'status' =>
                            'Aktif',
                        'entry_date' =>
                            now()->toDateString(),
                        'exit_date' =>
                            null,
                    ]
                );
            }
        });

        // Ambil nama siswa yang dimasukkan.
        $studentNames = Student::whereIn(
            'id',
            $studentIds
        )
            ->orderBy('name')
            ->pluck('name')
            ->implode(', ');

        // Catat aktivitas memasukkan siswa ke kelas.
        app(ActivityLogger::class)->log(
            'students_added',
            'classes',
            'Memasukkan siswa ke kelas ' .
                $class->name .
                ': ' .
                $studentNames,
            $class->fresh(),
            null,
            [
                'student_ids' =>
                    $studentIds,
                'student_names' =>
                    $studentNames,
            ]
        );

        return redirect()
            ->route(
                'classes.students',
                $class
            )
            ->with(
                'success',
                'Siswa berhasil dimasukkan ke kelas.'
            );
    }

    /**
     * Mengeluarkan siswa dari kelas.
     */
    public function removeStudent(
        Request $request,
        SchoolClass $class,
        Student $student
    ) {
        $this->assertTeacherCanAccessClass($request, $class);
        $assignment = ClassStudent::where(
            'class_id',
            $class->id
        )
            ->where(
                'student_id',
                $student->id
            )
            ->where(
                'status',
                'Aktif'
            )
            ->first();

        if (!$assignment) {
            return back()->withErrors([
                'student' =>
                    'Siswa tidak ditemukan dalam kelas ini.',
            ]);
        }

        $assignment->update([
            'status' => 'Pindah',
            'exit_date' =>
                now()->toDateString(),
        ]);

        // Catat aktivitas mengeluarkan siswa dari kelas.
        app(ActivityLogger::class)->log(
            'student_removed',
            'classes',
            'Mengeluarkan siswa ' .
                $student->name .
                ' dari kelas ' .
                $class->name,
            $class->fresh(),
            [
                'student_id' =>
                    $student->id,
                'student_name' =>
                    $student->name,
                'status' =>
                    'Aktif',
            ],
            [
                'student_id' =>
                    $student->id,
                'student_name' =>
                    $student->name,
                'status' =>
                    'Pindah',
                'exit_date' =>
                    $assignment->exit_date,
            ]
        );

        return back()->with(
            'success',
            'Siswa berhasil dikeluarkan dari kelas.'
        );
    }
}
