<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->enum('gender', ['L', 'P'])->nullable()->change();
            $table->string('citizenship', 50)->nullable()->default(null)->change();
            $table->integer('siblings_biological')->nullable()->default(0)->change();
            $table->integer('siblings_step')->nullable()->default(0)->change();
            $table->integer('siblings_adopted')->nullable()->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->enum('gender', ['L', 'P'])->nullable(false)->change();
            $table->string('citizenship', 50)->nullable(false)->default('WNI')->change();
        });
    }
};
