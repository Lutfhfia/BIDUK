<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_extracurriculars', function (Blueprint $table) {
            $table->text('description_2')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('student_extracurriculars', function (Blueprint $table) {
            $table->dropColumn('description_2');
        });
    }
};
