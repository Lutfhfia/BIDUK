<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicYear = AcademicYear::where('status', 'active')->first();

        if (!$academicYear) {
            return;
        }

        $classes = [
            ['name' => '1A', 'grade_level' => 1],
            ['name' => '1B', 'grade_level' => 1],
            ['name' => '2A', 'grade_level' => 2],
            ['name' => '2B', 'grade_level' => 2],
            ['name' => '3A', 'grade_level' => 3],
            ['name' => '4A', 'grade_level' => 4],
            ['name' => '5A', 'grade_level' => 5],
            ['name' => '6A', 'grade_level' => 6],
        ];

        foreach ($classes as $classData) {
            SchoolClass::firstOrCreate([
                'academic_year_id' => $academicYear->id,
                'name' => $classData['name'],
            ], [
                'grade_level' => $classData['grade_level'],
                'status' => 'Aktif',
            ]);
        }
    }
}
