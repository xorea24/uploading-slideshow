<?php

namespace App\Http\Controllers;

use App\Models\Slideshow;
use App\Models\Album; // IMPORTANTE: Huwag kalimutan ito
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SlideshowController extends Controller
{
    /**
     * INDEX: Fetching albums with their slides.
     */
    public function index() 
    {
        // 2. PASTE THE NEW CODE HERE:
    $albums = Album::with(['slides' => function($query) {
        $query->latest(); 
    }])->latest()->get();

        // Get the latest uploaded slide globally
        $latestUpload = Slideshow::with('album')->orderBy('created_at', 'desc')->first();
        
         // 3. This returns the data to your HTML (Blade) file
        return view('dashboard', compact('albums'));
    }

    /**
     * STORE: Saving images and handling New Album creation.
     */
    public function store(Request $request)
    {
        $request->validate([
            'album_id' => 'required', // Pwedeng ID or string "new"
            'new_album_name' => 'required_if:album_id,new|max:255',
            'new_album_desc' => 'nullable|string|max:1000',
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:5120' // 5MB limit
        ]);

        // Logic para sa Album
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

        return back()
            ->with('status', 'Photos uploaded successfully!')
            ->with('last_tab', 'manage')
            ->with('last_album', $albumId);
    }



    /**
     * RESTORE: Restore trashed slideshow
     */
    public function restore($id)
    {
        $slideshow = Slideshow::withTrashed()->findOrFail($id);
        $slideshow->restore();
        // If the parent album was trashed, restore it as well
        $album = Album::withTrashed()->find($slideshow->album_id);
        if ($album && $album->trashed()) {
            $album->restore();
        }

        return back()->with('status', 'Photo restored successfully!')->with('last_tab', 'trash');
    }

    /**
     * DESTROY: Soft delete a slideshow
     */
    public function destroy(Slideshow $slideshow)
    {
        $album = $slideshow->album;

        $slideshow->delete();

        // If the album has no more non-trashed slides, move the album to Recycle Bin
        if ($album && $album->slides()->count() === 0) {
            $album->delete();
        }

        return back()->with('status', 'Photo moved to Recycle Bin.')->with('last_tab', 'manage');
    }

    public function toggle(Slideshow $slideshow)
    {
        $slideshow->is_active = !$slideshow->is_active;
        $slideshow->save();
        return back()->with('status', 'Visibility updated!');
    }

 public function forceDelete($id)
{
    // FIX: You must include onlyTrashed() to find the record!
    $slide = \App\Models\Slideshow::onlyTrashed()->findOrFail($id);

    // Delete the file from storage
    if ($slide->image_path && \Storage::disk('public')->exists($slide->image_path)) {
        \Storage::disk('public')->delete($slide->image_path);
    }

    // Permanently remove from DB
    $slide->forceDelete();

    return back()->with('status', 'Item permanently removed.');
}

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