<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Achievement;
use App\Models\ReportCardGrade;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\RekapAbsensi;
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
     * Semester TIDAK dipilih di halaman index.
     * Ganjil dan Genap akan dikelola sekaligus di halaman edit.
     */
    public function index(Request $request)
    {
        /*
         * Tahun ajaran.
         */
        $academicYears = AcademicYear::orderByDesc('id')->get();

        /*
         * Tahun ajaran aktif sebagai default.
         */
        $selectedAcademicYearId = $request->academic_year_id
            ?? AcademicYear::getActive()?->id;

        $classes = collect();
        $students = collect();

        /*
         * Semester tahun ajaran yang dipilih.
         */
        $semesterGanjil = null;
        $semesterGenap = null;

        if ($selectedAcademicYearId) {

            /*
             * Ambil kelas berdasarkan tahun ajaran.
             */
            $classes = SchoolClass::with('academicYear')
                ->where(
                    'academic_year_id',
                    $selectedAcademicYearId
                )
                ->where(
                    'status',
                    'Aktif'
                )
                ->withCount('subjects')
                ->orderBy('grade_level')
                ->orderBy('name')
                ->get();

            /*
             * Ambil semester tahun ajaran tersebut.
             */
            $semesters = Semester::where(
                'academic_year_id',
                $selectedAcademicYearId
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
        }

        /*
         * Kelas yang dipilih.
         */
        $selectedClassId = $request->class_id;

        /*
         * Pastikan kelas benar-benar milik tahun ajaran yang dipilih.
         */
        $selectedClass = null;

        if (
            $selectedClassId &&
            $selectedAcademicYearId
        ) {

            $selectedClass = SchoolClass::with([
                'academicYear',
                'subjects'
            ])
                ->where(
                    'id',
                    $selectedClassId
                )
                ->where(
                    'academic_year_id',
                    $selectedAcademicYearId
                )
                ->where(
                    'status',
                    'Aktif'
                )
                ->first();

            /*
             * Jika kelas ditemukan, ambil siswanya.
             */
            if ($selectedClass) {

                $students = $selectedClass->students()
                    ->wherePivot(
                        'status',
                        'Aktif'
                    )
                    ->orderBy('name')
                    ->get();

                /*
                 * Ambil seluruh nilai siswa di kelas tersebut
                 * untuk semester Ganjil dan Genap.
                 */
                $semesterIds = collect([
                    $semesterGanjil?->id,
                    $semesterGenap?->id,
                ])->filter();

                if ($semesterIds->isNotEmpty()) {

                    $grades = ReportCardGrade::where(
                        'class_id',
                        $selectedClass->id
                    )
                        ->whereIn(
                            'semester_id',
                            $semesterIds
                        )
                        ->get()
                        ->groupBy('student_id');

                    /*
                     * Tambahkan informasi jumlah nilai
                     * Ganjil dan Genap ke masing-masing siswa.
                     */
                    $students = $students->map(
                        function ($student) use (
                            $grades,
                            $semesterGanjil,
                            $semesterGenap
                        ) {

                            $studentGrades =
                                $grades->get(
                                    $student->id,
                                    collect()
                                );

                            /*
                             * Jumlah nilai Ganjil.
                             */
                            $student->ganjil_grades_count =
                                $semesterGanjil
                                    ? $studentGrades
                                        ->where(
                                            'semester_id',
                                            $semesterGanjil->id
                                        )
                                        ->count()
                                    : 0;

                            /*
                             * Jumlah nilai Genap.
                             */
                            $student->genap_grades_count =
                                $semesterGenap
                                    ? $studentGrades
                                        ->where(
                                            'semester_id',
                                            $semesterGenap->id
                                        )
                                        ->count()
                                    : 0;

                            return $student;
                        }
                    );
                }
            }
        }

        return view(
            'admin.report-card-grades.index',
            compact(
                'academicYears',
                'classes',
                'students',
                'selectedAcademicYearId',
                'selectedClassId',
                'selectedClass',
                'semesterGanjil',
                'semesterGenap'
            )
        );
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
        ->where('students.id', $student->id)
        ->wherePivot('status', 'Aktif')
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
    // Ambil rekap absensi semester ganjil.
$attendanceGanjil = null;

if ($semesterGanjil) {
    $attendanceGanjil = RekapAbsensi::where([
        'student_id' => $student->id,
        'class_id' => $class->id,
        'semester_id' => $semesterGanjil->id,
    ])->first();
}

// Ambil rekap absensi semester genap.
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
     * PRESTASI
     * ======================================================
     *
     * Ambil prestasi siswa pada:
     *
     * - siswa yang sama
     * - kelas yang sama
     * - tahun ajaran yang sama
     *
     * Maksimal 3 ditampilkan karena form Nilai Rapot
     * saat ini menyediakan 3 baris prestasi.
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
     *
     * Key menggunakan 1, 2, 3 agar sesuai dengan
     * struktur form yang sekarang.
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
     * ======================================================
     * RETURN VIEW
     * ======================================================
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
            'achievementData'
        )
    );
}



    /**
 * ==========================================================
 * UPDATE
 * ==========================================================
 *
 * Menyimpan:
 *
 * - Nilai Ganjil
 * - Nilai Genap
 * - Prestasi
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
        ->where('students.id', $student->id)
        ->wherePivot('status', 'Aktif')
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
            'array'
        ],

        'ganjil_learning_outcomes' => [
            'nullable',
            'array'
        ],

        /*
         * Genap
         */
        'genap_scores' => [
            'nullable',
            'array'
        ],

        'genap_learning_outcomes' => [
            'nullable',
            'array'
        ],

        /*
         * Ekstrakurikuler
         */
        'extracurricular' => [
            'nullable',
            'array'
        ],

        /*
         * Prestasi
         */
        'achievements' => [
            'nullable',
            'array'
        ],

        'achievements.*.type' => [
            'nullable',
            'string',
            'max:255'
        ],

        'achievements.*.level' => [
            'nullable',
            'string',
            'in:Sekolah,Kecamatan,Kota,Provinsi,Nasional,Internasional'
        ],

        'achievements.*.description' => [
            'nullable',
            'string',
            'max:2000'
        ],

        /*
         * Kenaikan
         */
        'promotion_status' => [
            'nullable',
            'string',
            'max:255'
        ],

        'promotion_note' => [
            'nullable',
            'string',
            'max:2000'
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
            'max:100'
        ];

        /*
         * Capaian Ganjil
         */
        $rules[
            'ganjil_learning_outcomes.' . $subject->id
        ] = [
            'nullable',
            'string',
            'max:2000'
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
            'max:100'
        ];

        /*
         * Capaian Genap
         */
        $rules[
            'genap_learning_outcomes.' . $subject->id
        ] = [
            'nullable',
            'string',
            'max:2000'
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
                    ($score === null || $score === '') &&
                    (
                        $learningOutcome === null ||
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
                    ($score === null || $score === '') &&
                    (
                        $learningOutcome === null ||
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
         *
         * Strategi:
         *
         * 1. Hapus prestasi lama siswa pada kelas + tahun
         * 2. Simpan kembali data yang ada di form
         *
         * Karena form sekarang hanya menyediakan 3 baris,
         * maksimal 3 prestasi akan disimpan dari halaman ini.
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
         * Loop setiap baris.
         */
        foreach ($achievements as $achievementData) {

            $type =
                trim(
                    $achievementData['type'] ?? ''
                );

            $level =
                trim(
                    $achievementData['level'] ?? ''
                );

            $description =
                trim(
                    $achievementData['description'] ?? ''
                );

            /*
             * Jika satu baris benar-benar kosong,
             * jangan buat record database.
             */
            if (
                $type === '' &&
                $level === '' &&
                $description === ''
            ) {
                continue;
            }

            /*
             * Simpan prestasi.
             */
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
                'Seluruh nilai rapot dan prestasi ' .
                $student->name .
                ' berhasil dihapus.'
            );
    }
}
