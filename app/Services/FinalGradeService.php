<?php

namespace App\Services;

use App\Models\ClassStudent;
use App\Models\FinalGrade;
use App\Models\ReportCardGrade;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentClassHistory;
use App\Models\Subject;
use Illuminate\Support\Collection;

class FinalGradeService
{
    /**
     * Daftar mata pelajaran bawaan untuk Nilai Akhir/Ijazah.
     *
     * Urutan dan nama harus sesuai dengan daftar yang disepakati guru.
     * Kunci 'code' digunakan untuk mencocokkan ke tabel subjects.
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
            'name'     => 'Pendidikan Jasmani, Olahraga dan Kesehatan',
            'match'    => [
                'Pendidikan Jasmani, Olahraga dan Kesehatan (PJOK)',
                'Pendidikan Jasmani, Olahraga dan Kesehatan',
                'Pendidikan Jasmani Olahraga dan Kesehatan',
                'PJOK',
            ],
            'order'    => 8,
        ],
    ];

    /**
     * Mencocokkan daftar mata pelajaran ijazah bawaan dengan
     * tabel subjects yang ada di database.
     *
     * @return Collection Koleksi 8 mata pelajaran utama beserta padanan master.
     */
    public function getIjazahSubjectMapping(): Collection
    {
        $allSubjects = Subject::active()->get();
        $result = collect();

        foreach (self::IJAZAH_SUBJECTS as $item) {
            $matched = $this->findMatchingSubject($allSubjects, $item);
            $result->push([
                'ijazah_name' => $item['name'],
                'subject_id'  => $matched?->id,
                'subject'     => $matched,
                'order'       => $item['order'],
                'is_group' => false,
                'group_name' => null,
            ]);
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
        return $this->calculateSchoolScores($student, [$subjectId])[$subjectId];
    }

    /**
     * Menghitung nilai sekolah untuk beberapa mata pelajaran dengan
     * memuat penempatan kelas dan nilai rapor dalam satu rangkaian query.
     *
     * @param iterable<int> $subjectIds
     * @return array<int, array{score: float|null, count: int, missing: array}>
     */
    public function calculateSchoolScores(
        Student $student,
        iterable $subjectIds,
        ?SchoolClass $classForLevelSix = null
    ): array {
        $subjectIds = collect($subjectIds)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->unique()
            ->values();

        $results = $subjectIds->mapWithKeys(fn (int $id) => [$id => [
            'score' => null,
            'count' => 0,
            'missing' => [],
        ]])->all();

        if ($subjectIds->isEmpty()) {
            return $results;
        }

        // Penempatan melalui class_student, diurutkan dari tahun terbaru.
        $classAssignments = ClassStudent::query()
            ->where('student_id', $student->id)
            ->with(['schoolClass.academicYear.semesters'])
            ->orderByDesc('created_at')
            ->get();

        // Riwayat kelas manual dapat menyimpan kelas lampau tanpa pivot class_student.
        $manualHistories = StudentClassHistory::query()
            ->where('student_id', $student->id)
            ->get()
            ->keyBy('grade_level');

        // Ambil kelas 5/6 yang memang memiliki record nilai rapor siswa.
        $gradedClassIds = ReportCardGrade::query()
            ->where('student_id', $student->id)
            ->select('class_id')
            ->distinct()
            ->pluck('class_id')
            ->map(fn ($id) => (int) $id)
            ->filter()
            ->unique()
            ->values();

        $gradedClasses = $gradedClassIds->isEmpty()
            ? collect()
            : SchoolClass::query()
                ->with(['academicYear.semesters'])
                ->whereIn('id', $gradedClassIds)
                ->whereIn('grade_level', [5, 6])
                ->get();

        $targetLevels = [5, 6];
        $semesterNames = ['ganjil', 'genap'];
        $missingBySubject = array_fill_keys($subjectIds->all(), []);
        $classesByLevel = [];
        $semestersByLevel = [];
        $targetClassIds = [];
        $targetSemesterIds = [];

        foreach ($targetLevels as $level) {
            $classModel = null;

            // Kelas 6 mengikuti kelas yang sedang dibuka pada halaman Ijazah.
            if (
                $level === 6
                && $classForLevelSix
                && (int) $classForLevelSix->grade_level === 6
            ) {
                $classModel = $classForLevelSix->loadMissing(['academicYear.semesters']);
            }

            // Jika riwayat manual tersedia, cocokkan dengan rombel dan tahun ajarannya.
            if (!$classModel) {
                $manualHistory = $manualHistories->get($level);

                if ($manualHistory) {
                    $classModel = SchoolClass::query()
                        ->with(['academicYear.semesters'])
                        ->where('grade_level', $level)
                        ->whereRaw('LOWER(name) = ?', [mb_strtolower(trim($manualHistory->rombel))])
                        ->whereHas('academicYear', function ($query) use ($manualHistory) {
                            $query->where('name', trim($manualHistory->academic_year));
                        })
                        ->first();
                }
            }

            // Berikutnya prioritaskan kelas yang mempunyai nilai rapor tersimpan.
            if (!$classModel) {
                $classModel = $gradedClasses
                    ->where('grade_level', $level)
                    ->sortByDesc(function (SchoolClass $candidate) {
                        $startDate = $candidate->academicYear?->start_date;
                        $yearOrder = $startDate ? (strtotime((string) $startDate) ?: 0) : 0;

                        return sprintf('%010d-%010d', $yearOrder, (int) $candidate->id);
                    })
                    ->first();
            }

            // Fallback terakhir: kelas yang tercatat di penempatan siswa.
            if (!$classModel) {
                $assignment = $classAssignments
                    ->filter(fn ($item) => $item->schoolClass
                        && (int) $item->schoolClass->grade_level === $level)
                    ->sortByDesc(function ($item) {
                        $startDate = $item->schoolClass?->academicYear?->start_date;
                        $yearOrder = $startDate ? (strtotime((string) $startDate) ?: 0) : 0;
                        $createdOrder = $item->created_at?->timestamp ?? 0;

                        return sprintf(
                            '%010d-%010d-%010d',
                            $yearOrder,
                            $createdOrder,
                            (int) $item->id
                        );
                    })
                    ->first();

                $classModel = $assignment?->schoolClass;
            }

            if (!$classModel) {
                foreach ($subjectIds as $subjectId) {
                    foreach ($semesterNames as $semesterName) {
                        $missingBySubject[$subjectId][] = "Kelas {$level} {$semesterName}";
                    }
                }
                continue;
            }

            $classesByLevel[$level] = $classModel;
            $academicYear = $classModel->academicYear;

            if (!$academicYear) {
                foreach ($subjectIds as $subjectId) {
                    foreach ($semesterNames as $semesterName) {
                        $missingBySubject[$subjectId][] = "Kelas {$level} {$semesterName} (tahun ajaran tidak ditemukan)";
                    }
                }
                continue;
            }

            $semesters = $academicYear->semesters ?? collect();
            $targetClassIds[] = (int) $classModel->id;

            foreach ($semesterNames as $semesterName) {
                $semester = $semesters->first(
                    fn (Semester $item) => mb_strtolower(trim($item->name)) === $semesterName
                );

                if (!$semester) {
                    foreach ($subjectIds as $subjectId) {
                        $missingBySubject[$subjectId][] = "Kelas {$level} {$semesterName} (semester tidak ditemukan)";
                    }
                    continue;
                }

                $semestersByLevel[$level][$semesterName] = $semester;
                $targetSemesterIds[] = (int) $semester->id;
            }
        }

        $grades = collect();
        if ($targetClassIds && $targetSemesterIds) {
            $grades = ReportCardGrade::query()
                ->where('student_id', $student->id)
                ->whereIn('class_id', array_values(array_unique($targetClassIds)))
                ->whereIn('subject_id', $subjectIds)
                ->whereIn('semester_id', array_values(array_unique($targetSemesterIds)))
                ->get()
                ->keyBy(fn (ReportCardGrade $grade) => implode(':', [
                    $grade->class_id,
                    $grade->subject_id,
                    $grade->semester_id,
                ]));
        }

        foreach ($targetLevels as $level) {
            $classModel = $classesByLevel[$level] ?? null;
            if (!$classModel || !$classModel->academicYear) {
                continue;
            }

            foreach ($semesterNames as $semesterName) {
                $semester = $semestersByLevel[$level][$semesterName] ?? null;
                if (!$semester) {
                    continue;
                }

                foreach ($subjectIds as $subjectId) {
                    $grade = $grades->get(implode(':', [
                        $classModel->id,
                        $subjectId,
                        $semester->id,
                    ]));

                    if (!$grade || $grade->score === null || trim((string) $grade->score) === '') {
                        $missingBySubject[$subjectId][] = "Kelas {$level} " . ucfirst($semesterName);
                        continue;
                    }

                    $results[$subjectId]['score'] = ($results[$subjectId]['score'] ?? 0)
                        + (float) $grade->score;
                    $results[$subjectId]['count']++;
                }
            }
        }

        foreach ($results as $subjectId => &$result) {
            if ($result['count'] > 0) {
                // Rata-rata parsial tetap terlihat, tetapi missing tetap menjadi peringatan.
                $result['score'] /= $result['count'];
            }

            $result['missing'] = array_values(array_unique($missingBySubject[$subjectId]));
        }
        unset($result);

        return $results;
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
        $schoolScoreResults = $this->calculateSchoolScores(
            $student,
            $mapping->pluck('subject_id')->filter(),
            $class
        );

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
            $schoolResult = $schoolScoreResults[$subjectId];
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
        $subjectIds = $this->getIjazahSubjectMapping()
            ->pluck('subject_id')
            ->filter()
            ->unique()
            ->values();

        if ($subjectIds->isEmpty()) {
            return collect();
        }

        return FinalGrade::where([
            'student_id' => $studentId,
            'class_id' => $classId,
            'academic_year_id' => $academicYearId,
        ])
            ->whereIn('subject_id', $subjectIds)
            ->get()
            ->keyBy('subject_id');
    }

    public function calculateFinalAverage(Collection $grades): ?float
    {
        return $this->calculateAverage($grades, 'final_score');
    }

    /**
     * Menghitung rata-rata dari koleksi nilai.
     * Nilai null tidak diikutkan.
     */
    public function calculateAverage(Collection $grades, string $field): ?float
    {
        $validGrades = $grades->filter(fn ($grade) => $grade->$field !== null && $grade->$field !== '');

        if ($validGrades->isEmpty()) {
            return null;
        }

        return round($validGrades->avg($field), 2);
    }
}
