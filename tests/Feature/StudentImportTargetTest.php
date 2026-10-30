<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class StudentImportTargetTest extends TestCase
{
    use RefreshDatabase;

    private array $temporaryWorkbooks = [];

    protected function tearDown(): void
    {
        foreach ($this->temporaryWorkbooks as $temporaryWorkbook) {
            if (is_file($temporaryWorkbook)) {
                unlink($temporaryWorkbook);
            }
        }

        parent::tearDown();
    }

    public function test_teacher_import_form_only_offers_assigned_years_and_classes(): void
    {
        [$teacher, $year, $class] = $this->createTeacherWithClass();
        $otherYear = $this->createYear('2027/2028', 'inactive');
        $otherClass = $this->createClass($otherYear, '4B');

        $this->actingAs($teacher)
            ->get(route('students.import.form'))
            ->assertOk()
            ->assertSeeText($year->name)
            ->assertSeeText($class->name)
            ->assertSeeText('Semua Kelas (tanpa penempatan kelas)')
            ->assertDontSeeText($otherYear->name)
            ->assertDontSeeText($otherClass->name);
    }

    public function test_non_teacher_import_form_offers_classes_from_all_academic_years(): void
    {
        $role = Role::create(['name' => 'Super Admin']);
        $permission = Permission::create(['name' => 'students.import']);
        $role->permissions()->attach($permission->id);
        $admin = User::create([
            'name' => 'Admin Import',
            'username' => 'admin-import-form',
            'email' => 'admin-import-form@example.test',
            'password' => 'password',
            'role_id' => $role->id,
            'status' => 'Aktif',
        ]);
        $firstYear = $this->createYear('2025/2026', 'active');
        $secondYear = $this->createYear('2024/2025', 'inactive');
        $firstClass = $this->createClass($firstYear, '3A');
        $secondClass = $this->createClass($secondYear, '4B');

        $this->actingAs($admin)
            ->get(route('students.import.form'))
            ->assertOk()
            ->assertSeeText($firstYear->name)
            ->assertSeeText($secondYear->name)
            ->assertSeeText($firstClass->name)
            ->assertSeeText($secondClass->name);
    }

    public function test_teacher_can_import_to_selected_historical_class_and_excel_cannot_override_it(): void
    {
        [$teacher, $year, $class] = $this->createTeacherWithClass('2024/2025', 'inactive');
        $otherClass = $this->createClass($year, '3B');
        $semester = Semester::create([
            'academic_year_id' => $year->id,
            'name' => 'Ganjil',
        ]);
        $file = $this->makeWorkbook([
            'B4' => 'NIS-IMPORT-SELECTED',
            'C4' => 'NISN-IMPORT-SELECTED',
            'D4' => 'Siswa Import Terpilih',
            'E4' => 'Siswa',
            'F4' => 'L',
            'P4' => 52,
            'Q4' => 150,
            'S4' => 'Alergi',
            'BL4' => 'Kelas Lain dari Excel',
        ]);

        $response = $this->actingAs($teacher)->post(route('students.import'), [
            'academic_year_id' => $year->id,
            'class_id' => $class->id,
            'file' => new UploadedFile(
                $file,
                'students.xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                null,
                true
            ),
        ]);

        $response->assertRedirect(route('students.index'));
        $response->assertSessionHas('success');

        $student = Student::where('nis', 'NIS-IMPORT-SELECTED')->firstOrFail();
        $this->assertSame('Siswa Import Terpilih', $student->name);
        $this->assertDatabaseHas('class_student', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'status' => 'Aktif',
        ]);
        $this->assertDatabaseMissing('class_student', [
            'student_id' => $student->id,
            'class_id' => $otherClass->id,
        ]);
        $this->assertDatabaseHas('student_healths', [
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'semester_id' => $semester->id,
            'diseases' => 'Alergi',
        ]);
        $this->assertDatabaseHas('student_physiques', [
            'student_id' => $student->id,
            'academic_year_id' => $year->id,
            'semester_id' => $semester->id,
            'height' => 150,
            'weight' => 52,
        ]);
    }

    public function test_teacher_cannot_import_to_a_class_not_assigned_to_them(): void
    {
        [$teacher, $year] = $this->createTeacherWithClass();
        $otherClass = $this->createClass($year, '3B');
        $file = $this->makeWorkbook(['D4' => 'Harus Ditolak']);

        $this->actingAs($teacher)
            ->post(route('students.import'), [
                'academic_year_id' => $year->id,
                'class_id' => $otherClass->id,
                'file' => new UploadedFile(
                    $file,
                    'students.xlsx',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    null,
                    true
                ),
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('students', [
            'name' => 'Harus Ditolak',
        ]);
    }

    public function test_teacher_can_import_students_without_class_placement(): void
    {
        [$teacher, $year] = $this->createTeacherWithClass();
        $file = $this->makeWorkbook([
            'B4' => 'NIS-IMPORT-NO-CLASS',
            'C4' => 'NISN-IMPORT-NO-CLASS',
            'D4' => 'Siswa Tanpa Kelas',
        ]);

        $response = $this->actingAs($teacher)->post(route('students.import'), [
            'academic_year_id' => $year->id,
            'class_id' => '',
            'file' => new UploadedFile(
                $file,
                'students.xlsx',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                null,
                true
            ),
        ]);

        $response->assertRedirect(route('students.index'));
        $response->assertSessionHas('success');

        $student = Student::where('nis', 'NIS-IMPORT-NO-CLASS')->firstOrFail();
        $this->assertSame('Siswa Tanpa Kelas', $student->name);
        $this->assertDatabaseMissing('class_student', [
            'student_id' => $student->id,
        ]);
    }

    private function createTeacherWithClass(
        string $yearName = '2025/2026',
        string $yearStatus = 'active'
    ): array {
        $role = Role::create(['name' => 'Guru']);
        $permission = Permission::create(['name' => 'students.import']);
        $role->permissions()->attach($permission->id);

        $employee = Employee::create([
            'name' => 'Guru Import',
            'gender' => 'P',
            'position' => 'Guru',
            'employee_type' => 'Guru',
            'employment_status' => 'PNS',
            'status' => 'Aktif',
        ]);
        $teacher = User::create([
            'name' => $employee->name,
            'username' => 'guru-import-' . str_replace('/', '-', $yearName),
            'email' => 'guru-import-' . str_replace('/', '-', $yearName) . '@example.test',
            'password' => 'password',
            'role_id' => $role->id,
            'employee_id' => $employee->id,
            'status' => 'Aktif',
        ]);
        $year = $this->createYear($yearName, $yearStatus);
        $class = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => '3A',
            'grade_level' => 3,
            'homeroom_teacher_id' => $employee->id,
            'homeroom_teacher_user_id' => $teacher->id,
            'status' => 'Aktif',
        ]);

        return [$teacher, $year, $class];
    }

    private function createYear(string $name, string $status): AcademicYear
    {
        return AcademicYear::create([
            'name' => $name,
            'start_date' => '2025-07-01',
            'end_date' => '2026-06-30',
            'status' => $status,
        ]);
    }

    private function createClass(AcademicYear $year, string $name): SchoolClass
    {
        return SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => $name,
            'grade_level' => 3,
            'status' => 'Aktif',
        ]);
    }

    private function makeWorkbook(array $cells): string
    {
        $spreadsheet = new Spreadsheet();
        foreach ($cells as $cell => $value) {
            $spreadsheet->getActiveSheet()->setCellValue($cell, $value);
        }

        $path = tempnam(sys_get_temp_dir(), 'student-import-');
        $this->temporaryWorkbooks[] = $path;
        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return $path;
    }
}
