<?php

namespace Tests\Feature;

use App\Models\Extracurricular;
use App\Models\Role;
use App\Models\SchoolAchievement;
use App\Models\SchoolGallery;
use App\Models\SchoolNews;
use App\Models\SchoolProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class LandingSchoolProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_landing_page_shows_profile_sections_and_public_media_from_school_profile_data(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('school-profiles/logo/logo.png', 'logo');
        Storage::disk('public')->put('school-profiles/cover/school.jpg', 'cover');
        Storage::disk('public')->put('school-profiles/organization/structure.png', 'structure');
        Storage::disk('public')->put('school-achievements/award.png', 'award');
        Storage::disk('public')->put('extracurriculars/scout.png', 'scout');
        Storage::disk('public')->put('school-news/news.png', 'news');
        Storage::disk('public')->put('school-galleries/gallery.png', 'gallery');

        SchoolProfile::create([
            'name' => 'SDN Uji BIDUK',
            'npsn' => '12345678',
            'nss' => '10203040',
            'address' => 'Jalan Uji Nomor 1',
            'phone' => '0711000000',
            'email' => 'sekolah@example.test',
            'website' => 'https://sekolah.example.test',
            'description' => 'Deskripsi profil sekolah dari database.',
            'vision' => 'Visi yang diatur admin.',
            'mission' => 'Misi yang diatur admin.',
            'logo' => 'school-profiles/logo/logo.png',
            'cover_image' => 'school-profiles/cover/school.jpg',
            'organization_image' => 'school-profiles/organization/structure.png',
            'organization_title' => 'Struktur Organisasi Uji',
            'organization_description' => 'Keterangan struktur dari database.',
        ]);

        SchoolAchievement::create([
            'title' => 'Juara Prestasi Uji',
            'year' => 2026,
            'level' => 'Kota',
            'description' => 'Keterangan prestasi dari database.',
            'image' => 'school-achievements/award.png',
        ]);

        Extracurricular::create([
            'name' => 'Pramuka Uji',
            'description' => 'Keterangan ekstrakurikuler dari database.',
            'image' => 'extracurriculars/scout.png',
            'is_active' => true,
        ]);

        Extracurricular::create([
            'name' => 'Ekstrakurikuler Nonaktif',
            'is_active' => false,
        ]);

        SchoolNews::create([
            'title' => 'Berita Uji BIDUK',
            'published_at' => '2026-10-30',
            'summary' => 'Ringkasan berita dari database.',
            'content' => 'Isi berita dari database.',
            'image' => 'school-news/news.png',
            'is_published' => true,
        ]);

        SchoolNews::create([
            'title' => 'Berita Draft Tidak Tampil',
            'is_published' => false,
        ]);

        SchoolGallery::create([
            'title' => 'Galeri Uji BIDUK',
            'description' => 'Keterangan galeri dari database.',
            'image' => 'school-galleries/gallery.png',
            'is_active' => true,
        ]);

        SchoolGallery::create([
            'title' => 'Galeri Nonaktif Tidak Tampil',
            'image' => 'school-galleries/gallery.png',
            'is_active' => false,
        ]);

        $response = $this->get(route('landing'));

        $response->assertOk()
            ->assertSee('id="informasi-sekolah"', false)
            ->assertSee('id="visi-misi"', false)
            ->assertSee('id="struktur-organisasi"', false)
            ->assertSee('id="prestasi"', false)
            ->assertSee('id="ekstrakurikuler"', false)
            ->assertSee('id="berita"', false)
            ->assertSee('id="galeri"', false)
            ->assertSee('href="#informasi-sekolah"', false)
            ->assertSee('href="#visi-misi"', false)
            ->assertSee('href="#struktur-organisasi"', false)
            ->assertSee('href="#prestasi"', false)
            ->assertSee('href="#ekstrakurikuler"', false)
            ->assertSee('href="#berita"', false)
            ->assertSee('href="#galeri"', false)
            ->assertSee('SDN Uji BIDUK')
            ->assertSee('Visi yang diatur admin.')
            ->assertSee('Misi yang diatur admin.')
            ->assertSee('Struktur Organisasi Uji')
            ->assertSee('Keterangan struktur dari database.')
            ->assertSee('Juara Prestasi Uji')
            ->assertSee('Keterangan prestasi dari database.')
            ->assertSee('Pramuka Uji')
            ->assertSee('Keterangan ekstrakurikuler dari database.')
            ->assertSee('Berita Uji BIDUK')
            ->assertSee('Ringkasan berita dari database.')
            ->assertSee('Isi berita dari database.')
            ->assertSee('Galeri Uji BIDUK')
            ->assertSee('Keterangan galeri dari database.')
            ->assertSee('col-12 col-md-6 col-xl-4', false)
            ->assertDontSee('Ekstrakurikuler Nonaktif')
            ->assertDontSee('Berita Draft Tidak Tampil')
            ->assertDontSee('Galeri Nonaktif Tidak Tampil')
            ->assertSee(asset('storage/school-profiles/logo/logo.png'), false)
            ->assertSee(asset('storage/school-profiles/cover/school.jpg'), false)
            ->assertSee(asset('storage/school-profiles/organization/structure.png'), false)
            ->assertSee(asset('storage/school-achievements/award.png'), false)
            ->assertSee(asset('storage/extracurriculars/scout.png'), false)
            ->assertSee(asset('storage/school-news/news.png'), false)
            ->assertSee(asset('storage/school-galleries/gallery.png'), false);
    }

    public function test_landing_page_shows_empty_states_without_creating_sample_profile_data(): void
    {
        $response = $this->get(route('landing'));

        $response->assertOk()
            ->assertSee('id="struktur-organisasi"', false)
            ->assertSee('id="prestasi"', false)
            ->assertSee('id="ekstrakurikuler"', false)
            ->assertSee('Data visi sekolah belum tersedia.')
            ->assertSee('Data misi sekolah belum tersedia.')
            ->assertSee('Data struktur organisasi belum tersedia')
            ->assertSee('Data prestasi belum tersedia')
            ->assertSee('Data ekstrakurikuler belum tersedia')
            ->assertSee('Belum Ada Berita Dipublikasikan')
            ->assertSee('Belum Ada Foto Galeri');

        $this->assertDatabaseCount('school_profiles', 0);
    }

    public function test_school_profile_dashboard_updates_are_reflected_on_the_next_landing_request(): void
    {
        Storage::fake('public');

        $school = SchoolProfile::create([
            'name' => 'Nama Lama',
            'vision' => 'Visi Lama',
        ]);
        $adminRole = Role::create(['name' => 'Super Admin']);
        $admin = User::create([
            'name' => 'Admin BIDUK',
            'username' => 'admin-biduk',
            'email' => 'admin-biduk@example.test',
            'password' => 'password',
            'role_id' => $adminRole->id,
            'status' => 'Aktif',
        ]);

        $this->actingAs($admin)
            ->put(route('school-profile.update'), [
                'name' => 'Nama Baru dari Admin',
                'vision' => 'Visi Baru dari Admin',
            ])
            ->assertRedirect(route('school-profile.index'));

        $this->post(route('school-achievements.store'), [
            'title' => 'Prestasi Baru dari Admin',
            'year' => 2026,
        ])->assertRedirect(route('school-profile.index'));

        $this->post(route('extracurriculars.store'), [
            'name' => 'Ekskul Baru dari Admin',
            'is_active' => true,
        ])->assertRedirect(route('school-profile.index'));

        $this->post(route('school-news.store'), [
            'title' => 'Berita Baru dari Admin',
            'summary' => 'Ringkasan berita terbaru.',
            'content' => 'Isi berita terbaru.',
            'is_published' => true,
        ])->assertRedirect(route('school-profile.index'));

        $this->post(route('school-galleries.store'), [
            'title' => 'Galeri Baru dari Admin',
            'description' => 'Dokumentasi kegiatan terbaru.',
            'image' => UploadedFile::fake()->image('kegiatan.jpg'),
            'is_active' => true,
        ])->assertRedirect(route('school-profile.index'));

        $this->get(route('landing'))
            ->assertOk()
            ->assertSee('Nama Baru dari Admin')
            ->assertSee('Visi Baru dari Admin')
            ->assertSee('Prestasi Baru dari Admin')
            ->assertSee('Ekskul Baru dari Admin')
            ->assertSee('Berita Baru dari Admin')
            ->assertSee('Galeri Baru dari Admin')
            ->assertDontSee('Nama Lama')
            ->assertDontSee('Visi Lama');

        $this->assertDatabaseHas('school_profiles', [
            'id' => $school->id,
            'name' => 'Nama Baru dari Admin',
            'vision' => 'Visi Baru dari Admin',
        ]);
    }
}
