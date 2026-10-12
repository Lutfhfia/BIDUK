<?php

namespace App\Services;

use App\Models\ClassStudent;
use App\Models\FinalGrade;
use App\Models\ReportCardGrade;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Support\Collection;

class FinalGradeService
{
    /**
     * Daftar mata pelajaran bawaan untuk Nilai Akhir/Ijazah.
     *
     * Urutan dan nama harus sesuai dengan daftar yang disepakati guru.
     * Kunci 'code' digunakan untuk mencocokkan ke tabel subjects.
     * Kunci 'is_group' menandai kelompok (Muatan Lokal).
     * Kunci 'children' berisi submata pelajaran.
     */
    public const IJAZAH_SUBJECTS = [
        [
            'name'  => 'Pendidikan Agama dan Budi Pekerti',
            'order' => 1,
        ],
        [
            'name'  => 'Pendidikan Kewarganegaraan',
            'match' => ['Pendidikan Pancasila', 'Pendidikan Kewarganegaraan', 'PKn'],
            'order' => 2,
        ],
        [
            'name'  => 'Bahasa Indonesia',
            'order' => 3,
        ],
        [
            'name'  => 'Matematika',
            'order' => 4,
        ],
        [
            'name'  => 'Ilmu Pengetahuan Alam dan Sosial (IPAS)',
            'match' => ['Ilmu Pengetahuan Alam dan Sosial', 'IPAS'],
            'order' => 5,
        ],
        [
            'name'  => 'Seni Budaya dan Prakarya',
            'match' => ['Seni Budaya dan Prakarya', 'Seni dan Budaya', 'SBdP'],
            'order' => 6,
        ],
        [
            'name'  => 'Bahasa Inggris',
            'order' => 7,
        ],
        [
            'name'     => 'Pendidikan Jasmani Olahraga dan Kesehatan',
            'match'    => [
                'Pendidikan Jasmani, Olahraga dan Kesehatan (PJOK)',
                'Pendidikan Jasmani, Olahraga dan Kesehatan',
                'Pendidikan Jasmani Olahraga dan Kesehatan',
                'PJOK',
            ],
            'order'    => 8,
        ],
        [
            'name'     => 'Muatan Lokal',
            'is_group' => true,
            'order'    => 9,
            'children' => [
                [
                    'name'  => 'Baca Tulis Alquran',
                    'match' => ['Baca Tulis Alquran', 'BTQ', 'Baca Tulis Al-Quran', 'Muatan Lokal A', 'MULOK-A'],
                    'order' => 10,
                ],
                [
                    'name'  => 'Bahasa Daerah Palembang',
                    'match' => ['Bahasa Daerah Palembang', 'Bahasa Daerah', 'Mulok Bahasa Daerah', 'Muatan Lokal B', 'MULOK-B', 'Muatan Lokal C', 'MULOK-C'],
                    'order' => 11,
                ],
            ],
        ],
    ];

    /**
     * Mencocokkan daftar mata pelajaran ijazah bawaan dengan
     * tabel subjects yang ada di database.
     *
     * @return Collection  Koleksi item: [
     *   'ijazah_name'  => string,
     *   'subject_id'   => int|null,
     *   'subject'      => Subject|null,
     *   'order'        => int,
     *   'is_group'     => bool,
     *   'group_name'   => string|null,
     * ]
     */
    public function getIjazahSubjectMapping(): Collection
    {
        $allSubjects = Subject::active()->get();
        $result = collect();

        foreach (self::IJAZAH_SUBJECTS as $item) {
            if (!empty($item['is_group'])) {
                // Kelompok seperti Muatan Lokal — hanya headernya
                foreach ($item['children'] ?? [] as $child) {
                    $matched = $this->findMatchingSubject($allSubjects, $child);
                    $result->push([
                        'ijazah_name' => $child['name'],
                        'subject_id'  => $matched?->id,
                        'subject'     => $matched,
                        'order'       => $child['order'],
                        'is_group'    => false,
                        'group_name'  => $item['name'],
                    ]);
                }
            } else {
                $matched = $this->findMatchingSubject($allSubjects, $item);
                $result->push([
                    'ijazah_name' => $item['name'],
                    'subject_id'  => $matched?->id,
                    'subject'     => $matched,
                    'order'       => $item['order'],
                    'is_group'    => false,
                    'group_name'  => null,
                ]);
            }
        }

        return $result->sortBy('order')->values();
    }

    /**
     * Mencari subject dari database yang cocok dengan item ijazah.
     */
    private function findMatchingSubject(Collection $allSubjects, array $item): ?Subject
    {
        // 1. Coba cocokkan nama persis
        $subject = $allSubjects->first(
            fn(Subject $s) => mb_strtolower(trim($s->name)) === mb_strtolower(trim($item['name']))
        );

        if ($subject) {
            return $subject;
        }

        // 2. Coba cocokkan dari daftar alternatif (nama atau kode)
        if (!empty($item['match'])) {
            foreach ($item['match'] as $alt) {
                $altLower = mb_strtolower(trim($alt));
                $subject = $allSubjects->first(
                    fn(Subject $s) => mb_strtolower(trim($s->name)) === $altLower
                        || mb_strtolower(trim($s->code)) === $altLower
                );
                if ($subject) {
                    return $subject;
                }
            }
        }

        // 3. Coba kecocokan substring
        $needle = mb_strtolower(trim($item['name']));
        $subject = $allSubjects->first(
            fn(Subject $s) => str_contains(mb_strtolower($s->name), $needle)
                || str_contains($needle, mb_strtolower($s->name))
        );

        return $subject;
    }

    /**
     * Menghitung Nilai Sekolah (rata-rata 4 semester) untuk satu siswa
     * dan satu subject.
     *
     * Mengambil nilai dari:
     * - Kelas 5 Ganjil
     * - Kelas 5 Genap
     * - Kelas 6 Ganjil
     * - Kelas 6 Genap
     *
     * @return array{score: float|null, count: int, missing: array}
     */
    public function calculateSchoolScore(
        Student $student,
        int     $subjectId
    ): array {
        // Ambil semua kelas yang pernah ditempuh siswa
        $classAssignments = ClassStudent::where('student_id', $student->id)
            ->with(['schoolClass.academicYear.semesters'])
            ->get();

        $scores  = [];
        $missing = [];

        // Cari kelas 5 dan kelas 6
        $targetLevels = [5, 6];
        $targetSemesters = ['ganjil', 'genap'];

        foreach ($targetLevels as $level) {
            // Cari kelas di grade level ini
            $assignment = $classAssignments->first(function ($cs) use ($level) {
                return $cs->schoolClass
                    && (int) $cs->schoolClass->grade_level === $level;
            });

            if (!$assignment || !$assignment->schoolClass) {
                foreach ($targetSemesters as $semName) {
                    $missing[] = "Kelas {$level} {$semName}";
                }
                continue;
            }

            $classModel    = $assignment->schoolClass;
            $academicYear  = $classModel->academicYear;

            if (!$academicYear) {
                foreach ($targetSemesters as $semName) {
                    $missing[] = "Kelas {$level} {$semName} (tahun ajaran tidak ditemukan)";
                }
                continue;
            }

            $semesters = $academicYear->semesters;

            foreach ($targetSemesters as $semName) {
                $semester = $semesters->first(
                    fn(Semester $s) => strtolower($s->name) === $semName
                );

                if (!$semester) {
                    $missing[] = "Kelas {$level} {$semName} (semester tidak ditemukan)";
                    continue;
                }

                $grade = ReportCardGrade::where([
                    'student_id' => $student->id,
                    'class_id'   => $classModel->id,
                    'subject_id' => $subjectId,
                    'semester_id' => $semester->id,
                ])->first();

                if (!$grade || $grade->score === null) {
                    $missing[] = "Kelas {$level} " . ucfirst($semName);
                } else {
                    $scores[] = (float) $grade->score;
                }
            }
        }

        $score = null;
        if (count($scores) > 0 && count($missing) === 0) {
            // Semua 4 nilai tersedia
            $score = array_sum($scores) / count($scores);
        } elseif (count($scores) > 0) {
            // Sebagian tersedia — hitung dengan data yang ada tapi tandai
            $score = array_sum($scores) / count($scores);
        }

        return [
            'score'   => $score,
            'count'   => count($scores),
            'missing' => $missing,
        ];
    }

    /**
     * Menghitung Nilai Ujian Sekolah.
     *
     * @return float|null
     */
    public function calculateExamScore(?float $practical, ?float $written): ?float
    {
        if ($practical === null || $written === null) {
            return null;
        }

        return ($practical + $written) / 2;
    }

    /**
     * Menghitung Nilai Akhir.
     *
     * Rumus: (Nilai Sekolah × 0.60) + (Nilai Ujian Sekolah × 0.40)
     *
     * @return float|null
     */
    public function calculateFinalScore(?float $schoolScore, ?float $examScore): ?float
    {
        if ($schoolScore === null || $examScore === null) {
            return null;
        }

        return ($schoolScore * 0.60) + ($examScore * 0.40);
    }

    /**
     * Menghitung dan menyimpan semua nilai akhir untuk satu siswa.
     *
     * @return array  Data yang sudah dihitung
     */
    public function calculateAndSave(
        Student $student,
        SchoolClass $class,
        int $academicYearId,
        array $inputData
    ): array {
        $mapping = $this->getIjazahSubjectMapping();
        $results = [];

        foreach ($mapping as $item) {
            $subjectId = $item['subject_id'];

            if (!$subjectId) {
                continue;
            }

            $key = $subjectId;

            // Ambil input manual
            $practicalScore = isset($inputData['practical_scores'][$key])
                && $inputData['practical_scores'][$key] !== ''
                && $inputData['practical_scores'][$key] !== null
                ? (float) $inputData['practical_scores'][$key]
                : null;

            $writtenScore = isset($inputData['written_scores'][$key])
                && $inputData['written_scores'][$key] !== ''
                && $inputData['written_scores'][$key] !== null
                ? (float) $inputData['written_scores'][$key]
                : null;

            $schoolLearningOutcome = $inputData['school_learning_outcomes'][$key] ?? null;
            $finalLearningOutcome  = $inputData['final_learning_outcomes'][$key] ?? null;

            // Hitung Nilai Sekolah
            $schoolResult = $this->calculateSchoolScore($student, $subjectId);
            $schoolScore  = $schoolResult['score'];

            // Hitung Nilai Ujian Sekolah
            $examScore = $this->calculateExamScore($practicalScore, $writtenScore);

            // Hitung Nilai Akhir
            $finalScore = $this->calculateFinalScore($schoolScore, $examScore);

            // Simpan ke database
            FinalGrade::updateOrCreate(
                [
                    'student_id'      => $student->id,
                    'class_id'        => $class->id,
                    'subject_id'      => $subjectId,
                    'academic_year_id' => $academicYearId,
                ],
                [
                    'school_score'           => $schoolScore !== null ? round($schoolScore, 2) : null,
                    'practical_score'        => $practicalScore,
                    'written_score'          => $writtenScore,
                    'exam_score'             => $examScore !== null ? round($examScore, 2) : null,
                    'final_score'            => $finalScore !== null ? round($finalScore, 2) : null,
                    'school_learning_outcome' => $schoolLearningOutcome,
                    'final_learning_outcome'  => $finalLearningOutcome,
                ]
            );

            $results[$subjectId] = [
                'school_score'  => $schoolScore,
                'practical'     => $practicalScore,
                'written'       => $writtenScore,
                'exam_score'    => $examScore,
                'final_score'   => $finalScore,
                'missing'       => $schoolResult['missing'],
            ];
        }

        return $results;
    }

    /**
     * Mendapatkan data nilai akhir yang sudah tersimpan untuk satu siswa.
     */
    public function getStoredGrades(
        int $studentId,
        int $classId,
        int $academicYearId
    ): Collection {
        return FinalGrade::where([
            'student_id'      => $studentId,
            'class_id'        => $classId,
            'academic_year_id' => $academicYearId,
        ])->get()->keyBy('subject_id');
    }

    /**
     * Menghitung rata-rata dari koleksi nilai.
     * Nilai null tidak diikutkan.
     */
    public function calculateAverage(Collection $grades, string $field): ?float
    {
        $validGrades = $grades->filter(fn($g) => $g->$field !== null);

        if ($validGrades->isEmpty()) {
            return null;
        }

        return round($validGrades->avg($field), 2);
    }
}
