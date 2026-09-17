<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('report_card_grades', function (Blueprint $table) {
            $table->id();

            // Siswa
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // Kelas
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            // Mata pelajaran
            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnDelete();

            // Semester
            $table->foreignId('semester_id')
                ->constrained('semesters')
                ->cascadeOnDelete();

            // Nilai
            $table->decimal('score', 5, 2)->nullable();

            // Catatan tambahan
            $table->text('notes')->nullable();

            $table->timestamps();

            // Satu siswa hanya boleh memiliki
            // satu nilai untuk mapel yang sama
            // pada kelas dan semester yang sama.
            $table->unique([
                'student_id',
                'class_id',
                'subject_id',
                'semester_id'
            ], 'report_card_grades_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_card_grades');
    }
};
