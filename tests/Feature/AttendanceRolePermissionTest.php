<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Permission;
use App\Models\RekapAbsensi;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceRolePermissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_headmaster_with_view_only_permission_cannot_manage_attendance(): void
    {
        $role = Role::create(['name' => 'Kepala Sekolah']);
        $role->permissions()->attach(Permission::create([
            'name' => 'attendance.view',
            'description' => 'Lihat Rekap Absensi',
        ])->id);
        $headmaster = User::create([
            'name' => 'Kepala Sekolah Uji',
            'username' => 'kepala-sekolah-view-only',
            'email' => 'kepala-sekolah-view-only@example.test',
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
            'status' => 'Aktif',
        ]);
        $semester = Semester::create([
            'academic_year_id' => $year->id,
            'name' => 'Ganjil',
        ]);
        $student = Student::create([
            'nis' => 'ATTENDANCE-VIEW-ONLY',
            'nisn' => 'ATTENDANCE-VIEW-ONLY-NISN',
            'name' => 'Siswa Uji',
            'gender' => 'L',
            'status' => 'Aktif',
        ]);
        $class->students()->attach($student->id, ['status' => 'Aktif']);
        $record = RekapAbsensi::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'semester_id' => $semester->id,
            'sakit' => 1,
            'izin' => 0,
            'tanpa_keterangan' => 0,
        ]);

        $this->actingAs($headmaster)
            ->get(route('rekap-absensi.index'))
            ->assertOk()
            ->assertSeeText('Siswa Uji')
            ->assertDontSeeText('Tambah Rekap Absensi')
            ->assertDontSeeText('Unduh PDF');

        $this->get(route('rekap-absensi.show', $record))->assertOk();
        $this->get(route('rekap-absensi.students', $class))->assertOk();
        $this->get(route('rekap-absensi.create'))->assertForbidden();
        $this->post(route('rekap-absensi.store'), [])->assertForbidden();
        $this->get(route('rekap-absensi.edit', $record))->assertForbidden();
        $this->put(route('rekap-absensi.update', $record), [])->assertForbidden();
        $this->delete(route('rekap-absensi.destroy', $record))->assertForbidden();
        $this->get(route('rekap-absensi.pdf'))->assertForbidden();
    }
}
