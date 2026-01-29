<?php

namespace App\Http\Controllers;

use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SlideshowController extends Controller
{


    public function index()
{
    $slides = Slideshow::all();
    
    // Fetch count of soft-deleted items
    $trashCount = Slideshow::onlyTrashed()->count();
    // .env value

    return view('admin.dashboard', compact('slides', 'trashCount'));
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
        return back()->with('status', 'Slide status updated!');
    }

    /**
     * SOFT DELETE: Move to Trash
     */
    public function destroy(Slideshow $slideshow)
    {
        // We do NOT delete the file from Storage here.
        // The SoftDeletes trait in the model handles the 'deleted_at' column.
        $slideshow->delete();
        
        return back()->with('status', 'Slide moved to Trash!');
    }

    /**
     * RESTORE: Bring back from Trash
     */
    public function restore($id)
    {
        // Must use withTrashed() to find the record
        $slide = Slideshow::withTrashed()->findOrFail($id);
        $slide->restore();

        return back()->with('status', 'Slide restored successfully!');
    }

    /**
     * FORCE DELETE: Permanent erasure
     */
    public function forceDelete($id)
    {
        $slide = Slideshow::withTrashed()->findOrFail($id);

        // This is where we finally delete the physical file
        if (Storage::disk('public')->exists($slide->image_path)) {
            Storage::disk('public')->delete($slide->image_path);
        }

        $slide->forceDelete();

        return back()->with('status', 'Slide permanently deleted from the server.');
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
  
 