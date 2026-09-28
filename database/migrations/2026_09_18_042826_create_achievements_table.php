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
        Schema::create('achievements', function (Blueprint $table) {

            $table->id();

            /*
             * Siswa yang memperoleh prestasi
             */
            $table->foreignId('student_id')
                ->constrained('students')
                ->cascadeOnDelete();

            /*
             * Kelas siswa saat prestasi dicatat
             */
            $table->foreignId('class_id')
                ->constrained('classes')
                ->cascadeOnDelete();

            /*
             * Tahun ajaran
             */
            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->cascadeOnDelete();

            /*
             * Jenis / nama prestasi
             *
             * Contoh:
             * - Juara 1 Lomba Cerdas Cermat
             * - Juara 2 Olimpiade Matematika
             * - Juara 3 Lomba Baca Puisi
             */
            $table->string('type');

            /*
             * Tingkat prestasi
             *
             * Contoh:
             * Sekolah
             * Kecamatan
             * Kota
             * Provinsi
             * Nasional
             * Internasional
             */
            $table->string('level', 50)->nullable();

            /*
             * Keterangan prestasi
             */
            $table->text('description')->nullable();

            $table->timestamps();

            /*
             * Index untuk mempercepat pencarian
             * berdasarkan siswa, kelas dan tahun ajaran.
             */
            $table->index([
                'student_id',
                'class_id',
                'academic_year_id'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
