<?php

namespace App\Http\Controllers;

use App\Exports\EmployeeExport;
use App\Exports\EmployeeTemplateExport;
use App\Imports\EmployeeImport;
use App\Models\Employee;
use App\Models\SchoolClass;
use App\Models\User;
use App\Services\ActivityLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
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
     * Simpan data pegawai.
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
        'Tenaga Administrasi',
        'OPS',
        'Perpstk',
        'Keamanan',
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

        $photoPath = null;

        try {
            $employee = DB::transaction(function () use (
                $validated,
                $request,
                &$photoPath
            ) {
                if ($request->hasFile('photo')) {
                    $photoPath = $request->file('photo')
                        ->store('employees', 'public');

                    if (!$photoPath) {
                        throw new \RuntimeException('Foto pegawai gagal disimpan.');
                    }
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

                return $employee;
            });

            // Catat aktivitas penambahan pegawai.
            app(ActivityLogger::class)->log(
                'created',
                'employees',
                'Menambahkan data pegawai: ' . $employee->name,
                $employee->fresh(),
                null,
                $employee->fresh()->getAttributes()
            );

            return redirect()
                ->route('pegawai.index')
                ->with('success', 'Data pegawai berhasil ditambahkan. Akun login dapat dibuat melalui Manajemen User.');

        } catch (Throwable $e) {

            if ($photoPath) {
                Storage::disk('public')->delete($photoPath);
            }

            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Data pegawai gagal disimpan. Silakan periksa kembali data yang dikirim.',
                ]);
        }
    }

    /**
     * Menampilkan detail pegawai.
     */
    public function show(Employee $pegawai)
    {
        $pegawai->load('user.role');
        $userId = $pegawai->user?->id;

        $homeroomClasses = SchoolClass::query()
            ->where(function ($query) use ($pegawai, $userId) {
                $query->where('homeroom_teacher_id', $pegawai->id);

                if ($userId) {
                    $query->orWhere('homeroom_teacher_user_id', $userId);
                }
            })
            ->with('academicYear')
            ->withCount([
                'students as active_students_count' => fn ($query) => $query
                    ->where('class_student.status', 'Aktif'),
            ])
            ->get()
            ->sortByDesc(fn (SchoolClass $class) => $class->academicYear?->start_date?->getTimestamp() ?? 0)
            ->values();

        return view('pegawai.show', [
            'employee' => $pegawai,
            'homeroomClasses' => $homeroomClasses,
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
     * Update data pegawai dan identitas akun yang terhubung.
     */
    public function update(Request $request, Employee $pegawai)
    {
        $employeeUser = $pegawai->user;

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
        'Tenaga Administrasi',
        'OPS',
        'Perpstk',
        'Keamanan',
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
                Rule::unique('users', 'email')->ignore($employeeUser?->id),
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

        $username = trim((string) ($validated['nip'] ?? ''))
            ?: trim((string) ($validated['nuptk'] ?? ''));

        if ($employeeUser && $username === '') {
            return back()
                ->withInput()
                ->withErrors([
                    'nip' => 'Pegawai yang sudah memiliki akun harus tetap memiliki NIP atau NUPTK.',
                ]);
        }

        if (
            $employeeUser &&
            User::where('username', $username)
                ->where('id', '!=', $employeeUser->id)
                ->exists()
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'nip' => 'NIP/NUPTK tersebut sudah digunakan sebagai username akun lain.',
                ]);
        }

        // Simpan kondisi data sebelum perubahan.
        $oldEmployeeValues = $pegawai->getAttributes();

        $oldPhoto = $pegawai->photo;
        $newPhotoPath = null;

        try {
            DB::transaction(function () use (
                $validated,
                $request,
                $pegawai,
                $employeeUser,
                $username,
                &$newPhotoPath
            ) {
                if ($request->hasFile('photo')) {
                    $newPhotoPath = $request->file('photo')
                        ->store('employees', 'public');

                    if (!$newPhotoPath) {
                        throw new \RuntimeException('Foto pegawai gagal disimpan.');
                    }

                    $validated['photo'] = $newPhotoPath;
                }

                $validated['employee_type'] = $this->employeeTypeFromPosition(
                    $validated['position']
                );

                $validated['employment_status'] = $this->normalizeEmploymentStatus(
                    $validated['employment_status']
                );

                $pegawai->update($validated);

                if ($employeeUser) {
                    $employeeUser->update([
                        'name' => $pegawai->name,
                        'username' => $username,
                        'email' => $pegawai->email,
                    ]);
                }
            });

            if ($newPhotoPath && $oldPhoto) {
                Storage::disk('public')->delete($oldPhoto);
            }

            // Ambil kondisi data setelah perubahan.
            $newEmployeeValues = $pegawai->fresh()->getAttributes();

            // Jangan masukkan timestamp sebagai perubahan data.
            unset(
                $oldEmployeeValues['created_at'],
                $oldEmployeeValues['updated_at']
            );

            unset(
                $newEmployeeValues['created_at'],
                $newEmployeeValues['updated_at']
            );

            // Cari field yang benar-benar berubah.
            $changedOldValues = [];
            $changedNewValues = [];

            foreach ($newEmployeeValues as $key => $newValue) {
                $oldValue = $oldEmployeeValues[$key] ?? null;

                if ((string) $oldValue !== (string) $newValue) {
                    $changedOldValues[$key] = $oldValue;
                    $changedNewValues[$key] = $newValue;
                }
            }

            // Catat aktivitas jika ada perubahan.
            if (!empty($changedNewValues)) {
                app(ActivityLogger::class)->log(
                    'updated',
                    'employees',
                    'Mengubah data pegawai: ' . $pegawai->name,
                    $pegawai->fresh(),
                    $changedOldValues,
                    $changedNewValues
                );
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
        // Simpan data sebelum dihapus.
        $oldEmployeeValues = $pegawai->getAttributes();
        $employeeName = $pegawai->name;

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

            // Catat aktivitas penghapusan setelah transaction berhasil.
            app(ActivityLogger::class)->log(
                'deleted',
                'employees',
                'Menghapus data pegawai: ' . $employeeName,
                $pegawai,
                $oldEmployeeValues,
                null
            );

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

    /**
     * Export PDF.
     */
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
            'Guru' => 'Guru',
            default => 'Tenaga Kependidikan',
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

}