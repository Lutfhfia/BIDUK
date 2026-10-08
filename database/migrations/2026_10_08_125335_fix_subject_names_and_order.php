<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migration.
     */
    public function up(): void
    {
        /*
         * =========================================================
         * PERBAIKI NAMA MATA PELAJARAN
         * =========================================================
         *
         * Nama yang sekarang ada di database disesuaikan
         * dengan nama yang tercantum pada buku rapot sekolah.
         */

        DB::table('subjects')
            ->where('name', 'Pendidikan Agama Islam')
            ->update([
                'name' => 'Pendidikan Agama dan Budi Pekerti',
            ]);

        DB::table('subjects')
            ->where('name', 'Seni Budaya')
            ->update([
                'name' => 'Seni dan Budaya',
            ]);

        DB::table('subjects')
            ->where('name', 'Mulok Lokal')
            ->update([
                'name' => 'Muatan Lokal',
            ]);


        /*
         * =========================================================
         * PASTIKAN SEMUA MATA PELAJARAN INI INTRAKURIKULER
         * =========================================================
         */

        DB::table('subjects')
            ->whereIn('name', [
                'Pendidikan Agama dan Budi Pekerti',
                'Pendidikan Pancasila',
                'Bahasa Indonesia',
                'Matematika',
                'Ilmu Pengetahuan Alam dan Sosial',
                'Pendidikan Jasmani, Olahraga dan Kesehatan',
                'Bahasa Inggris',
                'Seni dan Budaya',
                'Muatan Lokal',
            ])
            ->update([
                'category' => 'Intrakurikuler',
            ]);


        /*
         * =========================================================
         * ATUR URUTAN SESUAI BUKU RAPOT
         * =========================================================
         */

        $orders = [
            'Pendidikan Agama dan Budi Pekerti' => 1,
            'Pendidikan Pancasila' => 2,
            'Bahasa Indonesia' => 3,
            'Matematika' => 4,
            'Ilmu Pengetahuan Alam dan Sosial' => 5,
            'Pendidikan Jasmani, Olahraga dan Kesehatan' => 6,
            'Bahasa Inggris' => 7,
            'Seni dan Budaya' => 8,
            'Muatan Lokal' => 9,
        ];

        foreach ($orders as $name => $order) {
            DB::table('subjects')
                ->where('name', $name)
                ->update([
                    'sort_order' => $order,
                ]);
        }
    }


    /**
     * Balikkan perubahan migration.
     */
    public function down(): void
    {
        /*
         * Kembalikan nama mata pelajaran seperti sebelumnya.
         */

        DB::table('subjects')
            ->where('name', 'Pendidikan Agama dan Budi Pekerti')
            ->update([
                'name' => 'Pendidikan Agama Islam',
            ]);

        DB::table('subjects')
            ->where('name', 'Seni dan Budaya')
            ->update([
                'name' => 'Seni Budaya',
            ]);

        DB::table('subjects')
            ->where('name', 'Muatan Lokal')
            ->update([
                'name' => 'Mulok Lokal',
            ]);

        /*
         * Kembalikan urutan ke default.
         */
        DB::table('subjects')
            ->update([
                'sort_order' => 999,
            ]);
    }
};