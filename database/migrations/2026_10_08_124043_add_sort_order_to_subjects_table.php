<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')
                ->default(999)
                ->after('category');
        });

        /*
         * Normalisasi kategori lama.
         * Muatan Lokal dan Seni dan Budaya bukan kategori lagi,
         * tetapi mata pelajaran dengan kategori Intrakurikuler.
         */
        DB::table('subjects')
            ->whereIn('category', [
                'Muatan Lokal',
                'Seni dan Budaya',
            ])
            ->update([
                'category' => 'Intrakurikuler',
            ]);

        /*
         * Atur urutan mata pelajaran sesuai urutan buku rapot.
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

    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};