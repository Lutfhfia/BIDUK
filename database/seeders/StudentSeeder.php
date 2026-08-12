<?php

namespace Database\Seeders;

use App\Models\ClassStudent;
use App\Models\SchoolClass;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = [
            [
                'nis' => '24001',
                'nisn' => '0101010101',
                'name' => 'Ahmad Ramadhan',
                'gender' => 'L',
                'birth_place' => 'Jakarta',
                'birth_date' => '2016-01-15',
                'religion' => 'Islam',
                'status' => 'Aktif',
            ],
            [
                'nis' => '24002',
                'nisn' => '0101010102',
                'name' => 'Siti Aisyah',
                'gender' => 'P',
                'birth_place' => 'Bandung',
                'birth_date' => '2016-03-20',
                'religion' => 'Islam',
                'status' => 'Aktif',
            ],
            [
                'nis' => '24003',
                'nisn' => '0101010103',
                'name' => 'Budi Santoso',
                'gender' => 'L',
                'birth_place' => 'Surabaya',
                'birth_date' => '2016-05-10',
                'religion' => 'Islam',
                'status' => 'Aktif',
            ],
            [
                'nis' => '24004',
                'nisn' => '0101010104',
                'name' => 'Maria Wijaya',
                'gender' => 'P',
                'birth_place' => 'Semarang',
                'birth_date' => '2016-07-25',
                'religion' => 'Katolik',
                'status' => 'Aktif',
            ],
        ];

        $class4A = SchoolClass::where('name', '4A')->first();

        foreach ($students as $studentData) {
            $student = Student::firstOrCreate(['nis' => $studentData['nis']], $studentData);

            if ($class4A) {
                ClassStudent::firstOrCreate([
                    'class_id' => $class4A->id,
                    'student_id' => $student->id,
                ], [
                    'status' => 'Aktif',
                    'entry_date' => now()->toDateString(),
                ]);
            }
        }
    }
}
