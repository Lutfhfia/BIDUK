<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
            $table->unique('employee_id');
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->unique('nip');
            $table->unique('nuptk');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['employee_id']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique(['nip']);
            $table->dropUnique(['nuptk']);
        });
    }
};