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
                $query->where(
                    'status',
                    $studentStatus === 'aktif' ? 'Aktif' : 'Nonaktif'
                );
            })
            ->when(
                $request->filled('search'),
                fn ($query) => $query->search($request->search)
            )
            ->whereHas('classAssignments', function ($query) use (
                $request,
                $selectedAcademicYear
            ) {
                $query->where('status', 'Aktif')
                    ->when(
                        $request->filled('class_id') && $request->input('print_type') !== 'all',
                        function ($query) use ($request) {
                            $query->where('class_id', $request->class_id);
                        }
                    )
                    ->whereHas('schoolClass', function ($query) use (
                        $selectedAcademicYear
                    ) {
                        $query->when(
                            $selectedAcademicYear,
                            function ($query) use ($selectedAcademicYear) {
                                $query->where(
                                    'academic_year_id',
                                    $selectedAcademicYear
                                );
                            }
                        );
                    });
            })
            ->with([
                'classAssignments' => function ($query) use (
                    $selectedAcademicYear
                ) {
                    $query->where('status', 'Aktif')
                        ->whereHas('schoolClass', function ($query) use (
                            $selectedAcademicYear
                        ) {
                            $query->when(
                                $selectedAcademicYear,
                                function ($query) use ($selectedAcademicYear) {
                                    $query->where(
                                        'academic_year_id',
                                        $selectedAcademicYear
                                    );
                                }
                            );
                        });
                },
            ])
            ->orderBy('name')
            ->get();

        $selectedStudent = null;
        if ($request->filled('student_id')) {
            $selectedStudent = $students->firstWhere(
                'id',
                (int) $request->input('student_id')
            ) ?? Student::with([
                'classAssignments.schoolClass.academicYear',
                'classes.academicYear'
            ])->find($request->input('student_id'));
        }

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
                $query->where(
                    'status',
                    $studentStatus === 'aktif' ? 'Aktif' : 'Nonaktif'
                );
            })
            ->when(
                $request->filled('search'),
                fn ($query) => $query->search($request->search)
            )
            ->whereHas('classAssignments', function ($query) use (
                $request,
                $selectedAcademicYear
            ) {
                $query->where('status', 'Aktif')
                    ->when(
                        $request->filled('class_id'),
                        function ($query) use ($request) {
                            $query->where('class_id', $request->class_id);
                        }
                    )
                    ->whereHas('schoolClass', function ($query) use (
                        $selectedAcademicYear
                    ) {
                        $query->when(
                            $selectedAcademicYear,
                            function ($query) use ($selectedAcademicYear) {
                                $query->where(
                                    'academic_year_id',
                                    $selectedAcademicYear
                                );
                            }
                        );
                    });
            })
            ->orderBy('name')
            ->get();

        $class = $request->filled('class_id')
            ? SchoolClass::where(
                'academic_year_id',
                $selectedAcademicYear
            )->findOrFail($request->class_id)
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
                ->setPaper('a4', 'portrait')
                ->download(
                    'buku-induk-' . ($student->nis ?: $student->id) . '.pdf'
                );
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


    /**
     * Membuat SATU file PDF untuk semua siswa yang dipilih.
     *
     * Setiap siswa terdiri dari 2 halaman Buku Induk.
     * Tidak membuat ZIP dan tidak membuat satu PDF per siswa.
     */
    public function batchPdf(Request $request)
    {
        @ini_set('memory_limit', '512M');
        @set_time_limit(300);

        $studentIds = $request->input('student_ids', []);

        if (is_array($studentIds) && count($studentIds)) {
            $students = Student::query()
                ->whereIn('id', $studentIds)
                ->orderBy('name')
                ->get();
        } else {
            $selectedAcademicYear = $request->input(
                'academic_year_id',
                AcademicYear::getActive()?->id
            );

            $studentStatus = $request->input('status', 'aktif');
            $printType = $request->input('print_type');
            $classId = ($printType === 'all') ? null : $request->input('class_id');

            $students = Student::query()
                ->when($studentStatus !== '', function ($query) use ($studentStatus) {
                    $query->where(
                        'status',
                        $studentStatus === 'aktif' ? 'Aktif' : 'Nonaktif'
                    );
                })
                ->when(
                    $request->filled('search'),
                    fn ($query) => $query->search($request->search)
                )
                ->whereHas('classAssignments', function ($query) use (
                    $classId,
                    $selectedAcademicYear
                ) {
                    $query->where('status', 'Aktif')
                        ->when(
                            $classId,
                            function ($query) use ($classId) {
                                $query->where('class_id', $classId);
                            }
                        )
                        ->whereHas('schoolClass', function ($query) use (
                            $selectedAcademicYear
                        ) {
                            $query->when(
                                $selectedAcademicYear,
                                function ($query) use ($selectedAcademicYear) {
                                    $query->where(
                                        'academic_year_id',
                                        $selectedAcademicYear
                                    );
                                }
                            );
                        });
                })
                ->orderBy('name')
                ->get();
        }

        abort_if(
            $students->isEmpty(),
            404,
            'Siswa tidak ditemukan.'
        );

        $style = '';
        $studentPages = [];

        foreach ($students as $student) {
            $reportData = $this->reportData($request, $student);

            $html = view(
                'reports.buku-induk.print',
                $reportData
            )->render();

            /*
             * Ambil CSS satu kali dari template Buku Induk.
             */
            if ($style === '' &&
                preg_match(
                    '/<style[^>]*>(.*?)<\/style>/is',
                    $html,
                    $styleMatch
                )
            ) {
                $style = $styleMatch[1];
            }

            /*
             * Ambil isi BODY saja (yaitu div.page halaman 1 dan 2).
             * Jangan menumpuk <html>, <head>, dan <body>
             * untuk setiap siswa.
             */
            if (preg_match(
                '/<body[^>]*>(.*?)<\/body>/is',
                $html,
                $bodyMatch
            )) {
                $body = trim($bodyMatch[1]);
            } else {
                $body = trim($html);
            }

            /*
             * Setiap siswa menghasilkan 2 blok .page langsung sebagai
             * child dari body. Tidak ada wrapper <div> per siswa agar
             * layout dan page-break Dompdf 100% konsisten dengan
             * cetak individual.
             */
            $studentPages[] = $body;
        }

        $combinedHtml = '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Induk</title>
    <style>' . $style . '</style>
</head>
<body class="pdf-export">' . implode('', $studentPages) . '</body>
</html>';

        return Pdf::loadHTML($combinedHtml)
            ->setPaper('a4', 'portrait')
            ->download(
                'buku-induk-' . now()->format('Y-m-d-His') . '.pdf'
            );
    }

    /**
     * Membuat ZIP yang berisi satu PDF untuk setiap siswa terpilih.
     */
    public function downloadAll(Request $request)
    {
        $studentIds = $request->input('student_ids', []);

        abort_unless(
            is_array($studentIds) && count($studentIds),
            422,
            'Tidak ada siswa yang dipilih.'
        );

        $students = Student::whereIn('id', $studentIds)
            ->orderBy('name')
            ->get();

        abort_if(
            $students->isEmpty(),
            404,
            'Siswa tidak ditemukan.'
        );

        if (!class_exists(\ZipArchive::class)) {
            abort(
                500,
                'Ekstensi PHP ZipArchive belum aktif. Aktifkan extension=zip pada PHP yang digunakan Laragon.'
            );
        }

        $zipPath = tempnam(
            storage_path('app'),
            'buku-induk-'
        );

        $zip = new \ZipArchive();

        abort_unless(
            $zipPath &&
            $zip->open(
                $zipPath,
                \ZipArchive::CREATE | \ZipArchive::OVERWRITE
            ) === true,
            500,
            'File ZIP tidak dapat dibuat.'
        );

        try {
            foreach ($students as $student) {
                $reportData = $this->reportData(
                    $request,
                    $student
                );

                /*
                 * Gunakan render path yang sama dengan export PDF
                 * per individu agar hasil PDF di dalam ZIP konsisten.
                 */
                $view = view(
                    'reports.buku-induk.print',
                    $reportData
                );

                $pdf = Pdf::loadHTML($view->render())
                    ->setPaper('a4', 'portrait')
                    ->output();

                $filename = 'buku-induk-' .
                    ($student->nis ?: $student->id) .
                    '.pdf';

                $zip->addFromString(
                    $filename,
                    $pdf
                );
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

    /**
     * Menyiapkan seluruh data yang digunakan oleh template Buku Induk.
     */
    private function reportData(
        Request $request,
        Student $student
    ): array {
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
            ])->where(
                'academic_year_id',
                $academicYear?->id
            )->find(
                $request->class_id
            );
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
            $semester = Semester::where(
                'academic_year_id',
                $academicYear?->id
            )->find(
                $request->semester_id
            );
        }

        return compact(
            'student',
            'academicYear',
            'semester',
            'class'
        );
    }
}
