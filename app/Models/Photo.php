<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import SoftDeletes trait

class Photo extends Model
{
    use HasFactory;

    /**
     * Ang mga attributes na ito ay pwedeng lagyan ng data nang sabay-sabay (Mass Assignment).
     */
    use SoftDeletes; // 2. Add this inside the class

    protected $table = 'photos'; // Match the migration table name

    protected $fillable = ['category_name', 'description', 'image_path', 'album_id', 'is_active'];

    public function album()
   {
       return $this->belongsTo(Album::class);
   }

}