<?php

namespace Tests\Feature;

use App\Http\Controllers\AchievementReportController;
use App\Models\Achievement;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class AchievementReportYearFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_report_includes_achievements_from_all_academic_years(): void
    {
        $activeYear = $this->createAcademicYear('2025/2026', 'active');
        $oldYear = $this->createAcademicYear('2024/2025', 'inactive');
        $activeAchievement = $this->createAchievement($activeYear, 'Prestasi Tahun Aktif');
        $oldAchievement = $this->createAchievement($oldYear, 'Prestasi Tahun Sebelumnya');

        $view = app(AchievementReportController::class)->index(
            Request::create('/achievement-reports', 'GET')
        );
        $data = $view->getData();

        $this->assertNull($data['selectedAcademicYearId']);
        $this->assertEqualsCanonicalizing(
            [$activeYear->id, $oldYear->id],
            $data['classes']->pluck('academic_year_id')->all()
        );
        $this->assertEqualsCanonicalizing(
            [$activeAchievement->id, $oldAchievement->id],
            $data['achievements']->pluck('id')->all()
        );
    }

    private function createAcademicYear(string $name, string $status): AcademicYear
    {
        $startYear = (int) substr($name, 0, 4);

        return AcademicYear::create([
            'name' => $name,
            'start_date' => "{$startYear}-07-01",
            'end_date' => ($startYear + 1) . '-06-30',
            'status' => $status,
        ]);
    }

    private function createAchievement(AcademicYear $academicYear, string $type): Achievement
    {
        $class = SchoolClass::create([
            'academic_year_id' => $academicYear->id,
            'name' => '1A',
            'grade_level' => 1,
            'status' => 'Aktif',
        ]);
        $student = Student::create([
            'nis' => 'NIS-' . $academicYear->id,
            'nisn' => 'NISN-' . $academicYear->id,
            'name' => 'Siswa ' . $academicYear->name,
            'gender' => 'L',
            'status' => 'Aktif',
        ]);

        return Achievement::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $academicYear->id,
            'type' => $type,
        ]);
    }
}
