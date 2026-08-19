<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $fillable = [
    'name',
    'start_date',
    'end_date',
    'status',
    'current_semester',
];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    /**
     * Semester-semester pada tahun ajaran ini.
     */
    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class);
    }

    /**
     * Kelas-kelas pada tahun ajaran ini.
     */
    public function classes(): HasMany
    {
        return $this->hasMany(SchoolClass::class);
    }

    /**
     * Scope: hanya tahun ajaran aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Mendapatkan tahun ajaran aktif.
     */
    public static function getActive(): ?self
    {
        return static::active()->first();
    }
}
