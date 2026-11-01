<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentExtracurricular extends Model
{
    protected $fillable = [
        'student_id',
        'class_id',
        'academic_year_id',
        'position',
        'activity',
        'description',
        'description_2',
    ];
}
