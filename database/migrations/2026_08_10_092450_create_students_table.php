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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            // Identitas Pokok
            $table->string('nis', 20)->unique();
            $table->string('nisn', 20)->unique();
            $table->string('nik', 20)->nullable();
            
            // Identitas Sekolah & Wilayah
            $table->string('school_code', 50)->nullable();
            $table->string('district_code', 50)->nullable();
            $table->string('city_code', 50)->nullable();
            $table->string('province_code', 50)->nullable();
            $table->string('student_number', 20)->nullable(); // Nomor Urut

            // Biodata
            $table->string('name', 100);
            $table->string('nickname', 50)->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place', 100)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion', 30)->nullable();
            $table->string('citizenship', 50)->default('WNI');
            
            // Data Saudara
            $table->integer('child_position')->nullable(); // Anak ke-
            $table->integer('siblings_biological')->default(0);
            $table->integer('siblings_step')->default(0);
            $table->integer('siblings_adopted')->default(0);
            
            // Data Tambahan
            $table->string('daily_language', 50)->nullable();
            $table->string('blood_type', 5)->nullable();
            
            // Alamat & Kontak
            $table->text('address')->nullable();
            $table->string('rt_rw', 20)->nullable();
            $table->string('village', 50)->nullable();
            $table->string('district', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('province', 50)->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone_number', 20)->nullable(); // No telepon siswa
            $table->string('living_with', 50)->nullable();
            $table->string('distance_to_school', 30)->nullable();

            $table->string('kk_number', 20)->nullable();
            
            // Data Orang Tua
            $table->string('father_name', 100)->nullable();
            $table->string('father_nik', 20)->nullable();
            $table->string('father_education', 50)->nullable();
            $table->string('father_job', 100)->nullable();
            
            $table->string('mother_name', 100)->nullable();
            $table->string('mother_nik', 20)->nullable();
            $table->string('mother_education', 50)->nullable();
            $table->string('mother_job', 100)->nullable();
            
            $table->string('guardian_name', 100)->nullable();
            $table->string('guardian_relation', 50)->nullable();
            $table->string('guardian_education', 50)->nullable();
            $table->string('guardian_job', 100)->nullable();
            $table->string('guardian_phone', 20)->nullable();
            
            // Lainnya
            $table->string('photo')->nullable();
            $table->enum('status', ['Aktif', 'Pindah', 'Lulus', 'Alumni', 'Nonaktif'])->default('Aktif');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
