<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use SoftDeletes;

    protected $table = 'photos'; // Siguraduhing 'photos' ito

    protected $fillable = ['name', 'description', 'image_path', 'is_active', 'album_id'];

    protected $casts = [
        'is_active' => 'boolean', // Mahalaga para sa Alpine.js logic
    ];
}