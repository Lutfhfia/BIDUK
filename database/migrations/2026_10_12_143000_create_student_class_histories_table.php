<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_class_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('grade_level');
            $table->string('academic_year', 20);
            $table->string('rombel', 50);
            $table->string('final_status', 30);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['student_id', 'grade_level']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_class_histories');
    }
};
