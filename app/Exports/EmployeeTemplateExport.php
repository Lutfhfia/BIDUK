<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class EmployeeTemplateExport implements
    FromArray,
    WithHeadings,
    ShouldAutoSize,
    WithTitle
{
    /**
     * Template tidak memiliki data.
     */
    public function array(): array
    {
        return [];
    }

    /**
     * Kolom yang tersedia pada template.
     */
    public function headings(): array
    {
        return [
            'NIP',
            'NUPTK',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Pegawai',
            'Status Kepegawaian',
            'Jabatan',
            'No. HP',
            'Email',
            'Alamat',
            'Status',
        ];
    }

    /**
     * Nama sheet.
     */
    public function title(): string
    {
        return 'Template Pegawai';
    }
}