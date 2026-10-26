<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\FinalGrade;
use App\Models\GraduationStatus;
use App\Models\Permission;
use App\Models\ReportCardGrade;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\FinalGradeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinalGradeManualSubjectsTest extends TestCase
{
    use RefreshDatabase;

    public function test_final_grade_uses_only_eight_subjects_and_preserves_existing_data(): void
    {
        $permissions = collect(['grades.edit', 'grades.view'])->map(
            fn (string $name) => Permission::create(['name' => $name])
        );
        $role = Role::create(['name' => 'Admin']);
        $role->permissions()->attach($permissions->pluck('id'));
        $user = User::create([
            'name' => 'Admin Nilai',
            'username' => 'admin-final-grade',
            'email' => 'admin-final-grade@example.test',
            'password' => 'password',
            'role_id' => $role->id,
            'status' => 'Aktif',
        ]);

        $subjectNames = [
            'Pendidikan Agama dan Budi Pekerti',
            'Pendidikan Pancasila',
            'Bahasa Indonesia',
            'Matematika',
            'Ilmu Pengetahuan Alam dan Sosial',
            'Seni Budaya dan Prakarya',
            'Bahasa Inggris',
            'PJOK',
            'Baca Tulis Alquran',
            'Bahasa Daerah Palembang',
        ];
        $subjects = collect($subjectNames)->map(
            fn (string $name, int $index) => Subject::create([
                'code' => 'FG-' . $index,
                'name' => $name,
                'category' => 'Intrakurikuler',
                'status' => 'Aktif',
            ])
        );
        $student = Student::create([
            'nis' => 'FINAL-GRADE-01',
            'nisn' => 'FINAL-GRADE-NISN-01',
            'name' => 'Siswa Nilai Akhir',
            'gender' => 'P',
            'status' => 'Aktif',
        ]);
        $previousYear = $this->createYear('2024/2025', '2024-07-01', '2025-06-30');
        $graduationYear = $this->createYear('2025/2026', '2025-07-01', '2026-06-30', 'active');
        $gradeFiveClass = $this->createClass($previousYear, '5A', 5);
        $gradeSixClass = $this->createClass($graduationYear, '6A', 6);
        ClassStudent::create([
            'class_id' => $gradeFiveClass->id,
            'student_id' => $student->id,
            'status' => 'Naik Kelas',
        ]);
        ClassStudent::create([
            'class_id' => $gradeSixClass->id,
            'student_id' => $student->id,
            'status' => 'Aktif',
        ]);

        $schoolSubject = $subjects->first();
        foreach ([
            [$previousYear, 'Ganjil', $gradeFiveClass, 70],
            [$previousYear, 'Genap', $gradeFiveClass, 80],
            [$graduationYear, 'Ganjil', $gradeSixClass, 90],
            [$graduationYear, 'Genap', $gradeSixClass, 100],
        ] as [$year, $semesterName, $class, $score]) {
            $semester = Semester::create([
                'academic_year_id' => $year->id,
                'name' => $semesterName,
            ]);
            ReportCardGrade::create([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'subject_id' => $schoolSubject->id,
                'semester_id' => $semester->id,
                'score' => $score,
            ]);
        }

        $legacyLocalGrade = FinalGrade::create([
            'student_id' => $student->id,
            'class_id' => $gradeSixClass->id,
            'subject_id' => $subjects[8]->id,
            'academic_year_id' => $graduationYear->id,
            'school_score' => 77,
            'practical_score' => 78,
            'written_score' => 79,
            'exam_score' => 78.5,
            'final_score' => 77.6,
        ]);
        $graduationStatus = GraduationStatus::create([
            'student_id' => $student->id,
            'class_id' => $gradeSixClass->id,
            'academic_year_id' => $graduationYear->id,
            'status' => 'LULUS',
            'certificate_number' => 'IJZ-EXISTING-001',
        ]);

        $mapping = app(FinalGradeService::class)->getIjazahSubjectMapping();
        $this->assertCount(8, $mapping);
        $this->assertNotContains('Baca Tulis Alquran', $mapping->pluck('ijazah_name')->all());
        $this->assertNotContains('Bahasa Daerah Palembang', $mapping->pluck('ijazah_name')->all());

        $this->actingAs($user)
            ->get(route('final-grades.edit', [$gradeSixClass, $student]))
            ->assertOk()
            ->assertSeeText('Pendidikan Jasmani, Olahraga dan Kesehatan')
            ->assertDontSee('manual_subjects', false)
            ->assertDontSeeText('Baca Tulis Alquran')
            ->assertDontSeeText('Bahasa Daerah Palembang')
            ->assertDontSeeText('BTA')
            ->assertDontSeeText('Muatan Lokal');

        $subjectId = $schoolSubject->id;
        $this->put(route('final-grades.update', [$gradeSixClass, $student]), [
            'practical_scores' => [$subjectId => 80],
            'written_scores' => [$subjectId => 80],
        ])->assertRedirect();

        $this->assertDatabaseHas('final_grades', [
            'id' => $legacyLocalGrade->id,
            'subject_id' => $subjects[8]->id,
            'school_score' => 77,
            'final_score' => 77.6,
        ]);
        $this->assertDatabaseHas('graduation_statuses', [
            'id' => $graduationStatus->id,
            'status' => 'LULUS',
            'certificate_number' => 'IJZ-EXISTING-001',
        ]);
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $gradeSixClass->id,
            'subject_id' => $schoolSubject->id,
            'score' => 100,
        ]);

        $service = app(FinalGradeService::class);
        $storedGrades = $service->getStoredGrades(
            $student->id,
            $gradeSixClass->id,
            $graduationYear->id
        );
        $this->assertCount(8, $storedGrades);
        $this->assertSame(83.0, $service->calculateFinalAverage($storedGrades));
        $this->get(route('final-grades.edit', [$gradeSixClass, $student]))
            ->assertOk()
            ->assertSee('83.00')
            ->assertDontSee('manual_subjects', false);

        $pdfHtml = $this->renderFinalGradeHtml($student, $gradeSixClass);
        $this->assertStringNotContainsString('BTA', $pdfHtml);
        $this->assertStringNotContainsString('Bahasa Daerah Palembang', $pdfHtml);
        $this->assertStringContainsString('83.00', $pdfHtml);

        $this->get(route('final-grades.print', [$gradeSixClass, $student]))
            ->assertOk();
    }

    public function test_average_uses_only_non_null_scores_from_the_eight_mapped_subjects(): void
    {
        $grades = collect(range(81, 88))
            ->map(fn (int $score) => (object) ['final_score' => (float) $score])
            ->push((object) ['final_score' => null]);
        $service = app(FinalGradeService::class);
        $this->assertSame(
            84.5,
            $service->calculateFinalAverage($grades)
        );
        $this->assertNull($service->calculateFinalAverage(collect([
            (object) ['final_score' => null],
        ])));
    }

    private function renderFinalGradeHtml(Student $student, SchoolClass $class): string
    {
        $service = app(FinalGradeService::class);
        $storedGrades = $service->getStoredGrades($student->id, $class->id, $class->academic_year_id);

        return view('reports.nilai-akhir.print', [
            'student' => $student,
            'class' => $class,
            'academicYear' => $class->academicYear,
            'ijazahMapping' => $service->getIjazahSubjectMapping(),
            'storedGrades' => $storedGrades,
            'graduationStatus' => GraduationStatus::where([
                'student_id' => $student->id,
                'class_id' => $class->id,
                'academic_year_id' => $class->academic_year_id,
            ])->first(),
            'attendance' => null,
            'avgSchoolScore' => $service->calculateAverage($storedGrades, 'school_score'),
            'avgFinalScore' => $service->calculateFinalAverage($storedGrades),
            'homeroomTeacher' => null,
            'school' => null,
            'headmaster' => null,
            'headmasterNip' => null,
            'letterheadImage' => '',
        ])->render();
    }

    private function createYear(
        string $name,
        string $startDate,
        string $endDate,
        string $status = 'inactive'
    ): AcademicYear {
        return AcademicYear::create([
            'name' => $name,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => $status,
        ]);
    }

    private function createClass(AcademicYear $year, string $name, int $grade): SchoolClass
    {
        return SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => $name,
            'grade_level' => $grade,
            'status' => 'Aktif',
        ]);
    }
}
