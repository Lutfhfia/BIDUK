<?php

namespace Database\Seeders;

use App\Models\Employee;
use Illuminate\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat Employee untuk Super Admin
        Employee::firstOrCreate(
            ['nip' => '999999999'],
            [
                'name' => 'Super Admin',
                'gender' => 'L',
                'position' => 'System Administrator',
                'employee_type' => 'Kepala Sekolah',
                'employment_status' => 'PNS',
                'status' => 'Aktif',
            ]
        );
    }
}
