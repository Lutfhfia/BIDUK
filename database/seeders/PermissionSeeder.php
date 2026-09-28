<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Seed data permission.
     */
    public function run(): void
    {
        $permissions = [
            [
                'name' => 'dashboard.view',
                'description' => 'Melihat dashboard',
            ],

            [
                'name' => 'students.view',
                'description' => 'Melihat data siswa',
            ],
            [
                'name' => 'students.create',
                'description' => 'Menambah data siswa',
            ],
            [
                'name' => 'students.edit',
                'description' => 'Mengubah data siswa',
            ],
            [
                'name' => 'students.delete',
                'description' => 'Menghapus data siswa',
            ],

            [
                'name' => 'employees.view',
                'description' => 'Melihat data pegawai',
            ],
            [
                'name' => 'employees.create',
                'description' => 'Menambah data pegawai',
            ],
            [
                'name' => 'employees.edit',
                'description' => 'Mengubah data pegawai',
            ],
            [
                'name' => 'employees.delete',
                'description' => 'Menghapus data pegawai',
            ],

            [
                'name' => 'classes.view',
                'description' => 'Melihat data kelas',
            ],
            [
                'name' => 'classes.create',
                'description' => 'Menambah data kelas',
            ],
            [
                'name' => 'classes.edit',
                'description' => 'Mengubah data kelas',
            ],
            [
                'name' => 'classes.delete',
                'description' => 'Menghapus data kelas',
            ],

            [
                'name' => 'academic_years.view',
                'description' => 'Melihat tahun ajaran',
            ],
            [
                'name' => 'academic_years.create',
                'description' => 'Menambah tahun ajaran',
            ],
            [
                'name' => 'academic_years.edit',
                'description' => 'Mengubah tahun ajaran',
            ],
            [
                'name' => 'academic_years.delete',
                'description' => 'Menghapus tahun ajaran',
            ],

            [
                'name' => 'subjects.view',
                'description' => 'Melihat mata pelajaran',
            ],
            [
                'name' => 'subjects.create',
                'description' => 'Menambah mata pelajaran',
            ],
            [
                'name' => 'subjects.edit',
                'description' => 'Mengubah mata pelajaran',
            ],
            [
                'name' => 'subjects.delete',
                'description' => 'Menghapus mata pelajaran',
            ],

            [
                'name' => 'attendance.view',
                'description' => 'Melihat rekap absensi',
            ],
            [
                'name' => 'attendance.create',
                'description' => 'Menambah rekap absensi',
            ],
            [
                'name' => 'attendance.edit',
                'description' => 'Mengubah rekap absensi',
            ],
            [
                'name' => 'attendance.delete',
                'description' => 'Menghapus rekap absensi',
            ],

            [
                'name' => 'grades.view',
                'description' => 'Melihat nilai rapot',
            ],
            [
                'name' => 'grades.create',
                'description' => 'Menambah nilai rapot',
            ],
            [
                'name' => 'grades.edit',
                'description' => 'Mengubah nilai rapot',
            ],
            [
                'name' => 'grades.delete',
                'description' => 'Menghapus nilai rapot',
            ],

            [
                'name' => 'reports.view',
                'description' => 'Melihat laporan',
            ],

            [
                'name' => 'users.manage',
                'description' => 'Mengelola pengguna',
            ],

            [
                'name' => 'roles.manage',
                'description' => 'Mengelola hak akses role',
            ],

            [
                'name' => 'semesters.view',
                'description' => 'Melihat data semester',
            ],
            [
                'name' => 'semesters.create',
                'description' => 'Menambah data semester',
            ],
            [
                'name' => 'semesters.edit',
                'description' => 'Mengubah data semester',
            ],
            [
                'name' => 'semesters.delete',
                'description' => 'Menghapus data semester',
            ],

            [
                'name' => 'student_records.view',
                'description' => 'Melihat Buku Induk siswa',
            ],

            [
                'name' => 'report_cards.view',
                'description' => 'Melihat rekap rapot',
            ],

            [
                'name' => 'achievements.view',
                'description' => 'Melihat rekap prestasi',
            ],

            [
                'name' => 'school_profile.manage',
                'description' => 'Mengelola profil sekolah',
            ],

            [
                'name' => 'news.view',
                'description' => 'Melihat berita sekolah',
            ],
            [
                'name' => 'news.create',
                'description' => 'Menambah berita sekolah',
            ],
            [
                'name' => 'news.edit',
                'description' => 'Mengubah berita sekolah',
            ],
            [
                'name' => 'news.delete',
                'description' => 'Menghapus berita sekolah',
            ],

            [
                'name' => 'activity_logs.view',
                'description' => 'Melihat log aktivitas',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}