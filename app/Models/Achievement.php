<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Achievement extends Model
{
    use HasFactory;

    /**
     * Kolom yang boleh diisi menggunakan create()
     * atau update().
     */
    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year_id',
        'type',
        'level',
        'description',
    ];

    /**
     * Relasi ke siswa.
     */
    public function student()
    {
        return $this->belongsTo(
            Student::class,
            'student_id'
        );
    }

    /**
     * Relasi ke kelas.
     */
    public function schoolClass()
    {
        return $this->belongsTo(
            SchoolClass::class,
            'class_id'
        );
    }

    /**
     * Relasi ke tahun ajaran.
     */
    public function academicYear()
    {
        return $this->belongsTo(
            AcademicYear::class,
            'academic_year_id'
        );
    }
}
