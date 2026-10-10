<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\ReportCardGrade;
use App\Models\SchoolClass;
use App\Models\SchoolProfile;
use App\Models\Semester;
use App\Models\Student;
use App\Models\RekapAbsensi;
use App\Models\StudentPromotion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $searched = false;

        /*
         * Cek apakah ada filter yang aktif.
         */
        if ($search || $selectedAcademicYearId || $selectedClassId) {

            $searched = true;

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

                    $studentsList = $studentsQuery->orderBy('students.name')->get();

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

                $paginatedStudents = $query->orderBy('name')->paginate(20)->withQueryString();

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
    public function history(Student $student)
    {
        $student->load([
            'classes' => function ($q) {
                $q->with([
                    'academicYear.semesters',
                    'subjects'
                ])->orderBy('grade_level')->orderByDesc('academic_year_id');
            },
            'reportCardGrades'
        ]);

        $classesByGradeLevel = $student->classes->groupBy('grade_level');

        $gradeLevelsData = [];

        for ($level = 1; $level <= 6; $level++) {
            $matchingClasses = $classesByGradeLevel->get($level, collect());

            // Ambil kelas aktif atau kelas terbaru untuk grade level ini
            $levelClass = $matchingClasses->first(fn($c) => ($c->pivot->status ?? '') === 'Aktif')
                ?? $matchingClasses->first();

            if ($levelClass) {
                $semesters = $levelClass->academicYear?->semesters ?? collect();
                $semGanjil = $semesters->first(fn($s) => strtolower($s->name) === 'ganjil');
                $semGenap = $semesters->first(fn($s) => strtolower($s->name) === 'genap');

                $ganjilCount = $semGanjil
                    ? $student->reportCardGrades
                        ->where('class_id', $levelClass->id)
                        ->where('semester_id', $semGanjil->id)
                        ->count()
                    : 0;

                $genapCount = $semGenap
                    ? $student->reportCardGrades
                        ->where('class_id', $levelClass->id)
                        ->where('semester_id', $semGenap->id)
                        ->count()
                    : 0;

                $gradeLevelsData[$level] = [
                    'level' => $level,
                    'has_class' => true,
                    'class' => $levelClass,
                    'academic_year' => $levelClass->academicYear,
                    'has_ganjil' => (bool)$semGanjil,
                    'has_genap' => (bool)$semGenap,
                    'ganjil_count' => $ganjilCount,
                    'genap_count' => $genapCount,
                    'has_data' => ($ganjilCount > 0 || $genapCount > 0),
                ];
            } else {
                $gradeLevelsData[$level] = [
                    'level' => $level,
                    'has_class' => false,
                    'class' => null,
                    'academic_year' => null,
                    'has_ganjil' => false,
                    'has_genap' => false,
                    'ganjil_count' => 0,
                    'genap_count' => 0,
                    'has_data' => false,
                ];
            }
        }

        return view(
            'admin.report-card-grades.history',
            compact(
                'student',
                'gradeLevelsData'
            )
        );
    }


    /**
     * ==========================================================
     * CETAK RAPOT PDF (RIWAYAT)
     * ==========================================================
     *
     * Menghasilkan file PDF rapot untuk satu siswa,
     * pada satu kelas & satu semester tertentu.
     *
     * URL: /report-card-grades/{class}/{student}/print?semester={ganjil|genap}
     */
    public function printHistory(
        Request $request,
        SchoolClass $class,
        Student $student
    ) {
        /*
         * Validasi: siswa harus pernah terdaftar di kelas ini.
         */
        $isStudentInClass = $class->students()
            ->where('students.id', $student->id)
            ->exists();

        if (!$isStudentInClass) {
            abort(404, 'Siswa tidak ditemukan di kelas ini.');
        }

        /*
         * Tentukan semester.
         */
        $semesterName = $request->input('semester', 'ganjil');

        $semester = Semester::where('academic_year_id', $class->academic_year_id)
            ->whereRaw('LOWER(name) = ?', [strtolower($semesterName)])
            ->first();

        if (!$semester) {
            abort(404, 'Semester tidak ditemukan untuk tahun ajaran ini.');
        }

        /*
         * Ambil tahun ajaran.
         */
        $academicYear = $class->academicYear;

        /*
         * Ambil nilai rapot siswa pada kelas + semester.
         */
        $grades = ReportCardGrade::where('student_id', $student->id)
            ->where('class_id', $class->id)
            ->where('semester_id', $semester->id)
            ->with('subject')
            ->get()
            ->sortBy(fn ($g) => $g->subject->name ?? '');

        /*
         * Ambil rekap absensi.
         */
        $attendance = RekapAbsensi::where([
            'student_id' => $student->id,
            'class_id'   => $class->id,
            'semester_id' => $semester->id,
        ])->first();

        /*
         * Ambil prestasi pada kelas + tahun ajaran.
         */
        $achievements = Achievement::where('student_id', $student->id)
            ->where('class_id', $class->id)
            ->where('academic_year_id', $class->academic_year_id)
            ->orderBy('id')
            ->get();

        /*
         * Wali kelas.
         */
        $homeroomTeacher = $class->homeroomTeacher;

        /*
         * Data sekolah.
         */
        $school = SchoolProfile::first();

        /*
         * Kepala sekolah (ambil dari data pegawai jika tersedia).
         */
        $headmaster = null;
        $headmasterNip = null;

        $kepalaSekolah = \App\Models\Employee::where('position', 'like', '%Kepala Sekolah%')
            ->first();

        if ($kepalaSekolah) {
            $headmaster = $kepalaSekolah->name;
            $headmasterNip = $kepalaSekolah->nip;
        }

        /*
         * Render view lalu generate PDF.
         */
        $view = view('reports.rapot.print', compact(
            'student',
            'class',
            'semester',
            'academicYear',
            'grades',
            'attendance',
            'achievements',
            'homeroomTeacher',
            'school',
            'headmaster',
            'headmasterNip'
        ));

        $filename = 'rapot-'
            . ($student->nis ?: $student->id)
            . '-kelas-' . $class->grade_level
            . '-' . strtolower($semester->name)
            . '.pdf';

        return Pdf::loadHTML($view->render())
            ->setPaper('a4', 'portrait')
            ->download($filename);
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
        SchoolClass $class,
        Student $student
    ) {
        /*
         * Pastikan siswa memang berada di kelas tersebut.
         */
        $isStudentInClass = $class->students()
            ->where(
                'students.id',
                $student->id
            )
            ->wherePivot(
                'status',
                'Aktif'
            )
            ->exists();

        if (!$isStudentInClass) {
            abort(404);
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
                'promotion'
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

        $isStudentInClass = $class->students()
            ->where(
                'students.id',
                $student->id
            )
            ->wherePivot(
                'status',
                'Aktif'
            )
            ->exists();

        if (!$isStudentInClass) {
            abort(404);
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

        return redirect()
            ->route(
                'report-card-grades.index',
                [
                    'academic_year_id' =>
                        $class->academic_year_id,

                    'class_id' =>
                        $class->id,
                ]
            )
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
        return redirect()
            ->route(
                'report-card-grades.index',
                [
                    'academic_year_id' =>
                        $class->academic_year_id,

                    'class_id' =>
                        $class->id,
                ]
            )
            ->with(
                'success',
                'Seluruh nilai rapot dan prestasi '
                . $student->name
                . ' berhasil dihapus.'
            );
    }
}