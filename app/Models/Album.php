<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get all slides/images in this album
     */
    public function slides()
    {
        return $this->hasMany(Slideshow::class, 'album_id');
    }
}
