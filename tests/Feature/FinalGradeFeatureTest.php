<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\FinalGrade;
use App\Models\GraduationStatus;
use App\Models\ReportCardGrade;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\FinalGradeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinalGradeFeatureTest extends TestCase
{
    private FinalGradeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FinalGradeService();
    }

    /**
     * Test 1: Rumus perhitungan nilai ujian: (praktik + tertulis) / 2
     */
    public function test_calculate_exam_score_formula(): void
    {
        // 90 dan 80 -> 85
        $examScore = $this->service->calculateExamScore(90.0, 80.0);
        $this->assertEquals(85.0, $examScore);

        // Jika salah satu null, hasil harus null (bukan dianggap nol)
        $this->assertNull($this->service->calculateExamScore(90.0, null));
        $this->assertNull($this->service->calculateExamScore(null, 80.0));
        $this->assertNull($this->service->calculateExamScore(null, null));
    }

    /**
     * Test 2: Rumus perhitungan nilai akhir: 60% Nilai Sekolah + 40% Nilai Ujian
     * Contoh: Sekolah 80 (80 * 0.6 = 48), Ujian 85 (85 * 0.4 = 34) -> 82
     */
    public function test_calculate_final_score_formula(): void
    {
        $finalScore = $this->service->calculateFinalScore(80.0, 85.0);
        $this->assertEquals(82.0, $finalScore);

        // Jika salah satu komponen null, hasil harus null
        $this->assertNull($this->service->calculateFinalScore(80.0, null));
        $this->assertNull($this->service->calculateFinalScore(null, 85.0));
    }

    /**
     * Test 3: Nilai kosong tidak boleh dianggap nol
     */
    public function test_empty_scores_are_not_treated_as_zero(): void
    {
        $this->assertNull($this->service->calculateExamScore(null, null));
        $this->assertNull($this->service->calculateFinalScore(null, null));

        // Rata-rata dari nilai kosong menghasilkan null
        $emptyCollection = collect([
            (object) ['final_score' => null],
            (object) ['final_score' => null],
        ]);
        $this->assertNull($this->service->calculateAverage($emptyCollection, 'final_score'));
    }

    /**
     * Test 4: Rata-rata menghitung nilai valid saja dan tidak menganggap null sebagai nol
     */
    public function test_calculate_average_ignores_nulls(): void
    {
        $grades = collect([
            (object) ['final_score' => 80.0],
            (object) ['final_score' => 90.0],
            (object) ['final_score' => null],
        ]);

        // Rata-rata dari 80 dan 90 adalah 85, bukan (80+90+0)/3 = 56.67
        $avg = $this->service->calculateAverage($grades, 'final_score');
        $this->assertEquals(85.0, $avg);
    }

    /**
     * Test 5: Daftar bawaan ijazah urutan dan muatan lokal
     */
    public function test_ijazah_subject_mapping_structure(): void
    {
        $subjects = FinalGradeService::IJAZAH_SUBJECTS;
        $this->assertNotEmpty($subjects);

        $names = collect($subjects)->map(function ($item) {
            if (!empty($item['is_group'])) {
                return collect($item['children'])->pluck('name');
            }
            return $item['name'];
        })->flatten()->toArray();

        $this->assertContains('Pendidikan Agama dan Budi Pekerti', $names);
        $this->assertContains('Bahasa Indonesia', $names);
        $this->assertContains('Matematika', $names);
        $this->assertContains('Baca Tulis Alquran', $names);
        $this->assertContains('Bahasa Daerah Palembang', $names);
    }
}
