<?php

namespace App\Http\Controllers;

use App\Models\Slideshow;
use Illuminate\Http\Request;

class SlideshowController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // 2MB Max
        ]);

        // Save file to storage/app/public/slides
        $path = $request->file('image')->store('slides', 'public');

        Slideshow::create([
            'title' => $request->title,
            'image_path' => $path,
        ]);

        return back()->with('status', 'Slide uploaded successfully!');
    }
}