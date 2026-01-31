<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingsController extends Controller
{

public function getLatestData() {
    return response()->json([
        'seconds' => \DB::table('settings')->where('key', 'slide_duration')->value('value') ?? 5,
        'effect' => \DB::table('settings')->where('key', 'transition_effect')->value('value') ?? 'fade',
        'last_update' => \DB::table('settings')->max('updated_at'), // Mahalaga ito!
    ]);
}
    public function update(Request $request)
{
    $request->validate([
        'slide_duration' => 'required|integer|min:1|max:60',
        'transition_effect' => 'required|string',
    ]);

    // Update or Insert the Duration
    \DB::table('settings')->updateOrInsert(
        ['key' => 'slide_duration'],
        ['value' => $request->slide_duration, 'updated_at' => now()]
    );

    // Update or Insert the Effect
    \DB::table('settings')->updateOrInsert(
        ['key' => 'transition_effect'],
        ['value' => $request->transition_effect, 'updated_at' => now()]
    );

    return back()->with('status', 'Slideshow settings updated successfully!');
    }
}