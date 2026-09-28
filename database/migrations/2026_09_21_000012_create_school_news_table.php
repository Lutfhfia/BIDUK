<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
 public function up(): void { Schema::create('school_news', function (Blueprint $table) {
  $table->id(); $table->string('title'); $table->date('published_at')->nullable(); $table->string('image')->nullable();
  $table->text('summary')->nullable(); $table->longText('content')->nullable(); $table->boolean('is_published')->default(false); $table->timestamps();
 }); }
 public function down(): void { Schema::dropIfExists('school_news'); }
};
