<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Import ito sa pinakataas

class Photo extends Model
{
    use SoftDeletes; // Gamitin ito sa loob ng class

    protected $fillable = ['name', 'description', 'is_active', 'image_path', 'album_id'];

    // FIX para sa RelationNotFoundException
    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}