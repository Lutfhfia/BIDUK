<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'Super Admin', 'description' => 'Administrator dengan akses penuh'],
            ['name' => 'Admin', 'description' => 'Tata Usaha / Admin Akademik'],
            ['name' => 'Guru', 'description' => 'Guru & Wali Kelas'],
            ['name' => 'Kepala Sekolah', 'description' => 'Kepala Sekolah (Monitoring)'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}
