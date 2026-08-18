<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentPreviousEducation extends Model
{
    protected $table = 'student_previous_educations';

    protected $fillable = [
        'student_id', 'previous_school_name', 'certificate_date_number',
        'transfer_from_school', 'transfer_from_grade', 'transfer_accepted_date', 'transfer_letter_number',
    ];

    protected function casts(): array { return ['transfer_accepted_date' => 'date']; }
}
