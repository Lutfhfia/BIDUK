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

class StudentAdminYearFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_roles_can_filter_students_by_academic_year_and_class(): void
    {
        $permission = Permission::create(['name' => 'students.view']);
        $oldYear = AcademicYear::create([
            'name' => '2024/2025',
            'start_date' => '2024-07-01',
            'end_date' => '2025-06-30',
            'status' => 'inactive',
        ]);
        $activeYear = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);
        $oldClass = $this->createClass($oldYear, '6A', 6);
        $activeClass = $this->createClass($activeYear, '3A', 3);
        $oldStudent = $this->createStudent('NIS-OLD-01', 'Siswa Periode Lama', 'Lulus');
        $activeStudent = $this->createStudent('NIS-ACTIVE-01', 'Siswa Periode Aktif', 'Aktif');
        ClassStudent::create([
            'class_id' => $oldClass->id,
            'student_id' => $oldStudent->id,
            'status' => 'Naik Kelas',
        ]);
        ClassStudent::create([
            'class_id' => $activeClass->id,
            'student_id' => $activeStudent->id,
            'status' => 'Aktif',
        ]);

        foreach (['Admin', 'Super Admin', 'Kepala Sekolah'] as $roleName) {
            $role = Role::create(['name' => $roleName]);
            $role->permissions()->attach($permission->id);
            $user = User::create([
                'name' => $roleName . ' Filter',
                'username' => strtolower(str_replace(' ', '-', $roleName)) . '-student-filter',
                'email' => strtolower(str_replace(' ', '-', $roleName)) . '-student-filter@example.test',
                'password' => 'password',
                'role_id' => $role->id,
                'status' => 'Aktif',
            ]);

            $this->actingAs($user)
                ->get(route('students.index', ['academic_year_id' => $oldYear->id]))
                ->assertOk()
                ->assertViewHas('stats.total', 2)
                ->assertSeeText('2024/2025')
                ->assertSeeText('6A')
                ->assertSeeText('Siswa Periode Lama')
                ->assertDontSeeText('3A')
                ->assertDontSeeText('Siswa Periode Aktif');

            $this->get(route('students.index', [
                'academic_year_id' => $activeYear->id,
                'class_id' => $activeClass->id,
            ]))
                ->assertOk()
                ->assertViewHas('stats.total', 2)
                ->assertSeeText('2025/2026')
                ->assertSeeText('3A')
                ->assertSeeText('Siswa Periode Aktif')
                ->assertDontSeeText('6A')
                ->assertDontSeeText('Siswa Periode Lama');
        }
    }

    private function createClass(AcademicYear $year, string $name, int $gradeLevel): SchoolClass
    {
        return SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => $name,
            'grade_level' => $gradeLevel,
            'status' => 'Aktif',
        ]);
    }

    private function createStudent(string $nis, string $name, string $status): Student
    {
        return Student::create([
            'nis' => $nis,
            'nisn' => 'NISN-' . $nis,
            'name' => $name,
            'gender' => 'L',
            'status' => $status,
        ]);
    }
}
