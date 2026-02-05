<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Album extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description'];

    /**
     * Get all slides/images in this album
     */
    public function slides()
    {
        return $this->hasMany(Slideshow::class, 'album_id');
    }

    /**
     * Cascade soft-deletes and restores to slides.
     */
    protected static function booted()
    {
        static::deleting(function ($album) {
            if (! $album->isForceDeleting()) {
                $album->slides()->delete();
            }
        });

        static::restoring(function ($album) {
            $album->slides()->withTrashed()->restore();
        });
    }
}
