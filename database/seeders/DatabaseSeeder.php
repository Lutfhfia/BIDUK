<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            RoleSeeder::class,
            EmployeeSeeder::class,
            AcademicYearSeeder::class,
            ClassSeeder::class,
            StudentSeeder::class,
        ]);

        // Buat Super Admin User yang terkait dengan Employee
        $superAdminEmployee = Employee::where('nip', '999999999')->first();

        if ($superAdminEmployee) {
            User::factory()->create([
                'name' => 'Super Admin',
                'email' => 'admin@sdn204.sch.id',
                'username' => 'admin',
                'password' => bcrypt('password'),
                'employee_id' => $superAdminEmployee->id,
                'role_id' => 1,
                'status' => 'Aktif',
            ]);
        }
    }
}
