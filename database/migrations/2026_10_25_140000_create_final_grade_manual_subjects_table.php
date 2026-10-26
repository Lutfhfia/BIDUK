<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_grade_manual_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('slot');
            $table->string('subject_name', 150)->nullable();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamps();

            $table->unique(
                ['student_id', 'class_id', 'academic_year_id', 'slot'],
                'final_grade_manual_subjects_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_grade_manual_subjects');
    }
};
