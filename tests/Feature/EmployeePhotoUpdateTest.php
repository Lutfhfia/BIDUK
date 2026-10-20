<?php

namespace Tests\Feature;

use App\Http\Controllers\EmployeeController;
use App\Models\Employee;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EmployeePhotoUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_uploaded_employee_photo_is_stored_and_saved_on_update(): void
    {
        Storage::fake('public');
        Role::create(['name' => 'Guru']);

        $employee = Employee::create([
            'nip' => 'NIP-TEST',
            'name' => 'Guru Uji',
            'gender' => 'P',
            'position' => 'Guru',
            'employee_type' => 'Guru',
            'employment_status' => 'Honorer',
            'status' => 'Aktif',
        ]);

        $request = Request::create(
            '/pegawai/'.$employee->id,
            'PUT',
            [
                'nip' => 'NIP-TEST',
                'name' => 'Guru Uji',
                'gender' => 'P',
                'position' => 'Guru',
                'employment_status' => 'Non-ASN',
                'status' => 'Aktif',
            ],
            [],
            ['photo' => UploadedFile::fake()->image('foto-baru.jpg')]
        );
        $response = app(EmployeeController::class)->update($request, $employee);

        $this->assertSame(route('pegawai.index'), $response->getTargetUrl());
        $photoPath = $employee->fresh()->photo;
        $this->assertNotEmpty($photoPath);
        Storage::disk('public')->assertExists($photoPath);
    }
}
