<?php
namespace App\Http\Controllers;
use App\Models\SchoolNews; use Illuminate\Http\Request; use Illuminate\Support\Facades\Storage;
class SchoolNewsController extends Controller {
 public function store(Request $r){$d=$r->validate(['title'=>'required|string|max:255','published_at'=>'nullable|date','summary'=>'nullable|string|max:5000','content'=>'nullable|string','is_published'=>'nullable|boolean','image'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:5120']);$d['is_published']=$r->boolean('is_published');if($r->hasFile('image'))$d['image']=$r->file('image')->store('school-news','public');SchoolNews::create($d);return back()->with('success','Berita berhasil ditambahkan.');}
 public function update(Request $r,SchoolNews $news){$d=$r->validate(['title'=>'required|string|max:255','published_at'=>'nullable|date','summary'=>'nullable|string|max:5000','content'=>'nullable|string','is_published'=>'nullable|boolean','image'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:5120']);$d['is_published']=$r->boolean('is_published');if($r->hasFile('image')){if($news->image&&Storage::disk('public')->exists($news->image))Storage::disk('public')->delete($news->image);$d['image']=$r->file('image')->store('school-news','public');}$news->update($d);return back()->with('success','Berita berhasil diperbarui.');}
 public function destroy(SchoolNews $news){$news->delete();return back()->with('success','Berita berhasil dihapus.');}
}
