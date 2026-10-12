<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GraduationStatus extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year_id',
        'status',
        'graduation_date',
        'certificate_number',
        'skhus_number',
        'approved_by',
    ];

    protected $casts = [
        'graduation_date' => 'date',
    ];

    /**
     * Siswa.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Kelas.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Tahun ajaran.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    /**
     * User yang menyetujui kelulusan.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
