<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SchoolGallery extends Model
{
    protected $fillable = ['title','description','image','is_active'];
    protected $casts = ['is_active' => 'boolean'];

    protected static function booted(): void
    {
        static::deleting(function (SchoolGallery $item) {
            if ($item->image && Storage::disk('public')->exists($item->image)) Storage::disk('public')->delete($item->image);
        });
    }
}
