<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Employee extends Model
{
    protected $fillable = [
        'nip',
        'nuptk',
        'name',
        'gender',
        'birth_place',
        'birth_date',
        'position',
        'employee_type',
        'employment_status',
        'phone',
        'email',
        'address',
        'photo',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    /**
     * Akun user yang terkait dengan pegawai ini.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * Kelas-kelas yang diwalikan oleh pegawai ini.
     */
    public function homeroomClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'homeroom_teacher_id');
    }

    /**
     * Scope: hanya pegawai aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }

    /**
     * Scope: hanya guru.
     */
    public function scopeTeachers($query)
    {
        return $query->where('employee_type', 'Guru');
    }
}
