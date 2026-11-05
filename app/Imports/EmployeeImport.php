<?php

namespace App\Imports;

use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Throwable;

class EmployeeImport implements
    ToCollection,
    WithHeadingRow,
    SkipsEmptyRows
{
    /**
     * Jumlah data yang berhasil diimport.
     */
    protected int $importedCount = 0;
    protected int $updatedCount = 0;
    protected array $warnings = [];

    /**
     * Proses seluruh baris Excel.
     */
    public function collection(Collection $rows): void
    {
        $preparedRows = [];
        $errors = [];
        $this->warnings = [];

        /*
         * Untuk mendeteksi data duplikat di file Excel
         * maupun yang sudah ada di database.
         */
        $seenNip = [];
        $seenNuptk = [];
        $seenEmail = [];

        foreach ($rows as $index => $row) {

            /*
             * HeadingRow adalah baris pertama Excel.
             * Data pertama berarti baris Excel nomor 2.
             */
            $excelRow = $index + 2;

            /*
             * =====================================================
             * 1. BACA DAN NORMALISASI DATA EXCEL
             * =====================================================
             */
            $data = [

                'nip' => $this->clean(
                    $row['nip'] ?? null
                ),

                'nuptk' => $this->clean(
                    $row['nuptk'] ?? null
                ),

                'name' => $this->clean(
                    $row['nama_lengkap'] ?? null
                ),

                'gender' => $this->normalizeGender(
                    $this->clean(
                        $row['jenis_kelamin'] ?? null
                    )
                ),

                'birth_place' => $this->clean(
                    $row['tempat_lahir'] ?? null
                ),

                'birth_date' => $this->normalizeDate(
                    $row['tanggal_lahir'] ?? null
                ),

                /*
                 * P3K / P3K PARUH WAKTU / PPPK
                 * dinormalisasi menjadi PPPK.
                 */
                'employment_status' =>
                    $this->normalizeEmploymentStatus(
                        $row['status_kepegawaian'] ?? null
                    ),

                /*
                 * Jabatan dinormalisasi hanya untuk variasi nama
                 * yang jelas seperti Kepsek -> Kepala Sekolah.
                 *
                 * Jabatan lain seperti OPS, TU, Perpstk, Keamanan
                 * tetap dipertahankan.
                 */
                'position' =>
                    $this->normalizePosition(
                        $row['jabatan'] ?? null
                    ),

                /*
                 * Tidak Aktif -> Nonaktif.
                 */
                'status' =>
    $this->normalizeEmployeeStatus(
        $row['status'] ?? $row['status_pegawai'] ?? null
    ),

                'phone' => $this->clean(
                    $row['nomor_hp'] ?? null
                ),

                'email' => $this->normalizeEmail(
                    $row['email'] ?? null
                ),

                'address' => $this->clean(
                    $row['alamat'] ?? null
                ),
            ];


            /*
             * =====================================================
             * 2. VALIDASI DATA
             * =====================================================
             */
            $existingByNip = $data['nip']
                ? Employee::where('nip', $data['nip'])->first()
                : null;
            $existingByNuptk = $data['nuptk']
                ? Employee::where('nuptk', $data['nuptk'])->first()
                : null;
            $existingByEmail = $data['email']
                ? Employee::where('email', $data['email'])->first()
                : null;

            if (
                $existingByNip
                && $existingByNuptk
                && $existingByNip->id !== $existingByNuptk->id
            ) {
                $errors[] =
                    "Baris {$excelRow}: NIP dan NUPTK mengarah ke data pegawai yang berbeda.";

                continue;
            }

            $existingEmployee = $existingByNip ?? $existingByNuptk;
            if (
                !$existingEmployee
                && $existingByEmail
                && $data['name']
                && mb_strtolower(trim($existingByEmail->name)) === mb_strtolower(trim($data['name']))
            ) {
                $existingEmployee = $existingByEmail;
            }

            $requiredOrNullable = $existingEmployee ? 'nullable' : 'required';

            $validator = Validator::make(
                $data,
                [
                    'nip' => [
                        'nullable',
                        'string',
                        'max:30',
                    ],

                    'nuptk' => [
                        'nullable',
                        'string',
                        'max:30',
                    ],

                    'name' => [
                        $requiredOrNullable,
                        'string',
                        'max:100',
                    ],

                    'gender' => [
                        $requiredOrNullable,
                        'in:L,P',
                    ],

                    'birth_date' => [
                        'nullable',
                        'date',
                    ],

                    'employment_status' => [
                        $requiredOrNullable,
                        'in:PNS,PPPK,Non-ASN',
                    ],

                    /*
                     * Jabatan tidak lagi dipaksa hanya menjadi
                     * Guru / Kepala Sekolah / Tata Usaha.
                     *
                     * Karena Excel sekolah memiliki:
                     * Guru
                     * Kepsek
                     * OPS
                     * TU
                     * Perpstk
                     * Keamanan
                     */
                    'position' => [
                        $requiredOrNullable,
                        'string',
                        'max:100',
                    ],

                    'status' => [
                        'nullable',
                        'in:Aktif,Nonaktif',
                    ],

                    'email' => [
                        'nullable',
                        'email',
                        'max:100',
                    ],
                ],
                [
                    'name.required' =>
                        'Nama Lengkap wajib diisi.',

                    'gender.required' =>
                        'Jenis Kelamin wajib diisi.',

                    'employment_status.required' =>
                        'Status Kepegawaian wajib diisi.',

                    'position.required' =>
                        'Jabatan wajib diisi.',

                    'status.required' =>
                        'Status Pegawai wajib diisi.',
                ]
            );


            /*
             * =====================================================
             * 3. TAMPUNG ERROR BARIS
             * =====================================================
             */
            if ($validator->fails()) {

                foreach (
                    $validator->errors()->all()
                    as $message
                ) {

                    $errors[] =
                        "Baris {$excelRow}: {$message}";
                }

                continue;
            }


            /*
             * =====================================================
             * 4. NIP / NUPTK WAJIB ADA SALAH SATU
             * =====================================================
             */
            if (
                !$data['nip']
                &&
                !$data['nuptk']
            ) {

                $errors[] =
                    "Baris {$excelRow}: NIP atau NUPTK wajib diisi.";

                continue;
            }


            /*
             * =====================================================
             * 5. CEK NIP DUPLIKAT
             * =====================================================
             */
            if (
                $data['nip']
                &&
                (
                    in_array(
                        $data['nip'],
                        $seenNip,
                        true
                    )
                    ||
                    Employee::where('nip', $data['nip'])
                        ->when($existingEmployee, fn ($query) => $query->where('id', '!=', $existingEmployee->id))
                        ->exists()
                )
            ) {

                $errors[] =
                    "Baris {$excelRow}: NIP {$data['nip']} sudah digunakan.";

                continue;
            }


            /*
             * =====================================================
             * 6. CEK NUPTK DUPLIKAT
             * =====================================================
             */
            if (
                $data['nuptk']
                &&
                (
                    in_array(
                        $data['nuptk'],
                        $seenNuptk,
                        true
                    )
                    ||
                    Employee::where('nuptk', $data['nuptk'])
                        ->when($existingEmployee, fn ($query) => $query->where('id', '!=', $existingEmployee->id))
                        ->exists()
                )
            ) {

                $errors[] =
                    "Baris {$excelRow}: NUPTK {$data['nuptk']} sudah digunakan.";

                continue;
            }


            /*
             * =====================================================
             * 7. CEK EMAIL DUPLIKAT
             * =====================================================
             */
            if (
                $data['email']
                &&
                (
                    in_array(
                        $data['email'],
                        $seenEmail,
                        true
                    )
                    ||
                    Employee::where('email', $data['email'])
                        ->when($existingEmployee, fn ($query) => $query->where('id', '!=', $existingEmployee->id))
                        ->exists()
                )
            ) {
                $this->warnings[] =
                    "Baris {$excelRow}: Email {$data['email']} sudah digunakan pegawai lain; data selain email tetap diimpor.";
                $data['email'] = null;
            }


            /*
             * =====================================================
             * 10. SIMPAN DATA YANG SUDAH VALID
             * =====================================================
             */
            $seenNip[] =
                $data['nip'];

            $seenNuptk[] =
                $data['nuptk'];

            $seenEmail[] =
                $data['email'];

            $preparedRows[] = [
                ...$data,

                'employee_id' => $existingEmployee?->id,

                'excel_row' =>
                    $excelRow,
            ];
        }


        /*
         * =========================================================
         * 11. JIKA ADA SATU ERROR, SEMUA IMPORT DIBATALKAN
         * =========================================================
         */
        if (!empty($errors)) {

            throw new \RuntimeException(
                "Import dibatalkan karena terdapat kesalahan:\n\n"
                .
                implode(
                    "\n",
                    $errors
                )
            );
        }


        /*
         * =========================================================
         * 12. SIMPAN KE DATABASE
         * =========================================================
         */
        DB::transaction(
            function () use ($preparedRows) {
                $updatedCount = 0;

                foreach (
                    $preparedRows
                    as $data
                ) {
                    $employeeId = $data['employee_id'];
                    unset($data['employee_id'], $data['excel_row']);

                    $employee = $employeeId
                        ? Employee::findOrFail($employeeId)
                        : new Employee();

                    /*
                     * =================================================
                     * JENIS PEGAWAI
                     * =================================================
                     *
                     * Guru          -> Guru
                     * Kepala Sekolah -> Kepala Sekolah
                     * Selain itu     -> Tenaga Kependidikan
                     */
                    if ($data['position'] !== null) {
                        $data['employee_type'] = $this->employeeTypeFromPosition(
                            $data['position']
                        );
                    }


                    /*
                     * =================================================
                     * STATUS KEPEGAWAIAN DATABASE
                     * =================================================
                     *
                     * UI menggunakan Non-ASN.
                     * Database lama menggunakan Honorer.
                     */
                    if ($data['employment_status'] !== null) {
                        $data['employment_status'] = $this->employmentStatusForDatabase(
                            $data['employment_status']
                        );
                    }


                    /*
                     * =================================================
                     * SIMPAN PEGAWAI
                     * =================================================
                     */
                    $employee->fill(array_filter(
                        $data,
                        static fn ($value) => $value !== null
                    ));
                    $employee->save();
                    $updatedCount += $employeeId !== null ? 1 : 0;
                }


                /*
                 * Jumlah data berhasil.
                 */
                $this->importedCount =
                    count($preparedRows);
                $this->updatedCount = $updatedCount;
            }
        );
    }


    /**
     * Jumlah data yang berhasil diimport.
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getUpdatedCount(): int
    {
        return $this->updatedCount;
    }

    public function getWarnings(): array
    {
        return $this->warnings;
    }


    /**
     * =========================================================
     * MEMBERSIHKAN DATA STRING
     * =========================================================
     */
    private function clean($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim(str_replace("\u{00A0}", ' ', (string) $value));

        return $value === '' || $value === '-' || $value === '—'
            ? null
            : $value;
    }


    /**
     * =========================================================
     * NORMALISASI JENIS KELAMIN
     * =========================================================
     */
    private function normalizeGender($value): ?string
    {
        $value = $this->clean($value);

        if (!$value) {
            return null;
        }

        $normalized =
            strtolower(
                trim($value)
            );

        return match ($normalized) {

            'l',
            'laki-laki',
            'laki laki',
            'laki',
            'pria'
                => 'L',

            'p',
            'perempuan',
            'wanita'
                => 'P',

            default
                => $value,
        };
    }


    /**
     * =========================================================
     * NORMALISASI STATUS KEPEGAWAIAN
     * =========================================================
     *
     * Contoh Excel:
     * PNS
     * P3K
     * P3K PARUH WAKTU
     * PPPK
     * Non-ASN
     */
    private function normalizeEmploymentStatus(
        $value
    ): ?string {

        $value = $this->clean($value);

        if (!$value) {
            return null;
        }

        $normalized =
            strtolower(
                trim($value)
            );

        /*
         * Satukan spasi.
         */
        $normalized =
            preg_replace(
                '/\s+/',
                ' ',
                $normalized
            );

        return match ($normalized) {

            'pns'
                => 'PNS',

            'p3k',
            'pppk',
            'p3k paruh waktu',
            'pppk paruh waktu'
                => 'PPPK',

            'non-asn',
            'non asn',
            'nonasn',
            'honorer',
            'kontrak'
                => 'Non-ASN',

            default
                => $value,
        };
    }


    /**
     * =========================================================
     * NORMALISASI JABATAN
     * =========================================================
     *
     * Contoh Excel:
     * Guru
     * Kepsek
     * Kepala Sekolah
     * OPS
     * TU
     * Perpstk
     * Keamanan
     *
     * Jabatan selain Guru/Kepala Sekolah
     * tetap disimpan sesuai data sekolah.
     */
    private function normalizePosition(
        $value
    ): ?string {

        $value = $this->clean($value);

        if (!$value) {
            return null;
        }

        $normalized = strtolower(trim(preg_replace('/\s+/u', ' ', $value)));

        return match ($normalized) {

            'guru',
            'teacher'
                => 'Guru',

            'kepsek',
            'kepala sekolah',
            'kepala sekolah sd'
                => 'Kepala Sekolah',

            'tu',
            'tata usaha'
                => 'Tata Usaha',

            'ops',
            'operator',
            'operator sekolah'
                => 'OPS',

            'perpstk',
            'perpustakaan',
            'pustakawan',
            'petugas perpustakaan'
                => 'Perpstk',

            'keamanan',
            'satpam',
            'security'
                => 'Keamanan',

            default
                => $value,
        };
    }


    /**
     * =========================================================
     * NORMALISASI STATUS PEGAWAI
     * =========================================================
     *
     * Excel:
     * Aktif
     * Tidak Aktif
     * PNS / PPPK / PPPK Paruh Waktu (legacy employment-status column)
     *
     * Database:
     * Aktif
     * Nonaktif
     */
    private function normalizeEmployeeStatus(
        $value
    ): ?string {

        $value = $this->clean($value);

        if (!$value) {
            return null;
        }

        $normalized = strtolower(trim(preg_replace('/\s+/u', ' ', $value)));

        return match ($normalized) {

            'aktif'
                => 'Aktif',

            'tidak aktif',
            'nonaktif',
            'non aktif',
            'tidakaktif'
                => 'Nonaktif',

            'pns',
            'pppk',
            'p3k',
            'pppk paruh waktu',
            'p3k paruh waktu',
            'non-asn',
            'non asn',
            'nonasn',
            'honorer',
            'kontrak'
                => null,

            default
                => $value,
        };
    }


    /**
     * =========================================================
     * NORMALISASI EMAIL
     * =========================================================
     */
    private function normalizeEmail(
        $value
    ): ?string {

        $value = $this->clean($value);

        if (!$value) {
            return null;
        }

        return strtolower($value);
    }


    /**
     * =========================================================
     * NORMALISASI TANGGAL
     * =========================================================
     */
    private function normalizeDate(
        $value
    ): ?string {

        if (!$value) {
            return null;
        }

        try {

            /*
             * Excel menyimpan tanggal
             * sebagai angka serial.
             */
            if (is_numeric($value)) {

                return Carbon::createFromTimestamp(
                    ((float) $value - 25569)
                    * 86400
                )->format('Y-m-d');
            }


            /*
             * Coba beberapa format umum.
             */
            foreach (
                [
                    'Y-m-d',
                    'd/m/Y',
                    'd-m-Y',
                    'm/d/Y',
                ] as $format
            ) {

                try {

                    return Carbon::createFromFormat(
                        $format,
                        trim((string) $value)
                    )->format('Y-m-d');

                } catch (Throwable) {

                    continue;
                }
            }


            /*
             * Fallback Carbon.
             */
            return Carbon::parse($value)
                ->format('Y-m-d');

        } catch (Throwable) {

            return null;
        }
    }


    /**
     * =========================================================
     * EMPLOYEE TYPE
     * =========================================================
     */
    private function employeeTypeFromPosition(
        string $position
    ): string {

        $normalized =
            strtolower(
                trim($position)
            );

        return match ($normalized) {

            'kepala sekolah'
                => 'Kepala Sekolah',

            'guru'
                => 'Guru',

            default
                => 'Tenaga Kependidikan',
        };
    }


    /**
     * =========================================================
     * STATUS KEPEGAWAIAN UNTUK DATABASE
     * =========================================================
     */
    private function employmentStatusForDatabase(
        string $status
    ): string {

        return match ($status) {

            'Non-ASN'
                => 'Honorer',

            default
                => $status,
        };
    }


}