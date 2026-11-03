<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FinalGrade;
use App\Models\GraduationStatus;
use App\Models\RekapAbsensi;
use App\Models\SchoolClass;
use App\Models\SchoolProfile;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use App\Services\FinalGradeService;
use App\Services\TeacherClassAccess;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinalGradeController extends Controller
{
    protected FinalGradeService $service;

    public function __construct(FinalGradeService $service)
    {
        $this->service = $service;
    }

    /**
     * ==========================================================
     * EDIT / INPUT NILAI AKHIR IJAZAH
     * ==========================================================
     *
     * Menampilkan form input Nilai Akhir/Ijazah untuk satu siswa
     * pada kelas 6 semester genap.
     *
     * URL: /report-card-grades/{class}/{student}/nilai-akhir
     */
    public function edit(Request $request, SchoolClass $class, Student $student)
    {
        return $this->renderFinalGrade($request, $class, $student, true);
    }

    /**
     * Menampilkan nilai akhir/ijazah tanpa hak untuk mengubah data.
     */
    public function show(Request $request, SchoolClass $class, Student $student)
    {
        return $this->renderFinalGrade($request, $class, $student, false);
    }

    private function renderFinalGrade(
        Request $request,
        SchoolClass $class,
        Student $student,
        bool $canEditGrades
    ) {
        app(TeacherClassAccess::class)->assertCanAccessClass($request->user(), $class);

        /*
         * Validasi: kelas harus kelas 6.
         */
        if ((int) $class->grade_level !== 6) {
            abort(404, 'Fitur Nilai Akhir/Ijazah hanya tersedia untuk kelas 6.');
        }

        /*
         * Validasi: siswa harus pernah terdaftar di kelas.
         */
        $isStudentInClass = $class->students()
            ->where('students.id', $student->id)
            ->exists();

        if (!$isStudentInClass) {
            abort(404, 'Siswa tidak ditemukan di kelas ini.');
        }

        /*
         * Daftar mata pelajaran ijazah.
         */
        $ijazahMapping = $this->service->getIjazahSubjectMapping();

        /*
         * Data nilai akhir yang sudah tersimpan.
         */
        $storedGrades = $this->service->getStoredGrades(
            $student->id,
            $class->id,
            $class->academic_year_id
        );
        $avgFinalScore = $this->service->calculateFinalAverage($storedGrades);

        /*
         * Hitung Nilai Sekolah dari rapor yang tersimpan.
         */
        $schoolScores = [];
        $missingData  = [];

        $schoolScoreResults = $this->service->calculateSchoolScores(
            $student,
            $ijazahMapping->pluck('subject_id')->filter(),
            $class
        );

        foreach ($ijazahMapping as $item) {
            if (!$item['subject_id']) {
                continue;
            }

            $result = $schoolScoreResults[$item['subject_id']];

            $schoolScores[$item['subject_id']] = $result['score'];
            if (!empty($result['missing'])) {
                $missingData[$item['subject_id']] = $result['missing'];
            }
        }

        /*
         * Status kelulusan.
         */
        $graduationStatus = GraduationStatus::where([
            'student_id'      => $student->id,
            'class_id'        => $class->id,
            'academic_year_id' => $class->academic_year_id,
        ])->first();

        /*
         * Absensi Ijazah adalah total Kelas 6 Semester Ganjil + Genap.
         * Bila satu semester belum tersedia, total sementara tetap dihitung
         * dari data yang ada dan daftar semester yang kurang dikirim ke view.
         */
        $attendanceSummary = $this->getGradeSixAttendanceSummary($student, $class);
        $attendance = $attendanceSummary['attendance'];
        $attendanceIsComplete = $attendanceSummary['attendanceIsComplete'];
        $attendanceMissingSemesters = $attendanceSummary['attendanceMissingSemesters'];

        /*
         * Wali kelas.
         */
        $homeroomTeacher = $class->homeroomTeacherUser?->loadMissing('employee')
            ?? $class->homeroomTeacher;
        $homeroomTeacher = $homeroomTeacher?->employee ?? $homeroomTeacher;

        /*
         * Kepala sekolah.
         */
        $headmaster = $this->getHeadmaster();

        $returnUrl = $request->input('return_url')
            ?: session('report_card_grades_index_url')
            ?: route('report-card-grades.edit', [
                'class' => $class->id,
                'student' => $student->id,
            ]);

        return view('admin.report-card-grades.final-grade', compact(
            'class',
            'student',
            'ijazahMapping',
            'storedGrades',
            'avgFinalScore',
            'schoolScores',
            'missingData',
            'graduationStatus',
            'attendance',
            'attendanceIsComplete',
            'attendanceMissingSemesters',
            'homeroomTeacher',
            'headmaster',
            'returnUrl',
            'canEditGrades'
        ));
    }

    /**
     * ==========================================================
     * UPDATE / SIMPAN NILAI AKHIR IJAZAH
     * ==========================================================
     */
    public function update(Request $request, SchoolClass $class, Student $student)
    {
        app(TeacherClassAccess::class)->assertCanAccessClass($request->user(), $class);

        /*
         * Validasi kelas 6.
         */
        if ((int) $class->grade_level !== 6) {
            abort(404, 'Fitur Nilai Akhir/Ijazah hanya tersedia untuk kelas 6.');
        }

        /*
         * Validasi siswa.
         */
        $isStudentInClass = $class->students()
            ->where('students.id', $student->id)
            ->exists();

        if (!$isStudentInClass) {
            abort(404);
        }

        /*
         * Validasi input.
         */
        $ijazahMapping = $this->service->getIjazahSubjectMapping();
        $subjectIds    = $ijazahMapping->pluck('subject_id')->filter()->values();

        $rules = [
            'practical_scores'          => ['nullable', 'array'],
            'written_scores'            => ['nullable', 'array'],
            'school_learning_outcomes'  => ['nullable', 'array'],
            'final_learning_outcomes'   => ['nullable', 'array'],
            'graduation_status'         => ['nullable', 'string', 'in:LULUS,TIDAK LULUS'],
            'graduation_date'           => ['nullable', 'date'],
            'certificate_number'        => ['nullable', 'string', 'max:100'],
            'skhus_number'              => ['nullable', 'string', 'max:100'],
        ];

        foreach ($subjectIds as $sid) {
            $rules["practical_scores.{$sid}"]         = ['nullable', 'numeric', 'min:0', 'max:100'];
            $rules["written_scores.{$sid}"]           = ['nullable', 'numeric', 'min:0', 'max:100'];
            $rules["school_learning_outcomes.{$sid}"]  = ['nullable', 'string', 'max:2000'];
            $rules["final_learning_outcomes.{$sid}"]   = ['nullable', 'string', 'max:2000'];
        }

        $validated = $request->validate($rules);

        /*
         * Transaksi database.
         */
        DB::transaction(function () use ($validated, $student, $class, $request) {
            /*
             * Simpan nilai akhir.
             */
            $this->service->calculateAndSave(
                $student,
                $class,
                $class->academic_year_id,
                $validated
            );
            /*
             * Simpan status kelulusan.
             */
            $graduationStatus = $validated['graduation_status'] ?? null;
            $graduationDate   = $validated['graduation_date'] ?? null;
            $certificateNum   = trim($validated['certificate_number'] ?? '');
            $skhusNum         = trim($validated['skhus_number'] ?? '');

            if (
                !empty($graduationStatus)
                || !empty($graduationDate)
                || $certificateNum !== ''
                || $skhusNum !== ''
            ) {
                GraduationStatus::updateOrCreate(
                    [
                        'student_id'      => $student->id,
                        'class_id'        => $class->id,
                        'academic_year_id' => $class->academic_year_id,
                    ],
                    [
                        'status'             => $graduationStatus,
                        'graduation_date'    => $graduationDate,
                        'certificate_number' => $certificateNum !== '' ? $certificateNum : null,
                        'skhus_number'       => $skhusNum !== '' ? $skhusNum : null,
                        'approved_by'        => $request->user()?->id,
                    ]
                );
            }
        });

        $redirectParams = ['class' => $class->id, 'student' => $student->id];
        if ($request->filled('return_url')) {
            $redirectParams['return_url'] = $request->input('return_url');
        }

        return redirect()
            ->route('final-grades.edit', $redirectParams)
            ->with('success', 'Data Nilai Akhir/Ijazah berhasil disimpan.');
    }

    /**
     * ==========================================================
     * CETAK NILAI AKHIR/IJAZAH PDF
     * ==========================================================
     */
    public function print(Request $request, SchoolClass $class, Student $student)
    {
        app(TeacherClassAccess::class)->assertCanAccessClass($request->user(), $class);

        /*
         * Validasi.
         */
        if ((int) $class->grade_level !== 6) {
            abort(404);
        }

        $isStudentInClass = $class->students()
            ->where('students.id', $student->id)
            ->exists();

        if (!$isStudentInClass) {
            abort(404, 'Siswa tidak ditemukan di kelas ini.');
        }

        /*
         * Ambil data.
         */
        $ijazahMapping = $this->service->getIjazahSubjectMapping();

        $storedGrades = $this->service->getStoredGrades(
            $student->id,
            $class->id,
            $class->academic_year_id
        );
        $graduationStatus = GraduationStatus::where([
            'student_id'      => $student->id,
            'class_id'        => $class->id,
            'academic_year_id' => $class->academic_year_id,
        ])->first();

        /*
         * Absensi pada PDF Ijazah menggunakan total Kelas 6 Ganjil + Genap,
         * sama seperti halaman input Nilai Akhir/Ijazah.
         */
        $attendanceSummary = $this->getGradeSixAttendanceSummary($student, $class);
        $attendance = $attendanceSummary['attendance'];
        $attendanceIsComplete = $attendanceSummary['attendanceIsComplete'];
        $attendanceMissingSemesters = $attendanceSummary['attendanceMissingSemesters'];

        /*
         * Rata-rata.
         */
        $avgSchoolScore = $this->service->calculateAverage($storedGrades, 'school_score');
        $avgFinalScore  = $this->service->calculateFinalAverage($storedGrades);

        /*
         * Data identitas.
         */
        $homeroomTeacher = $class->homeroomTeacherUser?->loadMissing('employee')
            ?? $class->homeroomTeacher;
        $homeroomTeacher = $homeroomTeacher?->employee ?? $homeroomTeacher;
        $school          = SchoolProfile::first();
        $academicYear    = $class->academicYear;

        $headmaster    = null;
        $headmasterNip = null;

        $letterheadImage = base64_encode(
            file_get_contents(public_path('images/nilai-akhir-kop.png'))
        );

        $headmasterUser = $this->getHeadmaster();
        if ($headmasterUser) {
            $headmaster = $headmasterUser->name;
            $headmasterNip = $headmasterUser->employee?->nip;
        }

        /*
         * Render view dan generate PDF.
         */
        $view = view('reports.nilai-akhir.print', compact(
            'student',
            'class',
            'academicYear',
            'ijazahMapping',
            'storedGrades',
            'graduationStatus',
            'attendance',
            'attendanceIsComplete',
            'attendanceMissingSemesters',
            'avgSchoolScore',
            'avgFinalScore',
            'homeroomTeacher',
            'school',
            'headmaster',
            'headmasterNip',
            'letterheadImage'
        ));

        $filename = 'nilai-akhir-'
            . ($student->nis ?: $student->id)
            . '-kelas-6'
            . '.pdf';

        return Pdf::loadHTML($view->render())
            ->setPaper('a4', 'portrait')
            ->download($filename);
    }

    private function getHeadmaster(): ?User
    {
        return User::query()
            ->with('employee')
            ->where('status', 'Aktif')
            ->whereHas('role', fn ($query) => $query->where('name', 'Kepala Sekolah'))
            ->orderBy('id')
            ->first();
    }

    /**
     * Mengambil dan menjumlahkan rekap absensi Kelas 6 untuk dua semester.
     *
     * @return array{
     *   attendance: object|null,
     *   attendanceIsComplete: bool,
     *   attendanceMissingSemesters: array<int, string>
     * }
     */
    private function getGradeSixAttendanceSummary(Student $student, SchoolClass $class): array
    {
        $semesters = Semester::query()
            ->where('academic_year_id', $class->academic_year_id)
            ->orderBy('id')
            ->get();

        $semesterByName = [];
        foreach ($semesters as $semester) {
            $normalizedName = mb_strtolower(trim($semester->name));

            if (str_contains($normalizedName, 'ganjil') && !isset($semesterByName['ganjil'])) {
                $semesterByName['ganjil'] = $semester;
            } elseif (str_contains($normalizedName, 'genap') && !isset($semesterByName['genap'])) {
                $semesterByName['genap'] = $semester;
            }
        }

        $totals = [
            'sakit' => 0,
            'izin' => 0,
            'tanpa_keterangan' => 0,
        ];
        $availableSemesters = [];
        $missingSemesters = [];

        foreach (['ganjil' => 'Semester Ganjil', 'genap' => 'Semester Genap'] as $key => $label) {
            $semester = $semesterByName[$key] ?? null;

            if (!$semester) {
                $missingSemesters[] = $label . ' belum tersedia pada tahun ajaran Kelas 6';
                continue;
            }

            $record = RekapAbsensi::query()
                ->where('student_id', $student->id)
                ->where('class_id', $class->id)
                ->where('semester_id', $semester->id)
                ->first();

            if (!$record) {
                $missingSemesters[] = $label . ' belum memiliki rekap absensi';
                continue;
            }

            $availableSemesters[] = $label;
            $totals['sakit'] += (int) ($record->sakit ?? 0);
            $totals['izin'] += (int) ($record->izin ?? 0);
            $totals['tanpa_keterangan'] += (int) ($record->tanpa_keterangan ?? 0);
        }

        return [
            'attendance' => $availableSemesters !== [] ? (object) $totals : null,
            'attendanceIsComplete' => count($availableSemesters) === 2 && $missingSemesters === [],
            'attendanceMissingSemesters' => $missingSemesters,
        ];
    }

}
