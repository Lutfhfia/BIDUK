<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_extracurriculars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('position');
            $table->string('activity')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(
                ['student_id', 'class_id', 'academic_year_id', 'position'],
                'student_extracurriculars_record_position_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_extracurriculars');
    }
};
