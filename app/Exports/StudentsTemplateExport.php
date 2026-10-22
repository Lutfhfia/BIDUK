<?php

namespace App\Exports;

use Illuminate\Support\Collection;

class StudentsTemplateExport extends StudentsExport
{
    /**
     * Template hanya berisi satu baris kosong
     * setelah tiga baris header.
     *
     * Header dan format tetap menggunakan StudentsExport.
     */
    public function collection(): Collection
    {
        return collect([
            array_fill(0, 73, null),
        ]);
    }
}