<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\Employee;
use App\Models\RekapAbsensi;
use App\Models\ReportCardGrade;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentPromotion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use App\Models\GraduationStatus;
use App\Services\FinalGradeService;
use Symfony\Component\HttpFoundation\Response;

class RekapRapotController extends Controller
{
    /**
     * Halaman utama Rekap Rapot.
     */
    public function index(Request $request): View
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $selectedAcademicYearId = $request->input(
            'academic_year_id',
            AcademicYear::getActive()?->id
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
                $selectedAcademicYearId,
                fn ($query) => $query->where('academic_year_id', $selectedAcademicYearId)
            )
            ->where('status', 'Aktif')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->input('class_id');
        $selectedStatus = $request->input('status', 'Aktif');
        $search = trim((string) $request->input('search', ''));
        $students = collect();

        if ($selectedAcademicYearId) {
            $selectedClass = ($selectedClassId !== null && $selectedClassId !== '' && $selectedClassId !== 'all')
                ? $classes->firstWhere('id', (int) $selectedClassId)
                : null;

            $studentsQuery = Student::query();

            if ($selectedClass) {
                $studentsQuery
                    ->whereHas('classAssignments', function ($query) use ($selectedClass, $selectedStatus) {
                        $query->where('class_id', $selectedClass->id)
                            ->when(
                                $selectedStatus !== 'Semua',
                                fn ($q) => $q->where('status', $selectedStatus)
                            );
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
                $studentsQuery->whereHas('classAssignments.schoolClass', function ($query) use ($selectedAcademicYearId) {
                    $query->where('academic_year_id', $selectedAcademicYearId);
                });

                if ($search !== '') {
                    $studentsQuery->where(function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('nis', 'like', '%' . $search . '%')
                            ->orWhere('nisn', 'like', '%' . $search . '%');
                    });
                }
            }

            if ($selectedClass || $selectedClassId === 'all') {
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
            'search'
        ));
    }

    /**
     * Preview/cetak rapor satu siswa.
     */
    public function print(Request $request, Student $student): Response|View
    {
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

        $students = Student::query()
            ->whereHas('classes', function ($query) use ($academicYearId, $classId) {
                $query->where('classes.academic_year_id', $academicYearId)
                    ->when($classId !== null, fn ($q) => $q->where('classes.id', $classId));
            })
            ->orderBy('name')
            ->get();

        abort_unless($students->isNotEmpty(), 422, 'Tidak ada siswa yang dapat dicetak.');

        $reports = $students->map(function ($student) use ($academicYearId, $semesterMode, $classId) {
            $class = $this->resolveStudentClass($student, $academicYearId, $classId);

            return $class
                ? $this->buildReport($student, $class, $academicYearId, $semesterMode)
                : null;
        })->filter();

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

        abort_unless($reports->isNotEmpty(), 422, 'Tidak ada rapor yang dapat dicetak untuk kelas/tahun ajaran tersebut.');

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
        $academicYear = AcademicYear::find($academicYearId) ?? $class->academicYear;

        $semesters = Semester::where('academic_year_id', $academicYearId)
            ->orderBy('id')
            ->get();

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

        // Urutan harus sama dengan halaman input Nilai Rapot.
        $subjects = $class->subjects()
            ->where('subjects.status', 'Aktif')
            ->orderBy('subjects.sort_order')
            ->orderBy('subjects.name')
            ->get();

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

        // Data keputusan kenaikan disimpan per siswa, kelas, dan tahun ajaran.
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
            : null;

        $headmaster = Employee::query()
            ->where('status', 'Aktif')
            ->where(function ($query) {
                $query->where('position', 'like', '%Kepala Sekolah%')
                    ->orWhere('position', 'like', '%Kepala%');
            })
            ->orderBy('id')
            ->first();

        $schoolProfile = $this->schoolProfile();
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
            $ijazahMapping = $finalGradeService->getIjazahSubjectMapping();

            $finalGrades = $finalGradeService->getStoredGrades(
                $student->id,
                $class->id,
                $academicYearId
            );

            // Ambil rata-rata rapor dari perhitungan yang sudah digunakan
            // oleh fitur Nilai Akhir/Ijazah.
           // Nilai rata-rata rapor mengikuti mata pelajaran aktif
// yang tersedia pada kelas di fitur Mata Pelajaran.
foreach ($subjects as $subject) {
    $reportResult = $finalGradeService->calculateSchoolScore(
        $student,
        (int) $subject->id
    );

    $reportAverageScores[$subject->id] = $reportResult['score'];
}

            // Ambil status kelulusan siswa pada kelas dan tahun ajaran ini.
            $graduationStatus = GraduationStatus::where([
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
        mixed $classId = null
    ): ?SchoolClass {
        return SchoolClass::query()
            ->where('academic_year_id', $academicYearId)
            ->when($classId !== null, fn ($query) => $query->whereKey($classId))
            ->whereHas('classStudents', function ($query) use ($student) {
                $query->where('student_id', $student->id);
            })
            ->with('academicYear')
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
