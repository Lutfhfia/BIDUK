<?php

namespace App\Http\Controllers;

use App\Models\SchoolAchievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolAchievementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'nullable|integer|min:1900|max:2200',
            'level' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('school-achievements', 'public');
        }

        SchoolAchievement::create($validated);

        return redirect()->route('school-profile.index')
            ->with('success', 'Prestasi sekolah berhasil ditambahkan.')
            ->with('active_tab', 'prestasi');
    }

    public function update(Request $request, SchoolAchievement $school_achievement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'year' => 'nullable|integer|min:1900|max:2200',
            'level' => 'nullable|string|max:50',
            'description' => 'nullable|string|max:5000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($request->hasFile('image')) {
            if ($school_achievement->image && Storage::disk('public')->exists($school_achievement->image)) {
                Storage::disk('public')->delete($school_achievement->image);
            }
            $validated['image'] = $request->file('image')->store('school-achievements', 'public');
        } else {
            unset($validated['image']);
        }

        $school_achievement->update($validated);

        return redirect()->route('school-profile.index')
            ->with('success', 'Prestasi sekolah berhasil diperbarui.')
            ->with('active_tab', 'prestasi');
    }

    public function destroy(SchoolAchievement $school_achievement)
    {
        if ($school_achievement->image && Storage::disk('public')->exists($school_achievement->image)) {
            Storage::disk('public')->delete($school_achievement->image);
        }

        $school_achievement->delete();

        return redirect()->route('school-profile.index')
            ->with('success', 'Prestasi sekolah berhasil dihapus.')
            ->with('active_tab', 'prestasi');
    }
}
