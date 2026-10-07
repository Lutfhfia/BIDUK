<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Excel asli dapat mempunyai NIS/NISN kosong.
            $table->string('nis', 20)->nullable()->change();
            $table->string('nisn', 20)->nullable()->change();

            $columns = [
                'rt' => fn () => $table->string('rt', 10)->nullable()->after('rt_rw'),
                'rw' => fn () => $table->string('rw', 10)->nullable()->after('rt'),
                'diseases' => fn () => $table->text('diseases')->nullable(),
                'immunizations' => fn () => $table->text('immunizations')->nullable(),

                'father_birth_place' => fn () => $table->string('father_birth_place', 100)->nullable(),
                'father_birth_date' => fn () => $table->date('father_birth_date')->nullable(),
                'father_address' => fn () => $table->text('father_address')->nullable(),
                'father_phone' => fn () => $table->string('father_phone', 30)->nullable(),
                'father_citizenship' => fn () => $table->string('father_citizenship', 50)->nullable(),

                'mother_birth_place' => fn () => $table->string('mother_birth_place', 100)->nullable(),
                'mother_birth_date' => fn () => $table->date('mother_birth_date')->nullable(),
                'mother_address' => fn () => $table->text('mother_address')->nullable(),
                'mother_phone' => fn () => $table->string('mother_phone', 30)->nullable(),
                'mother_citizenship' => fn () => $table->string('mother_citizenship', 50)->nullable(),

                'guardian_birth_place' => fn () => $table->string('guardian_birth_place', 100)->nullable(),
                'guardian_birth_date' => fn () => $table->date('guardian_birth_date')->nullable(),
                'guardian_address' => fn () => $table->text('guardian_address')->nullable(),
                'guardian_citizenship' => fn () => $table->string('guardian_citizenship', 50)->nullable(),

                'student_origin' => fn () => $table->string('student_origin', 100)->nullable(),
                'scholarship_type' => fn () => $table->string('scholarship_type', 100)->nullable(),

                'graduation_year' => fn () => $table->string('graduation_year', 20)->nullable(),
                'graduation_certificate_number' => fn () => $table->string('graduation_certificate_number', 100)->nullable(),
                'continued_school' => fn () => $table->string('continued_school', 150)->nullable(),

                'transfer_left_grade' => fn () => $table->string('transfer_left_grade', 50)->nullable(),
                'transfer_to_school' => fn () => $table->string('transfer_to_school', 150)->nullable(),
                'transfer_to_grade' => fn () => $table->string('transfer_to_grade', 50)->nullable(),
                'transfer_date' => fn () => $table->date('transfer_date')->nullable(),

                'leaving_date' => fn () => $table->date('leaving_date')->nullable(),
                'leaving_reason' => fn () => $table->text('leaving_reason')->nullable(),
            ];

            foreach ($columns as $name => $callback) {
                if (!Schema::hasColumn('students', $name)) {
                    $callback();
                }
            }
        });
    }

    public function down(): void
    {
        $columns = [
            'rt', 'rw', 'diseases', 'immunizations',
            'father_birth_place', 'father_birth_date', 'father_address', 'father_phone', 'father_citizenship',
            'mother_birth_place', 'mother_birth_date', 'mother_address', 'mother_phone', 'mother_citizenship',
            'guardian_birth_place', 'guardian_birth_date', 'guardian_address', 'guardian_citizenship',
            'student_origin', 'scholarship_type',
            'graduation_year', 'graduation_certificate_number', 'continued_school',
            'transfer_left_grade', 'transfer_to_school', 'transfer_to_grade', 'transfer_date',
            'leaving_date', 'leaving_reason',
        ];

        Schema::table('students', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn('students', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
