<?php

namespace Tests\Feature;

use App\Http\Controllers\RekapRapotController;
use App\Http\Controllers\ReportCardGradeController;
use App\Http\Controllers\SubjectController;
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
        $this->assertStringNotContainsString('Menerapkan operasi hitung.', $html);
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

        $genapResponse = app(RekapRapotController::class)->print(
            Request::create('/laporan/rekap-rapot', 'GET', [
                'academic_year_id' => $academicYear->id,
                'class_id' => $class->id,
                'semester' => 'genap',
            ]),
            $student
        );
        $genapHtml = $genapResponse->render();
        $intracurricularTable = explode(
            '<div class="section-title">C. EKSTRAKURIKULER</div>',
            explode('<div class="section-title">B. INTRAKURIKULER</div>', $genapHtml, 2)[1],
            2
        )[0];

        $this->assertStringContainsString('<th colspan="2">SEMESTER GENAP</th>', $intracurricularTable);
        $this->assertStringNotContainsString('<th colspan="2">SEMESTER GANJIL</th>', $intracurricularTable);
        $this->assertStringContainsString('>80.00</td>', $intracurricularTable);
        $this->assertStringNotContainsString('>90.00</td>', $intracurricularTable);
    }

    public function test_report_subjects_follow_code_order_without_moving_scores_between_subjects(): void
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
            'nis' => 'NIS-CODE-ORDER',
            'nisn' => 'NISN-CODE-ORDER',
            'name' => 'Siswa Urutan Kode',
            'gender' => 'L',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'Aktif',
        ]);
        $ganjil = Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Ganjil',
        ]);

        $subjects = collect([
            ['code' => 'H', 'name' => 'Matematika', 'score' => 90, 'sort_order' => 1],
            ['code' => 'C', 'name' => 'IPAS', 'score' => 85, 'sort_order' => 2],
            ['code' => 'A', 'name' => 'Bahasa Indonesia', 'score' => 88, 'sort_order' => 3],
        ])->map(function (array $data) use ($academicYear, $class, $ganjil, $student) {
            $subject = Subject::create([
                'code' => $data['code'],
                'name' => $data['name'],
                'category' => 'Intrakurikuler',
                'status' => 'Aktif',
                'sort_order' => $data['sort_order'],
            ]);
            $class->subjects()->attach($subject->id);
            ReportCardGrade::create([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'subject_id' => $subject->id,
                'semester_id' => $ganjil->id,
                'score' => $data['score'],
            ]);

            return $subject;
        });

        $masterView = app(SubjectController::class)->index(Request::create('/subjects'));
        $this->assertSame(
            ['A', 'C', 'H'],
            $masterView->getData()['subjects']->getCollection()->pluck('code')->all()
        );

        $inputView = app(ReportCardGradeController::class)->edit(
            Request::create('/rapot/edit', 'GET'),
            $class,
            $student
        );
        $inputData = $inputView->getData();
        $this->assertSame(['A', 'C', 'H'], $inputData['subjects']->pluck('code')->all());
        foreach ($subjects as $subject) {
            $grade = $inputData['grades']->get($ganjil->id . '_' . $subject->id);
            $this->assertSame((string) $subject->reportCardGrades()->value('score'), (string) $grade->score);
        }

        $response = app(RekapRapotController::class)->print(
            Request::create('/laporan/rekap-rapot', 'GET', [
                'academic_year_id' => $academicYear->id,
                'class_id' => $class->id,
                'semester' => 'ganjil',
            ]),
            $student
        );
        $html = $response->render();

        $this->assertSame(['A', 'C', 'H'], $response->getData()['subjects']->pluck('code')->all());
        $this->assertCount(3, $response->getData()['subjects']);
        $rows = collect([]);
        preg_match_all('/<tr>(.*?)<\/tr>/s', $html, $matches);
        foreach ($matches[1] as $row) {
            $rows->push(preg_replace('/\s+/', ' ', strip_tags($row)));
        }

        $languageRow = $rows->first(fn (string $row) => str_contains($row, 'Bahasa Indonesia'));
        $scienceRow = $rows->first(fn (string $row) => str_contains($row, 'IPAS'));
        $mathRow = $rows->first(fn (string $row) => str_contains($row, 'Matematika'));
        $this->assertStringContainsString('88.00', $languageRow);
        $this->assertStringContainsString('85.00', $scienceRow);
        $this->assertStringContainsString('90.00', $mathRow);
        $this->assertLessThan(strpos($html, 'IPAS'), strpos($html, 'Bahasa Indonesia'));
        $this->assertLessThan(strpos($html, 'Matematika'), strpos($html, 'IPAS'));
    }
}
