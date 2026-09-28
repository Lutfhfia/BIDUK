<?php

namespace App\Http\Controllers;

use App\Models\Extracurricular;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ExtracurricularController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('extracurriculars', 'public');
        }

        Extracurricular::create($validated);

        return redirect()->route('school-profile.index')
            ->with('success', 'Ekstrakurikuler berhasil ditambahkan.')
            ->with('active_tab', 'ekskul');
    }

    public function update(Request $request, Extracurricular $extracurricular)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($extracurricular->image && Storage::disk('public')->exists($extracurricular->image)) {
                Storage::disk('public')->delete($extracurricular->image);
            }
            $validated['image'] = $request->file('image')->store('extracurriculars', 'public');
        } else {
            unset($validated['image']);
        }

        $extracurricular->update($validated);

        return redirect()->route('school-profile.index')
            ->with('success', 'Ekstrakurikuler berhasil diperbarui.')
            ->with('active_tab', 'ekskul');
    }

    public function destroy(Extracurricular $extracurricular)
    {
        if ($extracurricular->image && Storage::disk('public')->exists($extracurricular->image)) {
            Storage::disk('public')->delete($extracurricular->image);
        }

        $extracurricular->delete();

        return redirect()->route('school-profile.index')
            ->with('success', 'Ekstrakurikuler berhasil dihapus.')
            ->with('active_tab', 'ekskul');
    }
}
