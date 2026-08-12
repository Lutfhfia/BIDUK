<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nis',
        'nisn',
        'nik',
        'school_code', 'district_code', 'city_code', 'province_code', 'student_number',
        'name',
        'nickname',
        'gender',
        'birth_place',
        'birth_date',
        'religion',
        'citizenship', 'child_position', 'siblings_biological', 'siblings_step', 'siblings_adopted',
        'daily_language', 'blood_type',
        'address',
        'rt_rw', 'village', 'district', 'city', 'province', 'postal_code', 'phone_number',
        'living_with', 'distance_to_school',
        'kk_number',
        'father_name',
        'father_nik',
        'father_education',
        'father_job',
        'mother_name',
        'mother_nik',
        'mother_education',
        'mother_job',
        'guardian_name',
        'guardian_relation', 'guardian_education', 'guardian_job',
        'guardian_phone',
        'photo',
        'status',
    ];

    protected $attributes = [
        'siblings_biological' => 0,
        'siblings_step' => 0,
        'siblings_adopted' => 0,
    ];

    protected $casts = [
        'birth_date' => 'date',
        'siblings_biological' => 'integer',
        'siblings_step' => 'integer',
        'siblings_adopted' => 'integer',
    ];

    protected function normalizeSiblingValue($value): int
    {
        return $value === null || $value === '' ? 0 : (int) $value;
    }

    public function setSiblingsBiologicalAttribute($value)
    {
        $this->attributes['siblings_biological'] = $this->normalizeSiblingValue($value);
    }

    public function setSiblingsStepAttribute($value)
    {
        $this->attributes['siblings_step'] = $this->normalizeSiblingValue($value);
    }

    public function setSiblingsAdoptedAttribute($value)
    {
        $this->attributes['siblings_adopted'] = $this->normalizeSiblingValue($value);
    }

    /**
     * Kelas-kelas yang pernah/sedang ditempati siswa (melalui pivot class_student).
     */
    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_student', 'student_id', 'class_id')
                    ->withPivot(['status', 'entry_date', 'exit_date'])
                    ->withTimestamps();
    }

    /**
     * Record penempatan kelas siswa.
     */
    public function classAssignments(): HasMany
    {
        return $this->hasMany(ClassStudent::class);
    }

    public function previousEducation(): HasOne
    {
        return $this->hasOne(StudentPreviousEducation::class);
    }

    public function physiques(): HasMany
    {
        return $this->hasMany(StudentPhysique::class);
    }

    public function healths(): HasMany
    {
        return $this->hasMany(StudentHealth::class);
    }

    /**
     * Kelas aktif saat ini (berdasarkan tahun ajaran aktif).
     */
    public function currentClass()
    {
        return $this->belongsToMany(SchoolClass::class, 'class_student', 'student_id', 'class_id')
                    ->withPivot(['status', 'entry_date', 'exit_date'])
                    ->wherePivot('status', 'Aktif')
                    ->whereHas('academicYear', function ($query) {
                        $query->where('status', 'active');
                    });
    }

    /**
     * Scope: pencarian berdasarkan nama, NIS, atau NISN.
     */
    public function scopeSearch($query, ?string $search)
    {
        if ($search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }
        return $query;
    }

    /**
     * Scope: filter berdasarkan status siswa.
     */
    public function scopeFilterStatus($query, ?string $status)
    {
        if ($status) {
            return $query->where('status', $status);
        }
        return $query;
    }

    /**
     * Scope: filter berdasarkan kelas (melalui class_student).
     */
    public function scopeFilterClass($query, ?int $classId)
    {
        if ($classId) {
            return $query->whereHas('classes', function ($q) use ($classId) {
                $q->where('classes.id', $classId);
            });
        }
        return $query;
    }

    /**
     * Scope: hanya siswa aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Mendapatkan nama kelas aktif saat ini.
     */
    public function getCurrentClassNameAttribute(): ?string
    {
        $currentClass = $this->currentClass()->first();
        return $currentClass?->name;
    }
}
