<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BukuIndukController extends Controller
{
    /**
     * Halaman utama Cetak Buku Induk.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();

        $selectedAcademicYear = $request->input(
            'academic_year_id',
            AcademicYear::getActive()?->id
        );

        $semesters = Semester::query()
            ->when(
                $selectedAcademicYear,
                fn ($query) => $query->where(
                    'academic_year_id',
                    $selectedAcademicYear
                )
            )
            ->orderBy('name')
            ->get();

        $classes = SchoolClass::query()
            ->when(
                $selectedAcademicYear,
                fn ($query) => $query->where(
                    'academic_year_id',
                    $selectedAcademicYear
                )
            )
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        $studentStatus = $request->input('status', 'aktif');

        $students = Student::query()
            ->when($studentStatus !== '', function ($query) use ($studentStatus) {
                $query->where('status', $studentStatus === 'aktif' ? 'Aktif' : 'Nonaktif');
            })
            ->when($request->filled('search'), fn ($query) => $query->search($request->search))
            ->whereHas('classAssignments', function ($query) use ($request, $selectedAcademicYear) {
                $query->where('status', 'Aktif')
                    ->when($request->filled('class_id'), function ($query) use ($request) {
                        $query->where('class_id', $request->class_id);
                    })
                    ->whereHas('schoolClass', function ($query) use ($selectedAcademicYear) {
                        $query->when($selectedAcademicYear, function ($query) use ($selectedAcademicYear) {
                            $query->where('academic_year_id', $selectedAcademicYear);
                        });
                    });
            })
            ->with(['classAssignments' => function ($query) use ($selectedAcademicYear) {
                $query->where('status', 'Aktif')
                    ->whereHas('schoolClass', function ($query) use ($selectedAcademicYear) {
                        $query->when($selectedAcademicYear, function ($query) use ($selectedAcademicYear) {
                            $query->where('academic_year_id', $selectedAcademicYear);
                        });
                    });
            }])
            ->orderBy('name')
            ->get();

        $selectedStudent = $students->firstWhere(
            'id',
            (int) $request->input('student_id')
        );

        return view('reports.buku-induk.index', compact(
            'academicYears',
            'semesters',
            'classes',
            'students',
            'selectedStudent',
            'selectedAcademicYear'
        ));
    }

    /**
     * Menampilkan daftar Buku Induk untuk satu kelas atau seluruh kelas.
     */
    public function batch(Request $request): View
    {
        $selectedAcademicYear = $request->input(
            'academic_year_id',
            AcademicYear::getActive()?->id
        );

        $studentStatus = $request->input('status', 'aktif');

        $students = Student::query()
            ->when($studentStatus !== '', function ($query) use ($studentStatus) {
                $query->where('status', $studentStatus === 'aktif' ? 'Aktif' : 'Nonaktif');
            })
            ->whereHas('classAssignments', function ($query) use ($request, $selectedAcademicYear) {
                $query->where('status', 'Aktif')
                    ->when($request->filled('class_id'), function ($query) use ($request) {
                        $query->where('class_id', $request->class_id);
                    })
                    ->whereHas('schoolClass', function ($query) use ($selectedAcademicYear) {
                        $query->when($selectedAcademicYear, function ($query) use ($selectedAcademicYear) {
                            $query->where('academic_year_id', $selectedAcademicYear);
                        });
                    });
            })
            ->orderBy('name')
            ->get();

        $class = $request->filled('class_id')
            ? SchoolClass::where('academic_year_id', $selectedAcademicYear)
                ->findOrFail($request->class_id)
            : null;

        return view('reports.buku-induk.batch', compact(
            'students',
            'class',
            'selectedAcademicYear'
        ));
    }

    /**
     * Menampilkan Buku Induk dalam format siap cetak.
     */
    public function print(Request $request, Student $student)
    {
        $reportData = $this->reportData($request, $student);

        $view = view('reports.buku-induk.print', $reportData);

        if ($request->boolean('pdf')) {
            return Pdf::loadHTML($view->render())
                ->setPaper([0, 0, 609.45, 935.43], 'portrait')
                ->download('buku-induk-' . ($student->nis ?: $student->id) . '.pdf');
        }

        if ($request->boolean('download')) {
            $filename = 'buku-induk-' . ($student->nis ?: $student->id) . '.html';

            return response()->streamDownload(
                fn () => print($view->render()),
                $filename,
                ['Content-Type' => 'text/html; charset=UTF-8']
            );
        }

        return $view;
    }

    public function downloadAll(Request $request)
    {
        $studentIds = $request->input('student_ids', []);

        abort_unless(is_array($studentIds) && count($studentIds), 422, 'Tidak ada siswa yang dipilih.');

        $students = Student::whereIn('id', $studentIds)->get();
        abort_if($students->isEmpty(), 404, 'Siswa tidak ditemukan.');

        $reports = $students->map(fn (Student $student) => [
            'student' => $student,
            ...$this->reportData($request, $student),
        ]);

        if (!class_exists(\ZipArchive::class)) {
            return Pdf::loadView('reports.buku-induk.bulk-pdf', compact('reports'))
                ->setPaper([0, 0, 609.45, 935.43], 'portrait')
                ->download('buku-induk-semua-' . now()->format('Y-m-d-His') . '.pdf');
        }

        $zipPath = tempnam(storage_path('app'), 'buku-induk-');
        $zip = new \ZipArchive();

        abort_unless($zipPath && $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) === true, 500, 'File ZIP tidak dapat dibuat.');

        try {
            foreach ($students as $student) {
                $pdf = Pdf::loadView('reports.buku-induk.print', $this->reportData($request, $student))
                    ->setPaper([0, 0, 609.45, 935.43], 'portrait')
                    ->output();

                $filename = 'buku-induk-' . ($student->nis ?: $student->id) . '.pdf';
                $zip->addFromString($filename, $pdf);
            }

            $zip->close();
        } catch (\Throwable $exception) {
            $zip->close();
            @unlink($zipPath);
            throw $exception;
        }

        return response()->download(
            $zipPath,
            'buku-induk-' . now()->format('Y-m-d-His') . '.zip',
            ['Content-Type' => 'application/zip']
        )->deleteFileAfterSend(true);
    }

    private function reportData(Request $request, Student $student): array
    {
        $student->load([
            'classes.academicYear',
            'classes.homeroomTeacher',
            'classAssignments.schoolClass.academicYear',
            'classAssignments.schoolClass.homeroomTeacher',
            'previousEducation',
            'physiques.academicYear',
            'physiques.semester',
            'healths.academicYear',
            'healths.semester',
        ]);

        $academicYear = null;

        if ($request->filled('academic_year_id')) {
            $academicYear = AcademicYear::find(
                $request->academic_year_id
            );
        }

        if (!$academicYear) {
            $academicYear = AcademicYear::getActive();
        }

        $class = null;

        if ($request->filled('class_id')) {
            $class = SchoolClass::with([
                'academicYear',
                'homeroomTeacher',
            ])->find($request->class_id);
        }

        if (!$class) {
            $class = $student->classes()
                ->with([
                    'academicYear',
                    'homeroomTeacher',
                ])
                ->wherePivot('status', 'Aktif')
                ->latest('class_student.created_at')
                ->first();
        }

        $semester = null;

        if ($request->filled('semester_id')) {
            $semester = Semester::find($request->semester_id);
        }

        return compact(
            'student',
            'academicYear',
            'semester',
            'class'
        );
    }

}


