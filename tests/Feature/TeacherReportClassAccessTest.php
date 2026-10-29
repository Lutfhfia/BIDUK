<?php

namespace Tests\Feature;

use App\Models\Achievement;
use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\Permission;
use App\Models\RekapAbsensi;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherReportClassAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_report_lists_are_limited_to_directly_assigned_classes(): void
    {
        [$teacher, $year, $teacherClass, $teacherStudent, $otherClass, $otherStudent] = $this->createReportFixture();

        Achievement::create([
            'student_id' => $teacherStudent->id,
            'class_id' => $teacherClass->id,
            'academic_year_id' => $year->id,
            'type' => 'Prestasi Milik Wali',
        ]);
        Achievement::create([
            'student_id' => $otherStudent->id,
            'class_id' => $otherClass->id,
            'academic_year_id' => $year->id,
            'type' => 'Prestasi Kelas Lain',
        ]);
        $semester = Semester::create([
            'academic_year_id' => $year->id,
            'name' => 'Ganjil',
        ]);
        RekapAbsensi::create([
            'student_id' => $teacherStudent->id,
            'class_id' => $teacherClass->id,
            'semester_id' => $semester->id,
            'sakit' => 1,
            'izin' => 0,
            'tanpa_keterangan' => 0,
        ]);
        RekapAbsensi::create([
            'student_id' => $otherStudent->id,
            'class_id' => $otherClass->id,
            'semester_id' => $semester->id,
            'sakit' => 9,
            'izin' => 0,
            'tanpa_keterangan' => 0,
        ]);

        $this->actingAs($teacher)
            ->get(route('achievement-reports.index', [
                'academic_year_id' => $year->id,
            ]))
            ->assertOk()
            ->assertSeeText('Prestasi Milik Wali')
            ->assertDontSeeText('Prestasi Kelas Lain');

        $this->get(route('achievement-reports.index', [
            'academic_year_id' => $year->id,
            'class_id' => $otherClass->id,
        ]))->assertForbidden();

        $this->get(route('buku-induk.index', [
            'academic_year_id' => $year->id,
            'print_type' => 'all',
        ]))
            ->assertOk()
            ->assertSeeText($teacherStudent->name)
            ->assertDontSeeText($otherStudent->name);

        $this->get(route('buku-induk.print', [
            'student' => $otherStudent->id,
            'academic_year_id' => $year->id,
            'class_id' => $otherClass->id,
            'print_type' => 'class',
        ]))->assertForbidden();

        $this->get(route('rekap-rapot.index', [
            'academic_year_id' => $year->id,
            'class_id' => 'all',
        ]))
            ->assertOk()
            ->assertSeeText($teacherStudent->name)
            ->assertDontSeeText($otherStudent->name);

        $this->get(route('rekap-rapot.batch', [
            'academic_year_id' => $year->id,
            'class_id' => 'all',
            'pdf' => 0,
        ]))
            ->assertOk()
            ->assertSeeText($teacherStudent->name)
            ->assertDontSeeText($otherStudent->name);

        $this->get(route('rekap-rapot.print', [
            'student' => $otherStudent->id,
            'academic_year_id' => $year->id,
            'class_id' => $otherClass->id,
        ]))->assertForbidden();

        $this->get(route('rekap-absensi.index', [
            'academic_year_id' => $year->id,
        ]))
            ->assertOk()
            ->assertSeeText($teacherStudent->name)
            ->assertDontSeeText($otherStudent->name);
    }

    public function test_teacher_cannot_access_another_homeroom_class_through_report_actions(): void
    {
        [$teacher, , , , $otherClass, $otherStudent] = $this->createReportFixture();

        $this->actingAs($teacher)
            ->get(route('rekap-absensi.students', $otherClass))
            ->assertForbidden();

        $this->get(route('final-grades.print', [
            'class' => $otherClass->id,
            'student' => $otherStudent->id,
        ]))->assertForbidden();

        $this->get(route('report-card-grades.print', [
            'class' => $otherClass->id,
            'student' => $otherStudent->id,
        ]))->assertForbidden();
    }

    public function test_teacher_report_filters_only_assigned_academic_years_and_matches_classes(): void
    {
        [$teacher, $currentYear, $currentClass] = $this->createReportFixture();
        $olderYear = AcademicYear::create([
            'name' => '2024/2025',
            'start_date' => '2024-07-01',
            'end_date' => '2025-06-30',
            'status' => 'inactive',
        ]);
        $olderClass = SchoolClass::create([
            'academic_year_id' => $olderYear->id,
            'name' => '5A',
            'grade_level' => 5,
            'status' => 'Aktif',
            'homeroom_teacher_user_id' => $teacher->id,
        ]);
        $unrelatedYear = AcademicYear::create([
            'name' => '2023/2024',
            'start_date' => '2023-07-01',
            'end_date' => '2024-06-30',
            'status' => 'inactive',
        ]);

        $this->actingAs($teacher)
            ->get(route('report-card-grades.index', [
                'academic_year_id' => $olderYear->id,
                'class_id' => $currentClass->id,
            ]))
            ->assertOk()
            ->assertSeeText($olderYear->name)
            ->assertSeeText($olderClass->name)
            ->assertDontSeeText($unrelatedYear->name);
    }

    public function test_teacher_class_management_is_visible_and_limited_to_assigned_classes(): void
    {
        $teacherRole = Role::create(['name' => 'Guru']);
        $permission = Permission::create(['name' => 'classes.edit']);
        $teacherRole->permissions()->attach($permission->id);
        $teacher = User::create([
            'name' => 'Guru Kelas',
            'username' => 'guru-kelas',
            'email' => 'guru-kelas@example.test',
            'password' => 'password',
            'role_id' => $teacherRole->id,
            'status' => 'Aktif',
        ]);
        $year = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);
        $teacherClass = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => '4A',
            'grade_level' => 4,
            'status' => 'Aktif',
            'homeroom_teacher_user_id' => $teacher->id,
        ]);
        $otherClass = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => '4B',
            'grade_level' => 4,
            'status' => 'Aktif',
        ]);
        $previousYear = AcademicYear::create([
            'name' => '2024/2025',
            'start_date' => '2024-07-01',
            'end_date' => '2025-06-30',
            'status' => 'inactive',
        ]);
        $previousClass = SchoolClass::create([
            'academic_year_id' => $previousYear->id,
            'name' => '3A',
            'grade_level' => 3,
            'status' => 'Nonaktif',
            'homeroom_teacher_user_id' => $teacher->id,
        ]);
        $historyStudent = $this->createStudent('Siswa Riwayat');
        ClassStudent::create([
            'class_id' => $previousClass->id,
            'student_id' => $historyStudent->id,
            'status' => 'Naik Kelas',
        ]);

        $this->actingAs($teacher)
            ->get(route('classes.index'))
            ->assertOk()
            ->assertSeeText('4A')
            ->assertDontSeeText('4B')
            ->assertSeeText('Data Kelas');

        $this->get(route('classes.edit', $teacherClass))->assertOk();
        $this->get(route('classes.show', $otherClass))->assertForbidden();
        $this->get(route('classes.edit', $otherClass))->assertForbidden();
        $this->get(route('classes.students', $otherClass))->assertForbidden();
        $this->get(route('classes.students.add', $otherClass))->assertForbidden();

        $this->put(route('classes.update', $otherClass), [
            'academic_year_id' => $year->id,
            'name' => '4B Diubah',
            'grade_level' => 4,
            'homeroom_teacher_user_id' => $teacher->id,
            'capacity' => 30,
            'status' => 'Aktif',
        ])->assertForbidden();

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Riwayat Kelas Wali')
            ->assertSeeText($previousYear->name)
            ->assertSeeText('3A')
            ->assertSeeText($historyStudent->name);
    }

    public function test_teacher_student_list_defaults_to_active_year_and_can_filter_assigned_class_history(): void
    {
        [$teacher, $activeYear, $activeClass, $activeStudent, $otherClass, $otherStudent] = $this->createReportFixture();

        $secondAssignedClass = SchoolClass::create([
            'academic_year_id' => $activeYear->id,
            'name' => '6C',
            'grade_level' => 6,
            'status' => 'Aktif',
            'homeroom_teacher_user_id' => $teacher->id,
        ]);
        $secondActiveStudent = $this->createStudent('Siswa Kelas Aktif Lain');
        ClassStudent::create([
            'class_id' => $secondAssignedClass->id,
            'student_id' => $secondActiveStudent->id,
            'status' => 'Aktif',
        ]);

        $olderYear = AcademicYear::create([
            'name' => '2024/2025',
            'start_date' => '2024-07-01',
            'end_date' => '2025-06-30',
            'status' => 'inactive',
        ]);
        $olderClass = SchoolClass::create([
            'academic_year_id' => $olderYear->id,
            'name' => '5A',
            'grade_level' => 5,
            'status' => 'Nonaktif',
            'homeroom_teacher_user_id' => $teacher->id,
        ]);
        $historyStudent = $this->createStudent('Siswa Kelas Riwayat');
        ClassStudent::create([
            'class_id' => $olderClass->id,
            'student_id' => $historyStudent->id,
            'status' => 'Naik Kelas',
        ]);
        $this->actingAs($teacher)
            ->get(route('students.index'))
            ->assertOk()
            ->assertSeeText($activeStudent->name)
            ->assertSeeText($secondActiveStudent->name)
            ->assertDontSeeText($historyStudent->name)
            ->assertDontSeeText($otherStudent->name)
            ->assertSeeText('2025/2026');

        $this->get(route('students.index', [
            'academic_year_id' => $activeYear->id,
            'class_id' => $activeClass->id,
        ]))
            ->assertOk()
            ->assertSeeText($activeStudent->name)
            ->assertDontSeeText($secondActiveStudent->name)
            ->assertDontSeeText($otherStudent->name);

        $this->get(route('students.index', [
            'academic_year_id' => $olderYear->id,
        ]))
            ->assertOk()
            ->assertSeeText($historyStudent->name)
            ->assertDontSeeText($activeStudent->name)
            ->assertDontSeeText($otherStudent->name);

        $this->get(route('students.show', [
            'student' => $historyStudent->id,
            'academic_year_id' => $olderYear->id,
        ]))->assertOk()
            ->assertSeeText($historyStudent->name)
            ->assertSee('data-bs-target="#keterangan"', false)
            ->assertSee('data-bs-target="#ortu"', false)
            ->assertSee('data-bs-target="#perkembangan"', false)
            ->assertSee('data-bs-target="#physique"', false)
            ->assertSee('data-bs-target="#health"', false)
            ->assertSee('data-bs-target="#lainnya"', false)
            ->assertSee('id="keterangan" role="tabpanel" aria-labelledby="keterangan-tab"', false)
            ->assertSee('id="ortu" role="tabpanel" aria-labelledby="ortu-tab"', false)
            ->assertSee('id="perkembangan" role="tabpanel" aria-labelledby="perkembangan-tab"', false)
            ->assertSee('id="physique" role="tabpanel" aria-labelledby="physique-tab"', false)
            ->assertSee('id="health" role="tabpanel" aria-labelledby="health-tab"', false)
            ->assertSee('id="lainnya" role="tabpanel" aria-labelledby="lainnya-tab"', false)
            ->assertSee('window.location.hash', false);

        $this->get(route('students.index', [
            'academic_year_id' => $olderYear->id,
            'class_id' => $activeClass->id,
        ]))->assertForbidden();
    }

    public function test_teacher_recap_rapot_shows_assigned_inactive_class_and_historical_students(): void
    {
        [$teacher, , $currentClass] = $this->createReportFixture();
        $historicalYear = AcademicYear::create([
        'name' => '2023/2024',
        'start_date' => '2023-07-01',
        'end_date' => '2024-06-30',
        'status' => 'inactive',
        ]);
        $historicalClass = SchoolClass::create([
        'academic_year_id' => $historicalYear->id,
        'name' => '5A',
        'grade_level' => 5,
        'status' => 'Nonaktif',
        'homeroom_teacher_user_id' => $teacher->id,
        ]);
        $historicalStudent = $this->createStudent('Siswa Rapor Historis');
        ClassStudent::create([
        'class_id' => $historicalClass->id,
        'student_id' => $historicalStudent->id,
        'status' => 'Naik Kelas',
        ]);
        Semester::create([
        'academic_year_id' => $historicalYear->id,
        'name' => 'Ganjil',
        ]);

        $response = $this->actingAs($teacher)
        ->get(route('rekap-rapot.index', [
            'academic_year_id' => $historicalYear->id,
            'class_id' => $historicalClass->id,
        ]))
        ->assertOk()
        ->assertSeeText($historicalStudent->name)
        ->assertSeeText($historicalClass->name)
        ->assertSeeText('Semua Status');

        $this->assertSame(
        [$historicalClass->id],
        $response->viewData('classes')->pluck('id')->all()
        );
        $this->assertSame('Semua', $response->viewData('selectedStatus'));

        $changedYearResponse = $this->get(route('rekap-rapot.index', [
            'academic_year_id' => $historicalYear->id,
            'class_id' => $currentClass->id,
        ]))
            ->assertOk()
            ->assertSeeText($historicalStudent->name)
            ->assertSeeText($historicalClass->name);

        $this->assertSame('all', $changedYearResponse->viewData('selectedClassId'));
        $this->assertSame('Semua', $changedYearResponse->viewData('selectedStatus'));
        $this->assertSame(
            [$historicalClass->id],
            $changedYearResponse->viewData('classes')->pluck('id')->all()
        );
    }

    private function createReportFixture(): array
    {
        $teacherRole = Role::create(['name' => 'Guru']);
        foreach ([
            'achievements.view',
            'student_records.view',
            'report_cards.view',
            'grades.view',
            'grades.edit',
            'students.view',
        ] as $permissionName) {
            $permission = Permission::create(['name' => $permissionName]);
            $teacherRole->permissions()->attach($permission->id);
        }

        $teacher = User::create([
            'name' => 'Guru Wali',
            'username' => 'guru-wali',
            'email' => 'guru-wali@example.test',
            'password' => 'password',
            'role_id' => $teacherRole->id,
            'status' => 'Aktif',
        ]);
        $year = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);
        $teacherClass = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => '6A',
            'grade_level' => 6,
            'status' => 'Aktif',
            'homeroom_teacher_user_id' => $teacher->id,
        ]);
        $otherClass = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => '6B',
            'grade_level' => 6,
            'status' => 'Aktif',
        ]);
        $teacherStudent = $this->createStudent('Siswa Wali');
        $otherStudent = $this->createStudent('Siswa Kelas Lain');

        foreach ([[$teacherClass, $teacherStudent], [$otherClass, $otherStudent]] as [$class, $student]) {
            ClassStudent::create([
                'class_id' => $class->id,
                'student_id' => $student->id,
                'status' => 'Aktif',
            ]);
        }

        return [$teacher, $year, $teacherClass, $teacherStudent, $otherClass, $otherStudent];
    }

    private function createStudent(string $name): Student
    {
        return Student::create([
            'nis' => str_replace(' ', '-', strtolower($name)),
            'nisn' => 'nisn-'.str_replace(' ', '-', strtolower($name)),
            'name' => $name,
            'gender' => 'L',
            'status' => 'Aktif',
        ]);
    }
}
