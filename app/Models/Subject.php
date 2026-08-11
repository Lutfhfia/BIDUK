<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
