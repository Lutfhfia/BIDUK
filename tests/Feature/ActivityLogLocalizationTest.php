<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogLocalizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_log_filters_and_rows_use_indonesian_labels_but_keep_login_label(): void
    {
        $admin = $this->createSuperAdmin();
        ActivityLog::create([
            'action' => 'login',
            'module' => 'authentication',
            'description' => 'User berhasil login ke sistem',
        ]);
        ActivityLog::create([
            'action' => 'students_added',
            'module' => 'student_class_histories',
            'description' => 'Menambahkan siswa ke kelas 1A',
        ]);

        $this->actingAs($admin)
            ->get(route('activity-logs.index'))
            ->assertOk()
            ->assertSeeText('Autentikasi')
            ->assertSeeText('Riwayat Kelas Siswa')
            ->assertSeeText('Siswa Ditambahkan')
            ->assertSeeText('Login')
            ->assertDontSeeText('Authentication')
            ->assertDontSeeText('Student Class Histories')
            ->assertDontSeeText('Students_added');

        $loginLog = ActivityLog::where('action', 'login')->firstOrFail();
        $this->get(route('activity-logs.show', $loginLog))
            ->assertOk()
            ->assertSeeText('Autentikasi')
            ->assertSeeText('Login')
            ->assertDontSeeText('Authentication');
    }

    public function test_activity_log_filters_keep_original_values_while_displaying_translated_labels(): void
    {
        $admin = $this->createSuperAdmin();
        ActivityLog::create([
            'action' => 'updated',
            'module' => 'academic_years',
            'description' => 'Tahun ajaran diperbarui',
        ]);

        $this->actingAs($admin)
            ->get(route('activity-logs.index', [
                'module' => 'academic_years',
                'action' => 'updated',
            ]))
            ->assertOk()
            ->assertSeeText('Tahun Ajaran')
            ->assertSeeText('Diperbarui')
            ->assertSee('value="academic_years"', false)
            ->assertSee('value="updated"', false)
            ->assertSee('selected', false);
    }

    private function createSuperAdmin(): User
    {
        return User::create([
            'name' => 'Super Admin',
            'username' => 'super-admin-activity-localization',
            'email' => 'super-admin-activity-localization@example.test',
            'password' => 'password',
            'role_id' => Role::create(['name' => 'Super Admin'])->id,
            'status' => 'Aktif',
        ]);
    }
}
