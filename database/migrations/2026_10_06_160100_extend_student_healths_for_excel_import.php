<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_healths', function (Blueprint $table) {
            if (!Schema::hasColumn('student_healths', 'diseases')) {
                $table->text('diseases')->nullable()->after('teeth');
            }

            if (!Schema::hasColumn('student_healths', 'immunizations')) {
                $table->text('immunizations')->nullable()->after('diseases');
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_healths', function (Blueprint $table) {
            if (Schema::hasColumn('student_healths', 'diseases')) {
                $table->dropColumn('diseases');
            }
            if (Schema::hasColumn('student_healths', 'immunizations')) {
                $table->dropColumn('immunizations');
            }
        });
    }
};
