<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolClassTeacherAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_view_only_headmaster_cannot_remove_a_student_from_a_class(): void
    {
        $role = Role::create(['name' => 'Kepala Sekolah']);
        $role->permissions()->attach(Permission::create([
            'name' => 'classes.view',
            'description' => 'Lihat Data Kelas',
        ])->id);
        $headmaster = User::create([
            'name' => 'Kepala Sekolah Lihat Kelas',
            'username' => 'kepala-view-kelas',
            'email' => 'kepala-view-kelas@example.test',
            'password' => 'password',
            'role_id' => $role->id,
            'status' => 'Aktif',
        ]);
        $year = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);
        $class = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => '1A',
            'grade_level' => 1,
            'capacity' => 30,
            'status' => 'Aktif',
        ]);
        $student = Student::create([
            'nis' => 'VIEW-ONLY-CLASS',
            'nisn' => 'VIEW-ONLY-CLASS-NISN',
            'name' => 'Siswa Tetap di Kelas',
            'gender' => 'L',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'Aktif',
        ]);

        $this->actingAs($headmaster)
            ->get(route('classes.show', $class))
            ->assertOk()
            ->assertSeeText($student->name)
            ->assertDontSee('bi-person-dash', false)
            ->assertDontSeeText('Tambah Siswa');

        $this->delete(route('classes.students.remove', [$class, $student]))
            ->assertForbidden();

        $this->assertDatabaseHas('class_student', [
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'Aktif',
        ]);
    }

    public function test_active_teacher_user_without_employee_profile_can_be_assigned_as_homeroom_teacher(): void
    {
        $academicYear = AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'status' => 'active',
        ]);
        $teacherRole = Role::create(['name' => 'Guru']);
        $teacher = User::create([
            'name' => 'Tata Guru',
            'username' => 'tata-guru',
            'email' => 'tata-guru@example.test',
            'password' => 'password',
            'role_id' => $teacherRole->id,
            'status' => 'Aktif',
        ]);
        $admin = User::create([
            'name' => 'Admin Kelas',
            'username' => 'admin-kelas',
            'email' => 'admin-kelas@example.test',
            'password' => 'password',
            'role_id' => Role::create(['name' => 'Super Admin'])->id,
            'status' => 'Aktif',
        ]);

        $this->actingAs($admin)
            ->get(route('classes.create'))
            ->assertOk()
            ->assertSeeText($teacher->name);

        $this->post(route('classes.store'), [
            'academic_year_id' => $academicYear->id,
            'name' => '1A',
            'grade_level' => 1,
            'homeroom_teacher_user_id' => (string) $teacher->id,
            'capacity' => 30,
            'status' => 'Aktif',
            'subject_ids' => [],
        ])->assertRedirect(route('classes.index'));

        $this->assertDatabaseHas('classes', [
            'academic_year_id' => $academicYear->id,
            'name' => '1A',
            'homeroom_teacher_user_id' => $teacher->id,
            'homeroom_teacher_id' => null,
        ]);

        $class = SchoolClass::query()->where('name', '1A')->firstOrFail();
        $this->get(route('classes.edit', $class))
            ->assertOk()
            ->assertSee($teacher->name);
    }
}
