<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use App\Services\ActivityLogger;

class StudentController extends Controller
{
    /**
     * Menampilkan daftar siswa dengan search, filter, dan pagination.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $teacherClass = $this->teacherClassOrNull();
        $classId = $teacherClass?->id ?? $request->input('class_id');

        // Query dasar ini membatasi seluruh data yang dapat dijangkau Guru.
        $visibleStudents = $this->applyTeacherClassScope(
            Student::query(),
            $teacherClass
        );

        $studentQuery = (clone $visibleStudents)
            ->search($search)
            ->filterStatus($status);

        // Filter kelas pilihan hanya dipakai untuk role selain Guru.
        if (!$teacherClass) {
            $studentQuery->filterClass($classId);
        }

        $students = $studentQuery
            ->with(['classes' => function ($query) use ($teacherClass) {
                $query->where('class_student.status', 'Aktif')
                    ->whereHas('academicYear', function ($yearQuery) {
                        $yearQuery->where('status', 'active');
                    });

                if ($teacherClass) {
                    $query->where('classes.id', $teacherClass->id);
                }
            }])
            ->orderByRaw(
                'CASE WHEN nis IS NULL OR nis = "" THEN 1 ELSE 0 END, CAST(nis AS UNSIGNED) ASC, nis ASC, name ASC'
            )
            ->paginate(10)
            ->withQueryString();

        $classes = $teacherClass
            ? collect([$teacherClass])
            : SchoolClass::currentYear()
                ->active()
                ->orderBy('grade_level')
                ->orderBy('name')
                ->get();

        $statuses = ['Aktif', 'Pindah', 'Lulus', 'Alumni', 'Nonaktif'];

        // Statistik hanya dihitung dari siswa yang boleh dilihat role ini.
        $stats = [
            'total' => (clone $visibleStudents)->count(),
            'active' => (clone $visibleStudents)->where('status', 'Aktif')->count(),
            'male' => (clone $visibleStudents)->where('gender', 'L')->count(),
            'female' => (clone $visibleStudents)->where('gender', 'P')->count(),
        ];

        return view('students.index', compact(
            'students', 'classes', 'statuses', 'search', 'classId', 'status', 'stats'
        ));
    }

    /**
     * Menampilkan form tambah siswa.
     */
    public function create(): View
    {
        $teacherClass = $this->teacherClassOrNull();

        $classes = $teacherClass
            ? collect([$teacherClass])
            : SchoolClass::currentYear()
                ->active()
                ->orderBy('grade_level')
                ->orderBy('name')
                ->get();

        $academicYears = AcademicYear::orderBy('name')->get();
        $semesters = Semester::with('academicYear')
            ->orderBy('academic_year_id')
            ->orderBy('name')
            ->get();

        return view('students.create', compact(
            'classes', 'academicYears', 'semesters'
        ));
    }

    /**
     * Menyimpan data siswa baru.
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $teacherClass = $this->teacherClassOrNull();
        $data = $request->validated();

        // Guru tidak dapat menentukan class_id sendiri melalui request browser.
        if ($teacherClass) {
            $data['class_id'] = $teacherClass->id;
        }

        // Tetapkan 4 kode administratif sebagai template resmi sekolah
        $data['school_code'] = Student::DEFAULT_SCHOOL_CODE;
        $data['district_code'] = Student::DEFAULT_DISTRICT_CODE;
        $data['city_code'] = Student::DEFAULT_CITY_CODE;
        $data['province_code'] = Student::DEFAULT_PROVINCE_CODE;

        // Handle upload foto
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $classId = $data['class_id'] ?? null;
        unset($data['class_id']);

        $physiques = $data['physiques'] ?? [];
        $healths = $data['healths'] ?? [];
        unset($data['physiques'], $data['healths']);

        $previousEducationData = collect([
            'previous_school_name',
            'certificate_date_number',
            'transfer_from_school',
            'transfer_from_grade',
            'transfer_accepted_date',
            'transfer_letter_number',
        ])->mapWithKeys(function ($field) use ($data) {
            return [$field => $data[$field] ?? null];
        })->filter(fn ($value) => !is_null($value) && $value !== '')
          ->all();

        foreach (['previous_school_name', 'certificate_date_number', 'transfer_from_school', 'transfer_from_grade', 'transfer_accepted_date', 'transfer_letter_number'] as $field) {
            unset($data[$field]);
        }

        $student = Student::create($data);

        if (!empty($previousEducationData)) {
            $student->previousEducation()->create($previousEducationData);
        }

        foreach ($physiques as $entry) {
            if (!empty($entry['academic_year_id']) && !empty($entry['semester_id'])) {
                $student->physiques()->updateOrCreate(
                    [
                        'academic_year_id' => $entry['academic_year_id'],
                        'semester_id' => $entry['semester_id'],
                    ],
                    [
                        'height' => $entry['height'] ?? null,
                        'weight' => $entry['weight'] ?? null,
                    ]
                );
            }
        }

        foreach ($healths as $entry) {
            if (!empty($entry['academic_year_id']) && !empty($entry['semester_id'])) {
                $student->healths()->updateOrCreate(
                    [
                        'academic_year_id' => $entry['academic_year_id'],
                        'semester_id' => $entry['semester_id'],
                    ],
                    [
                        'hearing' => $entry['hearing'] ?? null,
                        'vision' => $entry['vision'] ?? null,
                        'teeth' => $entry['teeth'] ?? null,
                        'notes' => $entry['notes'] ?? null,
                    ]
                );
            }
        }

        // Assign ke kelas jika dipilih
        if ($classId) {
            ClassStudent::create([
                'class_id'   => $classId,
                'student_id' => $student->id,
                'status'     => 'Aktif',
                'entry_date' => now()->toDateString(),
            ]);
        }
        // Catat aktivitas penambahan siswa.
app(ActivityLogger::class)->log(
    'created',
    'students',
    'Menambahkan data siswa: ' . $student->name,
    $student,
    null,
    $student->getAttributes()
);

        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail siswa.
     */
    public function show(Request $request, Student $student): View
    {
        $teacherClass = $this->teacherClassOrNull();
        $this->ensureTeacherCanAccessStudent($student, $teacherClass);

        $search = $request->input('search');
        $status = $request->input('status');
        $classId = $teacherClass?->id ?? $request->input('class_id');

        $student->load([
            'classes.academicYear',
            'classes.homeroomTeacher',
            'classAssignments.schoolClass.academicYear',
            'classAssignments.schoolClass.homeroomTeacher',
            'previousEducation',
            'physiques.academicYear',
            'physiques.semester',
            'healths.academicYear',
            'healths.semester',
        ]);

        $baseQuery = Student::query()
            ->search($search)
            ->filterStatus($status);

        $this->applyTeacherClassScope($baseQuery, $teacherClass);

        if (!$teacherClass) {
            $baseQuery->filterClass($classId);
        }

        // Urutan navigasi detail harus memakai kumpulan siswa yang diizinkan.
        $studentIds = (clone $baseQuery)
            ->orderByRaw(
                'CASE WHEN nis IS NULL OR nis = "" THEN 1 ELSE 0 END, CAST(nis AS UNSIGNED) ASC, nis ASC, name ASC, id ASC'
            )
            ->pluck('id')
            ->toArray();

        $currentIndex = array_search($student->id, $studentIds);
        $previousStudent = null;
        $nextStudent = null;

        if ($currentIndex !== false && $currentIndex > 0) {
            $previousStudent = Student::find($studentIds[$currentIndex - 1]);
        }

        if ($currentIndex !== false && isset($studentIds[$currentIndex + 1])) {
            $nextStudent = Student::find($studentIds[$currentIndex + 1]);
        }

        return view('students.show', compact(
            'student', 'previousStudent', 'nextStudent', 'search', 'classId', 'status'
        ));
    }

    /**
     * Menampilkan form edit siswa.
     */
    public function edit(Student $student): View
    {
        $teacherClass = $this->teacherClassOrNull();
        $this->ensureTeacherCanAccessStudent($student, $teacherClass);

        $classes = $teacherClass
            ? collect([$teacherClass])
            : SchoolClass::currentYear()
                ->active()
                ->orderBy('grade_level')
                ->orderBy('name')
                ->get();

        $academicYears = AcademicYear::orderBy('name')->get();
        $semesters = Semester::with('academicYear')
            ->orderBy('academic_year_id')
            ->orderBy('name')
            ->get();

        $currentClassId = $teacherClass?->id
            ?? $student->currentClass()->first()?->id;

        return view('students.edit', compact(
            'student', 'classes', 'currentClassId', 'academicYears', 'semesters'
        ));
    }

    /**
     * Mengupdate data siswa.
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $teacherClass = $this->teacherClassOrNull();
        $this->ensureTeacherCanAccessStudent($student, $teacherClass);

        $data = $request->validated();

        // Guru boleh mengelola siswa kelasnya, tetapi tidak memindahkannya ke kelas lain.
        if ($teacherClass) {
            $data['class_id'] = $teacherClass->id;
        }

        // Tetapkan 4 kode administratif sebagai template resmi sekolah
        $data['school_code'] = Student::DEFAULT_SCHOOL_CODE;
        $data['district_code'] = Student::DEFAULT_DISTRICT_CODE;
        $data['city_code'] = Student::DEFAULT_CITY_CODE;
        $data['province_code'] = Student::DEFAULT_PROVINCE_CODE;

        // Simpan kondisi data siswa sebelum perubahan.
        $oldStudentValues = $student->getAttributes();

        // Handle upload foto
        if ($request->hasFile('photo')) {
            // Hapus foto lama jika ada
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $data['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $classId = $data['class_id'] ?? null;
        unset($data['class_id']);

        $physiques = $data['physiques'] ?? [];
        $healths = $data['healths'] ?? [];
        unset($data['physiques'], $data['healths']);

        $previousEducationData = collect([
            'previous_school_name',
            'certificate_date_number',
            'transfer_from_school',
            'transfer_from_grade',
            'transfer_accepted_date',
            'transfer_letter_number',
        ])->mapWithKeys(function ($field) use ($data) {
            return [$field => $data[$field] ?? null];
        })->filter(fn ($value) => !is_null($value) && $value !== '')
          ->all();

        foreach (['previous_school_name', 'certificate_date_number', 'transfer_from_school', 'transfer_from_grade', 'transfer_accepted_date', 'transfer_letter_number'] as $field) {
            unset($data[$field]);
        }

        $student->update($data);

        if (!empty($previousEducationData)) {
            $student->previousEducation()->updateOrCreate(
                ['student_id' => $student->id],
                $previousEducationData
            );
        } elseif ($student->previousEducation()->exists()) {
            $student->previousEducation()->delete();
        }

        foreach ($physiques as $entry) {
            if (!empty($entry['academic_year_id']) && !empty($entry['semester_id'])) {
                $student->physiques()->updateOrCreate(
                    [
                        'academic_year_id' => $entry['academic_year_id'],
                        'semester_id' => $entry['semester_id'],
                    ],
                    [
                        'height' => $entry['height'] ?? null,
                        'weight' => $entry['weight'] ?? null,
                    ]
                );
            }
        }

        foreach ($healths as $entry) {
            if (!empty($entry['academic_year_id']) && !empty($entry['semester_id'])) {
                $student->healths()->updateOrCreate(
                    [
                        'academic_year_id' => $entry['academic_year_id'],
                        'semester_id' => $entry['semester_id'],
                    ],
                    [
                        'hearing' => $entry['hearing'] ?? null,
                        'vision' => $entry['vision'] ?? null,
                        'teeth' => $entry['teeth'] ?? null,
                        'notes' => $entry['notes'] ?? null,
                    ]
                );
            }
        }

        // Update penempatan kelas jika berubah.
        $currentClassId = $student->currentClass()->first()?->id;
        $shouldUpdateClass = $classId
            && (!$teacherClass || (int) $currentClassId !== (int) $teacherClass->id);

        if ($shouldUpdateClass) {
            $activeYear = AcademicYear::getActive();
            if ($activeYear) {
                // Nonaktifkan penempatan kelas lama pada tahun ajaran aktif
                ClassStudent::where('student_id', $student->id)
                    ->where('status', 'Aktif')
                    ->whereHas('schoolClass', function ($q) use ($activeYear) {
                        $q->where('academic_year_id', $activeYear->id);
                    })
                    ->update([
                        'status'    => 'Naik Kelas',
                        'exit_date' => now()->toDateString(),
                    ]);

                // Buat penempatan baru
                ClassStudent::updateOrCreate(
                    [
                        'class_id'   => $classId,
                        'student_id' => $student->id,
                    ],
                    [
                        'status'     => 'Aktif',
                        'entry_date' => now()->toDateString(),
                        'exit_date'  => null,
                    ]
                );
            }
        }
        // Ambil kondisi terbaru data siswa setelah perubahan.
$newStudentValues = $student->fresh()->getAttributes();

// Ambil hanya field yang benar-benar berubah.
$changedOldValues = [];
$changedNewValues = [];

foreach ($newStudentValues as $key => $newValue) {
    $oldValue = $oldStudentValues[$key] ?? null;

    if ((string) $oldValue !== (string) $newValue) {
        $changedOldValues[$key] = $oldValue;
        $changedNewValues[$key] = $newValue;
    }
}

// Catat aktivitas jika memang ada perubahan.
if (!empty($changedNewValues)) {
    app(ActivityLogger::class)->log(
        'updated',
        'students',
        'Mengubah data siswa: ' . $student->name,
        $student,
        $changedOldValues,
        $changedNewValues
    );
}
        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Menghapus data siswa (soft delete).
     */
    public function destroy(Student $student): RedirectResponse
    {
        $teacherClass = $this->teacherClassOrNull();
        $this->ensureTeacherCanAccessStudent($student, $teacherClass);

        $oldStudentValues = $student->getAttributes();
        $student->delete();
        app(ActivityLogger::class)->log(
            'deleted',
            'students',
            'Menghapus data siswa: ' . $student->name,
            $student,
            $oldStudentValues,
            null
        );

        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Mengembalikan kelas wali Guru pada tahun ajaran aktif.
     * Role selain Guru tidak dibatasi oleh helper ini.
     */
    private function teacherClassOrNull(): ?SchoolClass
    {
        $user = auth()->user();

        if (!$user || !$user->isGuru()) {
            return null;
        }

        $employee = $user->employee;
        $activeYear = AcademicYear::getActive();

        abort_unless(
            $employee && $activeYear,
            403,
            'Data guru atau tahun ajaran aktif tidak ditemukan.'
        );

        $teacherClasses = $employee->homeroomClasses()
            ->where('classes.academic_year_id', $activeYear->id)
            ->where('classes.status', 'Aktif')
            ->whereHas('academicYear', function ($query) {
                $query->where('status', 'active');
            })
            ->get();

        abort_unless(
            $teacherClasses->count() === 1,
            403,
            'Guru harus ditetapkan sebagai wali tepat satu kelas pada tahun ajaran aktif.'
        );

        return $teacherClasses->first();
    }

    /**
     * Menambahkan batas kelas wali pada query siswa.
     */
    private function applyTeacherClassScope(
        Builder $query,
        ?SchoolClass $teacherClass
    ): Builder {
        if (!$teacherClass) {
            return $query;
        }

        return $query->whereHas('classes', function ($classQuery) use ($teacherClass) {
            $classQuery
                ->where('classes.id', $teacherClass->id)
                ->where('classes.academic_year_id', $teacherClass->academic_year_id)
                ->where('classes.status', 'Aktif')
                ->where('class_student.status', 'Aktif');
        });
    }

    /**
     * Menolak akses langsung ke detail, edit, update, atau hapus
     * siswa yang bukan anggota aktif kelas wali Guru.
     */
    private function ensureTeacherCanAccessStudent(
        Student $student,
        ?SchoolClass $teacherClass
    ): void {
        if (!$teacherClass) {
            return;
        }

        $allowed = $student->classes()
            ->where('classes.id', $teacherClass->id)
            ->where('classes.academic_year_id', $teacherClass->academic_year_id)
            ->where('classes.status', 'Aktif')
            ->where('class_student.status', 'Aktif')
            ->exists();

        abort_unless(
            $allowed,
            403,
            'Kamu tidak memiliki akses ke data siswa di luar kelas wali.'
        );
    }

}
