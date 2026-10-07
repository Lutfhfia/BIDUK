<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPreviousEducation extends Model
{
    protected $table = 'student_previous_educations';

    protected $fillable = [
        'student_id',
        'student_origin',
        'previous_school_name',
        'certificate_date_number',
        'kindergarten_name',
        'kindergarten_address',
        'sttb_date',
        'sttb_number',
        'transfer_from_school',
        'transfer_from_grade',
        'transfer_accepted_date',
        'transfer_to_class',
        'transfer_letter_number',
    ];

    protected $casts = [
        'sttb_date' => 'date',
        'transfer_accepted_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
