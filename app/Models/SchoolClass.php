<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolClass extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'academic_year_id',
        'name',
        'grade_level',
        'homeroom_teacher_id',
        'status',
    ];

    /**
     * Tahun ajaran yang terkait dengan kelas ini.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * Wali kelas (pegawai).
     */
    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'homeroom_teacher_id');
    }

    /**
     * Siswa-siswa dalam kelas ini (melalui pivot class_student).
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'class_student', 'class_id', 'student_id')
                    ->withPivot(['status', 'entry_date', 'exit_date'])
                    ->withTimestamps();
    }

    /**
     * Record penempatan siswa di kelas ini.
     */
    public function classStudents(): HasMany
    {
        return $this->hasMany(ClassStudent::class, 'class_id');
    }

    /**
     * Scope: hanya kelas aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Scope: kelas pada tahun ajaran aktif.
     */
    public function scopeCurrentYear($query)
    {
        return $query->whereHas('academicYear', function ($q) {
            $q->where('status', 'active');
        });
    }

    /**
     * Jumlah siswa aktif di kelas.
     */
    public function getStudentCountAttribute(): int
    {
        return $this->students()->wherePivot('status', 'Aktif')->count();
    }
}
