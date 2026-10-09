<?php

namespace App\Imports;

use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentHealth;
use App\Models\StudentPhysique;
use App\Models\StudentPreviousEducation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Import;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

class StudentsImport implements Import, ToCollection, WithStartRow, WithChunkReading
{
    /**
     * Data siswa dimulai pada baris ke-4:
     * Baris 1: Header Utama
     * Baris 2: Subheader
     * Baris 3: Nomor Posisi Kolom (1-73)
     * Baris 4: Data Siswa Pertama
     */
    private const START_ROW = 4;

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
        foreach ($rows as $rowIndex => $row) {
            $data = array_values($row->toArray());

            if ($this->isEmptyStudentRow($data)) {
                continue;
            }

            $excelRowNum = self::START_ROW + $rowIndex;

            try {
                DB::transaction(function () use ($data) {
                    $student = $this->findExistingStudent($data);
                    $payload = $this->studentPayload($data);

                    if (!$student) {
                        $student = Student::create($payload);
                        $this->importedCount++;
                    } else {
                        // Jangan overwrite data yang ada jika payload menghasilkan null
                        $cleanPayload = array_filter($payload, fn ($v) => $v !== null);
                        $student->update($cleanPayload);
                        $this->updatedCount++;
                    }

                    $this->savePreviousEducation($student, $data);
                    $this->saveHealth($student, $data);
                    $this->savePhysique($student, $data);
                    $this->syncClass($student, $data);
                });
            } catch (Throwable $e) {
                $this->skippedCount++;
                $studentName = $this->nullableString($this->value($data, 4)) ?? 'Baris #' . $excelRowNum;

                $this->errors[] = [
                    'row' => $this->value($data, 1) ?? $excelRowNum,
                    'name' => $studentName,
                    'message' => $e->getMessage(),
                ];

                Log::error('Import Siswa Gagal pada baris ' . $excelRowNum, [
                    'row' => $excelRowNum,
                    'name' => $studentName,
                    'message' => $e->getMessage(),
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
            'name' => $this->nullableString($this->value($r, 4)),
            'nickname' => $this->nullableString($this->value($r, 5)),
            'gender' => $this->gender($this->value($r, 6)),
            'birth_place' => $this->nullableString($this->value($r, 7)),
            'birth_date' => $this->date($this->value($r, 8)),
            'religion' => $this->nullableString($this->value($r, 9)),
            'citizenship' => $this->nullableString($this->value($r, 10)),
            'child_position' => $this->integer($this->value($r, 11)),
            'siblings_biological' => $this->integer($this->value($r, 12)) ?? 0,
            'siblings_step' => $this->integer($this->value($r, 13)) ?? 0,
            'siblings_adopted' => $this->integer($this->value($r, 14)) ?? 0,
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

        $semester = Semester::where('academic_year_id', $academicYear->id)
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

        $semester = Semester::where('academic_year_id', $academicYear->id)
            ->orderBy('id')
            ->first();

        if (!$semester) {
            return;
        }

        StudentPhysique::updateOrCreate(
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
        $nisn = $this->nullableString($this->value($r, 3));
        $nis = $this->nullableString($this->value($r, 2));
        $name = $this->nullableString($this->value($r, 4));
        $birthDate = $this->date($this->value($r, 8));
        $birthPlace = $this->nullableString($this->value($r, 7));

        if ($nisn !== null && $nisn !== '') {
            $student = Student::where('nisn', $nisn)->first();
            if ($student) {
                return $student;
            }
        }

        if ($nis !== null && $nis !== '') {
            $student = Student::where('nis', $nis)->first();
            if ($student) {
                return $student;
            }
        }

        if ($name !== null && $birthDate !== null) {
            $student = Student::where('name', $name)
                ->whereDate('birth_date', $birthDate)
                ->first();
            if ($student) {
                return $student;
            }
        }

        if ($name !== null && $birthPlace !== null) {
            $student = Student::where('name', $name)
                ->where('birth_place', $birthPlace)
                ->first();
            if ($student) {
                return $student;
            }
        }

        if ($name !== null && $nis === null && $nisn === null && $birthDate === null) {
            $matches = Student::where('name', $name)->get();
            if ($matches->count() === 1) {
                return $matches->first();
            }
        }

        return null;
    }

    private function isEmptyStudentRow(array $r): bool
    {
        $name = $this->nullableString($this->value($r, 4));
        $nis = $this->nullableString($this->value($r, 2));
        $nisn = $this->nullableString($this->value($r, 3));
        $nickname = $this->nullableString($this->value($r, 5));

        return $name === null && $nis === null && $nisn === null && $nickname === null;
    }

    private function value(array $row, int $column): mixed
    {
        return $row[$column - 1] ?? null;
    }

    private function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    private function integer(mixed $value): ?int
    {
        $value = $this->nullableString($value);

        if ($value === null) {
            return null;
        }

        return is_numeric($value) ? (int) $value : null;
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

    private function gender(mixed $value): ?string
    {
        $value = $this->nullableString($value);
        if ($value === null) {
            return null;
        }

        $upper = strtoupper($value);

        return match ($upper) {
            'L', 'LAKI-LAKI', 'LAKI LAKI', 'LAKI - LAKI', 'M', 'MALE' => 'L',
            'P', 'PEREMPUAN', 'WANITA', 'F', 'FEMALE' => 'P',
            default => in_array($upper[0] ?? '', ['L', 'P'], true) ? $upper[0] : null,
        };
    }

    private function date(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            }

            return Carbon::parse($value)->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }

    private function cleanRtRwValue(?string $val): ?string
    {
        if ($val === null) {
            return null;
        }
        $cleaned = preg_replace('/^(rt|rw)[\s\.\:\-]*/i', '', $val);
        $cleaned = trim($cleaned);
        return $cleaned === '' ? null : $cleaned;
    }

    private function combineRtRw(mixed $rt, mixed $rw): ?string
    {
        $rtRaw = $this->nullableString($rt);
        $rwRaw = $this->nullableString($rw);

        if ($rtRaw === null && $rwRaw === null) {
            return null;
        }

        $cleanRt = $this->cleanRtRwValue($rtRaw);
        $cleanRw = $this->cleanRtRwValue($rwRaw);

        if ($cleanRt !== null && $cleanRw !== null) {
            $combined = "RT $cleanRt / RW $cleanRw";
        } elseif ($cleanRt !== null) {
            $combined = "RT $cleanRt";
        } elseif ($cleanRw !== null) {
            $combined = "RW $cleanRw";
        } else {
            $combined = trim(($rtRaw ?? '') . ' ' . ($rwRaw ?? ''));
        }

        return mb_substr($combined, 0, 20);
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
