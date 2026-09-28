<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
 public function up(): void { Schema::create('school_achievements', function (Blueprint $table) {
  $table->id(); $table->string('title'); $table->unsignedSmallInteger('year')->nullable();
  $table->string('level')->nullable(); $table->text('description')->nullable(); $table->string('image')->nullable(); $table->timestamps();
 }); }
 public function down(): void { Schema::dropIfExists('school_achievements'); }
};
