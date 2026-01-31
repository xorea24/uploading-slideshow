<?php

namespace App\Http\Controllers;

use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SlideshowController extends Controller
{

        public function restoreAlbum(Request $request)
        {
            $category = $request->category_name;

            // Restore all deleted items belonging to this category
            \App\Models\Slideshow::onlyTrashed()
                ->where('category_name', $category)
                ->restore();

            return back()->with([
                'status' => "Album '$category' has been fully restored.",
                'last_tab' => 'trash'
            ]);
        }

    public function restore($id)
    {
        // Find the record even if it is trashed
        $slide = \App\Models\Slideshow::withTrashed()->findOrFail($id);

        // Restore the record
        $slide->restore();

        return back()->with([
            'status' => 'Photo successfully restored to ' . ($slide->category_name ?: 'Album'),
            'last_tab' => 'trash' // Keeps the user on the Recycle Bin tab
        ]);
    }
    public function index()
    {
        $slides = \App\Models\Slideshow::latest()->get();
        $slides = Slideshow::all();
        $trashCount = Slideshow::onlyTrashed()->count();
        return view('admin.dashboard', compact('slides', 'trashCount'));
    }

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
                    'updated_at' => now(), // Siguraduhin na mayroon nito
                ]);
            }
        }
        return back()->with('status', 'All images uploaded successfully!');
    }

    public function destroy(Slideshow $slideshow)
    {
        $slideshow->delete();
        return back()->with('status', 'Moved to Recycle Bin.')->with('last_tab', 'manage');
    }

    // Add this inside your SlideshowController class
        public function destroyAlbum(Request $request) // Change this to accept Request
        {
            // Get the category name from the hidden input field in your form
            $category_name = $request->input('category_name');

            if ($category_name) {
                // Soft delete all images matching the category
                Slideshow::where('category_name', $category_name)->delete();
                
                return back()->with('status', "Album '$category_name' moved to Recycle Bin.")
                            ->with('last_tab', 'manage');
            }

            return back()->with('error', 'Category not found.');
        }

    public function forceDelete($id)
    {
        $slideshow = Slideshow::withTrashed()->findOrFail($id);
        
        // Physically remove file so it's gone from storage too
        if (Storage::disk('public')->exists($slideshow->image_path)) {
            Storage::disk('public')->delete($slideshow->image_path);
        }
        
        $slideshow->forceDelete();
        return back()->with('status', 'Deleted permanently.')->with('last_tab', 'trash');
    }

    public function toggle(Slideshow $slideshow)
    {
        $slideshow->is_active = !$slideshow->is_active;
        $slideshow->save();
        return back()->with('status', 'Slide visibility toggled successfully!');
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