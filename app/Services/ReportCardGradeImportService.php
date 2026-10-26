<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\ReportCardGrade;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportCardGradeImportService
{
    private const SHEET_NAME = 'IMPORT NILAI';

    private const FIRST_SUBJECT_COLUMN = 4;

    private const SUBJECT_FIELDS = [
        ['Data Nilai', 'Nilai Akhir', 'score'],
        ['Data Nilai', 'Predikat', 'knowledge_predicate'],
        ['Data Nilai', 'Capaian Pembelajaran', 'learning_outcome'],
    ];

    public function createTemplate(array $context): Spreadsheet
    {
        $academicYear = $this->resolveAcademicYear($context);
        $class = $this->resolveClass($context, $academicYear);
        $semester = $this->resolveSemester($context, $academicYear);
        $subjects = $this->subjectsForClass($class);

        if ($subjects->isEmpty()) {
            throw ValidationException::withMessages([
                'class_id' => 'Kelas '.$class->name.' belum memiliki mata pelajaran aktif yang ditetapkan.',
            ]);
        }

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle(self::SHEET_NAME);
        $lastColumn = Coordinate::stringFromColumnIndex(
            self::FIRST_SUBJECT_COLUMN + ($subjects->count() * count(self::SUBJECT_FIELDS)) - 1
        );

        $sheet->mergeCells('A1:'.$lastColumn.'1');
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT NILAI RAPOT — BIDUK SDN 204 PALEMBANG');
        foreach ([
            3 => ['Tahun Ajaran', $academicYear->name],
            4 => ['Kelas', 'Kelas '.$class->name],
            5 => ['Semester', $semester->name],
        ] as $row => [$label, $value]) {
            $sheet->setCellValue('A'.$row, $label);
            $sheet->setCellValue('B'.$row, $value);
        }

        foreach (['A', 'B', 'C'] as $column) {
            $sheet->mergeCells($column.'7:'.$column.'9');
        }
        $sheet->setCellValue('A7', 'No');
        $sheet->setCellValue('B7', 'NISN');
        $sheet->setCellValue('C7', 'Nama');

        foreach ($subjects as $index => $subject) {
            $startIndex = self::FIRST_SUBJECT_COLUMN + ($index * count(self::SUBJECT_FIELDS));
            $startColumn = Coordinate::stringFromColumnIndex($startIndex);
            $endColumn = Coordinate::stringFromColumnIndex($startIndex + count(self::SUBJECT_FIELDS) - 1);

            $sheet->mergeCells($startColumn.'7:'.$endColumn.'7');
            $sheet->setCellValue($startColumn.'7', $subject->name);
            $sheet->mergeCells($startColumn.'8:'.$endColumn.'8');
            $sheet->setCellValue($startColumn.'8', 'Data Nilai');

            foreach (self::SUBJECT_FIELDS as $offset => [, $field]) {
                $column = Coordinate::stringFromColumnIndex($startIndex + $offset);
                $sheet->setCellValue($column.'9', $field);
                $sheet->getColumnDimension($column)->setWidth(
                    $offset === 2 ? 32 : ($offset === 0 ? 13 : 12)
                );
            }
        }

        $sheet->mergeCells('A10:'.$lastColumn.'10');
        $sheet->setCellValue('A10', 'Isi data siswa mulai baris 11. NISN digunakan untuk mencocokkan siswa dengan data BIDUK.');
        $students = $class->students()
            ->orderBy('students.name')
            ->orderBy('students.id')
            ->get(['students.id', 'students.nisn', 'students.name']);
        foreach ($students as $index => $student) {
            $row = 11 + $index;
            $sheet->setCellValue('A'.$row, $index + 1);
            $sheet->setCellValueExplicit('B'.$row, (string) $student->nisn, DataType::TYPE_STRING);
            $sheet->setCellValue('C'.$row, $student->name);

            for ($subjectIndex = 0; $subjectIndex < $subjects->count(); $subjectIndex++) {
                $startIndex = self::FIRST_SUBJECT_COLUMN + ($subjectIndex * count(self::SUBJECT_FIELDS));
                $predicateColumn = Coordinate::stringFromColumnIndex($startIndex + 1);
                $validation = $sheet->getCell($predicateColumn.$row)->getDataValidation();
                $validation->setType(DataValidation::TYPE_LIST);
                $validation->setErrorStyle(DataValidation::STYLE_STOP);
                $validation->setAllowBlank(true);
                $validation->setShowInputMessage(true);
                $validation->setShowErrorMessage(true);
                $validation->setShowDropDown(false);
                $validation->setFormula1('"A,B,C,D"');
                $validation->setPromptTitle('Pilih Predikat');
                $validation->setPrompt('Pilih A, B, C, atau D dari daftar.');
                $validation->setErrorTitle('Predikat tidak valid');
                $validation->setError('Predikat harus A, B, C, atau D.');
            }
        }
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(30);
        $sheet->getRowDimension(1)->setRowHeight(28);
        $sheet->getRowDimension(7)->setRowHeight(32);
        $sheet->getRowDimension(8)->setRowHeight(30);
        $sheet->getRowDimension(9)->setRowHeight(30);
        $sheet->freezePane('D11');
        $sheet->getStyle('A1:'.$lastColumn.'1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '17365D']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('A3:B5')->applyFromArray([
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('A3:A5')->getFont()->setBold(true);
        $sheet->getStyle('A7:'.$lastColumn.'9')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D9E2F3']]],
        ]);
        $sheet->getStyle('A10:'.$lastColumn.'10')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '666666']],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);

        return $spreadsheet;
    }

    public function preview(string $path, array $context): array
    {
        $academicYear = $this->resolveAcademicYear($context);
        $class = $this->resolveClass($context, $academicYear);
        $semester = $this->resolveSemester($context, $academicYear);
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getSheetByName(self::SHEET_NAME);
        $result = $this->emptyPreview();

        if (! $sheet || $spreadsheet->getSheetCount() !== 1) {
            $result['global_errors'][] = 'File harus memiliki tepat satu sheet bernama IMPORT NILAI. Gunakan file Template Import Nilai Rapot BIDUK.';

            return $result;
        }

        $result['metadata'] = [
            'academic_year' => $academicYear->name,
            'class' => 'Kelas '.$class->name,
            'semester' => $semester->name,
        ];
        $result['academic_year_id'] = $academicYear->id;
        $subjects = $this->subjectsForClass($class);
        if ($subjects->isEmpty()) {
            $result['global_errors'][] = 'Kelas '.$class->name.' belum memiliki mata pelajaran aktif yang ditetapkan.';

            return $result;
        }
        $enrolledStudentIds = $class->students()
            ->pluck('students.id')
            ->flip();
        $columns = $this->validateStructure(
            $sheet,
            $subjects,
            $academicYear,
            $class,
            $semester,
            $result['global_errors']
        );

        if ($result['global_errors']) {
            return $result;
        }

        $highestRow = min($sheet->getHighestDataRow(), 1000);
        $seenNisn = [];
        for ($rowNumber = 11; $rowNumber <= $highestRow; $rowNumber++) {
            $identity = [
                'nisn' => trim($this->cellText($sheet, 'B', $rowNumber)),
                'name' => trim($this->cellText($sheet, 'C', $rowNumber)),
            ];
            $hasGradeData = collect($columns)->contains(
                fn (array $column) => $this->hasValue(
                    $this->cellValue($sheet, $column['column'], $rowNumber)
                )
            );

            if ($this->isExampleRow($sheet, $rowNumber, $identity)) {
                continue;
            }
            if ($identity['nisn'] === '' && $identity['name'] === '' && ! $hasGradeData) {
                continue;
            }

            $result['summary']['read_rows']++;
            $rowErrors = [];
            $rowWarnings = [];
            $nisn = $identity['nisn'];

            if ($nisn === '') {
                $rowErrors[] = 'NISN wajib diisi untuk mencocokkan siswa.';
            } elseif (isset($seenNisn[$nisn])) {
                $rowErrors[] = 'NISN duplikat pada baris '.$seenNisn[$nisn].' dan '.$rowNumber.'. Setiap siswa hanya boleh muncul satu kali.';
            } else {
                $seenNisn[$nisn] = $rowNumber;
            }

            $student = $nisn !== '' ? Student::where('nisn', $nisn)->first() : null;
            if ($nisn !== '' && ! $student) {
                $rowErrors[] = 'NISN tidak ditemukan.';
                $result['summary']['students_not_found']++;
            } elseif ($student) {
                $result['summary']['students_found']++;
                if ($identity['name'] !== '' && mb_strtolower($identity['name']) !== mb_strtolower(trim($student->name))) {
                    $rowWarnings[] = 'Nama pada template berbeda dari data BIDUK. Siswa tetap dicocokkan berdasarkan NISN.';
                }
                if (! $enrolledStudentIds->has($student->id)) {
                    $rowErrors[] = 'Siswa tidak terdaftar pada kelas dan tahun ajaran yang dipilih.';
                }
            }

            $recordAttributes = $subjects
                ->mapWithKeys(fn ($subject) => [
                    $subject->id => array_merge(
                        array_fill_keys(array_column(self::SUBJECT_FIELDS, 2), null),
                        [
                            'skill_score' => null,
                            'skill_predicate' => null,
                            'skill_learning_outcome' => null,
                        ]
                    ),
                ])
                ->all();
            foreach ($columns as $column) {
                $value = $this->cellValue($sheet, $column['column'], $rowNumber);
                if (! $this->hasValue($value)) {
                    continue;
                }

                $result['summary']['valid_values']++;
                $normalizedValue = $this->validateValue($value, $column, $rowErrors);
                if ($normalizedValue === false) {
                    $result['summary']['valid_values']--;
                    $result['summary']['invalid_values']++;

                    continue;
                }

                $recordAttributes[$column['subject_id']][$column['attribute']] = $normalizedValue;
            }

            $rowPreview = [
                'row' => $rowNumber,
                'nisn' => $nisn,
                'name' => $identity['name'],
                'matched_name' => $student?->name,
                'class_name' => $class->name,
                'grades' => $subjects
                    ->mapWithKeys(fn ($subject) => [
                        $subject->name => $recordAttributes[$subject->id] ?? [],
                    ])
                    ->all(),
                'warnings' => array_values(array_unique($rowWarnings)),
                'errors' => array_values(array_unique($rowErrors)),
            ];

            if ($rowErrors) {
                $result['rows'][] = $rowPreview;

                continue;
            }

            $result['summary']['valid_students']++;
            foreach ($recordAttributes as $subjectId => $attributes) {
                $identityKeys = [
                    'student_id' => $student->id,
                    'class_id' => $class->id,
                    'subject_id' => $subjectId,
                    'semester_id' => $semester->id,
                ];
                $hasValues = collect($attributes)->contains(
                    fn ($value) => $value !== null && $value !== ''
                );
                $existingRecord = ReportCardGrade::where($identityKeys)->exists();
                if (! $hasValues && ! $existingRecord) {
                    continue;
                }

                $result['records'][] = $identityKeys + ['attributes' => $attributes];
                $result['summary'][$existingRecord ? 'updated_records' : 'new_records']++;
            }

            $result['rows'][] = $rowPreview;
        }

        $result['can_import'] = count($result['records']) > 0;

        return $result;
    }

    public function import(array $preview): int
    {
        abort_unless(
            $preview['can_import'] && $preview['records'],
            422,
            'Tidak ada data nilai rapor valid untuk diimpor.'
        );

        DB::transaction(function () use ($preview) {
            foreach ($preview['records'] as $record) {
                ReportCardGrade::updateOrCreate(
                    [
                        'student_id' => $record['student_id'],
                        'class_id' => $record['class_id'],
                        'subject_id' => $record['subject_id'],
                        'semester_id' => $record['semester_id'],
                    ],
                    $record['attributes']
                );
            }
        });

        return count($preview['records']);
    }

    private function emptyPreview(): array
    {
        return [
            'metadata' => ['academic_year' => '', 'class' => '', 'semester' => ''],
            'academic_year_id' => null,
            'summary' => [
                'read_rows' => 0,
                'students_found' => 0,
                'students_not_found' => 0,
                'valid_students' => 0,
                'valid_values' => 0,
                'invalid_values' => 0,
                'new_records' => 0,
                'updated_records' => 0,
            ],
            'global_errors' => [],
            'rows' => [],
            'records' => [],
            'can_import' => false,
        ];
    }

    private function validateStructure(
        Worksheet $sheet,
        Collection $subjects,
        AcademicYear $academicYear,
        SchoolClass $class,
        Semester $semester,
        array &$errors
    ): array {
        if ($this->cellText($sheet, 'B', 7) === 'NIS' && $this->cellText($sheet, 'C', 7) === 'NISN') {
            $errors[] = 'File menggunakan template lama yang masih memiliki kolom NIS. Download ulang template import terbaru (No, NISN, Nama), lalu salin data nilai ke template tersebut.';

            return [];
        }

        foreach ([
            'A3' => 'Tahun Ajaran',
            'A4' => 'Kelas',
            'A5' => 'Semester',
            'A7' => 'No',
            'B7' => 'NISN',
            'C7' => 'Nama',
        ] as $cell => $expected) {
            if ($this->cellText($sheet, substr($cell, 0, 1), (int) substr($cell, 1)) !== $expected) {
                $errors[] = 'Kolom/label wajib "'.$expected.'" hilang atau berubah.';
            }
        }

        foreach ([
            'B3' => $academicYear->name,
            'B4' => 'Kelas '.$class->name,
            'B5' => $semester->name,
        ] as $cell => $expected) {
            if ($this->cellText($sheet, substr($cell, 0, 1), (int) substr($cell, 1)) !== $expected) {
                $errors[] = 'Metadata '.$cell.' tidak sesuai dengan Tahun Ajaran, Kelas, dan Semester yang dipilih pada form.';
            }
        }

        $columns = [];

        foreach ($subjects as $subjectIndex => $subject) {
            $startColumnIndex = self::FIRST_SUBJECT_COLUMN + ($subjectIndex * count(self::SUBJECT_FIELDS));
            $subjectColumn = Coordinate::stringFromColumnIndex($startColumnIndex);
            $subjectHeader = trim($this->cellText($sheet, $subjectColumn, 7));

            if ($this->normalizeName($subjectHeader) !== $this->normalizeName($subject->name)) {
                $errors[] = 'Mata pelajaran pada kolom '.$subjectColumn.'7 harus "'.$subject->name.'", sesuai mata pelajaran yang ditetapkan pada kelas '.$class->name.'.';

                continue;
            }

            foreach (self::SUBJECT_FIELDS as $offset => [$aspect, $field, $attribute]) {
                $columnIndex = $startColumnIndex + $offset;
                $columnName = Coordinate::stringFromColumnIndex($columnIndex);
                $aspectHeader = trim($this->cellText($sheet, $subjectColumn, 8));
                $fieldHeader = trim($this->cellText($sheet, $columnName, 9));

                if ($aspectHeader !== $aspect || $fieldHeader !== $field) {
                    $errors[] = 'Kolom wajib "'.$subject->name.' / '.$aspect.' / '.$field.'" hilang atau berubah.';

                    continue;
                }

                $columns[] = [
                    'column' => $columnName,
                    'subject' => $subject->name,
                    'subject_id' => $subject->id,
                    'aspect' => $aspect,
                    'field' => $field,
                    'attribute' => $attribute,
                ];
            }
        }

        $lastExpectedColumn = self::FIRST_SUBJECT_COLUMN
            + ($subjects->count() * count(self::SUBJECT_FIELDS))
            - 1;
        foreach ($sheet->getCellCollection()->getCoordinates() as $coordinate) {
            [$column, $row] = Coordinate::coordinateFromString($coordinate);
            if (
                Coordinate::columnIndexFromString($column) > $lastExpectedColumn
                && $this->hasValue($this->cellValue($sheet, $column, (int) $row))
            ) {
                $errors[] = 'Template memuat kolom tambahan di luar mata pelajaran yang ditetapkan untuk kelas '.$class->name.'. Download ulang template untuk kelas terpilih.';

                break;
            }
        }

        return $columns;
    }

    private function subjectsForClass(SchoolClass $class): Collection
    {
        return $class->subjects()
            ->where('subjects.status', 'Aktif')
            ->orderBy('subjects.code')
            ->orderBy('subjects.id')
            ->get();
    }

    private function validateValue(mixed $value, array $column, array &$errors): mixed
    {
        if ($value instanceof Cell && $value->getDataType() === 'f') {
            $errors[] = $this->valueError($column, 'tidak boleh menggunakan rumus Excel.');

            return false;
        }

        if (in_array($column['field'], ['Nilai', 'Nilai Akhir'], true)) {
            if (! is_numeric($value) || (float) $value < 0 || (float) $value > 100) {
                $errors[] = $this->valueError($column, 'harus berupa angka 0–100.');

                return false;
            }

            return round((float) $value, 2);
        }

        if ($column['field'] === 'Predikat') {
            $predicate = strtoupper(trim((string) $value));
            if (! in_array($predicate, ['A', 'B', 'C', 'D'], true)) {
                $errors[] = $this->valueError($column, 'harus A, B, C, atau D.');

                return false;
            }

            return $predicate;
        }

        $outcome = trim($this->valueText($value));
        if (mb_strlen($outcome) > 2000) {
            $errors[] = $this->valueError($column, 'maksimal 2.000 karakter.');

            return false;
        }

        return $outcome;
    }

    private function valueError(array $column, string $message): string
    {
        return 'Nilai '.$column['subject'].' ('.$column['aspect'].' - '.$column['field'].') '.$message;
    }

    private function resolveAcademicYear(array $context): AcademicYear
    {
        $academicYear = $context['academic_year'] ?? null;

        return $academicYear instanceof AcademicYear
            ? $academicYear
            : AcademicYear::findOrFail($context['academic_year_id']);
    }

    private function resolveClass(array $context, AcademicYear $academicYear): SchoolClass
    {
        $class = $context['class'] ?? null;
        $classId = $class instanceof SchoolClass ? $class->id : ($context['class_id'] ?? null);

        return SchoolClass::query()
            ->where('academic_year_id', $academicYear->id)
            ->findOrFail($classId);
    }

    private function resolveSemester(array $context, AcademicYear $academicYear): Semester
    {
        $semester = $context['semester'] ?? null;
        $semesterId = $semester instanceof Semester ? $semester->id : ($context['semester_id'] ?? null);

        return Semester::query()
            ->where('academic_year_id', $academicYear->id)
            ->findOrFail($semesterId);
    }

    private function isExampleRow(Worksheet $sheet, int $rowNumber, array $identity): bool
    {
        return mb_strtoupper(trim($this->cellText($sheet, 'A', $rowNumber))) === 'CONTOH'
            || mb_strtoupper($identity['name']) === 'CONTOH SISWA'
            || $identity['nisn'] === '0000000000';
    }

    private function cellValue(Worksheet $sheet, string $column, int $row): mixed
    {
        $cell = $sheet->getCellCollection()->get($column.$row);
        if (! $cell) {
            return null;
        }

        if ($cell->getDataType() === 'f') {
            return $cell;
        }

        $value = $cell->getValue();

        return $value instanceof RichText ? $value->getPlainText() : $value;
    }

    private function cellText(Worksheet $sheet, string $column, int $row): string
    {
        return $this->valueText($this->cellValue($sheet, $column, $row));
    }

    private function valueText(mixed $value): string
    {
        if ($value instanceof Cell) {
            $value = $value->getValue();
        }
        if ($value instanceof RichText) {
            return $value->getPlainText();
        }

        return is_scalar($value) ? (string) $value : '';
    }

    private function hasValue(mixed $value): bool
    {
        return $value instanceof Cell
            || (is_scalar($value) && trim((string) $value) !== '');
    }

    private function normalizeName(string $name): string
    {
        return mb_strtolower(trim((string) preg_replace('/\s+/', ' ', $name)));
    }
}
