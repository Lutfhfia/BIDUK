<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProfile extends Model
{
    protected $fillable = [
        'name','npsn','nss','address','phone','email','website',
        'description','vision','mission','logo','cover_image',
        'organization_image','hero_title','hero_description',
        'school_image','organization_title','organization_description',
    ];
}
