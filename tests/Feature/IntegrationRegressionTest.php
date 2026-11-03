<?php

namespace Tests\Feature;

use App\Http\Controllers\RekapRapotController;
use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\Permission;
use App\Models\RekapAbsensi;
use App\Models\ReportCardGrade;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class IntegrationRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_and_pdf_use_the_selected_year_class_semester_grades_and_attendance(): void
    {
        $oldYear = $this->createAcademicYear('2024/2025', '2024-07-01', '2025-06-30', 'inactive');
        $selectedYear = $this->createAcademicYear('2025/2026', '2025-07-01', '2026-06-30', 'active');
        $oldClass = $this->createClass($oldYear, '1B');
        $selectedClass = $this->createClass($selectedYear, '1A');
        $student = Student::create([
            'nis' => 'INTEGRATION-007',
            'nisn' => 'INTEGRATION-007-NISN',
            'name' => 'Siswa Uji Integrasi',
            'gender' => 'L',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $oldClass->id,
            'student_id' => $student->id,
            'status' => 'Naik Kelas',
        ]);
        ClassStudent::create([
            'class_id' => $selectedClass->id,
            'student_id' => $student->id,
            'status' => 'Aktif',
        ]);
        $oldSemester = $this->createSemester($oldYear, 'Ganjil');
        $selectedSemester = $this->createSemester($selectedYear, 'Ganjil');
        $subject = Subject::create([
            'code' => 'INTEG-MAT',
            'name' => 'Matematika',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
        $oldClass->subjects()->attach($subject->id);
        $selectedClass->subjects()->attach($subject->id);
        ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $oldClass->id,
            'subject_id' => $subject->id,
            'semester_id' => $oldSemester->id,
            'score' => 99,
            'learning_outcome' => 'Capaian tahun lama.',
        ]);
        ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $selectedClass->id,
            'subject_id' => $subject->id,
            'semester_id' => $selectedSemester->id,
            'score' => 82,
            'learning_outcome' => 'Capaian tahun terpilih.',
        ]);
        RekapAbsensi::create([
            'student_id' => $student->id,
            'class_id' => $oldClass->id,
            'semester_id' => $oldSemester->id,
            'sakit' => 9,
            'izin' => 0,
            'tanpa_keterangan' => 0,
        ]);
        RekapAbsensi::create([
            'student_id' => $student->id,
            'class_id' => $selectedClass->id,
            'semester_id' => $selectedSemester->id,
            'sakit' => 2,
            'izin' => 1,
            'tanpa_keterangan' => 0,
        ]);

        $requestData = [
            'academic_year_id' => $selectedYear->id,
            'class_id' => $selectedClass->id,
            'semester' => 'ganjil',
        ];
        $view = app(RekapRapotController::class)->print(
            Request::create('/laporan/rekap-rapot', 'GET', $requestData),
            $student
        );
        $html = $view->render();

        $this->assertSame($selectedYear->id, $view->getData()['academicYear']->id);
        $this->assertSame($selectedClass->id, $view->getData()['class']->id);
        $this->assertSame(
            82.0,
            (float) $view->getData()['grades']->get($selectedSemester->id)->first()->score
        );
        $this->assertSame(2, $view->getData()['attendanceGanjil']->sakit);
        $this->assertStringContainsString('2025/2026', $html);
        $this->assertStringContainsString('Capaian tahun terpilih.', $html);
        $this->assertStringContainsString('82', $html);
        $this->assertStringContainsString('2 Hari', $html);
        $this->assertStringNotContainsString('Capaian tahun lama.', $html);
        $this->assertStringNotContainsString('99', $html);
        $this->assertStringNotContainsString('9 Hari', $html);

        $role = Role::create(['name' => 'Admin Rapor Integrasi']);
        $role->permissions()->attach([
            Permission::create(['name' => 'report_cards.view'])->id,
            Permission::create(['name' => 'report_cards.export'])->id,
        ]);
        $admin = User::create([
            'name' => 'Admin Rapor Integrasi',
            'username' => 'admin-rapor-integrasi',
            'email' => 'admin-rapor-integrasi@example.test',
            'password' => 'password',
            'role_id' => $role->id,
            'status' => 'Aktif',
        ]);
        $pdf = $this->actingAs($admin)->get(route(
            'rekap-rapot.print',
            $requestData + ['student' => $student->id, 'pdf' => true]
        ));

        $this->assertSame('application/pdf', $pdf->headers->get('Content-Type'));
        $this->assertStringContainsString('.pdf', $pdf->headers->get('Content-Disposition'));

        $pdfText = $this->extractPdfText($pdf->getContent());
        foreach ([
            'Siswa Uji Integrasi',
            'INTEGRATION-007-NISN',
            'INTEGRATION-007',
            '1A',
            'Ganjil',
            '2025/2026',
            'Matematika',
            '82',
            '2 Hari',
            '1 Hari',
            '0 Hari',
        ] as $expectedValue) {
            $this->assertStringContainsString($expectedValue, $pdfText);
        }

        foreach (['2024/2025', '1B', 'Capaian tahun lama.', '99', '9 Hari'] as $oldValue) {
            $this->assertStringNotContainsString($oldValue, $pdfText);
        }
    }

    public function test_student_excel_export_contains_the_application_student_record(): void
    {
        $admin = $this->createSuperAdmin();
        $year = $this->createAcademicYear('2025/2026', '2025-07-01', '2026-06-30', 'active');
        $class = $this->createClass($year, '4A');
        $student = Student::create([
            'nis' => 'EXPORT-009',
            'nisn' => 'EXPORT-009-NISN',
            'name' => 'Siswa Ekspor Teruji',
            'gender' => 'P',
            'birth_place' => 'Palembang',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($admin)->get(route('students.export.excel'));
        $response->assertOk();
        $this->assertStringContainsString(
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            $response->headers->get('Content-Type')
        );

        $temporaryFile = tempnam(sys_get_temp_dir(), 'biduk-export-');
        $this->assertNotFalse($temporaryFile);

        try {
            file_put_contents($temporaryFile, $response->streamedContent());
            $sheet = IOFactory::load($temporaryFile)->getActiveSheet();

            $this->assertSame('EXPORT-009', $sheet->getCell('B4')->getValue());
            $this->assertSame('EXPORT-009-NISN', $sheet->getCell('C4')->getValue());
            $this->assertSame('Siswa Ekspor Teruji', $sheet->getCell('D4')->getValue());
            $this->assertSame('P', $sheet->getCell('F4')->getValue());
        } finally {
            if (is_file($temporaryFile)) {
                unlink($temporaryFile);
            }
        }
    }

    public function test_soft_deleting_student_keeps_historical_class_grade_and_health_relations(): void
    {
        $admin = $this->createSuperAdmin();
        $year = $this->createAcademicYear('2025/2026', '2025-07-01', '2026-06-30', 'active');
        $class = $this->createClass($year, '2A');
        $semester = $this->createSemester($year, 'Ganjil');
        $subject = Subject::create([
            'code' => 'INTEG-DELETE',
            'name' => 'Bahasa Indonesia',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
        $student = Student::create([
            'nis' => 'DELETE-011',
            'nisn' => 'DELETE-011-NISN',
            'name' => 'Siswa Arsip',
            'gender' => 'L',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'Aktif',
        ]);
        ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $semester->id,
            'score' => 88,
        ]);
        RekapAbsensi::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'semester_id' => $semester->id,
            'sakit' => 1,
            'izin' => 2,
            'tanpa_keterangan' => 0,
        ]);
        \App\Models\StudentHealth::create([
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'semester_id' => $semester->id,
            'notes' => 'Catatan kesehatan arsip',
        ]);

        $this->actingAs($admin)
            ->delete(route('students.destroy', $student))
            ->assertRedirect(route('students.index'));

        $this->assertSoftDeleted('students', ['id' => $student->id]);
        $this->assertDatabaseHas('class_student', [
            'student_id' => $student->id,
            'class_id' => $class->id,
        ]);
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'score' => 88,
        ]);
        $this->assertDatabaseHas('rekap_absensis', [
            'student_id' => $student->id,
            'sakit' => 1,
            'izin' => 2,
        ]);
        $this->assertDatabaseHas('student_healths', [
            'student_id' => $student->id,
            'notes' => 'Catatan kesehatan arsip',
        ]);
    }

    private function createSuperAdmin(): User
    {
        return User::create([
            'name' => 'Super Admin Integrasi',
            'username' => 'super-admin-integration-regression',
            'email' => 'super-admin-integration-regression@example.test',
            'password' => 'password',
            'role_id' => Role::create(['name' => 'Super Admin'])->id,
            'status' => 'Aktif',
        ]);
    }

    private function createAcademicYear(
        string $name,
        string $startDate,
        string $endDate,
        string $status
    ): AcademicYear {
        return AcademicYear::create([
            'name' => $name,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
        ]);
    }

    private function createClass(AcademicYear $year, string $name): SchoolClass
    {
        return SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => $name,
            'grade_level' => 1,
            'status' => 'Aktif',
        ]);
    }

    private function createSemester(AcademicYear $year, string $name): Semester
    {
        return Semester::create([
            'academic_year_id' => $year->id,
            'name' => $name,
        ]);
    }

    private function extractPdfText(string $pdf): string
    {
        $pdftotext = (new ExecutableFinder)->find('pdftotext');
        if ($pdftotext === null) {
            $this->markTestSkipped('pdftotext is required to compare rendered PDF content.');
        }

        $pdfPath = tempnam(sys_get_temp_dir(), 'biduk-report-');
        $this->assertNotFalse($pdfPath);

        try {
            file_put_contents($pdfPath, $pdf);
            $process = new Process([$pdftotext, '-layout', $pdfPath, '-']);
            $process->run();
            $this->assertTrue(
                $process->isSuccessful(),
                'PDF text extraction failed: '.$process->getErrorOutput()
            );

            return $process->getOutput();
        } finally {
            if (is_file($pdfPath)) {
                unlink($pdfPath);
            }
        }
    }
}
