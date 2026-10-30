<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployeePaginationActionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_crud_actions_are_visible_for_employees_on_each_page(): void
    {
        $adminRole = Role::create(['name' => 'Super Admin']);
        $admin = User::create([
            'name' => 'Super Admin',
            'username' => 'super-admin',
            'email' => 'super-admin@example.test',
            'password' => 'password',
            'role_id' => $adminRole->id,
            'status' => 'Aktif',
        ]);

        foreach (range(1, 12) as $number) {
            Employee::create([
                'name' => "Pegawai {$number}",
                'gender' => 'L',
                'position' => 'Guru',
                'employee_type' => 'Guru',
                'employment_status' => 'PNS',
                'status' => 'Aktif',
            ]);
        }

        $firstPage = $this->actingAs($admin)
            ->get(route('pegawai.index'));

        $firstPage->assertOk();
        $this->assertSame(10, substr_count($firstPage->getContent(), 'aria-label="Aksi pegawai '));
        $this->assertSame(10, substr_count($firstPage->getContent(), 'Detail Pegawai'));
        $this->assertSame(10, substr_count($firstPage->getContent(), 'Edit Pegawai'));
        $this->assertSame(10, substr_count($firstPage->getContent(), 'Hapus Pegawai'));
        $this->assertSame(
            10,
            preg_match_all(
                '/<button\b(?=[^>]*aria-label="Aksi pegawai [^"]+")(?=[^>]*data-bs-toggle="dropdown")[^>]*>/s',
                $firstPage->getContent()
            )
        );

        $secondPage = $this->get(route('pegawai.index', ['page' => 2]));

        $secondPage->assertOk();
        $this->assertSame(2, substr_count($secondPage->getContent(), 'aria-label="Aksi pegawai '));
        $this->assertSame(2, substr_count($secondPage->getContent(), 'Detail Pegawai'));
        $this->assertSame(2, substr_count($secondPage->getContent(), 'Edit Pegawai'));
        $this->assertSame(2, substr_count($secondPage->getContent(), 'Hapus Pegawai'));
        $this->assertSame(
            2,
            preg_match_all(
                '/<button\b(?=[^>]*aria-label="Aksi pegawai [^"]+")(?=[^>]*data-bs-toggle="dropdown")[^>]*>/s',
                $secondPage->getContent()
            )
        );
    }
}
