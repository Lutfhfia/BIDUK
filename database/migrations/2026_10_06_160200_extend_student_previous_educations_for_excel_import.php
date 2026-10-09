<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_previous_educations', function (Blueprint $table) {
            $columns = [
                'student_origin' => fn () => $table->string('student_origin', 100)->nullable(),
                'kindergarten_name' => fn () => $table->string('kindergarten_name', 150)->nullable(),
                'kindergarten_address' => fn () => $table->text('kindergarten_address')->nullable(),
                'sttb_date' => fn () => $table->date('sttb_date')->nullable(),
                'sttb_number' => fn () => $table->string('sttb_number', 100)->nullable(),
                'transfer_to_class' => fn () => $table->string('transfer_to_class', 50)->nullable(),
            ];

            foreach ($columns as $name => $callback) {
                if (!Schema::hasColumn('student_previous_educations', $name)) {
                    $callback();
                }
            }
        });
    }

    public function down(): void
    {
        $columns = [
            'student_origin',
            'kindergarten_name',
            'kindergarten_address',
            'sttb_date',
            'sttb_number',
            'transfer_to_class',
        ];

        Schema::table('student_previous_educations', function (Blueprint $table) use ($columns) {
            foreach ($columns as $column) {
                if (Schema::hasColumn('student_previous_educations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
