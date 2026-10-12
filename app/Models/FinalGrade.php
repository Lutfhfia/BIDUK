<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalGrade extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'subject_id',
        'academic_year_id',
        'school_score',
        'practical_score',
        'written_score',
        'exam_score',
        'final_score',
        'school_learning_outcome',
        'final_learning_outcome',
    ];

    protected $casts = [
        'school_score'    => 'decimal:2',
        'practical_score' => 'decimal:2',
        'written_score'   => 'decimal:2',
        'exam_score'      => 'decimal:2',
        'final_score'     => 'decimal:2',
    ];

    /**
     * Siswa pemilik nilai akhir.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Kelas 6 saat nilai akhir ditetapkan.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Mata pelajaran ijazah.
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Tahun ajaran kelulusan.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }
}
