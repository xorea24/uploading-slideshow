<?php

namespace App\Http\Controllers;

use App\Models\Slideshow;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SlideshowController extends Controller
{
    /**
     * INDEX: Display albums and slides
     */
    public function index() 
    {
        $albums = Album::with(['slides' => function($query) {
            $query->latest(); 
        }])->latest()->get();

        return view('dashboard', compact('albums'));
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
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5120'
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
            foreach ($request->file('images') as $image) {
                $path = $image->store('slides', 'public');

                Slideshow::create([
                    'title' => pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME),
                    'image_path' => $path,
                    'is_active' => true,
                    'album_id' => $albumId, 
                ]);
            }
        }

        return back()->with('status', 'Photos uploaded successfully!')->with('last_tab', 'manage');
    }

    /**
     * TOGGLE: Switch visibility for a single slide
     */
    public function toggle(Slideshow $slideshow)
    {
        $slideshow->is_active = !$slideshow->is_active;
        $slideshow->save();
        return back()->with('status', 'Visibility updated!');
    }

    /**
     * TOGGLE ALL: Switch visibility for an entire album
     */
    public function toggleAll(Album $album)
    {
        $hasHidden = $album->slides()->where('is_active', false)->exists();
        $album->slides()->update(['is_active' => $hasHidden]);

        return back()->with('status', 'Album visibility updated successfully!');
    }

    /**
     * RESTORE: Single item
     */
    public function restore($id)
    {
        $slideshow = Slideshow::withTrashed()->findOrFail($id);
        $slideshow->restore();

        $album = Album::withTrashed()->find($slideshow->album_id);
        if ($album && $album->trashed()) {
            $album->restore();
        }

        return back()->with('status', 'Photo restored successfully!')->with('last_tab', 'trash');
    }

    /**
     * RESTORE ALBUM: Restore all items in an album from trash
     */
    public function restoreAlbum(Request $request)
    {
        $albumId = $request->album_id;
        Slideshow::onlyTrashed()->where('album_id', $albumId)->restore();
        
        $album = Album::withTrashed()->find($albumId);
        if ($album && $album->trashed()) { $album->restore(); }

        return back()->with('status', 'Album items restored!')->with('last_tab', 'trash');
    }

    /**
     * DESTROY: Soft delete
     */
    public function destroy(Slideshow $slideshow)
    {
        $album = $slideshow->album;
        $slideshow->delete();

        if ($album && $album->slides()->count() === 0) {
            $album->delete();
        }

        return back()->with('status', 'Moved to Recycle Bin.')->with('last_tab', 'manage');
    }

    /**
     * FORCE DELETE: Permanent removal
     */
    public function forceDelete($id)
    {
        $slide = Slideshow::onlyTrashed()->findOrFail($id);

        if ($slide->image_path && Storage::disk('public')->exists($slide->image_path)) {
            Storage::disk('public')->delete($slide->image_path);
        }

        $slide->forceDelete();
        return back()->with('status', 'Permanently removed.');
    }

    /**
     * SETTINGS: Update duration and effects
     */
    public function updateSettings(Request $request) 
    {
        $request->validate([
            'slide_duration' => 'required|numeric|min:1',
            'transition_effect' => 'required|string'
        ]);

        DB::table('settings')->updateOrInsert(['key' => 'slide_duration'], ['value' => $request->slide_duration]);
        DB::table('settings')->updateOrInsert(['key' => 'transition_effect'], ['value' => $request->transition_effect]);
        
        return back()->with('status', 'Settings updated successfully!');
    }
}