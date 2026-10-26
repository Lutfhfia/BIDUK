<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherStudentCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_teacher_can_create_student_in_main_student_data_and_assign_to_own_class(): void
    {
        $role = Role::create(['name' => 'Guru']);
        $permissions = collect(['students.create', 'students.view'])
            ->map(fn (string $name) => Permission::create(['name' => $name]));
        $role->permissions()->attach($permissions->pluck('id'));

        $employee = Employee::create([
            'name' => 'Guru Penguji',
            'gender' => 'P',
            'position' => 'Guru',
            'employee_type' => 'Guru',
            'employment_status' => 'PNS',
            'status' => 'Aktif',
        ]);
        $teacher = User::create([
            'name' => $employee->name,
            'username' => 'guru-penguji',
            'email' => 'guru-penguji@example.test',
            'password' => 'password',
            'role_id' => $role->id,
            'employee_id' => $employee->id,
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
            'name' => '3A',
            'grade_level' => 3,
            'homeroom_teacher_id' => $employee->id,
            'homeroom_teacher_user_id' => $teacher->id,
            'status' => 'Aktif',
        ]);
        $otherClass = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => '3B',
            'grade_level' => 3,
            'status' => 'Aktif',
        ]);

        $response = $this->actingAs($teacher)->post(route('students.store'), [
            'nis' => 'NIS-GURU-ADD-01',
            'nisn' => 'NISN-GURU-ADD-01',
            'name' => 'Siswa Baru Guru',
            'gender' => 'L',
            'status' => 'Aktif',
            'class_id' => $otherClass->id,
        ]);

        $response->assertRedirect(route('students.index'));

        $student = Student::query()
            ->where('nis', 'NIS-GURU-ADD-01')
            ->first();

        $this->assertNotNull($student, 'Siswa baru harus tersimpan di tabel utama students.');
        $this->assertDatabaseHas('students', [
            'id' => $student->id,
            'name' => 'Siswa Baru Guru',
            'status' => 'Aktif',
        ]);
        $this->assertDatabaseHas('class_student', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'status' => 'Aktif',
        ]);
        $this->assertDatabaseMissing('class_student', [
            'student_id' => $student->id,
            'class_id' => $otherClass->id,
        ]);

        $this->get(route('students.index'))
            ->assertOk()
            ->assertSeeText('Siswa Baru Guru');
    }

    public function test_teacher_student_list_uses_the_classes_and_students_of_the_selected_year(): void
    {
        $role = Role::create(['name' => 'Guru']);
        $permissions = collect(['students.create', 'students.view'])
            ->map(fn (string $name) => Permission::create(['name' => $name]));
        $role->permissions()->attach($permissions->pluck('id'));

        $employee = Employee::create([
            'name' => 'Guru Riwayat',
            'gender' => 'P',
            'position' => 'Guru',
            'employee_type' => 'Guru',
            'employment_status' => 'PNS',
            'status' => 'Aktif',
        ]);
        $teacher = User::create([
            'name' => $employee->name,
            'username' => 'guru-riwayat',
            'email' => 'guru-riwayat@example.test',
            'password' => 'password',
            'role_id' => $role->id,
            'employee_id' => $employee->id,
            'status' => 'Aktif',
        ]);
        $olderYear = AcademicYear::create([
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
        $olderClass = SchoolClass::create([
            'academic_year_id' => $olderYear->id,
            'name' => '6A',
            'grade_level' => 6,
            'homeroom_teacher_id' => $employee->id,
            'homeroom_teacher_user_id' => $teacher->id,
            'status' => 'Aktif',
        ]);
        $activeClass = SchoolClass::create([
            'academic_year_id' => $activeYear->id,
            'name' => '3A',
            'grade_level' => 3,
            'homeroom_teacher_id' => $employee->id,
            'homeroom_teacher_user_id' => $teacher->id,
            'status' => 'Aktif',
        ]);
        $olderStudent = Student::create([
            'nis' => 'NIS-HISTORICAL-01',
            'nisn' => 'NISN-HISTORICAL-01',
            'name' => 'Siswa Tahun Lama',
            'gender' => 'L',
            'status' => 'Lulus',
        ]);
        $activeStudent = Student::create([
            'nis' => 'NIS-ACTIVE-01',
            'nisn' => 'NISN-ACTIVE-01',
            'name' => 'Siswa Tahun Aktif',
            'gender' => 'P',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $olderClass->id,
            'student_id' => $olderStudent->id,
            'status' => 'Naik Kelas',
        ]);
        ClassStudent::create([
            'class_id' => $activeClass->id,
            'student_id' => $activeStudent->id,
            'status' => 'Aktif',
        ]);

        $this->actingAs($teacher)
            ->get(route('students.index', ['academic_year_id' => $olderYear->id]))
            ->assertOk()
            ->assertSeeText('6A')
            ->assertSeeText('Siswa Tahun Lama')
            ->assertDontSeeText('3A')
            ->assertDontSeeText('Siswa Tahun Aktif');

        $this->get(route('students.index', ['academic_year_id' => $activeYear->id]))
            ->assertOk()
            ->assertSeeText('3A')
            ->assertSeeText('Siswa Tahun Aktif')
            ->assertDontSeeText('6A')
            ->assertDontSeeText('Siswa Tahun Lama');
    }
}
