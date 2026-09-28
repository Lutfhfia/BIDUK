<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SchoolNews extends Model
{
    protected $fillable = ['title','published_at','summary','content','image','is_published'];
    protected $casts = ['published_at' => 'date','is_published' => 'boolean'];

    protected static function booted(): void
    {
        static::deleting(function (SchoolNews $item) {
            if ($item->image && Storage::disk('public')->exists($item->image)) Storage::disk('public')->delete($item->image);
        });
    }
}
