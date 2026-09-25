<?php
namespace App\Http\Controllers;
use App\Models\SchoolGallery; use Illuminate\Http\Request; use Illuminate\Support\Facades\Storage;
class SchoolGalleryController extends Controller {
 public function store(Request $r){$d=$r->validate(['title'=>'required|string|max:255','description'=>'nullable|string|max:5000','is_active'=>'nullable|boolean','image'=>'required|image|mimes:jpg,jpeg,png,webp|max:5120']);$d['is_active']=$r->boolean('is_active');$d['image']=$r->file('image')->store('school-galleries','public');SchoolGallery::create($d);return back()->with('success','Galeri berhasil ditambahkan.');}
 public function update(Request $r,SchoolGallery $gallery){$d=$r->validate(['title'=>'required|string|max:255','description'=>'nullable|string|max:5000','is_active'=>'nullable|boolean','image'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:5120']);$d['is_active']=$r->boolean('is_active');if($r->hasFile('image')){if($gallery->image&&Storage::disk('public')->exists($gallery->image))Storage::disk('public')->delete($gallery->image);$d['image']=$r->file('image')->store('school-galleries','public');}$gallery->update($d);return back()->with('success','Galeri berhasil diperbarui.');}
 public function destroy(SchoolGallery $gallery){$gallery->delete();return back()->with('success','Galeri berhasil dihapus.');}
}
