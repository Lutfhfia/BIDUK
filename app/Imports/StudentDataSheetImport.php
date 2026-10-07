<?php

namespace App\Imports;

use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentHealth;
use App\Models\StudentPreviousEducation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Illuminate\Support\Collection;
use Throwable;

class StudentDataSheetImport implements ToCollection, WithStartRow, WithChunkReading
{
    private const START_ROW = 6;

    private int $importedCount = 0;
    private int $updatedCount = 0;
    private int $skippedCount = 0;
    private array $errors = [];

    public function startRow(): int
    {
        return self::START_ROW;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $data = array_values($row->toArray());

            if ($this->isEmptyStudentRow($data)) {
                continue;
            }

            try {
                DB::transaction(function () use ($data) {
                    $student = $this->findExistingStudent($data);

                    $payload = $this->studentPayload($data);

                    if (!$student) {
                        $student = Student::create($payload);
                        $this->importedCount++;
                    } else {
                        $student->update($payload);
                        $this->updatedCount++;
                    }

                    $this->savePreviousEducation($student, $data);
                    $this->saveHealth($student, $data);
                    $this->savePhysique($student, $data);
                    $this->syncClass($student, $data);
                });
            } catch (Throwable $e) {
                $this->skippedCount++;
                $this->errors[] = [
                    'row' => $data[0] ?? '?',
                    'name' => $this->value($data, 4),
                    'message' => $e->getMessage(),
                ];

                Log::error('Import Data Siswa BIDUK gagal', [
                    'row' => $data[0] ?? null,
                    'name' => $this->value($data, 4),
                    'exception' => $e,
                ]);
            }
        }
    }

    private function studentPayload(array $r): array
    {
        return [
            'student_number' => $this->nullableString($this->value($r, 1)),
            'nis' => $this->nullableString($this->value($r, 2)),
            'nisn' => $this->nullableString($this->value($r, 3)),
            'name' => $this->nullableString($this->value($r, 4)) ?? 'Tanpa Nama',
            'nickname' => $this->nullableString($this->value($r, 5)),
            'gender' => $this->gender($this->value($r, 6)),
            'birth_place' => $this->nullableString($this->value($r, 7)),
            'birth_date' => $this->date($this->value($r, 8)),
            'religion' => $this->nullableString($this->value($r, 9)),
            'citizenship' => $this->nullableString($this->value($r, 10)),
            'child_position' => $this->integer($this->value($r, 11)),
            'siblings_biological' => $this->integerOrZero($this->value($r, 12)),
            'siblings_step' => $this->integerOrZero($this->value($r, 13)),
            'siblings_adopted' => $this->integerOrZero($this->value($r, 14)),
            'daily_language' => $this->nullableString($this->value($r, 15)),
            'blood_type' => $this->nullableString($this->value($r, 18)),
            'diseases' => $this->nullableString($this->value($r, 19)),
            'immunizations' => $this->nullableString($this->value($r, 20)),
            'address' => $this->nullableString($this->value($r, 21)),
            'rt_rw' => $this->combineRtRw($this->value($r, 22), $this->value($r, 23)),
            'rt' => $this->nullableString($this->value($r, 22)),
            'rw' => $this->nullableString($this->value($r, 23)),
            'village' => $this->nullableString($this->value($r, 24)),
            'district' => $this->nullableString($this->value($r, 25)),
            'city' => $this->nullableString($this->value($r, 26)),
            'province' => $this->nullableString($this->value($r, 27)),
            'postal_code' => $this->nullableString($this->value($r, 28)),
            'living_with' => $this->nullableString($this->value($r, 29)),
            'distance_to_school' => $this->nullableString($this->value($r, 30)),

            'father_name' => $this->nullableString($this->value($r, 31)),
            'father_birth_place' => $this->nullableString($this->value($r, 32)),
            'father_birth_date' => $this->date($this->value($r, 33)),
            'father_education' => $this->nullableString($this->value($r, 34)),
            'father_job' => $this->nullableString($this->value($r, 35)),
            'father_address' => $this->nullableString($this->value($r, 36)),
            'father_phone' => $this->nullableString($this->value($r, 37)),
            'father_citizenship' => $this->nullableString($this->value($r, 38)),

            'mother_name' => $this->nullableString($this->value($r, 39)),
            'mother_birth_place' => $this->nullableString($this->value($r, 40)),
            'mother_birth_date' => $this->date($this->value($r, 41)),
            'mother_education' => $this->nullableString($this->value($r, 42)),
            'mother_job' => $this->nullableString($this->value($r, 43)),
            'mother_address' => $this->nullableString($this->value($r, 44)),
            'mother_phone' => $this->nullableString($this->value($r, 45)),
            'mother_citizenship' => $this->nullableString($this->value($r, 46)),

            'guardian_name' => $this->nullableString($this->value($r, 47)),
            'guardian_birth_place' => $this->nullableString($this->value($r, 48)),
            'guardian_birth_date' => $this->date($this->value($r, 49)),
            'guardian_education' => $this->nullableString($this->value($r, 50)),
            'guardian_job' => $this->nullableString($this->value($r, 51)),
            'guardian_address' => $this->nullableString($this->value($r, 52)),
            'guardian_phone' => $this->nullableString($this->value($r, 53)),
            'guardian_citizenship' => $this->nullableString($this->value($r, 54)),
            'guardian_relation' => $this->nullableString($this->value($r, 55)),

            'student_origin' => $this->nullableString($this->value($r, 56)),
            'scholarship_type' => $this->nullableString($this->value($r, 65)),

            'graduation_year' => $this->nullableString($this->value($r, 66)),
            'continued_school' => $this->nullableString($this->value($r, 67)),

            'transfer_left_grade' => $this->nullableString($this->value($r, 68)),
            'transfer_to_school' => $this->nullableString($this->value($r, 69)),
            'transfer_to_grade' => $this->nullableString($this->value($r, 70)),
            'transfer_date' => $this->date($this->value($r, 71)),

            'leaving_date' => $this->date($this->value($r, 72)),
            'leaving_reason' => $this->nullableString($this->value($r, 73)),
        ];
    }

    private function savePreviousEducation(Student $student, array $r): void
    {
        $payload = [
            'student_origin' => $this->nullableString($this->value($r, 56)),
            'kindergarten_name' => $this->nullableString($this->value($r, 57)),
            'kindergarten_address' => $this->nullableString($this->value($r, 58)),
            'sttb_date' => $this->date($this->value($r, 59)),
            'sttb_number' => $this->nullableString($this->value($r, 60)),
            'previous_school_name' => $this->nullableString($this->value($r, 61)),
            'transfer_from_school' => $this->nullableString($this->value($r, 61)),
            'transfer_from_grade' => $this->nullableString($this->value($r, 62)),
            'transfer_accepted_date' => $this->date($this->value($r, 63)),
            'transfer_to_class' => $this->nullableString($this->value($r, 64)),
        ];

        $hasAny = collect($payload)->contains(fn ($v) => $v !== null && $v !== '');

        if ($hasAny) {
            $student->previousEducation()->updateOrCreate(
                ['student_id' => $student->id],
                $payload
            );
        }
    }

    private function saveHealth(Student $student, array $r): void
    {
        $academicYear = AcademicYear::getActive();

        if (!$academicYear) {
            return;
        }

        $semester = \App\Models\Semester::where('academic_year_id', $academicYear->id)
            ->orderBy('id')
            ->first();

        if (!$semester) {
            return;
        }

        $payload = [
            'hearing' => null,
            'vision' => null,
            'teeth' => null,
            'diseases' => $this->nullableString($this->value($r, 19)),
            'immunizations' => $this->nullableString($this->value($r, 20)),
            'notes' => null,
        ];

        $hasHealth = $payload['diseases'] !== null || $payload['immunizations'] !== null;

        if ($hasHealth) {
            StudentHealth::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                ],
                $payload
            );
        }
    }

    private function savePhysique(Student $student, array $r): void
    {
        $height = $this->numeric($this->value($r, 17));
        $weight = $this->numeric($this->value($r, 16));

        if ($height === null && $weight === null) {
            return;
        }

        $academicYear = AcademicYear::getActive();

        if (!$academicYear) {
            return;
        }

        $semester = \App\Models\Semester::where('academic_year_id', $academicYear->id)
            ->orderBy('id')
            ->first();

        if (!$semester) {
            return;
        }

        \App\Models\StudentPhysique::updateOrCreate(
            [
                'student_id' => $student->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
            ],
            [
                'height' => $height,
                'weight' => $weight,
            ]
        );
    }

    private function syncClass(Student $student, array $r): void
    {
        $className = $this->nullableString($this->value($r, 64));

        if (!$className) {
            return;
        }

        $activeYear = AcademicYear::getActive();

        if (!$activeYear) {
            return;
        }

        $class = SchoolClass::where('academic_year_id', $activeYear->id)
            ->where(function ($q) use ($className) {
                $q->where('name', $className)
                    ->orWhere('name', 'like', '%' . $className . '%');
            })
            ->first();

        if (!$class) {
            return;
        }

        ClassStudent::where('student_id', $student->id)
            ->whereHas('schoolClass', fn ($q) => $q->where('academic_year_id', $activeYear->id))
            ->where('status', 'Aktif')
            ->update([
                'status' => 'Naik Kelas',
                'exit_date' => now()->toDateString(),
            ]);

        ClassStudent::updateOrCreate(
            [
                'class_id' => $class->id,
                'student_id' => $student->id,
            ],
            [
                'status' => 'Aktif',
                'entry_date' => now()->toDateString(),
                'exit_date' => null,
            ]
        );
    }

    private function findExistingStudent(array $r): ?Student
    {
        $nis = $this->nullableString($this->value($r, 2));
        $nisn = $this->nullableString($this->value($r, 3));
        $name = $this->nullableString($this->value($r, 4));
        $birthDate = $this->date($this->value($r, 8));

        if ($nis !== null) {
            $student = Student::where('nis', $nis)->first();
            if ($student) return $student;
        }

        if ($nisn !== null) {
            $student = Student::where('nisn', $nisn)->first();
            if ($student) return $student;
        }

        if ($name !== null && $birthDate !== null) {
            return Student::where('name', $name)
                ->whereDate('birth_date', $birthDate)
                ->first();
        }

        return null;
    }

    private function isEmptyStudentRow(array $r): bool
    {
        $name = $this->nullableString($this->value($r, 4));
        $nis = $this->nullableString($this->value($r, 2));
        $nisn = $this->nullableString($this->value($r, 3));

        return $name === null && $nis === null && $nisn === null;
    }

    private function value(array $row, int $column): mixed
    {
        return $row[$column - 1] ?? null;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) return null;

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function integer(mixed $value): ?int
    {
        $value = $this->nullableString($value);

        if ($value === null) return null;

        return is_numeric($value) ? (int) $value : null;
    }

    private function integerOrZero(mixed $value): int
    {
        return $this->integer($value) ?? 0;
    }

    private function numeric(mixed $value): ?float
    {
        $value = $this->nullableString($value);

        if ($value === null) {
            return null;
        }

        $value = str_replace(',', '.', $value);

        return is_numeric($value) ? (float) $value : null;
    }

    private function gender(mixed $value): string
    {
        $value = strtoupper(trim((string) $value));

        return match ($value) {
            'L', 'LAKI-LAKI', 'LAKI LAKI', 'M', 'MALE' => 'L',
            'P', 'PEREMPUAN', 'F', 'FEMALE' => 'P',
            default => 'P',
        };
    }

    private function date(mixed $value): ?string
    {
        if ($value === null || $value === '') return null;

        try {
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }

    private function combineRtRw(mixed $rt, mixed $rw): ?string
    {
        $rt = $this->nullableString($rt);
        $rw = $this->nullableString($rw);

        if (!$rt && !$rw) return null;
        return trim('RT ' . ($rt ?? '-') . ' / RW ' . ($rw ?? '-'));
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getSkippedCount(): int
    {
        return $this->skippedCount;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
