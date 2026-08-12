<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Semester extends Model
{
    protected $fillable = [
        'academic_year_id',
        'name',
    ];

    /**
     * Tahun ajaran yang terkait.
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public static function forActiveAcademicYear(): ?self
    {
        $year = AcademicYear::getActive();
        if (! $year) return null;

        $name = now()->month >= 7 ? 'Ganjil' : 'Genap';
        return static::firstOrCreate(['academic_year_id' => $year->id, 'name' => $name]);
    }
}
