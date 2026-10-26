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
                'default_academic_year_id' => null,
            ];
        }

        $isTeacher = $user->isGuru();
        $activeAcademicYear = AcademicYear::active()->first();
        $teacherClasses = $isTeacher
            ? SchoolClass::query()
                ->where(function ($query) use ($user) {
                    $query->where('homeroom_teacher_user_id', $user->id);
                    if ($user->employee_id) {
                        $query->orWhere('homeroom_teacher_id', $user->employee_id);
                    }
                })
                ->get(['id', 'academic_year_id'])
            : collect();

        $academicYears = $isTeacher
            ? AcademicYear::query()
                ->whereIn('id', $teacherClasses->pluck('academic_year_id')->unique())
                ->orderByDesc('start_date')
                ->orderByDesc('id')
                ->get()
            : AcademicYear::query()->orderByDesc('start_date')->orderByDesc('id')->get();

        $classes = SchoolClass::query()
            ->when($isTeacher, function ($query) use ($user) {
                $query->where(function ($teacherQuery) use ($user) {
                    $teacherQuery->where('homeroom_teacher_user_id', $user->id);
                    if ($user->employee_id) {
                        $teacherQuery->orWhere('homeroom_teacher_id', $user->employee_id);
                    }
                });
            })
            ->when(! $isTeacher, fn ($query) => $query->where('status', 'Aktif'))
            ->with('academicYear')
            ->orderBy('grade_level')
            ->orderBy('name')
            ->get();
        $defaultAcademicYearId = $academicYears->contains('id', $activeAcademicYear?->id)
            ? $activeAcademicYear?->id
            : $academicYears->first()?->id;

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
            'default_academic_year_id' => $defaultAcademicYearId,
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
        $classQuery = SchoolClass::query()
            ->where('academic_year_id', $academicYear->id);
        if ($user->isGuru()) {
            $classQuery->where(function ($query) use ($user) {
                $query->where('homeroom_teacher_user_id', $user->id);
                if ($user->employee_id) {
                    $query->orWhere('homeroom_teacher_id', $user->employee_id);
                }
            });
        } else {
            $classQuery->where('status', 'Aktif');
        }
        $class = $classQuery->find($validated['class_id']);
        if (! $class) {
            throw ValidationException::withMessages([
                'class_id' => $user->isGuru()
                    ? 'Guru hanya dapat mengelola nilai kelas yang tercatat sebagai kelas wali pada tahun ajaran tersebut.'
                    : 'Kelas yang dipilih tidak tersedia.',
            ]);
        }
        $semester = Semester::query()
            ->where('academic_year_id', $academicYear->id)
            ->findOrFail($validated['semester_id']);

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
