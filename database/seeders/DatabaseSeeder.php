<?php

namespace Database\Seeders;

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
            AcademicYearSeeder::class,
            ClassSeeder::class,
            StudentSeeder::class,
        ]);

        User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@sdn204.sch.id',
            'username' => 'admin',
            'password' => bcrypt('password'),
            'role_id' => 1,
        ]);
    }
}
