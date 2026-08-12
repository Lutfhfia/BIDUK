<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Database\Seeder;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $activeYear = AcademicYear::firstOrCreate([
            'name' => '2026/2027'
        ], [
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'status' => 'active',
        ]);
        Semester::firstOrCreate(['academic_year_id' => $activeYear->id, 'name' => 'Ganjil']);
        Semester::firstOrCreate(['academic_year_id' => $activeYear->id, 'name' => 'Genap']);
        
        AcademicYear::firstOrCreate([
            'name' => '2025/2026'
        ], [
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'inactive',
        ]);
    }
}
