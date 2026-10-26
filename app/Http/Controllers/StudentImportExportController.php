<?php

namespace App\Http\Controllers;

use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Services\TeacherClassAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StudentsTemplateExport;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Illuminate\Validation\Rule;
use Throwable;

class StudentImportExportController extends Controller
{
    /**
     * Menampilkan form import siswa.
     * Akses route harus dibatasi dengan permission:students.import.
     */
    public function form(Request $request): View
    {
        $teacherClassIds = app(TeacherClassAccess::class)->classIds($request->user());
        $academicYears = AcademicYear::query()
            ->when(
                $teacherClassIds !== null,
                fn ($query) => $query->whereIn(
                    'id',
                    SchoolClass::whereIn('id', $teacherClassIds)
                        ->select('academic_year_id')
                        ->distinct()
                )
            )
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();

        $activeAcademicYear = AcademicYear::getActive();
        $selectedAcademicYearId = $request->input('academic_year_id');
        if (!$selectedAcademicYearId || !$academicYears->contains('id', (int) $selectedAcademicYearId)) {
            $selectedAcademicYearId = $academicYears->contains('id', $activeAcademicYear?->id)
                ? $activeAcademicYear?->id
                : $academicYears->first()?->id;
        }

        $classes = SchoolClass::query()
            ->when(
                $teacherClassIds !== null,
                fn ($query) => $query->whereIn('id', $teacherClassIds)
            )
            ->with('academicYear')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $selectedClassId = $request->input('class_id');
        if (!$classes->contains(fn ($class) =>
            (int) $class->id === (int) $selectedClassId
            && (int) $class->academic_year_id === (int) $selectedAcademicYearId
        )) {
            $selectedClassId = $classes
                ->first(fn ($class) => (int) $class->academic_year_id === (int) $selectedAcademicYearId)
                ?->id;
        }

        return view('students.import', compact(
            'academicYears',
            'classes',
            'selectedAcademicYearId',
            'selectedClassId',
            'teacherClassIds'
        ));
    }

    /**
     * Import siswa. Pembatasan kelas Guru juga diterapkan di StudentsImport.
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'class_id' => [
                'required',
                'integer',
                Rule::exists('classes', 'id')->where(
                    'academic_year_id',
                    $request->input('academic_year_id')
                ),
            ],
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:10240',
            ],
        ]);

        try {
            $teacherClassIds = app(TeacherClassAccess::class)->classIds($request->user());
            $class = SchoolClass::query()
                ->where('academic_year_id', $request->integer('academic_year_id'))
                ->when(
                    $teacherClassIds !== null,
                    fn ($query) => $query->whereIn('id', $teacherClassIds)
                )
                ->find($request->integer('class_id'));

            abort_unless(
                $class,
                403,
                'Kelas yang dipilih bukan kelas yang dapat Anda kelola pada tahun ajaran tersebut.'
            );

            $import = new StudentsImport(
                $request->integer('academic_year_id'),
                $class->id
            );

            Excel::import($import, $request->file('file'));

            $message = sprintf(
                'Import selesai. Baru: %d, diperbarui: %d, dilewati/gagal: %d.',
                $import->getImportedCount(),
                $import->getUpdatedCount(),
                $import->getSkippedCount()
            );

            return redirect()
                ->route('students.index')
                ->with('success', $message)
                ->with('import_errors', $import->getErrors());
        } catch (HttpExceptionInterface $e) {
            // Jangan ubah penolakan 403 menjadi pesan error import biasa.
            throw $e;
        } catch (Throwable $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'import' => 'Import gagal. Periksa format dan isi file, lalu coba kembali.',
                ]);
        }
    }
/**
 * Download template kosong untuk import siswa.
 */
public function downloadTemplate()
{
    return Excel::download(
        new StudentsTemplateExport(),
        'Template_Import_Data_Siswa.xlsx'
    );
}
    /**
     * Export siswa. StudentsExport membatasi hasil untuk kelas wali Guru.
     */
    public function exportExcel()
    {
        return Excel::download(
            new StudentsExport(),
            'BIDUK_Data_Siswa_' . now()->format('Ymd_His') . '.xlsx'
        );
    }
}
