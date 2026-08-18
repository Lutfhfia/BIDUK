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
        Schema::create('student_healths', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            
            $table->string('hearing', 100)->nullable(); // Pendengaran
            $table->string('vision', 100)->nullable(); // Penglihatan
            $table->string('teeth', 100)->nullable(); // Gigi
            $table->text('notes')->nullable(); // Keterangan lain
            
            $table->timestamps();
            
            $table->unique(['student_id', 'academic_year_id', 'semester_id'], 'health_unique_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_healths');
    }
};
