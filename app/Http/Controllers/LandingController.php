<?php

namespace App\Http\Controllers;

use App\Models\Extracurricular;
use App\Models\SchoolAchievement;
use App\Models\SchoolGallery;
use App\Models\SchoolNews;
use App\Models\SchoolProfile;

class LandingController extends Controller
{
    public function index()
    {
        $school = SchoolProfile::query()->first() ?? new SchoolProfile;

        $achievements = SchoolAchievement::latest('year')->latest()->get();
        $extracurriculars = Extracurricular::where('is_active', true)->latest()->get();
        $newsItems = SchoolNews::where('is_published', true)->latest('published_at')->latest()->get();
        $galleries = SchoolGallery::where('is_active', true)->latest()->get();

        return view('landing.index', [
            'school' => $school,
            'achievements' => $achievements,
            'extracurriculars' => $extracurriculars,
            'newsItems' => $newsItems,
            'galleries' => $galleries,
        ]);
    }
}
