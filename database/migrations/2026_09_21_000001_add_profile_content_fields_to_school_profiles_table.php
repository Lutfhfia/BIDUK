<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('school_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('school_profiles', 'hero_title')) {
                $table->string('hero_title')->nullable();
            }

            if (!Schema::hasColumn('school_profiles', 'hero_description')) {
                $table->text('hero_description')->nullable();
            }

            if (!Schema::hasColumn('school_profiles', 'school_image')) {
                $table->string('school_image')->nullable();
            }

            if (!Schema::hasColumn('school_profiles', 'organization_title')) {
                $table->string('organization_title')->nullable();
            }

            if (!Schema::hasColumn('school_profiles', 'organization_description')) {
                $table->text('organization_description')->nullable();
            }
        });
    }

    public function down(): void
    {
        $columns = [
            'hero_title',
            'hero_description',
            'school_image',
            'organization_title',
            'organization_description',
        ];

        foreach ($columns as $column) {
            if (Schema::hasColumn('school_profiles', $column)) {
                Schema::table('school_profiles', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }
};
