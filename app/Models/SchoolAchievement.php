<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SchoolAchievement extends Model
{
    protected $fillable = ['title','year','level','description','image'];

    protected static function booted(): void
    {
        static::deleting(function (SchoolAchievement $item) {
            if ($item->image && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }
        });
    }
}
