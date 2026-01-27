<?php

namespace App\Http\Controllers;

use App\Models\Slideshow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SlideshowController extends Controller
{
   public function store(Request $request)
{
    // 1. Validate the array of images
    $request->validate([
        'images' => 'required',
        'images.*' => 'image|mimes:jpeg,png,jpg|max:2048' // Validates each file in the array
    ]);

    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            // 2. Generate a clean title based on the original filename
            $title = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            
            // 3. Store the file in the public 'slides' folder
            $path = $image->store('slides', 'public');

            // 4. Create a database entry for each slide
            \App\Models\Slideshow::create([
                'title' => $title,
                'image_path' => $path,
                'is_active' => true,
            ]);
        }
    }

    return back()->with('status', 'All images uploaded successfully!');
    }

    public function toggle(Slideshow $slideshow)
    {
        $slideshow->update(['is_active' => !$slideshow->is_active]);
        return back()->with('status', 'Slide status updated!');
    }

    public function destroy(Slideshow $slideshow)
    {
        Storage::disk('public')->delete($slideshow->image_path);
        $slideshow->delete();
        return back()->with('status', 'Slide deleted successfully!');
    }
}