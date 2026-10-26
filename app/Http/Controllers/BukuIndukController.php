<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Services\TeacherClassAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BukuIndukController extends Controller
{
    /**
     * Halaman utama Cetak Buku Induk.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $teacherClassIds = app(TeacherClassAccess::class)->classIds($request->user());
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $selectedAcademicYear = $request->input(
            'academic_year_id',
            AcademicYear::getActive()?->id
        );

        $semesters = Semester::query()
            ->when(
                $selectedAcademicYear,
                fn ($query) => $query->where(
                    'academic_year_id',
                    $selectedAcademicYear
                )
            )
            ->orderBy('name')
            ->get();

        $classes = SchoolClass::query()
            ->when(
                $teacherClassIds !== null,
                fn ($query) => $query->whereIn('id', $teacherClassIds)
            )
            ->when(
                $selectedAcademicYear,
                fn ($query) => $query->where(
                    'academic_year_id',
                    $selectedAcademicYear
                )
            )
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $studentStatus = $request->input('status', 'aktif');

        $studentsQuery = Student::query()
            ->when($studentStatus !== '', function ($query) use ($studentStatus) {
                $query->where(
                    'status',
                    $studentStatus === 'aktif' ? 'Aktif' : 'Nonaktif'
                );
            })
            ->when(
                $request->filled('search'),
                fn ($query) => $query->search($request->search)
            )
            ->whereHas('classAssignments', function ($query) use (
                $request,
                $selectedAcademicYear,
                $teacherClassIds
            ) {
                $query->when(
                    $request->filled('class_id') && $request->input('print_type') !== 'all',
                    function ($query) use ($request) {
                        $query->where('class_id', $request->class_id);
                    }
                )
                    ->whereHas('schoolClass', function ($query) use (
                        $selectedAcademicYear,
                        $teacherClassIds
                    ) {
                        $query->when(
                            $selectedAcademicYear,
                            function ($query) use ($selectedAcademicYear) {
                                $query->where(
                                    'academic_year_id',
                                    $selectedAcademicYear
                                );
                            }
                        );
                        if ($teacherClassIds !== null) {
                            $query->whereIn('id', $teacherClassIds);
                        }
                    });
            })
            ->with([
                'classAssignments' => function ($query) use (
                    $selectedAcademicYear,
                    $teacherClassIds
                ) {
                    $query->whereHas('schoolClass', function ($query) use (
                        $selectedAcademicYear,
                        $teacherClassIds
                    ) {
                        $query->when(
                            $selectedAcademicYear,
                            function ($query) use ($selectedAcademicYear) {
                                $query->where(
                                    'academic_year_id',
                                    $selectedAcademicYear
                                );
                            }
                        );
                        if ($teacherClassIds !== null) {
                            $query->whereIn('id', $teacherClassIds);
                        }
                    });
                },
            ]);

        $this->applyStudentClassOrdering(
            $studentsQuery,
            $selectedAcademicYear ? (int) $selectedAcademicYear : null,
            $request->filled('class_id') && $request->input('print_type') !== 'all'
                ? (int) $request->input('class_id')
                : null
        );

        $students = $studentsQuery->get();

        $selectedStudent = null;
        if ($request->filled('student_id')) {
            $selectedStudent = $students->firstWhere(
                'id',
                (int) $request->input('student_id')
            );

            if ($teacherClassIds !== null && !$selectedStudent) {
                abort(403, 'Anda tidak memiliki akses ke data siswa tersebut.');
            }

            $selectedStudent ??= Student::with([
                'classAssignments.schoolClass.academicYear',
                'classes.academicYear',
            ])->find($request->input('student_id'));
        }

        return view('reports.buku-induk.index', compact(
            'academicYears',
            'semesters',
            'classes',
            'students',
            'selectedStudent',
            'selectedAcademicYear'
        ));
    }

    /**
     * Menampilkan daftar Buku Induk untuk satu kelas atau seluruh kelas.
     */
    public function batch(Request $request): View
    {
        $teacherClassIds = app(TeacherClassAccess::class)->classIds($request->user());
        $selectedAcademicYear = $request->input(
            'academic_year_id',
            AcademicYear::getActive()?->id
        );

        $studentStatus = $request->input('status', 'aktif');

        $studentsQuery = Student::query()
            ->when($studentStatus !== '', function ($query) use ($studentStatus) {
                $query->where(
                    'status',
                    $studentStatus === 'aktif' ? 'Aktif' : 'Nonaktif'
                );
            })
            ->when(
                $request->filled('search'),
                fn ($query) => $query->search($request->search)
            )
            ->whereHas('classAssignments', function ($query) use (
                $request,
                $selectedAcademicYear,
                $teacherClassIds
            ) {
                $query->when(
                    $request->filled('class_id'),
                    function ($query) use ($request) {
                        $query->where('class_id', $request->class_id);
                    }
                )
                    ->whereHas('schoolClass', function ($query) use (
                        $selectedAcademicYear,
                        $teacherClassIds
                    ) {
                        $query->when(
                            $selectedAcademicYear,
                            function ($query) use ($selectedAcademicYear) {
                                $query->where(
                                    'academic_year_id',
                                    $selectedAcademicYear
                                );
                            }
                        );
                        if ($teacherClassIds !== null) {
                            $query->whereIn('id', $teacherClassIds);
                        }
                    });
            });

        $this->applyStudentClassOrdering(
            $studentsQuery,
            $selectedAcademicYear ? (int) $selectedAcademicYear : null,
            $request->filled('class_id') ? (int) $request->input('class_id') : null
        );

        $students = $studentsQuery->get();

        $class = $request->filled('class_id')
            ? SchoolClass::when(
                $teacherClassIds !== null,
                fn ($query) => $query->whereIn('id', $teacherClassIds)
            )->where(
                'academic_year_id',
                $selectedAcademicYear
            )->findOrFail($request->class_id)
            : null;

        return view('reports.buku-induk.batch', compact(
            'students',
            'class',
            'selectedAcademicYear'
        ));
    }

    /**
     * Menampilkan Buku Induk dalam format siap cetak.
     */
    public function print(Request $request, Student $student)
    {
        $reportData = $this->reportData($request, $student);
        $classOrderNumbers = [];
        $this->applyClassOrderNumber(
            $reportData['student'],
            $reportData['class'],
            $classOrderNumbers
        );

        $view = view('reports.buku-induk.print', $reportData);

        if ($request->boolean('pdf')) {
            return Pdf::loadHTML($view->render())
                ->setPaper('a4', 'portrait')
                ->download(
                    'buku-induk-' . ($student->nis ?: $student->id) . '.pdf'
                );
        }

        if ($request->boolean('download')) {
            $filename = 'buku-induk-' . ($student->nis ?: $student->id) . '.html';

            return response()->streamDownload(
                fn () => print($view->render()),
                $filename,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }

        return $view;
    }

    /**
     * Membuat SATU file PDF untuk semua siswa yang dipilih.
     *
     * Setiap siswa terdiri dari 2 halaman Buku Induk.
     * Tidak membuat ZIP dan tidak membuat satu PDF per siswa.
     */
    public function batchPdf(Request $request)
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        $teacherClassIds = app(TeacherClassAccess::class)->classIds($request->user());
        $studentIds = $request->input('student_ids', []);

        if (is_array($studentIds) && count($studentIds)) {
            $selectedAcademicYear = $request->input(
                'academic_year_id',
                AcademicYear::getActive()?->id
            );
            $selectedClassId = $request->input('print_type') === 'all'
                ? null
                : ($request->filled('class_id') ? (int) $request->input('class_id') : null);

            $studentsQuery = Student::query()->whereIn('id', $studentIds);
            if ($teacherClassIds !== null) {
                $studentsQuery->whereHas(
                    'classAssignments',
                    fn ($query) => $query->whereIn('class_id', $teacherClassIds)
                        ->whereHas('schoolClass', fn ($classQuery) => $classQuery
                            ->when($selectedAcademicYear, fn ($q) => $q->where('academic_year_id', $selectedAcademicYear))
                            ->when($selectedClassId, fn ($q) => $q->whereKey($selectedClassId)))
                );
            }
            $this->applyStudentClassOrdering(
                $studentsQuery,
                $selectedAcademicYear ? (int) $selectedAcademicYear : null,
                $selectedClassId
            );
            $students = $studentsQuery->get();
        } else {
            $selectedAcademicYear = $request->input(
                'academic_year_id',
                AcademicYear::getActive()?->id
            );

            $studentStatus = $request->input('status', 'aktif');
            $printType = $request->input('print_type');
            $classId = ($printType === 'all') ? null : $request->input('class_id');
            $search = $request->input('search');

            if ($request->filled('student_id') && $request->filled('search')) {
                $previewStudent = Student::find($request->input('student_id'));
                $previewStudentLabel = $previewStudent
                    ? $previewStudent->name.' ('.($previewStudent->nis ?: '-').')'
                    : null;

                if ($previewStudentLabel !== null && trim($search) === $previewStudentLabel) {
                    $search = null;
                }
            }

            $studentsQuery = Student::query()
                ->when($studentStatus !== '', function ($query) use ($studentStatus) {
                    $query->where(
                        'status',
                        $studentStatus === 'aktif' ? 'Aktif' : 'Nonaktif'
                    );
                })
                ->when(
                    $search !== null && $search !== '',
                    fn ($query) => $query->search($search)
                )
                ->whereHas('classAssignments', function ($query) use (
                    $classId,
                    $selectedAcademicYear,
                    $teacherClassIds
                ) {
                    $query->when(
                        $classId,
                        function ($query) use ($classId) {
                            $query->where('class_id', $classId);
                        }
                    )
                        ->whereHas('schoolClass', function ($query) use (
                            $selectedAcademicYear,
                            $teacherClassIds
                        ) {
                            $query->when(
                                $selectedAcademicYear,
                                function ($query) use ($selectedAcademicYear) {
                                    $query->where(
                                        'academic_year_id',
                                        $selectedAcademicYear
                                    );
                                }
                            );
                            if ($teacherClassIds !== null) {
                                $query->whereIn('id', $teacherClassIds);
                            }
                        });
                });

            $this->applyStudentClassOrdering(
                $studentsQuery,
                $selectedAcademicYear ? (int) $selectedAcademicYear : null,
                $classId ? (int) $classId : null
            );
            $students = $studentsQuery->get();
        }

        abort_if(
            $students->isEmpty(),
            404,
            'Siswa tidak ditemukan.'
        );

        $style = '';
        $studentPages = [];
        $classOrderNumbers = [];

        foreach ($students as $student) {
            $reportData = $this->reportData($request, $student);
            $this->applyClassOrderNumber(
                $reportData['student'],
                $reportData['class'],
                $classOrderNumbers
            );

            $html = view(
                'reports.buku-induk.print',
                $reportData
            )->render();

            /*
             * Ambil CSS satu kali dari template Buku Induk.
             */
            if ($style === '' &&
                preg_match(
                    '/<style[^>]*>(.*?)<\/style>/is',
                    $html,
                    $styleMatch
                )
            ) {
                $style = $styleMatch[1];
            }

            /*
             * Ambil isi BODY saja (yaitu div.page halaman 1 dan 2).
             * Jangan menumpuk <html>, <head>, dan <body>
             * untuk setiap siswa.
             */
            if (preg_match(
                '/<body[^>]*>(.*?)<\/body>/is',
                $html,
                $bodyMatch
            )) {
                $body = trim($bodyMatch[1]);
            } else {
                $body = trim($html);
            }

            /*
             * Setiap siswa menghasilkan 2 blok .page langsung sebagai
             * child dari body. Tidak ada wrapper <div> per siswa agar
             * layout dan page-break Dompdf 100% konsisten dengan
             * cetak individual.
             */
            $studentPages[] = $body;
        }

        $combinedHtml = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Induk</title>
    <style>' . $style . '</style>
</head>
<body class="pdf-export">' . implode('', $studentPages) . '</body>
</html>';

        return Pdf::loadHTML($combinedHtml)
            ->setPaper('a4', 'portrait')
            ->download(
                'buku-induk-' . now()->format('Y-m-d-His') . '.pdf'
            );
    }

    /**
     * Membuat ZIP yang berisi satu PDF untuk setiap siswa terpilih.
     */
    public function downloadAll(Request $request)
    {
        $studentIds = $request->input('student_ids', []);

        abort_unless(
            is_array($studentIds) && count($studentIds),
            422,
            'Tidak ada siswa yang dipilih.'
        );

        $selectedAcademicYear = $request->input(
            'academic_year_id',
            AcademicYear::getActive()?->id
        );
        $selectedClassId = $request->input('print_type') === 'all'
            ? null
            : ($request->filled('class_id') ? (int) $request->input('class_id') : null);

        $studentsQuery = Student::whereIn('id', $studentIds);
        $teacherClassIds = app(TeacherClassAccess::class)->classIds($request->user());
        if ($teacherClassIds !== null) {
            $studentsQuery->whereHas(
                'classAssignments',
                fn ($query) => $query->whereIn('class_id', $teacherClassIds)
                    ->whereHas('schoolClass', fn ($classQuery) => $classQuery
                        ->when($selectedAcademicYear, fn ($q) => $q->where('academic_year_id', $selectedAcademicYear))
                        ->when($selectedClassId, fn ($q) => $q->whereKey($selectedClassId)))
            );
        }
        $this->applyStudentClassOrdering(
            $studentsQuery,
            $selectedAcademicYear ? (int) $selectedAcademicYear : null,
            $selectedClassId
        );
        $students = $studentsQuery->get();

        abort_if(
            $students->isEmpty(),
            404,
            'Siswa tidak ditemukan.'
        );

        if (!class_exists(\ZipArchive::class)) {
            abort(
                500,
                'Ekstensi PHP ZipArchive belum aktif. Aktifkan extension=zip pada PHP yang digunakan Laragon.'
            );
        }

        $zipPath = tempnam(
            storage_path('app'),
            'buku-induk-'
        );

        $zip = new \ZipArchive();

        abort_unless(
            $zipPath &&
            $zip->open(
                $zipPath,
                \ZipArchive::CREATE | \ZipArchive::OVERWRITE
            ) === true,
            500,
            'File ZIP tidak dapat dibuat.'
        );

        try {
            $classOrderNumbers = [];

            foreach ($students as $student) {
                $reportData = $this->reportData(
                    $request,
                    $student
                );
                $this->applyClassOrderNumber(
                    $reportData['student'],
                    $reportData['class'],
                    $classOrderNumbers
                );

                /*
                 * Gunakan render path yang sama dengan export PDF
                 * per individu agar hasil PDF di dalam ZIP konsisten.
                 */
                $view = view(
                    'reports.buku-induk.print',
                    $reportData
                );

                $pdf = Pdf::loadHTML($view->render())
                    ->setPaper('a4', 'portrait')
                    ->output();

                $filename = 'buku-induk-' .
                    ($student->nis ?: $student->id) .
                    '.pdf';

                $zip->addFromString(
                    $filename,
                    $pdf
                );
            }

            $zip->close();
        } catch (\Throwable $exception) {
            $zip->close();
            @unlink($zipPath);

            throw $exception;
        }

        return response()->download(
            $zipPath,
            'buku-induk-' . now()->format('Y-m-d-His') . '.zip',
            ['Content-Type' => 'application/zip']
        )->deleteFileAfterSend(true);
    }

    /**
     * Menyiapkan seluruh data yang digunakan oleh template Buku Induk.
     */
    private function reportData(
        Request $request,
        Student $student
    ): array {
        $student->load([
            'classes.academicYear',
            'classes.homeroomTeacher',
            'classes.homeroomTeacherUser.employee',
            'classAssignments.schoolClass.academicYear',
            'classAssignments.schoolClass.homeroomTeacher',
            'classAssignments.schoolClass.homeroomTeacherUser.employee',
            'previousEducation',
            'physiques.academicYear',
            'physiques.semester',
            'healths.academicYear',
            'healths.semester',
        ]);

        $academicYear = null;

        if ($request->filled('academic_year_id')) {
            $academicYear = AcademicYear::find(
                $request->academic_year_id
            );
        }

        if (!$academicYear) {
            $academicYear = AcademicYear::getActive();
        }

        $class = null;

        if ($request->filled('class_id') && $request->input('print_type') !== 'all') {
            $teacherClassIds = app(TeacherClassAccess::class)->classIds($request->user());
            if ($teacherClassIds !== null) {
                abort_unless(
                    $teacherClassIds->contains((int) $request->input('class_id')),
                    403,
                    'Anda tidak memiliki akses ke kelas tersebut.'
                );
            }

            $class = SchoolClass::with([
                'academicYear',
                'homeroomTeacher',
                'homeroomTeacherUser.employee',
            ])->where(
                'academic_year_id',
                $academicYear?->id
            )->find(
                $request->class_id
            );
        }

        if (!$class) {
            $class = $student->classAssignments
                ->first(function ($assignment) use ($academicYear) {
                    return $academicYear &&
                        $assignment->schoolClass?->academic_year_id === $academicYear->id;
                })
                ?->schoolClass;
        }

        if (!$class) {
            $class = $student->classes()
                ->with([
                    'academicYear',
                    'homeroomTeacher',
                    'homeroomTeacherUser.employee',
                ])
                ->wherePivot('status', 'Aktif')
                ->latest('class_student.created_at')
                ->first();
        }

        if ($request->user()?->isGuru()) {
            abort_unless($class, 403, 'Tidak ada kelas yang dapat diakses untuk laporan siswa ini.');
            app(TeacherClassAccess::class)->assertCanAccessClass($request->user(), $class);
            abort_unless(
                $student->classAssignments()->where('class_id', $class->id)->exists(),
                403,
                'Anda tidak memiliki akses ke data siswa tersebut.'
            );
        }

        $semester = null;

        if ($request->filled('semester_id')) {
            $semester = Semester::where(
                'academic_year_id',
                $academicYear?->id
            )->find(
                $request->semester_id
            );
        }

        return compact(
            'student',
            'academicYear',
            'semester',
            'class'
        );
    }

    /**
     * Mengurutkan daftar siswa berdasarkan tingkat kelas, nama kelas,
     * lalu nama siswa. Subquery mengikuti kelas yang aktif pada tahun ajaran
     * yang dipilih sehingga urutan cetak semua kelas menjadi konsisten.
     */
    private function applyStudentClassOrdering(
        $query,
        ?int $academicYearId,
        ?int $classId = null
    ): void {
        $gradeOrder = DB::table('class_student as class_order_assignment')
            ->join('classes as class_order', 'class_order.id', '=', 'class_order_assignment.class_id')
            ->select('class_order.grade_level')
            ->whereColumn('class_order_assignment.student_id', 'students.id')
            ->where('class_order_assignment.status', 'Aktif')
            ->when($academicYearId, function ($subquery) use ($academicYearId) {
                $subquery->where('class_order.academic_year_id', $academicYearId);
            })
            ->when($classId, function ($subquery) use ($classId) {
                $subquery->where('class_order.id', $classId);
            })
            ->orderBy('class_order.grade_level')
            ->orderBy('class_order.name')
            ->limit(1);

        $classNameOrder = DB::table('class_student as class_name_assignment')
            ->join('classes as class_name_order', 'class_name_order.id', '=', 'class_name_assignment.class_id')
            ->select('class_name_order.name')
            ->whereColumn('class_name_assignment.student_id', 'students.id')
            ->where('class_name_assignment.status', 'Aktif')
            ->when($academicYearId, function ($subquery) use ($academicYearId) {
                $subquery->where('class_name_order.academic_year_id', $academicYearId);
            })
            ->when($classId, function ($subquery) use ($classId) {
                $subquery->where('class_name_order.id', $classId);
            })
            ->orderBy('class_name_order.grade_level')
            ->orderBy('class_name_order.name')
            ->limit(1);

        $query
            ->orderBy($gradeOrder)
            ->orderBy($classNameOrder)
            ->orderBy('students.name')
            ->orderBy('students.id');
    }

    private function applyClassOrderNumber(
        Student $student,
        ?SchoolClass $class,
        array &$classOrderNumbers
    ): void {
        if (! $class) {
            return;
        }

        if (! isset($classOrderNumbers[$class->id])) {
            $classRoster = Student::query()
                ->whereHas('classAssignments', function ($query) use ($class) {
                    $query->where('class_id', $class->id);
                })
                ->orderBy('name')
                ->orderBy('id')
                ->pluck('id');

            $classOrderNumbers[$class->id] = [];
            foreach ($classRoster as $index => $studentId) {
                $classOrderNumbers[$class->id][$studentId] = $index + 1;
            }
        }

        $student->student_number = $classOrderNumbers[$class->id][$student->id] ?? '';
    }
}
