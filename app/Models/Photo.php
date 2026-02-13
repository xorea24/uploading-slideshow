<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Photo extends Model
{
    use HasFactory, SoftDeletes; // Pagsamahin na natin dito para malinis

    protected $table = 'photos';

   // app/Models/Photo.php

      // App/Models/Photo.php o Slide.php
protected $fillable = ['name', 'description', 'album_id', 'image_path', 'is_active'];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }
}