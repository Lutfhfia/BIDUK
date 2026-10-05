<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique('students_nis_unique');
            $table->dropUnique('students_nisn_unique');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unique('nis', 'students_nis_unique');
            $table->unique('nisn', 'students_nisn_unique');
        });
    }
};
