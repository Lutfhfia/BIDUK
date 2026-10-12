<?php

use App\Models\SchoolClass;
use App\Services\FinalGradeService;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Daftarkan mata pelajaran bawaan untuk kelas 5 dan kelas 6
     * sesuai spesifikasi dokumen guru.
     */
    public function up(): void
    {
        $service = new FinalGradeService();
        $mapping = $service->getIjazahSubjectMapping();
        $defaultSubjectIds = $mapping->pluck('subject_id')->filter()->values()->all();

        if (empty($defaultSubjectIds)) {
            return;
        }

        // Hubungkan mata pelajaran bawaan ke semua kelas tingkat 5 dan tingkat 6
        $classes = SchoolClass::whereIn('grade_level', [5, 6])->get();
        foreach ($classes as $class) {
            $class->subjects()->syncWithoutDetaching($defaultSubjectIds);
        }
    }

    public function down(): void
    {
        // Tidak perlu menghapus relasi secara destruktif
    }
};
