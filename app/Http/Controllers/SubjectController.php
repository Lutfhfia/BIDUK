<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SubjectController extends Controller
{
    /**
     * Menampilkan daftar mata pelajaran.
     */
    public function index(Request $request)
    {
        $query = Subject::query();

        // Pencarian
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%');
            });
        }

        // Filter kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $subjects = $query
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // Untuk pilihan filter kategori
        $categories = Subject::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

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
                'nullable',
                'string',
                'max:50',
            ],
            'status' => [
                'required',
                Rule::in(['Aktif', 'Nonaktif']),
            ],
        ], [
            'code.required' => 'Kode mata pelajaran wajib diisi.',
            'code.unique' => 'Kode mata pelajaran sudah digunakan.',
            'code.max' => 'Kode mata pelajaran maksimal 20 karakter.',
            'name.required' => 'Nama mata pelajaran wajib diisi.',
            'name.max' => 'Nama mata pelajaran maksimal 100 karakter.',
            'category.max' => 'Kategori maksimal 50 karakter.',
            'status.required' => 'Status mata pelajaran wajib dipilih.',
            'status.in' => 'Status hanya boleh Aktif atau Nonaktif.',
        ]);

        $subject = Subject::create($validated);

        // Catat aktivitas penambahan mata pelajaran.
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
            ->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail mata pelajaran.
     */
    public function show(Subject $subject)
    {
        return view('subjects.show', compact('subject'));
    }

    /**
     * Menampilkan form edit mata pelajaran.
     */
    public function edit(Subject $subject)
    {
        return view('subjects.edit', compact('subject'));
    }

    /**
     * Memperbarui mata pelajaran.
     */
    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('subjects', 'code')->ignore($subject->id),
            ],
            'name' => [
                'required',
                'string',
                'max:100',
            ],
            'category' => [
                'nullable',
                'string',
                'max:50',
            ],
            'status' => [
                'required',
                Rule::in(['Aktif', 'Nonaktif']),
            ],
        ], [
            'code.required' => 'Kode mata pelajaran wajib diisi.',
            'code.unique' => 'Kode mata pelajaran sudah digunakan.',
            'code.max' => 'Kode mata pelajaran maksimal 20 karakter.',
            'name.required' => 'Nama mata pelajaran wajib diisi.',
            'name.max' => 'Nama mata pelajaran maksimal 100 karakter.',
            'category.max' => 'Kategori maksimal 50 karakter.',
            'status.required' => 'Status mata pelajaran wajib dipilih.',
            'status.in' => 'Status hanya boleh Aktif atau Nonaktif.',
        ]);

        // Simpan kondisi sebelum perubahan.
        $oldSubjectValues = $subject->getAttributes();

        $subject->update($validated);

        // Ambil kondisi setelah perubahan.
        $newSubjectValues = $subject->fresh()->getAttributes();

        // Jangan catat timestamp sebagai perubahan.
        unset(
            $oldSubjectValues['created_at'],
            $oldSubjectValues['updated_at']
        );

        unset(
            $newSubjectValues['created_at'],
            $newSubjectValues['updated_at']
        );

        // Cari field yang benar-benar berubah.
        $changedOldValues = [];
        $changedNewValues = [];

        foreach ($newSubjectValues as $key => $newValue) {
            $oldValue = $oldSubjectValues[$key] ?? null;

            if ((string) $oldValue !== (string) $newValue) {
                $changedOldValues[$key] = $oldValue;
                $changedNewValues[$key] = $newValue;
            }
        }

        // Catat aktivitas jika memang ada perubahan.
        if (!empty($changedNewValues)) {
            app(ActivityLogger::class)->log(
                'updated',
                'subjects',
                'Mengubah mata pelajaran: ' . $subject->name,
                $subject->fresh(),
                $changedOldValues,
                $changedNewValues
            );
        }

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    /**
     * Menghapus mata pelajaran.
     */
    public function destroy(Subject $subject)
    {
        // Simpan data sebelum dihapus.
        $oldSubjectValues = $subject->getAttributes();
        $subjectName = $subject->name;

        $subject->delete();

        // Catat aktivitas penghapusan.
        app(ActivityLogger::class)->log(
            'deleted',
            'subjects',
            'Menghapus mata pelajaran: ' . $subjectName,
            $subject,
            $oldSubjectValues,
            null
        );

        return redirect()
            ->route('subjects.index')
            ->with('success', 'Mata pelajaran berhasil dihapus.');
    }
}