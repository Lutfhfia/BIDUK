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
        Schema::table('class_student', function (Blueprint $table) {
            if (!Schema::hasColumn('class_student', 'final_status')) {
                $table->string('final_status')->nullable();
            }

            if (!Schema::hasColumn('class_student', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('class_student', function (Blueprint $table) {
            if (Schema::hasColumn('class_student', 'final_status')) {
                $table->dropColumn('final_status');
            }

            if (Schema::hasColumn('class_student', 'notes')) {
                $table->dropColumn('notes');
            }
        });
    }
};
