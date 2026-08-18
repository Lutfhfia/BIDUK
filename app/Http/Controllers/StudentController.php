<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentController extends Controller
{
    /**
     * Menampilkan daftar siswa dengan search, filter, dan pagination.
     */
    public function index(Request $request): View
    {
        $search  = $request->input('search');
        $classId = $request->input('class_id');
        $status  = $request->input('status');

        $students = Student::query()
            ->search($search)
            ->filterStatus($status)
            ->filterClass($classId)
            ->with(['classes' => function ($query) {
                $query->whereHas('academicYear', fn($q) => $q->where('status', 'active'));
            }])
            ->orderBy('name', 'asc')
            ->paginate(10)
            ->withQueryString();

        // Ambil kelas pada tahun ajaran aktif untuk dropdown filter
        $classes = SchoolClass::currentYear()
            ->active()
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $statuses = ['Aktif', 'Pindah', 'Lulus', 'Alumni', 'Nonaktif'];

        return view('students.index', compact('students', 'classes', 'statuses', 'search', 'classId', 'status'));
    }

    /**
     * Menampilkan form tambah siswa.
     */
    public function create(): View
    {
        $classes = SchoolClass::currentYear()
            ->active()
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $academicYears = AcademicYear::orderBy('name')->get();
        $semesters = Semester::with('academicYear')->orderBy('academic_year_id')->orderBy('name')->get();

        return view('students.create', compact('classes', 'academicYears', 'semesters'));
    }

    /**
     * Menyimpan data siswa baru.
     */
    public function store(StoreStudentRequest $request): RedirectResponse
    {
        $data = $request->validated();

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

        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail siswa.
     */
    public function show(Student $student): View
    {
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

        return view('students.show', compact('student'));
    }

    /**
     * Menampilkan form edit siswa.
     */
    public function edit(Student $student): View
    {
        $classes = SchoolClass::currentYear()
            ->active()
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $academicYears = AcademicYear::orderBy('name')->get();
        $semesters = Semester::with('academicYear')->orderBy('academic_year_id')->orderBy('name')->get();

        // Ambil kelas aktif siswa saat ini
        $currentClassId = $student->currentClass()->first()?->id;

        return view('students.edit', compact('student', 'classes', 'currentClassId', 'academicYears', 'semesters'));
    }

    /**
     * Mengupdate data siswa.
     */
    public function update(UpdateStudentRequest $request, Student $student): RedirectResponse
    {
        $data = $request->validated();

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

        // Update penempatan kelas jika berubah
        if ($classId) {
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

        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Menghapus data siswa (soft delete).
     */
    public function destroy(Student $student): RedirectResponse
    {
        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
