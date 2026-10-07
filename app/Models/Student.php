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

    /**
     * Template Identitas Administratif Sekolah SD Negeri 204 Palembang
     */
    public const DEFAULT_SCHOOL_CODE = '10604338';
    public const DEFAULT_DISTRICT_CODE = '16.71.04';
    public const DEFAULT_CITY_CODE = '16.71';
    public const DEFAULT_PROVINCE_CODE = '16';

    protected $fillable = [
        'nis', 'nisn', 'nik',
        'school_code', 'district_code', 'city_code', 'province_code', 'student_number',
        'name', 'nickname', 'gender', 'birth_place', 'birth_date', 'religion', 'citizenship',
        'child_position', 'siblings_biological', 'siblings_step', 'siblings_adopted',
        'daily_language', 'blood_type',
        'address', 'rt_rw', 'rt', 'rw', 'village', 'district', 'city', 'province',
        'postal_code', 'phone_number', 'living_with', 'distance_to_school', 'kk_number',
        'diseases', 'immunizations',

        'father_name', 'father_nik', 'father_birth_place', 'father_birth_date',
        'father_education', 'father_job', 'father_address', 'father_phone', 'father_citizenship',

        'mother_name', 'mother_nik', 'mother_birth_place', 'mother_birth_date',
        'mother_education', 'mother_job', 'mother_address', 'mother_phone', 'mother_citizenship',

        'guardian_name', 'guardian_relation', 'guardian_birth_place', 'guardian_birth_date',
        'guardian_education', 'guardian_job', 'guardian_address', 'guardian_phone',
        'guardian_citizenship',

        'student_origin', 'scholarship_type',
        'graduation_year', 'graduation_certificate_number', 'continued_school',
        'transfer_left_grade', 'transfer_to_school', 'transfer_to_grade', 'transfer_date',
        'leaving_date', 'leaving_reason',
        'photo', 'status',
    ];

    protected $attributes = [
        'siblings_biological' => 0,
        'siblings_step' => 0,
        'siblings_adopted' => 0,
    ];

    protected $casts = [
        'birth_date' => 'date',
        'father_birth_date' => 'date',
        'mother_birth_date' => 'date',
        'guardian_birth_date' => 'date',
        'transfer_date' => 'date',
        'leaving_date' => 'date',
        'siblings_biological' => 'integer',
        'siblings_step' => 'integer',
        'siblings_adopted' => 'integer',
    ];

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_student', 'student_id', 'class_id')
            ->withPivot(['status', 'entry_date', 'exit_date'])
            ->withTimestamps();
    }

    public function currentClass(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_student', 'student_id', 'class_id')
            ->whereHas('academicYear', fn ($q) => $q->where('status', 'active'))
            ->wherePivot('status', 'Aktif')
            ->withPivot(['status', 'entry_date', 'exit_date'])
            ->withTimestamps();
    }

    public function getCurrentClassNameAttribute(): ?string
    {
        return $this->currentClass()->first()?->name
            ?? $this->classes()->wherePivot('status', 'Aktif')->first()?->name
            ?? $this->classes()->first()?->name;
    }

    public function getSchoolCodeAttribute(?string $value): string
    {
        return !empty($value) ? $value : self::DEFAULT_SCHOOL_CODE;
    }

    public function getDistrictCodeAttribute(?string $value): string
    {
        return !empty($value) ? $value : self::DEFAULT_DISTRICT_CODE;
    }

    public function getCityCodeAttribute(?string $value): string
    {
        return !empty($value) ? $value : self::DEFAULT_CITY_CODE;
    }

    public function getProvinceCodeAttribute(?string $value): string
    {
        return !empty($value) ? $value : self::DEFAULT_PROVINCE_CODE;
    }

    public function getOrderNumberAttribute(): ?string
    {
        return $this->student_number;
    }

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
     * Scope pencarian berdasarkan nama, NIS, atau NISN.
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

public function scopeFilterStatus($query, ?string $status)
{
    if ($status) {
        return $query->where('status', $status);
    }

    return $query;
}

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
}
