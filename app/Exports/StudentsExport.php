<?php

namespace App\Exports;

use App\Models\AcademicYear;
use App\Models\Student;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StudentsExport implements Export, FromCollection, WithHeadings, ShouldAutoSize, WithEvents
{
    public function collection(): Collection
    {
        return Student::with(['previousEducation', 'classes', 'physiques'])
            ->orderBy('name')
            ->get()
            ->map(function (Student $s, int $index) {
                $pe = $s->previousEducation;
                $class = $s->classes->first();
                $activeYear = AcademicYear::getActive();
                $physique = $activeYear
                    ? $s->physiques->where('academic_year_id', $activeYear->id)->sortByDesc('id')->first()
                    : $s->physiques->sortByDesc('id')->first();

                return [
                    $index + 1,
                    $s->nis,
                    $s->nisn,
                    $s->name,
                    $s->nickname,
                    $s->gender,
                    $s->birth_place,
                    optional($s->birth_date)->format('Y-m-d'),
                    $s->religion,
                    $s->citizenship,
                    $s->child_position,
                    $s->siblings_biological,
                    $s->siblings_step,
                    $s->siblings_adopted,
                    $s->daily_language,
                    $physique?->weight,
                    $physique?->height,
                    $s->blood_type,
                    $s->diseases,
                    $s->immunizations,
                    $s->address,
                    $s->rt,
                    $s->rw,
                    $s->village,
                    $s->district,
                    $s->city,
                    $s->province,
                    $s->postal_code,
                    $s->living_with,
                    $s->distance_to_school,

                    $s->father_name,
                    $s->father_birth_place,
                    optional($s->father_birth_date)->format('Y-m-d'),
                    $s->father_education,
                    $s->father_job,
                    $s->father_address,
                    $s->father_phone,
                    $s->father_citizenship,

                    $s->mother_name,
                    $s->mother_birth_place,
                    optional($s->mother_birth_date)->format('Y-m-d'),
                    $s->mother_education,
                    $s->mother_job,
                    $s->mother_address,
                    $s->mother_phone,
                    $s->mother_citizenship,

                    $s->guardian_name,
                    $s->guardian_birth_place,
                    optional($s->guardian_birth_date)->format('Y-m-d'),
                    $s->guardian_education,
                    $s->guardian_job,
                    $s->guardian_address,
                    $s->guardian_phone,
                    $s->guardian_citizenship,
                    $s->guardian_relation,

                    $s->student_origin,
                    $pe?->kindergarten_name,
                    $pe?->kindergarten_address,
                    optional($pe?->sttb_date)->format('Y-m-d'),
                    $pe?->sttb_number,
                    $pe?->transfer_from_school ?? $pe?->previous_school_name,
                    $pe?->transfer_from_grade,
                    optional($pe?->transfer_accepted_date)->format('Y-m-d'),
                    $pe?->transfer_to_class ?? $class?->name,

                    $s->scholarship_type,
                    $s->graduation_year,
                    $s->continued_school,
                    $s->transfer_left_grade,
                    $s->transfer_to_school,
                    $s->transfer_to_grade,
                    optional($s->transfer_date)->format('Y-m-d'),
                    optional($s->leaving_date)->format('Y-m-d'),
                    $s->leaving_reason,
                ];
            });
    }

    public function headings(): array
    {
        return [
            // Baris 1: Header Utama
            [
                'No', 'No Induk', 'NISN', 'Nama Lengkap', 'Nama Panggilan', 'Jenis Kelamain', 'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'Kewarganegaraan', 'Anak Ke',
                'Jumlah Saudara', '', '',
                'Keadaan Jasmani', '', '', '',
                'Penyakit Yang Pernah Diderita',
                'Imuninasi Yang Pernah Di Terima',
                'Alamat Rumah', '', '', '', '', '', '', '',
                'Bertempat Tinggal Pada',
                'Jarak Tempat Tinggal',
                'Ayah Kandung', '', '', '', '', '', '', '',
                'Ibu Kandung', '', '', '', '', '', '', '',
                'Wali Murid', '', '', '', '', '', '', '', '',
                'Masuk Menjadi Murid Baru', '', '', '', '',
                'Pindahan dari sekolah lain', '', '', '',
                'Jenis Bea Siswa',
                'Tamat belajar', '',
                'Pindah Sekolah', '', '', '',
                'Keluar Sekolah', '',
            ],
            // Baris 2: Subheader
            [
                '', '', '', '', '', '', '', '', '', '', '',
                'Kandung', 'Tiri', 'Angkat',
                'Bahasa Sehari-hari', 'Berat Badan', 'Tinggi Badan', 'Golongan Darah',
                '', '',
                'Jalan', 'RT', 'RW', 'Kelurahan', 'Kecamatan', 'Kabupaten', 'Provinsi', 'Kode POS',
                '', '',
                'Nama', 'Tmp. Lahir', 'Tgl. Lahir', 'Pendidikan Tertinggi', 'Pekerjaan', 'Alamat Rumah', 'No Telepon', 'Kewarganegaraan',
                'Nama', 'Tmp. Lahir', 'Tgl. Lahir', 'Pendidikan Tertinggi', 'Pekerjaan', 'Alamat Rumah', 'No Telepon', 'Kewarganegaraan',
                'Nama', 'Tmp. Lahir', 'Tgl. Lahir', 'Pendidikan Tertinggi', 'Pekerjaan', 'Alamat Rumah', 'No Telepon', 'Kewarganegaraan', 'Hubungan Keluarga',
                'Asal Murid', 'Nama TK', 'Alamat Sekolah', 'Tgl. STTB', 'Nomor STTB',
                'Nama Sekolah Asal', 'Dari Kelas', 'Diterima tanggal', 'Di Kelas',
                '',
                'Tahun', 'Melanjutkan ke sekolah',
                'Dari kelas', 'Ke Sekolah', 'Kelas', 'Tanggal',
                'Tanggal', 'Alasan',
            ],
            // Baris 3: Nomor Posisi Kolom (1-73)
            range(1, 73),
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Style Baris 1-3 (Header)
                $headerStyle = [
                    'font' => [
                        'bold' => true,
                        'size' => 10,
                        'name' => 'Calibri',
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => 'FFAAAAAA'],
                        ],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['argb' => 'FFE8F4EC'],
                    ],
                ];

                $sheet->getStyle('A1:BU3')->applyFromArray($headerStyle);
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(25);
                $sheet->getRowDimension(3)->setRowHeight(18);

                // Merge Header Baris 1 untuk kolom tunggal (A-K, S, T, AC, AD, BM)
                $singleCols = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'S', 'T', 'AC', 'AD', 'BM'];
                foreach ($singleCols as $col) {
                    $sheet->mergeCells("{$col}1:{$col}2");
                }

                // Merge Kelompok Header Utama (Baris 1)
                $sheet->mergeCells('L1:N1');   // Jumlah Saudara
                $sheet->mergeCells('O1:R1');   // Keadaan Jasmani
                $sheet->mergeCells('U1:AB1');  // Alamat Rumah
                $sheet->mergeCells('AE1:AL1'); // Ayah Kandung
                $sheet->mergeCells('AM1:AT1'); // Ibu Kandung
                $sheet->mergeCells('AU1:BC1'); // Wali Murid
                $sheet->mergeCells('BD1:BH1'); // Masuk Menjadi Murid Baru
                $sheet->mergeCells('BI1:BL1'); // Pindahan dari sekolah lain
                $sheet->mergeCells('BN1:BO1'); // Tamat belajar
                $sheet->mergeCells('BP1:BS1'); // Pindah Sekolah
                $sheet->mergeCells('BT1:BU1'); // Keluar Sekolah
            },
        ];
    }
}
