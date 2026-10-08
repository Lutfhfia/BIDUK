<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * Kategori yang diperbolehkan.
     */
    private const CATEGORIES = [
        'Intrakurikuler',
        'Ekstrakurikuler',
        'Kokurikuler',
    ];

    /**
     * Urutan standar mata pelajaran sesuai Buku Rapot.
     */
    private const SUBJECT_ORDER = [
        'Pendidikan Agama dan Budi Pekerti' => 1,
        'Pendidikan Pancasila' => 2,
        'Bahasa Indonesia' => 3,
        'Matematika' => 4,
        'Ilmu Pengetahuan Alam dan Sosial' => 5,
        'Pendidikan Jasmani, Olahraga dan Kesehatan' => 6,
        'Bahasa Inggris' => 7,
        'Seni dan Budaya' => 8,
        'Muatan Lokal' => 9,
    ];

    /**
     * Menentukan nomor urut mata pelajaran.
     *
     * Mata pelajaran yang belum masuk daftar akan diletakkan
     * setelah mata pelajaran standar.
     */
    private function getSortOrder(string $name): int
    {
        $normalizedName = trim($name);

        return self::SUBJECT_ORDER[$normalizedName] ?? 999;
    }

    /**
     * Menampilkan daftar mata pelajaran.
     */
    public function index(Request $request)
    {
        $query = Subject::query();

        /*
         * Pencarian.
         */
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        /*
         * Filter kategori.
         */
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        /*
         * Filter status.
         */
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        /*
         * Urutkan berdasarkan sort_order,
         * kemudian nama untuk data tambahan.
         */
        $subjects = $query
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        /*
         * Kategori dibuat tetap agar hanya ada 3 pilihan.
         */
        $categories = collect(self::CATEGORIES);

        return view('subjects.index', compact(
            'subjects',
            'categories'
        ));
    }

    /**
     * Menampilkan form tambah mata pelajaran.
     */
    public function create()
    {
        return view('subjects.create');
    }

    /**
     * Menyimpan mata pelajaran baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                'unique:subjects,code',
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'category' => [
                'required',
                'string',
                Rule::in(self::CATEGORIES),
            ],

            'status' => [
                'required',
                Rule::in([
                    'Aktif',
                    'Nonaktif',
                ]),
            ],
        ], [
            'code.required' =>
                'Kode mata pelajaran wajib diisi.',

            'code.unique' =>
                'Kode mata pelajaran sudah digunakan.',

            'code.max' =>
                'Kode mata pelajaran maksimal 20 karakter.',

            'name.required' =>
                'Nama mata pelajaran wajib diisi.',

            'name.max' =>
                'Nama mata pelajaran maksimal 100 karakter.',

            'category.required' =>
                'Kategori mata pelajaran wajib dipilih.',

            'category.in' =>
                'Kategori hanya boleh Intrakurikuler, Ekstrakurikuler, atau Kokurikuler.',

            'status.required' =>
                'Status mata pelajaran wajib dipilih.',

            'status.in' =>
                'Status hanya boleh Aktif atau Nonaktif.',
        ]);

        /*
         * Tentukan urutan otomatis.
         */
        $validated['sort_order'] =
            $this->getSortOrder(
                $validated['name']
            );

        $subject = Subject::create($validated);

        /*
         * Catat aktivitas.
         */
        app(ActivityLogger::class)->log(
            'created',
            'subjects',
            'Menambahkan mata pelajaran: ' . $subject->name,
            $subject->fresh(),
            null,
            $subject->fresh()->getAttributes()
        );

        return redirect()
            ->route('subjects.index')
            ->with(
                'success',
                'Mata pelajaran berhasil ditambahkan.'
            );
    }

    /**
     * Menampilkan detail mata pelajaran.
     */
    public function show(Subject $subject)
    {
        return view(
            'subjects.show',
            compact('subject')
        );
    }

    /**
     * Menampilkan form edit mata pelajaran.
     */
    public function edit(Subject $subject)
    {
        return view(
            'subjects.edit',
            compact('subject')
        );
    }

    /**
     * Memperbarui mata pelajaran.
     */
    public function update(
        Request $request,
        Subject $subject
    ) {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique(
                    'subjects',
                    'code'
                )->ignore($subject->id),
            ],

            'name' => [
                'required',
                'string',
                'max:100',
            ],

            'category' => [
                'required',
                'string',
                Rule::in(self::CATEGORIES),
            ],

            'status' => [
                'required',
                Rule::in([
                    'Aktif',
                    'Nonaktif',
                ]),
            ],
        ], [
            'code.required' =>
                'Kode mata pelajaran wajib diisi.',

            'code.unique' =>
                'Kode mata pelajaran sudah digunakan.',

            'code.max' =>
                'Kode mata pelajaran maksimal 20 karakter.',

            'name.required' =>
                'Nama mata pelajaran wajib diisi.',

            'name.max' =>
                'Nama mata pelajaran maksimal 100 karakter.',

            'category.required' =>
                'Kategori mata pelajaran wajib dipilih.',

            'category.in' =>
                'Kategori hanya boleh Intrakurikuler, Ekstrakurikuler, atau Kokurikuler.',

            'status.required' =>
                'Status mata pelajaran wajib dipilih.',

            'status.in' =>
                'Status hanya boleh Aktif atau Nonaktif.',
        ]);

        /*
         * Hitung ulang urutan berdasarkan nama.
         */
        $validated['sort_order'] =
            $this->getSortOrder(
                $validated['name']
            );

        /*
         * Simpan kondisi sebelum perubahan.
         */
        $oldSubjectValues =
            $subject->getAttributes();

        /*
         * Update.
         */
        $subject->update($validated);

        /*
         * Ambil kondisi setelah perubahan.
         */
        $newSubjectValues =
            $subject->fresh()->getAttributes();

        /*
         * Jangan catat timestamp.
         */
        unset(
            $oldSubjectValues['created_at'],
            $oldSubjectValues['updated_at']
        );

        unset(
            $newSubjectValues['created_at'],
            $newSubjectValues['updated_at']
        );

        /*
         * Cari field yang berubah.
         */
        $changedOldValues = [];
        $changedNewValues = [];

        foreach (
            $newSubjectValues
            as $key => $newValue
        ) {
            $oldValue =
                $oldSubjectValues[$key] ?? null;

            if (
                (string) $oldValue
                !==
                (string) $newValue
            ) {
                $changedOldValues[$key] =
                    $oldValue;

                $changedNewValues[$key] =
                    $newValue;
            }
        }

        /*
         * Catat aktivitas jika ada perubahan.
         */
        if (!empty($changedNewValues)) {
            app(ActivityLogger::class)->log(
                'updated',
                'subjects',
                'Mengubah mata pelajaran: ' .
                    $subject->name,
                $subject->fresh(),
                $changedOldValues,
                $changedNewValues
            );
        }

        return redirect()
            ->route('subjects.index')
            ->with(
                'success',
                'Mata pelajaran berhasil diperbarui.'
            );
    }

    /**
     * Menghapus mata pelajaran.
     */
    public function destroy(Subject $subject)
    {
        /*
         * Simpan data sebelum dihapus.
         */
        $oldSubjectValues =
            $subject->getAttributes();

        $subjectName =
            $subject->name;

        /*
         * Hapus.
         */
        $subject->delete();

        /*
         * Catat aktivitas.
         */
        app(ActivityLogger::class)->log(
            'deleted',
            'subjects',
            'Menghapus mata pelajaran: ' .
                $subjectName,
            $subject,
            $oldSubjectValues,
            null
        );

        return redirect()
            ->route('subjects.index')
            ->with(
                'success',
                'Mata pelajaran berhasil dihapus.'
            );
    }
}