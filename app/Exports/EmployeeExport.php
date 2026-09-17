<?php

namespace App\Exports;

use App\Models\Employee;
use Illuminate\Support\Enumerable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class EmployeeExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithTitle
{
    /**
     * Data pegawai yang akan diekspor.
     */
    public function collection(): Enumerable
    {
        return Employee::with([
            'homeroomClasses.academicYear',
        ])->get();
    }

    /**
     * Judul kolom Excel.
     */
    public function headings(): array
    {
        return [
            'No',
            'NIP',
            'NUPTK',
            'Nama Lengkap',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Pegawai',
            'Status Kepegawaian',
            'Jabatan',
            'Wali Kelas',
            'No. HP',
            'Email',
            'Alamat',
            'Status',
        ];
    }

    /**
     * Mengatur isi setiap baris Excel.
     */
    public function map($employee): array
    {
        $waliKelas = $employee->homeroomClasses
            ->map(function ($class) {
                $namaKelas = $class->name;

                if ($class->academicYear) {
                    $namaKelas .= ' (' . $class->academicYear->name . ')';
                }

                return $namaKelas;
            })
            ->implode(', ');

        return [
            $employee->id,
            $employee->nip ?? '-',
            $employee->nuptk ?? '-',
            $employee->name,
            $employee->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            $employee->birth_place ?? '-',
            $employee->birth_date
                ? $employee->birth_date->format('d-m-Y')
                : '-',
            $employee->employee_type ?? '-',
            $employee->employment_status ?? '-',
            $employee->position ?? '-',
            $waliKelas ?: 'Belum ditentukan',
            $employee->phone ?? '-',
            $employee->email ?? '-',
            $employee->address ?? '-',
            $employee->status ?? '-',
        ];
    }

    /**
     * Nama sheet Excel.
     */
    public function title(): string
    {
        return 'Data Pegawai';
    }
}