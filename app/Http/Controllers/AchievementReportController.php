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
     * Menampilkan rekap prestasi siswa.
     * Siswa tanpa prestasi tidak ditampilkan.
     */
    public function index(Request $request)
    {
        $classAccess = app(TeacherClassAccess::class);
        $user = $request->user();

        // null berarti pengguna bukan Guru sehingga tidak dibatasi ke kelas tertentu.
        // Untuk Guru, hasilnya berisi kelas yang ditugaskan kepadanya.
        $teacherClassIds = $classAccess->classIds($user);

        $teacherAcademicYearIds = $teacherClassIds === null
            ? null
            : SchoolClass::query()
                ->whereIn('id', $teacherClassIds)
                ->distinct()
                ->pluck('academic_year_id');

        // Super Admin, Admin, dan Kepala Sekolah dapat memilih seluruh tahun ajaran.
        // Guru hanya melihat tahun ajaran dari kelas yang menjadi tanggung jawabnya.
        $academicYears = AcademicYear::query()
            ->when(
                $teacherAcademicYearIds !== null,
                fn ($query) => $query->whereIn('id', $teacherAcademicYearIds)
            )
            ->orderByDesc('start_date')
            ->orderByDesc('id')
            ->get();

        $selectedAcademicYearId = $request->query->has('academic_year_id')
            ? ($request->input('academic_year_id') ?: null)
            : null;

        abort_if(
            $teacherAcademicYearIds !== null
                && $selectedAcademicYearId
                && !$teacherAcademicYearIds->contains((int) $selectedAcademicYearId),
            403,
            'Anda tidak memiliki akses ke tahun ajaran ini.'
        );

        /*
         * Daftar kelas:
         * - Non-Guru: seluruh kelas, termasuk kelas dari tahun ajaran lama/nonaktif.
         * - Guru: hanya kelas yang ada dalam TeacherClassAccess, termasuk riwayat kelas.
         * Status kelas tidak dipakai untuk menghapus riwayat dari laporan.
         */
        $classesQuery = SchoolClass::query()
            ->with('academicYear')
            ->when(
                $selectedAcademicYearId,
                fn ($query) => $query->where('academic_year_id', $selectedAcademicYearId)
            );

        if ($teacherClassIds !== null) {
            $classesQuery->whereIn('id', $teacherClassIds);
        }

        $classes = $classesQuery
            ->orderBy('academic_year_id')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        /*
         * Validasi kelas yang dipilih.
         * Pengecekan ini dilakukan di server supaya Guru tidak dapat membuka
         * kelas milik Guru lain dengan mengubah parameter URL.
         */
        $selectedClassId = $request->input('class_id') ?: null;

        if ($selectedClassId !== null) {
            $classQuery = SchoolClass::query()
                ->whereKey($selectedClassId)
                ->when(
                    $selectedAcademicYearId,
                    fn ($query) => $query->where('academic_year_id', $selectedAcademicYearId)
                );

            if ($teacherClassIds !== null) {
                $classQuery->whereIn('id', $teacherClassIds);
            }

            abort_unless(
                $classQuery->exists(),
                403,
                'Anda tidak memiliki akses ke kelas ini.'
            );
        }

        // Query data prestasi. Pengguna non-Guru tidak dibatasi kelas;
        // Guru hanya memperoleh data dari kelas yang ditugaskan kepadanya.
        $query = Achievement::query()
            ->with([
                'student',
                'schoolClass',
                'academicYear',
            ])
            ->when(
                $selectedAcademicYearId,
                fn ($query) => $query->where('academic_year_id', $selectedAcademicYearId)
            );

        if ($teacherClassIds !== null) {
            $query->whereIn('class_id', $teacherClassIds);
        }

        if ($selectedClassId !== null) {
            $query->where('class_id', $selectedClassId);
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));

            $query->whereHas('student', function ($studentQuery) use ($search) {
                $studentQuery->where(function ($searchQuery) use ($search) {
                    $searchQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('nis', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                });
            });
        }

        $achievements = $query
            ->orderBy('class_id')
            ->orderBy('student_id')
            ->orderBy('id')
            ->get();

        return view('admin.achievement-reports.index', compact(
            'academicYears',
            'classes',
            'achievements',
            'selectedAcademicYearId',
            'selectedClassId'
        ));
    }
}
