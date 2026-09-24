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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();

            // Pengguna yang melakukan aktivitas
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Jenis aktivitas: login, logout, created, updated, deleted
            $table->string('action', 50);

            // Modul yang terkena aktivitas
            $table->string('module', 100);

            // Keterangan yang mudah dibaca
            $table->text('description')->nullable();

            // Data/model yang terkena aktivitas
            $table->string('subject_type', 255)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            // Data sebelum perubahan
            $table->json('old_values')->nullable();

            // Data setelah perubahan
            $table->json('new_values')->nullable();

            // Informasi akses
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            // Index untuk pencarian/filter
            $table->index(['subject_type', 'subject_id']);
            $table->index('module');
            $table->index('action');
            $table->index('created_at');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};