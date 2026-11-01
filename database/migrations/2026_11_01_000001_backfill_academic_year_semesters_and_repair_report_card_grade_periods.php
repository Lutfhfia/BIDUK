<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        foreach (DB::table('academic_years')->orderBy('id')->get(['id']) as $academicYear) {
            $classIds = DB::table('classes')
                ->where('academic_year_id', $academicYear->id)
                ->pluck('id');

            foreach (['Ganjil', 'Genap'] as $name) {
                $semester = DB::table('semesters')
                    ->where('academic_year_id', $academicYear->id)
                    ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                    ->first(['id']);

                if (! $semester) {
                    $semesterId = DB::table('semesters')->insertGetId([
                        'academic_year_id' => $academicYear->id,
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    $semesterId = $semester->id;
                }

                if ($classIds->isEmpty()) {
                    continue;
                }

                $sourceSemesterIds = DB::table('semesters')
                    ->where('academic_year_id', '!=', $academicYear->id)
                    ->whereRaw('LOWER(name) = ?', [strtolower($name)])
                    ->pluck('id');

                foreach ($sourceSemesterIds as $sourceSemesterId) {
                    $gradeIds = DB::table('report_card_grades as source')
                        ->leftJoin('report_card_grades as target', function ($join) use ($semesterId) {
                            $join->on('target.student_id', '=', 'source.student_id')
                                ->on('target.class_id', '=', 'source.class_id')
                                ->on('target.subject_id', '=', 'source.subject_id')
                                ->where('target.semester_id', '=', $semesterId);
                        })
                        ->whereIn('source.class_id', $classIds)
                        ->where('source.semester_id', $sourceSemesterId)
                        ->whereNull('target.id')
                        ->pluck('source.id');

                    foreach ($gradeIds->chunk(500) as $gradeIdBatch) {
                        DB::table('report_card_grades')
                            ->whereIn('id', $gradeIdBatch)
                            ->update([
                                'semester_id' => $semesterId,
                                'updated_at' => now(),
                            ]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        // Backfilled semesters and corrected grade periods are retained on rollback.
    }
};
