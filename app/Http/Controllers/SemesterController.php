<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SemesterController extends Controller
{
    public function index()
    {
        $semesters = Semester::with('academicYear')->orderByDesc('academic_year_id')->orderBy('name')->paginate(10);
        return view('admin.semesters.index', compact('semesters'));
    }

    public function create()
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        return view('admin.semesters.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => ['required', Rule::in(['Ganjil', 'Genap'])],
        ]);
        $alreadyExists = Semester::where('academic_year_id', $validated['academic_year_id'])->where('name', $validated['name'])->exists();
        if ($alreadyExists) {
            return back()->withInput()->withErrors(['name' => 'Semester tersebut sudah tersedia pada tahun ajaran yang dipilih.']);
        }
        Semester::create($validated);
        return redirect()->route('semesters.index')->with('success', 'Semester berhasil ditambahkan.');
    }

    public function show(Semester $semester)
    {
        $semester->load('academicYear');
        return view('admin.semesters.show', compact('semester'));
    }

    public function edit(Semester $semester)
    {
        $academicYears = AcademicYear::orderByDesc('start_date')->get();
        return view('admin.semesters.edit', compact('semester', 'academicYears'));
    }

    public function update(Request $request, Semester $semester)
    {
        $validated = $request->validate([
            'academic_year_id' => ['required', 'exists:academic_years,id'],
            'name' => ['required', Rule::in(['Ganjil', 'Genap'])],
        ]);
        $alreadyExists = Semester::where('academic_year_id', $validated['academic_year_id'])->where('name', $validated['name'])->where('id', '!=', $semester->id)->exists();
        if ($alreadyExists) {
            return back()->withInput()->withErrors(['name' => 'Semester tersebut sudah tersedia pada tahun ajaran yang dipilih.']);
        }
        $semester->update($validated);
        return redirect()->route('semesters.index')->with('success', 'Semester berhasil diperbarui.');
    }

    public function destroy(Semester $semester)
    {
        $semester->delete();
        return redirect()->route('semesters.index')->with('success', 'Semester berhasil dihapus.');
    }
}
