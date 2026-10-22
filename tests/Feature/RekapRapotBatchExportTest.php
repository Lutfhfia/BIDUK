<?php

namespace Tests\Feature;

use App\Http\Controllers\RekapRapotController;
use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\ReportCardGrade;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
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
        $ganjil = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Ganjil',
        ]);
        $genap = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Genap',
        ]);
        $subject = Subject::create([
            'code' => 'MAT-KELAS6',
            'name' => 'Matematika',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
        $class->subjects()->attach($subject->id);
        ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $ganjil->id,
            'score' => 90,
            'learning_outcome' => 'Memahami konsep hitung.',
            'skill_score' => 89,
            'skill_learning_outcome' => 'Menerapkan operasi hitung.',
        ]);
        ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $genap->id,
            'score' => 80,
            'learning_outcome' => 'Memecahkan masalah.',
            'skill_score' => 85,
            'skill_learning_outcome' => 'Menerapkan operasi hitung.',
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
        $html = $response->render();
        $intracurricularTable = explode('<div class="bottom-heading"', $html, 2)[0];
        $this->assertStringContainsString('NILAI AKHIR *)', $html);
        $this->assertStringContainsString('<th colspan="2">NILAI RATA-RATA RAPOR</th>', $intracurricularTable);
        $this->assertStringContainsString('<th colspan="2">NILAI UJIAN SEKOLAH</th>', $intracurricularTable);
        $this->assertStringContainsString('<th colspan="2">NILAI SEKOLAH</th>', $intracurricularTable);
        $this->assertStringContainsString('<th>CAPAIAN PEMBELAJARAN</th>', $intracurricularTable);
        $this->assertStringContainsString('Memecahkan masalah.', $html);
        $this->assertStringContainsString('Menerapkan operasi hitung.', $html);
        $this->assertStringNotContainsString('Pengetahuan:', $intracurricularTable);
        $this->assertStringNotContainsString('Keterampilan:', $intracurricularTable);
        $this->assertStringNotContainsString('90,00', $intracurricularTable);
        $this->assertStringNotContainsString('<th colspan="2">SEMESTER GANJIL</th>', $intracurricularTable);
        $this->assertStringNotContainsString('<th colspan="2">SEMESTER GENAP</th>', $intracurricularTable);
    }

    public function test_standard_report_uses_the_requested_two_semester_score_and_outcome_table(): void
    {
        $academicYear = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);
        $class = SchoolClass::create([
            'academic_year_id' => $academicYear->id,
            'name' => '1A',
            'grade_level' => 1,
            'status' => 'Aktif',
        ]);
        $student = Student::create([
            'nis' => 'NIS-REPORT',
            'nisn' => 'NISN-REPORT',
            'name' => 'Siswa Rapor',
            'gender' => 'L',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'Aktif',
        ]);
        $subject = Subject::create([
            'code' => 'MAT-REPORT',
            'name' => 'Matematika',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
        $class->subjects()->attach($subject->id);
        $ganjil = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Ganjil',
        ]);
        $genap = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Genap',
        ]);
        ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $ganjil->id,
            'score' => 90,
            'learning_outcome' => 'Menguasai konsep bilangan.',
        ]);
        ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $genap->id,
            'score' => 80,
            'learning_outcome' => 'Menerapkan operasi hitung.',
        ]);

        $response = app(RekapRapotController::class)->batch(
            Request::create('/laporan/rekap-rapot/batch', 'GET', [
                'academic_year_id' => $academicYear->id,
                'class_id' => $class->id,
                'pdf' => false,
            ])
        );
        $html = $response->render();

        $this->assertStringContainsString('<th>NILAI AKHIR</th>', $html);
        $this->assertSame(2, substr_count($html, '<th>CAPAIAN PEMBELAJARAN</th>'));
        $this->assertStringContainsString('Menguasai konsep bilangan.', $html);
        $this->assertStringContainsString('Menerapkan operasi hitung.', $html);
        $this->assertStringContainsString('>90.00</td>', $html);
        $this->assertStringContainsString('>80.00</td>', $html);
        $this->assertStringNotContainsString('NILAI PENGETAHUAN / KETERAMPILAN', $html);
    }
}
