<?php

namespace Tests\Feature;

use App\Http\Controllers\ReportCardGradeController;
use App\Http\Controllers\ReportCardGradeImportController;
use App\Models\AcademicYear;
use App\Models\ClassStudent;
use App\Models\Employee;
use App\Models\ReportCardGrade;
use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use App\Services\ReportCardGradeImportContext;
use App\Services\ReportCardGradeImportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ReportCardGradeImportTest extends TestCase
{
    use RefreshDatabase;

    private array $temporaryWorkbooks = [];

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        foreach ($this->temporaryWorkbooks as $temporaryWorkbook) {
            if (is_file($temporaryWorkbook)) {
                unlink($temporaryWorkbook);
            }
        }

        parent::tearDown();
    }

    public function test_repeat_import_updates_existing_grade_and_clears_omitted_values(): void
    {
        [$year, $class, $student, $subject, $odd, $even] = $this->createRoster();
        $importer = app(ReportCardGradeImportService::class);

        $firstFile = $this->makeWorkbook($student, $year, $class, $odd, [
            'D11' => 80,
            'E11' => 'B',
            'F11' => 'Memahami konsep matematika.',
        ]);
        $firstPreview = $this->previewFile($importer, $firstFile, $year, $class, $odd);

        $this->assertSame([], $firstPreview['global_errors']);
        $this->assertSame(1, $firstPreview['summary']['read_rows']);
        $this->assertSame(1, $firstPreview['summary']['new_records']);
        $this->assertSame(1, $importer->import($firstPreview));
        $this->assertSame(1, $importer->import($firstPreview));
        $this->assertSame(1, ReportCardGrade::where('student_id', $student->id)->count());

        $grade = ReportCardGrade::where([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $odd->id,
        ])->firstOrFail();

        $secondFile = $this->makeWorkbook($student, $year, $class, $odd, ['D11' => 90]);
        $secondPreview = $this->previewFile($importer, $secondFile, $year, $class, $odd);
        $this->assertSame(1, $secondPreview['summary']['updated_records']);
        $this->assertSame(0, $secondPreview['summary']['new_records']);
        $this->assertSame(1, $importer->import($secondPreview));

        $grade->refresh();
        $this->assertSame('90.00', $grade->score);
        $this->assertNull($grade->knowledge_predicate);
        $this->assertNull($grade->learning_outcome);
        $this->assertNull($grade->skill_score);
        $this->assertNull($grade->skill_predicate);
        $this->assertNull($grade->skill_learning_outcome);

        $genapFile = $this->makeWorkbook($student, $year, $class, $even, ['D11' => 72]);
        $genapPreview = $this->previewFile($importer, $genapFile, $year, $class, $even);
        $this->assertSame(1, $genapPreview['summary']['new_records']);
        $importer->import($genapPreview);
        $this->assertSame(2, ReportCardGrade::where('student_id', $student->id)->count());

        $nextYear = AcademicYear::create([
            'name' => '2027/2028',
            'start_date' => '2027-07-01',
            'end_date' => '2028-06-30',
            'status' => 'inactive',
        ]);
        $nextYearClass = SchoolClass::create([
            'academic_year_id' => $nextYear->id,
            'name' => '1A',
            'grade_level' => 1,
            'status' => 'Aktif',
        ]);
        Semester::create([
            'academic_year_id' => $nextYear->id,
            'name' => 'Ganjil',
        ]);
        ClassStudent::create([
            'class_id' => $nextYearClass->id,
            'student_id' => $student->id,
            'status' => 'Naik Kelas',
        ]);
        $nextYearClass->subjects()->attach($subject->id);
        $newYearSemester = Semester::where('academic_year_id', $nextYear->id)->firstOrFail();
        $newYearFile = $this->makeWorkbook($student, $nextYear, $nextYearClass, $newYearSemester, ['D11' => 70]);
        $newYearPreview = $this->previewFile($importer, $newYearFile, $nextYear, $nextYearClass, $newYearSemester);
        $this->assertSame(1, $newYearPreview['summary']['new_records']);
        $importer->import($newYearPreview);
        $this->assertSame(3, ReportCardGrade::where('student_id', $student->id)->count());
    }

    public function test_partial_report_card_scores_are_saved_but_semester_status_stays_incomplete(): void
    {
        [$year, $class, $student, $subject, $odd, $even] = $this->createRoster();
        $secondSubject = Subject::create([
            'code' => 'IND-IMPORT-STATUS',
            'name' => 'Bahasa Indonesia',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
        $class->subjects()->attach($secondSubject->id);

        $admin = User::create([
            'name' => 'Admin Status',
            'username' => 'admin-report-card-status',
            'email' => 'admin-report-card-status@example.test',
            'password' => 'password',
            'role_id' => Role::create(['name' => 'Super Admin'])->id,
        ]);
        $this->actingAs($admin);

        $this->put(route('report-card-grades.update', [$class, $student]), [
            'ganjil_scores' => [$subject->id => 85],
            'genap_scores' => [$subject->id => 82],
        ])->assertRedirect();

        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $odd->id,
            'score' => 85,
            'skill_score' => null,
        ]);
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $even->id,
            'score' => 82,
            'skill_score' => null,
        ]);

        $this->get(route('report-card-grades.index', [
            'academic_year_id' => $year->id,
            'class_id' => $class->id,
        ]))
            ->assertOk()
            ->assertSeeText('Belum Diisi')
            ->assertDontSeeText('Sudah Diisi');

        $this->get(route('report-card-grades.history', $student))
            ->assertOk()
            ->assertSeeText('Belum Diisi')
            ->assertDontSeeText('Sudah Diisi');

        $this->put(route('report-card-grades.update', [$class, $student]), [
            'ganjil_scores' => [$subject->id => 85, $secondSubject->id => 90],
            'genap_scores' => [$subject->id => 82, $secondSubject->id => 88],
        ])->assertRedirect();

        $this->get(route('report-card-grades.index', [
            'academic_year_id' => $year->id,
            'class_id' => $class->id,
        ]))
            ->assertOk()
            ->assertSeeText('Sudah Diisi')
            ->assertDontSeeText('Belum Diisi');

        $this->get(route('report-card-grades.history', $student))
            ->assertOk()
            ->assertSeeText('Sudah Diisi')
            ->assertDontSeeText('Belum Diisi');
    }

    public function test_template_predicate_dropdown_values_are_imported_into_report_card_grades(): void
    {
        [$year, $class, $student, $subject, $odd] = $this->createRoster();
        $spreadsheet = app(ReportCardGradeImportService::class)->createTemplate([
            'academic_year' => $year,
            'class' => $class,
            'semester' => $odd,
        ]);
        $sheet = $spreadsheet->getActiveSheet();

        foreach (['E11'] as $cell) {
            $validation = $sheet->getCell($cell)->getDataValidation();
            $this->assertSame(DataValidation::TYPE_LIST, $validation->getType());
            $this->assertSame('"A,B,C,D"', $validation->getFormula1());
        }

        $sheet->setCellValue('D11', 85);
        $sheet->setCellValue('E11', 'B');
        $sheet->setCellValue('F11', 'Capaian matematika.');
        $temporaryWorkbook = tempnam(sys_get_temp_dir(), 'biduk-import-');
        $this->temporaryWorkbooks[] = $temporaryWorkbook;
        (new Xlsx($spreadsheet))->save($temporaryWorkbook);
        $downloadedSheet = IOFactory::load($temporaryWorkbook)->getActiveSheet();
        foreach (['E11'] as $cell) {
            $validation = $downloadedSheet->getCell($cell)->getDataValidation();
            $this->assertSame(DataValidation::TYPE_LIST, $validation->getType());
            $this->assertSame('"A,B,C,D"', $validation->getFormula1());
        }

        $importer = app(ReportCardGradeImportService::class);
        $preview = $this->previewFile($importer, $temporaryWorkbook, $year, $class, $odd);

        $this->assertTrue($preview['can_import']);
        $this->assertSame([], $preview['rows'][0]['errors']);
        $this->assertSame(1, $importer->import($preview));
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $odd->id,
            'score' => 85,
            'knowledge_predicate' => 'B',
            'learning_outcome' => 'Capaian matematika.',
            'skill_score' => null,
            'skill_predicate' => null,
        ]);
    }

    public function test_preview_and_import_keep_distinct_excel_scores_for_each_student_and_subject(): void
    {
        [$year, $class, $firstStudent, $math, $odd] = $this->createRoster();
        $secondStudent = Student::create([
            'nis' => 'NIS-SECOND-SCORE',
            'nisn' => '0012345682',
            'name' => 'Siswa Nilai Kedua',
            'gender' => 'P',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $class->id,
            'student_id' => $secondStudent->id,
            'status' => 'Aktif',
        ]);
        $language = Subject::create([
            'code' => 'IND-SECOND-SCORE',
            'name' => 'Bahasa Indonesia',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
        $class->subjects()->attach($language->id);

        $spreadsheet = app(ReportCardGradeImportService::class)->createTemplate([
            'academic_year' => $year,
            'class' => $class,
            'semester' => $odd,
        ]);
        $sheet = $spreadsheet->getActiveSheet();
        $subjectScoreColumns = [];
        for ($index = 0; $index < 2; $index++) {
            $column = Coordinate::stringFromColumnIndex(4 + ($index * 3));
            $subjectScoreColumns[$sheet->getCell($column.'7')->getValue()] = $column;
        }
        $sheet->setCellValue($subjectScoreColumns['Matematika'].'11', 81);
        $sheet->setCellValue($subjectScoreColumns['Bahasa Indonesia'].'11', 92);
        $sheet->setCellValue($subjectScoreColumns['Matematika'].'12', 67);
        $sheet->setCellValue($subjectScoreColumns['Bahasa Indonesia'].'12', 75);

        $temporaryWorkbook = tempnam(sys_get_temp_dir(), 'biduk-import-');
        $this->temporaryWorkbooks[] = $temporaryWorkbook;
        (new Xlsx($spreadsheet))->save($temporaryWorkbook);

        $importer = app(ReportCardGradeImportService::class);
        $preview = $this->previewFile($importer, $temporaryWorkbook, $year, $class, $odd);

        $this->assertTrue($preview['can_import']);
        $previewByNisn = collect($preview['rows'])->keyBy('nisn');
        $this->assertSame(81.0, $previewByNisn[$firstStudent->nisn]['grades']['Matematika']['score']);
        $this->assertSame(92.0, $previewByNisn[$firstStudent->nisn]['grades']['Bahasa Indonesia']['score']);
        $this->assertSame(67.0, $previewByNisn[$secondStudent->nisn]['grades']['Matematika']['score']);
        $this->assertSame(75.0, $previewByNisn[$secondStudent->nisn]['grades']['Bahasa Indonesia']['score']);

        $this->assertSame(4, $importer->import($preview));
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $firstStudent->id,
            'class_id' => $class->id,
            'subject_id' => $math->id,
            'semester_id' => $odd->id,
            'score' => 81,
        ]);
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $firstStudent->id,
            'class_id' => $class->id,
            'subject_id' => $language->id,
            'semester_id' => $odd->id,
            'score' => 92,
        ]);
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $secondStudent->id,
            'class_id' => $class->id,
            'subject_id' => $math->id,
            'semester_id' => $odd->id,
            'score' => 67,
        ]);
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $secondStudent->id,
            'class_id' => $class->id,
            'subject_id' => $language->id,
            'semester_id' => $odd->id,
            'score' => 75,
        ]);
    }

    public function test_import_confirmation_saves_each_template_subject_and_field_to_the_selected_class(): void
    {
        [$year, $class, $student, $math, $odd] = $this->createRoster();
        $language = Subject::create([
            'code' => 'IND-CONFIRM',
            'name' => 'Bahasa Indonesia',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
        $class->subjects()->attach($language->id);
        $otherClass = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => '1B',
            'grade_level' => 1,
            'status' => 'Aktif',
        ]);
        $otherSubject = Subject::create([
            'code' => 'IPA-OTHER-CLASS',
            'name' => 'Ilmu Pengetahuan Alam',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
        $otherClass->subjects()->attach($otherSubject->id);
        $admin = User::create([
            'name' => 'Admin Confirm Import',
            'username' => 'admin-confirm-import',
            'email' => 'admin-confirm-import@example.test',
            'password' => 'password',
            'role_id' => Role::create(['name' => 'Super Admin'])->id,
        ]);
        $this->actingAs($admin);
        Storage::fake('local');

        $spreadsheet = app(ReportCardGradeImportService::class)->createTemplate([
            'academic_year' => $year,
            'class' => $class,
            'semester' => $odd,
        ]);
        $sheet = $spreadsheet->getSheetByName('IMPORT NILAI');
        $subjectColumns = [];
        foreach ($class->subjects()
            ->where('subjects.status', 'Aktif')
            ->orderBy('subjects.sort_order')
            ->orderBy('subjects.name')
            ->get() as $index => $subject) {
            $startIndex = 4 + ($index * 3);
            $subjectColumns[$subject->id] = [];
            foreach (range(0, 2) as $offset) {
                $subjectColumns[$subject->id][] = Coordinate::stringFromColumnIndex($startIndex + $offset);
            }
            $sheet->setCellValue($subjectColumns[$subject->id][0].'11', $subject->id === $math->id ? 81.5 : 92.25);
            $sheet->setCellValue($subjectColumns[$subject->id][1].'11', $subject->id === $math->id ? 'B' : 'A');
            $sheet->setCellValue($subjectColumns[$subject->id][2].'11', 'Capaian pembelajaran '.$subject->name);
        }

        $temporaryWorkbook = tempnam(sys_get_temp_dir(), 'biduk-import-confirm-');
        $this->temporaryWorkbooks[] = $temporaryWorkbook;
        (new Xlsx($spreadsheet))->save($temporaryWorkbook);

        $previewResponse = $this->post(route('report-card-grades.import.preview'), [
            'academic_year_id' => $year->id,
            'class_id' => $class->id,
            'semester_id' => $odd->id,
            'file' => UploadedFile::fake()->createWithContent(
                'nilai.xlsx',
                file_get_contents($temporaryWorkbook)
            ),
        ]);
        $previewResponse->assertOk();
        $preview = $previewResponse->viewData('preview');
        $this->assertSame('Kelas '.$class->name, $preview['metadata']['class']);
        $this->assertSame($odd->name, $preview['metadata']['semester']);
        $this->assertSame($student->nisn, $preview['rows'][0]['nisn']);
        $this->assertSame(81.5, $preview['rows'][0]['grades']['Matematika']['score']);
        $this->assertSame('B', $preview['rows'][0]['grades']['Matematika']['knowledge_predicate']);
        $this->assertSame('Capaian pembelajaran Matematika', $preview['rows'][0]['grades']['Matematika']['learning_outcome']);

        $this->post(route('report-card-grades.import.confirm'), [
            'token' => $previewResponse->viewData('token'),
        ])->assertRedirect()
            ->assertSessionHas('success');

        foreach ([
            [$math, [81.5, 'B', 'Capaian pembelajaran Matematika']],
            [$language, [92.25, 'A', 'Capaian pembelajaran Bahasa Indonesia']],
        ] as [$subject, $values]) {
            $this->assertDatabaseHas('report_card_grades', [
                'student_id' => $student->id,
                'class_id' => $class->id,
                'subject_id' => $subject->id,
                'semester_id' => $odd->id,
                'score' => $values[0],
                'knowledge_predicate' => $values[1],
                'learning_outcome' => $values[2],
                'skill_score' => null,
                'skill_predicate' => null,
                'skill_learning_outcome' => null,
            ]);
        }

        $this->assertDatabaseMissing('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $otherClass->id,
            'subject_id' => $otherSubject->id,
            'semester_id' => $odd->id,
        ]);
    }

    public function test_unknown_nisn_and_invalid_scores_are_reported_without_creating_students(): void
    {
        [$year, $class, , , $odd] = $this->createRoster();
        $file = $this->makeWorkbook(null, $year, $class, $odd, [
            'B11' => '9999999999',
            'C11' => 'Siswa Tidak Ada',
            'D11' => '85/100',
            'E11' => 'E',
        ]);

        $preview = $this->previewFile(app(ReportCardGradeImportService::class), $file, $year, $class, $odd);

        $this->assertSame(1, $preview['summary']['students_not_found']);
        $this->assertSame(0, $preview['summary']['valid_students']);
        $this->assertFalse($preview['can_import']);
        $this->assertContains('NISN tidak ditemukan.', $preview['rows'][0]['errors']);
        $this->assertSame(1, Student::count());
    }

    public function test_template_contains_only_students_assigned_to_the_selected_class_and_year(): void
    {
        [$year, $class, $student, $subject, $odd] = $this->createRoster();
        $secondStudent = Student::create([
            'nis' => 'NIS-SECOND',
            'nisn' => '0012345679',
            'name' => 'Siswa Kedua',
            'gender' => 'P',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $class->id,
            'student_id' => $secondStudent->id,
            'status' => 'Aktif',
        ]);

        $otherClass = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => '1B',
            'grade_level' => 1,
            'status' => 'Aktif',
        ]);
        $otherClassStudent = Student::create([
            'nis' => 'NIS-OTHER-CLASS',
            'nisn' => '0012345680',
            'name' => 'Siswa Kelas Lain',
            'gender' => 'L',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $otherClass->id,
            'student_id' => $otherClassStudent->id,
            'status' => 'Aktif',
        ]);

        $otherYear = AcademicYear::create([
            'name' => '2027/2028',
            'start_date' => '2027-07-01',
            'end_date' => '2028-06-30',
            'status' => 'inactive',
        ]);
        $otherYearClass = SchoolClass::create([
            'academic_year_id' => $otherYear->id,
            'name' => '1A',
            'grade_level' => 1,
            'status' => 'Aktif',
        ]);
        $otherYearStudent = Student::create([
            'nis' => 'NIS-OTHER-YEAR',
            'nisn' => '0012345681',
            'name' => 'Siswa Tahun Lain',
            'gender' => 'P',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $otherYearClass->id,
            'student_id' => $otherYearStudent->id,
            'status' => 'Aktif',
        ]);

        $spreadsheet = app(ReportCardGradeImportService::class)->createTemplate([
            'academic_year' => $year,
            'class' => $class,
            'semester' => $odd,
        ]);
        $sheet = $spreadsheet->getActiveSheet();

        $this->assertSame('1', (string) $sheet->getCell('A11')->getValue());
        $this->assertSame($student->nisn, (string) $sheet->getCell('B11')->getValue());
        $this->assertSame($student->name, $sheet->getCell('C11')->getValue());
        $this->assertSame('2', (string) $sheet->getCell('A12')->getValue());
        $this->assertSame($secondStudent->nisn, (string) $sheet->getCell('B12')->getValue());
        $this->assertSame($secondStudent->name, $sheet->getCell('C12')->getValue());
        $this->assertNull($sheet->getCell('B13')->getValue());
        $this->assertSame($subject->name, $sheet->getCell('D7')->getValue());
        $this->assertNull($sheet->getCell('D11')->getValue());

        $sheet->setCellValue('D11', 85);
        $temporaryWorkbook = tempnam(sys_get_temp_dir(), 'biduk-import-');
        $this->temporaryWorkbooks[] = $temporaryWorkbook;
        (new Xlsx($spreadsheet))->save($temporaryWorkbook);
        $preview = app(ReportCardGradeImportService::class)->preview($temporaryWorkbook, [
            'academic_year' => $year,
            'class' => $class,
            'semester' => $odd,
        ]);
        $this->assertTrue($preview['can_import']);
        $this->assertSame(1, $preview['summary']['new_records']);
    }

    public function test_import_rejects_student_without_assignment_to_the_selected_class(): void
    {
        [$year, $class, $student, $subject, $odd] = $this->createRoster();
        $selectedClass = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => '1B',
            'grade_level' => 1,
            'status' => 'Aktif',
        ]);
        $selectedClass->subjects()->attach($subject->id);
        ClassStudent::where('student_id', $student->id)->delete();

        $file = $this->makeWorkbook($student, $year, $selectedClass, $odd, ['W11' => 86]);
        $importer = app(ReportCardGradeImportService::class);
        $preview = $this->previewFile($importer, $file, $year, $selectedClass, $odd);

        $this->assertFalse($preview['can_import']);
        $this->assertContains(
            'Siswa tidak terdaftar pada kelas dan tahun ajaran yang dipilih.',
            $preview['rows'][0]['errors']
        );
        $this->assertSame(0, ClassStudent::where('student_id', $student->id)->count());
        $this->assertSame(0, ReportCardGrade::where('student_id', $student->id)->count());
    }

    public function test_zero_score_is_valid_and_values_follow_dynamic_class_subject_columns(): void
    {
        [$year, $class, $student, $math, $odd] = $this->createRoster();
        $religion = Subject::create([
            'code' => 'PAI-IMPORT',
            'name' => 'Pendidikan Agama dan Budi Pekerti',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
        $class->subjects()->attach($religion->id);
        $file = $this->makeWorkbook($student, $year, $class, $odd, [
            'D11' => 0,
            'G11' => 89,
        ]);

        $importer = app(ReportCardGradeImportService::class);
        $preview = $this->previewFile($importer, $file, $year, $class, $odd);
        $this->assertTrue($preview['can_import']);
        $this->assertSame(2, $preview['summary']['new_records']);
        $importer->import($preview);

        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $math->id,
            'semester_id' => $odd->id,
            'score' => 0,
        ]);
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $religion->id,
            'semester_id' => $odd->id,
            'score' => 89,
        ]);
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $religion->id,
            'semester_id' => $odd->id,
            'score' => 89,
        ]);
    }

    public function test_teacher_import_options_and_validation_are_limited_to_active_homeroom_class(): void
    {
        [$year, $class, , , $odd] = $this->createRoster();
        $teacherRole = Role::create(['name' => 'Guru']);
        $employee = Employee::create([
            'name' => 'Guru Wali',
            'gender' => 'P',
            'employee_type' => 'Guru',
            'status' => 'Aktif',
        ]);
        $otherEmployee = Employee::create([
            'name' => 'Guru Lain',
            'gender' => 'L',
            'employee_type' => 'Guru',
            'status' => 'Aktif',
        ]);
        $user = User::create([
            'name' => 'Guru Wali',
            'username' => 'guru-wali-import',
            'email' => 'guru-wali@example.test',
            'password' => 'password',
            'employee_id' => $employee->id,
            'role_id' => $teacherRole->id,
        ]);
        $class->update(['homeroom_teacher_id' => $employee->id]);
        $otherClass = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => '1B',
            'grade_level' => 1,
            'homeroom_teacher_id' => $otherEmployee->id,
            'status' => 'Aktif',
        ]);

        $context = app(ReportCardGradeImportContext::class);
        $options = $context->options($user);
        $this->assertSame([$year->id], $options['academic_years']->pluck('id')->all());
        $this->assertSame([$class->id], $options['classes']->pluck('id')->all());

        $this->assertSame(
            $class->id,
            $context->validateSelection($user, [
                'academic_year_id' => $year->id,
                'class_id' => $class->id,
                'semester_id' => $odd->id,
            ])['class']->id
        );
        $this->expectException(ValidationException::class);
        $context->validateSelection($user, [
            'academic_year_id' => $year->id,
            'class_id' => $otherClass->id,
            'semester_id' => $odd->id,
        ]);
    }

    public function test_invalid_numeric_scores_and_predicates_are_rejected_in_preview(): void
    {
        [$year, $class, $student, , $odd] = $this->createRoster();
        $file = $this->makeWorkbook($student, $year, $class, $odd, [
            'D11' => '85/100',
            'E11' => 'E',
        ]);

        $preview = $this->previewFile(app(ReportCardGradeImportService::class), $file, $year, $class, $odd);

        $this->assertSame(2, $preview['summary']['invalid_values']);
        $this->assertFalse($preview['can_import']);
        $this->assertTrue(collect($preview['rows'][0]['errors'])->contains(
            fn ($error) => str_contains($error, 'Nilai Matematika (Data Nilai - Nilai Akhir) harus berupa angka 0–100.')
        ));
        $this->assertTrue(collect($preview['rows'][0]['errors'])->contains(
            fn ($error) => str_contains($error, 'Nilai Matematika (Data Nilai - Predikat) harus A, B, C, atau D.')
        ));
    }

    public function test_template_example_row_is_ignored_and_name_mismatch_is_only_a_warning(): void
    {
        [$year, $class, $student, , $odd] = $this->createRoster();
        $file = $this->makeWorkbook($student, $year, $class, $odd, [
            'C11' => 'Nama Berbeda',
            'W11' => 75,
        ]);

        $preview = $this->previewFile(app(ReportCardGradeImportService::class), $file, $year, $class, $odd);

        $this->assertSame(1, $preview['summary']['read_rows']);
        $this->assertSame(1, $preview['summary']['valid_students']);
        $this->assertCount(1, $preview['rows'][0]['warnings']);
        $this->assertSame($student->name, $preview['rows'][0]['matched_name']);
        $this->assertSame(1, $preview['summary']['new_records']);
    }

    public function test_example_only_workbook_does_not_create_or_count_any_student_rows(): void
    {
        [$year, $class, , , $odd] = $this->createRoster();
        $file = $this->makeWorkbook(null, $year, $class, $odd, []);

        $preview = $this->previewFile(app(ReportCardGradeImportService::class), $file, $year, $class, $odd);

        $this->assertSame(0, $preview['summary']['read_rows']);
        $this->assertSame(0, $preview['summary']['students_found']);
        $this->assertFalse($preview['can_import']);
    }

    public function test_importing_blank_values_clears_stored_scores_and_keeps_semester_incomplete(): void
    {
        [$year, $class, $student, $subject, $odd] = $this->createRoster();
        ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $odd->id,
            'score' => 76,
            'skill_score' => 76,
        ]);
        $file = $this->makeWorkbook($student, $year, $class, $odd, []);
        $importer = app(ReportCardGradeImportService::class);
        $preview = $this->previewFile($importer, $file, $year, $class, $odd);

        $this->assertSame(1, $preview['summary']['read_rows']);
        $this->assertSame(1, $preview['summary']['students_found']);
        $this->assertSame(0, $preview['summary']['valid_values']);
        $this->assertTrue($preview['can_import']);
        $this->assertSame([], $preview['global_errors']);
        $this->assertSame(1, $importer->import($preview));
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $odd->id,
            'score' => null,
            'skill_score' => null,
        ]);

        $admin = User::create([
            'name' => 'Admin Blank Import',
            'username' => 'admin-blank-import',
            'email' => 'admin-blank-import@example.test',
            'password' => 'password',
            'role_id' => Role::create(['name' => 'Super Admin'])->id,
        ]);
        $this->actingAs($admin)
            ->get(route('report-card-grades.index', [
                'academic_year_id' => $year->id,
                'class_id' => $class->id,
            ]))
            ->assertOk()
            ->assertSeeText('Belum Diisi')
            ->assertDontSeeText('Sudah Diisi');
    }

    public function test_importing_blank_subject_columns_clears_values_for_other_subjects(): void
    {
        [$year, $class, $student, $math, $odd] = $this->createRoster();
        $language = Subject::create([
            'code' => 'IND-IMPORT',
            'name' => 'Bahasa Indonesia',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
        $class->subjects()->attach($language->id);
        $existingOtherGrade = ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $language->id,
            'semester_id' => $odd->id,
            'score' => 91,
            'learning_outcome' => 'Nilai bahasa tetap.',
        ]);
        $file = $this->makeWorkbook($student, $year, $class, $odd, ['W11' => 82]);

        $importer = app(ReportCardGradeImportService::class);
        $importer->import($this->previewFile($importer, $file, $year, $class, $odd));

        $this->assertDatabaseHas('report_card_grades', [
            'id' => $existingOtherGrade->id,
            'score' => null,
            'learning_outcome' => null,
        ]);
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $math->id,
            'semester_id' => $odd->id,
            'score' => 82,
        ]);
    }

    public function test_changed_required_header_blocks_import_with_specific_error(): void
    {
        [$year, $class, $student, , $odd] = $this->createRoster();
        $file = $this->makeWorkbook($student, $year, $class, $odd, [
            'W9' => 'Nilai Berubah',
            'W11' => 80,
        ]);

        $preview = $this->previewFile(app(ReportCardGradeImportService::class), $file, $year, $class, $odd);

        $this->assertFalse($preview['can_import']);
        $this->assertTrue(collect($preview['global_errors'])->contains(
            fn ($error) => str_contains($error, 'Matematika / Data Nilai / Nilai Akhir')
        ));
    }

    public function test_template_uses_nisn_without_an_nis_column(): void
    {
        [$year, $class, , , $odd] = $this->createRoster();
        $spreadsheet = app(ReportCardGradeImportService::class)->createTemplate([
            'academic_year' => $year,
            'class' => $class,
            'semester' => $odd,
        ]);
        $sheet = $spreadsheet->getSheetByName('IMPORT NILAI');

        $this->assertSame(['IMPORT NILAI'], $spreadsheet->getSheetNames());
        $this->assertSame('No', $this->cellText($sheet, 'A7'));
        $this->assertSame('NISN', $this->cellText($sheet, 'B7'));
        $this->assertSame('Nama', $this->cellText($sheet, 'C7'));
        $this->assertSame('Matematika', $this->cellText($sheet, 'D7'));
        $this->assertSame('Data Nilai', $this->cellText($sheet, 'D8'));
        $this->assertSame('Nilai Akhir', $this->cellText($sheet, 'D9'));
        $this->assertSame('Predikat', $this->cellText($sheet, 'E9'));
        $this->assertSame('Capaian Pembelajaran', $this->cellText($sheet, 'F9'));
        $this->assertNull($sheet->getCell('G7')->getValue());
    }

    public function test_template_subjects_are_dynamic_for_each_selected_class(): void
    {
        [$year, $class, , , $odd] = $this->createRoster();
        $science = Subject::create([
            'code' => 'PANC-IMPORT',
            'name' => 'Pendidikan Pancasila',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
        $class->subjects()->attach($science->id);

        $importer = app(ReportCardGradeImportService::class);
        $firstTemplate = $importer->createTemplate([
            'academic_year' => $year,
            'class' => $class,
            'semester' => $odd,
        ])->getActiveSheet();
        $this->assertSame('Matematika', $this->cellText($firstTemplate, 'D7'));
        $this->assertSame('Pendidikan Pancasila', $this->cellText($firstTemplate, 'G7'));

        $otherClass = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => '1B',
            'grade_level' => 1,
            'status' => 'Aktif',
        ]);
        $language = Subject::create([
            'code' => 'IND-OTHER-IMPORT',
            'name' => 'Bahasa Indonesia',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
        $otherClass->subjects()->attach($language->id);
        $secondTemplate = $importer->createTemplate([
            'academic_year' => $year,
            'class' => $otherClass,
            'semester' => $odd,
        ])->getActiveSheet();

        $this->assertSame('Bahasa Indonesia', $this->cellText($secondTemplate, 'D7'));
        $this->assertSame('', $this->cellText($secondTemplate, 'J7'));
    }

    public function test_template_generation_requires_active_subjects_assigned_to_the_class(): void
    {
        [$year, $class, , , $odd] = $this->createRoster();
        $class->subjects()->detach();

        try {
            app(ReportCardGradeImportService::class)->createTemplate([
                'academic_year' => $year,
                'class' => $class,
                'semester' => $odd,
            ]);
            $this->fail('A template must not be generated without class subjects.');
        } catch (ValidationException $exception) {
            $this->assertStringContainsString(
                'belum memiliki mata pelajaran aktif yang ditetapkan',
                $exception->validator->errors()->first('class_id')
            );
        }
    }

    public function test_download_endpoint_returns_template_for_selected_class_and_semester(): void
    {
        [$year, $class, , , $odd] = $this->createRoster();
        $role = Role::create(['name' => 'Admin']);
        $admin = User::create([
            'name' => 'Admin Download',
            'username' => 'admin-download-import',
            'email' => 'admin-download-import@example.test',
            'password' => 'password',
            'role_id' => $role->id,
        ]);
        $request = Request::create('/report-card-grades/import/template', 'GET', [
            'academic_year_id' => $year->id,
            'class_id' => $class->id,
            'semester_id' => $odd->id,
        ]);
        $request->setUserResolver(fn () => $admin);

        $response = app(ReportCardGradeImportController::class)->downloadTemplate(
            $request,
            app(ReportCardGradeImportContext::class),
            app(ReportCardGradeImportService::class)
        );
        $this->assertStringContainsString(
            '2026-2027-1a-ganjil.xlsx',
            $response->headers->get('Content-Disposition')
        );

        ob_start();
        ($response->getCallback())();
        $content = ob_get_clean();
        $temporaryWorkbook = tempnam(sys_get_temp_dir(), 'biduk-download-');
        $this->temporaryWorkbooks[] = $temporaryWorkbook;
        file_put_contents($temporaryWorkbook, $content);
        $sheet = $this->loadWorkbook($temporaryWorkbook)->getSheetByName('IMPORT NILAI');

        $this->assertSame('2026/2027', $this->cellText($sheet, 'B3'));
        $this->assertSame('Kelas 1A', $this->cellText($sheet, 'B4'));
        $this->assertSame('Ganjil', $this->cellText($sheet, 'B5'));
        $this->assertSame('Matematika', $this->cellText($sheet, 'D7'));
    }

    public function test_subject_header_not_assigned_to_class_is_rejected(): void
    {
        [$year, $class, $student, , $odd] = $this->createRoster();
        $file = $this->makeWorkbook($student, $year, $class, $odd, ['D11' => 80]);
        $spreadsheet = $this->loadWorkbook($file);
        $spreadsheet->getSheetByName('IMPORT NILAI')->setCellValue('D7', 'Bahasa Indonesia');
        (new Xlsx($spreadsheet))->save($file);

        $preview = $this->previewFile(app(ReportCardGradeImportService::class), $file, $year, $class, $odd);

        $this->assertFalse($preview['can_import']);
        $this->assertContains(
            'Mata pelajaran pada kolom D7 harus "Matematika", sesuai mata pelajaran yang ditetapkan pada kelas 1A.',
            $preview['global_errors']
        );
    }

    public function test_legacy_template_with_nis_column_gets_a_clear_update_message(): void
    {
        [$year, $class, $student, , $odd] = $this->createRoster();
        $file = $this->makeWorkbook($student, $year, $class, $odd, []);
        $spreadsheet = $this->loadWorkbook($file);
        $sheet = $spreadsheet->getSheetByName('IMPORT NILAI');
        $sheet->insertNewColumnBefore('B');
        $sheet->setCellValue('B7', 'NIS');
        $sheet->setCellValue('C7', 'NISN');
        $sheet->setCellValue('D7', 'Nama Siswa');
        (new Xlsx($spreadsheet))->save($file);

        $preview = $this->previewFile(app(ReportCardGradeImportService::class), $file, $year, $class, $odd);

        $this->assertContains(
            'File menggunakan template lama yang masih memiliki kolom NIS. Download ulang template import terbaru (No, NISN, Nama), lalu salin data nilai ke template tersebut.',
            $preview['global_errors']
        );
        $this->assertFalse($preview['can_import']);
    }

    public function test_missing_nisn_is_rejected_with_a_row_error(): void
    {
        [$year, $class, , , $odd] = $this->createRoster();
        $file = $this->makeWorkbook(null, $year, $class, $odd, [
            'C11' => 'Nama Tanpa NISN',
            'W11' => 80,
        ]);

        $preview = $this->previewFile(app(ReportCardGradeImportService::class), $file, $year, $class, $odd);

        $this->assertFalse($preview['can_import']);
        $this->assertContains('NISN wajib diisi untuk mencocokkan siswa.', $preview['rows'][0]['errors']);
    }

    public function test_import_rejects_template_with_non_intracurricular_sections(): void
    {
        [$year, $class, $student, , $odd] = $this->createRoster();
        $file = $this->makeWorkbook($student, $year, $class, $odd, [
            'D9' => 'Sosial',
            'CW7' => 'Ekstrakulikuler',
            'W11' => 80,
        ]);

        $preview = $this->previewFile(app(ReportCardGradeImportService::class), $file, $year, $class, $odd);

        $this->assertFalse($preview['can_import']);
        $this->assertContains(
            'Template memuat kolom tambahan di luar mata pelajaran yang ditetapkan untuk kelas 1A. Download ulang template untuk kelas terpilih.',
            $preview['global_errors']
        );
    }

    public function test_import_uses_one_final_score_and_ignores_legacy_skill_values(): void
    {
        [$year, $class, $student, $subject, $odd] = $this->createRoster();
        ReportCardGrade::create([
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $odd->id,
            'score' => 77,
            'skill_score' => 99,
            'skill_predicate' => 'A',
            'skill_learning_outcome' => 'Capaian keterampilan lama.',
        ]);
        $file = $this->makeWorkbook($student, $year, $class, $odd, [
            'D11' => 80.5,
            'E11' => 'B',
            'F11' => 'Capaian pembelajaran teruji.',
        ]);

        $importer = app(ReportCardGradeImportService::class);
        $preview = $this->previewFile($importer, $file, $year, $class, $odd);
        $this->assertSame([
            'score' => 80.5,
            'knowledge_predicate' => 'B',
            'learning_outcome' => 'Capaian pembelajaran teruji.',
            'skill_score' => null,
            'skill_predicate' => null,
            'skill_learning_outcome' => null,
        ], $preview['rows'][0]['grades']['Matematika']);
        $this->assertSame(1, $importer->import($preview));
        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'semester_id' => $odd->id,
            'score' => 80.5,
            'knowledge_predicate' => 'B',
            'learning_outcome' => 'Capaian pembelajaran teruji.',
            'skill_score' => null,
            'skill_predicate' => null,
            'skill_learning_outcome' => null,
        ]);

        $admin = User::create([
            'name' => 'Admin Import Print',
            'username' => 'admin-import-print',
            'email' => 'admin-import-print@example.test',
            'password' => 'password',
            'role_id' => Role::create(['name' => 'Super Admin'])->id,
        ]);
        $this->actingAs($admin)
            ->get(route('report-card-grades.edit', [$class, $student]))
            ->assertOk()
            ->assertSee('value="80.50"', false)
            ->assertSee('Nilai Akhir')
            ->assertSee('Capaian pembelajaran teruji.')
            ->assertDontSee('Keterampilan');

        $view = app(ReportCardGradeController::class)->printHistory(
            Request::create('/rapot', 'GET', ['academic_year_id' => $year->id]),
            $class,
            $student
        );

        $html = $view->render();
        $this->assertStringContainsString('<th>NILAI AKHIR</th>', $html);
        $this->assertStringContainsString('<th>CAPAIAN PEMBELAJARAN</th>', $html);
        $this->assertStringContainsString('>80.50</td>', $html);
        $this->assertStringContainsString('Capaian pembelajaran teruji.', $html);
        $this->assertStringNotContainsString('Capaian keterampilan lama.', $html);
        $this->assertStringNotContainsString('NILAI PENGETAHUAN / KETERAMPILAN', $html);
    }

    public function test_final_score_is_the_only_grade_value_in_grade_six_report_print(): void
    {
        [$year, $class, $student, , $odd] = $this->createRoster(6);
        $file = $this->makeWorkbook($student, $year, $class, $odd, [
            'D11' => 80,
            'E11' => 'B',
            'F11' => 'Capaian pembelajaran teruji.',
        ]);

        $importer = app(ReportCardGradeImportService::class);
        $importer->import($this->previewFile($importer, $file, $year, $class, $odd));

        $view = app(ReportCardGradeController::class)->printHistory(
            Request::create('/rapot', 'GET', ['academic_year_id' => $year->id]),
            $class,
            $student
        );

        $html = $view->render();
        $this->assertStringContainsString('<th>NILAI AKHIR</th>', $html);
        $this->assertStringContainsString('Capaian pembelajaran teruji.', $html);
        $this->assertStringNotContainsString('Keterampilan', $html);
        $this->assertStringContainsString('>80.00</td>', $html);
    }

    private function createRoster(int $gradeLevel = 1): array
    {
        $year = AcademicYear::create([
            'name' => '2026/2027',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
            'status' => 'active',
        ]);
        $odd = Semester::create([
            'academic_year_id' => $year->id,
            'name' => 'Ganjil',
        ]);
        $even = Semester::create([
            'academic_year_id' => $year->id,
            'name' => 'Genap',
        ]);
        $class = SchoolClass::create([
            'academic_year_id' => $year->id,
            'name' => $gradeLevel.'A',
            'grade_level' => $gradeLevel,
            'status' => 'Aktif',
        ]);
        $student = Student::create([
            'nis' => 'NIS-IMPORT',
            'nisn' => '0012345678',
            'name' => 'Siswa Import',
            'gender' => 'L',
            'status' => 'Aktif',
        ]);
        ClassStudent::create([
            'class_id' => $class->id,
            'student_id' => $student->id,
            'status' => 'Aktif',
        ]);
        $subject = Subject::create([
            'code' => 'MAT-IMPORT',
            'name' => 'Matematika',
            'category' => 'Intrakurikuler',
            'status' => 'Aktif',
        ]);
        $class->subjects()->attach($subject->id);

        return [$year, $class, $student, $subject, $odd, $even];
    }

    private function cellText(Worksheet $sheet, string $cell): string
    {
        $value = $sheet->getCell($cell)->getValue();

        return $value instanceof RichText
            ? $value->getPlainText()
            : (string) $value;
    }

    private function previewFile(
        ReportCardGradeImportService $importer,
        string $path,
        AcademicYear $year,
        SchoolClass $class,
        Semester $semester
    ): array {
        return $importer->preview($path, [
            'academic_year' => $year,
            'class' => $class,
            'semester' => $semester,
        ]);
    }

    private function loadWorkbook(string $path): Spreadsheet
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);

        return $reader->load($path);
    }

    private function makeWorkbook(
        ?Student $student,
        AcademicYear $year,
        SchoolClass $class,
        Semester $semester,
        array $values
    ): string {
        $spreadsheet = app(ReportCardGradeImportService::class)->createTemplate([
            'academic_year' => $year,
            'class' => $class,
            'semester' => $semester,
        ]);
        $sheet = $spreadsheet->getSheetByName('IMPORT NILAI');

        foreach (range(11, 10 + $class->students()->count()) as $row) {
            $sheet->setCellValue('A'.$row, null);
            $sheet->setCellValue('B'.$row, null);
            $sheet->setCellValue('C'.$row, null);
        }

        if ($student) {
            $sheet->setCellValue('A11', 1);
            $sheet->setCellValueExplicit('B11', $student->nisn, DataType::TYPE_STRING);
            $sheet->setCellValue('C11', $student->name);
        }

        foreach ($values as $cell => $value) {
            [$legacyColumn, $row] = Coordinate::coordinateFromString($cell);
            $legacyFieldOffsets = [
                'W' => 0,
                'X' => 1,
                'Y' => 2,
            ];
            if (isset($legacyFieldOffsets[$legacyColumn])) {
                $subjectIndex = $class->subjects()
                    ->where('subjects.status', 'Aktif')
                    ->orderBy('subjects.sort_order')
                    ->orderBy('subjects.name')
                    ->get()
                    ->search(fn (Subject $subject) => $subject->name === 'Matematika');
                if ($subjectIndex !== false) {
                    $newColumn = Coordinate::stringFromColumnIndex(
                        4 + ($subjectIndex * 3) + $legacyFieldOffsets[$legacyColumn]
                    );
                    $sheet->setCellValue($newColumn.$row, $value);

                    continue;
                }
            }

            $sheet->setCellValue($cell, $value);
        }

        $temporaryWorkbook = tempnam(sys_get_temp_dir(), 'biduk-import-');
        $this->temporaryWorkbooks[] = $temporaryWorkbook;
        (new Xlsx($spreadsheet))->save($temporaryWorkbook);

        return $temporaryWorkbook;
    }
}
