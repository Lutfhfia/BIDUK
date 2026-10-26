<?php

namespace Tests\Feature;

use App\Http\Controllers\RekapAbsensiController;
use App\Models\AcademicYear;
use App\Models\RekapAbsensi;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RekapAbsensiSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_summary_sums_all_absence_values_matching_the_selected_filters(): void
    {
        $academicYear = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);
        $otherAcademicYear = AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'status' => 'inactive',
        ]);
        $class = $this->createClass($academicYear, '1A');
        $otherClass = $this->createClass($academicYear, '1B');
        $otherYearClass = $this->createClass($otherAcademicYear, '1A');
        $semester = $this->createSemester($academicYear, 'Ganjil');
        $otherSemester = $this->createSemester($academicYear, 'Genap');

        $matchingStudent = $this->createStudent('Aktif');
        $this->createAttendance($matchingStudent, $class, $semester, 2, 3, 4);

        $otherSemesterStudent = $this->createStudent('Aktif');
        $this->createAttendance($otherSemesterStudent, $class, $otherSemester, 10, 10, 10);

        $otherClassStudent = $this->createStudent('Aktif');
        $this->createAttendance($otherClassStudent, $otherClass, $semester, 10, 10, 10);

        $otherYearStudent = $this->createStudent('Aktif');
        $otherYearSemester = $this->createSemester($otherAcademicYear, 'Ganjil');
        $this->createAttendance($otherYearStudent, $otherYearClass, $otherYearSemester, 10, 10, 10);

        $inactiveStudent = $this->createStudent('Nonaktif');
        $this->createAttendance($inactiveStudent, $class, $semester, 10, 10, 10);

        $user = new User();
        $user->setRelation('role', new Role(['name' => 'Admin']));
        Auth::shouldReceive('user')->once()->andReturn($user);

        $view = app(RekapAbsensiController::class)->index(Request::create('/rekap-absensi', 'GET', [
            'academic_year_id' => $academicYear->id,
            'class_id' => $class->id,
            'semester_id' => $semester->id,
        ]));
        $summary = $view->getData()['rekapAbsensiSummary'];

        $this->assertSame(2, $summary->sakit);
        $this->assertSame(3, $summary->izin);
        $this->assertSame(4, $summary->tanpa_keterangan);
    }

    private function createClass(AcademicYear $academicYear, string $name): SchoolClass
    {
        return SchoolClass::create([
            'academic_year_id' => $academicYear->id,
            'name' => $name,
            'grade_level' => 1,
            'status' => 'Aktif',
        ]);
    }

    private function createSemester(AcademicYear $academicYear, string $name): Semester
    {
        return Semester::create([
            'academic_year_id' => $academicYear->id,
            'name' => $name,
        ]);
    }

    private function createStudent(string $status): Student
    {
        static $index = 0;
        $index++;

        return Student::create([
            'nis' => "NIS-{$index}",
            'nisn' => "NISN-{$index}",
            'name' => "Siswa {$index}",
            'gender' => 'L',
            'status' => $status,
        ]);
    }

    private function createAttendance(
        Student $student,
        SchoolClass $class,
        Semester $semester,
        int $sakit,
        int $izin,
        int $tanpaKeterangan
    ): void {
        RekapAbsensi::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'semester_id' => $semester->id,
            'sakit' => $sakit,
            'izin' => $izin,
            'tanpa_keterangan' => $tanpaKeterangan,
        ]);
    }
}
