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
        Schema::create('student_physiques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained('semesters')->cascadeOnDelete();
            
            $table->decimal('height', 5, 2)->nullable(); // Tinggi Badan (cm)
            $table->decimal('weight', 5, 2)->nullable(); // Berat Badan (kg)
            
            $table->timestamps();
            
            // Satu murid hanya punya satu data per semester/tahun ajaran
            $table->unique(['student_id', 'academic_year_id', 'semester_id'], 'physique_unique_period');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_physiques');
    }
};
