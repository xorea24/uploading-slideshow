<?php

namespace App\Http\Controllers;

use App\Models\Photo;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PhotosController extends Controller
{


    /**
     * INDEX: Display albums and slides on the Dashboard
     */
    public function index() 
    {
        // Fetch all albums with their photos for the management tabs
        $albums = Album::with(['slides' => function($query) {
            $query->latest(); 
        }])->latest()->get();

        // Also fetch the settings so the view can show current values
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
     * UPDATE PHOTO: Update photo title and description
     */
    public function updatePhoto(Request $request, Photo $photo)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $photo->update([
            'category_name' => $request->name,
            'description' => $request->description,
        ]);

        return back()->with('status', 'Photo updated successfully!');
    }

    /**
     * SETTINGS: Update duration, effects, and active albums
     */
    public function update(Request $request)
    {
        // 1. Validate the settings input
        $request->validate([
            'slide_duration' => 'required|integer|min:1|max:60',
            'transition_effect' => 'required|string',
            'display_album_ids' => 'nullable|string',
        ]);

        $settings = [
            'slide_duration' => $request->slide_duration,
            'transition_effect' => $request->transition_effect,
            'display_album_ids' => $request->display_album_ids ?? '',
        ];

        // 2. Loop and Update with Timestamps
        foreach ($settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                [
                    'value' => $value,
                    'updated_at' => Carbon::now() // Critical for your JSON update detection
                ]
            );
        }

        return back()->with('success', 'Settings saved successfully!');
    }

    /**
     * TOGGLE: Switch visibility for a single slide
     */
    public function toggle(Photo $Photo)
    {
        $Photo->is_active = !$Photo->is_active;
        $Photo->save();
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
     * DESTROY: Soft delete
     */
    public function destroy(Photo $Photo)
    {
        $album = $Photo->album;
        $Photo->delete();

        // Optional: delete album if it becomes empty
        if ($album && $album->slides()->count() === 0) {
            $album->delete();
        }

        return back()->with('status', 'Moved to Recycle Bin.')->with('last_tab', 'manage');
    }

    /**
     * RESTORE: Single item from trash
     */
    public function restore($id)
    {
        $Photo = Photo::withTrashed()->findOrFail($id);
        $Photo->restore();

        $album = Album::withTrashed()->find($Photo->album_id);
        if ($album && $album->trashed()) {
            $album->restore();
        }

        return back()->with('status', 'Photo restored successfully!')->with('last_tab', 'trash');
    }

    /**
     * FORCE DELETE: Permanent removal
     */
    public function forceDelete($id)
    {
        $slide = Photo::onlyTrashed()->findOrFail($id);

        if ($slide->image_path && Storage::disk('public')->exists($slide->image_path)) {
            Storage::disk('public')->delete($slide->image_path);
        }

        $slide->forceDelete();
        return back()->with('status', 'Permanently removed.');
    }
}