<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
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

    /**
     * Proses seluruh baris Excel.
     */
    public function collection(Collection $rows): void
    {
        $preparedRows = [];
        $errors = [];

        /*
         * Untuk mendeteksi data duplikat di file Excel
         * maupun yang sudah ada di database.
         */
        $seenNip = [];
        $seenNuptk = [];
        $seenEmail = [];
        $seenUsername = [];

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
                        'required',
                        'string',
                        'max:100',
                    ],

                    'gender' => [
                        'required',
                        'in:L,P',
                    ],

                    'birth_date' => [
                        'nullable',
                        'date',
                    ],

                    'employment_status' => [
                        'required',
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
                        'required',
                        'string',
                        'max:100',
                    ],

                    'status' => [
                        'required',
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
                    Employee::where(
                        'nip',
                        $data['nip']
                    )->exists()
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
                    Employee::where(
                        'nuptk',
                        $data['nuptk']
                    )->exists()
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
                    Employee::where(
                        'email',
                        $data['email']
                    )->exists()
                    ||
                    User::where(
                        'email',
                        $data['email']
                    )->exists()
                )
            ) {

                $errors[] =
                    "Baris {$excelRow}: Email {$data['email']} sudah digunakan.";

                continue;
            }


            /*
             * =====================================================
             * 8. TENTUKAN USERNAME
             * =====================================================
             */
            $username =
                $data['nip']
                ?:
                $data['nuptk'];


            /*
             * =====================================================
             * 9. CEK USERNAME DUPLIKAT
             * =====================================================
             */
            if (
                in_array(
                    $username,
                    $seenUsername,
                    true
                )
                ||
                User::where(
                    'username',
                    $username
                )->exists()
            ) {

                $errors[] =
                    "Baris {$excelRow}: Username {$username} sudah digunakan.";

                continue;
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

            $seenUsername[] =
                $username;


            $preparedRows[] = [
                ...$data,

                'username' =>
                    $username,

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

                foreach (
                    $preparedRows
                    as $data
                ) {

                    /*
                     * =================================================
                     * JENIS PEGAWAI
                     * =================================================
                     *
                     * Guru          -> Guru
                     * Kepala Sekolah -> Kepala Sekolah
                     * Selain itu     -> Tenaga Kependidikan
                     */
                    $employeeType =
                        $this->employeeTypeFromPosition(
                            $data['position']
                        );


                    /*
                     * =================================================
                     * STATUS KEPEGAWAIAN DATABASE
                     * =================================================
                     *
                     * UI menggunakan Non-ASN.
                     * Database lama menggunakan Honorer.
                     */
                    $employmentStatus =
                        $this->employmentStatusForDatabase(
                            $data['employment_status']
                        );


                    /*
                     * =================================================
                     * SIMPAN PEGAWAI
                     * =================================================
                     */
                    $employee = Employee::create([

                        'nip' =>
                            $data['nip'],

                        'nuptk' =>
                            $data['nuptk'],

                        'name' =>
                            $data['name'],

                        'gender' =>
                            $data['gender'],

                        'birth_place' =>
                            $data['birth_place'],

                        'birth_date' =>
                            $data['birth_date'],

                        'position' =>
                            $data['position'],

                        'employee_type' =>
                            $employeeType,

                        'employment_status' =>
                            $employmentStatus,

                        'phone' =>
                            $data['phone'],

                        'email' =>
                            $data['email'],

                        'address' =>
                            $data['address'],

                        'status' =>
                            $data['status'],
                    ]);


                    /*
                     * =================================================
                     * TENTUKAN ROLE BIDUK
                     * =================================================
                     */
                    $roleName =
                        $this->roleNameFromPosition(
                            $data['position']
                        );


                    /*
                     * Cari role.
                     */
                    $role =
                        Role::where(
                            'name',
                            $roleName
                        )->first();


                    if (!$role) {

                        throw new \RuntimeException(
                            "Role '{$roleName}' belum tersedia."
                        );
                    }


                    /*
                     * =================================================
                     * PASSWORD AWAL
                     * =================================================
                     */
                    $password =
                        $this->initialPassword(
                            $data['username']
                        );


                    /*
                     * =================================================
                     * BUAT AKUN USER
                     * =================================================
                     */
                    User::create([

                        'employee_id' =>
                            $employee->id,

                        'name' =>
                            $employee->name,

                        'username' =>
                            $data['username'],

                        'email' =>
                            $employee->email,

                        'password' =>
                            $password,

                        'role_id' =>
                            $role->id,

                        'status' =>
                            $employee->status,
                    ]);
                }


                /*
                 * Jumlah data berhasil.
                 */
                $this->importedCount =
                    count($preparedRows);
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

        $value = trim(
            (string) $value
        );

        return $value === ''
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

        $normalized =
            strtolower(
                trim($value)
            );

        $normalized =
            preg_replace(
                '/\s+/',
                ' ',
                $normalized
            );

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

        $normalized =
            strtolower(
                trim($value)
            );

        $normalized =
            preg_replace(
                '/\s+/',
                ' ',
                $normalized
            );

        return match ($normalized) {

            'aktif'
                => 'Aktif',

            'tidak aktif',
            'nonaktif',
            'non aktif',
            'tidakaktif'
                => 'Nonaktif',

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


    /**
     * =========================================================
     * ROLE BIDUK
     * =========================================================
     *
     * Guru            -> Guru
     * Kepala Sekolah  -> Kepala Sekolah
     * Selain itu      -> Admin
     *
     * Jadi:
     * OPS       -> Admin
     * TU        -> Admin
     * Perpstk   -> Admin
     * Keamanan  -> Admin
     */
    private function roleNameFromPosition(
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
                => 'Admin',
        };
    }


    /**
     * =========================================================
     * PASSWORD AWAL
     * =========================================================
     *
     * Contoh:
     * NIP 198507012010011001
     * menjadi:
     * BIDUK@1001
     */
    private function initialPassword(
        string $username
    ): string {

        $digits =
            preg_replace(
                '/\D+/',
                '',
                $username
            );

        $lastFour =
            substr(
                str_pad(
                    $digits,
                    4,
                    '0',
                    STR_PAD_LEFT
                ),
                -4
            );

        return 'BIDUK@' . $lastFour;
    }
}