<?php

namespace App\Services;

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TeacherClassAccess
{
    /**
     * Returns assigned class IDs for teachers; null for other roles.
     */
    public function classIds(?User $user): ?Collection
    {
        if (!$user || !$user->isGuru()) {
            return null;
        }

        return SchoolClass::query()
            ->where(function (Builder $query) use ($user) {
                $query->where('homeroom_teacher_user_id', $user->id);

                if ($user->employee_id) {
                    $query->orWhere(function (Builder $legacyQuery) use ($user) {
                        $legacyQuery
                            ->whereNull('homeroom_teacher_user_id')
                            ->where('homeroom_teacher_id', $user->employee_id);
                    });
                }
            })
            ->pluck('id');
    }

    /**
     * Restricts a class query to the current teacher's assignments.
     */
    public function restrictClassQuery(Builder $query, User $user): Builder
    {
        $classIds = $this->classIds($user);

        if ($classIds !== null) {
            $query->whereIn('classes.id', $classIds);
        }

        return $query;
    }

    public function assertCanAccessClass(?User $user, SchoolClass $class): void
    {
        $classIds = $this->classIds($user);

        if ($classIds !== null) {
            abort_unless(
                $classIds->contains((int) $class->id),
                403,
                'Anda hanya dapat mengakses laporan kelas yang menjadi wali kelas Anda.'
            );
        }
    }
}
