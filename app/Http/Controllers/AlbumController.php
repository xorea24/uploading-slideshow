<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Slideshow;
use Illuminate\Http\Request;

class AlbumController extends Controller
{
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
    public function destroy(Request $request, Album $album)
    {
        // Soft delete all slides in this album, then soft-delete the album
        Slideshow::where('album_id', $album->id)->delete();

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

        Slideshow::onlyTrashed()
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
        $slides = Slideshow::onlyTrashed()->where('album_id', $albumId)->get();
        
        foreach ($slides as $slide) {
            if ($slide->image_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($slide->image_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($slide->image_path);
            }
            $slide->forceDelete();
        }

        // Also permanently remove the album record if present
        $album = Album::withTrashed()->find($albumId);
        if ($album) {
            $album->forceDelete();
        }

        return back()->with('status', 'Album permanently deleted.')->with('last_tab', 'trash');
    }
}
