<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $columns = [
            'hero_title' => fn (Blueprint $table) => $table->string('hero_title')->nullable(),
            'hero_description' => fn (Blueprint $table) => $table->text('hero_description')->nullable(),
            'school_image' => fn (Blueprint $table) => $table->string('school_image')->nullable(),
            'organization_title' => fn (Blueprint $table) => $table->string('organization_title')->nullable(),
            'organization_description' => fn (Blueprint $table) => $table->text('organization_description')->nullable(),
        ];

        foreach ($columns as $name => $definition) {
            if (!Schema::hasColumn('school_profiles', $name)) {
                Schema::table('school_profiles', $definition);
            }
        }
    }

    public function down(): void
    {
        foreach ([
            'hero_title',
            'hero_description',
            'school_image',
            'organization_title',
            'organization_description',
        ] as $name) {
            if (Schema::hasColumn('school_profiles', $name)) {
                Schema::table('school_profiles', function (Blueprint $table) use ($name): void {
                    $table->dropColumn($name);
                });
            }
        }
    }
};
