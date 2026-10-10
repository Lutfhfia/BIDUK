<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('school_profiles', 'nss')) {
            Schema::table('school_profiles', function (Blueprint $table) {
                $table->string('nss', 100)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('school_profiles', 'nss')) {
            Schema::table('school_profiles', function (Blueprint $table) {
                $table->dropColumn('nss');
            });
        }
    }
};