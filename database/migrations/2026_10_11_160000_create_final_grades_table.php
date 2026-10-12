<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel final_grades menyimpan data Nilai Akhir/Ijazah
     * untuk siswa kelas 6 semester genap.
     *
     * Setiap baris menyimpan:
     * - Nilai ujian (praktik + tertulis) yang diinput manual
     * - Capaian pembelajaran (nilai sekolah & nilai akhir)
     * - Nilai sekolah (rata-rata 4 semester) dihitung otomatis
     * - Nilai ujian sekolah dihitung otomatis
     * - Nilai akhir dihitung otomatis (60% sekolah + 40% ujian)
     * - Status kelulusan
     */
    public function up(): void
    {
        Schema::create('final_grades', function (Blueprint $table) {
            $table->id();

            // Siswa
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            // Kelas 6 (kelas saat ini)
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            // Mata pelajaran ijazah (merujuk ke subjects)
            $table->foreignId('subject_id')
                ->constrained('subjects')
                ->cascadeOnDelete();

            // Tahun ajaran kelulusan
            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->cascadeOnDelete();

            // ===== NILAI YANG DIHITUNG OTOMATIS =====

            // Rata-rata nilai rapor 4 semester (kelas 5 ganjil, genap, kelas 6 ganjil, genap)
            $table->decimal('school_score', 5, 2)->nullable()
                ->comment('Nilai Sekolah: rata-rata 4 semester');

            // ===== NILAI INPUT MANUAL =====

            // Nilai ujian praktik
            $table->decimal('practical_score', 5, 2)->nullable()
                ->comment('Nilai Ujian Praktik');

            // Nilai ujian tertulis
            $table->decimal('written_score', 5, 2)->nullable()
                ->comment('Nilai Ujian Tertulis');

            // ===== NILAI YANG DIHITUNG OTOMATIS =====

            // Nilai Ujian Sekolah = (praktik + tertulis) / 2
            $table->decimal('exam_score', 5, 2)->nullable()
                ->comment('Nilai Ujian Sekolah: (praktik + tertulis) / 2');

            // Nilai Akhir = (school_score * 0.60) + (exam_score * 0.40)
            $table->decimal('final_score', 5, 2)->nullable()
                ->comment('Nilai Akhir: 60% sekolah + 40% ujian');

            // ===== CAPAIAN PEMBELAJARAN =====

            // Capaian pembelajaran untuk Nilai Sekolah
            $table->text('school_learning_outcome')->nullable()
                ->comment('Capaian Pembelajaran Nilai Sekolah');

            // Capaian pembelajaran untuk Nilai Akhir
            $table->text('final_learning_outcome')->nullable()
                ->comment('Capaian Pembelajaran Nilai Akhir');

            $table->timestamps();

            // Satu siswa hanya memiliki satu nilai akhir
            // untuk satu mata pelajaran pada satu tahun ajaran
            $table->unique(
                ['student_id', 'class_id', 'subject_id', 'academic_year_id'],
                'final_grades_unique'
            );
        });

        // ===== TABEL STATUS KELULUSAN =====
        Schema::create('graduation_statuses', function (Blueprint $table) {
            $table->id();

            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->cascadeOnDelete();

            // Status: LULUS / TIDAK LULUS
            $table->enum('status', ['LULUS', 'TIDAK LULUS'])->nullable();

            // Tanggal penetapan
            $table->date('graduation_date')->nullable();

            // Nomor ijazah (opsional)
            $table->string('certificate_number')->nullable();

            // Nomor SKHUS (opsional)
            $table->string('skhus_number')->nullable();

            // User yang menetapkan status
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->unique(
                ['student_id', 'class_id', 'academic_year_id'],
                'graduation_statuses_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('graduation_statuses');
        Schema::dropIfExists('final_grades');
    }
};
