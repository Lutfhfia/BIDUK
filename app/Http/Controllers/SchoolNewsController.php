<?php

namespace App\Http\Controllers;

use App\Models\SchoolNews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolNewsController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'published_at' => 'nullable|date',
            'summary' => 'nullable|string|max:5000',
            'content' => 'nullable|string',
            'is_published' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $validated['is_published'] = $request->boolean('is_published');
        if (empty($validated['published_at']) && $validated['is_published']) {
            $validated['published_at'] = now()->toDateString();
        }

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('school-news', 'public');
        }

        SchoolNews::create($validated);

        return redirect()->route('school-profile.index')
            ->with('success', 'Berita sekolah berhasil ditambahkan.')
            ->with('active_tab', 'berita');
    }

    public function update(Request $request, SchoolNews $school_news)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'published_at' => 'nullable|date',
            'summary' => 'nullable|string|max:5000',
            'content' => 'nullable|string',
            'is_published' => 'nullable',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        if ($request->hasFile('image')) {
            if ($school_news->image && Storage::disk('public')->exists($school_news->image)) {
                Storage::disk('public')->delete($school_news->image);
            }
            $validated['image'] = $request->file('image')->store('school-news', 'public');
        } else {
            unset($validated['image']);
        }

        $school_news->update($validated);

        return redirect()->route('school-profile.index')
            ->with('success', 'Berita sekolah berhasil diperbarui.')
            ->with('active_tab', 'berita');
    }

    public function destroy(SchoolNews $school_news)
    {
        if ($school_news->image && Storage::disk('public')->exists($school_news->image)) {
            Storage::disk('public')->delete($school_news->image);
        }

        $school_news->delete();

        return redirect()->route('school-profile.index')
            ->with('success', 'Berita sekolah berhasil dihapus.')
            ->with('active_tab', 'berita');
    }
}
