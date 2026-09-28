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
        $school = SchoolProfile::firstOrCreate([], [
            'name' => 'SDN 204 Palembang',
            'npsn' => '10604338',
            'address' => 'Jl. Balap Sepeda, LR. Muhajirin 4, RT 058, RW 013, Kec.Lorok Pakjo, Kel. Ilir Barat Satu, Kota Palembang, Sumatera Selatan',
            'phone' => '620000000',
            'email' => 'sdn204palembang@gmail.com',
            'description' => 'SD Negeri 204 Palembang merupakan satuan pendidikan dasar berstatus negeri yang berada di Kota Palembang, Sumatera Selatan. Sekolah menyelenggarakan pendidikan jenjang Sekolah Dasar dan berkomitmen mendukung pembelajaran serta perkembangan peserta didik berakhlak mulia, cerdas, dan berprestasi.',
            'hero_title' => 'Mengenal Lebih Dekat SDN 204',
            'hero_description' => 'Website informasi sekolah yang menyediakan berbagai informasi mengenai profil, kegiatan, prestasi, berita, dan galeri sekolah.',
            'organization_title' => 'Struktur Organisasi SDN 204 Palembang',
        ]);

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
