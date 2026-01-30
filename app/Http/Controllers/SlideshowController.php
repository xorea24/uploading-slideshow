<?php

namespace App\Http\Controllers;

use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SlideshowController extends Controller
{
    /**
     * Display the dashboard with paginated slides and trash count.
     */

    public function destroyAlbum(Request $request)
    {
        $category = $request->input('category_name');

        // This will move all images in this category to the Trash (soft delete)
        \App\Models\Slideshow::where('category_name', $category)->delete();

        return back()->with('status', "Album '$category' has been moved to the Recycle Bin.");
    }

    public function index() 
    {
        // 1. Fetch count of soft-deleted items for the sidebar badge
        $trashCount = Slideshow::onlyTrashed()->count();

        // 2. Fetch paginated slides (ordered by newest)
        // This returns a Paginator object, fixing the "hasPages does not exist" error
        $slides = Slideshow::orderBy('created_at', 'desc')->paginate(10);

        // 3. Return the correct view (Ensure this matches your folder structure)
        // Based on your error screenshot, it's likely 'dashboard' or 'admin.dashboard'
        return view('dashboard', compact('slides', 'trashCount'));
    }

    /**
     * Upload and store multiple images
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048' 
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $title = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $path = $image->store('slides', 'public');

                Slideshow::create([
                    'title' => $title,
                    'image_path' => $path,
                    'is_active' => true,
                    'category_name' => $request->category_name,
                ]);
            }
        }

        return back()->with('status', 'All images uploaded successfully!');
    }

    /**
     * Toggle visibility
     */
        public function toggle(Slideshow $slideshow)
    {
        $slideshow->update(['is_active' => !$slideshow->is_active]);
        return back()->with('last_tab', 'manage');
    }

    /**
     * SOFT DELETE: Move to Trash
     */
    // For Soft Deleting (Moving to Trash)
    public function destroy(Slideshow $slideshow)
    {
        $slideshow->delete();
        // Return back and tell the frontend to stay on the 'manage' tab
        return back()->with('status', 'Moved to Recycle Bin.')->with('last_tab', 'manage');
    }

    // For Restoring
    public function restore($id)
    {
        Slideshow::withTrashed()->findOrFail($id)->restore();
        // Return back and tell the frontend to stay on the 'trash' tab
        return back()->with('status', 'Image restored.')->with('last_tab', 'trash');
    }

    // For Permanent Deletion
    public function forceDelete($id)
    {
        Slideshow::withTrashed()->findOrFail($id)->forceDelete();
        return back()->with('status', 'Deleted permanently.')->with('last_tab', 'trash');
    }
    /**
     * Save Slideshow Settings
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