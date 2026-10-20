<?php

namespace Tests\Feature;

use App\Http\Controllers\RekapRapotController;
use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class RekapRapotBatchExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_class_batch_includes_students_from_a_nonactive_graduated_grade_six_class(): void
    {
        $academicYear = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);

        $class = SchoolClass::create([
            'academic_year_id' => $academicYear->id,
            'name' => '6A',
            'grade_level' => 6,
            'status' => 'Nonaktif',
        ]);

        $student = Student::create([
            'nis' => 'NIS-TEST',
            'nisn' => 'NISN-TEST',
            'name' => 'Siswa Kelas Enam',
            'gender' => 'L',
            'status' => 'Aktif',
        ]);

        ClassStudent::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'Lulus',
        ]);

        $response = app(RekapRapotController::class)->batch(
            Request::create('/laporan/rekap-rapot/batch', 'GET', [
                'academic_year_id' => $academicYear->id,
                'class_id' => 'all',
                'pdf' => false,
            ])
        );

        $reports = $response->getData()['reports'];

        $this->assertCount(1, $reports);
        $this->assertSame($class->id, $reports->first()['class']->id);
        $this->assertTrue($reports->first()['isFinalClass']);
        $this->assertStringContainsString('NILAI AKHIR *)', $response->render());
    }
}
