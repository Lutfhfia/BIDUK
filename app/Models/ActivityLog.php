<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    private const MODULE_LABELS = [
        'academic_years' => 'Tahun Ajaran',
        'attendance' => 'Absensi',
        'authentication' => 'Autentikasi',
        'classes' => 'Kelas',
        'employees' => 'Pegawai',
        'report_card_grades' => 'Nilai Rapor',
        'school_profile' => 'Profil Sekolah',
        'student_class_histories' => 'Riwayat Kelas Siswa',
        'students' => 'Siswa',
        'subjects' => 'Mata Pelajaran',
        'users' => 'Pengguna',
    ];

    private const ACTION_LABELS = [
        'created' => 'Ditambahkan',
        'updated' => 'Diperbarui',
        'deleted' => 'Dihapus',
        'imported' => 'Diimpor',
        'login' => 'Login',
        'logout' => 'Keluar',
        'students_added' => 'Siswa Ditambahkan',
    ];

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'subject_type',
        'subject_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
        ];
    }

    public function getModuleLabelAttribute(): string
    {
        return self::MODULE_LABELS[$this->module]
            ?? ucwords(str_replace('_', ' ', $this->module ?? ''));
    }

    public function getActionLabelAttribute(): string
    {
        return self::ACTION_LABELS[$this->action]
            ?? ucfirst(str_replace('_', ' ', $this->action ?? ''));
    }

    /**
     * Pengguna yang melakukan aktivitas.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Data/model yang terkena aktivitas.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}