<?php

namespace Tests\Feature;

use App\Http\Controllers\BukuIndukController;
use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class BukuIndukBatchPdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_class_preview_includes_students_with_historical_class_assignment_status(): void
    {
        [$academicYear, $class, $student] = $this->createClassStudent('Naik Kelas');

        $view = app(BukuIndukController::class)->index(Request::create(
            '/laporan/buku-induk',
            'GET',
            [
                'academic_year_id' => $academicYear->id,
                'class_id' => $class->id,
                'status' => 'aktif',
                'print_type' => 'class',
            ]
        ));

        $this->assertTrue($view->getData()['students']->contains('id', $student->id));
    }

    public function test_class_pdf_contains_students_with_historical_class_assignment_status(): void
    {
        [$academicYear, $class] = $this->createClassStudent('Naik Kelas');
        $this->addStudentToClass($class, 'NIS-SECOND', 'Siswa Kedua');

        $response = app(BukuIndukController::class)->batchPdf(Request::create(
            '/laporan/buku-induk/cetak-batch-pdf',
            'GET',
            [
                'academic_year_id' => $academicYear->id,
                'class_id' => $class->id,
                'status' => 'aktif',
                'print_type' => 'class',
                'student_id' => Student::where('nis', 'NIS-TEST')->value('id'),
                'search' => 'Siswa Uji (NIS-TEST)',
                'pdf' => '1',
            ]
        ));

        $this->assertSame('application/pdf', $response->headers->get('content-type'));
        $this->assertSame(4, $this->pdfPageCount($response->getContent()));
    }

    public function test_all_class_pdf_ignores_the_preview_student_label_as_a_search_filter(): void
    {
        [$academicYear, $class, $student] = $this->createClassStudent('Aktif');
        $otherClass = SchoolClass::create([
            'academic_year_id' => $academicYear->id,
            'name' => '6B',
            'grade_level' => 6,
            'status' => 'Aktif',
        ]);
        $this->addStudentToClass($otherClass, 'NIS-SECOND', 'Siswa Kedua');

        $response = app(BukuIndukController::class)->batchPdf(Request::create(
            '/laporan/buku-induk/cetak-batch-pdf',
            'GET',
            [
                'academic_year_id' => $academicYear->id,
                'status' => 'aktif',
                'print_type' => 'all',
                'student_id' => $student->id,
                'search' => 'Siswa Uji (NIS-TEST)',
                'pdf' => '1',
            ]
        ));

        $this->assertSame(4, $this->pdfPageCount($response->getContent()));
    }

    public function test_previewing_a_student_keeps_the_class_filter_available_for_export(): void
    {
        [$academicYear, $class, $student] = $this->createClassStudent('Naik Kelas');

        $view = app(BukuIndukController::class)->index(Request::create(
            '/laporan/buku-induk',
            'GET',
            [
                'academic_year_id' => $academicYear->id,
                'class_id' => $class->id,
                'status' => 'aktif',
                'print_type' => 'class',
                'student_id' => $student->id,
            ]
        ));

        $this->assertTrue($view->getData()['selectedStudent']->is($student));
        $this->assertTrue($view->getData()['students']->contains('id', $student->id));
    }

    public function test_single_student_print_uses_the_students_position_in_its_class(): void
    {
        [$academicYear, $class, $student] = $this->createClassStudent('Aktif');
        $student->update(['student_number' => '=ROW()-5']);
        $this->addStudentToClass($class, 'NIS-FIRST', 'A Siswa');

        $view = app(BukuIndukController::class)->print(
            Request::create('/laporan/buku-induk/cetak', 'GET', [
                'academic_year_id' => $academicYear->id,
                'class_id' => $class->id,
                'print_type' => 'class',
            ]),
            $student
        );

        $this->assertSame(2, (int) $view->getData()['student']->student_number);
    }

    private function createClassStudent(string $assignmentStatus): array
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
            'status' => 'Aktif',
        ]);

        $student = Student::create([
            'nis' => 'NIS-TEST',
            'nisn' => 'NISN-TEST',
            'name' => 'Siswa Uji',
            'gender' => 'L',
            'status' => 'Aktif',
        ]);

        ClassStudent::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => $assignmentStatus,
        ]);

        return [$academicYear, $class, $student];
    }

    private function addStudentToClass(SchoolClass $class, string $nis, string $name): Student
    {
        $student = Student::create([
            'nis' => $nis,
            'nisn' => 'NISN-'.$nis,
            'name' => $name,
            'gender' => 'L',
            'status' => 'Aktif',
        ]);

        ClassStudent::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'Aktif',
        ]);

        return $student;
    }

    private function pdfPageCount(string $pdf): int
    {
        preg_match_all('/\/Type\s*\/Page\b/', $pdf, $matches);

        return count($matches[0]);
    }
}
