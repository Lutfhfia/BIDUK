<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    /*
     * Hanya boleh ada satu tahun ajaran aktif.
     */
    if ($validated['status'] === 'active') {
        AcademicYear::where('status', 'active')
            ->update([
                'status' => 'inactive',
            ]);
    }

    $academicYear = AcademicYear::create($validated);


    return redirect()
        ->route('academic-years.index')
        ->with('success', 'Tahun ajaran berhasil ditambahkan.');
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
         * Jika tahun ajaran ini diaktifkan,
         * nonaktifkan tahun ajaran aktif lainnya.
         */
        if ($validated['status'] === 'active') {
            AcademicYear::where('id', '!=', $academicYear->id)
                ->where('status', 'active')
                ->update([
                    'status' => 'inactive',
                ]);
        }

        $academicYear->update($validated);

        return redirect()
            ->route('academic-years.index')
            ->with('success', 'Tahun ajaran berhasil diperbarui.');
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

        $academicYear->delete();

        return redirect()
            ->route('academic-years.index')
            ->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
