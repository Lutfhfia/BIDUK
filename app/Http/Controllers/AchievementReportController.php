<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class AchievementReportController extends Controller
{
    /**
     * ==========================================================
     * INDEX
     * ==========================================================
     *
     * Menampilkan rekap prestasi siswa.
     *
     * Siswa tanpa prestasi tidak ditampilkan.
     */
    public function index(Request $request)
    {
        /*
         * ======================================================
         * TAHUN AJARAN
         * ======================================================
         */

        $academicYears = AcademicYear::orderByDesc('id')
            ->get();

        /*
         * Tahun ajaran aktif sebagai default.
         */
        $selectedAcademicYearId =
            $request->academic_year_id
            ?? AcademicYear::getActive()?->id;

        /*
         * ======================================================
         * KELAS
         * ======================================================
         */

        $classes = collect();

        if ($selectedAcademicYearId) {

            $classes = SchoolClass::where(
                'academic_year_id',
                $selectedAcademicYearId
            )
                ->where('status', 'Aktif')
                ->orderBy('grade_level')
                ->orderBy('name')
                ->get();
        }

        /*
         * ======================================================
         * FILTER KELAS
         * ======================================================
         */

        $selectedClassId = $request->class_id;

        /*
         * Pastikan kelas sesuai dengan tahun ajaran.
         */
        if (
            $selectedClassId &&
            $selectedAcademicYearId
        ) {

            $classExists = SchoolClass::where(
                'id',
                $selectedClassId
            )
                ->where(
                    'academic_year_id',
                    $selectedAcademicYearId
                )
                ->where('status', 'Aktif')
                ->exists();

            if (!$classExists) {
                $selectedClassId = null;
            }
        }

        /*
         * ======================================================
         * QUERY PRESTASI
         * ======================================================
         *
         * Hanya mengambil siswa yang memiliki prestasi.
         */

        $query = Achievement::with([
            'student',
            'schoolClass',
            'academicYear'
        ])
            ->where(
                'academic_year_id',
                $selectedAcademicYearId
            );

        /*
         * Filter kelas jika dipilih.
         */
        if ($selectedClassId) {

            $query->where(
                'class_id',
                $selectedClassId
            );
        }

        /*
         * Search siswa / NIS / NISN.
         */
        if ($request->filled('search')) {

            $search = $request->search;

            $query->whereHas(
                'student',
                function ($studentQuery) use ($search) {

                    $studentQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere(
                            'nis',
                            'like',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'nisn',
                            'like',
                            "%{$search}%"
                        );
                }
            );
        }

        /*
         * Urutan data.
         */
        $achievements = $query
            ->orderBy('class_id')
            ->orderBy('student_id')
            ->orderBy('id')
            ->get();

        /*
         * ======================================================
         * RETURN VIEW
         * ======================================================
         */

        return view(
            'admin.achievement-reports.index',
            compact(
                'academicYears',
                'classes',
                'achievements',
                'selectedAcademicYearId',
                'selectedClassId'
            )
        );
    }
}
