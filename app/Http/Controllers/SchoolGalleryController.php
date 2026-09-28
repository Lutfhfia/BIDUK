<?php

namespace App\Http\Controllers;

use App\Models\SchoolGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolGalleryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'is_active' => 'nullable',
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('school-galleries', 'public');
        }

        SchoolGallery::create($validated);

        return redirect()->route('school-profile.index')
            ->with('success', 'Galeri sekolah berhasil ditambahkan.')
            ->with('active_tab', 'galeri');
    }

    public function update(Request $request, SchoolGallery $school_gallery)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'is_active' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            if ($school_gallery->image && Storage::disk('public')->exists($school_gallery->image)) {
                Storage::disk('public')->delete($school_gallery->image);
            }
            $validated['image'] = $request->file('image')->store('school-galleries', 'public');
        } else {
            unset($validated['image']);
        }

        $school_gallery->update($validated);

        return redirect()->route('school-profile.index')
            ->with('success', 'Galeri sekolah berhasil diperbarui.')
            ->with('active_tab', 'galeri');
    }

    public function destroy(SchoolGallery $school_gallery)
    {
        if ($school_gallery->image && Storage::disk('public')->exists($school_gallery->image)) {
            Storage::disk('public')->delete($school_gallery->image);
        }

        $school_gallery->delete();

        return redirect()->route('school-profile.index')
            ->with('success', 'Galeri sekolah berhasil dihapus.')
            ->with('active_tab', 'galeri');
    }
}
