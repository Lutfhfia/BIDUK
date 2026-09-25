<?php
namespace App\Http\Controllers;
use App\Models\SchoolAchievement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
class SchoolAchievementController extends Controller {
 public function store(Request $r){$d=$r->validate(['title'=>'required|string|max:255','year'=>'nullable|integer|min:1900|max:2200','level'=>'nullable|string|max:50','description'=>'nullable|string|max:5000','image'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:5120']); if($r->hasFile('image'))$d['image']=$r->file('image')->store('school-achievements','public'); SchoolAchievement::create($d); return back()->with('success','Prestasi berhasil ditambahkan.');}
 public function update(Request $r, SchoolAchievement $achievement){$d=$r->validate(['title'=>'required|string|max:255','year'=>'nullable|integer|min:1900|max:2200','level'=>'nullable|string|max:50','description'=>'nullable|string|max:5000','image'=>'nullable|image|mimes:jpg,jpeg,png,webp|max:5120']); if($r->hasFile('image')){if($achievement->image&&Storage::disk('public')->exists($achievement->image))Storage::disk('public')->delete($achievement->image);$d['image']=$r->file('image')->store('school-achievements','public');} $achievement->update($d);return back()->with('success','Prestasi berhasil diperbarui.');}
 public function destroy(SchoolAchievement $achievement){$achievement->delete();return back()->with('success','Prestasi berhasil dihapus.');}
}
