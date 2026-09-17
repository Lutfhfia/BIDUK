<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_card_grades', function (Blueprint $table) {
            $table->text('learning_outcome')
                ->nullable()
                ->after('score');
        });
    }

    public function down(): void
    {
        Schema::table('report_card_grades', function (Blueprint $table) {
            $table->dropColumn('learning_outcome');
        });
    }
};
