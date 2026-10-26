<?php

use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->foreignId('homeroom_teacher_user_id')
                ->nullable()
                ->after('homeroom_teacher_id')
                ->constrained('users')
                ->nullOnDelete();
        });

        SchoolClass::query()
            ->whereNotNull('homeroom_teacher_id')
            ->orderBy('id')
            ->each(function (SchoolClass $class): void {
                $user = User::query()
                    ->where('employee_id', $class->homeroom_teacher_id)
                    ->whereHas('role', fn ($query) => $query->where('name', 'Guru'))
                    ->first();

                if ($user) {
                    $class->forceFill(['homeroom_teacher_user_id' => $user->id])->save();
                }
            });
    }

    public function down(): void
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('homeroom_teacher_user_id');
        });
    }
};
