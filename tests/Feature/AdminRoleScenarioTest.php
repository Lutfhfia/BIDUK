<?php

namespace Tests\Feature;

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
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class AdminRoleScenarioTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_manages_academic_years_without_overwriting_other_period_data(): void
    {
        $admin = $this->createUserWithPermissions('Admin Tahun Ajaran', [
            'academic_years.view',
            'academic_years.create',
            'academic_years.edit',
        ]);
        $activeYear = $this->createYear('2024/2025', '2024-07-01', '2025-06-30', 'active');
        $untouchedYear = $this->createYear('2023/2024', '2023-07-01', '2024-06-30', 'inactive');
        $class = $this->createClass($activeYear, '4A');
        $semester = $this->createSemester($activeYear, 'Ganjil');
        $student = $this->createStudent('TA-OLD-001', 'Siswa Periode Lama');
        $subject = $this->createSubject('TA-MATH', 'Matematika');
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
            'score' => 87,
        ]);

        $this->actingAs($admin)
            ->post(route('academic-years.store'), [
                'name' => '2025/2026',
                'start_date' => '2025-07-01',
                'end_date' => '2026-06-30',
                'status' => 'inactive',
            ])
            ->assertRedirect(route('academic-years.index'));

        $newYear = AcademicYear::where('name', '2025/2026')->firstOrFail();
        $this->assertSame('active', $activeYear->fresh()->status);
        $this->assertSame(['Ganjil', 'Genap'], $newYear->semesters()->orderBy('id')->pluck('name')->all());

        $this->put(route('academic-years.update', $newYear), [
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ])->assertRedirect(route('academic-years.index'));

        $this->assertDatabaseHas('academic_years', [
            'id' => $activeYear->id,
            'name' => '2024/2025',
            'status' => 'inactive',
        ]);
        $this->assertDatabaseHas('academic_years', [
            'id' => $untouchedYear->id,
            'name' => '2023/2024',
            'status' => 'inactive',
        ]);
        $this->assertDatabaseHas('classes', [
            'id' => $class->id,
            'academic_year_id' => $activeYear->id,
            'name' => '4A',
        ]);
        $this->assertDatabaseHas('semesters', [
            'id' => $semester->id,
            'academic_year_id' => $activeYear->id,
            'name' => 'Ganjil',
        ]);
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'semester_id' => $semester->id,
            'score' => 87,
        ]);
    }

    public function test_admin_without_year_edit_permissions_cannot_change_academic_years(): void
    {
        $admin = $this->createUserWithPermissions('Admin Lihat Tahun', [
            'academic_years.view',
        ]);
        $year = $this->createYear('2025/2026', '2025-07-01', '2026-06-30', 'active');

        $this->actingAs($admin)
            ->get(route('academic-years.index'))
            ->assertOk();

        $this->post(route('academic-years.store'), [
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'status' => 'inactive',
        ])->assertForbidden();

        $this->put(route('academic-years.update', $year), [
            'name' => 'Tahun Ditimpa',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'inactive',
        ])->assertForbidden();

        $this->assertDatabaseHas('academic_years', [
            'id' => $year->id,
            'name' => '2025/2026',
            'status' => 'active',
        ]);
    }

    public function test_admin_creates_and_updates_subjects_without_breaking_existing_class_or_grade_links(): void
    {
        $admin = $this->createUserWithPermissions('Admin Mata Pelajaran', [
            'subjects.view',
            'subjects.create',
            'subjects.edit',
        ]);
        $year = $this->createYear('2025/2026', '2025-07-01', '2026-06-30', 'active');
        $class = $this->createClass($year, '5A');
        $semester = $this->createSemester($year, 'Ganjil');
        $student = $this->createStudent('SUBJECT-001', 'Siswa Mata Pelajaran');
        $existingSubject = $this->createSubject('SUB-EXISTING', 'Bahasa Indonesia');
        $class->subjects()->attach($existingSubject->id);
        ClassStudent::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'Aktif',
        ]);
        $grade = ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $existingSubject->id,
            'semester_id' => $semester->id,
            'score' => 91,
        ]);

        $this->actingAs($admin)
            ->post(route('subjects.store'), [
                'code' => 'SUB-SCIENCE',
                'name' => 'Ilmu Pengetahuan Terpadu',
                'category' => 'Intrakurikuler',
                'status' => 'Aktif',
            ])
            ->assertRedirect(route('subjects.index'));

        $createdSubject = Subject::where('code', 'SUB-SCIENCE')->firstOrFail();
        $class->subjects()->attach($createdSubject->id);

        $this->put(route('subjects.update', $createdSubject), [
            'code' => 'SUB-SCIENCE',
            'name' => 'Ilmu Pengetahuan Alam dan Sosial',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ])->assertRedirect(route('subjects.index'));

        $this->assertDatabaseHas('subjects', [
            'id' => $createdSubject->id,
            'code' => 'SUB-SCIENCE',
            'name' => 'Ilmu Pengetahuan Alam dan Sosial',
            'status' => 'Aktif',
        ]);
        $this->assertDatabaseHas('subjects', [
            'id' => $existingSubject->id,
            'code' => 'SUB-EXISTING',
            'name' => 'Bahasa Indonesia',
        ]);
        $this->assertDatabaseHas('class_subject', [
            'class_id' => $class->id,
            'subject_id' => $createdSubject->id,
        ]);
        $this->assertDatabaseHas('class_subject', [
            'class_id' => $class->id,
            'subject_id' => $existingSubject->id,
        ]);
        $this->assertDatabaseHas('report_card_grades', [
            'id' => $grade->id,
            'subject_id' => $existingSubject->id,
            'score' => 91,
        ]);
    }

    public function test_admin_prints_selected_student_class_and_year_and_export_permission_is_enforced(): void
    {
        $admin = $this->createUserWithPermissions('Admin Cetak Rapor', [
            'report_cards.view',
            'report_cards.export',
        ]);
        $viewOnlyAdmin = $this->createUserWithPermissions('Admin Lihat Rapor', [
            'report_cards.view',
        ]);
        $oldYear = $this->createYear('2023/2024', '2023-07-01', '2024-06-30', 'inactive');
        $selectedYear = $this->createYear('2025/2026', '2025-07-01', '2026-06-30', 'active');
        $oldClass = $this->createClass($oldYear, 'KELAS-LAMA-2A', 2);
        $selectedClass = $this->createClass($selectedYear, 'KELAS-PILIHAN-3B', 3);
        $oldSemester = $this->createSemester($oldYear, 'Ganjil');
        $ganjil = $this->createSemester($selectedYear, 'Ganjil');
        $genap = $this->createSemester($selectedYear, 'Genap');
        $student = $this->createStudent('RAPOR-010', 'Siswa Rapor Pilihan');
        $subject = $this->createSubject('RAPOR-MATH', 'Matematika');
        $oldClass->subjects()->attach($subject->id);
        $selectedClass->subjects()->attach($subject->id);
        foreach ([[$oldClass, 'Naik Kelas'], [$selectedClass, 'Aktif']] as [$class, $status]) {
            ClassStudent::create([
                'class_id' => $class->id,
                'student_id' => $student->id,
                'status' => $status,
            ]);
        }
        ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $oldClass->id,
            'subject_id' => $subject->id,
            'semester_id' => $oldSemester->id,
            'score' => 61,
            'learning_outcome' => 'Capaian periode lama.',
        ]);
        ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $selectedClass->id,
            'subject_id' => $subject->id,
            'semester_id' => $ganjil->id,
            'score' => 84.5,
            'learning_outcome' => 'Capaian periode terpilih.',
        ]);
        ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $selectedClass->id,
            'subject_id' => $subject->id,
            'semester_id' => $genap->id,
            'score' => 88,
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
            'semester_id' => $ganjil->id,
            'sakit' => 2,
            'izin' => 1,
            'tanpa_keterangan' => 3,
        ]);
        RekapAbsensi::create([
            'student_id' => $student->id,
            'class_id' => $selectedClass->id,
            'semester_id' => $genap->id,
            'sakit' => 4,
            'izin' => 5,
            'tanpa_keterangan' => 6,
        ]);
        $reportParams = [
            'academic_year_id' => $selectedYear->id,
            'class_id' => $selectedClass->id,
            'semester' => 'ganjil_genap',
        ];

        $this->actingAs($admin)
            ->get(route('rekap-rapot.print', $reportParams + ['student' => $student->id]))
            ->assertOk()
            ->assertSeeText('Siswa Rapor Pilihan')
            ->assertSeeText('2025/2026')
            ->assertSeeText('KELAS-PILIHAN-3B');

        $pdf = $this->get(route(
            'rekap-rapot.print',
            $reportParams + ['student' => $student->id, 'pdf' => 1]
        ))->assertOk();
        $this->assertSame('application/pdf', $pdf->headers->get('Content-Type'));
        $pdfText = $this->extractPdfText($pdf->getContent());

        foreach ([
            'Siswa Rapor Pilihan',
            'RAPOR-010',
            'KELAS-PILIHAN-3B',
            '2025/2026',
            'Ganjil & Genap',
            'Matematika',
            '84.50',
            '88.00',
            '2 Hari',
            '1 Hari',
            '3 Hari',
            '4 Hari',
            '5 Hari',
            '6 Hari',
        ] as $value) {
            $this->assertStringContainsString($value, $pdfText);
        }
        foreach (['2023/2024', 'KELAS-LAMA-2A', 'Capaian periode lama.', '61.00', '9 Hari'] as $oldValue) {
            $this->assertStringNotContainsString($oldValue, $pdfText);
        }

        $this->actingAs($viewOnlyAdmin)
            ->get(route('rekap-rapot.index'))
            ->assertOk()
            ->assertSee('id="rrPreviewFrame"', false)
            ->assertDontSee('id="rrExportPreviewPdf"', false);
        $this->get(route('rekap-rapot.print', $reportParams + ['student' => $student->id]))
            ->assertOk();
        $this->get(route(
            'rekap-rapot.print',
            $reportParams + ['student' => $student->id, 'pdf' => 1]
        ))->assertForbidden();
        $this->get(route('rekap-rapot.batch', $reportParams + ['pdf' => 1]))
            ->assertForbidden();
    }

    public function test_headmaster_with_view_only_grade_permission_can_view_and_download_diploma_grades(): void
    {
        $headmaster = $this->createUserWithPermissions('Kepala Sekolah', ['grades.view']);
        $year = $this->createYear('2025/2026', '2025-07-01', '2026-06-30', 'active');
        $class = $this->createClass($year, '6A', 6);
        $semester = $this->createSemester($year, 'Ganjil');
        $student = $this->createStudent('GRADE-VIEW-ONLY', 'Siswa Nilai Read Only');
        $subject = $this->createSubject('GRADE-MATH', 'Matematika');
        ClassStudent::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'Aktif',
        ]);
        $grade = ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $semester->id,
            'score' => 76,
        ]);

        $this->actingAs($headmaster)
            ->get(route('report-card-grades.index', [
                'academic_year_id' => $year->id,
                'class_id' => $class->id,
            ]))
            ->assertOk()
            ->assertSee('Lihat Ijazah');
        $this->get(route('report-card-grades.history', $student))
            ->assertOk()
            ->assertSee('76');
        $this->get(route('report-card-grades.edit', [$class, $student]))->assertForbidden();
        $this->put(route('report-card-grades.update', [$class, $student]), [
            'ganjil_scores' => [$subject->id => 99],
        ])->assertForbidden();
        $this->delete(route('report-card-grades.destroy', [$class, $student]))->assertForbidden();
        $this->post(route('report-card-grades.import.preview'), [])->assertForbidden();
        $this->get(route('final-grades.edit', [$class, $student]))->assertForbidden();
        $this->get(route('final-grades.show', [$class, $student]))
            ->assertOk()
            ->assertSee('Lihat nilai akhir dan data ijazah')
            ->assertDontSee('Simpan Nilai Akhir')
            ->assertSee('<fieldset class="border-0 p-0 m-0" disabled>', false);
        $pdfResponse = $this->get(route('final-grades.print', [$class, $student]))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString(
            'attachment',
            $pdfResponse->headers->get('content-disposition')
        );
        $this->put(route('final-grades.update', [$class, $student]), [
            'exam_scores' => [$subject->id => 99],
        ])->assertForbidden();

        $this->assertDatabaseHas('report_card_grades', [
            'id' => $grade->id,
            'score' => 76,
        ]);
    }

    private function createUserWithPermissions(string $name, array $permissionNames): User
    {
        $role = Role::create(['name' => $name]);
        $permissions = collect($permissionNames)->map(
            fn (string $permissionName) => Permission::create(['name' => $permissionName])
        );
        $role->permissions()->attach($permissions->pluck('id'));

        return User::create([
            'name' => $name,
            'username' => str_replace(' ', '-', strtolower($name)),
            'email' => str_replace(' ', '-', strtolower($name)).'@example.test',
            'password' => 'password',
            'role_id' => $role->id,
            'status' => 'Aktif',
        ]);
    }

    private function createYear(string $name, string $start, string $end, string $status): AcademicYear
    {
        return AcademicYear::create([
            'name' => $name,
            'start_date' => $start,
            'end_date' => $end,
            'status' => $status,
        ]);
    }

    private function createClass(AcademicYear $year, string $name, int $gradeLevel = 1): SchoolClass
    {
        return SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => $name,
            'grade_level' => $gradeLevel,
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

    private function createStudent(string $nis, string $name): Student
    {
        return Student::create([
            'nis' => $nis,
            'nisn' => $nis.'-NISN',
            'name' => $name,
            'gender' => 'L',
            'status' => 'Aktif',
        ]);
    }

    private function createSubject(string $code, string $name): Subject
    {
        return Subject::create([
            'code' => $code,
            'name' => $name,
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
    }

    private function extractPdfText(string $pdf): string
    {
        $pdftotext = (new ExecutableFinder)->find('pdftotext');
        if ($pdftotext === null) {
            $this->markTestSkipped('pdftotext is required to compare rendered PDF content.');
        }

        $pdfPath = tempnam(sys_get_temp_dir(), 'biduk-admin-report-');
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
