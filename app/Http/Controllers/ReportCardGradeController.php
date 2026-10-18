<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\ClassStudent;
use App\Models\ReportCardGrade;
use App\Models\SchoolClass;
use App\Models\SchoolProfile;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentClassHistory;
use App\Models\Employee;
use App\Models\RekapAbsensi;
use App\Models\StudentPromotion;
use App\Models\Subject;
use App\Services\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class ReportCardGradeController extends Controller
{
    /**
     * ==========================================================
     * INDEX
     * ==========================================================
     *
     * Alur:
     * Tahun Ajaran -> Kelas -> Daftar Siswa
     *
     * Semester tidak dipilih di halaman index.
     * Ganjil dan Genap dikelola sekaligus di halaman edit.
     */
    public function index(Request $request)
    {
        /*
         * Simpan URL index saat ini (beserta query filter/pencarian/halaman)
         * agar saat kembali dari halaman edit/riwayat/ijazah, filter tetap terjaga.
         */
        session()->put('report_card_grades_index_url', $request->fullUrl());

        /*
         * Daftar Tahun Ajaran.
         */
        $academicYears = AcademicYear::orderByDesc('id')->get();

        /*
         * Parameter filter.
         */
        $search = $request->input('search');
        $selectedAcademicYearId = $request->input('academic_year_id');
        $selectedClassId = $request->input('class_id');

        /*
         * Ambil daftar kelas untuk dropdown filter.
         */
        $classesQuery = SchoolClass::with('academicYear')
            ->where('status', 'Aktif')
            ->orderBy('grade_level')
            ->orderBy('name');

        if ($selectedAcademicYearId) {
            $classesQuery->where('academic_year_id', $selectedAcademicYearId);
        }

        $classes = $classesQuery->get();

        $selectedClass = null;
        $students = collect();
        $searched = true;

        /*
         * Tampilkan seluruh siswa secara default; filter hanya mempersempit daftar.
         */
        if ($searched) {

            if ($selectedClassId) {

                /*
                 * ------------------------------------------------------
                 * FILTER BERDASARKAN KELAS SPESIFIK
                 * ------------------------------------------------------
                 */
                $selectedClass = SchoolClass::with([
                    'academicYear.semesters',
                    'subjects'
                ])
                ->where('status', 'Aktif')
                ->find($selectedClassId);

                if ($selectedClass) {

                    $studentsQuery = $selectedClass->students()
                        ->wherePivot('status', 'Aktif');

                    if ($search) {
                        $studentsQuery->where(function ($q) use ($search) {
                            $q->where('students.name', 'like', "%{$search}%")
                                ->orWhere('students.nis', 'like', "%{$search}%")
                                ->orWhere('students.nisn', 'like', "%{$search}%");
                        });
                    }

                    $semesters = $selectedClass->academicYear?->semesters ?? collect();

                    $semesterGanjil = $semesters->first(
                        fn ($s) => strtolower($s->name) === 'ganjil'
                    );
                    
                    $semesterGenap = $semesters->first(
                        fn ($s) => strtolower($s->name) === 'genap'
                    );
                    $semesterIds = collect([
                        $semesterGanjil?->id,
                        $semesterGenap?->id,
                    ])->filter();

                    $studentsList = $studentsQuery
                        ->orderByRaw('CASE WHEN students.nis IS NULL OR students.nis = \'\' THEN 1 ELSE 0 END')
                        ->orderByRaw('CAST(students.nis AS UNSIGNED)')
                        ->orderBy('students.nis')
                        ->orderBy('students.id')
                        ->get();

                    if ($semesterIds->isNotEmpty() && $studentsList->isNotEmpty()) {

                        $grades = ReportCardGrade::where('class_id', $selectedClass->id)
                            ->whereIn('semester_id', $semesterIds)
                            ->whereIn('student_id', $studentsList->pluck('id'))
                            ->get()
                            ->groupBy('student_id');

                        $studentsList->transform(function ($student) use ($grades, $semesterGanjil, $semesterGenap, $selectedClass) {
                            $studentGrades = $grades->get($student->id, collect());
                            $student->target_class = $selectedClass;
                            $student->has_ganjil = (bool) $semesterGanjil;
                            $student->has_genap = (bool) $semesterGenap;
                            $student->ganjil_grades_count = $semesterGanjil
                                ? $studentGrades->where('semester_id', $semesterGanjil->id)->count()
                                : 0;
                            $student->genap_grades_count = $semesterGenap
                                ? $studentGrades->where('semester_id', $semesterGenap->id)->count()
                                : 0;
                            return $student;
                        });

                    } else {

                        $studentsList->transform(function ($student) use ($semesterGanjil, $semesterGenap, $selectedClass) {
                            $student->target_class = $selectedClass;
                            $student->has_ganjil = (bool) $semesterGanjil;
                            $student->has_genap = (bool) $semesterGenap;
                            $student->ganjil_grades_count = 0;
                            $student->genap_grades_count = 0;
                            return $student;
                        });

                    }

                    $students = $studentsList;
                }

            } else {

                /*
                 * ------------------------------------------------------
                 * PENCARIAN SISWA (TANPA PILIH KELAS SPESIFIK)
                 * ------------------------------------------------------
                 */
                $query = Student::query();

                if ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%")
                            ->orWhere('nisn', 'like', "%{$search}%");
                    });
                }

                if ($selectedAcademicYearId) {
                    $query->whereHas('classes', function ($q) use ($selectedAcademicYearId) {
                        $q->where('academic_year_id', $selectedAcademicYearId);
                    });
                }

                $query->with([
                    'classes' => function ($q) use ($selectedAcademicYearId) {
                        if ($selectedAcademicYearId) {
                            $q->where('academic_year_id', $selectedAcademicYearId);
                        }
                        $q->with('academicYear.semesters')
                            ->orderByDesc('academic_year_id');
                    },
                    'reportCardGrades'
                ]);

                $paginatedStudents = $query
                    ->orderByRaw('CASE WHEN nis IS NULL OR nis = \'\' THEN 1 ELSE 0 END')
                    ->orderByRaw('CAST(nis AS UNSIGNED)')
                    ->orderBy('nis')
                    ->orderBy('id')
                    ->paginate(20)
                    ->withQueryString();

                $paginatedStudents->getCollection()->transform(function ($student) use ($selectedAcademicYearId) {
                    $targetClass = null;

                    if ($selectedAcademicYearId) {
                        $targetClass = $student->classes
                            ->where('academic_year_id', $selectedAcademicYearId)
                            ->first(fn ($c) => ($c->pivot->status ?? '') === 'Aktif')
                            ?? $student->classes
                                ->where('academic_year_id', $selectedAcademicYearId)
                                ->first();
                    }

                    if (!$targetClass) {
                        $targetClass = $student->classes
                            ->first(fn ($c) => ($c->pivot->status ?? '') === 'Aktif')
                            ?? $student->classes->first();
                    }

                    $student->target_class = $targetClass;

                    if ($targetClass) {
                        $semesters = $targetClass->academicYear?->semesters ?? collect();
                        $semesterGanjil = $semesters->first(fn ($s) => strtolower($s->name) === 'ganjil');
                        $semesterGenap = $semesters->first(fn ($s) => strtolower($s->name) === 'genap');

                        $student->has_ganjil = (bool) $semesterGanjil;
                        $student->has_genap = (bool) $semesterGenap;

                        $student->ganjil_grades_count = $semesterGanjil
                            ? $student->reportCardGrades
                                ->where('class_id', $targetClass->id)
                                ->where('semester_id', $semesterGanjil->id)
                                ->count()
                            : 0;

                        $student->genap_grades_count = $semesterGenap
                            ? $student->reportCardGrades
                                ->where('class_id', $targetClass->id)
                                ->where('semester_id', $semesterGenap->id)
                                ->count()
                            : 0;
                    } else {
                        $student->has_ganjil = false;
                        $student->has_genap = false;
                        $student->ganjil_grades_count = 0;
                        $student->genap_grades_count = 0;
                    }

                    return $student;
                });

                $students = $paginatedStudents;
            }
        }

        /*
         * PENTING:
         * index mengembalikan halaman index,
         * bukan halaman edit.
         */
        return view(
            'admin.report-card-grades.index',
            compact(
                'academicYears',
                'classes',
                'students',
                'selectedAcademicYearId',
                'selectedClassId',
                'selectedClass',
                'searched',
                'search'
            )
        );
    }

    /**
     * ==========================================================
     * RIWAYAT NILAI RAPOT SISWA (KELAS 1 - 6)
     * ==========================================================
     *
     * Menampilkan riwayat nilai rapot satu siswa dari kelas 1 sampai 6
     * berdasarkan kelas dan tahun ajaran yang pernah ditempuh siswa.
     */
    public function history(Request $request, Student $student)
    {
        $student->load([
            'classAssignments.schoolClass.academicYear',
            'reportCardGrades'
        ]);

        $classAssignments = $student->classAssignments()
            ->with('schoolClass.academicYear')
            ->orderByDesc('created_at')
            ->get();
        $manualHistories = $student->classHistories()
            ->get()
            ->keyBy('grade_level');

        $currentActiveAssignment = $classAssignments->first(fn ($assignment) => $assignment->status === 'Aktif');
        $currentGradeLevel = $currentActiveAssignment?->schoolClass?->grade_level;

        $assignmentsByGrade = $classAssignments
            ->filter(fn ($assignment) => $assignment->schoolClass && $assignment->schoolClass->grade_level !== null)
            ->groupBy(fn ($assignment) => (int) $assignment->schoolClass->grade_level);
        $masterClassesByGrade = SchoolClass::with('academicYear')
            ->whereBetween('grade_level', [1, 6])
            ->get()
            ->groupBy('grade_level');
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $gradeLevelsData = [];

        for ($level = 1; $level <= 6; $level++) {
            $manualHistory = $manualHistories->get($level);
            $matchingAssignments = $assignmentsByGrade->get($level, collect());
            $historyClasses = $masterClassesByGrade->get($level, collect())
                ->filter(fn ($historyClass) => $historyClass->academicYear)
                ->sortBy([
                    ['academicYear.start_date', 'desc'],
                    ['name', 'asc'],
                ])
                ->values();

            $levelAssignment = $matchingAssignments
                ->sortByDesc(fn ($assignment) => $assignment->schoolClass?->academicYear?->start_date?->timestamp ?? $assignment->entry_date?->timestamp ?? 0)
                ->first();

            $class = null;
            if ($manualHistory) {
                $class = SchoolClass::with(['academicYear.semesters', 'subjects'])
                    ->where('grade_level', $level)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($manualHistory->rombel))])
                    ->whereHas('academicYear', function ($query) use ($manualHistory) {
                        $query->where('name', trim($manualHistory->academic_year));
                    })
                    ->first();

                $levelAssignment = $class
                    ? $matchingAssignments->firstWhere('class_id', $class->id)
                    : null;
            } else {
                $class = $levelAssignment?->schoolClass;
            }

            if (!$class) {
                $gradeLevelsData[$level] = [
                    'level' => $level,
                    'has_class' => (bool) $manualHistory,
                    'is_assigned' => false,
                    'class' => null,
                    'academic_year' => null,
                    'history_entry' => null,
                    'manual_history' => $manualHistory,
                    'history_classes' => $historyClasses,
                    'has_subjects' => false,
                    'academic_year_master' => $manualHistory
                        ? AcademicYear::where('name', trim($manualHistory->academic_year))->first()
                        : null,
                    'has_ganjil' => false,
                    'has_genap' => false,
                    'ganjil_count' => 0,
                    'genap_count' => 0,
                    'has_data' => false,
                ];

                continue;
            }

            $academicYear = $class->academicYear;
            $semesters = $academicYear?->semesters ?? collect();
            $semGanjil = $semesters->first(fn ($s) => strtolower($s->name) === 'ganjil');
            $semGenap = $semesters->first(fn ($s) => strtolower($s->name) === 'genap');

            $ganjilCount = $semGanjil
                ? ReportCardGrade::where('student_id', $student->id)
                    ->where('class_id', $class->id)
                    ->where('semester_id', $semGanjil->id)
                    ->count()
                : 0;

            $genapCount = $semGenap
                ? ReportCardGrade::where('student_id', $student->id)
                    ->where('class_id', $class->id)
                    ->where('semester_id', $semGenap->id)
                    ->count()
                : 0;

            $gradeLevelsData[$level] = [
                'level' => $level,
                'has_class' => true,
                'is_assigned' => $levelAssignment?->status === 'Aktif',
                'class' => $class,
                'academic_year' => $academicYear,
                'history_entry' => $levelAssignment,
                'manual_history' => $manualHistory,
                'history_classes' => $historyClasses,
                'has_subjects' => $class->subjects->contains(fn ($subject) => $subject->status === 'Aktif'),
                'academic_year_master' => $academicYear,
                'has_ganjil' => (bool) $semGanjil,
                'has_genap' => (bool) $semGenap,
                'ganjil_count' => $ganjilCount,
                'genap_count' => $genapCount,
                'has_data' => ($ganjilCount > 0 || $genapCount > 0),
            ];
        }

        $returnUrl = $request->input('return_url')
            ?: session('report_card_grades_index_url')
            ?: route('report-card-grades.index');

        return view(
            'admin.report-card-grades.history',
            compact(
                'student',
                'gradeLevelsData',
                'academicYears',
                'returnUrl',
                'currentGradeLevel'
            )
        );
    }

    public function storeClassHistory(Request $request, Student $student)
    {
        $validated = $request->validate([
            'history_id' => [
                'nullable',
                'integer',
                Rule::exists('student_class_histories', 'id')->where('student_id', $student->id),
            ],
            'grade_level' => ['required', 'integer', 'between:1,6'],
            'academic_year_id' => ['required', 'integer', Rule::exists('academic_years', 'id')],
            'class_id' => [
                'required',
                'integer',
                Rule::exists('classes', 'id')
                    ->where('academic_year_id', $request->input('academic_year_id'))
                    ->where('grade_level', $request->input('grade_level')),
            ],
            'final_status' => ['required', 'in:Naik Kelas,Tinggal Kelas,Belum Diketahui'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $selectedClass = SchoolClass::with('academicYear')
            ->whereKey($validated['class_id'])
            ->where('academic_year_id', $validated['academic_year_id'])
            ->where('grade_level', $validated['grade_level'])
            ->firstOrFail();
        $selectedAcademicYear = $selectedClass->academicYear;

        $activeAssignment = $student->classAssignments()
            ->with('schoolClass.academicYear')
            ->where('status', 'Aktif')
            ->first();

        $currentGradeLevel = (int) ($activeAssignment?->schoolClass?->grade_level ?? 0);
        $currentAcademicYear = $activeAssignment?->schoolClass?->academicYear;

        if ($currentGradeLevel > 0 && (int) $validated['grade_level'] === $currentGradeLevel) {
            return back()
                ->withInput()
                ->withErrors([
                    'grade_level' => 'Kelas aktif siswa dikelola melalui penempatan kelas. Form ini hanya untuk melengkapi riwayat kelas sebelumnya.',
                ]);
        }

        if (
            $currentGradeLevel > 0
            && $validated['grade_level'] < $currentGradeLevel
            && $currentAcademicYear
            && $selectedAcademicYear->start_date >= $currentAcademicYear->start_date
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'academic_year_id' => 'Riwayat kelas ' . $validated['grade_level'] . ' harus menggunakan tahun ajaran sebelum tahun ajaran kelas aktif siswa (' . $currentAcademicYear->name . ').',
                ]);
        }

        $historyEntry = null;

        if (!empty($validated['history_id'])) {
            $historyEntry = StudentClassHistory::where('id', $validated['history_id'])
                ->where('student_id', $student->id)
                ->first();
        }

        if ($historyEntry && (int) $historyEntry->grade_level !== (int) $validated['grade_level']) {
            return back()
                ->withInput()
                ->withErrors([
                    'history_id' => 'Riwayat yang akan diedit tidak sesuai dengan tingkatan kelas yang dipilih.',
                ]);
        }

        if (!$historyEntry && $student->classHistories()->where('grade_level', $validated['grade_level'])->exists()) {
            return back()
                ->withInput()
                ->withErrors([
                    'history_id' => 'Riwayat kelas ini sudah tersimpan. Gunakan opsi edit pada kartu riwayat.',
                ]);
        }

        $hasConflictingGradeInAcademicYear = StudentClassHistory::query()
            ->where('student_id', $student->id)
            ->when($historyEntry, fn ($query) => $query->where('id', '!=', $historyEntry->id))
            ->where('academic_year', $selectedAcademicYear->name)
            ->where('grade_level', '!=', $validated['grade_level'])
            ->exists();

        $hasConflictingMasterAssignment = $student->classAssignments()
            ->whereHas('schoolClass', function ($query) use ($validated) {
                $query->where('academic_year_id', $validated['academic_year_id'])
                    ->where('id', '!=', $validated['class_id']);
            })
            ->exists();

        if ($hasConflictingGradeInAcademicYear || $hasConflictingMasterAssignment) {
            return back()
                ->withInput()
                ->withErrors([
                    'academic_year_id' => 'Siswa sudah memiliki penempatan kelas lain pada tahun ajaran ini. Periksa kembali tahun ajaran, rombel, dan dokumen sekolah.',
                ]);
        }

        $historyData = [
            'academic_year' => $selectedAcademicYear->name,
            'rombel' => $selectedClass->name,
            'final_status' => $validated['final_status'],
            'notes' => trim((string) ($validated['notes'] ?? '')) !== '' ? trim((string) ($validated['notes'] ?? '')) : null,
        ];
        $oldValues = $historyEntry?->only([
            'grade_level',
            'academic_year',
            'rombel',
            'final_status',
            'notes',
        ]);
        $action = $historyEntry ? 'updated' : 'created';

        DB::transaction(function () use ($student, $historyEntry, $validated, $historyData, $oldValues, $action) {
            if ($historyEntry) {
                $historyEntry->update($historyData);
            } else {
                $historyEntry = $student->classHistories()->create([
                    'grade_level' => $validated['grade_level'],
                    ...$historyData,
                ]);
            }

            app(ActivityLogger::class)->log(
                $action,
                'student_class_histories',
                ($action === 'created' ? 'Menambahkan' : 'Memperbarui')
                    . ' riwayat kelas ' . $validated['grade_level'] . ' siswa: ' . $student->name,
                $historyEntry,
                $oldValues,
                $historyEntry->only([
                    'grade_level',
                    'academic_year',
                    'rombel',
                    'final_status',
                    'notes',
                ])
            );
        });

        return redirect()->route('report-card-grades.history', ['student' => $student->id])
            ->with('success', 'Riwayat kelas berhasil disimpan.');
    }


    /**
     * ==========================================================
     * CETAK RAPOT (TAMPILAN REKAP RAPOT KURIKULUM MERDEKA)
     * ==========================================================
     *
     * Menghasilkan tampilan atau PDF rapot siswa dengan format
     * identik seperti pada fitur Rekap Rapot.
     *
     * URL: /report-card-grades/{class}/{student}/print
     */
    public function printHistory(
        Request $request,
        SchoolClass $class,
        Student $student
    ) {
        if (!$student->exists) {
            abort(404, 'Siswa tidak ditemukan.');
        }

        $academicYearId = (int) (
            $request->input('academic_year_id')
            ?: ($class->academic_year_id ?: ($class->academicYear?->id ?: AcademicYear::getActive()?->id ?: 0))
        );

        $semesterMode = $request->input('semester', 'both');

        $reportData = $this->buildReportData($student, $class, $academicYearId, $semesterMode);
        $view = view('reports.rekap-rapot.print', $reportData);

        if ($request->boolean('pdf')) {
            $filename = 'rapot-' . ($student->nis ?: $student->id) . '-kelas-' . $class->grade_level . '.pdf';

            return Pdf::loadHTML($view->render())
                ->setOption('defaultMediaType', 'print')
                ->setPaper('a4', 'portrait')
                ->download($filename);
        }

        return $view;
    }

    /**
     * Menyusun data laporan rapot identik dengan format Rekap Rapot.
     */
    private function buildReportData(
        Student $student,
        SchoolClass $class,
        int $academicYearId,
        string $semesterMode = 'both'
    ): array {
        $semesterMode = in_array(strtolower(trim($semesterMode)), ['ganjil', 'genap', 'both'], true)
            ? strtolower(trim($semesterMode))
            : 'both';

        $academicYear = AcademicYear::find($academicYearId) ?? $class->academicYear ?? AcademicYear::getActive();

        $semesters = Semester::where('academic_year_id', $academicYearId)
            ->orderBy('id')
            ->get();

        if ($semesters->isEmpty() && AcademicYear::getActive()) {
            $semesters = Semester::where('academic_year_id', AcademicYear::getActive()->id)
                ->orderBy('id')
                ->get();
        }

        $semesterGanjil = $semesters->first(
            fn ($semester) => strtolower($semester->name) === 'ganjil'
        );

        $semesterGenap = $semesters->first(
            fn ($semester) => strtolower($semester->name) === 'genap'
        );

        $semesterIds = collect([
            $semesterGanjil?->id,
            $semesterGenap?->id,
        ])->filter()->values();

        $grades = ReportCardGrade::with('subject')
            ->where('student_id', $student->id)
            ->where('class_id', $class->id)
            ->whereIn('semester_id', $semesterIds)
            ->get()
            ->groupBy('semester_id');

        $subjects = $class->subjects()
            ->where('subjects.status', 'Aktif')
            ->orderBy('subjects.sort_order')
            ->orderBy('subjects.name')
            ->get();

        if ($subjects->isEmpty()) {
            $subjects = Subject::where('status', 'Aktif')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        $attendanceGanjil = $semesterGanjil
            ? RekapAbsensi::where([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'semester_id' => $semesterGanjil->id,
            ])->first()
            : null;

        $attendanceGenap = $semesterGenap
            ? RekapAbsensi::where([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'semester_id' => $semesterGenap->id,
            ])->first()
            : null;

        $promotion = StudentPromotion::where([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $academicYearId,
        ])->first();

        $achievements = Achievement::where([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $academicYearId,
        ])
            ->orderBy('id')
            ->limit(3)
            ->get();

        $homeroomTeacher = $class->homeroom_teacher_id
            ? Employee::find($class->homeroom_teacher_id)
            : ($class->homeroomTeacher ?? null);

        $headmaster = Employee::query()
            ->where('status', 'Aktif')
            ->where(function ($query) {
                $query->where('position', 'like', '%Kepala Sekolah%')
                    ->orWhere('position', 'like', '%Kepala%');
            })
            ->orderBy('id')
            ->first();

        $schoolProfile = $this->getSchoolProfile();
        $gradeLevel = (int) $class->grade_level;
        $isFinalClass = $gradeLevel === 6;

        return compact(
            'student',
            'class',
            'academicYear',
            'semesters',
            'semesterGanjil',
            'semesterGenap',
            'semesterMode',
            'grades',
            'subjects',
            'attendanceGanjil',
            'attendanceGenap',
            'promotion',
            'achievements',
            'homeroomTeacher',
            'headmaster',
            'schoolProfile',
            'isFinalClass'
        );
    }

    /**
     * Profil sekolah untuk kop rapot.
     */
    private function getSchoolProfile(): array
    {
        $profile = [
            'name' => 'SDN 204 Palembang',
            'address' => '',
            'logo' => null,
        ];

        if (Schema::hasTable('school_profiles')) {
            try {
                $row = DB::table('school_profiles')->first();

                if ($row) {
                    $profile['name'] = $row->school_name
                        ?? $row->name
                        ?? $profile['name'];
                    $profile['address'] = $row->address
                        ?? $row->school_address
                        ?? '';
                    $profile['logo'] = $row->logo
                        ?? $row->school_logo
                        ?? null;
                }
            } catch (\Throwable $exception) {
            }
        }

        return $profile;
    }


    /**
     * ==========================================================
     * EDIT
     * ==========================================================
     *
     * Form rapot satu siswa.
     *
     * Ganjil + Genap ditampilkan sekaligus.
     */
    public function edit(
        Request $request,
        SchoolClass $class,
        Student $student
    ) {
        /*
         * Validasi siswa.
         */
        if (!$student->exists) {
            abort(404, 'Siswa tidak ditemukan.');
        }

        /*
         * ======================================================
         * SEMESTER
         * ======================================================
         */

        $semesters = Semester::where(
            'academic_year_id',
            $class->academic_year_id
        )
            ->orderBy('id')
            ->get();

        if ($semesters->isEmpty() && AcademicYear::getActive()) {
            $semesters = Semester::where(
                'academic_year_id',
                AcademicYear::getActive()->id
            )
                ->orderBy('id')
                ->get();
        }

        /*
         * Semester Ganjil.
         */
        $semesterGanjil = $semesters->first(
            fn ($semester) =>
                strtolower($semester->name) === 'ganjil'
        );

        /*
         * Semester Genap.
         */
        $semesterGenap = $semesters->first(
            fn ($semester) =>
                strtolower($semester->name) === 'genap'
        );

        /*
         * ======================================================
         * MATA PELAJARAN
         * ======================================================
         */

        $subjects = $class->subjects()
            ->where('subjects.status', 'Aktif')
            ->orderBy('subjects.sort_order')
            ->orderBy('subjects.name')
            ->get();

        /*
         * ======================================================
         * NILAI RAPOT
         * ======================================================
         */

        $semesterIds = collect([
            $semesterGanjil?->id,
            $semesterGenap?->id,
        ])->filter();

        $grades = collect();

        if ($semesterIds->isNotEmpty()) {

            $grades = ReportCardGrade::where(
                'student_id',
                $student->id
            )
                ->where(
                    'class_id',
                    $class->id
                )
                ->whereIn(
                    'semester_id',
                    $semesterIds
                )
                ->get()
                ->keyBy(function ($grade) {

                    return $grade->semester_id
                        . '_'
                        . $grade->subject_id;
                });
        }

        /*
         * ======================================================
         * ABSENSI GANJIL
         * ======================================================
         */

        $attendanceGanjil = null;

        if ($semesterGanjil) {

            $attendanceGanjil = RekapAbsensi::where([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'semester_id' => $semesterGanjil->id,
            ])->first();
        }

        /*
         * ======================================================
         * ABSENSI GENAP
         * ======================================================
         */

        $attendanceGenap = null;

        if ($semesterGenap) {

            $attendanceGenap = RekapAbsensi::where([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'semester_id' => $semesterGenap->id,
            ])->first();
        }

        /*
         * ======================================================
         * KENAIKAN
         * ======================================================
         */

        $promotion = StudentPromotion::where([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $class->academic_year_id,
        ])->first();

        /*
         * ======================================================
         * PRESTASI
         * ======================================================
         */

        $achievements = Achievement::where(
            'student_id',
            $student->id
        )
            ->where(
                'class_id',
                $class->id
            )
            ->where(
                'academic_year_id',
                $class->academic_year_id
            )
            ->orderBy('id')
            ->limit(3)
            ->get();

        /*
         * Siapkan 3 baris untuk Blade.
         */
        $achievementData = [];

        for ($i = 1; $i <= 3; $i++) {

            $achievement = $achievements->get($i - 1);

            $achievementData[$i] = [
                'type' => $achievement?->type,
                'level' => $achievement?->level,
                'description' => $achievement?->description,
            ];
        }

        /*
         * PENTING:
         * edit mengembalikan halaman edit.
         */
        $returnUrl = $request->input('return_url')
            ?: session('report_card_grades_index_url')
            ?: route('report-card-grades.index', [
                'academic_year_id' => $class->academic_year_id,
                'class_id' => $class->id,
            ]);

        return view(
            'admin.report-card-grades.edit',
            compact(
                'class',
                'student',
                'subjects',
                'semesterGanjil',
                'semesterGenap',
                'grades',
                'attendanceGanjil',
                'attendanceGenap',
                'achievementData',
                'promotion',
                'returnUrl'
            )
        );
    }


    /**
     * ==========================================================
     * UPDATE
     * ==========================================================
     *
     * Menyimpan:
     * - Nilai Ganjil
     * - Nilai Genap
     * - Prestasi
     * - Kenaikan
     */
    public function update(
        Request $request,
        SchoolClass $class,
        Student $student
    ) {
        /*
         * ======================================================
         * VALIDASI SISWA
         * ======================================================
         */

        if (!$student->exists) {
            abort(404, 'Siswa tidak ditemukan.');
        }

        // Jika siswa belum terdaftar di kelas ini, otomatis hubungkan ke kelas
        if (!$class->students()->where('students.id', $student->id)->exists()) {
            $class->students()->attach($student->id, ['status' => 'Aktif']);
        }

        /*
         * ======================================================
         * SEMESTER
         * ======================================================
         */

        $semesters = Semester::where(
            'academic_year_id',
            $class->academic_year_id
        )
            ->get();

        if ($semesters->isEmpty() && AcademicYear::getActive()) {
            $semesters = Semester::where(
                'academic_year_id',
                AcademicYear::getActive()->id
            )
                ->get();
        }

        $semesterGanjil = $semesters->first(
            fn ($semester) =>
                strtolower($semester->name) === 'ganjil'
        );

        $semesterGenap = $semesters->first(
            fn ($semester) =>
                strtolower($semester->name) === 'genap'
        );

        /*
         * ======================================================
         * MATA PELAJARAN
         * ======================================================
         */

         $subjects = $class->subjects()
         ->where('subjects.status', 'Aktif')
         ->orderBy('subjects.sort_order')
         ->orderBy('subjects.name')
         ->get();

        if ($subjects->isEmpty()) {
            return redirect()
                ->route('report-card-grades.edit', [
                    'class' => $class->id,
                    'student' => $student->id,
                ])
                ->with('error', 'Kelas ini belum memiliki mata pelajaran. Atur mata pelajaran kelas terlebih dahulu sebelum menyimpan nilai rapor.');
        }

        /*
         * ======================================================
         * VALIDASI
         * ======================================================
         */

        $rules = [

            /*
             * Ganjil
             */
            'ganjil_scores' => [
                'nullable',
                'array',
            ],

            'ganjil_learning_outcomes' => [
                'nullable',
                'array',
            ],

            /*
             * Genap
             */
            'genap_scores' => [
                'nullable',
                'array',
            ],

            'genap_learning_outcomes' => [
                'nullable',
                'array',
            ],

            /*
             * Ekstrakurikuler
             */
            'extracurricular' => [
                'nullable',
                'array',
            ],

            /*
             * Prestasi
             */
            'achievements' => [
                'nullable',
                'array',
            ],

            'achievements.*.type' => [
                'nullable',
                'string',
                'max:255',
            ],

            'achievements.*.level' => [
                'nullable',
                'string',
                'in:Sekolah,Kecamatan,Kota,Provinsi,Nasional,Internasional',
            ],

            'achievements.*.description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            /*
             * Kenaikan
             */
            'promotion_status' => [
                'nullable',
                'string',
                'in:Naik,Tidak Naik',
            ],

            'promotion_class' => [
                'nullable',
                'string',
                'max:255',
            ],

            'promotion_date' => [
                'nullable',
                'date',
            ],
        ];

        /*
         * Validasi setiap mata pelajaran.
         */
        foreach ($subjects as $subject) {

            /*
             * Nilai Ganjil
             */
            $rules[
                'ganjil_scores.' . $subject->id
            ] = [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ];

            /*
             * Capaian Ganjil
             */
            $rules[
                'ganjil_learning_outcomes.' . $subject->id
            ] = [
                'nullable',
                'string',
                'max:2000',
            ];

            /*
             * Nilai Genap
             */
            $rules[
                'genap_scores.' . $subject->id
            ] = [
                'nullable',
                'numeric',
                'min:0',
                'max:100',
            ];

            /*
             * Capaian Genap
             */
            $rules[
                'genap_learning_outcomes.' . $subject->id
            ] = [
                'nullable',
                'string',
                'max:2000',
            ];
        }

        /*
         * Jalankan validasi.
         */
        $validated = $request->validate($rules);

        /*
         * ======================================================
         * TRANSAKSI DATABASE
         * ======================================================
         */

        DB::transaction(function () use (
            $validated,
            $subjects,
            $student,
            $class,
            $semesterGanjil,
            $semesterGenap
        ) {

            /*
             * ==================================================
             * SEMESTER GANJIL
             * ==================================================
             */

            if ($semesterGanjil) {

                foreach ($subjects as $subject) {

                    $score =
                        $validated[
                            'ganjil_scores'
                        ][$subject->id] ?? null;

                    $learningOutcome =
                        $validated[
                            'ganjil_learning_outcomes'
                        ][$subject->id] ?? null;

                    /*
                     * Jika keduanya kosong,
                     * hapus data lama.
                     */
                    if (
                        ($score === null || $score === '')
                        &&
                        (
                            $learningOutcome === null
                            ||
                            $learningOutcome === ''
                        )
                    ) {

                        ReportCardGrade::where([
                            'student_id' => $student->id,
                            'class_id' => $class->id,
                            'subject_id' => $subject->id,
                            'semester_id' => $semesterGanjil->id,
                        ])->delete();

                        continue;
                    }

                    /*
                     * Simpan / update nilai.
                     */
                    ReportCardGrade::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'class_id' => $class->id,
                            'subject_id' => $subject->id,
                            'semester_id' => $semesterGanjil->id,
                        ],
                        [
                            'score' => $score,
                            'learning_outcome' => $learningOutcome,
                        ]
                    );
                }
            }

            /*
             * ==================================================
             * SEMESTER GENAP
             * ==================================================
             */

            if ($semesterGenap) {

                foreach ($subjects as $subject) {

                    $score =
                        $validated[
                            'genap_scores'
                        ][$subject->id] ?? null;

                    $learningOutcome =
                        $validated[
                            'genap_learning_outcomes'
                        ][$subject->id] ?? null;

                    /*
                     * Jika keduanya kosong,
                     * hapus data lama.
                     */
                    if (
                        ($score === null || $score === '')
                        &&
                        (
                            $learningOutcome === null
                            ||
                            $learningOutcome === ''
                        )
                    ) {

                        ReportCardGrade::where([
                            'student_id' => $student->id,
                            'class_id' => $class->id,
                            'subject_id' => $subject->id,
                            'semester_id' => $semesterGenap->id,
                        ])->delete();

                        continue;
                    }

                    /*
                     * Simpan / update nilai.
                     */
                    ReportCardGrade::updateOrCreate(
                        [
                            'student_id' => $student->id,
                            'class_id' => $class->id,
                            'subject_id' => $subject->id,
                            'semester_id' => $semesterGenap->id,
                        ],
                        [
                            'score' => $score,
                            'learning_outcome' => $learningOutcome,
                        ]
                    );
                }
            }

            /*
             * ==================================================
             * E. PRESTASI
             * ==================================================
             */

            Achievement::where(
                'student_id',
                $student->id
            )
                ->where(
                    'class_id',
                    $class->id
                )
                ->where(
                    'academic_year_id',
                    $class->academic_year_id
                )
                ->delete();

            /*
             * Ambil input prestasi.
             */
            $achievements =
                $validated['achievements'] ?? [];

            /*
             * Simpan prestasi.
             */
            foreach ($achievements as $achievementData) {

                $type = trim(
                    $achievementData['type'] ?? ''
                );

                $level = trim(
                    $achievementData['level'] ?? ''
                );

                $description = trim(
                    $achievementData['description'] ?? ''
                );

                /*
                 * Jika satu baris benar-benar kosong,
                 * jangan buat record database.
                 */
                if (
                    $type === ''
                    &&
                    $level === ''
                    &&
                    $description === ''
                ) {
                    continue;
                }

                Achievement::create([
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'academic_year_id' => $class->academic_year_id,
                    'type' => $type,
                    'level' => $level !== ''
                        ? $level
                        : null,
                    'description' => $description !== ''
                        ? $description
                        : null,
                ]);
            }

            /*
             * ==================================================
             * F. KENAIKAN
             * ==================================================
             */

            $promotionStatus =
                $validated['promotion_status'] ?? null;

            $promotionClass =
                trim($validated['promotion_class'] ?? '');

            $promotionDate =
                $validated['promotion_date'] ?? null;

            /*
             * Jika semua kosong, hapus data kenaikan lama.
             */
            if (
                empty($promotionStatus)
                &&
                $promotionClass === ''
                &&
                empty($promotionDate)
            ) {

                StudentPromotion::where([
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'academic_year_id' => $class->academic_year_id,
                ])->delete();

            } else {

                /*
                 * Simpan / update data kenaikan.
                 */
                StudentPromotion::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'class_id' => $class->id,
                        'academic_year_id' => $class->academic_year_id,
                    ],
                    [
                        'promotion_status' => $promotionStatus,
                        'promotion_class' => $promotionClass !== ''
                            ? $promotionClass
                            : null,
                        'promotion_date' => $promotionDate,
                    ]
                );
            }
        });

        /*
         * ======================================================
         * REDIRECT
         * ======================================================
         */

        $returnUrl = $request->input('return_url')
            ?: session('report_card_grades_index_url')
            ?: route(
                'report-card-grades.index',
                [
                    'academic_year_id' =>
                        $class->academic_year_id,

                    'class_id' =>
                        $class->id,
                ]
            );

        return redirect($returnUrl)
            ->with(
                'success',
                'Data rapot dan prestasi berhasil disimpan.'
            );
    }


    /**
     * ==========================================================
     * DESTROY
     * ==========================================================
     */
    public function destroy(
        Request $request,
        SchoolClass $class,
        Student $student
    ) {
        /*
         * Pastikan siswa berada di kelas.
         */
        $isStudentInClass = $class->students()
            ->where(
                'students.id',
                $student->id
            )
            ->exists();

        if (!$isStudentInClass) {
            abort(404);
        }

        /*
         * Hapus seluruh nilai rapot siswa
         * pada kelas tersebut.
         */
        ReportCardGrade::where(
            'student_id',
            $student->id
        )
            ->where(
                'class_id',
                $class->id
            )
            ->delete();

        /*
         * Hapus seluruh prestasi siswa
         * pada kelas dan tahun ajaran tersebut.
         */
        Achievement::where([
            'student_id' =>
                $student->id,

            'class_id' =>
                $class->id,

            'academic_year_id' =>
                $class->academic_year_id,
        ])->delete();

        /*
         * Kembali ke daftar siswa.
         */
        $returnUrl = $request->input('return_url')
            ?: session('report_card_grades_index_url')
            ?: route(
                'report-card-grades.index',
                [
                    'academic_year_id' =>
                        $class->academic_year_id,

                    'class_id' =>
                        $class->id,
                ]
            );

        return redirect($returnUrl)
            ->with(
                'success',
                'Seluruh nilai rapot dan prestasi '
                . $student->name
                . ' berhasil dihapus.'
            );
    }
}