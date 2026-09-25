<?php

namespace App\Http\Controllers;

use App\Models\SchoolAchievement;
use App\Models\SchoolGallery;
use App\Models\SchoolNews;
use App\Models\SchoolProfile;
use App\Models\Extracurricular;

class LandingController extends Controller
{
    public function index()
    {
        return view('landing.index', [
            'school' => SchoolProfile::first(),
            'achievements' => SchoolAchievement::latest()->get(),
            'extracurriculars' => Extracurricular::where('is_active', true)->latest()->get(),
            'newsItems' => SchoolNews::where('is_published', true)->latest('published_at')->latest()->get(),
            'galleries' => SchoolGallery::where('is_active', true)->latest()->get(),
        ]);
    }
}
