<?php

namespace Tests\Feature;

use App\Http\Controllers\RekapAbsensiController;
use App\Models\AcademicYear;
use App\Models\ActivityLog;
use App\Models\RekapAbsensi;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Tests\TestCase;

class RekapAbsensiSummaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_defaults_to_active_academic_year_but_allows_explicit_all_years(): void
    {
        $activeYear = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);
        $oldYear = AcademicYear::create([
            'name' => '2024/2025',
            'start_date' => '2024-07-01',
            'end_date' => '2025-06-30',
            'status' => 'inactive',
        ]);
        $activeClass = $this->createClass($activeYear, '1A');
        $oldClass = $this->createClass($oldYear, '1B');
        $activeSemester = $this->createSemester($activeYear, 'Ganjil');
        $oldSemester = $this->createSemester($oldYear, 'Ganjil');
        $this->createAttendance($this->createStudent('Aktif'), $activeClass, $activeSemester, 2, 0, 0);
        $this->createAttendance($this->createStudent('Aktif'), $oldClass, $oldSemester, 9, 0, 0);

        $user = new User;
        $user->setRelation('role', new Role(['name' => 'Admin']));
        Auth::shouldReceive('user')->twice()->andReturn($user);

        $defaultView = app(RekapAbsensiController::class)->index(
            Request::create('/rekap-absensi', 'GET')
        );
        $this->assertSame($activeYear->id, $defaultView->getData()['selectedAcademicYearId']);
        $this->assertSame([$activeClass->id], $defaultView->getData()['classes']->pluck('id')->all());
        $this->assertSame(2, $defaultView->getData()['rekapAbsensiSummary']->sakit);

        $allYearsView = app(RekapAbsensiController::class)->index(
            Request::create('/rekap-absensi', 'GET', ['academic_year_id' => ''])
        );
        $this->assertNull($allYearsView->getData()['selectedAcademicYearId']);
        $this->assertEqualsCanonicalizing(
            [$activeClass->id, $oldClass->id],
            $allYearsView->getData()['classes']->pluck('id')->all()
        );
        $this->assertSame(11, $allYearsView->getData()['rekapAbsensiSummary']->sakit);
    }

    public function test_index_filters_attendance_by_student_search_without_a_semester_filter(): void
    {
        $academicYear = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);
        $class = $this->createClass($academicYear, '1A');
        $semester = $this->createSemester($academicYear, 'Ganjil');
        $matchingStudent = $this->createStudent('Aktif');
        $otherStudent = $this->createStudent('Aktif');
        $this->createAttendance($matchingStudent, $class, $semester, 2, 0, 0);
        $this->createAttendance($otherStudent, $class, $semester, 8, 0, 0);

        $user = new User;
        $user->setRelation('role', new Role(['name' => 'Admin']));
        Auth::shouldReceive('user')->once()->andReturn($user);

        $view = app(RekapAbsensiController::class)->index(Request::create('/rekap-absensi', 'GET', [
            'academic_year_id' => $academicYear->id,
            'class_id' => $class->id,
            'search' => $matchingStudent->nis,
        ]));

        $this->assertSame(2, $view->getData()['rekapAbsensiSummary']->sakit);
        $this->assertCount(1, $view->getData()['rekapAbsensi']);
        $this->assertSame($matchingStudent->id, $view->getData()['rekapAbsensi']->first()->student->id);
    }

    public function test_create_update_and_detail_manage_both_semesters_as_one_annual_record(): void
    {
        $academicYear = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => 'active',
        ]);
        $class = $this->createClass($academicYear, '1A');
        $ganjil = $this->createSemester($academicYear, 'Ganjil');
        $genap = $this->createSemester($academicYear, 'Genap');
        $student = $this->createStudent('Aktif');
        $class->students()->attach($student->id, ['status' => 'Aktif']);

        $user = new User;
        $user->setRelation('role', new Role(['name' => 'Super Admin']));
        Auth::shouldReceive('user')->times(4)->andReturn($user);

        $activityLogger = Mockery::mock(ActivityLogger::class);
        $activityLogger->shouldReceive('log')
            ->twice()
            ->andReturn(new ActivityLog);
        $this->app->instance(ActivityLogger::class, $activityLogger);

        $controller = app(RekapAbsensiController::class);
        $controller->store(Request::create('/rekap-absensi', 'POST', [
            'academic_year_id' => $academicYear->id,
            'class_id' => $class->id,
            'student_id' => $student->id,
            'attendance' => [
                'ganjil' => ['sakit' => 1, 'izin' => 2, 'tanpa_keterangan' => 3],
                'genap' => ['sakit' => 4, 'izin' => 5, 'tanpa_keterangan' => 6],
            ],
        ]));

        $this->assertDatabaseHas('rekap_absensis', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'semester_id' => $ganjil->id,
            'sakit' => 1,
            'izin' => 2,
            'tanpa_keterangan' => 3,
        ]);
        $this->assertDatabaseHas('rekap_absensis', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'semester_id' => $genap->id,
            'sakit' => 4,
            'izin' => 5,
            'tanpa_keterangan' => 6,
        ]);

        $ganjilRecord = RekapAbsensi::where('semester_id', $ganjil->id)->firstOrFail();
        $edit = $controller->edit($ganjilRecord);
        $this->assertSame(1, $edit->getData()['attendanceBySemester']['ganjil']['sakit']);
        $this->assertSame(4, $edit->getData()['attendanceBySemester']['genap']['sakit']);

        $detail = $controller->show($ganjilRecord);
        $this->assertSame(1, $detail->getData()['attendanceBySemester']['ganjil']['sakit']);
        $this->assertSame(4, $detail->getData()['attendanceBySemester']['genap']['sakit']);

        $controller->update(Request::create('/rekap-absensi/'.$ganjilRecord->id, 'PUT', [
            'attendance' => [
                'ganjil' => ['sakit' => 7, 'izin' => 8, 'tanpa_keterangan' => 9],
                'genap' => ['sakit' => 10, 'izin' => 11, 'tanpa_keterangan' => 12],
            ],
        ]), $ganjilRecord);

        $this->assertDatabaseHas('rekap_absensis', [
            'student_id' => $student->id,
            'semester_id' => $ganjil->id,
            'sakit' => 7,
            'izin' => 8,
            'tanpa_keterangan' => 9,
        ]);
        $this->assertDatabaseHas('rekap_absensis', [
            'student_id' => $student->id,
            'semester_id' => $genap->id,
            'sakit' => 10,
            'izin' => 11,
            'tanpa_keterangan' => 12,
        ]);
    }

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

        $user = new User;
        $user->setRelation('role', new Role(['name' => 'Admin']));
        Auth::shouldReceive('user')->once()->andReturn($user);

        $view = app(RekapAbsensiController::class)->index(Request::create('/rekap-absensi', 'GET', [
            'academic_year_id' => $academicYear->id,
            'class_id' => $class->id,
            'semester_id' => $semester->id,
        ]));
        $summary = $view->getData()['rekapAbsensiSummary'];

        $this->assertSame(12, $summary->sakit);
        $this->assertSame(13, $summary->izin);
        $this->assertSame(14, $summary->tanpa_keterangan);
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
