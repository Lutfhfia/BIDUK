<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassStudent extends Model
{
    protected $table = 'class_student';

    protected $fillable = [
        'class_id',
        'student_id',
        'status',
        'entry_date',
        'exit_date',
    ];

    protected function casts(): array
    {
        return [
            'entry_date' => 'date',
            'exit_date' => 'date',
        ];
    }

    /**
     * Kelas yang terkait.
     */
    public function schoolClass(): BelongsTo
    {
        return $this->belongsTo(SchoolClass::class, 'class_id');
    }

    /**
     * Siswa yang terkait.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
