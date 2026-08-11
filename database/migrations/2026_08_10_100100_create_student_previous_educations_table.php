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
        Schema::create('student_previous_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            
            // Asal Sekolah
            $table->string('previous_school_name', 100)->nullable();
            $table->string('certificate_date_number', 100)->nullable(); // Tanggal dan Nomor Ijazah/STTB
            
            // Pindahan
            $table->string('transfer_from_school', 100)->nullable();
            $table->string('transfer_from_grade', 50)->nullable();
            $table->date('transfer_accepted_date')->nullable();
            $table->string('transfer_letter_number', 100)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_previous_educations');
    }
};
