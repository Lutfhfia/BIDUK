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
    protected int $importedCount = 0;

    public function collection(Collection $rows): void
        {
        $preparedRows = [];
        $errors = [];

        $seenNip = [];
        $seenNuptk = [];
        $seenEmail = [];
        $seenUsername = [];

        foreach ($rows as $index => $row) {

            $excelRow = $index + 2;

            $data = [
                'nip' => $this->clean($row['nip'] ?? null),
                'nuptk' => $this->clean($row['nuptk'] ?? null),
                'name' => $this->clean($row['nama_lengkap'] ?? null),
                'gender' => $this->normalizeGender(
                    $this->clean($row['jenis_kelamin'] ?? null)
                ),
                'birth_place' => $this->clean(
                    $row['tempat_lahir'] ?? null
                ),
                'birth_date' => $this->normalizeDate(
                    $row['tanggal_lahir'] ?? null
                ),
                'employment_status' => $this->clean(
                    $row['status_kepegawaian'] ?? null
                ),
                'position' => $this->clean(
                    $row['jabatan'] ?? null
                ),
                'status' => $this->clean(
                    $row['status_pegawai'] ?? null
                ),
                'phone' => $this->clean(
                    $row['nomor_hp'] ?? null
                ),
                'email' => $this->clean(
                    $row['email'] ?? null
                ),
                'address' => $this->clean(
                    $row['alamat'] ?? null
                ),
            ];


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

                    'position' => [
                        'required',
                        'in:Guru,Kepala Sekolah,Tata Usaha',
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


            if ($validator->fails()) {

                foreach ($validator->errors()->all() as $message) {

                    $errors[] =
                        "Baris {$excelRow}: {$message}";
                }

                continue;
            }


            if (!$data['nip'] && !$data['nuptk']) {

                $errors[] =
                    "Baris {$excelRow}: NIP atau NUPTK wajib diisi.";

                continue;
            }


            if (
                $data['nip'] &&
                (
                    in_array($data['nip'], $seenNip, true) ||
                    Employee::where('nip', $data['nip'])->exists()
                )
            ) {

                $errors[] =
                    "Baris {$excelRow}: NIP {$data['nip']} sudah digunakan.";

                continue;
            }


            if (
                $data['nuptk'] &&
                (
                    in_array($data['nuptk'], $seenNuptk, true) ||
                    Employee::where('nuptk', $data['nuptk'])->exists()
                )
            ) {

                $errors[] =
                    "Baris {$excelRow}: NUPTK {$data['nuptk']} sudah digunakan.";

                continue;
            }


            if (
                $data['email'] &&
                (
                    in_array($data['email'], $seenEmail, true) ||
                    Employee::where('email', $data['email'])->exists() ||
                    User::where('email', $data['email'])->exists()
                )
            ) {

                $errors[] =
                    "Baris {$excelRow}: Email {$data['email']} sudah digunakan.";

                continue;
            }


            $username = $data['nip'] ?: $data['nuptk'];


            if (
                in_array($username, $seenUsername, true) ||
                User::where('username', $username)->exists()
            ) {

                $errors[] =
                    "Baris {$excelRow}: Username {$username} sudah digunakan.";

                continue;
            }


            $seenNip[] = $data['nip'];
            $seenNuptk[] = $data['nuptk'];
            $seenEmail[] = $data['email'];
            $seenUsername[] = $username;


            $preparedRows[] = [
                ...$data,
                'username' => $username,
                'excel_row' => $excelRow,
            ];
        }


        if (!empty($errors)) {

            throw new \RuntimeException(
                "Import dibatalkan karena terdapat kesalahan:\n\n" .
                implode("\n", $errors)
            );
        }


        DB::transaction(function () use ($preparedRows) {

            foreach ($preparedRows as $data) {

                $employeeType = match ($data['position']) {

                    'Kepala Sekolah' =>
                        'Kepala Sekolah',

                    'Tata Usaha' =>
                        'Tenaga Kependidikan',

                    default =>
                        'Guru',
                };


                $employmentStatus = match (
                    $data['employment_status']
                ) {

                    'Non-ASN' => 'Honorer',

                    default => $data['employment_status'],
                };


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


                $roleName = match ($data['position']) {

                    'Kepala Sekolah' =>
                        'Kepala Sekolah',

                    'Tata Usaha' =>
                        'Admin',

                    default =>
                        'Guru',
                };


                $role = Role::where(
                    'name',
                    $roleName
                )->first();


                if (!$role) {

                    throw new \RuntimeException(
                        "Role '{$roleName}' belum tersedia."
                    );
                }


                $password =
                    $this->initialPassword(
                        $data['username']
                    );


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


            $this->importedCount =
                count($preparedRows);
        });
    }


    public function getImportedCount(): int
    {
        return $this->importedCount;
    }


    private function clean($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }


    private function normalizeGender($value): ?string
    {
        if (!$value) {
            return null;
        }

        return match (strtolower(trim($value))) {

            'l',
            'laki-laki',
            'laki laki',
            'laki' => 'L',

            'p',
            'perempuan' => 'P',

            default => $value,
        };
    }


    private function normalizeDate($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {

            if (is_numeric($value)) {

                return Carbon::createFromTimestamp(
                    ((float) $value - 25569) * 86400
                )->format('Y-m-d');
            }


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


            return Carbon::parse($value)
                ->format('Y-m-d');

        } catch (Throwable) {

            return null;
        }
    }


    private function initialPassword(string $username): string
    {
        $digits = preg_replace(
            '/\D+/',
            '',
            $username
        );

        $lastFour = substr(
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