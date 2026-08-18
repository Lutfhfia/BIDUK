<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentHealth extends Model
{
    protected $fillable = ['student_id', 'academic_year_id', 'semester_id', 'hearing', 'vision', 'teeth', 'notes'];

    public function academicYear(): BelongsTo { return $this->belongsTo(AcademicYear::class); }
    public function semester(): BelongsTo { return $this->belongsTo(Semester::class); }
}
