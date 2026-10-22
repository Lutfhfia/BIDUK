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
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
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

    public function test_repeat_import_updates_existing_grade_and_preserves_omitted_values(): void
    {
        [$year, $class, $student, $subject, $odd, $even] = $this->createRoster();
        $importer = app(ReportCardGradeImportService::class);

        $firstFile = $this->makeWorkbook($student, $year, $class, $odd, [
            'W11' => 80,
            'X11' => 'B',
            'Y11' => 'Memahami konsep matematika.',
            'Z11' => 88,
            'AA11' => 'A',
            'AB11' => 'Mampu menerapkan konsep matematika.',
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

        $secondFile = $this->makeWorkbook($student, $year, $class, $odd, ['W11' => 90]);
        $secondPreview = $this->previewFile($importer, $secondFile, $year, $class, $odd);
        $this->assertSame(1, $secondPreview['summary']['updated_records']);
        $this->assertSame(0, $secondPreview['summary']['new_records']);
        $this->assertSame(1, $importer->import($secondPreview));

        $grade->refresh();
        $this->assertSame('90.00', $grade->score);
        $this->assertSame('B', $grade->knowledge_predicate);
        $this->assertSame('Memahami konsep matematika.', $grade->learning_outcome);
        $this->assertSame('88.00', $grade->skill_score);
        $this->assertSame('A', $grade->skill_predicate);

        $genapFile = $this->makeWorkbook($student, $year, $class, $even, ['W11' => 72]);
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
        $newYearFile = $this->makeWorkbook($student, $nextYear, $nextYearClass, $newYearSemester, ['W11' => 70]);
        $newYearPreview = $this->previewFile($importer, $newYearFile, $nextYear, $nextYearClass, $newYearSemester);
        $this->assertSame(1, $newYearPreview['summary']['new_records']);
        $importer->import($newYearPreview);
        $this->assertSame(3, ReportCardGrade::where('student_id', $student->id)->count());
    }

    public function test_unknown_nisn_and_invalid_scores_are_reported_without_creating_students(): void
    {
        [$year, $class, , , $odd] = $this->createRoster();
        $file = $this->makeWorkbook(null, $year, $class, $odd, [
            'B11' => '9999999999',
            'C11' => 'Siswa Tidak Ada',
            'W11' => '85/100',
            'Z11' => 101,
        ]);

        $preview = $this->previewFile(app(ReportCardGradeImportService::class), $file, $year, $class, $odd);

        $this->assertSame(1, $preview['summary']['students_not_found']);
        $this->assertSame(0, $preview['summary']['valid_students']);
        $this->assertFalse($preview['can_import']);
        $this->assertContains('NISN tidak ditemukan.', $preview['rows'][0]['errors']);
        $this->assertSame(1, Student::count());
    }

    public function test_student_without_class_assignment_is_imported_into_the_selected_class_only(): void
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
        $adminRole = Role::create(['name' => 'Admin']);
        $admin = User::create([
            'name' => 'Admin Import',
            'username' => 'admin-import',
            'email' => 'admin-import@example.test',
            'password' => 'password',
            'role_id' => $adminRole->id,
        ]);
        $selection = app(ReportCardGradeImportContext::class)->validateSelection($admin, [
            'academic_year_id' => $year->id,
            'class_id' => $selectedClass->id,
            'semester_id' => $odd->id,
        ]);
        $preview = $importer->preview($file, $selection);

        $this->assertTrue($preview['can_import']);
        $this->assertSame([], $preview['rows'][0]['errors']);
        $importer->import($preview);

        $this->assertDatabaseHas('report_card_grades', [
            'student_id' => $student->id,
            'class_id' => $selectedClass->id,
            'subject_id' => $subject->id,
            'semester_id' => $odd->id,
            'score' => 86,
        ]);
        $this->assertSame(0, ClassStudent::where('student_id', $student->id)->count());

        $editView = app(ReportCardGradeController::class)->edit(
            Request::create('/rapot/edit', 'GET'),
            $selectedClass,
            $student
        );
        $gradeKey = $odd->id.'_'.$subject->id;
        $this->assertSame('86.00', $editView->getData()['grades'][$gradeKey]->score);

        $printView = app(ReportCardGradeController::class)->printHistory(
            Request::create('/rapot/print', 'GET', ['academic_year_id' => $year->id]),
            $selectedClass,
            $student
        );
        $this->assertStringContainsString('86', $printView->render());
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
            'J11' => 89,
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
            'W11' => '85/100',
            'X11' => 'E',
            'Z11' => 101,
        ]);

        $preview = $this->previewFile(app(ReportCardGradeImportService::class), $file, $year, $class, $odd);

        $this->assertSame(3, $preview['summary']['invalid_values']);
        $this->assertFalse($preview['can_import']);
        $this->assertTrue(collect($preview['rows'][0]['errors'])->contains(
            fn ($error) => str_contains($error, 'Nilai Matematika (Pengetahuan - Nilai) harus berupa angka 0–100.')
        ));
        $this->assertTrue(collect($preview['rows'][0]['errors'])->contains(
            fn ($error) => str_contains($error, 'Nilai Matematika (Pengetahuan - Predikat) harus A, B, C, atau D.')
        ));
        $this->assertTrue(collect($preview['rows'][0]['errors'])->contains(
            fn ($error) => str_contains($error, 'Nilai Matematika (Keterampilan - Nilai) harus berupa angka 0–100.')
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

    public function test_importing_one_subject_does_not_change_other_subject_records(): void
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
            'score' => 91,
            'learning_outcome' => 'Nilai bahasa tetap.',
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
            fn ($error) => str_contains($error, 'Matematika / Pengetahuan / Nilai')
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
        $this->assertSame('Pengetahuan', $this->cellText($sheet, 'D8'));
        $this->assertSame('Keterampilan', $this->cellText($sheet, 'F8'));
        $this->assertSame('Capaian Pembelajaran', $this->cellText($sheet, 'H8'));
        $this->assertSame('Nilai', $this->cellText($sheet, 'D9'));
        $this->assertSame('Predikat', $this->cellText($sheet, 'E9'));
        $this->assertSame('Nilai', $this->cellText($sheet, 'F9'));
        $this->assertSame('Predikat', $this->cellText($sheet, 'G9'));
        $this->assertSame('Pengetahuan', $this->cellText($sheet, 'H9'));
        $this->assertSame('Keterampilan', $this->cellText($sheet, 'I9'));
    }

    public function test_template_subjects_are_dynamic_for_each_selected_class(): void
    {
        [$year, $class, , , $odd] = $this->createRoster();
        $science = Subject::create([
            'code' => 'IPA-IMPORT',
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
        $this->assertSame('Pendidikan Pancasila', $this->cellText($firstTemplate, 'J7'));

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

    public function test_imported_knowledge_and_skill_values_appear_in_report_print(): void
    {
        [$year, $class, $student, , $odd] = $this->createRoster();
        $file = $this->makeWorkbook($student, $year, $class, $odd, [
            'W11' => 80,
            'X11' => 'B',
            'Y11' => 'Pengetahuan teruji.',
            'Z11' => 90,
            'AA11' => 'A',
            'AB11' => 'Keterampilan teruji.',
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
        $this->assertStringContainsString('<th>CAPAIAN PEMBELAJARAN</th>', $html);
        $this->assertStringContainsString('>80.00</td>', $html);
        $this->assertStringContainsString('Pengetahuan teruji.', $html);
        $this->assertStringContainsString('Keterampilan teruji.', $html);
        $this->assertStringNotContainsString('NILAI PENGETAHUAN / KETERAMPILAN', $html);
    }

    public function test_imported_knowledge_and_skill_values_appear_in_grade_six_report_print(): void
    {
        [$year, $class, $student, , $odd] = $this->createRoster(6);
        $file = $this->makeWorkbook($student, $year, $class, $odd, [
            'W11' => 80,
            'X11' => 'B',
            'Y11' => 'Pengetahuan teruji.',
            'Z11' => 90,
            'AA11' => 'A',
            'AB11' => 'Keterampilan teruji.',
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
        $this->assertStringContainsString('Pengetahuan teruji.', $html);
        $this->assertStringContainsString('Keterampilan teruji.', $html);
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
                'Y' => 4,
                'Z' => 2,
                'AA' => 3,
                'AB' => 5,
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
                        4 + ($subjectIndex * 6) + $legacyFieldOffsets[$legacyColumn]
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
