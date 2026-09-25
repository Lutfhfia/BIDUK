<?php

namespace App\Http\Controllers;

use App\Models\SchoolProfile;
use App\Models\SchoolAchievement;
use App\Models\Extracurricular;
use App\Models\SchoolNews;
use App\Models\SchoolGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SchoolProfileController extends Controller
{
    public function index()
    {
        $school = SchoolProfile::firstOrCreate([], ['name' => 'SDN 204 Palembang']);
        return view('admin.school-profile.index', [
            'school' => $school,
            'achievements' => SchoolAchievement::latest()->get(),
            'extracurriculars' => Extracurricular::latest()->get(),
            'newsItems' => SchoolNews::latest('published_at')->latest()->get(),
            'galleries' => SchoolGallery::latest()->get(),
        ]);
    }

    public function update(Request $request)
    {
        $school = SchoolProfile::firstOrCreate([], ['name' => 'SDN 204 Palembang']);
        $data = $request->validate([
            'name'=>'required|string|max:255','npsn'=>'nullable|string|max:50','nss'=>'nullable|string|max:50',
            'address'=>'nullable|string|max:3000','phone'=>'nullable|string|max:50','email'=>'nullable|email|max:255','website'=>'nullable|string|max:255',
            'description'=>'nullable|string|max:10000','vision'=>'nullable|string|max:10000','mission'=>'nullable|string|max:20000',
            'hero_title'=>'nullable|string|max:255','hero_description'=>'nullable|string|max:5000','organization_title'=>'nullable|string|max:255','organization_description'=>'nullable|string|max:5000',
            'logo'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:2048','cover_image'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:5120','school_image'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:5120','organization_image'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);
        foreach (['logo'=>'school-profile/logo','cover_image'=>'school-profile/cover','school_image'=>'school-profile/school','organization_image'=>'school-profile/organization'] as $field=>$folder) {
            if ($request->hasFile($field)) {
                if ($school->$field && Storage::disk('public')->exists($school->$field)) Storage::disk('public')->delete($school->$field);
                $data[$field] = $request->file($field)->store($folder, 'public');
            }
        }
        $school->update($data);
        return back()->with('success','Profil sekolah berhasil disimpan.');
    }
}
