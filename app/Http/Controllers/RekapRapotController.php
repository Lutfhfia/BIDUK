<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\Employee;
use App\Models\FinalGrade;
use App\Models\RekapAbsensi;
use App\Models\ReportCardGrade;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentExtracurricular;
use App\Models\StudentPromotion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use App\Models\GraduationStatus;
use App\Services\FinalGradeService;
use App\Services\TeacherClassAccess;
use Symfony\Component\HttpFoundation\Response;

class RekapRapotController extends Controller
{
    private array $reportLookupCache = [];

    /**
     * Halaman utama Rekap Rapot.
     */
    public function index(Request $request): View|JsonResponse
    {
        $this->reportLookupCache = [];
        $teacherClassIds = app(TeacherClassAccess::class)->classIds($request->user());
        $teacherAcademicYearIds = $teacherClassIds === null
            ? null
            : SchoolClass::whereIn('id', $teacherClassIds)
                ->distinct()
                ->pluck('academic_year_id');
        $academicYears = AcademicYear::query()
            ->when(
                $teacherAcademicYearIds !== null,
                fn ($query) => $query->whereIn('id', $teacherAcademicYearIds)
            )
            ->orderByDesc('start_date')
            ->get();

        $selectedAcademicYearId = $request->input('academic_year_id')
            ?? ($teacherAcademicYearIds?->contains(AcademicYear::getActive()?->id)
                ? AcademicYear::getActive()?->id
                : $academicYears->first()?->id);

        abort_if(
            $teacherAcademicYearIds !== null
                && $selectedAcademicYearId
                && !$teacherAcademicYearIds->contains((int) $selectedAcademicYearId),
            403,
            'Anda tidak memiliki akses ke tahun ajaran ini.'
        );

        $semesters = Semester::when(
            $selectedAcademicYearId,
            fn ($query) => $query->where('academic_year_id', $selectedAcademicYearId)
        )
            ->orderBy('id')
            ->get();

        // Pilihan tampilan semester tidak bergantung pada semester aktif.
        $semesterMode = $this->normalizeSemesterMode(
            $request->input('semester', 'both')
        );

        $classes = SchoolClass::query()
            ->when(
                $teacherClassIds !== null,
                fn ($query) => $query->whereIn('id', $teacherClassIds)
            )
            ->when(
                $selectedAcademicYearId,
                fn ($query) => $query->where('academic_year_id', $selectedAcademicYearId)
            )
            ->when(
                $teacherClassIds === null,
                fn ($query) => $query->where('status', 'Aktif')
            )
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->input('class_id');
        if (
            $teacherClassIds !== null
            && $selectedClassId !== null
            && $selectedClassId !== ''
            && $selectedClassId !== 'all'
            && !$classes->contains('id', (int) $selectedClassId)
        ) {
            abort_unless(
                $teacherClassIds->contains((int) $selectedClassId),
                403,
                'Anda tidak memiliki akses ke kelas ini.'
            );

            $selectedClassId = 'all';
        }

        $selectedStatus = $request->input(
            'status',
            $teacherClassIds !== null
                && $selectedAcademicYearId
                && AcademicYear::find($selectedAcademicYearId)?->status !== 'active'
                    ? 'Semua'
                    : 'Aktif'
        );
        $search = trim((string) $request->input('search', ''));

        if ($request->boolean('student_lookup')) {
            return $this->studentLookup(
                $search,
                $selectedAcademicYearId,
                $selectedClassId,
                $selectedStatus,
                $teacherClassIds,
                $request->boolean('student_list'),
                min(100000, max(0, (int) $request->input('offset', 0)))
            );
        }

        $students = collect();

        if ($selectedAcademicYearId) {
            $selectedClass = ($selectedClassId !== null && $selectedClassId !== '' && $selectedClassId !== 'all')
                ? $classes->firstWhere('id', (int) $selectedClassId)
                : null;

            $studentsQuery = Student::query();

            if ($selectedClass) {
                $studentsQuery
                    ->whereHas('classAssignments', function ($query) use ($selectedClass, $selectedAcademicYearId) {
                        $query->where('class_id', $selectedClass->id)
                            ->whereHas('schoolClass', fn ($classQuery) => $classQuery
                                ->where('academic_year_id', $selectedAcademicYearId));
                    })
                    ->when(
                        $search !== '',
                        fn ($query) => $query->where(function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%')
                                ->orWhere('nis', 'like', '%' . $search . '%')
                                ->orWhere('nisn', 'like', '%' . $search . '%');
                        })
                    );
            } elseif ($selectedClassId === 'all') {
                if ($teacherClassIds !== null) {
                    $studentsQuery->whereHas(
                        'classAssignments',
                        fn ($query) => $query
                            ->whereIn('class_id', $teacherClassIds)
                            ->whereHas(
                                'schoolClass',
                                fn ($classQuery) => $classQuery->where(
                                    'academic_year_id',
                                    $selectedAcademicYearId
                                )
                            )
                    );
                } else {
                    $studentsQuery->whereHas('classAssignments.schoolClass', function ($query) use ($selectedAcademicYearId) {
                        $query->where('academic_year_id', $selectedAcademicYearId);
                    });
                }

                if ($search !== '') {
                    $studentsQuery->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('nis', 'like', '%' . $search . '%')
                            ->orWhere('nisn', 'like', '%' . $search . '%');
                    });
                }
            } elseif ($search !== '') {
                $studentsQuery
                    ->whereHas('classAssignments', function ($query) use ($selectedAcademicYearId, $teacherClassIds) {
                        $query
                            ->when(
                                $teacherClassIds !== null,
                                fn ($assignmentQuery) => $assignmentQuery->whereIn('class_id', $teacherClassIds)
                            )
                            ->whereHas(
                                'schoolClass',
                                fn ($classQuery) => $classQuery->where('academic_year_id', $selectedAcademicYearId)
                            );
                    })
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('nis', 'like', '%' . $search . '%')
                            ->orWhere('nisn', 'like', '%' . $search . '%');
                    });
            }

            if ($selectedClass || $selectedClassId === 'all' || $search !== '') {
                $this->applyStudentStatus($studentsQuery, $selectedStatus);
                $students = $studentsQuery->orderBy('name')->get();
            }
        }

        return view('reports.rekap-rapot.index', compact(
            'academicYears',
            'semesters',
            'classes',
            'students',
            'selectedAcademicYearId',
            'semesterMode',
            'selectedClassId',
            'selectedStatus',
            'search',
            'teacherClassIds'
        ));
    }

    private function studentLookup(
        string $search,
        int|string|null $academicYearId,
        int|string|null $selectedClassId,
        string $selectedStatus,
        ?\Illuminate\Support\Collection $teacherClassIds,
        bool $studentList,
        int $offset
    ): JsonResponse {
        if ((!$studentList && $search === '') || !$academicYearId) {
            return response()->json($studentList
                ? ['data' => [], 'has_more' => false, 'next_offset' => 0]
                : []);
        }

        $assignmentFilter = function ($query) use (
            $academicYearId,
            $selectedClassId,
            $teacherClassIds
        ) {
            if (is_numeric($selectedClassId)) {
                $query->where('class_id', (int) $selectedClassId);
            } elseif ($teacherClassIds !== null) {
                $query->whereIn('class_id', $teacherClassIds);
            }

            $query->whereHas('schoolClass', fn ($classQuery) => $classQuery
                ->where('academic_year_id', $academicYearId)
                ->when(
                    $teacherClassIds === null,
                    fn ($activeClassQuery) => $activeClassQuery->where('status', 'Aktif')
                ));
        };

        $studentsQuery = Student::query()
            ->when($search !== '', fn ($query) => $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nis', 'like', '%' . $search . '%')
                    ->orWhere('nisn', 'like', '%' . $search . '%');
            }))
            ->whereHas('classAssignments', $assignmentFilter)
            ->with([
                'classAssignments' => function ($query) use ($assignmentFilter) {
                    $assignmentFilter($query);
                    $query->with('schoolClass');
                },
            ]);

        $this->applyStudentStatus($studentsQuery, $selectedStatus);

        $students = $studentsQuery
            ->orderBy('name')
            ->offset($studentList ? $offset : 0)
            ->limit($studentList ? 51 : 10)
            ->get();

        $hasMore = $studentList && $students->count() > 50;
        if ($hasMore) {
            $students->pop();
        }

        $results = $students->map(function (Student $student) {
            $class = $student->classAssignments
                ->first(fn ($assignment) => $assignment->schoolClass !== null)
                ?->schoolClass;

            if (!$class) {
                return null;
            }

            return [
                'id' => $student->id,
                'name' => $student->name,
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'class_name' => $class->name,
                'class_id' => $class->id,
            ];
        })->filter()->values();

        if ($studentList) {
            return response()->json([
                'data' => $results,
                'has_more' => $hasMore,
                'next_offset' => $offset + $results->count(),
            ]);
        }

        return response()->json($results);
    }

    /**
     * Preview/cetak rapor satu siswa.
     */
    public function print(Request $request, Student $student): Response|View
    {
        $this->reportLookupCache = [];
        $academicYearId = (int) $request->input(
            'academic_year_id',
            $this->resolveAcademicYearIdForStudent($student)
        );

        $semesterMode = $this->normalizeSemesterMode(
            $request->input('semester', 'both')
        );

        $classIdInput = $request->input('class_id');
        $classId = ($classIdInput !== null && $classIdInput !== '' && $classIdInput !== 'all')
            ? (int) $classIdInput
            : null;

        $class = $this->resolveStudentClass($student, $academicYearId, $classId);

        abort_unless($class, 404, 'Kelas siswa tidak ditemukan untuk tahun ajaran yang dipilih.');
        app(TeacherClassAccess::class)->assertCanAccessClass($request->user(), $class);

        if ($request->user()?->isGuru()) {
            abort_unless(
                $student->classAssignments()->where('class_id', $class->id)->exists(),
                403,
                'Anda tidak memiliki akses ke siswa tersebut.'
            );
        }

        $report = $this->buildReport(
            $student,
            $class,
            $academicYearId,
            $semesterMode
        );

        $template = $report['isFinalClass']
            ? 'reports.rekap-rapot.kelas-6'
            : 'reports.rekap-rapot.print';

        $view = view($template, $report);

        if ($request->boolean('pdf')) {
            $filename = 'rapot-' . ($student->nis ?: $student->id) . '.pdf';

            return Pdf::loadHTML($view->render())
                ->setOption('defaultMediaType', 'print')
                ->setPaper('a4', 'portrait')
                ->download($filename);
        }

        return $view;
    }

    /**
     * Preview/cetak banyak siswa. class_id diisi untuk per kelas;
     * class_id kosong atau "all" berarti semua kelas.
     */
    public function batch(Request $request): Response|View
    {
        $this->reportLookupCache = [];
        $academicYearId = (int) $request->input(
            'academic_year_id',
            AcademicYear::getActive()?->id
        );

        $semesterMode = $this->normalizeSemesterMode(
            $request->input('semester', 'both')
        );

        $classIdInput = $request->input('class_id');
        $isAllClasses = $classIdInput === null || $classIdInput === '' || $classIdInput === 'all';
        $classId = $isAllClasses ? null : (int) $classIdInput;
        $studentStatus = (string) $request->input('status', 'Aktif');
        $teacherClassIds = app(TeacherClassAccess::class)->classIds($request->user());

        if ($teacherClassIds !== null && $classId !== null) {
            abort_unless(
                $teacherClassIds->contains($classId),
                403,
                'Anda tidak memiliki akses ke kelas ini.'
            );
        }

        $studentsQuery = Student::query()
            ->with(['classes' => function ($query) use ($academicYearId, $classId, $teacherClassIds) {
                $query->where('academic_year_id', $academicYearId)
                    ->when($classId !== null, fn ($classQuery) => $classQuery->whereKey($classId))
                    ->when(
                        $teacherClassIds !== null,
                        fn ($classQuery) => $classQuery->whereIn('classes.id', $teacherClassIds)
                    )
                    ->with([
                        'academicYear',
                        'homeroomTeacher',
                        'homeroomTeacherUser.employee',
                    ]);
            }])
            ->whereHas('classes', function ($query) use ($academicYearId, $classId) {
                $query->where('classes.academic_year_id', $academicYearId)
                    ->when($classId !== null, fn ($q) => $q->where('classes.id', $classId));
            })
            ->when(
                $teacherClassIds !== null,
                fn ($query) => $query->whereHas(
                    'classAssignments',
                    fn ($assignmentQuery) => $assignmentQuery->whereIn('class_id', $teacherClassIds)
                        ->whereHas('schoolClass', fn ($classQuery) => $classQuery
                            ->where('academic_year_id', $academicYearId)
                            ->when($classId !== null, fn ($q) => $q->whereKey($classId)))
                )
            );

        $this->applyStudentStatus($studentsQuery, $studentStatus);

        $students = $studentsQuery->orderBy('name')->get();

        $reportContexts = $students->map(function (Student $student) use (
            $academicYearId,
            $classId,
            $teacherClassIds
        ) {
            $class = $this->resolveStudentClass(
                $student,
                $academicYearId,
                $classId,
                $teacherClassIds
            );

            return $class ? compact('student', 'class') : null;
        })->filter()->values();

        $this->preloadBatchReportData($reportContexts, $academicYearId);

        $reports = $reportContexts->map(fn (array $context) => $this->buildReport(
            $context['student'],
            $context['class'],
            $academicYearId,
            $semesterMode
        ));

        // Urutkan hasil cetak berdasarkan tingkat kelas, nama kelas,
        // lalu nama siswa. orderBy('name') pada query siswa saja tidak
        // menjamin urutan kelas saat mencetak semua kelas.
        $reports = $reports->sort(function (array $left, array $right) {
            $gradeComparison = (int) ($left['class']->grade_level ?? 0)
                <=> (int) ($right['class']->grade_level ?? 0);

            if ($gradeComparison !== 0) {
                return $gradeComparison;
            }

            $classComparison = strnatcasecmp(
                (string) ($left['class']->name ?? ''),
                (string) ($right['class']->name ?? '')
            );

            if ($classComparison !== 0) {
                return $classComparison;
            }

            return strnatcasecmp(
                (string) ($left['student']->name ?? ''),
                (string) ($right['student']->name ?? '')
            );
        })->values();

        $view = view('reports.rekap-rapot.bulk-pdf', compact('reports'));

        if ($request->boolean('pdf', true)) {
            $filename = !$isAllClasses
                ? 'rapot-kelas-' . $classId . '-' . now()->format('Y-m-d-His') . '.pdf'
                : 'rapot-semua-kelas-' . now()->format('Y-m-d-His') . '.pdf';

            return Pdf::loadHTML($view->render())
                ->setOption('defaultMediaType', 'print')
                ->setPaper('a4', 'portrait')
                ->download($filename);
        }

        return $view;
    }

    /**
     * Shortcut unduh semua rapor.
     */
    public function downloadAll(Request $request): Response|View
    {
        $request->merge(['class_id' => 'all', 'pdf' => true]);

        return $this->batch($request);
    }

    private function applyStudentStatus(Builder $query, string $status): void
    {
        if ($status === 'Aktif') {
            $query->where('students.status', 'Aktif');
        } elseif ($status === 'Tidak Aktif') {
            $query->where('students.status', '!=', 'Aktif');
        }
    }

    /**
     * Menyusun seluruh data yang dibutuhkan template rapor.
     */
    private function buildReport(
        Student $student,
        SchoolClass $class,
        int $academicYearId,
        string $semesterMode = 'both'
    ): array {
        $semesterMode = $this->normalizeSemesterMode($semesterMode);
        $academicYear = $class->academicYear;
        if (!$academicYear) {
            $yearCacheKey = 'academic-year:' . $academicYearId;
            if (!array_key_exists($yearCacheKey, $this->reportLookupCache)) {
                $this->reportLookupCache[$yearCacheKey] = AcademicYear::find($academicYearId);
            }
            $academicYear = $this->reportLookupCache[$yearCacheKey];
        }

        $semesterCacheKey = 'semesters:' . $academicYearId;
        if (!array_key_exists($semesterCacheKey, $this->reportLookupCache)) {
            $this->reportLookupCache[$semesterCacheKey] = Semester::where('academic_year_id', $academicYearId)
                ->orderBy('id')
                ->get();
        }
        $semesters = $this->reportLookupCache[$semesterCacheKey];

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

        $batchReportData = $this->reportLookupCache['batch-report-data'] ?? null;
        $reportKey = $student->id.':'.$class->id;
        $grades = $batchReportData !== null
            ? $batchReportData['grades']->get($reportKey, collect())
            : ReportCardGrade::with('subject')
                ->where('student_id', $student->id)
                ->where('class_id', $class->id)
                ->whereIn('semester_id', $semesterIds)
                ->get()
                ->groupBy('semester_id');

        // Urutan harus sama dengan halaman input Nilai Rapot.
        $subjectsCacheKey = 'subjects:' . $class->id;
        if (!array_key_exists($subjectsCacheKey, $this->reportLookupCache)) {
            $this->reportLookupCache[$subjectsCacheKey] = $class->subjects()
                ->where('subjects.status', 'Aktif')
                ->orderBy('subjects.code')
                ->orderBy('subjects.id')
                ->get();
        }
        $subjects = $this->reportLookupCache[$subjectsCacheKey];

        $attendanceGanjil = null;
        if ($semesterGanjil) {
            $attendanceGanjil = $batchReportData !== null
                ? $batchReportData['attendance']->get(
                    $student->id.':'.$class->id.':'.$semesterGanjil->id
                )
                : RekapAbsensi::where([
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'semester_id' => $semesterGanjil->id,
                ])->first();
        }

        $attendanceGenap = null;
        if ($semesterGenap) {
            $attendanceGenap = $batchReportData !== null
                ? $batchReportData['attendance']->get(
                    $student->id.':'.$class->id.':'.$semesterGenap->id
                )
                : RekapAbsensi::where([
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'semester_id' => $semesterGenap->id,
                ])->first();
        }

        // Data keputusan kenaikan disimpan per siswa, kelas, dan tahun ajaran.
        $promotionKey = $student->id.':'.$class->id.':'.$academicYearId;
        $promotion = $batchReportData !== null
            ? $batchReportData['promotions']->get($promotionKey)
            : StudentPromotion::where([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'academic_year_id' => $academicYearId,
            ])->first();

        $achievements = $batchReportData !== null
            ? $batchReportData['achievements']->get($reportKey, collect())
            : Achievement::where([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'academic_year_id' => $academicYearId,
            ])
                ->orderBy('id')
                ->limit(3)
                ->get();

        $extracurriculars = $batchReportData !== null
            ? $batchReportData['extracurriculars']->get($reportKey, collect())
            : StudentExtracurricular::where([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'academic_year_id' => $academicYearId,
            ])->orderBy('position')->get();

        $homeroomTeacher = $class->homeroomTeacherUser?->loadMissing('employee')
            ?? $class->homeroomTeacher;
        $homeroomTeacher = $homeroomTeacher?->employee ?? $homeroomTeacher;

        if (!array_key_exists('headmaster', $this->reportLookupCache)) {
            $this->reportLookupCache['headmaster'] = Employee::query()
                ->where('status', 'Aktif')
                ->where(function ($query) {
                    $query->where('position', 'like', '%Kepala Sekolah%')
                        ->orWhere('position', 'like', '%Kepala%');
                })
                ->orderBy('id')
                ->first();
        }
        $headmaster = $this->reportLookupCache['headmaster'];

        if (!array_key_exists('school-profile', $this->reportLookupCache)) {
            $this->reportLookupCache['school-profile'] = $this->schoolProfile();
        }
        $schoolProfile = $this->reportLookupCache['school-profile'];
        $gradeLevel = (int) $class->grade_level;
        $isFinalClass = $gradeLevel === 6;


        // Data tambahan khusus untuk Rekap Rapor Kelas 6.
        // Kelas 1-5 tetap menggunakan template rapor lama.
        $ijazahMapping = collect();
        $finalGrades = collect();
        $reportAverageScores = [];
        $graduationStatus = null;

        if ($isFinalClass) {
            $finalGradeService = app(FinalGradeService::class);

            // Daftar mata pelajaran dan nilai akhir yang sudah tersimpan.
            if (!array_key_exists('ijazah-mapping', $this->reportLookupCache)) {
                $this->reportLookupCache['ijazah-mapping'] = $finalGradeService->getIjazahSubjectMapping();
            }
            $ijazahMapping = $this->reportLookupCache['ijazah-mapping'];

            $finalGrades = $batchReportData !== null
                ? $batchReportData['final-grades']->get($reportKey, collect())
                : $finalGradeService->getStoredGrades(
                    $student->id,
                    $class->id,
                    $academicYearId
                );

            // Ambil rata-rata rapor dari perhitungan yang sudah digunakan
            // oleh fitur Nilai Akhir/Ijazah.
           // Nilai rata-rata rapor mengikuti mata pelajaran aktif
// yang tersedia pada kelas di fitur Mata Pelajaran.
            $schoolScoreResults = $finalGradeService->calculateSchoolScores(
                $student,
                $subjects->pluck('id')
            );
            foreach ($subjects as $subject) {
                $reportResult = $schoolScoreResults[$subject->id];
    $reportAverageScores[$subject->id] = $reportResult['score'];
            }

            // Ambil status kelulusan siswa pada kelas dan tahun ajaran ini.
            $graduationStatus = $batchReportData !== null
                ? $batchReportData['graduation-statuses']->get($promotionKey)
                : GraduationStatus::where([
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'academic_year_id' => $academicYearId,
                ])->first();
        }

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
            'extracurriculars',
            'homeroomTeacher',
            'headmaster',
            'schoolProfile',
            'isFinalClass',
            'ijazahMapping',
'finalGrades',
'reportAverageScores',
'graduationStatus'
        );
    }

    /**
     * Load per-student report data in a few batch queries instead of querying
     * every grade, attendance record, promotion, and achievement individually.
     */
    private function preloadBatchReportData(Collection $contexts, int $academicYearId): void
    {
        $this->reportLookupCache['batch-report-data'] = [
            'grades' => collect(),
            'attendance' => collect(),
            'promotions' => collect(),
            'achievements' => collect(),
            'extracurriculars' => collect(),
            'final-grades' => collect(),
            'graduation-statuses' => collect(),
        ];

        if ($contexts->isEmpty()) {
            return;
        }

        $classIds = $contexts->pluck('class.id')->unique()->values();
        $studentIds = $contexts->pluck('student.id')->unique()->values();
        $finalClassContexts = $contexts->filter(
            fn (array $context) => (int) $context['class']->grade_level === 6
        );
        $semesters = $this->reportLookupCache['semesters:'.$academicYearId]
            ?? Semester::where('academic_year_id', $academicYearId)->orderBy('id')->get();
        $semesterIds = $semesters->pluck('id');
        $pairKey = fn ($record) => $record->student_id.':'.$record->class_id;
        $tripletKey = fn ($record) => $record->student_id.':'.$record->class_id.':'.$record->academic_year_id;

        $this->reportLookupCache['semesters:'.$academicYearId] = $semesters;

        $batchData = [
            'grades' => ReportCardGrade::with('subject')
                ->whereIn('student_id', $studentIds)
                ->whereIn('class_id', $classIds)
                ->whereIn('semester_id', $semesterIds)
                ->get()
                ->groupBy($pairKey)
                ->map(fn (Collection $grades) => $grades->groupBy('semester_id')),
            'attendance' => RekapAbsensi::whereIn('student_id', $studentIds)
                ->whereIn('class_id', $classIds)
                ->whereIn('semester_id', $semesterIds)
                ->get()
                ->keyBy(fn (RekapAbsensi $record) => $record->student_id.':'.$record->class_id.':'.$record->semester_id),
            'promotions' => StudentPromotion::whereIn('student_id', $studentIds)
                ->whereIn('class_id', $classIds)
                ->where('academic_year_id', $academicYearId)
                ->get()
                ->keyBy($tripletKey),
            'achievements' => Achievement::whereIn('student_id', $studentIds)
                ->whereIn('class_id', $classIds)
                ->where('academic_year_id', $academicYearId)
                ->orderBy('id')
                ->get()
                ->groupBy($pairKey)
                ->map(fn (Collection $achievements) => $achievements->take(3)->values()),
            'extracurriculars' => StudentExtracurricular::whereIn('student_id', $studentIds)
                ->whereIn('class_id', $classIds)
                ->where('academic_year_id', $academicYearId)
                ->orderBy('position')
                ->get()
                ->groupBy($pairKey),
            'final-grades' => collect(),
            'graduation-statuses' => collect(),
        ];

        if ($finalClassContexts->isNotEmpty()) {
            $finalStudentIds = $finalClassContexts->pluck('student.id')->unique()->values();
            $finalClassIds = $finalClassContexts->pluck('class.id')->unique()->values();

            $batchData['final-grades'] = FinalGrade::whereIn('student_id', $finalStudentIds)
                ->whereIn('class_id', $finalClassIds)
                ->where('academic_year_id', $academicYearId)
                ->get()
                ->groupBy($pairKey)
                ->map(fn (Collection $grades) => $grades->keyBy('subject_id'));

            $batchData['graduation-statuses'] = GraduationStatus::whereIn('student_id', $finalStudentIds)
                ->whereIn('class_id', $finalClassIds)
                ->where('academic_year_id', $academicYearId)
                ->get()
                ->keyBy($tripletKey);
        }

        $this->reportLookupCache['batch-report-data'] = $batchData;
    }

    /**
     * Menormalisasi pilihan tampilan semester untuk cetak rapor.
     */
    private function normalizeSemesterMode(mixed $value): string
    {
        $value = strtolower(trim((string) $value));

        return in_array($value, ['ganjil', 'genap', 'both'], true)
            ? $value
            : 'both';
    }

    /**
     * Menentukan kelas siswa pada tahun ajaran tertentu.
     */
    private function resolveStudentClass(
        Student $student,
        int $academicYearId,
        mixed $classId = null,
        ?Collection $allowedClassIds = null
    ): ?SchoolClass {
        if ($student->relationLoaded('classes')) {
            return $student->classes
                ->filter(fn (SchoolClass $class) => (int) $class->academic_year_id === $academicYearId
                    && ($classId === null || (int) $class->id === (int) $classId)
                    && ($allowedClassIds === null || $allowedClassIds->contains((int) $class->id)))
                ->sortBy([
                    ['grade_level', 'asc'],
                    ['name', 'asc'],
                ])
                ->first();
        }

        return SchoolClass::query()
            ->where('academic_year_id', $academicYearId)
            ->when($classId !== null, fn ($query) => $query->whereKey($classId))
            ->when(
                $allowedClassIds !== null,
                fn ($query) => $query->whereIn('id', $allowedClassIds)
            )
            ->whereHas('classStudents', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with([
                'academicYear',
                'homeroomTeacher',
                'homeroomTeacherUser.employee',
            ])
            ->orderBy('grade_level')
            ->orderBy('name')
            ->first();
    }

    private function resolveAcademicYearIdForStudent(Student $student): int
    {
        return (int) (
            $student->classes()
                ->with('academicYear')
                ->first()?->academic_year_id
            ?? AcademicYear::getActive()?->id
            ?? 0
        );
    }

    /**
     * Mengambil profil sekolah bila tabel tersedia.
     * Fallback tetap aman bila migration school_profiles belum dijalankan.
     */
    private function schoolProfile(): array
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
                // Fallback bila struktur tabel belum tersedia.
            }
        }

        return $profile;
    }
}
