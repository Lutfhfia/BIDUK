<?php

namespace Tests\Feature;

use App\Models\Employee;
use App\Imports\EmployeeImport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementEmployeeLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_user_identity_is_taken_from_selected_employee_and_employee_cannot_be_reused(): void
    {
        $admin = $this->createSuperAdmin();
        $teacherRole = Role::create(['name' => 'Guru']);

        $linkedEmployee = $this->createEmployee([
            'name' => 'Pegawai Sudah Tertaut',
            'nip' => '10001',
        ]);
        User::create([
            'employee_id' => $linkedEmployee->id,
            'name' => $linkedEmployee->name,
            'username' => '10001',
            'email' => null,
            'password' => 'password',
            'role_id' => $teacherRole->id,
            'status' => 'Aktif',
        ]);

        $newEmployee = $this->createEmployee([
            'name' => 'Guru Sumber',
            'nip' => null,
            'nuptk' => '20002',
            'email' => 'guru@example.test',
        ]);

        $this->actingAs($admin)
            ->get(route('users.create'))
            ->assertOk()
            ->assertSeeText('Guru Sumber')
            ->assertDontSeeText('Pegawai Sudah Tertaut')
            ->assertDontSee('name="name"', false)
            ->assertDontSee('name="username"', false)
            ->assertDontSee('name="email"', false);

        $this->post(route('users.store'), [
            'employee_id' => $newEmployee->id,
            'name' => 'Nama Palsu',
            'username' => 'username-palsu',
            'email' => 'palsu@example.test',
            'role_id' => $teacherRole->id,
            'status' => 'Nonaktif',
        ])->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'employee_id' => $newEmployee->id,
            'name' => 'Guru Sumber',
            'username' => '20002',
            'email' => 'guru@example.test',
            'role_id' => $teacherRole->id,
            'status' => 'Nonaktif',
        ]);
        $this->assertDatabaseCount('employees', 2);
    }

    public function test_updating_user_uses_existing_employee_without_creating_employee_and_keeps_role_status_managed_here(): void
    {
        $admin = $this->createSuperAdmin();
        $teacherRole = Role::create(['name' => 'Guru']);
        $adminRole = Role::create(['name' => 'Admin']);
        $employee = $this->createEmployee([
            'name' => 'Identitas Pegawai',
            'nip' => '30003',
            'email' => 'identitas@example.test',
        ]);
        $user = User::create([
            'employee_id' => $employee->id,
            'name' => 'Nama Lama',
            'username' => 'lama',
            'email' => null,
            'password' => 'password',
            'role_id' => $teacherRole->id,
            'status' => 'Aktif',
        ]);

        $this->actingAs($admin)
            ->get(route('users.edit', $user))
            ->assertOk()
            ->assertSeeText('Identitas Pegawai')
            ->assertDontSee('name="name"', false)
            ->assertDontSee('name="username"', false)
            ->assertDontSee('name="email"', false);

        $this->actingAs($admin)
            ->put(route('users.update', $user), [
                'employee_id' => $employee->id,
                'name' => 'Nama Palsu',
                'username' => 'username-palsu',
                'email' => 'palsu@example.test',
                'role_id' => $adminRole->id,
                'status' => 'Nonaktif',
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'employee_id' => $employee->id,
            'name' => 'Identitas Pegawai',
            'username' => '30003',
            'email' => 'identitas@example.test',
            'role_id' => $adminRole->id,
            'status' => 'Nonaktif',
        ]);
        $this->assertDatabaseCount('employees', 1);
    }

    public function test_adding_employee_does_not_automatically_create_a_user_account(): void
    {
        $admin = $this->createSuperAdmin();

        $this->actingAs($admin)
            ->post(route('pegawai.store'), [
                'nip' => '40004',
                'nuptk' => null,
                'name' => 'Pegawai Baru',
                'gender' => 'P',
                'employment_status' => 'PNS',
                'position' => 'Guru',
                'status' => 'Aktif',
                'email' => 'pegawai-baru@example.test',
            ])
            ->assertRedirect(route('pegawai.index'));

        $this->assertDatabaseHas('employees', [
            'nip' => '40004',
            'name' => 'Pegawai Baru',
        ]);
        $this->assertDatabaseMissing('users', [
            'username' => '40004',
        ]);
    }

    public function test_importing_employees_does_not_automatically_create_user_accounts(): void
    {
        $import = new EmployeeImport();
        $import->collection(collect([[
            'nip' => '60006',
            'nuptk' => null,
            'nama_lengkap' => 'Pegawai Impor',
            'jenis_kelamin' => 'L',
            'status_kepegawaian' => 'PNS',
            'jabatan' => 'Guru',
            'status' => 'Aktif',
            'email' => 'pegawai-impor@example.test',
        ]]));

        $this->assertSame(1, $import->getImportedCount());
        $this->assertDatabaseHas('employees', [
            'nip' => '60006',
            'name' => 'Pegawai Impor',
        ]);
        $this->assertDatabaseMissing('users', [
            'username' => '60006',
        ]);
    }

    public function test_editing_employee_does_not_overwrite_user_role_or_status(): void
    {
        $admin = $this->createSuperAdmin();
        $teacherRole = Role::create(['name' => 'Guru']);
        $employee = $this->createEmployee([
            'name' => 'Nama Sebelum',
            'nip' => '50005',
            'email' => 'sebelum@example.test',
        ]);
        $user = User::create([
            'employee_id' => $employee->id,
            'name' => $employee->name,
            'username' => $employee->nip,
            'email' => $employee->email,
            'password' => 'password',
            'role_id' => $teacherRole->id,
            'status' => 'Aktif',
        ]);

        $this->actingAs($admin)
            ->put(route('pegawai.update', $employee), [
                'name' => 'Nama Sesudah',
                'nip' => '50005',
                'nuptk' => null,
                'gender' => 'L',
                'employment_status' => 'PNS',
                'position' => 'Tata Usaha',
                'status' => 'Nonaktif',
                'email' => 'sesudah@example.test',
            ])
            ->assertRedirect(route('pegawai.index'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nama Sesudah',
            'username' => '50005',
            'email' => 'sesudah@example.test',
            'role_id' => $teacherRole->id,
            'status' => 'Aktif',
        ]);
    }

    private function createSuperAdmin(): User
    {
        return User::create([
            'name' => 'Super Admin',
            'username' => 'super-admin',
            'email' => 'super-admin@example.test',
            'password' => 'password',
            'role_id' => Role::create(['name' => 'Super Admin'])->id,
            'status' => 'Aktif',
        ]);
    }

    private function createEmployee(array $attributes = []): Employee
    {
        return Employee::create(array_merge([
            'name' => 'Nama Pegawai',
            'gender' => 'L',
            'position' => 'Guru',
            'employee_type' => 'Guru',
            'employment_status' => 'PNS',
            'status' => 'Aktif',
        ], $attributes));
    }
}
