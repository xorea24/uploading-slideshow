<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Added this import
use App\Http\Controllers\AlbumController;

class AlbumController extends Controller
{
    /**
     * TOGGLE ALL: Show or Hide all slides in an album
     */

  // App\Http\Controllers\AlbumController.php

public function upload(Request $request)
{
    // ... validation ...

    foreach ($request->file('images') as $image) {
        $path = $image->store('slides', 'public');

        $album->slides()->create([
            'image_path' => $path,
            'title'      => $image->getClientOriginalName(),
            'description'=> $request->description,
            'is_active'  => true, // This ensures visibility immediately upon upload
        ]);
    }

    return back()->with('status', 'Images uploaded and set to Active!');
}
    public function toggle(Album $album)
    {
        // Check if there's at least one hidden slide
        $hasHidden = $album->slides()->where('is_active', false)->exists();

        // If hidden slides exist, make everything active (true). 
        // If everything is already active, hide everything (false).
        $album->slides()->update(['is_active' => $hasHidden]);

        return back()->with('status', 'Album visibility updated!');
    }

    /**
     * Update album name
     */
    public function update(Request $request, Album $album)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $album->update(['name' => $request->name]);

        return back()->with('status', 'Album renamed successfully!')->with('last_tab', 'manage');
    }

    /**
     * Soft delete album and its slides
     */
    public function destroy(Album $album)
    {
        // Soft delete all slides in this album
        Photo::where('album_id', $album->id)->delete();

        // Soft delete the album itself
        $album->delete();

        return back()->with('status', 'Album moved to Recycle Bin.')->with('last_tab', 'manage');
    }

    /**
     * Restore album contents by album_id
     */
    public function restoreAlbum(Request $request)
    {
        $albumId = $request->input('album_id');
        $album = Album::withTrashed()->find($albumId);

        if ($album) {
            $album->restore();
        }

        Photo::onlyTrashed()
            ->where('album_id', $albumId)
            ->restore();

        return back()->with([
            'status' => 'Album content restored.',
            'last_tab' => 'trash'
        ]);
    }

    /**
     * Permanently delete album contents
     */
    public function forceDeleteAlbum($albumId)
    {
        $slides = Photo::onlyTrashed()->where('album_id', $albumId)->get();
        
        foreach ($slides as $slide) {
            if ($slide->image_path && Storage::disk('public')->exists($slide->image_path)) {
                Storage::disk('public')->delete($slide->image_path);
            }
            $slide->forceDelete();
        }

        $album = Album::withTrashed()->find($albumId);
        if ($album) {
            $album->forceDelete();
        }

        return back()->with('status', 'Album permanently deleted.')->with('last_tab', 'trash');
    }
}