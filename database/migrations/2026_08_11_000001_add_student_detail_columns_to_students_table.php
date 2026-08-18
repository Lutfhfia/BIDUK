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
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'school_code')) {
                $table->string('school_code', 50)->nullable()->after('nik');
            }
            if (!Schema::hasColumn('students', 'district_code')) {
                $table->string('district_code', 50)->nullable()->after('school_code');
            }
            if (!Schema::hasColumn('students', 'city_code')) {
                $table->string('city_code', 50)->nullable()->after('district_code');
            }
            if (!Schema::hasColumn('students', 'province_code')) {
                $table->string('province_code', 50)->nullable()->after('city_code');
            }
            if (!Schema::hasColumn('students', 'student_number')) {
                $table->string('student_number', 20)->nullable()->after('province_code');
            }

            if (!Schema::hasColumn('students', 'citizenship')) {
                $table->string('citizenship', 50)->default('WNI')->after('religion');
            }
            if (!Schema::hasColumn('students', 'child_position')) {
                $table->integer('child_position')->nullable()->after('citizenship');
            }
            if (!Schema::hasColumn('students', 'siblings_biological')) {
                $table->integer('siblings_biological')->default(0)->after('child_position');
            }
            if (!Schema::hasColumn('students', 'siblings_step')) {
                $table->integer('siblings_step')->default(0)->after('siblings_biological');
            }
            if (!Schema::hasColumn('students', 'siblings_adopted')) {
                $table->integer('siblings_adopted')->default(0)->after('siblings_step');
            }

            if (!Schema::hasColumn('students', 'daily_language')) {
                $table->string('daily_language', 50)->nullable()->after('siblings_adopted');
            }
            if (!Schema::hasColumn('students', 'blood_type')) {
                $table->string('blood_type', 5)->nullable()->after('daily_language');
            }

            if (!Schema::hasColumn('students', 'rt_rw')) {
                $table->string('rt_rw', 20)->nullable()->after('address');
            }
            if (!Schema::hasColumn('students', 'village')) {
                $table->string('village', 50)->nullable()->after('rt_rw');
            }
            if (!Schema::hasColumn('students', 'district')) {
                $table->string('district', 50)->nullable()->after('village');
            }
            if (!Schema::hasColumn('students', 'city')) {
                $table->string('city', 50)->nullable()->after('district');
            }
            if (!Schema::hasColumn('students', 'province')) {
                $table->string('province', 50)->nullable()->after('city');
            }
            if (!Schema::hasColumn('students', 'postal_code')) {
                $table->string('postal_code', 10)->nullable()->after('province');
            }
            if (!Schema::hasColumn('students', 'phone_number')) {
                $table->string('phone_number', 20)->nullable()->after('postal_code');
            }
            if (!Schema::hasColumn('students', 'living_with')) {
                $table->string('living_with', 50)->nullable()->after('phone_number');
            }
            if (!Schema::hasColumn('students', 'distance_to_school')) {
                $table->string('distance_to_school', 30)->nullable()->after('living_with');
            }

            if (!Schema::hasColumn('students', 'father_education')) {
                $table->string('father_education', 50)->nullable()->after('father_nik');
            }
            if (!Schema::hasColumn('students', 'mother_education')) {
                $table->string('mother_education', 50)->nullable()->after('mother_nik');
            }
            if (!Schema::hasColumn('students', 'guardian_relation')) {
                $table->string('guardian_relation', 50)->nullable()->after('guardian_name');
            }
            if (!Schema::hasColumn('students', 'guardian_education')) {
                $table->string('guardian_education', 50)->nullable()->after('guardian_relation');
            }
            if (!Schema::hasColumn('students', 'guardian_job')) {
                $table->string('guardian_job', 100)->nullable()->after('guardian_education');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'school_code')) {
                $table->dropColumn('school_code');
            }
            if (Schema::hasColumn('students', 'district_code')) {
                $table->dropColumn('district_code');
            }
            if (Schema::hasColumn('students', 'city_code')) {
                $table->dropColumn('city_code');
            }
            if (Schema::hasColumn('students', 'province_code')) {
                $table->dropColumn('province_code');
            }
            if (Schema::hasColumn('students', 'student_number')) {
                $table->dropColumn('student_number');
            }
            if (Schema::hasColumn('students', 'citizenship')) {
                $table->dropColumn('citizenship');
            }
            if (Schema::hasColumn('students', 'child_position')) {
                $table->dropColumn('child_position');
            }
            if (Schema::hasColumn('students', 'siblings_biological')) {
                $table->dropColumn('siblings_biological');
            }
            if (Schema::hasColumn('students', 'siblings_step')) {
                $table->dropColumn('siblings_step');
            }
            if (Schema::hasColumn('students', 'siblings_adopted')) {
                $table->dropColumn('siblings_adopted');
            }
            if (Schema::hasColumn('students', 'daily_language')) {
                $table->dropColumn('daily_language');
            }
            if (Schema::hasColumn('students', 'blood_type')) {
                $table->dropColumn('blood_type');
            }
            if (Schema::hasColumn('students', 'rt_rw')) {
                $table->dropColumn('rt_rw');
            }
            if (Schema::hasColumn('students', 'village')) {
                $table->dropColumn('village');
            }
            if (Schema::hasColumn('students', 'district')) {
                $table->dropColumn('district');
            }
            if (Schema::hasColumn('students', 'city')) {
                $table->dropColumn('city');
            }
            if (Schema::hasColumn('students', 'province')) {
                $table->dropColumn('province');
            }
            if (Schema::hasColumn('students', 'postal_code')) {
                $table->dropColumn('postal_code');
            }
            if (Schema::hasColumn('students', 'phone_number')) {
                $table->dropColumn('phone_number');
            }
            if (Schema::hasColumn('students', 'living_with')) {
                $table->dropColumn('living_with');
            }
            if (Schema::hasColumn('students', 'distance_to_school')) {
                $table->dropColumn('distance_to_school');
            }
            if (Schema::hasColumn('students', 'father_education')) {
                $table->dropColumn('father_education');
            }
            if (Schema::hasColumn('students', 'mother_education')) {
                $table->dropColumn('mother_education');
            }
            if (Schema::hasColumn('students', 'guardian_relation')) {
                $table->dropColumn('guardian_relation');
            }
            if (Schema::hasColumn('students', 'guardian_education')) {
                $table->dropColumn('guardian_education');
            }
            if (Schema::hasColumn('students', 'guardian_job')) {
                $table->dropColumn('guardian_job');
            }
        });
    }
};
