<?php

namespace App\Http\Controllers;

use App\Models\SchoolProfile;
use App\Models\SchoolAchievement;
use App\Models\Extracurricular;
use App\Models\SchoolNews;
use App\Models\SchoolGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SchoolProfileController extends Controller
{
    public function index()
    {
        $school = SchoolProfile::firstOrCreate([], ['name' => 'SDN 204 Palembang']);

        return view('admin.school-profile.index', [
            'school' => $school,
            'achievements' => SchoolAchievement::latest('year')->latest()->get(),
            'extracurriculars' => Extracurricular::latest()->get(),
            'newsItems' => SchoolNews::latest('published_at')->latest()->get(),
            'galleries' => SchoolGallery::latest()->get(),
        ]);
    }

    public function update(Request $request)
    {
        $school = SchoolProfile::firstOrCreate([], ['name' => 'SDN 204 Palembang']);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'npsn' => 'nullable|string|max:50',
            'nss' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:3000',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:10000',
            'vision' => 'nullable|string|max:10000',
            'mission' => 'nullable|string|max:20000',
            'hero_title' => 'nullable|string|max:255',
            'hero_description' => 'nullable|string|max:5000',
            'organization_title' => 'nullable|string|max:255',
            'organization_description' => 'nullable|string|max:5000',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:3072',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'school_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'organization_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $fileFields = [
            'logo' => 'school-profiles/logo',
            'cover_image' => 'school-profiles/cover',
            'school_image' => 'school-profiles/school',
            'organization_image' => 'school-profiles/organization',
        ];

        foreach ($fileFields as $field => $folder) {
            if ($request->hasFile($field)) {
                if ($school->$field && Storage::disk('public')->exists($school->$field)) {
                    Storage::disk('public')->delete($school->$field);
                }
                $data[$field] = $request->file($field)->store($folder, 'public');
            } else {
                unset($data[$field]);
            }
        }

        $school->update($data);

        return redirect()->route('school-profile.index')
            ->with('success', 'Profil dan informasi sekolah berhasil diperbarui.')
            ->with('active_tab', 'informasi');
    }
}
