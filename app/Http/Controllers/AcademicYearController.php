<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class AcademicYearController extends Controller
{
    /**
     * Menampilkan daftar tahun ajaran.
     */
    public function index()
    {
        $academicYears = AcademicYear::query()
            ->orderByDesc('start_date')
            ->paginate(10);

        return view('admin.academic-years.index', compact(
            'academicYears'
        ));
    }

    /**
     * Menampilkan form tambah tahun ajaran.
     */
    public function create()
    {
        return view('admin.academic-years.create');
    }

    /**
     * Menyimpan tahun ajaran baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],

            'current_semester' => [
                'required',
                Rule::in(['Ganjil', 'Genap']),
            ],
        ], [
            'name.required' => 'Nama tahun ajaran wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'end_date.required' => 'Tanggal selesai wajib diisi.',
            'end_date.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'status.required' => 'Status tahun ajaran wajib dipilih.',
            'current_semester.required' => 'Semester berjalan wajib dipilih.',
        ]);

        try {
            /*
             * Simpan tahun ajaran aktif yang akan
             * otomatis dinonaktifkan.
             */
            $previousActiveYears = collect();

            $academicYear = DB::transaction(function () use (
                $validated,
                &$previousActiveYears
            ) {
                /*
                 * Hanya boleh ada satu tahun ajaran aktif.
                 */
                if ($validated['status'] === 'active') {
                    $previousActiveYears = AcademicYear::query()
                        ->where('status', 'active')
                        ->get();

                    AcademicYear::where('status', 'active')
                        ->update([
                            'status' => 'inactive',
                        ]);
                }

                return AcademicYear::create($validated);
            });

            /*
             * Catat aktivitas pembuatan tahun ajaran.
             */
            app(ActivityLogger::class)->log(
                'created',
                'academic_years',
                'Menambahkan tahun ajaran: ' . $academicYear->name,
                $academicYear->fresh(),
                null,
                $academicYear->fresh()->getAttributes()
            );

            /*
             * Catat otomatisasi perubahan status
             * tahun ajaran aktif sebelumnya.
             */
            foreach ($previousActiveYears as $previousYear) {
                app(ActivityLogger::class)->log(
                    'updated',
                    'academic_years',
                    'Menonaktifkan tahun ajaran otomatis karena tahun ajaran '
                        . $academicYear->name
                        . ' diaktifkan: '
                        . $previousYear->name,
                    $previousYear->fresh(),
                    [
                        'status' => 'active',
                    ],
                    [
                        'status' => 'inactive',
                    ]
                );
            }

            return redirect()
                ->route('academic-years.index')
                ->with('success', 'Tahun ajaran berhasil ditambahkan.');

        } catch (Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Tahun ajaran gagal ditambahkan.',
                ]);
        }
    }

    /**
     * Menampilkan detail tahun ajaran.
     */
    public function show(AcademicYear $academicYear)
    {
        $academicYear->load([
            'semesters',
            'classes',
        ]);

        return view('admin.academic-years.show', compact(
            'academicYear'
        ));
    }

    /**
     * Menampilkan form edit tahun ajaran.
     */
    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact(
            'academicYear'
        ));
    }

    /**
     * Memperbarui tahun ajaran.
     */
    public function update(
        Request $request,
        AcademicYear $academicYear
    ) {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:20',
            ],

            'start_date' => [
                'required',
                'date',
            ],

            'end_date' => [
                'required',
                'date',
                'after:start_date',
            ],

            'status' => [
                'required',
                Rule::in(['active', 'inactive']),
            ],

            'current_semester' => [
                'required',
                Rule::in(['Ganjil', 'Genap']),
            ],
        ]);

        /*
         * Simpan data sebelum perubahan.
         */
        $oldAcademicYearValues = $academicYear->getAttributes();

        /*
         * Simpan tahun ajaran lain yang akan
         * otomatis dinonaktifkan.
         */
        $previousActiveYears = collect();

        try {
            DB::transaction(function () use (
                $validated,
                $academicYear,
                &$previousActiveYears
            ) {
                /*
                 * Jika tahun ajaran ini diaktifkan,
                 * nonaktifkan tahun ajaran aktif lainnya.
                 */
                if ($validated['status'] === 'active') {
                    $previousActiveYears = AcademicYear::query()
                        ->where('id', '!=', $academicYear->id)
                        ->where('status', 'active')
                        ->get();

                    AcademicYear::where('id', '!=', $academicYear->id)
                        ->where('status', 'active')
                        ->update([
                            'status' => 'inactive',
                        ]);
                }

                $academicYear->update($validated);
            });

            /*
             * Ambil data setelah perubahan.
             */
            $newAcademicYearValues = $academicYear
                ->fresh()
                ->getAttributes();

            /*
             * Jangan anggap timestamp sebagai perubahan.
             */
            unset(
                $oldAcademicYearValues['created_at'],
                $oldAcademicYearValues['updated_at']
            );

            unset(
                $newAcademicYearValues['created_at'],
                $newAcademicYearValues['updated_at']
            );

            /*
             * Cari field yang benar-benar berubah.
             */
            $changedOldValues = [];
            $changedNewValues = [];

            foreach ($newAcademicYearValues as $key => $newValue) {
                $oldValue = $oldAcademicYearValues[$key] ?? null;

                if ((string) $oldValue !== (string) $newValue) {
                    $changedOldValues[$key] = $oldValue;
                    $changedNewValues[$key] = $newValue;
                }
            }

            /*
             * Catat update tahun ajaran jika memang
             * terdapat perubahan.
             */
            if (!empty($changedNewValues)) {
                app(ActivityLogger::class)->log(
                    'updated',
                    'academic_years',
                    'Mengubah tahun ajaran: ' . $academicYear->name,
                    $academicYear->fresh(),
                    $changedOldValues,
                    $changedNewValues
                );
            }

            /*
             * Catat tahun ajaran lain yang otomatis
             * berubah dari active menjadi inactive.
             */
            foreach ($previousActiveYears as $previousYear) {
                app(ActivityLogger::class)->log(
                    'updated',
                    'academic_years',
                    'Menonaktifkan tahun ajaran otomatis karena tahun ajaran '
                        . $academicYear->name
                        . ' diaktifkan: '
                        . $previousYear->name,
                    $previousYear->fresh(),
                    [
                        'status' => 'active',
                    ],
                    [
                        'status' => 'inactive',
                    ]
                );
            }

            return redirect()
                ->route('academic-years.index')
                ->with('success', 'Tahun ajaran berhasil diperbarui.');

        } catch (Throwable $e) {

            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'error' => 'Tahun ajaran gagal diperbarui.',
                ]);
        }
    }

    /**
     * Menghapus tahun ajaran.
     */
    public function destroy(AcademicYear $academicYear)
    {
        /*
         * Jangan hapus tahun ajaran yang masih memiliki
         * data kelas atau semester.
         */
        if (
            $academicYear->classes()->exists() ||
            $academicYear->semesters()->exists()
        ) {
            return back()->withErrors([
                'delete' =>
                    'Tahun ajaran tidak dapat dihapus karena masih memiliki data kelas atau semester.',
            ]);
        }

        /*
         * Simpan data sebelum dihapus.
         */
        $oldAcademicYearValues = $academicYear->getAttributes();
        $academicYearName = $academicYear->name;

        try {
            $academicYear->delete();

            /*
             * Catat aktivitas penghapusan.
             */
            app(ActivityLogger::class)->log(
                'deleted',
                'academic_years',
                'Menghapus tahun ajaran: ' . $academicYearName,
                $academicYear,
                $oldAcademicYearValues,
                null
            );

            return redirect()
                ->route('academic-years.index')
                ->with('success', 'Tahun ajaran berhasil dihapus.');

        } catch (Throwable $e) {

            report($e);

            return back()
                ->withErrors([
                    'error' => 'Tahun ajaran gagal dihapus.',
                ]);
        }
    }
}