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
        Schema::create('school_profiles', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('npsn')->nullable();
            $table->string('nis')->nullable();

            $table->text('address')->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            $table->text('description')->nullable();
            $table->text('vision')->nullable();
            $table->text('mission')->nullable();

            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();

            $table->string('organization_image')->nullable();

            $table->string('hero_title')->nullable();
            $table->text('hero_description')->nullable();

            $table->string('school_image')->nullable();

            $table->string('organization_title')->nullable();
            $table->text('organization_description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('school_profiles');
    }
};