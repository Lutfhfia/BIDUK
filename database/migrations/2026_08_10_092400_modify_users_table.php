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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('employee_id')->nullable()->after('id')->constrained('employees')->nullOnDelete();
            $table->string('username', 50)->unique()->after('name');
            $table->foreignId('role_id')->nullable()->after('username')->constrained('roles')->nullOnDelete();
            $table->enum('status', ['Aktif', 'Nonaktif'])->default('Aktif')->after('password');
            $table->timestamp('last_login_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['employee_id']);
            $table->dropForeign(['role_id']);
            $table->dropColumn(['employee_id', 'username', 'role_id', 'status', 'last_login_at']);
        });
    }
};
