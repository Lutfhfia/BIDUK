<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeeHomeroomHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_employee_details_show_homeroom_class_history_for_legacy_and_user_assignments(): void
    {
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $teacherRole = Role::create(['name' => 'Guru']);
        $employee = Employee::create([
            'nip' => 'NIP-HISTORY-01',
            'name' => 'Guru Riwayat',
            'gender' => 'P',
            'position' => 'Guru',
            'employee_type' => 'Guru',
            'employment_status' => 'PNS',
            'status' => 'Aktif',
        ]);
        $teacher = User::create([
            'name' => $employee->name,
            'username' => $employee->nip,
            'email' => 'guru-riwayat@example.test',
            'password' => 'password',
            'role_id' => $teacherRole->id,
            'employee_id' => $employee->id,
            'status' => 'Aktif',
        ]);
        $admin = User::create([
            'name' => 'Super Admin',
            'username' => 'super-admin-history',
            'email' => 'super-admin-history@example.test',
            'password' => 'password',
            'role_id' => $superAdminRole->id,
            'status' => 'Aktif',
        ]);

        $olderYear = AcademicYear::create([
            'name' => '2023/2024',
            'start_date' => '2023-07-01',
            'end_date' => '2024-06-30',
            'status' => 'inactive',
        ]);
        $currentYear = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);
        SchoolClass::create([
            'academic_year_id' => $olderYear->id,
            'name' => '3A',
            'grade_level' => 3,
            'homeroom_teacher_id' => $employee->id,
            'status' => 'Nonaktif',
        ]);
        SchoolClass::create([
            'academic_year_id' => $currentYear->id,
            'name' => '5B',
            'grade_level' => 5,
            'homeroom_teacher_user_id' => $teacher->id,
            'status' => 'Aktif',
        ]);

        $this->actingAs($admin)
            ->get(route('pegawai.show', $employee))
            ->assertOk()
            ->assertSeeText('Riwayat Wali Kelas')
            ->assertSeeText('2023/2024')
            ->assertSeeText('3A')
            ->assertSeeText('2025/2026')
            ->assertSeeText('5B');
    }
}
