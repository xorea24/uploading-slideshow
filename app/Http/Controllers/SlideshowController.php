<?php

namespace App\Http\Controllers;

use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class SlideshowController extends Controller
{
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
     * Toggle the visibility of a slide
     */
    public function toggle(Slideshow $slideshow)
    {
        $slideshow->update(['is_active' => !$slideshow->is_active]);
        return back()->with('status', 'Slide status updated!');
    }

    /**
     * Delete a slide and its image file
     */
    public function destroy(Slideshow $slideshow)
    {
        Storage::disk('public')->delete($slideshow->image_path);
        $slideshow->delete();
        return back()->with('status', 'Slide deleted successfully!');
    }

    /**
     * Save Slideshow Settings (Duration and Transition)
     */
    public function updateSettings(Request $request) 
    {
        $request->validate([
            'slide_duration' => 'required|numeric|min:1',
            'transition_effect' => 'required|string'
        ]);

        // 1. Save to Database (Permanent)
        DB::table('settings')->updateOrInsert(['key' => 'slide_duration'], ['value' => $request->slide_duration]);
        DB::table('settings')->updateOrInsert(['key' => 'transition_effect'], ['value' => $request->transition_effect]);
        
        // 2. Also save to Session (For immediate UI updates)
        session([
            'slide_duration' => $request->slide_duration,
            'transition_effect' => $request->transition_effect
        ]);
        
        return back()->with('status', 'Settings updated successfully!');
    }
}