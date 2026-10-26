<?php

namespace App\Http\Controllers;

use App\Models\Achievement;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Services\TeacherClassAccess;
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

        $classAccess = app(TeacherClassAccess::class);
        $user = $request->user();
        $teacherClassIds = $classAccess->classIds($user);
        $teacherAcademicYearIds = $teacherClassIds === null
            ? null
            : SchoolClass::whereIn('id', $teacherClassIds)
                ->distinct()
                ->pluck('academic_year_id');

        $academicYears = AcademicYear::query()
            ->when(
                $teacherAcademicYearIds !== null,
                fn ($query) => $query->whereIn('id', $teacherAcademicYearIds)
            )
            ->orderByDesc('id')
            ->get();

        /*
         * Tahun ajaran aktif sebagai default.
         */
        $selectedAcademicYearId = $request->academic_year_id
            ?? ($teacherAcademicYearIds?->contains(AcademicYear::getActive()?->id)
                ? AcademicYear::getActive()?->id
                : $academicYears->first()?->id);

        abort_if(
            $teacherAcademicYearIds !== null
                && $selectedAcademicYearId
                && !$teacherAcademicYearIds->contains((int) $selectedAcademicYearId),
            403,
            'Anda tidak memiliki akses ke tahun ajaran ini.'
        );

        /*
         * ======================================================
         * KELAS
         * ======================================================
         */

        $classes = collect();

        if ($selectedAcademicYearId) {

            $classesQuery = SchoolClass::where(
                'academic_year_id',
                $selectedAcademicYearId
            );

            if ($teacherClassIds !== null) {
                $classesQuery->whereIn('id', $teacherClassIds);
            }

            $classes = $classesQuery
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

            $classQuery = SchoolClass::where(
                'id',
                $selectedClassId
            )
                ->where(
                    'academic_year_id',
                    $selectedAcademicYearId
                )
                ->where('status', 'Aktif');

            if ($teacherClassIds !== null) {
                $classQuery->whereIn('id', $teacherClassIds);
            }

            $classExists = $classQuery->exists();

            if (!$classExists) {
                abort(403, 'Anda tidak memiliki akses ke kelas ini.');
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
            ->when(
                $selectedAcademicYearId,
                fn ($query) => $query->where('academic_year_id', $selectedAcademicYearId)
            );

        if ($teacherClassIds !== null) {
            $query->whereIn('class_id', $teacherClassIds);
        }

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
