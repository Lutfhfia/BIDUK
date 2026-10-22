<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_card_grades', function (Blueprint $table) {
            $table->string('knowledge_predicate', 1)->nullable()->after('learning_outcome');
            $table->decimal('skill_score', 5, 2)->nullable()->after('knowledge_predicate');
            $table->string('skill_predicate', 1)->nullable()->after('skill_score');
            $table->text('skill_learning_outcome')->nullable()->after('skill_predicate');
        });
    }

    public function down(): void
    {
        Schema::table('report_card_grades', function (Blueprint $table) {
            $table->dropColumn([
                'knowledge_predicate',
                'skill_score',
                'skill_predicate',
                'skill_learning_outcome',
            ]);
        });
    }
};
