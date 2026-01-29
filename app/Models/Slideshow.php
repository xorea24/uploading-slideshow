<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import SoftDeletes trait

class Slideshow extends Model
{
    use HasFactory;

    /**
     * Ang mga attributes na ito ay pwedeng lagyan ng data nang sabay-sabay (Mass Assignment).
     */
    use SoftDeletes; // 2. Add this inside the class
   
    protected $fillable = [
        'title',
        'category_name',
        'image_path',
        'is_active',
    ];
}