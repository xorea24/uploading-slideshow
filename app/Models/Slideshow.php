<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slideshow extends Model
{
    use HasFactory;

    /**
     * Ang mga attributes na ito ay pwedeng lagyan ng data nang sabay-sabay (Mass Assignment).
     */
    protected $fillable = [
        'title',
        'image_path',
        'order',
        'is_active',
    ];
}