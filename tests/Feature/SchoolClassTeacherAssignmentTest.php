<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolClassTeacherAssignmentTest extends TestCase
{
    use RefreshDatabase;

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
