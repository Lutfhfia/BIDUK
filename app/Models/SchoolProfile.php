<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SchoolProfile extends Model
{
    protected $fillable = [
        'name','npsn','nss','address','phone','email','website',
        'description','vision','mission','logo','cover_image',
        'organization_image','hero_title','hero_description',
        'school_image','organization_title','organization_description',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset('storage/' . $this->logo);
        }
        return null;
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        return null;
    }

    public function getSchoolImageUrlAttribute(): ?string
    {
        if ($this->school_image) {
            return asset('storage/' . $this->school_image);
        }
        return null;
    }

    public function getOrganizationImageUrlAttribute(): ?string
    {
        if ($this->organization_image) {
            return asset('storage/' . $this->organization_image);
        }
        return null;
    }
}

