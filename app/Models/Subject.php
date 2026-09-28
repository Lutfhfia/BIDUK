<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SchoolClass;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'status',
    ];

    /**
     * Scope: hanya mata pelajaran aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    public function classes(): BelongsToMany
{
    return $this->belongsToMany(
        SchoolClass::class,
        'class_subject',
        'subject_id',
        'class_id'
    )->withTimestamps();
}

public function reportCardGrades(): HasMany
{
    return $this->hasMany(ReportCardGrade::class);
}


}
