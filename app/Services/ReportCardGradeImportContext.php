<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Semester;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReportCardGradeImportContext
{
    public function options(User $user): array
    {
        if (! $this->canImport($user)) {
            return [
                'can_import' => false,
                'is_teacher' => false,
                'academic_years' => collect(),
                'classes' => collect(),
                'semesters' => collect(),
                'active_academic_year_id' => null,
            ];
        }

        $isTeacher = $user->isGuru();
        $activeAcademicYear = AcademicYear::active()->first();

        $academicYears = $isTeacher
            ? ($activeAcademicYear ? collect([$activeAcademicYear]) : collect())
            : AcademicYear::query()->orderByDesc('start_date')->get();

        $classes = SchoolClass::query()
            ->where('status', 'Aktif')
            ->when($isTeacher, function ($query) use ($user, $activeAcademicYear) {
                if (! $user->employee_id || ! $activeAcademicYear) {
                    $query->whereRaw('1 = 0');

                    return;
                }

                $query->where('homeroom_teacher_id', $user->employee_id)
                    ->where('academic_year_id', $activeAcademicYear->id);
            })
            ->with('academicYear')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();

        return [
            'can_import' => true,
            'is_teacher' => $isTeacher,
            'academic_years' => $academicYears,
            'classes' => $classes,
            'semesters' => Semester::query()
                ->whereIn('academic_year_id', $academicYears->pluck('id'))
                ->orderBy('name')
                ->get(),
            'active_academic_year_id' => $activeAcademicYear?->id,
        ];
    }

    public function validateSelection(User $user, array $input): array
    {
        abort_unless($this->canImport($user), 403, 'Akses import nilai rapor tidak diizinkan.');

        $validated = Validator::make($input, [
            'academic_year_id' => ['required', 'integer', 'exists:academic_years,id'],
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
        ])->validate();

        $academicYear = AcademicYear::findOrFail($validated['academic_year_id']);
        $class = SchoolClass::query()
            ->where('academic_year_id', $academicYear->id)
            ->where('status', 'Aktif')
            ->findOrFail($validated['class_id']);
        $semester = Semester::query()
            ->where('academic_year_id', $academicYear->id)
            ->findOrFail($validated['semester_id']);

        if ($user->isGuru()) {
            $activeAcademicYear = AcademicYear::active()->first();
            if (
                ! $user->employee_id
                ||
                ! $activeAcademicYear
                || $academicYear->isNot($activeAcademicYear)
                || $class->homeroom_teacher_id !== $user->employee_id
            ) {
                throw ValidationException::withMessages([
                    'class_id' => 'Guru hanya dapat mengimpor nilai untuk kelas wali pada tahun ajaran aktif.',
                ]);
            }
        }

        return [
            'academic_year' => $academicYear,
            'class' => $class,
            'semester' => $semester,
        ];
    }

    private function canImport(User $user): bool
    {
        return $user->isSuperAdmin() || $user->isAdmin() || $user->isGuru();
    }
}
