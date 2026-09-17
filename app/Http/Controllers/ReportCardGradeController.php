<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ReportCardGrade;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
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
                ->where('academic_year_id', $selectedAcademicYearId)
                ->where('status', 'Aktif')
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

            $semesterGanjil = $semesters->first(
                fn ($semester) =>
                    strtolower($semester->name) === 'ganjil'
            );

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

        if ($selectedClassId && $selectedAcademicYearId) {

            $selectedClass = SchoolClass::with([
                'academicYear',
                'subjects'
            ])
                ->where('id', $selectedClassId)
                ->where(
                    'academic_year_id',
                    $selectedAcademicYearId
                )
                ->where('status', 'Aktif')
                ->first();

            /*
             * Jika kelas ditemukan, ambil siswanya.
             */
            if ($selectedClass) {

                $students = $selectedClass->students()
                    ->wherePivot('status', 'Aktif')
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
                                $grades->get($student->id, collect());

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
         * Tahun ajaran kelas.
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
         * Mata pelajaran berdasarkan kelas.
         */
        $subjects = $class->subjects()
            ->where('subjects.status', 'Aktif')
            ->orderBy('subjects.name')
            ->get();

        /*
         * Nilai siswa untuk Ganjil + Genap.
         */
        $semesterIds = collect([
            $semesterGanjil?->id,
            $semesterGenap?->id,
        ])->filter();

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

        return view(
            'admin.report-card-grades.edit',
            compact(
                'class',
                'student',
                'subjects',
                'semesterGanjil',
                'semesterGenap',
                'grades'
            )
        );
    }


    /**
     * ==========================================================
     * UPDATE
     * ==========================================================
     *
     * Menyimpan nilai Ganjil + Genap.
     */
    public function update(
        Request $request,
        SchoolClass $class,
        Student $student
    ) {
        /*
         * Pastikan siswa berada di kelas tersebut.
         */
        $isStudentInClass = $class->students()
            ->where('students.id', $student->id)
            ->wherePivot('status', 'Aktif')
            ->exists();

        if (!$isStudentInClass) {
            abort(404);
        }

        /*
         * Ambil semester.
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
         * Mata pelajaran kelas.
         */
        $subjects = $class->subjects()
            ->where('subjects.status', 'Aktif')
            ->get();

        /*
         * Validasi.
         */
        $rules = [

            'ganjil_scores' => [
                'nullable',
                'array'
            ],

            'ganjil_learning_outcomes' => [
                'nullable',
                'array'
            ],

            'genap_scores' => [
                'nullable',
                'array'
            ],

            'genap_learning_outcomes' => [
                'nullable',
                'array'
            ],

            /*
             * D. Ekstrakurikuler
             */
            'extracurricular' => [
                'nullable',
                'array'
            ],

            /*
             * E. Prestasi
             */
            'achievements' => [
                'nullable',
                'array'
            ],

            /*
             * F. Ketidakhadiran
             */
            'attendance' => [
                'nullable',
                'array'
            ],

            /*
             * Kenaikan
             */
            'promotion_status' => [
                'nullable',
                'in:Naik,Tidak Naik'
            ],

            'promotion_class' => [
                'nullable',
                'string',
                'max:50'
            ],

            'promotion_date' => [
                'nullable',
                'date'
            ],
        ];

        /*
         * Validasi nilai berdasarkan mata pelajaran kelas.
         */
        foreach ($subjects as $subject) {

            $rules[
                'ganjil_scores.' . $subject->id
            ] = [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ];

            $rules[
                'ganjil_learning_outcomes.' . $subject->id
            ] = [
                'nullable',
                'string',
                'max:2000'
            ];

            $rules[
                'genap_scores.' . $subject->id
            ] = [
                'nullable',
                'numeric',
                'min:0',
                'max:100'
            ];

            $rules[
                'genap_learning_outcomes.' . $subject->id
            ] = [
                'nullable',
                'string',
                'max:2000'
            ];
        }

        $validated = $request->validate($rules);

        /*
         * Simpan semuanya dalam satu transaksi.
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
                     * Jika keduanya kosong, hapus record
                     * jika sebelumnya pernah ada.
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
                     * Jika keduanya kosong, hapus record
                     * jika sebelumnya pernah ada.
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
        });

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
                'Data nilai rapot ' .
                $student->name .
                ' berhasil disimpan.'
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
            ->where('students.id', $student->id)
            ->exists();

        if (!$isStudentInClass) {
            abort(404);
        }

        ReportCardGrade::where(
            'student_id',
            $student->id
        )
            ->where(
                'class_id',
                $class->id
            )
            ->delete();

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
                'Seluruh nilai rapot ' .
                $student->name .
                ' berhasil dihapus.'
            );
    }
}
