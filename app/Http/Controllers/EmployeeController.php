<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use App\Exports\EmployeeTemplateExport;
use App\Imports\EmployeeImport;
use App\Models\Employee;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Throwable;

class EmployeeController extends Controller
{
    /**
     * Menampilkan daftar pegawai.
     */
    public function index(Request $request)
    {
        $employees = Employee::with([
            'user.role',
            'homeroomClasses.academicYear',
        ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('nip', 'like', "%{$search}%")
                        ->orWhere('nuptk', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('position'), function ($query) use ($request) {
                $query->where('position', $request->position);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pegawai.index', compact('employees'));
    }

    /**
     * Form tambah pegawai.
     */
    public function create()
    {
        return view('pegawai.create');
    }

    /**
     * Simpan pegawai baru + buat akun User otomatis.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => [
                'nullable',
                'string',
                'max:30',
                'unique:employees,nip',
            ],

            'nuptk' => [
                'nullable',
                'string',
                'max:30',
                'unique:employees,nuptk',
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'gender' => [
                'required',
                Rule::in(['L', 'P']),
            ],

            'birth_place' => [
                'nullable',
                'string',
                'max:100',
            ],

            'birth_date' => [
                'nullable',
                'date',
            ],

            'employment_status' => [
                'required',
                Rule::in(['PNS', 'PPPK', 'Non-ASN']),
            ],

            'position' => [
                'required',
                Rule::in([
                    'Guru',
                    'Kepala Sekolah',
                    'Tata Usaha',
                ]),
            ],

            'status' => [
                'required',
                Rule::in(['Aktif', 'Nonaktif']),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:100',
                'unique:employees,email',
                'unique:users,email',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ]);

        if (empty($validated['nip']) && empty($validated['nuptk'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'nip' => 'NIP atau NUPTK wajib diisi karena digunakan sebagai username akun BIDUK.',
                ]);
        }

        $photoPath = null;

        try {
            $employee = DB::transaction(function () use ($validated, $request, &$photoPath) {

                if ($request->hasFile('photo')) {
                    $photoPath = $request->file('photo')
                        ->store('employees', 'public');
                }

                $employeeType = $this->employeeTypeFromPosition(
                    $validated['position']
                );

                $employmentStatus = $this->normalizeEmploymentStatus(
                    $validated['employment_status']
                );

                $employee = Employee::create([
                    'nip' => $validated['nip'] ?? null,
                    'nuptk' => $validated['nuptk'] ?? null,
                    'name' => $validated['name'],
                    'gender' => $validated['gender'],
                    'birth_place' => $validated['birth_place'] ?? null,
                    'birth_date' => $validated['birth_date'] ?? null,
                    'position' => $validated['position'],
                    'employee_type' => $employeeType,
                    'employment_status' => $employmentStatus,
                    'phone' => $validated['phone'] ?? null,
                    'email' => $validated['email'] ?? null,
                    'address' => $validated['address'] ?? null,
                    'photo' => $photoPath,
                    'status' => $validated['status'],
                ]);

                $this->createOrSyncUser($employee);

                return $employee;
            });

            $username = $employee->nip ?: $employee->nuptk;
            $password = $this->initialPassword($username);

            return redirect()
                ->route('pegawai.index')
                ->with(
                    'success',
                    "Pegawai berhasil ditambahkan. Akun BIDUK otomatis dibuat. Username: {$username} | Password awal: {$password}"
                );

        } catch (Throwable $e) {

            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Data pegawai gagal disimpan. Silakan periksa kembali data dan konfigurasi Role BIDUK.',
                ]);
        }
    }

    /**
     * Menampilkan detail pegawai.
     */
    public function show(Employee $pegawai)
    {
        $pegawai->load([
            'user.role',
            'homeroomClasses.academicYear',
        ]);

        return view('pegawai.show', [
            'employee' => $pegawai,
        ]);
    }

    /**
     * Form edit pegawai.
     */
    public function edit(Employee $pegawai)
    {
        return view('pegawai.edit', [
            'employee' => $pegawai,
        ]);
    }

    /**
     * Update pegawai + sinkronisasi User.
     */
    public function update(Request $request, Employee $pegawai)
    {
        $validated = $request->validate([
            'nip' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('employees', 'nip')->ignore($pegawai->id),
            ],

            'nuptk' => [
                'nullable',
                'string',
                'max:30',
                Rule::unique('employees', 'nuptk')->ignore($pegawai->id),
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'gender' => [
                'required',
                Rule::in(['L', 'P']),
            ],

            'birth_place' => [
                'nullable',
                'string',
                'max:100',
            ],

            'birth_date' => [
                'nullable',
                'date',
            ],

            'employment_status' => [
                'required',
                Rule::in(['PNS', 'PPPK', 'Non-ASN']),
            ],

            'position' => [
                'required',
                Rule::in([
                    'Guru',
                    'Kepala Sekolah',
                    'Tata Usaha',
                ]),
            ],

            'status' => [
                'required',
                Rule::in(['Aktif', 'Nonaktif']),
            ],

            'phone' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'nullable',
                'email',
                'max:100',
                Rule::unique('employees', 'email')->ignore($pegawai->id),
                Rule::unique('users', 'email')->ignore($pegawai->user?->id),
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'photo' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048',
            ],
        ]);

        if (empty($validated['nip']) && empty($validated['nuptk'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'nip' => 'NIP atau NUPTK wajib diisi karena digunakan sebagai username akun BIDUK.',
                ]);
        }

        $oldPhoto = $pegawai->photo;
        $newPhotoPath = null;

        try {

            DB::transaction(function () use (
                $validated,
                $request,
                $pegawai,
                &$newPhotoPath
            ) {

                if ($request->hasFile('photo')) {
                    $newPhotoPath = $request->file('photo')
                        ->store('employees', 'public');

                    $validated['photo'] = $newPhotoPath;
                }

                $validated['employee_type'] = $this->employeeTypeFromPosition(
                    $validated['position']
                );

                $validated['employment_status'] = $this->normalizeEmploymentStatus(
                    $validated['employment_status']
                );

                $pegawai->update($validated);

                $this->createOrSyncUser($pegawai->fresh());
            });

            if ($newPhotoPath && $oldPhoto) {
                Storage::disk('public')->delete($oldPhoto);
            }

            return redirect()
                ->route('pegawai.index')
                ->with('success', 'Data pegawai berhasil diperbarui.');

        } catch (Throwable $e) {

            if ($newPhotoPath) {
                Storage::disk('public')->delete($newPhotoPath);
            }

            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Data pegawai gagal diperbarui.',
                ]);
        }
    }

    /**
     * Hapus pegawai dan akun user terkait.
     */
    public function destroy(Employee $pegawai)
    {
        $photo = $pegawai->photo;

        try {

            DB::transaction(function () use ($pegawai) {

                if ($pegawai->user) {
                    $pegawai->user->delete();
                }

                $pegawai->delete();
            });

            if ($photo) {
                Storage::disk('public')->delete($photo);
            }

            return redirect()
                ->route('pegawai.index')
                ->with('success', 'Data pegawai dan akun BIDUK berhasil dihapus.');

        } catch (Throwable $e) {

            report($e);

            return redirect()
                ->route('pegawai.index')
                ->withErrors([
                    'error' => 'Data pegawai gagal dihapus.',
                ]);
        }
    }

    /**
     * Download template Excel.
     */
    public function downloadTemplate()
    {
        return Excel::download(
            new EmployeeTemplateExport(),
            'template-data-pegawai-biduk.xlsx'
        );
    }
    public function exportPdf()
{
    $employees = Employee::with([
        'homeroomClasses.academicYear',
    ])->get();

    $pdf = Pdf::loadView('pegawai.pdf', compact('employees'));

    $pdf->setPaper('a4', 'landscape');

    return $pdf->download('data-pegawai-biduk.pdf');
}

    /**
     * Import Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls',
                'max:5120',
            ],
        ]);

        try {

            $import = new EmployeeImport();

            Excel::import(
                $import,
                $request->file('file')
            );

            return redirect()
                ->route('pegawai.index')
                ->with(
                    'success',
                    "Import berhasil. {$import->getImportedCount()} data pegawai berhasil ditambahkan beserta akun BIDUK."
                );

        } catch (Throwable $e) {

            report($e);

            return redirect()
                ->route('pegawai.index')
                ->withErrors([
                    'import' => $e->getMessage(),
                ]);
        }
    }

    /**
     * Export seluruh data pegawai.
     */
    public function exportExcel()
    {
        return Excel::download(
            new EmployeeExport(),
            'data-pegawai-biduk.xlsx'
        );
    }

    /**
     * Menentukan employee_type berdasarkan jabatan.
     */
    private function employeeTypeFromPosition(string $position): string
    {
        return match ($position) {
            'Kepala Sekolah' => 'Kepala Sekolah',
            'Tata Usaha' => 'Tenaga Kependidikan',
            default => 'Guru',
        };
    }

    /**
     * Menyesuaikan status kepegawaian dengan enum database lama.
     *
     * Database saat ini menggunakan:
     * PNS, PPPK, Honorer, Kontrak.
     *
     * Di UI BIDUK kita tampilkan Non-ASN.
     */
    private function normalizeEmploymentStatus(string $status): string
    {
        return match ($status) {
            'Non-ASN' => 'Honorer',
            default => $status,
        };
    }

    /**
     * Menentukan role BIDUK berdasarkan jabatan.
     */
    private function roleNameFromPosition(string $position): string
    {
        return match ($position) {
            'Kepala Sekolah' => 'Kepala Sekolah',
            'Tata Usaha' => 'Admin',
            default => 'Guru',
        };
    }

    /**
     * Username otomatis.
     */
    private function usernameForEmployee(Employee $employee): string
    {
        $username = $employee->nip ?: $employee->nuptk;

        if (!$username) {
            throw ValidationException::withMessages([
                'nip' => 'Pegawai harus memiliki NIP atau NUPTK untuk dibuatkan akun.',
            ]);
        }

        return trim($username);
    }

    /**
     * Password awal sementara.
     */
    private function initialPassword(string $username): string
    {
        $digits = preg_replace('/\D+/', '', $username);

        $lastFour = substr(
            str_pad($digits, 4, '0', STR_PAD_LEFT),
            -4
        );

        return 'BIDUK@' . $lastFour;
    }

    /**
     * Membuat atau menyinkronkan akun User.
     */
    private function createOrSyncUser(Employee $employee): User
    {
        $username = $this->usernameForEmployee($employee);

        $roleName = $this->roleNameFromPosition(
            $employee->position
        );

        $role = Role::where('name', $roleName)->first();

        if (!$role) {
            throw new \RuntimeException(
                "Role '{$roleName}' belum tersedia di tabel roles."
            );
        }

        $existingUser = User::where('username', $username)
            ->when($employee->user, function ($query) use ($employee) {
                $query->where('id', '!=', $employee->user->id);
            })
            ->first();

        if ($existingUser) {
            throw new \RuntimeException(
                "Username '{$username}' sudah digunakan oleh akun lain."
            );
        }

        $user = $employee->user;

        if (!$user) {

            $user = User::create([
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'username' => $username,
                'email' => $employee->email,
                'password' => $this->initialPassword($username),
                'role_id' => $role->id,
                'status' => $employee->status,
            ]);

        } else {

            $user->update([
                'employee_id' => $employee->id,
                'name' => $employee->name,
                'username' => $username,
                'email' => $employee->email,
                'role_id' => $role->id,
                'status' => $employee->status,
            ]);
        }

        return $user;
    }
}