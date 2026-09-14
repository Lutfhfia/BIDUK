<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicYearController extends Controller
{
    public function index()
    {
        $academicYears = AcademicYear::query()->orderByDesc('start_date')->paginate(10);
        return view('admin.academic-years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('admin.academic-years.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'current_semester' => ['required', Rule::in(['Ganjil', 'Genap'])],
        ]);
        if ($validated['status'] === 'active') {
            AcademicYear::where('status', 'active')->update(['status' => 'inactive']);
        }
        AcademicYear::create($validated);
        return redirect()->route('academic-years.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function show(AcademicYear $academicYear)
    {
        $academicYear->load(['semesters', 'classes']);
        return view('admin.academic-years.show', compact('academicYear'));
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic-years.edit', compact('academicYear'));
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'current_semester' => ['required', Rule::in(['Ganjil', 'Genap'])],
        ]);
        if ($validated['status'] === 'active') {
            AcademicYear::where('id', '!=', $academicYear->id)->where('status', 'active')->update(['status' => 'inactive']);
        }
        $academicYear->update($validated);
        return redirect()->route('academic-years.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        if ($academicYear->classes()->exists() || $academicYear->semesters()->exists()) {
            return back()->withErrors(['delete' => 'Tahun ajaran tidak dapat dihapus karena masih memiliki data kelas atau semester.']);
        }
        $academicYear->delete();
        return redirect()->route('academic-years.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}
