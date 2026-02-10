<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SettingsController extends Controller
{
    public function getLatestData() 
    {
        // Fetch everything in one go for better performance
        $settings = DB::table('settings')->whereIn('key', ['slide_duration', 'transition_effect'])->pluck('value', 'key');

        return response()->json([
            'seconds' => $settings['slide_duration'] ?? 5,
            'effect' => $settings['transition_effect'] ?? 'fade',
            // If updated_at is null, we return the current time so the slideshow knows to refresh
            'last_update' => DB::table('settings')->max('updated_at') ?? Carbon::now()->toDateTimeString(),
        ]);
    }

  public function update(Request $request)
{
    $request->validate([
        'slide_duration' => 'required|integer|min:1|max:60',
        'transition_effect' => 'required|string',
        'display_album_ids' => 'nullable|string',
    ]);

    $data = [
        'slide_duration' => $request->slide_duration,
        'transition_effect' => $request->transition_effect,
        'display_album_ids' => $request->display_album_ids ?? '', 
    ];

    foreach ($data as $key => $value) {
        \DB::table('settings')->updateOrInsert(
            ['key' => $key],
            [
                'value' => $value,
                'updated_at' => \Carbon\Carbon::now() // CRITICAL for refresh detection
            ]
        );
    }

    return back()->with('success', 'Settings updated!');
    }
}