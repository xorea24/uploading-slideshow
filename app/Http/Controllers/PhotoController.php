<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PhotoController extends Controller
{
    /**
     * INDEX: Display albums and slides on the Dashboard
     */
    public function index() 
    {
        $albums = Album::with(['slides' => function($query) {
            $query->latest(); 
        }])->latest()->get();

        $settings = DB::table('settings')->pluck('value', 'key');

        return view('dashboard', compact('albums', 'settings'));
    }

    /**
     * STORE: Handle multiple uploads and album creation
     */
    public function store(Request $request)
    {
        $request->validate([
            'album_id' => 'required',
            'new_album_name' => 'required_if:album_id,new|max:255',
            'new_album_desc' => 'nullable|string|max:1000',
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $albumId = $request->album_id;

        if ($albumId === 'new') {
            $album = Album::create([    
                'name' => $request->new_album_name,
                'description' => $request->new_album_desc ?? null,
            ]);
            $albumId = $album->id;
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $key => $image) {
                // This saves to storage/app/public/slides/filename.png
                // $path will be "slides/filename.png"
                $path = $image->store('slides', 'public');

                Photo::create([
                    'album_id'    => $albumId,
                    'image_path'  => $path,
                    'name'        => $request->titles[$key] ?? 'Untitled', 
                    'description' => $request->descriptions[$key] ?? null,
                    'is_active'   => 1,
                ]);
            }
        }

        return back()->with('status', 'Photos uploaded successfully!')->with('last_tab', 'manage');
    }

    /**
     * UPDATE PHOTO: Fixed column name from 'category_name' to 'name'
     */
    public function update(Request $request, Photo $photo)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        // FIXED: Changed 'category_name' to 'name' to match your DB
        $photo->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return back()
        ->with('status', 'Photo updated successfully!')
        ->with('last_tab', 'manage'); 
        
    }

    /**
     * SETTINGS: Update duration, effects, and active albums
     */


    public function toggle(Photo $photo)
    {
        $photo->is_active = !$photo->is_active;
        $photo->save();
        return back()->with('status', 'Visibility updated!');
    }

    public function toggleAll(Album $album)
    {
        $hasHidden = $album->slides()->where('is_active', false)->exists();
        $album->slides()->update(['is_active' => $hasHidden]);
        return back()->with('status', 'Album visibility updated successfully!');
    }

    public function destroy(Photo $photo)
    {
        $album = $photo->album;
        $photo->delete();

        if ($album && $album->slides()->count() === 0) {
            $album->delete();
        }

        return back()->with('status', 'Photo removed.')->with('last_tab', 'manage');
    }
}