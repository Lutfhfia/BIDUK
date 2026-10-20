<?php

namespace Tests\Feature;

use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class StudentIdentityValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_allows_reusing_deleted_identifiers_and_rejects_active_duplicates(): void
    {
        $deletedStudent = Student::create($this->studentData());
        $deletedStudent->delete();

        $validator = Validator::make(
            $this->studentData(),
            (new StoreStudentRequest())->rules()
        );

        $this->assertTrue($validator->passes());

        $activeStudent = Student::create($this->studentData([
            'nis' => 'NIS-ACTIVE',
            'nisn' => 'NISN-ACTIVE',
            'nik' => 'NIK-ACTIVE',
        ]));

        foreach (['nis', 'nisn', 'nik'] as $field) {
            $validator = Validator::make(
                $this->studentData([
                    'nis' => 'NIS-NEW',
                    'nisn' => 'NISN-NEW',
                    'nik' => 'NIK-NEW',
                    $field => $activeStudent->$field,
                ]),
                (new StoreStudentRequest())->rules()
            );

            $this->assertFalse($validator->passes(), "Duplicate {$field} should fail validation.");
            $this->assertArrayHasKey($field, $validator->errors()->messages());
        }
    }

    public function test_update_allows_current_or_deleted_identifiers_but_rejects_other_active_duplicates(): void
    {
        $student = Student::create($this->studentData([
            'nis' => 'NIS-CURRENT',
            'nisn' => 'NISN-CURRENT',
            'nik' => 'NIK-CURRENT',
        ]));

        $deletedStudent = Student::create($this->studentData([
            'nis' => 'NIS-DELETED',
            'nisn' => 'NISN-DELETED',
            'nik' => 'NIK-DELETED',
        ]));
        $deletedStudent->delete();

        $request = new UpdateStudentRequest();
        $request->setRouteResolver(function () use ($student) {
            $route = \Mockery::mock();
            $route->shouldReceive('parameter')
                ->with('student', null)
                ->andReturn($student);

            return $route;
        });

        $rules = $request->rules();
        $validator = Validator::make([
            'nis' => $student->nis,
            'nisn' => $student->nisn,
            'nik' => $student->nik,
            'name' => $student->name,
            'gender' => $student->gender,
            'status' => $student->status,
        ], $rules);

        $this->assertTrue($validator->passes());

        $validator = Validator::make([
            'nis' => $deletedStudent->nis,
            'nisn' => $deletedStudent->nisn,
            'nik' => $deletedStudent->nik,
            'name' => $student->name,
            'gender' => $student->gender,
            'status' => $student->status,
        ], $rules);

        $this->assertTrue($validator->passes());

        $otherActiveStudent = Student::create($this->studentData([
            'nis' => 'NIS-OTHER',
            'nisn' => 'NISN-OTHER',
            'nik' => 'NIK-OTHER',
        ]));

        foreach (['nis', 'nisn', 'nik'] as $field) {
            $attributes = [
                'nis' => $student->nis,
                'nisn' => $student->nisn,
                'nik' => $student->nik,
                'name' => $student->name,
                'gender' => $student->gender,
                'status' => $student->status,
            ];
            $attributes[$field] = $otherActiveStudent->$field;

            $validator = Validator::make($attributes, $rules);

            $this->assertFalse($validator->passes(), "Duplicate {$field} should fail validation.");
            $this->assertArrayHasKey($field, $validator->errors()->messages());
        }
    }

    private function studentData(array $overrides = []): array
    {
        return array_merge([
            'nis' => 'NIS-001',
            'nisn' => 'NISN-001',
            'nik' => 'NIK-001',
            'name' => 'Siswa Uji',
            'gender' => 'L',
            'status' => 'Aktif',
        ], $overrides);
    }
}
