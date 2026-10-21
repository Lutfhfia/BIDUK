<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Mendaftarkan permission yang digunakan oleh aplikasi BIDUK.
     *
     * Seeder ini hanya membuat atau memperbarui data permission.
     * Pemberian permission kepada setiap role dilakukan melalui
     * halaman Hak Akses Role.
     */
    public function run(): void
    {
        $permissions = [

            // =================================================
            // DASHBOARD
            // =================================================

            [
                'name' => 'dashboard.view',
                'description' => 'Lihat Dashboard',
            ],

            // =================================================
            // DATA SISWA
            // =================================================

            [
                'name' => 'students.view',
                'description' => 'Lihat Data Siswa',
            ],
            [
                'name' => 'students.create',
                'description' => 'Tambah Siswa',
            ],
            [
                'name' => 'students.edit',
                'description' => 'Ubah Data Siswa',
            ],
            [
                'name' => 'students.delete',
                'description' => 'Hapus Siswa',
            ],
            [
                'name' => 'students.import',
                'description' => 'Import Data Siswa',
            ],
            [
                'name' => 'students.export',
                'description' => 'Export Data Siswa',
            ],

            // =================================================
            // DATA PEGAWAI
            // =================================================

            [
                'name' => 'employees.view',
                'description' => 'Lihat Data Pegawai',
            ],
            [
                'name' => 'employees.create',
                'description' => 'Tambah Pegawai',
            ],
            [
                'name' => 'employees.edit',
                'description' => 'Ubah Data Pegawai',
            ],
            [
                'name' => 'employees.delete',
                'description' => 'Hapus Pegawai',
            ],
            [
                'name' => 'employees.import',
                'description' => 'Import Data Pegawai',
            ],
            [
                'name' => 'employees.export',
                'description' => 'Export Data Pegawai',
            ],

            // =================================================
            // DATA KELAS
            // =================================================

            [
                'name' => 'classes.view',
                'description' => 'Lihat Data Kelas',
            ],
            [
                'name' => 'classes.create',
                'description' => 'Tambah Kelas',
            ],
            [
                'name' => 'classes.edit',
                'description' => 'Ubah Data Kelas',
            ],
            [
                'name' => 'classes.delete',
                'description' => 'Hapus Data Kelas',
            ],

            // =================================================
            // TAHUN AJARAN
            // =================================================

            [
                'name' => 'academic_years.view',
                'description' => 'Lihat Tahun Ajaran',
            ],
            [
                'name' => 'academic_years.create',
                'description' => 'Tambah Tahun Ajaran',
            ],
            [
                'name' => 'academic_years.edit',
                'description' => 'Ubah Tahun Ajaran',
            ],
            [
                'name' => 'academic_years.delete',
                'description' => 'Hapus Tahun Ajaran',
            ],

            // =================================================
            // MATA PELAJARAN
            // =================================================

            [
                'name' => 'subjects.view',
                'description' => 'Lihat Mata Pelajaran',
            ],
            [
                'name' => 'subjects.create',
                'description' => 'Tambah Mata Pelajaran',
            ],
            [
                'name' => 'subjects.edit',
                'description' => 'Ubah Mata Pelajaran',
            ],
            [
                'name' => 'subjects.delete',
                'description' => 'Hapus Mata Pelajaran',
            ],

            // =================================================
            // NILAI RAPOR
            // Nilai akhir/ijazah tetap menjadi bagian fitur ini.
            // =================================================

            [
                'name' => 'grades.view',
                'description' => 'Lihat Nilai Rapor',
            ],
            [
                'name' => 'grades.create',
                'description' => 'Tambah Nilai Rapor',
            ],
            [
                'name' => 'grades.edit',
                'description' => 'Ubah Nilai Rapor',
            ],
            [
                'name' => 'grades.delete',
                'description' => 'Hapus Nilai Rapor',
            ],
            [
                'name' => 'grades.import',
                'description' => 'Import Nilai Rapor',
            ],

            // =================================================
            // REKAP ABSENSI
            // =================================================

            [
                'name' => 'attendance.view',
                'description' => 'Lihat Rekap Absensi',
            ],
            [
                'name' => 'attendance.create',
                'description' => 'Tambah Rekap Absensi',
            ],
            [
                'name' => 'attendance.edit',
                'description' => 'Ubah Rekap Absensi',
            ],
            [
                'name' => 'attendance.delete',
                'description' => 'Hapus Rekap Absensi',
            ],
            [
                'name' => 'attendance.export',
                'description' => 'Unduh Rekap Absensi',
            ],

            // =================================================
            // BUKU INDUK
            // =================================================

            [
                'name' => 'student_records.view',
                'description' => 'Lihat Buku Induk',
            ],
            [
                'name' => 'student_records.print',
                'description' => 'Cetak Buku Induk',
            ],
            [
                'name' => 'student_records.export',
                'description' => 'Unduh Buku Induk',
            ],

            // =================================================
            // REKAP RAPOR
            // =================================================

            [
                'name' => 'report_cards.view',
                'description' => 'Lihat Rekap Rapor',
            ],
            [
                'name' => 'report_cards.print',
                'description' => 'Cetak Rekap Rapor',
            ],
            [
                'name' => 'report_cards.export',
                'description' => 'Unduh Rekap Rapor',
            ],

            // =================================================
            // REKAP PRESTASI
            // =================================================

            [
                'name' => 'achievements.view',
                'description' => 'Lihat Rekap Prestasi',
            ],
            [
                'name' => 'achievements.create',
                'description' => 'Tambah Data Prestasi',
            ],
            [
                'name' => 'achievements.edit',
                'description' => 'Ubah Data Prestasi',
            ],
            [
                'name' => 'achievements.delete',
                'description' => 'Hapus Data Prestasi',
            ],
            [
                'name' => 'achievements.print',
                'description' => 'Cetak Rekap Prestasi',
            ],
            [
                'name' => 'achievements.export',
                'description' => 'Unduh Rekap Prestasi',
            ],

            // =================================================
            // PROFIL SEKOLAH
            // =================================================

            [
                'name' => 'school_profile.view',
                'description' => 'Lihat Profil Sekolah',
            ],
            [
                'name' => 'school_profile.manage',
                'description' => 'Kelola Profil Sekolah',
            ],

            // =================================================
            // BERITA SEKOLAH
            // Dipertahankan untuk mendukung konten sekolah.
            // =================================================

            [
                'name' => 'news.view',
                'description' => 'Lihat Berita Sekolah',
            ],
            [
                'name' => 'news.create',
                'description' => 'Tambah Berita Sekolah',
            ],
            [
                'name' => 'news.edit',
                'description' => 'Ubah Berita Sekolah',
            ],
            [
                'name' => 'news.delete',
                'description' => 'Hapus Berita Sekolah',
            ],

            // =================================================
            // MANAJEMEN USER
            // =================================================

            [
                'name' => 'users.view',
                'description' => 'Lihat Pengguna',
            ],
            [
                'name' => 'users.create',
                'description' => 'Tambah Pengguna',
            ],
            [
                'name' => 'users.edit',
                'description' => 'Ubah Pengguna',
            ],
            [
                'name' => 'users.delete',
                'description' => 'Hapus Pengguna',
            ],
            [
                'name' => 'users.manage',
                'description' => 'Kelola Manajemen Pengguna',
            ],

            // =================================================
            // HAK AKSES ROLE
            // =================================================

            [
                'name' => 'roles.manage',
                'description' => 'Kelola Hak Akses Role',
            ],

            // =================================================
            // LOG AKTIVITAS
            // =================================================

            [
                'name' => 'activity_logs.view',
                'description' => 'Lihat Log Aktivitas',
            ],

            // =================================================
            // LAPORAN UMUM
            // =================================================

            [
                'name' => 'reports.view',
                'description' => 'Lihat Laporan',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                [
                    'name' => $permission['name'],
                ],
                [
                    'description' => $permission['description'],
                ]
            );
        }
    }
}
