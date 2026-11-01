<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\ReportCardGrade;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\ReportCardGradeImportContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class ReportCardGradeTeacherAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_view_and_edit_previous_year_homeroom_class_only(): void
    {
        [$year, $teacherClass, $teacherStudent, $teacherSubject, $semester, $teacher] = $this->createTeacherClass();
        [, $otherClass, $otherStudent] = $this->createTeacherClass('1B', 'other');

        ReportCardGrade::create([
            'student_id' => $teacherStudent->id,
            'class_id' => $teacherClass->id,
            'subject_id' => $teacherSubject->id,
            'semester_id' => $semester->id,
            'score' => 70,
            'skill_score' => 89,
            'skill_predicate' => 'A',
            'skill_learning_outcome' => 'Capaian keterampilan lama.',
        ]);

        $this->actingAs($teacher)
            ->get(route('report-card-grades.index', [
                'academic_year_id' => $year->id,
                'class_id' => $teacherClass->id,
            ]))
            ->assertOk()
            ->assertSeeText($teacherStudent->name)
            ->assertSeeText($year->name)
            ->assertDontSeeText($otherStudent->name);

        $this->get(route('report-card-grades.edit', [$teacherClass, $teacherStudent]))
            ->assertOk()
            ->assertSee('value="70.00"', false)
            ->assertSee('Nilai Akhir')
            ->assertDontSee('value="89.00"', false)
            ->assertDontSee('Keterampilan');

        $this->put(route('report-card-grades.update', [$teacherClass, $teacherStudent]), [
            'ganjil_scores' => [$teacherSubject->id => 88],
        ])->assertRedirect();

        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $teacherStudent->id,
            'class_id' => $teacherClass->id,
            'subject_id' => $teacherSubject->id,
            'semester_id' => $semester->id,
            'score' => 88,
            'skill_score' => null,
        ]);

        $this->get(route('report-card-grades.edit', [$otherClass, $otherStudent]))
            ->assertForbidden();
        $this->put(route('report-card-grades.update', [$otherClass, $otherStudent]), [
            'ganjil_scores' => [$teacherSubject->id => 99],
        ])->assertForbidden();
        $this->get(route('report-card-grades.index', [
            'academic_year_id' => $year->id,
            'class_id' => $otherClass->id,
        ]))->assertForbidden();
    }

    public function test_report_card_extracurriculars_are_saved_shown_in_edit_and_printed(): void
    {
        [$year, $class, $student, , , $teacher] = $this->createTeacherClass(
            '5A',
            'extracurricular-report'
        );

        $this->actingAs($teacher)
            ->put(route('report-card-grades.update', [$class, $student]), [
                'extracurricular' => [
                    1 => [
                        'activity' => 'Pramuka',
                        'description' => 'Aktif mengikuti latihan rutin.',
                        'description_2' => 'Mengikuti kegiatan setiap Jumat.',
                    ],
                    2 => [
                        'activity' => 'Tari',
                        'description' => 'Tampil pada acara sekolah.',
                    ],
                    3 => [
                        'activity' => '',
                        'description' => '',
                    ],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('student_extracurriculars', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'academic_year_id' => $year->id,
            'position' => 1,
            'activity' => 'Pramuka',
            'description' => 'Aktif mengikuti latihan rutin.',
            'description_2' => 'Mengikuti kegiatan setiap Jumat.',
        ]);

        $this->get(route('report-card-grades.edit', [$class, $student]))
            ->assertOk()
            ->assertSee('value="Pramuka"', false)
            ->assertSee('value="Aktif mengikuti latihan rutin."', false)
            ->assertSee('value="Mengikuti kegiatan setiap Jumat."', false)
            ->assertSee('value="Tari"', false);

        $this->get(route('report-card-grades.print', [
            'class' => $class->id,
            'student' => $student->id,
            'academic_year_id' => $year->id,
        ]))
            ->assertOk()
            ->assertSeeText('Pramuka')
            ->assertSeeText('Aktif mengikuti latihan rutin.')
            ->assertSeeText('Mengikuti kegiatan setiap Jumat.')
            ->assertSeeText('Tari')
            ->assertSeeText('Tampil pada acara sekolah.');
    }

    public function test_teacher_can_manage_existing_grades_when_current_class_assignment_status_is_not_active(): void
    {
        [$year, $class, $student, $subject, $semester, $teacher] = $this->createTeacherClass(
            '5A',
            'grade-status-incomplete'
        );
        $year->update(['status' => 'active']);
        $class->update(['status' => 'Aktif']);
        ClassStudent::where('class_id', $class->id)
            ->where('student_id', $student->id)
            ->update(['status' => 'Pindah']);
        ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $semester->id,
            'score' => 82,
        ]);

        $this->actingAs($teacher)
            ->get(route('report-card-grades.index', [
                'academic_year_id' => $year->id,
                'class_id' => $class->id,
            ]))
            ->assertOk()
            ->assertSeeText($student->name);

        $this->get(route('report-card-grades.edit', [$class, $student]))
            ->assertOk()
            ->assertSee('value="82.00"', false);
    }

    public function test_teacher_can_save_grades_to_both_semesters_of_an_inactive_year_without_existing_semester_rows(): void
    {
        [$year, $class, $student, $subject, , $teacher] = $this->createTeacherClass(
            '5A',
            'missing-year-semesters'
        );
        Semester::where('academic_year_id', $year->id)->delete();

        $this->actingAs($teacher)
            ->get(route('report-card-grades.edit', [$class, $student]))
            ->assertOk();

        $this->put(route('report-card-grades.update', [$class, $student]), [
            'ganjil_scores' => [$subject->id => 81],
            'genap_scores' => [$subject->id => 84],
        ])->assertRedirect();

        $ganjil = Semester::where('academic_year_id', $year->id)
            ->where('name', 'Ganjil')
            ->firstOrFail();
        $genap = Semester::where('academic_year_id', $year->id)
            ->where('name', 'Genap')
            ->firstOrFail();

        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $ganjil->id,
            'score' => 81,
        ]);
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $genap->id,
            'score' => 84,
        ]);

        $this->get(route('report-card-grades.index', [
            'academic_year_id' => $year->id,
            'class_id' => $class->id,
        ]))
            ->assertOk()
            ->assertSeeText('Sudah Diisi')
            ->assertDontSeeText('Tidak tersedia');
    }

    public function test_teacher_defaults_to_the_latest_assigned_academic_year(): void
    {
        [$oldYear, $oldClass, $oldStudent, $subject, , $teacher] = $this->createTeacherClass();
        $latestYear = AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'status' => 'active',
        ]);
        $latestClass = SchoolClass::create([
            'academic_year_id' => $latestYear->id,
            'name' => '2A',
            'grade_level' => 2,
            'homeroom_teacher_user_id' => $teacher->id,
            'status' => 'Aktif',
        ]);
        $newerInactiveYear = AcademicYear::create([
            'name' => '2027/2028',
            'start_date' => '2027-07-01',
            'end_date' => '2028-06-30',
            'status' => 'inactive',
        ]);
        SchoolClass::create([
            'academic_year_id' => $newerInactiveYear->id,
            'name' => '3A',
            'grade_level' => 3,
            'homeroom_teacher_user_id' => $teacher->id,
            'status' => 'Aktif',
        ]);
        $latestStudent = Student::create([
            'nis' => 'NIS-LATEST',
            'nisn' => 'NISN-LATEST',
            'name' => 'Siswa Tahun Terbaru',
            'gender' => 'L',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $latestClass->id,
            'student_id' => $latestStudent->id,
            'status' => 'Aktif',
        ]);
        $latestClass->subjects()->attach($subject->id);
        Semester::create([
            'academic_year_id' => $latestYear->id,
            'name' => 'Ganjil',
        ]);

        $response = $this->actingAs($teacher)
            ->get(route('report-card-grades.index'))
            ->assertOk()
            ->assertSeeText($latestStudent->name)
            ->assertSeeText('(Aktif)')
            ->assertSeeText('(Tidak Aktif)')
            ->assertDontSeeText($oldStudent->name);

        $this->assertSame($latestYear->id, $response->viewData('selectedAcademicYearId'));
        $this->assertNotSame($oldYear->id, $response->viewData('selectedAcademicYearId'));
        $this->assertNotSame($newerInactiveYear->id, $response->viewData('selectedAcademicYearId'));
    }

    public function test_teacher_can_view_but_cannot_manage_student_class_history_even_with_student_edit_permission(): void
    {
        [$year, $class, $student, , , $teacher] = $this->createTeacherClass();
        $editStudentsPermission = Permission::firstOrCreate(['name' => 'students.edit']);
        $teacher->role->permissions()->syncWithoutDetaching([$editStudentsPermission->id]);

        $this->actingAs($teacher)
            ->get(route('report-card-grades.history', $student))
            ->assertOk()
            ->assertSeeText($student->name)
            ->assertSeeText('Riwayat kelas dikelola oleh Admin sekolah.')
            ->assertDontSeeText('Lengkapi Riwayat')
            ->assertDontSeeText('Simpan Riwayat')
            ->assertDontSee(route('report-card-grades.history.store', $student), false);

        $this->post(route('report-card-grades.history.store', $student), [
            'grade_level' => 1,
            'academic_year_id' => $year->id,
            'class_id' => $class->id,
            'final_status' => 'Naik Kelas',
        ])->assertForbidden();

        $this->assertDatabaseMissing('student_class_histories', [
            'student_id' => $student->id,
            'grade_level' => 1,
        ]);
    }

    public function test_teacher_can_edit_buku_induk_for_a_student_from_a_previous_class_they_taught(): void
    {
        [, $class, $student, , , $teacher] = $this->createTeacherClass(
            '5A',
            'historical-student-edit'
        );
        $editPermission = Permission::firstOrCreate(['name' => 'students.edit']);
        $teacher->role->permissions()->syncWithoutDetaching([$editPermission->id]);

        $this->actingAs($teacher)
            ->get(route('students.edit', $student))
            ->assertOk()
            ->assertSeeText('Penempatan kelas hanya dapat diubah oleh Admin sekolah.');

        $this->put(route('students.update', $student), [
            'name' => 'Siswa Diperbarui',
            'gender' => 'L',
            'status' => 'Aktif',
            'class_id' => $class->id,
        ])->assertRedirect(route('students.index'));

        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Siswa Diperbarui',
        ]);
        $this->assertDatabaseHas('class_student', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'status' => 'Lulus',
        ]);
    }

    public function test_teacher_cannot_edit_buku_induk_for_a_student_outside_all_assigned_classes(): void
    {
        [, , , , , $teacher] = $this->createTeacherClass(
            '5A',
            'historical-outsider'
        );
        $editPermission = Permission::firstOrCreate(['name' => 'students.edit']);
        $teacher->role->permissions()->syncWithoutDetaching([$editPermission->id]);

        $otherYear = AcademicYear::create([
            'name' => '2023/2024',
            'start_date' => '2023-07-01',
            'end_date' => '2024-06-30',
            'status' => 'inactive',
        ]);
        $otherTeacher = User::create([
            'name' => 'Guru Lain',
            'username' => 'guru-outsider',
            'email' => 'guru-outsider@example.test',
            'password' => 'password',
            'role_id' => $teacher->role_id,
        ]);
        $otherClass = SchoolClass::create([
            'academic_year_id' => $otherYear->id,
            'name' => '5B',
            'grade_level' => 5,
            'status' => 'Nonaktif',
            'homeroom_teacher_user_id' => $otherTeacher->id,
        ]);
        $student = Student::create([
            'nis' => 'NIS-OUTSIDER',
            'nisn' => 'NISN-OUTSIDER',
            'name' => 'Siswa Kelas Lain',
            'gender' => 'L',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $otherClass->id,
            'student_id' => $student->id,
            'status' => 'Lulus',
        ]);

        $this->actingAs($teacher)
            ->get(route('students.edit', $student))
            ->assertForbidden();
    }

    public function test_teacher_year_filter_shows_all_owned_classes_for_that_year_without_selecting_the_first_one(): void
    {
        [$year, $firstClass, $firstStudent, $subject, , $teacher] = $this->createTeacherClass();
        $secondClass = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => '1C',
            'grade_level' => 1,
            'homeroom_teacher_id' => $teacher->employee_id,
            'homeroom_teacher_user_id' => $teacher->id,
            'status' => 'Nonaktif',
        ]);
        $secondStudent = Student::create([
            'nis' => 'NIS-SECOND-CLASS',
            'nisn' => 'NISN-SECOND-CLASS',
            'name' => 'Siswa Kelas Kedua',
            'gender' => 'P',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $secondClass->id,
            'student_id' => $secondStudent->id,
            'status' => 'Lulus',
        ]);
        $secondClass->subjects()->attach($subject->id);

        $response = $this->actingAs($teacher)
            ->get(route('report-card-grades.index', [
                'academic_year_id' => $year->id,
            ]))
            ->assertOk()
            ->assertSeeText($firstStudent->name)
            ->assertSeeText($secondStudent->name)
            ->assertSee('value="'.$firstClass->id.'"', false)
            ->assertSee('value="'.$secondClass->id.'"', false);

        $this->assertNull($response->viewData('selectedClassId'));
        $this->assertSame(
            [$firstClass->id, $secondClass->id],
            $response->viewData('classes')->pluck('id')->all()
        );

        $otherYear = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);
        $otherYearClass = SchoolClass::create([
            'academic_year_id' => $otherYear->id,
            'name' => '2A',
            'grade_level' => 2,
            'homeroom_teacher_id' => $teacher->employee_id,
            'homeroom_teacher_user_id' => $teacher->id,
            'status' => 'Aktif',
        ]);
        $changedYearResponse = $this->get(route('report-card-grades.index', [
            'academic_year_id' => $otherYear->id,
            'class_id' => $firstClass->id,
        ]))
            ->assertOk()
            ->assertSee('value="'.$otherYearClass->id.'"', false);

        $this->assertSame((string) $otherYear->id, $changedYearResponse->viewData('selectedAcademicYearId'));
        $this->assertNull($changedYearResponse->viewData('selectedClassId'));
        $this->assertSame(
            [$otherYearClass->id],
            $changedYearResponse->viewData('classes')->pluck('id')->all()
        );
    }

    public function test_teacher_without_employee_link_can_access_directly_assigned_homeroom_class(): void
    {
        [, $class, $student, , , $assignedTeacher] = $this->createTeacherClass();
        $unlinkedTeacher = User::create([
            'name' => 'Guru Tanpa Pegawai',
            'username' => 'guru-tanpa-pegawai',
            'email' => 'guru-tanpa-pegawai@example.test',
            'password' => 'password',
            'role_id' => $assignedTeacher->role_id,
        ]);
        $class->update(['homeroom_teacher_user_id' => $unlinkedTeacher->id]);
        $this->assertTrue($unlinkedTeacher->hasPermission('grades.view'));

        $this->actingAs($unlinkedTeacher)
            ->get(route('report-card-grades.index'))
            ->assertOk()
            ->assertSeeText($student->name);

        $importOptions = app(ReportCardGradeImportContext::class)->options($unlinkedTeacher);
        $this->assertSame([$class->id], $importOptions['classes']->pluck('id')->all());

        $this->get(route('report-card-grades.edit', [$class, $student]))
            ->assertOk();
    }

    public function test_teacher_import_selection_includes_only_assigned_previous_year_classes(): void
    {
        [$year, $teacherClass, , , $semester, $teacher] = $this->createTeacherClass();
        [, $otherClass] = $this->createTeacherClass('1B', 'other');
        $this->actingAs($teacher);

        $context = app(ReportCardGradeImportContext::class);
        $options = $context->options($teacher);

        $this->assertSame([$year->id], $options['academic_years']->pluck('id')->all());
        $this->assertSame($year->id, $options['default_academic_year_id']);
        $this->assertSame([$teacherClass->id], $options['classes']->pluck('id')->all());
        $this->assertEqualsCanonicalizing(
            ['Ganjil', 'Genap'],
            $options['semesters']->pluck('name')->all()
        );
        $this->assertSame($teacherClass->id, $context->validateSelection($teacher, [
            'academic_year_id' => $year->id,
            'class_id' => $teacherClass->id,
            'semester_id' => $semester->id,
        ])['class']->id);
        $this->get(route('report-card-grades.import.template', [
            'academic_year_id' => $year->id,
            'class_id' => $teacherClass->id,
            'semester_id' => $semester->id,
        ]))
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

        try {
            $context->validateSelection($teacher, [
                'academic_year_id' => $year->id,
                'class_id' => $otherClass->id,
                'semester_id' => $semester->id,
            ]);
            $this->fail('A teacher must not select a class assigned to another homeroom teacher.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('class_id', $exception->errors());
        }
    }

    private function createTeacherClass(
        string $className = '1A',
        string $suffix = 'owner'
    ): array {
        $year = AcademicYear::firstOrCreate(
            ['name' => '2024/2025'],
            [
                'start_date' => '2024-07-01',
                'end_date' => '2025-06-30',
                'status' => 'inactive',
            ]
        );
        $role = Role::firstOrCreate(['name' => 'Guru']);
        $permissions = collect(['grades.view', 'grades.edit'])->map(
            fn (string $name) => Permission::firstOrCreate(['name' => $name])
        );
        $role->permissions()->syncWithoutDetaching($permissions->pluck('id'));

        $employee = Employee::create([
            'name' => 'Guru '.$suffix,
            'gender' => 'P',
            'employee_type' => 'Guru',
            'status' => 'Aktif',
        ]);
        $teacher = User::create([
            'name' => $employee->name,
            'username' => 'guru-'.$suffix,
            'email' => 'guru-'.$suffix.'@example.test',
            'password' => 'password',
            'employee_id' => $employee->id,
            'role_id' => $role->id,
        ]);
        $class = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => $className,
            'grade_level' => 1,
            'homeroom_teacher_id' => $employee->id,
            'homeroom_teacher_user_id' => $teacher->id,
            'status' => 'Nonaktif',
        ]);
        $student = Student::create([
            'nis' => 'NIS-'.$suffix,
            'nisn' => 'NISN-'.$suffix,
            'name' => 'Siswa '.$suffix,
            'gender' => 'L',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'Lulus',
        ]);
        $subject = Subject::create([
            'code' => 'MAT-'.$suffix,
            'name' => 'Matematika',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
        $class->subjects()->attach($subject->id);
        $semester = Semester::firstOrCreate([
            'academic_year_id' => $year->id,
            'name' => 'Ganjil',
        ]);

        return [$year, $class, $student, $subject, $semester, $teacher];
    }
}
