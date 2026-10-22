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
     * Template tidak memiliki data pegawai.
     */
    public function array(): array
    {
        return [];
    }

    /**
     * Kolom template yang sesuai dengan proses import.
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
            'Status Kepegawaian',
            'Jabatan',
            'Nomor HP',
            'Email',
            'Alamat',
            'Status Pegawai',
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
